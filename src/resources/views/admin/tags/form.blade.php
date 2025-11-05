@extends('core-cms::admin.base')

@section('title')
    @if($tag->exists)
    {{ __('core-cms::admin.edit') }} {{ trans_choice('core-cms::admin.content.tag.value', 1) }}
    @else
        {{ __('core-cms::admin.create') }} {{ trans_choice('core-cms::admin.content.tag.value', 1) }}
    @endif
@endsection

@section('body')
    <section class="grid">
        <h2 class="heading-2 flex-group align-items-center">
            {!! icon('tag', 'small') !!}
            @if($tag->exists)
            {{ __('core-cms::admin.edit') }} {{ trans_choice('core-cms::admin.content.tag.value', 1) }}
            @else
                {{ __('core-cms::admin.create') }} {{ trans_choice('core-cms::admin.content.tag.value', 1) }}
            @endif
        </h2>
        <div class="card">
            <form class="grid"
                  action="{{ route($tag->exists ? 'admin.tags.update' : 'admin.tags.store', $tag) }}"
                  method="POST">
                @csrf
                @method($tag->exists ? 'put' : 'post')
                <div class="grid">
                    @include('core-cms::shared.form-field', ['label' => __('core-cms::admin.content.name'),'name' => 'name','value' => old('name', $tag->name)])
                    @include('core-cms::shared.form-field', ['label' => __('core-cms::admin.content.slug'), 'name' => 'slug', 'value' => old('slug', $tag->slug)])
                    <div class="text-center">
                        <button type="submit" class="button" data-type="primary">{{ __('core-cms::admin.save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection