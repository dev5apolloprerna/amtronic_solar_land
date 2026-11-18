<form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" novalidate data-doc-form data-reload-url="{{ route('documents.create') }}">
  @csrf
  <fieldset>
    @if ($plot)
      <input type="hidden" name="plot_id" value="{{ $plot->id }}">
      <x-field name="plot_ro" label="Land reference no." type="ro" :value="$plot->land_ref_no" span lock />
      @php($linked = $pre['linked'] ?? 'Plot')
      <x-field name="linked" label="Linked to" type="select" :options="collect($records)->filter(fn ($r, $k) => $k === 'Plot' || count($r))->keys()->mapWithKeys(fn ($k) => [$k => $k])->all()" :value="$linked" req :blank="false" />
      <x-field name="record" label="Linked record" type="select" :options="$records[$linked] ?? []" :value="$pre['record'] ?? null" :blank="false" />
      <div hidden id="docRecords" data-records="{{ json_encode($records) }}"></div>
    @else
      <x-field name="plot_id" label="Land reference no." type="select" :options="$plotOpts" req span hint="Choose the survey number first; its leases, sub-leases and other records then become available." />
    @endif
    <x-field name="doc_type" label="Document type" type="select" :options="opts('doc_type')" :value="$pre['doc_type'] ?? null" req />
    <x-field name="title" label="Document title" :value="$pre['title'] ?? null" req hint="Same title + type on this survey number = next version." />
    <div class="fld span2"><label for="f_file">File<i class="req">*</i></label>
      <div class="drop" data-max-mb="100"><b>Drop a file here or choose one</b><input type="file" id="f_file" name="file" accept=".pdf,.jpg,.jpeg,.png,.tif,.tiff,.doc,.docx,.xls,.xlsx,.zip"><span class="hint">PDF, image, Word, Excel or ZIP · maximum 100 MB</span></div></div>
    <x-field name="is_original_scan" label="Scan of the original" type="yn" value="Yes" req :blank="false" />
  </fieldset>
</form>
