{{-- search + filters for a parcel tab --}}
<form class="bar-f" method="GET" action="{{ route('lands.show', $land) }}">
  <input type="hidden" name="tab" value="{{ $tab }}">
  <input type="search" name="q" value="{{ request('q') }}" placeholder="Search {{ $what }}…" aria-label="Search">
  {{ $slot ?? '' }}
  <span class="sp"></span>
  <span class="meta">{{ $rows->count() }} of {{ $total }} rows @unless (auth()->user()->isSuper()) · limited to your SPVs @endunless</span>
</form>
