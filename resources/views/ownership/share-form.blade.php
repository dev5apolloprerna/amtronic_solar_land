@include('partials.form-open', ['action' => $share->exists ? route('ownership.shares.update', $share) : route('ownership.shares.store', $version), 'model' => $share])
  <div class="note-b">Other owners in draft v{{ $version->version_no }} hold <b>{{ fmt_num($others, 4) }}%</b>. This owner can have at most <b>{{ fmt_num(100 - $others, 4) }}%</b>.</div>
  <fieldset>
    <x-field name="party_id" label="Landowner" type="select" :options="$owners" :value="$share->party_id" req span :lock="$readonly"
      hint="Landowners come from the Party Master (type Landowner). Add a new one there first if they are missing." />
    <x-field name="share_percent" label="Ownership / rent share (%)" type="number" :value="$share->share_percent" req :lock="$readonly"
      data-others="{{ $others }}" hint="All owners must total exactly 100% before the version can be finalised." />
    <div class="fld"><label>Total after save</label><div class="ro" id="shareTotal">{{ fmt_num($others + (float) $share->share_percent, 4) }}%</div></div>
  </fieldset>
</form>
