<?php

namespace App\Http\Requests;

use App\Models\LandOwner;
use Illuminate\Validation\Validator;

class FarmerRequest extends BaseRequest
{
    protected function prepareForValidation(): void
    {
        $this->upper(['owner_pan']);
        $digits = preg_replace('/\D/', '', (string) $this->aadhaar);
        if (strlen($digits) === 12) {
            $this->merge(['aadhaar' => 'XXXX-XXXX-'.substr($digits, -4)]); // stored masked
        }
        $this->merge(['bank_ac' => preg_replace('/\s/', '', (string) $this->bank_ac) ?: null, 'owner_pan' => $this->owner_pan ?: null]);
    }

    public function rules(): array
    {
        return [
            'land_id' => $this->route('farmer') ? ['prohibited'] : ['required', 'exists:lands,id'],
            'owner_name' => ['required', 'string', 'max:150'],
            'ownership_pct' => ['required', 'numeric', 'gt:0', 'max:100'],
            'owner_pan' => ['nullable', 'regex:/^[A-Z]{5}\d{4}[A-Z]$/'],
            'aadhaar' => ['nullable', 'regex:/^XXXX-XXXX-\d{4}$/'],
            'voter_id' => ['nullable', 'string', 'max:30'],
            'bank_ac' => ['nullable', 'regex:/^\d{9,18}$/'],
            'bank_branch' => ['nullable', 'string', 'max:150'],
        ];
    }

    public function withValidator(Validator $v): void
    {
        $v->after(function (Validator $v) {
            if ($v->errors()->isNotEmpty()) {
                return;
            }
            $f = $this->route('farmer');
            $landId = $f?->land_id ?? $this->land_id;
            $others = LandOwner::where('land_id', $landId)->when($f, fn ($q) => $q->whereKeyNot($f->id));
            $other = (float) (clone $others)->sum('ownership_pct');
            if ($other + (float) $this->ownership_pct > 100.0001) {
                $v->errors()->add('ownership_pct', 'Total ownership exceeds 100%. Other owners already have '.fmt_num($other, 4).'%.');
            }
            if ($this->owner_pan && (clone $others)->where('owner_pan', $this->owner_pan)->exists()) {
                $v->errors()->add('owner_pan', "PAN {$this->owner_pan} is already on this parcel.");
            }
        });
    }

    public function messages(): array
    {
        return ['owner_pan.regex' => 'PAN must look like ABCDE1234F.', 'aadhaar.regex' => 'Enter the 12-digit Aadhaar number; it is stored masked.',
            'bank_ac.regex' => 'Bank account number must be 9 to 18 digits.', 'ownership_pct.gt' => 'Ownership percentage must be greater than 0 and at most 100.'];
    }

    public function payload(): array
    {
        $d = $this->validated();
        if ($this->route('farmer') && $d['bank_ac'] === null) {
            unset($d['bank_ac']); // blank keeps the stored account number
        }

        return $d;
    }
}
