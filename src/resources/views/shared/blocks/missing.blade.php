@extends('core-cms::shared.blocks.layouts.layout')

@php
    $block = $block ??  [];
    $key = $key ??  '_name';
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
    @endphp
    <div class="{{ join(" ", $sectionClasses) }}">
        <strong>
            {{ __('cms.block.missing', ['name' => $block[$key]]) }}
        </strong>
    </div>
@overwrite
