@php
    $duration ??= null;
    $floating ??= false;
@endphp

@if(session('success'))
    <alert-message type="success" duration="{{ $duration }}" @if($floating) is-floating="true" @endif>
        {{ session('success') }}
    </alert-message>
@endif

@if(session('error'))
    <alert-message type="error" duration="{{ $duration }}" @if($floating) is-floating="true" @endif>
        {{ session('error') }}
    </alert-message>
@endif

@if(session('warning'))
    <alert-message type="warning" duration="{{ $duration }}" @if($floating) is-floating="true" @endif>
        {{ session('warning') }}
    </alert-message>
@endif

@if(session('info'))
    <alert-message type="info" duration="{{ $duration }}" @if($floating) is-floating="true" @endif>
        {{ session('info') }}
    </alert-message>
@endif
