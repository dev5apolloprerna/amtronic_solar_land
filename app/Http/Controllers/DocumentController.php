<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\LandAccess;
use App\Http\Requests\DocumentRequest;
use App\Models\Document;
use App\Models\Land;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/** DOCUMENT_REPOSITORY · files linked to the parcel or one of its records; same type + title = new version */
class DocumentController extends Controller
{
    use LandAccess;

    public function create(Request $request, Land $land)
    {
        $this->guardLand($land);
        $d = new Document(['land_id' => $land->id, 'linked_entity_type' => $request->type ?: 'Land', 'linked_entity_id' => $request->id, 'doc_type' => $request->doc_type, 'doc_title' => $request->title, 'is_original_scan' => 'Yes']);

        return $this->drawer('Upload document', 'DOCUMENT_REPOSITORY · '.$land->land_ref_no, 'lands.forms.document',
            ['d' => $d, 'land' => $land, 'entityOpts' => LookupController::entityOptions($land, $d->linked_entity_type)], module: 'doc', saveLabel: 'Upload');
    }

    public function store(DocumentRequest $request, Land $land, DocumentService $svc)
    {
        $this->guardLand($land);
        $d = $request->validated();
        if ($d['linked_entity_type'] === 'Land') {
            $d['linked_entity_id'] = $land->id;
        } elseif (! array_key_exists($d['linked_entity_id'], LookupController::entityOptions($land, $d['linked_entity_type']))) {
            return $this->refuse('The selected '.mb_strtolower($d['linked_entity_type']).' record is not on '.$land->land_ref_no.'.');
        }
        $doc = $svc->store($request->file('file'), $land, collect($d)->except(['file', 'land_id'])->all());

        return $this->ok("{$doc->code} uploaded".($doc->version_no > 1 ? " as version {$doc->version_no}" : ''), $this->tabUrl($land, 'doc'));
    }

    /** Documents cannot be edited (template rule): the drawer shows details read-only. */
    public function show(Document $document)
    {
        $this->guardLand($document->land);

        return $this->drawer('Document details', 'DOCUMENT_REPOSITORY · '.$document->code, 'lands.forms.document',
            ['d' => $document, 'land' => $document->land, 'entityOpts' => []], true, 'doc');
    }

    public function download(Document $document)
    {
        $this->guardLand($document->land);
        $disk = Storage::disk(config('solar.upload.disk'));
        abort_unless($disk->exists($document->file_path), 404, 'The file is missing from storage.');

        return $disk->download($document->file_path, $document->file_name);
    }

    public function destroy(Document $document)
    {
        $land = $document->land;
        $this->guardLand($land);
        $document->delete(); // soft delete: file is kept for the audit trail

        return $this->ok("{$document->code} deleted", $this->tabUrl($land, 'doc'));
    }
}
