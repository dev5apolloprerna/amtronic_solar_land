<div class="tabhead">
  <div><span class="tbl-name">DOCUMENTS</span><h2>Documents</h2><p>Scans and agreements for this survey number, optionally linked to its lease, sub-lease, compliance record, payment or landowner. Maximum 100 MB per file.</p></div>
  <div class="actions">@if (can_do('document', 'add'))<button class="btn pri" type="button" data-drawer="{{ route('documents.create', ['plot_id' => $plot->id]) }}">Upload document</button>@endif</div>
</div>
@php($rows = $documents->each(fn ($d) => $d->setRelation('plot', $plot)))
@include('documents._table', ['emptyText' => 'No documents uploaded for this survey number yet.'])
