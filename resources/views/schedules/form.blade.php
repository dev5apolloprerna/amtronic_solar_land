@include('partials.form-open', ['action' => route('schedules.update', $s), 'model' => $s])
  <fieldset>
    <x-field name="lease_ro" label="Lease" type="ro" :value="$s->lease->lease_no" />
    <x-field name="ref_ro" label="Land ref no." type="ro" :value="$s->plot->land_ref_no" />
    <x-field name="period_ro" label="Period" type="ro" :value="fmt_date($s->period_from).' to '.fmt_date($s->period_to)" span />
    <x-field name="due_date" label="Due date" type="date" :value="$s->due_date" req :lock="$readonly" />
    <x-field name="rent_amount" label="Rent amount (₹)" type="number" :value="$s->rent_amount" req :lock="$readonly" />
    <x-field name="tds_ro" label="TDS (₹)" type="ro" :value="inr($s->tds_amount, 2)" />
    <x-field name="net_ro" label="Net payable (₹)" type="ro" :value="inr($s->net_payable, 2)" />
    <x-field name="status" label="Payment status" type="select" :options="$readonly ? [$s->status => $s->status] : ['Pending' => 'Pending', 'On hold' => 'On hold']" :value="$s->status" req :blank="false" :lock="$readonly"
      hint="Becomes Paid or Partially paid only through the Payment Register." />
    <x-field name="alert_sent" label="Due alert sent" type="yn" :value="$s->alert_sent" req :blank="false" :lock="$readonly" />
  </fieldset>
  @unless ($readonly)<p class="hint">Saving recalculates TDS from the lease rate and re-splits the instalment across the landowners in force on the due date.</p>@endunless
</form>
