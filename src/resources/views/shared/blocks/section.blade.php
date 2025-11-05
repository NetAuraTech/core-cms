@extends('core-cms::shared.blocks.layouts.layout')

@php
    $block = $block ??  [];
    $useContainer = $useContainer ?? $block['use-container'] ?? true;
    $section = $section ?? 'section';
    $classes = ['section'];
@endphp


@section('class')
    {{ join(' ', $classes) }}
@overwrite

@section('element')
    {{$section}}
@overwrite

@section('content')
    @php
        $sectionClasses = [];

        if($useContainer) {
            $sectionClasses[] = 'container';
        }

        $transitionName = null;
        if (key_exists('media-transition-name', $block) && $block['media-transition-name'] !== "") {
            $transitionName = $block['media-transition-name'];
        }
    @endphp
    <div class="{{ join(" ", $sectionClasses) }}">
        @if(key_exists('media', $block) && $block['media']['id'] !== "")
            <div class="margin-block-end-6 text-center">
                {!! image_tag($block['media']['id'], $block['media']['alt'] ?: null, $block['media']['height'] ?: null, $transitionName, 'block__' . substr(md5(json_encode($block)), 0, 8) . '-media') !!}
            </div>
        @endif
        @if(key_exists('title', $block)  && $block['title'] !== "")
            @include('core-cms::shared.blocks.components.title', ['block' => $block])
        @endif
        @if(key_exists('content', $block)  && $block['content'] !== "")
            @include('core-cms::shared.blocks.components.content', ['block' => $block])
        @endif
        @if(key_exists('ctas', $block) && count($block['ctas']) > 0)
            <div class="flex-group align-items-center margin-block-start-4">
                @foreach($block['ctas'] as $cta)
                    @include('core-cms::shared.blocks.components.cta', ['block' => $cta])
                @endforeach
            </div>
        @endif
    </div>
@overwrite
