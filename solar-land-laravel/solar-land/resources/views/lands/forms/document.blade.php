@php($ext = implode(',', array_map(fn ($e) => '.'.$e, config('solar.upload.extensions'))))
@if ($readonly)
  <fieldset>
    <x-field name="land_ref" label="Land reference no." type="ro" :value="$land->land_ref_no" span />
    <x-field name="l1" label="Linked to" type="ro" :value="$d->linked_entity_type" />
    <x-field name="l2" label="Linked record" type="ro" :value="$d->linkedCode()" />
    <x-field name="l3" label="Document type" type="ro" :value="$d->doc_type" />
    <x-field name="l4" label="Document title" type="ro" :value="$d->doc_title" />
    <div class="fld span2"><label>File</label><div class="ro"><a class="mono" href="{{ route('documents.download', $d) }}">{{ $d->file_name }}</a> · {{ number_format($d->file_size_kb / 1024, 1) }} MB</div>
      <small class="hint">To replace the file, use New version on the document list. The old version is kept.</small></div>
    <x-field name="l5" label="Scan of the original" type="ro" :value="$d->is_original_scan" />
    <x-field name="l6" label="Version" type="ro" :value="'v'.$d->version_no" />
    <x-field name="l7" label="Uploaded" type="ro" :value="($d->uploader?->username ?? '—').' · '.fmt_ts($d->created_at)" span />
  </fieldset>
@else
  @include('partials.form-open', ['action' => route('documents.store', $land), 'model' => $d])
    <fieldset data-doc-form data-entities-url="{{ route('lookup.entities', $land) }}?type=">
      <x-field name="land_ref" label="Land reference no." type="ro" :value="$land->land_ref_no" span />
      <x-field name="linked_entity_type" label="Linked to" type="select" :options="array_combine(\App\Models\Document::LINKS, \App\Models\Document::LINKS)" :value="$d->linked_entity_type" :blank="false" />
      <x-field name="linked_entity_id" label="Linked record" type="select" :options="$entityOpts" :value="$d->linked_entity_type === 'Land' ? $land->id : $d->linked_entity_id" req />
      <x-field name="doc_type" label="Document type" type="select" :options="opts('doc_type')" :value="$d->doc_type" req />
      <x-field name="doc_title" label="Document title" :value="$d->doc_title" req />
      <div class="fld span2">
        <label for="f_file">File<i class="req">*</i></label>
        <div class="drop" data-max-mb="{{ (int) (config('solar.upload.max_kb') / 1024) }}">
          <b>Drop a file here or choose one</b>
          <input type="file" id="f_file" name="file" accept="{{ $ext }}">
          <span class="hint">PDF, image, Word, Excel or ZIP · maximum {{ (int) (config('solar.upload.max_kb') / 1024) }} MB · same type and title as an existing document is saved as the next version</span>
        </div>
      </div>
      <x-field name="is_original_scan" label="Scan of the original" type="yn" :value="$d->is_original_scan" :blank="false" />
    </fieldset>
  </form>
@endif
