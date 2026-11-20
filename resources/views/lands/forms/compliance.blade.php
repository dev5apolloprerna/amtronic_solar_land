@php($t = fn ($type) => $readonly ? 'ro' : $type)
@include('partials.form-open', ['action' => $c->exists ? route('compliance.update', $c) : route('compliance.store', $land), 'model' => $c])
  <fieldset>
    <x-field name="land_ref" label="Land reference no." type="ro" :value="$land->land_ref_no" span :lock="$c->exists && ! $readonly" />
  </fieldset>
  <fieldset><legend>Mortgage</legend>
    <x-field name="mortgage_created" label="Mortgage created" :type="$t('yn')" :value="$c->mortgage_created" :blank="false" />
    <div data-show-if="mortgage_created=Yes" @if ($c->mortgage_created !== 'Yes') hidden @endif style="display:contents">
      <x-field name="mortgage_date" label="Mortgage date" :type="$t('date')" :value="$c->mortgage_date">{{ $readonly ? fmt_date($c->mortgage_date) : '' }}</x-field>
    </div>
    <x-field name="lender_name" label="Lender name" :type="$t('text')" :value="$c->lender_name" />
    <x-field name="cersai_reg_no" label="CERSAI registration no." :type="$t('text')" :value="$c->cersai_reg_no" />
  </fieldset>
  <fieldset><legend>Original documents</legend>
    <x-field name="original_docs_custody" label="Original documents with" :type="$t('select')" :options="opts('docs_custody')" :value="$c->original_docs_custody" :blank="false" />
    <x-field name="custodian_name" label="Custodian name" :type="$t('text')" :value="$c->custodian_name" />
    <x-field name="docs_deposit_date" label="Deposit date" :type="$t('date')" :value="$c->docs_deposit_date">{{ $readonly ? fmt_date($c->docs_deposit_date) : '' }}</x-field>
    <x-field name="lender_noc_status" label="Lender NOC" :type="$t('select')" :options="opts('lender_noc_status')" :value="$c->lender_noc_status" :blank="false" />
  </fieldset>
  <fieldset><legend>Open items</legend>
    <x-field name="pdc_open_items" label="Open PDC items" :type="$t('number')" :value="$c->pdc_open_items" />
    <x-field name="target_closure_date" label="Target closure date" :type="$t('date')" :value="$c->target_closure_date">{{ $readonly ? fmt_date($c->target_closure_date) : '' }}</x-field>
    <x-field name="escalation_level" label="Escalation level" :type="$t('select')" :options="opts('escalation_level')" :value="$c->escalation_level" :blank="false" />
    <x-field name="remarks" label="Remarks" :type="$t('textarea')" :value="$c->remarks" span />
  </fieldset>
  @if ($c->exists && ! $readonly)<p class="hint">Changes to each field are recorded in the audit trail with the old and new value.</p>@endif
</form>
