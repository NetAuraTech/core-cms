@extends('core-cms::shared.blocks.layouts.layout')

@php
    $block = $block ??  [];
    $useContainer = $useContainer ?? $block['use-container'] ?? true;
    $section = $section ?? 'section';
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
        $themeSwitcherClasses = [];

        if($useContainer) {
            $themeSwitcherClasses[] = 'container';
        }
    @endphp
    <div class="{{ join(" ", $themeSwitcherClasses) }}">
        @if(key_exists('title', $block)  && $block['title'] !== "")
            @include('core-cms::shared.blocks.components.title', ['block' => $block])
        @endif
        <theme-switcher></theme-switcher>
    </div>
@overwrite
