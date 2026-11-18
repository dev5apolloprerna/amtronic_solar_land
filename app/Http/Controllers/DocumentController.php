<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Plot;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/** Scans and agreements, versioned by plot + type + title. Old versions are kept. */
class DocumentController extends Controller
{
    public function __construct(private DocumentService $docs) {}

    public function index(Request $request)
    {
        $q = Document::visibleTo()->with(['plot', 'uploader'])
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('title', 'like', "%$s%")->orWhere('original_name', 'like', "%$s%")
                ->orWhereHas('plot', fn ($p) => $p->where('land_ref_no', 'like', "%$s%"))))
            ->when($request->doc_type, fn ($q, $v) => $q->where('doc_type', $v))
            ->when($request->linked, fn ($q, $v) => $q->where('documentable_type', Document::LINKABLE[$v] ?? ''))
            ->latest();

        if ($export = $this->maybeExport('document', 'Document Register', ['Title', 'Type', 'Land ref no.', 'Linked to', 'Record', 'File', 'Size KB', 'Version', 'Original scan', 'Uploaded by', 'Uploaded at'],
            $q->get()->map(fn ($d) => [$d->title, $d->doc_type, $d->plot?->land_ref_no, $d->linkedLabel(), $d->documentable_id, $d->original_name, $d->size_kb, $d->version_no, $d->is_original_scan,
                $d->uploader?->username, $d->created_at->format('Y-m-d H:i')]))) {
            return $export;
        }

        return view('documents.index', ['rows' => $q->paginate(config('solar.per_page'))->withQueryString(), 'total' => Document::visibleTo()->count()]);
    }

    public function create(Request $request)
    {
        $plot = $request->plot_id ? Plot::visibleTo()->find($request->plot_id) : null;

        return $this->drawer('Upload document', 'DOCUMENTS', 'documents.form', [
            'plot' => $plot,
            'pre' => $request->only(['linked', 'record', 'doc_type', 'title']),
            'plotOpts' => $plot ? [] : Plot::visibleTo()->orderBy('land_ref_no')->pluck('land_ref_no', 'id')->all(),
            'records' => $plot ? $this->linkableRecords($plot) : [],
        ], module: 'document', saveLabel: 'Upload');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'plot_id' => ['required', 'exists:plots,id'],
            'linked' => ['required', Rule::in(array_keys(Document::LINKABLE))],
            'record' => ['nullable', 'integer'],
            'doc_type' => ['required', Rule::in(config('solar.options.doc_type'))],
            'title' => ['required', 'string', 'max:200'],
            'is_original_scan' => ['required', Rule::in(['Yes', 'No'])],
            'file' => ['required', 'file', 'max:'.config('solar.upload.max_kb'), 'mimes:'.implode(',', config('solar.upload.extensions')), 'extensions:'.implode(',', config('solar.upload.extensions'))],
        ], ['file.required' => 'Choose a file to upload (100 MB maximum).', 'file.max' => 'The maximum upload size is 100 MB.']);
        $plot = Plot::findOrFail($data['plot_id']);
        abort_unless(auth()->user()->canAccessSpv($plot->spv_id), 403);

        $class = Document::LINKABLE[$data['linked']];
        $recordId = $data['linked'] === 'Plot' ? $plot->id : (int) $data['record'];
        $allowed = collect($this->linkableRecords($plot)[$data['linked']] ?? [])->keys()->map(fn ($k) => (int) $k);
        if ($data['linked'] !== 'Plot' && ! $allowed->contains($recordId)) {
            return $this->refuse('Choose a '.strtolower($data['linked']).' record that belongs to this survey number.');
        }
        $doc = $this->docs->store($request->file('file'), [
            'plot_id' => $plot->id, 'documentable_type' => $class, 'documentable_id' => $recordId,
            'doc_type' => $data['doc_type'], 'title' => $data['title'], 'is_original_scan' => $data['is_original_scan'],
        ]);

        return $this->ok("{$doc->original_name} saved as v{$doc->version_no}");
    }

    public function download(Document $document)
    {
        abort_unless(! $document->plot || auth()->user()->canAccessSpv($document->plot->spv_id), 403);
        abort_unless(Storage::disk(config('solar.upload.disk'))->exists($document->file_path), 404, 'The file is missing from storage.');

        return Storage::disk(config('solar.upload.disk'))->download($document->file_path, $document->original_name);
    }

    public function destroy(Document $document)
    {
        abort_unless(! $document->plot || auth()->user()->canAccessSpv($document->plot->spv_id), 403);
        $document->delete(); // soft delete: the file stays on disk for audit

        return $this->ok("{$document->title} v{$document->version_no} deleted");
    }

    /** Records of a plot a document can be linked to: ['Lease' => [id => label], ...] */
    private function linkableRecords(Plot $plot): array
    {
        return [
            'Plot' => [$plot->id => $plot->land_ref_no],
            'Lease' => $plot->leases()->pluck('lease_no', 'id')->all(),
            'Sub-lease' => \App\Models\SubLease::whereIn('lease_id', $plot->leases()->pluck('id'))->pluck('sub_lease_no', 'id')->all(),
            'Compliance' => $plot->compliance ? [$plot->compliance->id => 'Compliance record'] : [],
            'Payment' => \App\Models\RentPayment::whereHas('allocation.schedule', fn ($q) => $q->where('plot_id', $plot->id))->pluck('payment_no', 'id')->all(),
            'Party' => $plot->currentOwnership?->shares()->with('party')->get()->mapWithKeys(fn ($s) => [$s->party_id => $s->party->party_name])->all() ?? [],
        ];
    }
}
