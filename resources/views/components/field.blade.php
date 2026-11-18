{{--
  Form field in the template's .fld style.
  <x-field name="park_code" label="Park code" :value="$park->park_code" req hint="3 letters" />
  type: text|number|date|email|select|yn|textarea|checks|ro|file
  Extra attributes (data-cascade, step, id…) are placed on the input element.
--}}
@props(['name', 'label', 'type' => 'text', 'value' => null, 'options' => [], 'req' => false, 'hint' => null, 'span' => false, 'placeholder' => '', 'lock' => false, 'blank' => 'Select…'])
@php
  $id = $attributes->get('id', 'f_'.str_replace(['[', ']'], '_', $name));
  $val = old($name, $value instanceof \Carbon\CarbonInterface ? $value->format('Y-m-d') : ($value instanceof \BackedEnum ? $value->value : $value));
  if ($type === 'yn') { $options = ['Yes' => 'Yes', 'No' => 'No']; $type = 'select'; }
  $attrs = $attributes->except('id');
@endphp
<div class="fld{{ $span ? ' span2' : '' }}">
  <label for="{{ $id }}">{{ $label }}@if ($req && $type !== 'ro' && ! $lock)<i class="req">*</i>@endif</label>
  @if ($type === 'ro' || $lock)
    <div class="ro" id="{{ $id }}">{{ $slot->isEmpty() ? (($val === null || $val === '') ? '—' : (isset($options[$val]) ? $options[$val] : $val)) : $slot }}@if ($lock)<span class="lock">LOCKED</span>@endif</div>
  @elseif ($type === 'select')
    <select id="{{ $id }}" name="{{ $name }}" {{ $attrs }}>
      @if ($blank !== false)<option value="">{{ $blank }}</option>@endif
      @foreach ($options as $k => $l)
        <option value="{{ $k }}" @selected((string) $k === (string) $val)>{{ $l }}</option>
      @endforeach
    </select>
  @elseif ($type === 'textarea')
    <textarea id="{{ $id }}" name="{{ $name }}" placeholder="{{ $placeholder }}" {{ $attrs }}>{{ $val }}</textarea>
  @elseif ($type === 'checks')
    <div class="checks" id="{{ $id }}">
      @foreach ($options as $k => $l)
        <label><input type="checkbox" name="{{ $name }}[]" value="{{ $k }}" @checked(in_array((string) $k, array_map('strval', (array) $val), true))>{{ $l }}</label>
      @endforeach
    </div>
  @elseif ($type === 'number')
    <input id="{{ $id }}" name="{{ $name }}" type="number" step="any" min="0" value="{{ $val }}" placeholder="{{ $placeholder }}" {{ $attrs }}>
  @else
    <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" value="{{ $val }}" placeholder="{{ $placeholder }}" {{ $attrs }}>
  @endif
  @if ($hint)<small class="hint">{{ $hint }}</small>@endif
</div>
