@php($g = $plot->grid)
<section class="inline-card">
  <div class="inline-bar"><h2>Grid connectivity · one-to-one</h2><span class="chip info">1 survey number → 1 connectivity record</span></div>
  <form action="{{ route('plots.grid', $plot) }}" method="POST" data-ajax novalidate>
    @csrf @method('PUT')
    <div id="inlineBody">
      <p class="hint" style="margin:0">Connectivity is kept separately from the survey details. Each survey number has one record, edited here.</p>
      @php($ro = ! can_do('grid', 'edit'))
      <fieldset>
        <x-field name="connectivity_mw" label="Connectivity (MW)" type="number" :value="$g?->connectivity_mw" :lock="$ro" />
        <x-field name="substation" label="Substation" :value="$g?->substation" :lock="$ro" />
        <x-field name="aggregator" label="Aggregator" :value="$g?->aggregator" :lock="$ro" />
        <x-field name="tfr_confirmation" label="Connectivity confirmation (TFR)" type="yn" :value="$g?->tfr_confirmation ?? 'No'" :blank="false" :lock="$ro" />
      </fieldset>
    </div>
    <div class="inline-foot">
      @if ($g)<button class="lnk" type="button" data-history="{{ route('history', ['grid', $g->id]) }}">History</button>@endif
      @unless ($ro)<button class="btn pri" type="submit">Save connectivity</button>@endunless
    </div>
  </form>
</section>
