@extends('layouts.app')
@section('title', 'Document Repository')
@section('content')
  <x-page-head title="Document Repository" table="documents" desc="Scans and agreements, linked to a survey number and optionally to its lease, sub-lease, compliance record, payment or landowner. A file with the same title and type is saved as a new version; old versions are kept.">
    <x-export-buttons route="documents.index" module="document" />
    @if (can_do('document', 'add'))<button class="btn pri" type="button" data-drawer="{{ route('documents.create') }}">Upload document</button>@endif
  </x-page-head>
  <x-filter-bar placeholder="Search title, file or land ref…" :total="$total" :shown="$rows->total()">
    <x-filter-select name="doc_type" label="All types" :options="opts('doc_type')" />
    <x-filter-select name="linked" label="Linked to: any" :options="array_combine(array_keys(\App\Models\Document::LINKABLE), array_keys(\App\Models\Document::LINKABLE))" />
  </x-filter-bar>
  @include('documents._table', ['emptyText' => $total ? 'No documents match the search or filters.' : null])
  <x-pager :p="$rows" />
@endsection
