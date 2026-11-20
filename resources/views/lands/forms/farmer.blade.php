@php($t = fn ($type) => $readonly ? 'ro' : $type)
@include('partials.form-open', ['action' => $f->exists ? route('farmers.update', $f) : route('farmers.store', $land), 'model' => $f])
  <fieldset>
    <x-field name="land_ref" label="Land reference no." type="ro" :value="$land->land_ref_no" span :lock="$f->exists && ! $readonly" />
    <x-field name="owner_name" label="Farmer name" :type="$t('text')" :value="$f->owner_name" req />
    <x-field name="ownership_pct" label="Ownership / Rent share (%)" :type="$t('number')" :value="$f->ownership_pct !== null ? (float) $f->ownership_pct : null" req max="100"
      :hint="$readonly ? null : 'All owners for this land must total 100% before rent distribution. '.fmt_num($left).'% is still unallocated.'" />
    <x-field name="owner_pan" label="PAN" :type="$t('text')" :value="$f->owner_pan" placeholder="ABCDE1234F" maxlength="10" style="text-transform:uppercase" />
    <x-field name="aadhaar" label="Aadhaar no." :type="$t('text')" :value="$f->aadhaar" placeholder="XXXX-XXXX-1234" :hint="$readonly ? null : 'Stored masked. Only the last 4 digits are shown.'" />
    <x-field name="voter_id" label="Voter ID" :type="$t('text')" :value="$f->voter_id" />
    <x-field name="bank_ac" label="Bank A/c no." :type="$t('text')" :value="$readonly ? $f->maskedBank() : null" inputmode="numeric"
      :placeholder="$f->exists ? $f->maskedBank() : ''" :hint="$readonly ? null : ($f->exists ? 'Stored encrypted. Leave blank to keep the saved account number.' : 'Stored encrypted; lists show only the last 4 digits.')" />
    <x-field name="bank_branch" label="Bank & branch" :type="$t('text')" :value="$f->bank_branch" />
  </fieldset>
  @if ($f->exists && ! $readonly)<p class="hint">Changes to each field are recorded in the audit trail with the old and new value.</p>@endif
</form>
