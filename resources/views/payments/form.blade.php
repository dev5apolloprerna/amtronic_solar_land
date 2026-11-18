@include('partials.form-open', ['action' => $payment->exists ? route('payments.update', $payment) : route('payments.store'), 'model' => $payment])
  <div data-payment-form data-alloc-url="{{ route('lookup.allocations', ['schedule' => '__ID__']) }}" data-schedule-id="{{ $payment->exists ? '' : $schedule?->id }}"></div>
  <fieldset>
    <legend>Instalment &amp; landowner</legend>
    @if ($schedule)
      <x-field name="sch_ro" label="Rent instalment" type="ro" span>{{ $schedule->auditLabel() }} · {{ $schedule->plot->land_ref_no }} · {{ $schedule->lease->lease_no }} · due {{ fmt_date($schedule->due_date) }} · {{ inr($schedule->rent_amount, 2) }}</x-field>
    @else
      <x-field name="schedule_pick" label="Rent instalment" type="select" :options="$openSchedules" req span hint="Unpaid and part-paid instalments with a landowner split." />
    @endif
    @if ($payment->exists)
      <x-field name="owner_ro" label="Landowner" type="ro" span lock>{{ $allocation->party->party_name }} ({{ fmt_num($allocation->share_percent, 4) }}%)</x-field>
    @else
      @php($allocOpts = $schedule ? $schedule->allocations->mapWithKeys(fn ($a) => [$a->id => $a->party->party_name.' ('.fmt_num($a->share_percent, 4).'%) · balance '.inr($a->remaining(), 2)])->all() : [])
      <x-field name="rent_allocation_id" label="Select landowner" type="select" :options="$allocOpts" :value="$allocation?->id" req span />
    @endif
    <div class="fld span2" id="allocInfo">
      @if ($allocation)
        @php($bal = $allocation->remaining() + ($payment->exists ? (float) $payment->gross_amount : 0))
        <div class="note-b" data-balance="{{ $bal }}" data-tds-ratio="{{ (float) $allocation->gross_amount > 0 ? (float) $allocation->tds_amount / (float) $allocation->gross_amount : 0 }}">
          <b>{{ $allocation->party->party_name }}</b> · {{ fmt_num($allocation->share_percent, 4) }}% · Share {{ inr($allocation->gross_amount, 2) }} (TDS {{ inr($allocation->tds_amount, 2) }})
          · Already paid {{ inr($allocation->paid_amount, 2) }} · <b>Remaining {{ inr($allocation->remaining(), 2) }}</b>
          @if ($allocation->party->bank_account_no)<br>Bank {{ $allocation->party->maskedBankAccount() }} · {{ $allocation->party->bank_ifsc }} · {{ $allocation->party->bank_name_branch }}@endif
        </div>
      @else
        <div class="note-b">Select the instalment, then the landowner, to see their share, amount paid and remaining balance.</div>
      @endif
    </div>
  </fieldset>
  <fieldset>
    <legend>Payment</legend>
    <x-field name="payment_date" label="Payment date" type="date" :value="$payment->payment_date" req :lock="$readonly" />
    <x-field name="payment_mode" label="Payment mode" type="select" :options="opts('payment_mode')" :value="$payment->payment_mode" req :blank="false" :lock="$readonly" />
    <x-field name="gross_amount" label="Payment amount, gross (₹)" type="number" :value="$payment->gross_amount" req :lock="$readonly" hint="Part payment allowed. Cannot exceed the landowner’s remaining gross rent." />
    <x-field name="tds_deducted" label="TDS deducted (₹)" type="number" :value="$payment->tds_deducted ?? 0" :lock="$readonly" hint="Suggested in proportion to the gross amount." />
    <x-field name="tds_challan_no" label="TDS challan no." :value="$payment->tds_challan_no" :lock="$readonly" />
    <x-field name="net_ro" label="Net paid (₹)" type="ro"><span id="netPaid">{{ $payment->exists ? inr($payment->net_paid, 2) : '—' }}</span></x-field>
    <x-field name="utr_reference" label="Bank UTR reference" :value="$payment->utr_reference" req :lock="$readonly" />
    <x-field name="sap_voucher_no" label="SAP voucher no." :value="$payment->sap_voucher_no" :lock="$readonly" />
    <x-field name="remarks" label="Remarks" :value="$payment->remarks" span :lock="$readonly" />
    @unless ($readonly)
      <div class="fld span2"><label for="f_proof">Payment proof (optional)</label>
        <div class="drop" data-max-mb="100"><b>Drop a file here or choose one</b><input type="file" id="f_proof" name="proof"><span class="hint">Saved to Documents and linked to this payment</span></div></div>
    @endunless
  </fieldset>
</form>
