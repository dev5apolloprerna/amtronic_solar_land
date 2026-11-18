<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\State;
use App\Models\Taluka;
use Illuminate\Database\Seeder;

/** States, cities and talukas with the codes used in Land Reference Numbers. */
class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['Gujarat', 'GJ', [
                ['Bhavnagar', 'BHV', [['Talaja', 'TLJ'], ['Mahuva', 'MHV'], ['Bhavnagar', 'BVN']]],
                ['Amreli', 'AMR', [['Rajula', 'RJL']]],
                ['Kutch', 'KTC', [['Bhuj', 'BHJ'], ['Rapar', 'RPR']]],
            ]],
            ['Maharashtra', 'MH', [
                ['Solapur', 'SLP', [['Mohol', 'MHL'], ['Pandharpur', 'PDP']]],
                ['Pune', 'PUN', [['Baramati', 'BRM']]],
            ]],
            ['Rajasthan', 'RJ', [['Jodhpur', 'JDH', [['Phalodi', 'PHL']]], ['Bikaner', 'BKN', [['Kolayat', 'KLY']]]]],
            ['Karnataka', 'KA', [['Tumakuru', 'TMK', [['Pavagada', 'PVG']]]]],
            ['Madhya Pradesh', 'MP', [['Neemuch', 'NMH', [['Jawad', 'JWD']]]]],
            ['Andhra Pradesh', 'AP', [['Kurnool', 'KNL', [['Panyam', 'PNM']]]]],
        ];

        foreach ($data as [$sName, $sCode, $cities]) {
            $state = State::updateOrCreate(['code' => $sCode], ['name' => $sName, 'status' => 'Active']);
            foreach ($cities as [$cName, $cCode, $talukas]) {
                $city = City::updateOrCreate(['state_id' => $state->id, 'code' => $cCode], ['name' => $cName, 'status' => 'Active']);
                foreach ($talukas as [$tName, $tCode]) {
                    Taluka::updateOrCreate(['city_id' => $city->id, 'code' => $tCode], ['name' => $tName, 'status' => 'Active']);
                }
            }
        }
    }
}
