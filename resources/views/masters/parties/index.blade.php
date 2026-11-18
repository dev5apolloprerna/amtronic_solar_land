@extends('layouts.app')
@section('title', 'Party Master')
@section('content')
  <x-page-head title="Party Master" table="parties" desc="Landowners, companies, sub-lessees and other parties. Landowners are picked from here when adding owners to a survey number.">
    <x-export-buttons route="parties.index" module="party" />
    @if (can_do('party', 'add'))<button class="btn pri" type="button" data-drawer="{{ route('parties.create') }}">Add party</button>@endif
  </x-page-head>
  <x-filter-bar placeholder="Search name, code, PAN or mobile…" :total="$total" :shown="$rows->total()">
    <x-filter-select name="party_type" label="All types" :options="opts('party_type')" />
    <x-filter-select name="status" label="All statuses" :options="opts('status')" />
  </x-filter-bar>
  <div class="tbl-wrap"><table>
    <thead><tr><th>Party</th><th>Type</th><th>Contact</th><th>Location</th><th>PAN</th><th>GST</th><th>Status</th><th></th></tr></thead>
    <tbody>
    @forelse ($rows as $r)
      <tr>
        <td><b>{{ $r->party_name }}</b><span class="sub mono">{{ $r->party_code }}</span></td>
        <td>{{ chip($r->party_type, 'info') }}</td>
        <td>{{ $r->contact_person ?: '—' }}<span class="sub">{{ $r->mobile }}{{ $r->email ? ' · '.$r->email : '' }}</span></td>
        <td>{{ $r->taluka?->name }}, {{ $r->city?->name }}<span class="sub">{{ $r->state?->name }}</span></td>
        <td><span class="mono">{{ $r->pan ?: '—' }}</span></td>
        <td><span class="mono">{{ $r->gst_no ?: '—' }}</span></td>
        <td>{{ chip($r->status) }}</td>
        <td class="act"><x-row-actions module="party" :label="$r->party_name" :history="route('history', ['party', $r->id])" :edit="route('parties.edit', $r)" :delete="route('parties.destroy', $r)" /></td>
      </tr>
    @empty
      <tr><td colspan="8" class="empty">{{ $total ? 'No parties match the search or filters.' : 'No parties yet. Use “Add party” to create the first one.' }}</td></tr>
    @endforelse
    </tbody>
  </table></div>
  <x-pager :p="$rows" />
@endsection
