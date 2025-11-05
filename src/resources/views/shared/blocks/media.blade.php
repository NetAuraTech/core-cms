@extends('core-cms::shared.blocks.layouts.layout')

@php
    $block = $block ??  [];
    $useContainer = $useContainer ?? $block['use-container'] ?? true;
    $section = $section ?? 'section';
    $classes = ['image', $block['additional-classes'] ?? ""];
@endphp


@section('class')
    {{ join(' ', $classes) }}
@overwrite

@section('element')
    {{$section}}
@overwrite

@section('content')
    @php
        $imageClasses = [];

        if($useContainer) {
            $imageClasses[] = 'container';
        }
    @endphp
    <div class="{{ join(" ", $imageClasses) }}">
        @if(key_exists('media', $block) && $block['media']['id'] != "")
            {!! image_tag($block['media']['id'], $block['media']['alt'] ?: null, $block['media']['height'] ?: null, null, 'block__' . substr(md5(json_encode($block)), 0, 8) . '-media') !!}
        @endif
    </div>
@overwrite
