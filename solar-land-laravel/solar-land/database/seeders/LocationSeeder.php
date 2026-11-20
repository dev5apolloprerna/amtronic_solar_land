<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\District;
use App\Models\State;
use Illuminate\Database\Seeder;

/** States, districts and cities / talukas exactly as in the HTML template. Add more from the masters. */
class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $st = [];
        foreach ([['Gujarat', 'GJ'], ['Maharashtra', 'MH'], ['Rajasthan', 'RJ'], ['Karnataka', 'KA'], ['Madhya Pradesh', 'MP'], ['Andhra Pradesh', 'AP']] as [$n, $c]) {
            $st[$c] = State::firstOrCreate(['state_code' => $c], ['state_name' => $n])->id;
        }
        $dt = [];
        foreach ([['GJ', 'Bhavnagar'], ['GJ', 'Amreli'], ['GJ', 'Kutch'], ['MH', 'Solapur'], ['MH', 'Pune'], ['RJ', 'Jodhpur'], ['RJ', 'Bikaner'], ['KA', 'Tumakuru'], ['MP', 'Neemuch'], ['AP', 'Kurnool']] as [$s, $n]) {
            $dt[$n] = District::firstOrCreate(['state_id' => $st[$s], 'district_name' => $n]);
        }
        foreach ([['Bhavnagar', 'Talaja', 'Taluka'], ['Bhavnagar', 'Mahuva', 'Taluka'], ['Bhavnagar', 'Bhavnagar', 'City'], ['Amreli', 'Rajula', 'Taluka'], ['Kutch', 'Bhuj', 'Taluka'],
            ['Kutch', 'Rapar', 'Taluka'], ['Solapur', 'Mohol', 'Taluka'], ['Solapur', 'Pandharpur', 'Taluka'], ['Solapur', 'Solapur', 'City'], ['Pune', 'Baramati', 'Taluka'],
            ['Jodhpur', 'Phalodi', 'Tehsil'], ['Bikaner', 'Kolayat', 'Tehsil'], ['Tumakuru', 'Pavagada', 'Taluka'], ['Neemuch', 'Jawad', 'Tehsil'], ['Kurnool', 'Panyam', 'Tehsil']] as [$d, $n, $t]) {
            City::firstOrCreate(['district_id' => $dt[$d]->id, 'city_name' => $n], ['state_id' => $dt[$d]->state_id, 'city_type' => $t]);
        }
    }
}
