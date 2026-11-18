{{-- History / Edit-or-View / Delete links for a list row --}}
@props(['module', 'edit' => null, 'delete' => null, 'label' => '', 'history' => null, 'deleteMessage' => null])
@if ($history)<button class="lnk" type="button" data-history="{{ $history }}">History</button>@endif
{{ $slot }}
@if ($edit)
  @if (can_do($module, 'edit'))<button class="lnk" type="button" data-drawer="{{ $edit }}">Edit</button>
  @else<button class="lnk" type="button" data-drawer="{{ $edit }}">View</button>@endif
@endif
@if ($delete && can_do($module, 'delete'))
  <button class="lnk red" type="button" data-delete-url="{{ $delete }}" data-label="{{ $label }}" @if ($deleteMessage) data-message="{{ $deleteMessage }}" @endif>Delete</button>
@endif
