@php($t = fn ($type) => $readonly ? 'ro' : $type)
@include('partials.form-open', ['action' => route('schedules.update', $s), 'model' => $s])
  <fieldset data-schedule-form data-tds="{{ $s->lease->tds_applicable === 'Yes' ? (float) $s->lease->tds_rate : 0 }}">
    <x-field name="lease_ro" label="Lease" type="ro" :value="$s->lease->code" />
    <x-field name="land_ro" label="Land ref no." type="ro" :value="$s->land->land_ref_no" />
    <x-field name="due_month_ro" label="Due month" type="ro" :value="$s->due_month" />
    <x-field name="due_date" label="Due date" :type="$t('date')" :value="$s->due_date" req>{{ $readonly ? fmt_date($s->due_date) : '' }}</x-field>
    <x-field name="rent_amount" label="Rent amount (₹)" :type="$t('number')" :value="(float) $s->rent_amount" req>{{ $readonly ? inr($s->rent_amount) : '' }}</x-field>
    <x-field name="tds_ro" label="TDS (₹)" type="ro" id="f_tds_amount">{{ inr($s->tds_amount) }}</x-field>
    <x-field name="net_ro" label="Net payable (₹)" type="ro" id="f_net_payable">{{ inr($s->net_payable) }}</x-field>
    <x-field name="payment_status" label="Payment status" :type="$t('select')" :blank="false"
      :options="$s->payment_status === 'Paid' ? ['Paid' => 'Paid'] : ($s->payments->isNotEmpty() ? ['Partial Payment' => 'Partial Payment', 'On hold' => 'On hold'] : ['Pending' => 'Pending', 'On hold' => 'On hold'])"
      :value="$s->payment_status" :hint="$readonly ? null : 'Becomes Paid only through Payment Register.'" />
    <x-field name="alert_sent_flag" label="Due alert sent" :type="$t('yn')" :value="$s->alert_sent_flag" :blank="false" />
  </fieldset>
  <h4 style="margin:6px 0 0">Farmer-wise share of this instalment</h4>
  <div class="tbl-wrap"><table>
    <thead><tr><th>Farmer</th><th>Share</th><th>Gross</th><th>Paid</th><th>Remaining</th></tr></thead>
    <tbody>
    @foreach ($shares as $x)
      <tr><td>{{ $x['farmer']->owner_name }}</td><td>{{ fmt_num($x['pct']) }}%</td><td>{{ inr($x['share']) }}</td><td>{{ inr($x['paid']) }}</td><td><b>{{ inr($x['remaining']) }}</b></td></tr>
    @endforeach
    </tbody>
  </table></div>
</form>
