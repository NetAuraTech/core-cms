@extends('core-cms::base')

@section('stylesheets')
    @includeIf('theme::assets.admin.css')
    <style>
        {{ $css }}
    </style>
    @php
        $contents = [$options['header'], $options['footer']];
    @endphp
    @foreach($contents as $item)
        @php
            $cacheBuster = substr(md5(json_encode($item->updated_at)), 0, 8);
            $cssPath = 'css/' . $item->slug . '.css';
        @endphp
        <link rel="preload" href="{{ route('assets.show', ['path' => $cssPath]) }}?v={{ $cacheBuster }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
        <noscript>
            <link rel="stylesheet" href="{{ route('assets.show', ['path' => $cssPath]) }}?v={{ $cacheBuster }}">
        </noscript>
    @endforeach
@overwrite

@section('header')
    @if($options['header'] !== "")
        @foreach($options['header']->getContent() as $block)
            @includeIf('core-cms::shared.blocks.renderer', ['block' => $block])
        @endforeach
    @endif
@endsection

@section('footer')
    @if($options['footer'] !== "")
        @foreach($options['footer']->getContent() as $block)
            @includeIf('core-cms::shared.blocks.renderer', ['block' => $block])
        @endforeach
    @endif
@endsection

@section('body')
    <div id="ve-components">
        @foreach($blocks as $block)
            @includeIf('core-cms::shared.blocks.renderer', ['bloc' => $block, 'css' => null])
        @endforeach
    </div>
@overwrite
