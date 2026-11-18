@include('partials.form-open', ['action' => $lease->exists ? route('leases.update', $lease) : route('leases.store'), 'model' => $lease])
  <div data-lease-form data-plot-url="{{ route('lookup.plot', ['plot' => '__ID__']) }}"></div>
  <fieldset>
    <legend>Survey number</legend>
    @if ($lease->exists)
      <x-field name="plot_ro" label="Land reference no." type="ro" span lock>{{ $plot->land_ref_no }} · survey {{ $plot->surveyLabel() }}</x-field>
    @else
      <x-field name="plot_id" label="Land reference no." type="select" :options="$plotOpts" :value="$lease->plot_id" req span
        hint="Only survey numbers without an active lease are listed." />
    @endif
    <div class="fld span2" id="leasePlotInfo">
      @if ($plot)
        <div class="note-b"><b>{{ $plot->currentOwnership?->shares->map(fn ($s) => $s->party->party_name.' ('.fmt_num($s->share_percent, 4).'%)')->implode(', ') ?: 'Ownership not finalised yet' }}</b>
          · Park {{ $plot->park->park_code }} · SPV {{ $plot->spv->spv_code }} · {{ fmt_num($plot->area_under_lease_acres, 4) }} ac under lease · {{ inr($plot->rate_per_acre) }} per acre</div>
      @else
        <div class="note-b">Pick a survey number. Area, rate and escalation fill in from it; lessors are its finalised landowners.</div>
      @endif
    </div>
    <x-field name="lease_date" label="Lease date" type="date" :value="$lease->lease_date" :lock="$readonly" />
    <x-field name="status" label="Status" type="select" :options="$lease->exists ? opts('lease_status') : ['Draft' => 'Draft', 'Active' => 'Active']" :value="$lease->status" req :blank="false" :lock="$readonly"
      hint="Draft leases get no rent schedule and use no park capacity." />
  </fieldset>
  <fieldset>
    <legend>Dates &amp; registration</legend>
    <x-field name="atl_date" label="Agreement to lease (ATL) date" type="date" :value="$lease->atl_date" :lock="$readonly" />
    <x-field name="registration_date" label="Lease registration date" type="date" :value="$lease->registration_date" :lock="$readonly" />
    <x-field name="lease_start_date" label="Lease start date" type="date" :value="$lease->lease_start_date" req :lock="$readonly" />
    <x-field name="lease_duration_months" label="Lease duration (months)" type="number" :value="$lease->lease_duration_months" req :lock="$readonly" />
    <x-field name="end_ro" label="Lease end date" type="ro"><span id="leaseEnd">{{ fmt_date($lease->lease_end_date) }}</span></x-field>
    <x-field name="years_ro" label="Duration" type="ro"><span id="leaseYears">{{ $lease->lease_duration_months ? fmt_num($lease->lease_duration_months / 12).' years' : '—' }}</span></x-field>
    <x-field name="uni_number" label="UNI number" :value="$lease->uni_number" :lock="$readonly" />
    <x-field name="sro_doc_no" label="SRO document no." :value="$lease->sro_doc_no" :lock="$readonly" />
    <x-field name="agreement_no" label="Agreement no." :value="$lease->agreement_no" :lock="$readonly" />
    <x-field name="mutation_entry_no" label="Lease mutation entry (7/12)" :value="$lease->mutation_entry_no" :lock="$readonly" />
  </fieldset>
  <fieldset>
    <legend>Area &amp; capacity</legend>
    <x-field name="total_lease_area" label="Total lease area" type="number" :value="$lease->total_lease_area" req :lock="$readonly" />
    <x-field name="land_unit" label="Land unit" type="select" :options="opts('land_unit')" :value="$lease->land_unit" req :blank="false" :lock="$readonly" />
    <x-field name="leased_capacity_mw" label="Leased capacity (MW)" type="number" :value="$lease->leased_capacity_mw" req :lock="$readonly"
      hint="Contracted MW of this lease. Active leases in a park cannot exceed its installed MW on any day." />
    <div class="fld"><label>Park capacity</label><div class="ro" id="parkCapInfo">{{ $lease->exists ? fmt_mw($subUsedToday).' sub-leased today' : 'Pick a survey number' }}</div></div>
  </fieldset>
  <fieldset>
    <legend>Rent</legend>
    <x-field name="rent_type" label="Rent type" type="select" :options="opts('rent_type')" :value="$lease->rent_type" req :blank="false" :lock="$readonly" />
    <x-field name="rent_rate" label="Rent amount (₹ per acre per year, or fixed ₹ per year)" type="number" :value="$lease->rent_rate" req :lock="$readonly" />
    <x-field name="payment_frequency" label="Payment frequency" type="select" :options="opts('payment_frequency')" :value="$lease->payment_frequency" req :blank="false" :lock="$readonly" />
    <x-field name="rent_ro" label="Annual rent · first instalment" type="ro"><span id="rentPreview">{{ $lease->exists ? inr($lease->annual_rent).' / year' : '—' }}</span></x-field>
    <x-field name="escalation_pct" label="Escalation (%)" type="number" :value="$lease->escalation_pct" req :lock="$readonly" />
    <x-field name="escalation_every_yrs" label="Escalate every (years)" type="number" :value="$lease->escalation_every_yrs" req :lock="$readonly" />
    <x-field name="security_deposit" label="Security deposit (₹)" type="number" :value="$lease->security_deposit" :lock="$readonly" />
  </fieldset>
  <fieldset>
    <legend>Tax</legend>
    <x-field name="tds_applicable" label="TDS applicable (194-I)" type="yn" :value="$lease->tds_applicable" req :blank="false" :lock="$readonly" />
    <x-field name="tds_rate" label="TDS rate (%)" type="number" :value="$lease->tds_rate" :lock="$readonly" />
    <x-field name="gst_applicable" label="GST applicable" type="yn" :value="$lease->gst_applicable" req :blank="false" :lock="$readonly" />
  </fieldset>
  <fieldset>
    <legend>Agreement document</legend>
    @unless ($readonly)
      <div class="fld span2"><label for="f_agreement_document">Upload agreement</label>
        <div class="drop" data-max-mb="100"><b>Drop a file here or choose one</b><input type="file" id="f_agreement_document" name="agreement_document" accept=".pdf,.jpg,.jpeg,.png,.tif,.tiff,.doc,.docx,.zip"><span class="hint">PDF, image, Word or ZIP · maximum 100 MB · saved to Documents as a new version</span></div>
      </div>
    @endunless
    @foreach ($agreements as $d)
      <div class="file-pill span2"><span><span class="mono">{{ $d->original_name }}</span><span class="sub">v{{ $d->version_no }} · {{ fmt_ts($d->created_at) }}</span></span><a class="lnk" href="{{ route('documents.download', $d) }}">Download</a></div>
    @endforeach
  </fieldset>
  @if ($lease->exists && ! $readonly)<p class="hint">Changing dates, rent, frequency, escalation, TDS or status regenerates the unpaid instalments. Paid and part-paid instalments are kept.</p>@endif
</form>
