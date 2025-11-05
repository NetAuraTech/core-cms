@extends('core-cms::admin.base')

@section('title')
    @if($category->exists)
    {{ __('core-cms::admin.edit') }} {{ trans_choice('core-cms::admin.content.category.value', 1) }}
    @else
        {{ __('core-cms::admin.create') }} {{ trans_choice('core-cms::admin.content.category.value', 1) }}
    @endif
@endsection

@section('body')
    <section class="grid">
        <h2 class="heading-2 flex-group align-items-center">
            {!! icon('category', 'small') !!}
            @if($category->exists)
            {{ __('core-cms::admin.edit') }} {{ trans_choice('core-cms::admin.content.category.value', 1) }}
            @else
                {{ __('core-cms::admin.create') }} {{ trans_choice('core-cms::admin.content.category.value', 1) }}
            @endif
        </h2>
        <div class="card">
            <form class="grid"
                  action="{{ route($category->exists ? 'admin.categories.update' : 'admin.categories.store', $category) }}"
                  method="POST">
                @csrf
                @method($category->exists ? 'put' : 'post')
                <div class="grid">
                    @include('core-cms::shared.form-field', ['label' => __('core-cms::admin.content.name'),'name' => 'name','value' => old('name', $category->name)])
                    @include('core-cms::shared.form-field', ['label' => __('core-cms::admin.content.slug'), 'name' => 'slug', 'value' => old('slug', $category->slug)])
                    <div class="text-center">
                        <button type="submit" class="button" data-type="primary">{{ __('core-cms::admin.save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection