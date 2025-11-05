@extends('core-cms::shared.blocks.components.base')

@php
    $block = $block ?? [];
@endphp

@section('content')
    @if(key_exists('title', $block) && $block['title'] !== "")
        @include('core-cms::shared.blocks.components.title', ['block' => $block])
    @endif
    @if(key_exists('content', $block) && $block['content'] !== "")
        @include('core-cms::shared.blocks.components.content', ['block' => $block])
    @endif
    @php
        $classes = ['grid-auto-fit', 'block__' . substr(md5(json_encode($block)), 0, 8) . '-layout', $block['additional-classes'] ?? ""];
        if(key_exists('content', $block) && $block['content'] !== "" || key_exists('title', $block) && $block['title'] !== "") {
            $classes[] = 'margin-block-start-10';
        }
    @endphp
    <div class="{{ join(" ", $classes) }}">
        @foreach($block['layout-items'] as $item)
            @includeIf('core-cms::shared.blocks.renderer', ['block' => $item, 'key' => 'item-type', 'props' => ['useContainer' => false, 'section' => 'div']])
        @endforeach
    </div>
@overwrite
