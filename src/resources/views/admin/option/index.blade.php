@extends('core-cms::admin.base')

@section('title')
    {{ __('core-cms::admin.manage') }} {{ trans_choice('core-cms::admin.option.value', 2) }}
@endsection

@section('body')
    <section class="grid">
        <div class="flex-group justify-content-space-between align-items-center" style="width: initial">
            <h2 class="heading-2 flex-group align-items-center">{!! icon('option', 'small') !!} {{ __('core-cms::admin.manage') }} {{ trans_choice('core-cms::admin.option.value', 2) }}</h2>
            <a class="button" href="{{ route('admin.option.create') }}"
               data-type="primary">{{ __('core-cms::admin.add') }} {{ trans_choice('core-cms::admin.option.value', 1) }}</a>
        </div>
        <div class="card">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ __('core-cms::admin.option.key') }}</th>
                    <th>{{ __('core-cms::admin.value') }}</th>
                    <th>{{ __('core-cms::admin.option.type') }}</th>
                    <th>{{ __('core-cms::admin.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($cms_options as $item)
                    <tr>
                        <td>
                            <a href="{{ route('admin.option.edit', $item->key) }}">{{ $item->key }}</a>
                        </td>
                        <td>
                            <a href="{{ route('admin.option.edit', $item->key) }}">
                                {{-- TODO: Display content by their type: Content, image or only dispay $item->value --}}
                                {{ $item->value }}
                            </a>
                        </td>
                        <td>
                            {{ __('core-cms::admin.option.' . $item->type . '.value') }}
                        </td>
                        <td>
                            <div class="flex-group align-items-center justify-content-flex-end" style="width: initial">
                                <a href="{{ route('admin.option.edit', $item->key) }}" class="button padding-0"
                                   data-type="transparent"
                                   title="{{ __('core-cms::admin.edit') }} {{ $item->key }}">{!! icon('edit', 'small') !!}</a>
                                @if(!$item->used_by_cms)
                                    <form
                                            class="clr-red-300"
                                            action="{{ route('admin.option.destroy', $item->key) }}"
                                            method="post"
                                            onsubmit="return confirm('{{ __('core-cms::admin.delete.confirm') }}')">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="button padding-0" data-type="transparent"
                                                title="{{ __('core-cms::admin.delete.value') }} {{ $item->key }}">
                                            {!! icon('trash', 'small') !!}
                                        </button>
                                    </form>
                                @else
                                    <button type="submit" class="button padding-0" data-type="transparent"
                                            title="{{ __('core-cms::admin.delete.unable') }} {{ $item->key }}">
                                        {!! icon('ban', 'small') !!}
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$cms_options->links()}}
        </div>
    </section>
@endsection