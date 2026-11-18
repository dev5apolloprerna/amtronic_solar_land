<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Plot;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/** Stores uploads. A file with the same plot + type + title becomes the next version; old versions are kept. */
class DocumentService
{
    public function nextVersion(?int $plotId, string $docType, string $title): int
    {
        return (int) Document::withTrashed()->where('plot_id', $plotId)->where('doc_type', $docType)
            ->whereRaw('LOWER(TRIM(title)) = ?', [mb_strtolower(trim($title))])->max('version_no') + 1;
    }

    public function store(UploadedFile $file, array $attrs): Document
    {
        $plot = isset($attrs['plot_id']) ? Plot::with('spv')->find($attrs['plot_id']) : null;
        $folder = 'documents/'.($plot ? $plot->spv->spv_code.'/'.$plot->land_ref_no : 'general');
        $name = now()->format('YmdHis').'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.strtolower($file->getClientOriginalExtension());
        $path = $file->storeAs($folder, $name, config('solar.upload.disk'));

        return Document::create($attrs + [
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size_kb' => (int) ceil($file->getSize() / 1024),
            'version_no' => $this->nextVersion($attrs['plot_id'] ?? null, $attrs['doc_type'], $attrs['title']),
            'uploaded_by' => auth()->id(),
        ]);
    }
}
