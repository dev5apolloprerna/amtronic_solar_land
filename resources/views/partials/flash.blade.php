<div id="flash" hidden>
  @if (session('success'))<span data-toast="{{ session('success') }}" data-sub="{{ session('sub') }}"></span>@endif
  @if (session('error'))<span data-toast="{{ session('error') }}"></span>@endif
</div>
