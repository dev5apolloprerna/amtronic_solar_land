<?php

namespace App\Http\Requests;

use App\Enums\LandUnit;
use App\Http\Requests\Concerns\ChecksLocation;
use App\Models\Park;
use App\Models\Plot;
use App\Models\Spv;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class PlotRequest extends FormRequest
{
    use ChecksLocation;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'village' => trim((string) $this->village),
            'survey_no' => strtoupper(trim((string) $this->survey_no)),
            'sub_survey_no' => strtoupper(trim((string) $this->sub_survey_no)),
        ]);
    }

    public function rules(): array
    {
        $plot = $this->route('plot');
        $o = fn ($k) => Rule::in(config("solar.options.$k"));

        return [
            // Park and location feed the Land Reference Number; they lock once it exists.
            'park_id' => $plot ? ['prohibited'] : ['required', 'exists:parks,id'],
            'spv_id' => ['required', 'exists:spvs,id'],
            'state_id' => $plot ? ['prohibited'] : ['required', 'exists:states,id'],
            'city_id' => $plot ? ['prohibited'] : ['required', 'exists:cities,id'],
            'taluka_id' => $plot ? ['prohibited'] : ['required', 'exists:talukas,id'],
            'village' => ['required', 'string', 'max:100'],
            'survey_no' => ['required', 'string', 'max:30', 'regex:/^[A-Z0-9\/\-]+$/'],
            'sub_survey_no' => ['nullable', 'string', 'max:30', 'regex:/^[A-Z0-9\/\-]*$/'],
            'total_area' => ['required', 'numeric', 'gt:0'],
            'land_unit' => ['required', $o('land_unit')],
            'area_under_lease_acres' => ['required', 'numeric', 'min:0'],
            'dlr_area_sqm' => ['nullable', 'numeric', 'min:0'],
            'rate_per_acre' => ['required', 'numeric', 'min:0'],
            'increment_after_yrs' => ['required', 'integer', 'min:0', 'max:99'],
            'increment_pct' => ['required', 'numeric', 'min:0', 'max:100'],
            'title_report' => ['required', $o('yes_no')],
            'title_dd_status' => ['required', $o('title_dd_status')],
            'lessor_capacity_verified' => ['required', $o('yes_no')],
            'encumbrance_status' => ['required', $o('encumbrance_status')],
            'lien_details' => ['nullable', 'string', 'max:2000'],
            'na_application' => ['required', $o('yes_no')],
            'na_status' => ['required', $o('na_status')],
            'land_status' => ['required', $o('land_status')],
        ];
    }

    public function withValidator(Validator $v): void
    {
        if (! $this->route('plot')) {
            $this->checkLocation($v);
        }
        $v->after(function (Validator $v) {
            $plot = $this->route('plot');
            $parkId = $plot?->park_id ?? $this->park_id;
            $spv = Spv::find($this->spv_id);
            if ($spv && (int) $spv->park_id !== (int) $parkId) {
                $v->errors()->add('spv_id', 'The selected SPV does not belong to this park.');
            }
            if ($spv && ! $this->user()->canAccessSpv($spv->id)) {
                $v->errors()->add('spv_id', 'You do not have access to this SPV.');
            }
            if ($plot && $spv && $plot->spv_id !== $spv->id && $plot->leases()->withTrashed()->exists()) {
                $v->errors()->add('spv_id', 'This survey number already has leases under '.$plot->spv->spv_code.'. Its SPV cannot change.');
            }
            if (is_numeric($this->total_area) && is_numeric($this->area_under_lease_acres)) {
                $acres = LandUnit::acres((string) $this->land_unit, (float) $this->total_area);
                if ((float) $this->area_under_lease_acres > $acres + 0.0001) {
                    $v->errors()->add('area_under_lease_acres', 'Area under lease ('.fmt_num($this->area_under_lease_acres, 4).' ac) cannot exceed the survey number area ('.fmt_num($acres, 4).' ac).');
                }
            }
            $talukaId = $plot?->taluka_id ?? $this->taluka_id;
            $dupe = Plot::withTrashed()->where('taluka_id', $talukaId)->where('village', $this->village)->where('survey_no', $this->survey_no)
                ->where('sub_survey_no', (string) $this->sub_survey_no)->when($plot, fn ($q) => $q->whereKeyNot($plot->id))->first();
            if ($dupe) {
                $v->errors()->add('survey_no', 'Survey '.$this->survey_no.($this->sub_survey_no ? '/'.$this->sub_survey_no : '').' in '.$this->village.' already exists as '.$dupe->land_ref_no.'.');
            }
            if ($this->na_status !== 'Not applied' && $this->na_application === 'No') {
                $v->errors()->add('na_status', 'NA status can only be set after the NA application is filed.');
            }
        });
    }

    public function payload(): array
    {
        $data = $this->validated();
        $data['sub_survey_no'] = (string) ($data['sub_survey_no'] ?? '');

        return $data;
    }
}
