@extends('core-cms::admin.base')

@section('title')
    {{ __('core-cms::admin.manage') }} {{ trans_choice('core-cms::admin.user.value', 2) }}
@endsection

@section('body')
    <section class="grid">
        <div class="flex-group justify-content-space-between align-items-center" style="width: initial">
            <h2 class="heading-2 flex-group align-items-center">{!! icon('users', 'small') !!} {{ __('core-cms::admin.manage') }} {{ trans_choice('core-cms::admin.user.value', 2) }}</h2>
            <a class="button" href="{{ route('admin.user.create') }}" data-type="primary">{{ __('core-cms::admin.add') }} {{ trans_choice('core-cms::admin.user.value', 1) }}</a>
        </div>
        <div class="card">
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>{{ __('core-cms::admin.user.username') }}</th>
                    <th>{{ __('core-cms::admin.user.email') }}</th>
                    <th>{{ __('core-cms::admin.user.registration') }}</th>
                    <th>{{ trans_choice('core-cms::admin.role.value', 0) }}</th>
                    <th>{{ __('core-cms::admin.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>
                            <a href="{{ route('admin.user.edit', $user) }}">{{ $user->id }}</a>
                        </td>
                        <td>
                            <a href="{{ route('admin.user.edit', $user) }}">{{ $user->username }}</a>
                        </td>
                        <td>
                            {{ $user->email }}
                        </td>
                        <td>
                            {{ $user->created_at }}
                        </td>
                        <td>
                            @if(count($user->roles) > 0)
                                @foreach($user->roles as $role)
                                    <p>{{ $role->name }}</p>
                                @endforeach
                            @endif
                        </td>
                        <td>
                            <div class="flex-group align-items-center justify-content-flex-end" style="width: initial">
                                <a href="{{ route('admin.user.edit', $user) }}" class="button padding-0" data-type="transparent" title="{{ __('core-cms::admin.edit') }} {{ $user->username }}">{!! icon('edit', 'small') !!}</a>
                                @if(!$user->email_verified_at)
                                    <form action="{{ route('admin.user.confirm', $user) }}" method="post">
                                        @csrf
                                        <button type="submit" class="button padding-0" data-type="transparent" title="{{ __('core-cms::admin.user.confirm') }}">
                                            {!! icon('confirm', 'small') !!}
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.user.impersonate', $user) }}" class="button padding-0" data-type="transparent" title="{{ __('core-cms::admin.user.impersonate.value') }}">
                                        {!! icon('ninja', 'small') !!}
                                    </a>
                                @endif
                                <form
                                    action="{{ $user->status === 0 ? route('admin.user.unban', $user) : route('admin.user.ban', $user) }}"
                                    method="post"
                                    onsubmit="{{ $user->status === 0 ? 'return confirm("' . __('core-cms::admin.user.unban.confirm') . '")' : 'return confirm("' . __('core-cms::admin.user.ban.confirm') . '")' }}">
                                    @csrf
                                    <button class="button padding-0" data-type="transparent" title="{{ $user->status === 0 ? __('core-cms::admin.user.unban.value') : __('core-cms::admin.user.ban.value') }}" style="color: {{ $user->status === 0 ? 'var(--green-500);' : 'var(--red-400);' }}"
                                            type="submit">
                                        {!! icon('ban', 'small') !!}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$users->links()}}
        </div>
    </section>
@endsection
