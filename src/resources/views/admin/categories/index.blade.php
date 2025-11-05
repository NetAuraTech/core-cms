@extends('core-cms::admin.base')

@section('title')
    {{ __('core-cms::admin.manage') }} {{ trans_choice('core-cms::admin.content.category.value', 2) }}
@endsection

@section('body')
    <section class="grid">
        <div class="flex-group justify-content-space-between align-items-center" style="width: initial">
            <h2 class="heading-2 flex-group align-items-center">
                {!! icon('category', 'small') !!}
                {{ __('core-cms::admin.manage') }} {{ trans_choice('core-cms::admin.content.category.value', 2) }}
            </h2>
            <a class="button" href="{{ route('admin.categories.create') }}" data-type="primary">
                {{ __('core-cms::admin.add') }} {{ trans_choice('core-cms::admin.content.category.value', 1) }}
            </a>
        </div>
        <div class="card">
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>{{ __('core-cms::admin.content.name') }}</th>
                    <th>{{ __('core-cms::admin.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($categories as $category)
                <tr>
                    <td>
                        <a href="{{ route('admin.categories.edit', $category) }}">{{ $category->id }}</a>
                    </td>
                    <td>
                        <a href="{{ route('admin.categories.edit', $category) }}">{{ $category->name }}</a>
                    </td>
                    <td>
                        <div class="flex-group align-items-center justify-content-flex-end" style="width: initial">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="button padding-0" data-type="transparent" title="{{ __('core-cms::admin.edit') }} {{ $category->name }}">{!! icon('edit', 'small') !!}</a>
                            <form
                                    class="clr-red-300"
                                    action="{{ route('admin.categories.destroy', $category) }}"
                                    method="post"
                                    onsubmit="return confirm('{{ __('core-cms::admin.delete.confirm') }}')">
                                @csrf
                                @method('delete')
                                <button type="submit" class="button padding-0" data-type="transparent" title="{{ __('core-cms::admin.delete.value') }} {{ $category->name }}">
                                    {!! icon('trash', 'small') !!}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            {{$categories->links()}}
        </div>
    </section>
@endsection