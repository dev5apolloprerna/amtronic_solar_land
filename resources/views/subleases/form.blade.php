@include('partials.form-open', ['action' => $sub->exists ? route('subleases.update', $sub) : route('subleases.store'), 'model' => $sub])
  <div data-sublease-form data-cap-url="{{ route('lookup.lease-capacity', ['lease' => '__ID__']) }}" data-exclude="{{ $sub->id }}" data-lease="{{ $lease?->id }}"></div>
  <fieldset>
    <legend>Parent lease</legend>
    @if ($sub->exists)
      <x-field name="lease_ro" label="Parent lease" type="ro" span lock>{{ $lease->lease_no }} · {{ $lease->plot->land_ref_no }} · {{ fmt_mw($lease->leased_capacity_mw) }} · {{ fmt_date($lease->lease_start_date) }} to {{ fmt_date($lease->lease_end_date) }}</x-field>
    @else
      <x-field name="lease_id" label="Parent lease" type="select" :options="$leaseOpts" :value="$sub->lease_id" req span hint="Only active leases are listed." />
    @endif
    <div class="fld span2" id="capPanel">
      <div class="note-b">Pick a lease and dates. This panel shows how much MW is still free over the sub-lease period.</div>
    </div>
  </fieldset>
  <fieldset>
    <legend>Sub-lessee &amp; capacity</legend>
    <x-field name="party_id" label="Sub-lease party" type="select" :options="$parties" :value="$sub->party_id" req span :lock="$readonly" hint="Parties of type Sub-lessee, Company or Other." />
    <x-field name="capacity_mw" label="Sub-lease capacity (MW)" type="number" :value="$sub->capacity_mw" req :lock="$readonly" />
    <x-field name="status" label="Status" type="select" :options="$sub->exists ? opts('sublease_status') : ['Draft' => 'Draft', 'Active' => 'Active']" :value="$sub->status" req :blank="false" :lock="$readonly"
      hint="Only Active sub-leases use capacity. A Draft is checked again when activated." />
    <x-field name="start_date" label="Sub-lease start date" type="date" :value="$sub->start_date" req :lock="$readonly" />
    <x-field name="end_date" label="Sub-lease end date" type="date" :value="$sub->end_date" req :lock="$readonly" />
  </fieldset>
  <fieldset>
    <legend>Rent &amp; terms</legend>
    <x-field name="rent_amount" label="Sub-lease rent amount (₹ per period)" type="number" :value="$sub->rent_amount" :lock="$readonly" />
    <x-field name="rent_frequency" label="Rent frequency" type="select" :options="opts('payment_frequency')" :value="$sub->rent_frequency" req :blank="false" :lock="$readonly" />
    <x-field name="payment_terms" label="Payment terms" type="textarea" :value="$sub->payment_terms" span :lock="$readonly" />
    <x-field name="agreement_no" label="Agreement no." :value="$sub->agreement_no" :lock="$readonly" />
    <x-field name="lender_noc_obtained" label="Lender NOC obtained" type="yn" :value="$sub->lender_noc_obtained" req :blank="false" :lock="$readonly" hint="Required for an active sub-lease on a mortgaged survey number." />
    @unless ($readonly)
      <div class="fld span2"><label for="f_agreement_document">Agreement document</label>
        <div class="drop" data-max-mb="100"><b>Drop a file here or choose one</b><input type="file" id="f_agreement_document" name="agreement_document"><span class="hint">Saved to Documents as a new version</span></div></div>
    @endunless
    @foreach ($agreements as $d)
      <div class="file-pill span2"><span><span class="mono">{{ $d->original_name }}</span><span class="sub">v{{ $d->version_no }} · {{ fmt_ts($d->created_at) }}</span></span><a class="lnk" href="{{ route('documents.download', $d) }}">Download</a></div>
    @endforeach
  </fieldset>
</form>
