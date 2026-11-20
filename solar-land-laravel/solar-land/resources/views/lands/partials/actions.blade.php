{{-- History / Edit-or-View / Delete for a parcel-tab row. $module, $hist (id), $edit, $delete, $label --}}
<button class="lnk" type="button" data-history="{{ route('history', [$module, $hist]) }}">History</button>
@if ($edit ?? null)<button class="lnk" type="button" data-drawer="{{ $edit }}">{{ can_do($module, 'edit') ? 'Edit' : 'View' }}</button>@endif
@if (($delete ?? null) && can_do($module, 'delete'))<button class="lnk red" type="button" data-delete-url="{{ $delete }}" data-label="{{ $label }}" @isset($msg) data-message="{{ $msg }}" @endisset>Delete</button>@endif
