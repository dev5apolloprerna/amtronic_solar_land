{{-- GET filter form: search box + any selects passed in the slot. Auto-submits (app.js). --}}
@props(['placeholder' => 'Search…', 'total' => null, 'shown' => null, 'action' => null])
<form class="bar-f" method="GET" action="{{ $action ?? url()->current() }}">
  <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ $placeholder }}" aria-label="Search">
  {{ $slot }}
  <span class="sp"></span>
  @if ($total !== null)
    <span class="meta">{{ $shown ?? $total }} of {{ $total }} rows @unless (auth()->user()->isSuper()) · limited to your SPVs @endunless</span>
  @endif
</form>
