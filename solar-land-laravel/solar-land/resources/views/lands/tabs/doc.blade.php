@include('lands.partials.tabhead', ['table' => 'DOCUMENT_REPOSITORY', 'desc' => 'Scans and agreements for this parcel, optionally linked to its lease, sublease, compliance or payment record. Maximum file size is 100 MB. A file with the same title and type is saved as a new version.',
  'addUrl' => route('documents.create', $land), 'addLabel' => 'Upload document'])
@include('lands.partials.bar', ['what' => 'documents'])
<div class="tbl-wrap"><table>
  <thead><tr><th>Document</th><th>Linked to</th><th>File</th><th>Ver.</th><th>Original</th><th>Uploaded</th><th></th></tr></thead>
  <tbody>
  @forelse ($rows as $d)
    <tr>
      <td><b>{{ $d->doc_title }}</b><span class="sub">{{ $d->doc_type }}</span></td>
      <td>{{ $d->linked_entity_type }}<span class="sub mono">{{ $d->linkedCode() }}</span></td>
      <td><a class="mono" href="{{ route('documents.download', $d) }}">{{ $d->file_name }}</a><span class="sub">{{ number_format($d->file_size_kb / 1024, 1) }} MB</span></td>
      <td>{{ chip('v'.$d->version_no, 'info') }}</td>
      <td>{{ chip($d->is_original_scan) }}</td>
      <td>{{ $d->uploader?->username ?? '—' }}<span class="sub">{{ fmt_ts($d->created_at) }}</span></td>
      <td class="act">
        @if (can_do('doc', 'add'))<button class="lnk" type="button" data-drawer="{{ route('documents.create', [$land, 'type' => $d->linked_entity_type, 'id' => $d->linked_entity_id, 'doc_type' => $d->doc_type, 'title' => $d->doc_title]) }}">New version</button>@endif
        @include('lands.partials.actions', ['module' => 'doc', 'hist' => $d->id, 'edit' => route('documents.show', $d), 'delete' => route('documents.destroy', $d), 'label' => $d->code])
      </td>
    </tr>
  @empty
    <tr><td colspan="7" class="empty">{{ $total ? 'No records match the search or filters.' : 'No documents records yet'.(can_do('doc', 'add') ? '. Use “Upload document” to create the first one.' : '.') }}</td></tr>
  @endforelse
  </tbody>
</table></div>
