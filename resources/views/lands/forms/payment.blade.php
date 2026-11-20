@php
  $t = fn ($type) => $readonly ? 'ro' : $type;
  $total = $shares->sum('pct');
  $farmerOpts = $shares->mapWithKeys(fn ($x) => [$x['farmer']->id => $x['farmer']->owner_name.' ('.fmt_num($x['pct']).'%)'])->all();
  $acct = $shares->mapWithKeys(fn ($x) => [$x['farmer']->id => ['name' => $x['farmer']->owner_name, 'pct' => $x['pct'], 'share' => $x['share'], 'tds' => $x['tds'],
    'paid' => $x['paid'] - ($p->exists && $p->farmer_id == $x['farmer']->id ? (float) $p->gross_amount : 0)]]);
@endphp
@include('partials.form-open', ['action' => $p->exists ? route('payments.update', $p) : route('payments.store', $land), 'model' => $p])
  <fieldset data-payment-form data-accounts='@json($acct)' data-reload="{{ $p->exists ? '' : route('payments.create', $land) }}">
    @if ($p->exists || $readonly)
      <x-field name="schedule_ro" label="Rent schedule instalment" type="ro" :value="$scheduleOpts[$schedule->id] ?? $schedule->code" span :lock="! $readonly" />
    @else
      <x-field name="schedule_id" label="Rent schedule instalment" type="select" :options="$scheduleOpts" :value="$schedule->id" req span :blank="false" />
    @endif
    <x-field name="land_ro" label="Land ref no." type="ro" :value="$land->land_ref_no" />
    <x-field name="farmer_id" label="Select farmer / owner" :type="$t('select')" :options="$farmerOpts" :value="$p->farmer_id" req />
    @unless ($readonly)
      <div class="fld span2"><div class="note-b" data-account><b>Farmer account:</b> Choose an owner to see their share, paid and remaining balance.<br>Total ownership: {{ fmt_num($total) }}%.</div></div>
    @endunless
    <x-field name="payment_date" label="Payment date" :type="$t('date')" :value="$p->payment_date" req>{{ $readonly ? fmt_date($p->payment_date) : '' }}</x-field>
    <x-field name="gross_amount" label="Payment amount (₹)" :type="$t('number')" :value="$p->gross_amount !== null ? (float) $p->gross_amount : null" req
      :hint="$readonly ? null : 'Part payment allowed. Cannot exceed selected farmer’s remaining gross rent.'">{{ $readonly ? inr($p->gross_amount) : '' }}</x-field>
    <x-field name="tds_deducted" label="TDS deducted (₹)" :type="$t('number')" :value="$p->tds_deducted !== null ? (float) $p->tds_deducted : null">{{ $readonly ? inr($p->tds_deducted) : '' }}</x-field>
    <x-field name="tds_challan_no" label="TDS challan no." :type="$t('text')" :value="$p->tds_challan_no" />
    <x-field name="net_ro" label="Net paid (₹)" type="ro" id="f_net_paid">{{ inr((float) $p->gross_amount - (float) $p->tds_deducted) }}</x-field>
    <x-field name="utr_reference" label="Bank UTR reference" :type="$t('text')" :value="$p->utr_reference" req />
    <x-field name="sap_voucher_no" label="SAP voucher no." :type="$t('text')" :value="$p->sap_voucher_no" />
  </fieldset>
  @if ($p->exists && ! $readonly)<p class="hint">Changes to each field are recorded in the audit trail with the old and new value.</p>@endif
</form>
