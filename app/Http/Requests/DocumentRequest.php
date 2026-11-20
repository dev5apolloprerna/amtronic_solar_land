<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class DocumentRequest extends BaseRequest
{
    public function rules(): array
    {
        $ext = implode(',', config('solar.upload.extensions'));

        return [
            'land_id' => ['required', 'exists:lands,id'],
            'linked_entity_type' => ['required', Rule::in(\App\Models\Document::LINKS)],
            'linked_entity_id' => ['required_unless:linked_entity_type,Land', 'nullable', 'integer'],
            'doc_type' => ['required', $this->opt('doc_type')],
            'doc_title' => ['required', 'string', 'max:200'],
            'is_original_scan' => ['required', $this->opt('yes_no')],
            'file' => ['required', 'file', 'max:'.config('solar.upload.max_kb'), 'mimes:'.$ext, 'extensions:'.$ext],
        ];
    }

    public function messages(): array
    {
        return ['file.required' => 'Choose a file to upload (100 MB maximum).', 'file.max' => 'The maximum upload size is 100 MB. Compress or split the file and try again.',
            'file.mimes' => 'Upload a PDF, image, Word, Excel or ZIP file.', 'file.extensions' => 'Upload a PDF, image, Word, Excel or ZIP file.',
            'linked_entity_id.required_unless' => 'Choose the linked record.'];
    }
}
