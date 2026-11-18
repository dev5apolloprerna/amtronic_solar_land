<div class="tabhead">
  <div><span class="tbl-name">RENT_SCHEDULES</span><h2>Rent schedule</h2><p>Instalments generated from the lease with escalation and TDS. Each one is split across the landowners in force on its due date.</p></div>
  <div class="actions"><x-export-buttons route="schedules.index" module="schedule" :params="['lease_id' => $lease?->id]" /></div>
</div>
@php($rows = $schedules)
@include('schedules._table')
<x-pager :p="$schedules" />
