@php
    $label ??= null;
    $type ??= 'text';
    $class ??= null;
    $name ??= '';
    $id ??= $name;
    $current ??= 1;
    $value ??= '';
    $help ??= null;
@endphp

<div @class(['form-group', $class])>
    <div class="form-switch">
        <input type="checkbox"
               id="{{ $id }}"
               name="{{ $name }}"
               role="switch"
               value="{{$current}}"
               @checked( old($name, $value ?? false))
               class="form-control @error($name) is-invalid @enderror">
        <label class="form-check-label" for="{{ $id }}"><span class="switch"></span>{{ $label }}</label>
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
</div>
