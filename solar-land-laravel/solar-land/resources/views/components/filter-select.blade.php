@props(['name', 'label', 'options' => []])
<select name="{{ $name }}" aria-label="{{ $label }}">
  <option value="">{{ $label }}</option>
  @foreach ($options as $k => $l)
    <option value="{{ $k }}" @selected((string) request($name) === (string) $k)>{{ $l }}</option>
  @endforeach
</select>
