<div class="tabhead">
  <div><span class="tbl-name">RENT_PAYMENTS</span><h2>Payments</h2><p>Landowner-wise payments against this survey number’s rent instalments. Part payments are allowed up to each owner’s remaining share.</p></div>
  <div class="actions">@if (can_do('payment', 'add'))<button class="btn pri" type="button" data-drawer="{{ route('payments.create') }}">Record payment</button>@endif</div>
</div>
@php($rows = $payments)
@include('payments._table', ['emptyText' => 'No payments recorded for this survey number yet.'])
<x-pager :p="$payments" />
