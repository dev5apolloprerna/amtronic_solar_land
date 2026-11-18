{{-- Pagination in the template's button style --}}
@props(['p'])
@if ($p->hasPages())
  <nav class="pager" aria-label="Pages">
    <span>Page {{ $p->currentPage() }} of {{ $p->lastPage() }}</span>
    @if ($p->onFirstPage())<span class="dis">‹ Prev</span>@else<a href="{{ $p->previousPageUrl() }}">‹ Prev</a>@endif
    @foreach ($p->getUrlRange(max(1, $p->currentPage() - 2), min($p->lastPage(), $p->currentPage() + 2)) as $n => $url)
      @if ($n === $p->currentPage())<span class="cur">{{ $n }}</span>@else<a href="{{ $url }}">{{ $n }}</a>@endif
    @endforeach
    @if ($p->hasMorePages())<a href="{{ $p->nextPageUrl() }}">Next ›</a>@else<span class="dis">Next ›</span>@endif
  </nav>
@endif
