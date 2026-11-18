@include('partials.form-open', ['action' => $spv->exists ? route('spvs.update', $spv) : route('spvs.store'), 'model' => $spv])
  <fieldset>
    <legend>SPV</legend>
    <x-field name="spv_code" label="SPV code" :value="$spv->spv_code" req placeholder="PGP10" maxlength="12" hint="Locked after saving." :lock="$readonly || $spv->exists" />
    <x-field name="spv_name" label="SPV name" :value="$spv->spv_name" req :lock="$readonly" />
    <x-field name="park_id" label="Associated park" type="select" :options="$parks" :value="$spv->park_id" req :lock="$readonly" />
    <x-field name="party_id" label="Associated party / company" type="select" :options="$companies" :value="$spv->party_id" :lock="$readonly" hint="Parties of type Company." />
    <x-field name="technology" label="Technology" type="select" :options="opts('technology')" :value="$spv->technology" req :lock="$readonly" :blank="false" />
    <x-field name="capacity_mwp" label="Capacity (MWp)" type="number" :value="$spv->capacity_mwp" :lock="$readonly" />
    <x-field name="project_status" label="Project status" type="select" :options="opts('project_status')" :value="$spv->project_status" req :lock="$readonly" :blank="false" />
    <x-field name="status" label="Status" type="select" :options="opts('status')" :value="$spv->status" req :lock="$readonly" :blank="false" />
  </fieldset>
  <fieldset>
    <legend>Registration details</legend>
    <x-field name="cin" label="CIN" :value="$spv->cin" placeholder="U40106GJ2023PTC123456" maxlength="21" :lock="$readonly" />
    <x-field name="incorporation_date" label="Incorporation date" type="date" :value="$spv->incorporation_date" :lock="$readonly" />
    <x-field name="registered_address" label="Registered address" type="textarea" :value="$spv->registered_address" span :lock="$readonly" />
  </fieldset>
  <fieldset>
    <legend>Contact details</legend>
    <x-field name="contact_person" label="Contact person" :value="$spv->contact_person" :lock="$readonly" />
    <x-field name="mobile" label="Mobile" :value="$spv->mobile" maxlength="10" :lock="$readonly" />
    <x-field name="email" label="Email" type="email" :value="$spv->email" span :lock="$readonly" />
  </fieldset>
</form>
