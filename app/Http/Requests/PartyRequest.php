<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ChecksLocation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class PartyRequest extends FormRequest
{
    use ChecksLocation;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $up = fn ($v) => $v === null ? null : strtoupper(trim((string) $v));
        $aadhaar = preg_replace('/\D/', '', (string) $this->aadhaar);
        $this->merge([
            'pan' => $up($this->pan) ?: null,
            'gst_no' => $up($this->gst_no) ?: null,
            'bank_ifsc' => $up($this->bank_ifsc) ?: null,
            'bank_account_no' => preg_replace('/\s/', '', (string) $this->bank_account_no) ?: null,
            'aadhaar_last4' => strlen($aadhaar) >= 4 ? substr($aadhaar, -4) : ($this->route('party')?->aadhaar_last4),
            'aadhaar_digits' => $aadhaar,
        ]);
    }

    public function rules(): array
    {
        return [
            'party_name' => ['required', 'string', 'max:150'],
            'party_type' => ['required', Rule::in(config('solar.options.party_type'))],
            'contact_person' => ['nullable', 'string', 'max:120'],
            'mobile' => ['required', 'regex:/^[6-9]\d{9}$/'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['required', 'string', 'max:255'],
            'state_id' => ['required', 'exists:states,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'taluka_id' => ['required', 'exists:talukas,id'],
            'pincode' => ['nullable', 'digits:6'],
            'pan' => ['required', 'regex:/^[A-Z]{5}\d{4}[A-Z]$/'],
            'gst_no' => ['nullable', 'regex:/^\d{2}[A-Z]{5}\d{4}[A-Z][A-Z\d]Z[A-Z\d]$/'],
            'aadhaar_digits' => ['nullable', 'regex:/^(\d{4}|\d{12})$/'],
            'aadhaar_last4' => ['nullable', 'digits:4'],
            'voter_id' => ['nullable', 'string', 'max:30'],
            'bank_account_no' => ['nullable', 'regex:/^\d{9,18}$/'],
            'bank_ifsc' => ['nullable', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
            'bank_name_branch' => ['nullable', 'string', 'max:150'],
            'status' => ['required', Rule::in(config('solar.options.status'))],
        ];
    }

    public function withValidator(Validator $v): void
    {
        $this->checkLocation($v);
    }

    public function messages(): array
    {
        return [
            'mobile.regex' => 'Mobile must be a 10-digit Indian number starting 6–9.',
            'pan.regex' => 'PAN must look like ABCDE1234F.',
            'gst_no.regex' => 'GST number must be 15 characters, e.g. 24ABCDE1234F1Z5.',
            'aadhaar_digits.regex' => 'Enter the 12-digit Aadhaar number (only the last 4 digits are stored).',
            'bank_account_no.regex' => 'Bank account number must be 9 to 18 digits.',
            'bank_ifsc.regex' => 'IFSC must look like SBIN0001234.',
        ];
    }

    /** Only real columns (drops the helper aadhaar fields). */
    public function payload(): array
    {
        $data = collect($this->validated())->except(['aadhaar_digits'])->all();
        if ($this->route('party') && ($this->bank_account_no === null)) {
            unset($data['bank_account_no']); // blank = keep the stored (encrypted) number
        }

        return $data;
    }
}
