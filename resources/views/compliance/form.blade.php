@include('partials.form-open', ['action' => $rec->exists ? route('compliance.update', $rec) : route('compliance.store'), 'model' => $rec])
  <fieldset>
    @if ($rec->exists)
      <x-field name="plot_ro" label="Land reference no." type="ro" :value="$rec->plot->land_ref_no" span lock />
    @else
      <x-field name="plot_id" label="Land reference no." type="select" :options="$plotOpts" :value="$rec->plot_id" req span hint="Survey numbers without a compliance record." />
    @endif
  </fieldset>
  <fieldset>
    <legend>Mortgage</legend>
    <x-field name="mortgage_created" label="Mortgage created" type="yn" :value="$rec->mortgage_created" req :blank="false" :lock="$readonly" />
    <x-field name="mortgage_date" label="Mortgage date" type="date" :value="$rec->mortgage_date" :lock="$readonly" />
    <x-field name="lender_name" label="Lender name" :value="$rec->lender_name" :lock="$readonly" />
    <x-field name="cersai_reg_no" label="CERSAI registration no." :value="$rec->cersai_reg_no" :lock="$readonly" />
  </fieldset>
  <fieldset>
    <legend>Original documents</legend>
    <x-field name="original_docs_custody" label="Original documents with" type="select" :options="opts('docs_custody')" :value="$rec->original_docs_custody" req :blank="false" :lock="$readonly" />
    <x-field name="custodian_name" label="Custodian name" :value="$rec->custodian_name" :lock="$readonly" />
    <x-field name="docs_deposit_date" label="Deposit date" type="date" :value="$rec->docs_deposit_date" :lock="$readonly" />
    <x-field name="lender_noc_status" label="Lender NOC" type="select" :options="opts('lender_noc_status')" :value="$rec->lender_noc_status" req :blank="false" :lock="$readonly" />
  </fieldset>
  <fieldset>
    <legend>Open items</legend>
    <x-field name="pdc_open_items" label="Open PDC items" type="number" :value="$rec->pdc_open_items" req :lock="$readonly" />
    <x-field name="target_closure_date" label="Target closure date" type="date" :value="$rec->target_closure_date" :lock="$readonly" />
    <x-field name="escalation_level" label="Escalation level" type="select" :options="opts('escalation_level')" :value="$rec->escalation_level" req :blank="false" :lock="$readonly" />
    <x-field name="remarks" label="Remarks" type="textarea" :value="$rec->remarks" span :lock="$readonly" />
  </fieldset>
</form>
