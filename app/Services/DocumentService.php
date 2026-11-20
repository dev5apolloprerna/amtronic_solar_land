<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Land;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/** Stores uploads under documents/<SPV>/<land ref>/. Same parcel + type + title = next version; old versions are kept. */
class DocumentService
{
    public function nextVersion(int $landId, string $type, string $title): int
    {
        return (int) Document::withTrashed()->where('land_id', $landId)->where('doc_type', $type)
            ->whereRaw('LOWER(TRIM(doc_title)) = ?', [mb_strtolower(trim($title))])->max('version_no') + 1;
    }

    public function store(UploadedFile $file, Land $land, array $attrs): Document
    {
        $folder = 'documents/'.$land->spv->spv_code.'/'.$land->land_ref_no;
        $name = now()->format('YmdHis').'_'.Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)).'.'.strtolower($file->getClientOriginalExtension());
        $path = $file->storeAs($folder, $name, config('solar.upload.disk'));

        return Document::create($attrs + [
            'land_id' => $land->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size_kb' => (int) ceil($file->getSize() / 1024),
            'mime_type' => $file->getClientMimeType(),
            'version_no' => $this->nextVersion($land->id, $attrs['doc_type'], $attrs['doc_title']),
            'uploaded_by' => auth()->id(),
        ]);
    }
}
