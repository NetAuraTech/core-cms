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
        @foreach($groupedOptions as $group)
            <div class="card">
                <h2 class="heading-2 margin-block-end-6">{{ $group->label }}</h2>
                <table class="table" style="table-layout: fixed;">
                    <thead>
                    <tr>
                        <th>{{ __('core-cms::admin.option.key') }}</th>
                        <th>{{ __('core-cms::admin.option.type.value') }}</th>
                        <th>{{ __('core-cms::admin.value') }}</th>
                        <th>{{ __('core-cms::admin.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $scheduleOrder = [
                            'schedule_monday',
                            'schedule_tuesday',
                            'schedule_wednesday',
                            'schedule_thursday',
                            'schedule_friday',
                            'schedule_saturday',
                            'schedule_sunday'
                        ];

                        $scheduleOptions = collect($group->options)->filter(function($item) {
                            return str_starts_with($item->key, 'schedule_');
                        })->sortBy(function($item) use ($scheduleOrder) {
                            return array_search($item->key, $scheduleOrder);
                        });

                        $otherOptions = collect($group->options)->filter(function($item) {
                            return !str_starts_with($item->key, 'schedule_');
                        });

                        $sortedOptions = $otherOptions->concat($scheduleOptions);
                    @endphp

                    @foreach($sortedOptions as $item)
                        <tr>
                            <td>
                                <a href="{{ route('admin.option.edit', $item->key) }}">
                                    @php
                                        $translationKey = 'core-cms::admin.option.keys.' . $item->key;
                                        $translatedKey = __($translationKey);

                                        if ($translatedKey === $translationKey) {
                                            $translatedKey = $item->key;
                                        }
                                    @endphp
                                    {{ $translatedKey }}
                                </a>
                            </td>
                            <td>
                                {{ __('core-cms::admin.option.type.' . $item->type) }}
                            </td>
                            <td>
                                <a href="{{ route('admin.option.edit', $item->key) }}">
                                    @switch($item->type)
                                        @case('content')
                                        @case('template')
                                            @php
                                                $contentProvider = app(Netauratech\CoreCms\Contracts\ContentProviderInterface::class);
                                                $content = null;

                                                if($item->value !== "") {
                                                    $content = $contentProvider->getContentById($item->value);
                                                }
                                            @endphp

                                            @if ($content)
                                                {{ $content->title }}
                                            @else
                                                {{ $item->value }}
                                            @endif
                                            @break

                                        @default
                                            @php
                                                $formFields = $formFields ?? [];
                                                $field = collect($formFields)->firstWhere('type', $item->type);
                                            @endphp

                                            @if ($field)
                                                @include($field['renderer'], [...$field['props'] ?? [], 'value' => $item->value])
                                            @else
                                                @if (str_starts_with($item->key, 'schedule_') && !empty($item->value))
                                                    @php
                                                        $slots = explode('/', $item->value);
                                                        $formatted = [];
                                                        foreach ($slots as $slot) {
                                                            $times = explode('-', trim($slot));
                                                            if (count($times) === 2) {
                                                                $formatted[] = substr($times[0], 0, 5) . ' - ' . substr($times[1], 0, 5);
                                                            }
                                                        }
                                                        echo implode(' / ', $formatted);
                                                    @endphp
                                                @else
                                                    {{ $item->value ?: '—' }}
                                                @endif
                                            @endif
                                    @endswitch
                                </a>
                            </td>
                            <td>
                                <div class="flex-group align-items-center justify-content-flex-end" style="width: initial">
                                    <a href="{{ route('admin.option.edit', $item->key) }}" class="button padding-0"
                                       data-type="transparent"
                                       title="{{ __('core-cms::admin.edit') }} {{ $item->key }}">{!! icon('edit', 'small') !!}</a>
                                    @if($item->category === 'custom')
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
                                        <button type="button" class="button padding-0" data-type="transparent"
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
            </div>
        @endforeach
    </section>
@endsection