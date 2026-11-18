@include('partials.form-open', ['action' => $party->exists ? route('parties.update', $party) : route('parties.store'), 'model' => $party])
  <fieldset>
    <legend>Party</legend>
    @if ($party->exists)<x-field name="code_ro" label="Party code" type="ro" :value="$party->party_code" />@endif
    <x-field name="party_name" label="Party name" :value="$party->party_name" req :lock="$readonly" />
    <x-field name="party_type" label="Party type" type="select" :options="opts('party_type')" :value="$party->party_type" req :lock="$readonly" />
    <x-field name="status" label="Status" type="select" :options="opts('status')" :value="$party->status" req :lock="$readonly" :blank="false" />
  </fieldset>
  <fieldset>
    <legend>Contact &amp; address</legend>
    <x-field name="contact_person" label="Contact person" :value="$party->contact_person" :lock="$readonly" />
    <x-field name="mobile" label="Mobile number" :value="$party->mobile" req placeholder="9876543210" :lock="$readonly" maxlength="10" />
    <x-field name="email" label="Email address" type="email" :value="$party->email" :lock="$readonly" />
    <x-field name="pincode" label="PIN code" :value="$party->pincode" placeholder="364140" :lock="$readonly" maxlength="6" />
    <x-field name="address" label="Address" type="textarea" :value="$party->address" req span :lock="$readonly" />
    @include('partials.location-fields', ['model' => $party, 'ro' => $readonly])
  </fieldset>
  <fieldset>
    <legend>Tax &amp; KYC</legend>
    <x-field name="pan" label="PAN number" :value="$party->pan" req placeholder="ABCDE1234F" :lock="$readonly" maxlength="10" />
    <x-field name="gst_no" label="GST number (optional)" :value="$party->gst_no" placeholder="24ABCDE1234F1Z5" :lock="$readonly" maxlength="15" />
    <x-field name="aadhaar" label="Aadhaar number" :value="$party->aadhaar_last4 ? 'XXXX-XXXX-'.$party->aadhaar_last4 : ''" :lock="$readonly"
      hint="Only the last 4 digits are stored. Leave as is to keep them." />
    <x-field name="voter_id" label="Voter ID" :value="$party->voter_id" :lock="$readonly" />
  </fieldset>
  <fieldset>
    <legend>Bank details for rent payment</legend>
    <x-field name="bank_account_no" label="Bank account no." value="" :placeholder="$party->exists ? $party->maskedBankAccount() : ''" :lock="$readonly"
      :hint="$party->exists ? 'Stored encrypted. Leave blank to keep '.$party->maskedBankAccount().'.' : 'Stored encrypted.'" />
    <x-field name="bank_ifsc" label="IFSC" :value="$party->bank_ifsc" placeholder="SBIN0001234" :lock="$readonly" maxlength="11" />
    <x-field name="bank_name_branch" label="Bank & branch" :value="$party->bank_name_branch" span :lock="$readonly" />
  </fieldset>
</form>
