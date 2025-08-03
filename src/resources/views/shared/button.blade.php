@php
    $label ??= null;
    $type ??= 'button';
    $color ??= null;
    $icon ??= null;
    $form ??= null;
    $value ??= null;
    $name ??= null;
    $class ??= null;
@endphp

<button class="button flex-group align-items-center {{ $class }}" type="{{ $type }}" @if($form) form="{{ $form }}" @endif @if($value) value="{{ $value }}" @endif @if($name) name="{{ $name }}" @endif data-type="{{ $color }}">
    @if($icon)
        {!! icon($icon, 'small') !!}
    @endif
    {{ $label }}
</button>
