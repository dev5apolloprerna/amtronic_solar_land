<div class="tbl-wrap"><table>
  <thead><tr><th>Document</th><th>Land ref no.</th><th>Linked to</th><th>File</th><th>Ver.</th><th>Original</th><th>Uploaded</th><th></th></tr></thead>
  <tbody>
  @forelse ($rows as $d)
    <tr>
      <td><b>{{ $d->title }}</b><span class="sub">{{ $d->doc_type }}</span></td>
      <td>@if ($d->plot)<a class="reflink" href="{{ route('plots.show', [$d->plot_id, 'tab' => 'documents']) }}">{{ $d->plot->land_ref_no }}</a>@else — @endif</td>
      <td>{{ $d->linkedLabel() }}<span class="sub mono">#{{ $d->documentable_id }}</span></td>
      <td><span class="mono">{{ $d->original_name }}</span><span class="sub">{{ number_format($d->size_kb / 1024, 1) }} MB</span></td>
      <td>{{ chip('v'.$d->version_no, 'info') }}</td>
      <td>{{ chip($d->is_original_scan) }}</td>
      <td>{{ $d->uploader?->username ?? '—' }}<span class="sub">{{ fmt_ts($d->created_at) }}</span></td>
      <td class="act">
        <a class="lnk" href="{{ route('documents.download', $d) }}">Download</a>
        @if (can_do('document', 'add') && $d->plot_id)
          <button class="lnk" type="button" data-drawer="{{ route('documents.create', ['plot_id' => $d->plot_id, 'linked' => $d->linkedLabel(), 'record' => $d->documentable_id, 'doc_type' => $d->doc_type, 'title' => $d->title]) }}">New version</button>
        @endif
        <x-row-actions module="document" :label="$d->title.' v'.$d->version_no" :history="route('history', ['document', $d->id])" :delete="route('documents.destroy', $d)" />
      </td>
    </tr>
  @empty
    <tr><td colspan="8" class="empty">{{ $emptyText ?? 'No documents yet.' }}</td></tr>
  @endforelse
  </tbody>
</table></div>
