{{-- Page header: table name, title, description, action buttons --}}
@props(['title', 'table' => null, 'desc' => null])
<div class="ph">
  <div>
    @if ($table)<span class="tbl-name">{{ strtoupper($table) }}</span>@endif
    <h1>{{ $title }}</h1>
    @if ($desc)<p>{{ $desc }}</p>@endif
  </div>
  <div class="actions">{{ $slot }}</div>
</div>
