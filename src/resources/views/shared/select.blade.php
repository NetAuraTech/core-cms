@php
    $label ??= null;
    $class ??= null;
    $name ??= '';
    $value ??= '';
    $selectOptions ??= [];
    $help ??= null;
@endphp

<div @class(['form-group', $class])>
    <label for="{{ $name }}" class="required">{{ $label }}</label>
    <select id="{{ $name }}"
            name="{{ $name }}"
            class="form-control @error($name) is-invalid @enderror">
        <option value="">{{ __('core-cms::core.select.option.choose') }}</option>
        @foreach($selectOptions as $option)
            <option @selected(old($name, $value) === $option->key) value="{{ $option->key }}">{{ $option->label }}</option>
        @endforeach
    </select>
    @error($name)
    <div class="invalid-feedback">
        {{ $message }}
    </div>
    @enderror
    @if($help)
        <div class="clr-neutral-600 margin-block-start-2">
            {{ $help }}
        </div>
    @endif
</div>
