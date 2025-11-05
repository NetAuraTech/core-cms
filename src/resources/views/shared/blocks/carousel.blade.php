@extends('core-cms::shared.blocks.layouts.layout')

@php
    $block = $block ??  [];
    $useContainer = $useContainer ?? $block['use-container'] ?? true;
    $section = 'section';
    $classes = [''];
@endphp

@section('class')
    {{ join(' ', $classes) }}
@overwrite

@section('element')
    {{$section}}
@overwrite

@section('content')
    @php
        $carouselClasses = [];

        if($useContainer) {
            $carouselClasses[] = 'container';
        }
    @endphp
    <div class="{{ join(" ", $carouselClasses) }}">
        @if(key_exists('title', $block) && $block['title'] !== "")
            @include('core-cms::shared.blocks.components.title', ['block' => $block])
        @endif
        @if(key_exists('content', $block) && $block['content'] !== "")
                @include('core-cms::shared.blocks.components.content', ['block' => $block])
        @endif
        <carousel-items class="margin-block-start-10" data-slider style="position:relative;">
            <div id="carrousel" class="carrousel" data-slider-wrapper
                 style="--items: {{ $block['items-per-page'] }}">
                @if(key_exists('layout-items', $block))
                    @foreach($block['layout-items'] as $item)
                        @includeIf('core-cms::shared.blocks.renderer', ['block' => $item, 'key' => 'item-type', 'props' => ['useContainer' => false, 'section' => 'div']])
                    @endforeach
                @endif
            </div>
            <button
                data-slider-next=""
                class="carrousel__navigation next"
                title="{{ __('pagination.next') }}"
            >
                {!! icon('arrow', 'small') !!}
            </button>
            <button
                data-slider-prev=""
                class="carrousel__navigation prev"
                title="{{ __('pagination.previous') }}"
            >
                {!! icon('arrow', 'small') !!}
            </button>
        </carousel-items>
    </div>
@overwrite
