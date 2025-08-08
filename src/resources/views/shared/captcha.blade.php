@php
    $label ??= null;
    $name ??= '';
@endphp

<div @class(['form-group'])>
    <label for="{{ $name }}" class="required">{{ $label }}</label>
    @php
        $key = generate_challenge();
    @endphp
    <puzzle-captcha
        name="{{ $name }}"
        width="350"
        height="200"
        piece-width="80"
        piece-height="50"
        src="{{ route('captcha.image', ['key' => $key]) }}"
    >
        <input type="hidden" name="captcha-challenge" id="captcha-challenge" value="{{ $key }}">
        <input type="hidden" name="captcha-answer" id="captcha-answer">
    </puzzle-captcha>
    <div class="clr-neutral-600">
        {{ __('core-cms::core.captcha.help') }}
    </div>
</div>
