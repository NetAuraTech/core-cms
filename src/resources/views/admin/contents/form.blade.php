@extends('core-cms::admin.base')

@php
    $contentType = $contentType ?? "";
    $transKey = "$contentType-manager::admin.content.$contentType.value";

    if (trans()->has("core-cms::admin.content.$contentType.value")) {
        $transKey = "core-cms::admin.content.$contentType.value";
    }
@endphp

@section('title')
    @if($content->exists)
        {{ __('core-cms::admin.edit') }} {{ trans_choice($transKey, 1) }}
    @else
        {{ __('core-cms::admin.create') }} {{ trans_choice($transKey, 1) }}
    @endif
@endsection

@section('javascripts_footer')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const options = [
                    @foreach(array_merge($pages->items(), $articles->items()) as $post)
                {
                    label: "{{ $post->type }} - {{ $post->title }}",
                    value: JSON.stringify({!! json_encode([
                        'path'  => $post->type . '.show',
                        'label' => $post->title,
                        'slug'  => $post->slug,
                    ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!})
                }@if(!$loop->last),@endif
                @endforeach
            ];
            if (window.editor && typeof window.editor.initializeTheme === 'function') {
                window.editor.initializeTheme(options);
            }
        });
    </script>
@endsection

@section('body')
    <section class="grid">
        <h2 class="heading-2 flex-group align-items-center">
            {!! icon($contentType, 'small') !!}
            @if($content->exists)
                {{ __('core-cms::admin.edit') }} {{ trans_choice($transKey, 1) }}
            @else
                {{ __('core-cms::admin.create') }} {{ trans_choice($transKey, 1) }}
            @endif
        </h2>
        <div class="card">
            <form class="grid"
                  action="{{ route($content->exists ? 'admin.contents.update' : 'admin.contents.store', $content->exists ? $content : ['type' => $contentType]) }}"
                  method="POST">
                @csrf
                @method($content->exists ? 'put' : 'post')
                <div class="grid">
                    @include('core-cms::shared.form-field', ['label' => __('core-cms::admin.content.title'), 'name' => 'title', 'value' => $content->title])
                    @include('core-cms::shared.form-field', ['label' => __('core-cms::admin.content.slug'), 'name' => 'slug', 'value' => $content->slug])
                    @include('core-cms::shared.form-field', ['label' => __('core-cms::admin.content.description'), 'name' => 'description', 'value' => $content->description, 'type' => 'textarea'])
                    <editor-builder
                            id="content"
                            name="content"
                            value="{{ $content->content ?: '[]' }}"
                            preview="{{ route('admin.contents.preview', ['type' => $content->type]) }}"
                    ></editor-builder>
                    <input type="hidden" name="type" value="{{ $contentType }}">
                    @include('core-cms::shared.form-field', [
                        'type' => 'select',
                        'label' => __('core-cms::admin.content.status.value'),
                        'name' => 'status',
                        'value' => old('status', $content->status),
                        'selectOptions' => [
                            (object)['key' => 'draft', 'label' => __('core-cms::admin.content.status.draft')],
                            (object)['key' => 'published', 'label' => __('core-cms::admin.content.status.published')],
                            (object)['key' => 'archived', 'label' => __('core-cms::admin.content.status.archived')],
                        ]
                    ])
                    @foreach($formFields as $field)
                        @php
                            $fieldValue = old($field['props']['name'], $content->{$field['props']['name']} ?? null);
                        @endphp
                        @include($field['template'], [...$field['props'], 'value' => $fieldValue, 'content' => $content])
                    @endforeach
                    @include('core-cms::shared.form-field', ['label' => __('core-cms::admin.content.published_at'), 'name' => 'published_at', 'value' => $content->published_at?->format('Y-m-d H:i:s'), 'type' => 'datepicker'])
                    <div class="text-center">
                        <button type="submit" class="button" data-type="primary">{{ __('core-cms::admin.save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection