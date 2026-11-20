<?php

namespace App\Http\Requests;

use App\Models\LandSurvey;
use Illuminate\Validation\Validator;

class SurveyRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'land_id' => $this->route('survey') ? ['prohibited'] : ['required', 'exists:lands,id'],
            'state_id' => ['required', 'exists:states,id'],
            'district_id' => ['required', 'exists:districts,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'village' => ['required', 'string', 'max:100'],
            'survey_plot_no' => ['required', 'string', 'max:30'],
            'area_sqm' => ['required', 'numeric', 'gt:0'],
        ];
    }

    public function withValidator(Validator $v): void
    {
        $this->checkLocation($v);
        $v->after(function (Validator $v) {
            $s = $this->route('survey');
            $landId = $s?->land_id ?? $this->land_id;
            $dupe = LandSurvey::where('land_id', $landId)->whereRaw('LOWER(village) = ?', [mb_strtolower(trim((string) $this->village))])
                ->where('survey_plot_no', trim((string) $this->survey_plot_no))->when($s, fn ($q) => $q->whereKeyNot($s->id))->exists();
            if ($dupe) {
                $v->errors()->add('survey_plot_no', "Survey {$this->survey_plot_no} in {$this->village} is already on this parcel.");
            }
        });
    }
}
