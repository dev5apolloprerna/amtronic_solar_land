<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\LandRefService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Land Reference Number format and other system settings. */
class SettingController extends Controller
{
    public function index(LandRefService $refs)
    {
        return view('settings.index', [
            'format' => $refs->format(),
            'pad' => $refs->padding(),
            'scope' => $refs->scope(),
            'example' => $refs->build(['{PARK}' => 'BHV', '{STATE}' => 'GJ', '{CITY}' => 'BHV', '{TALUKA}' => 'TLJ'], 1),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'format' => ['required', 'string', 'max:80', 'regex:/^(\{(PARK|STATE|CITY|TALUKA|SEQ)\}|[A-Z0-9\-\/_.])+$/'],
            'seq_pad' => ['required', 'integer', 'min:1', 'max:8'],
            'seq_scope' => ['required', Rule::in(['prefix', 'park'])],
        ], ['format.regex' => 'Use only the tokens {PARK} {STATE} {CITY} {TALUKA} {SEQ} and the separators - / _ . or capital letters and digits.']);

        foreach (['{PARK}', '{SEQ}'] as $must) {
            if (! str_contains($data['format'], $must)) {
                return $this->refuse("The format must contain $must so every number is unique and linked to its park.");
            }
        }
        if ($data['seq_scope'] === 'prefix' && ! str_contains($data['format'], '{TALUKA}')) {
            return $this->refuse('With a sequence per prefix the format must include {TALUKA}; otherwise use one sequence per park.');
        }

        Setting::updateOrCreate(['key' => 'land_ref.format'], ['value' => $data['format'], 'description' => 'Land Reference Number format']);
        Setting::updateOrCreate(['key' => 'land_ref.seq_pad'], ['value' => (string) $data['seq_pad'], 'description' => 'Digits in the sequence']);
        Setting::updateOrCreate(['key' => 'land_ref.seq_scope'], ['value' => $data['seq_scope'], 'description' => 'prefix = per park+location, park = one per park']);

        return $this->ok('Settings saved · existing Land Reference Numbers are not changed');
    }
}
