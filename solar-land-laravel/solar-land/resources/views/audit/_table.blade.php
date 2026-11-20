<div class="tbl-wrap"><table>
  <thead><tr><th>When</th><th>User</th><th>Table</th><th>Record</th><th>Action</th><th>Field</th><th>Change</th><th>IP address</th></tr></thead>
  <tbody>
  @forelse ($rows as $a)
    <tr>
      <td style="white-space:nowrap">{{ fmt_ts($a->created_at) }}<span class="sub mono">AUD{{ str_pad((string) $a->id, 5, '0', STR_PAD_LEFT) }}</span></td>
      <td>{{ $a->username }}</td>
      <td class="mono">{{ strtoupper((string) $a->table_name) }}</td>
      <td><span class="ref">{{ $a->record_label ?? $a->record_id ?? '—' }}</span></td>
      <td><span class="chip act-{{ $a->action }}">{{ $a->action }}</span></td>
      <td class="mono">{{ $a->field_name ?? '—' }}</td>
      <td style="min-width:200px">@if ($a->action === 'UPDATE')<span class="old">{{ $a->old_value ?? '(blank)' }}</span> → <span class="new">{{ $a->new_value ?? '(blank)' }}</span>@else{{ $a->new_value ?? $a->old_value }}@endif</td>
      <td class="mono">{{ $a->ip_address }}</td>
    </tr>
  @empty
    <tr><td colspan="8" class="empty">No audit entries match.</td></tr>
  @endforelse
  </tbody>
</table></div>
