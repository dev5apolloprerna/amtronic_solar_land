<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\LandAccess;
use App\Http\Requests\SurveyRequest;
use App\Models\Land;
use App\Models\LandSurvey;

/** LAND_SURVEY · survey / plot numbers of a parcel */
class LandSurveyController extends Controller
{
    use LandAccess;

    public function create(Land $land)
    {
        $this->guardLand($land);
        $s = new LandSurvey(['land_id' => $land->id, 'state_id' => $land->state_id, 'district_id' => $land->district_id, 'city_id' => $land->city_id]);

        return $this->drawer('Add survey / plot no.', 'LAND_SURVEY · '.$land->land_ref_no, 'lands.forms.survey', ['s' => $s, 'land' => $land] + $this->locationOptions($s->state_id, $s->district_id), module: 'survey');
    }

    public function store(SurveyRequest $request, Land $land)
    {
        $this->guardLand($land);
        $d = $request->validated();
        $d['land_id'] = $land->id;
        $d['area_acres'] = round((float) $d['area_sqm'] / config('solar.sqm_per_acre'), 2);
        LandSurvey::create($d);

        return $this->ok('Survey / plot no. saved', $this->tabUrl($land, 'survey'));
    }

    public function edit(LandSurvey $survey)
    {
        $this->guardLand($survey->land);
        $ro = ! $this->canEdit('survey');

        return $this->drawer(($ro ? 'View' : 'Edit').' survey / plot no.', 'LAND_SURVEY · '.$survey->code, 'lands.forms.survey',
            ['s' => $survey, 'land' => $survey->land] + $this->locationOptions($survey->state_id, $survey->district_id), $ro, 'survey');
    }

    public function update(SurveyRequest $request, LandSurvey $survey)
    {
        $this->guardLand($survey->land);
        $d = $request->validated();
        $d['area_acres'] = round((float) $d['area_sqm'] / config('solar.sqm_per_acre'), 2);
        $survey->update($d);

        return $this->ok('Survey / plot no. saved', $this->tabUrl($survey->land, 'survey'));
    }

    public function destroy(LandSurvey $survey)
    {
        $this->guardLand($survey->land);
        if ($survey->land->documents()->where('linked_entity_type', 'Survey')->where('linked_entity_id', $survey->id)->exists()) {
            return $this->refuse("{$survey->code} has documents linked to it. Delete those documents first.");
        }
        $survey->delete();

        return $this->ok("{$survey->code} deleted", $this->tabUrl($survey->land, 'survey'));
    }
}
