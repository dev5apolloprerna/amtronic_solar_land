<section class="inline-card">
  <div class="inline-bar"><h2>Grid Connectivity · One-to-One</h2><span class="chip info">1 land → 1 connectivity record</span></div>
  <form class="pb" style="padding:18px" data-ajax action="{{ route('lands.grid', $land) }}" method="POST" novalidate>
    @csrf @method('PUT')
    <p class="hint">Connectivity is maintained separately from Land Master. Each parcel has only one record, editable here.</p>
    @php($ro = ! can_do('land', 'edit'))
    <fieldset>
      <x-field name="connectivity_mw" label="Connectivity (MW)" :type="$ro ? 'ro' : 'number'" :value="$land->connectivity_mw !== null ? (float) $land->connectivity_mw : null" />
      <x-field name="substation" label="Substation" :type="$ro ? 'ro' : 'text'" :value="$land->substation" />
      <x-field name="aggregator" label="Aggregator" :type="$ro ? 'ro' : 'text'" :value="$land->aggregator" />
      <x-field name="tfr_confirmation" label="Connectivity confirmation (TFR)" :type="$ro ? 'ro' : 'yn'" :value="$land->tfr_confirmation ?? 'No'" :blank="false" />
    </fieldset>
    <div class="inline-foot"><button class="btn pri" type="submit" @disabled($ro)>Save Connectivity</button></div>
  </form>
</section>
