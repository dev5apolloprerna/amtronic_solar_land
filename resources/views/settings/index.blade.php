@extends('layouts.app')
@section('title', 'Settings')
@section('content')
  <x-page-head title="Settings" table="settings" desc="Land Reference Number format. Changes apply to survey numbers created from now on; existing numbers never change." />
  <section class="inline-card">
    <div class="inline-bar"><h2>Land Reference Number</h2><span class="ref">{{ $example }}</span></div>
    <form action="{{ route('settings.update') }}" method="POST" data-ajax novalidate>
      @csrf @method('PUT')
      <div id="inlineBody">
        @php($ro = ! can_do('setting', 'edit'))
        <fieldset>
          <x-field name="format" label="Format" :value="$format" req span :lock="$ro"
            hint="Tokens: {PARK} park code · {STATE} state code · {CITY} city code · {TALUKA} taluka code · {SEQ} sequence. Must contain {PARK} and {SEQ}." />
          <x-field name="seq_pad" label="Sequence digits" type="number" :value="$pad" req :lock="$ro" hint="4 gives 0001." />
          <x-field name="seq_scope" label="Sequence runs" type="select" :options="['prefix' => 'Per park + state + city + taluka', 'park' => 'One sequence per park']" :value="$scope" req :blank="false" :lock="$ro" />
        </fieldset>
        <div class="note-b">Each number is reserved inside a database transaction with a row lock on its sequence, so two users saving at the same moment never get the same number. The model refuses any change to a saved Land Reference Number.</div>
      </div>
      @unless ($ro)<div class="inline-foot"><button class="lnk" type="button" data-history="{{ route('history', ['setting', \App\Models\Setting::where('key', 'land_ref.format')->value('id') ?? 0]) }}">History</button><button class="btn pri" type="submit">Save settings</button></div>@endunless
    </form>
  </section>
@endsection
