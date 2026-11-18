{{-- <form> for a drawer: POST for create, PUT for update. $action, $model --}}
<form action="{{ $action }}" method="POST" enctype="multipart/form-data" novalidate>
  @csrf
  @if (($model ?? null)?->exists) @method('PUT') @endif
