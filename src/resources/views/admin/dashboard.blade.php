@extends('core-cms::admin.base')

@section('title')
    {{ __('core-cms::admin.dashboard') }}
@endsection

@section('body')
    <div>
        @foreach($widgets as $widgetClass)
            @php
                $widget = new $widgetClass();
            @endphp
            {{ $widget->render() }}
        @endforeach
    </div>
@endsection