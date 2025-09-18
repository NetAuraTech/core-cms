@php
    use Illuminate\Support\ViewErrorBag;

    $label ??= null;
    $type ??= 'text';
    $class ??= null;
    $name ??= '';
    $id ??= $name;
    $value ??= '';
    $displayError ??= true;
    $errorLocation ??= 'default';
    $disabled ??= false;
    $help ??= null;


    $selectOptions ??= [];

    $current ??= 1;

    $getOldValue = function() use ($name, $value, $type) {
        $default = $value;
        if($type === "checkbox") {
            $default = $value ?? false;
        }

        return old($name, $default);
    };

    $hasError = function($name, $errorLocation = null) {
        /** @var ViewErrorBag $errors */
        $errors = session('errors');

        if(!$errors) {
            return false;
        }

        return $errorLocation
        ? $errors->getBag($errorLocation)->has($name)
        : $errors->has($name);
    };

    $getErrorMessage = function($name, $errorLocation = null) {
        /** @var ViewErrorBag $errors */
        $errors = session('errors');

        if(!$errors) {
            return "";
        }

        return $errorLocation
        ? $errors->getBag($errorLocation)->first($name)
        : $errors->first($name);
    };
@endphp

<div @class(['form-group', $class])>
    @if($type === 'textarea')
        <label for="{{ $id }}" class="required">{{ $label }}</label>
        <textarea
                id="{{ $id }}"
                name="{{ $name }}"
                class="form-control @if($displayError && $hasError($name, $errorLocation)) is-invalid @endif"
                @if($disabled)
                    disabled="disabled"
                @endif
        >{{ $getOldValue() }}</textarea>
    @elseif($type === 'datepicker')
        <label for="{{ $id }}" class="required">{{ $label }}</label>
        <input
                type="hidden"
                id="{{ $id }}"
                name="{{ $name }}" is="date-picker"
                class="form-control flatpickr-input @if($displayError && $hasError($name, $errorLocation)) is-invalid @endif"
                value="{{ $getOldValue() }}"
                @if($disabled)
                    disabled="disabled"
                @endif
        >
    @elseif($type === "select")
        <label for="{{ $id }}" class="required">{{ $label }}</label>
        <select id="{{ $id }}"
                name="{{ $name }}"
                class="form-control @if($displayError && $hasError($name, $errorLocation)) is-invalid @endif">
            <option value="">{{ __('core-cms::core.select.option.choose') }}</option>
            @foreach($selectOptions as $option)
                <option @selected($getOldValue() === $option->key) value="{{ $option->key }}">{{ $option->label }}</option>
            @endforeach
        </select>
    @elseif($type === "checkbox")
        <div class="form-switch">
            <input type="checkbox"
                   id="{{ $id }}"
                   name="{{ $name }}"
                   role="switch"
                   value="{{$current}}"
                   @checked( $getOldValue())
                   class="form-control @if($displayError && $hasError($name, $errorLocation)) is-invalid @endif">
            <label class="form-check-label" for="{{ $id }}"><span class="switch"></span>{{ $label }}</label>
        </div>
    @else
        <label for="{{ $id }}" class="required">{{ $label }}</label>
        <input
                type="{{ $type }}"
                id="{{ $id }}"
                name="{{ $name }}"
                value="{{ $getOldValue() }}"
                class="form-control @if($displayError && $hasError($name, $errorLocation)) is-invalid @endif"
                @if($disabled)
                    disabled="disabled"
                @endif
        >
    @endif

    @if($displayError && $hasError($name, $errorLocation))
        <div class="invalid-feedback">
            {{ $getErrorMessage($name, $errorLocation) }}
        </div>
    @endif

    @if($help)
        <div class="clr-neutral-600 margin-block-start-2">
            {{ $help }}
        </div>
    @endif
</div>