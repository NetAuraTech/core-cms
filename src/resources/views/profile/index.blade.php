@php use Illuminate\Contracts\Auth\MustVerifyEmail; @endphp
@extends('core-cms::base')

@section('title', __('core-cms::core.profile.title'))

@section('description')
    @parent
@endsection

@section('header')
    @if($options['header'] !== "")
        @foreach($options['header']->getContent() as $block)
            @includeIf('core-cms::shared.blocks.renderer', ['block' => $block])
        @endforeach
    @endif
@endsection

@section('footer')
    @if($options['footer'] !== "")
        @foreach($options['footer']->getContent() as $block)
            @includeIf('core-cms::shared.blocks.renderer', ['block' => $block])
        @endforeach
    @endif
@endsection

@section('body')
    <section class="container grid-aside padding-block-6">
        <aside>
            @if(session()->has('impersonate'))
                <div class="card margin-block-end-6">
                    <p>{{ __('core-cms::admin.user.impersonate.info') }}</p>
                    <a href="{{ route('admin.user.impersonate.leave') }}" class="button" data-type="primary">{{ __('core-cms::admin.user.impersonate.leave') }}</a>
                </div>
            @endif
            @php
                $hasSSO = config('services.facebook.client_id')
                    || config('services.google.client_id');
            @endphp
            @if($hasSSO)
                <div class="card margin-block-end-6">
                    <h3 class="heading-3">{{ __('core-cms::core.profile.social.value') }}</h3>
                    <div class="grid">
                        <p>{{ __('core-cms::core.profile.social.info') }}</p>
                        @if(config('services.facebook.client_id'))
                            <a href="{{ route($user->facebook_id  ? 'oauth.unlink' : 'oauth.connect', 'facebook') }}"
                               title="{{ $user->facebook_id ? __('core-cms::core.profile.social.unlink.value') : __('core-cms::core.profile.social.link.value') }} Facebook"
                               class="button flex-group align-items-center"
                               data-type="facebook"
                               style="width: initial; font-size: .90rem; justify-content: flex-start"
                            >
                                <svg class="icon small">
                                    <use xlink:href="/social.svg#facebook"></use>
                                </svg>
                                {{ $user->facebook_id ? __('core-cms::core.profile.social.unlink.value') : __('core-cms::core.profile.social.link.value') }}
                                Facebook
                            </a>
                        @endif
                        @if(config('services.google.client_id'))
                            <a href="{{ route($user->google_id  ? 'oauth.unlink' : 'oauth.connect', 'google') }}"
                               title="{{ $user->google_id ? __('core-cms::core.profile.social.unlink.value') : __('core-cms::core.profile.social.link.value') }} Google"
                               class="button flex-group align-items-center"
                               data-type="google"
                               style="width: initial; font-size: .90rem; justify-content: flex-start"
                            >
                                <svg class="icon small">
                                    <use xlink:href="/social.svg#google"></use>
                                </svg>
                                {{ $user->google_id ? __('core-cms::core.profile.social.unlink.value') : __('core-cms::core.profile.social.link.value') }}
                                Google
                            </a>
                        @endif
                        @if(config('services.github.client_id'))
                            <a href="{{ route($user->github_id  ? 'oauth.unlink' : 'oauth.connect', 'github') }}"
                               title="{{ $user->github_id ? __('core-cms::core.profile.social.unlink.value') : __('core-cms::core.profile.social.link.value') }} Github"
                               class="button flex-group align-items-center"
                               data-type="github"
                               style="width: initial; font-size: .90rem; justify-content: flex-start"
                            >
                                <svg class="icon small">
                                    <use xlink:href="/social.svg#github"></use>
                                </svg>
                                {{ $user->github_id ? __('core-cms::core.profile.social.unlink.value') : __('core-cms::core.profile.social.link.value') }}
                                Github
                            </a>
                        @endif
                    </div>
                </div>
            @endif
            <div class="card margin-block-end-6">
                <h3 class="heading-3 flex-group align-items-center justify-content-space-between"
                    style="width: initial">
                    {{ __('core-cms::core.profile.notifications.value') }}
                    <form class="clr-red-300" action="{{ route('profile.clean-notification') }}" method="post">
                        @csrf
                        @method('delete')
                        @include('core-cms::shared.button', ['type' => 'submit', 'icon' => 'trash', 'color' => 'transparent', 'class' => 'padding-0'])
                    </form>
                </h3>
                <div class="grid">
                    @forelse ($notifications as $notification)
                        <a href="{{ $notification['url'] }}">
                            {!! $notification['message'] !!}
                        </a>
                    @empty
                        <p class="">
                            {{ __('core-cms::core.profile.notifications.empty') }}
                        </p>
                    @endforelse
                </div>
            </div>
            @if($hasActivity)
                @if(count($comments) > 0)
                    <div class="card">
                        <h3 class="heading-3">{{ __('core-cms::core.profile.comments.value') }}</h3>
                        <table class="table">
                            <thead>
                            <tr>
                                <th width="40%">{{ __('core-cms::core.profile.comments.article') }}</th>
                                <th>{{ __('core-cms::core.profile.comments.comment') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($comments as $comment)
                                <tr>
                                    <td>
                                        <a href="{{ route('article.show', $comment->getContent()->slug) }}">
                                            {{ $comment->getContent()->title }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ $comment->content }}
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @endif
            @endif
        </aside>
        <main>
            <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                @csrf
            </form>
            <div class="card margin-block-end-6">
                <form method="post" class="grid" action="{{ route('profile.update') }}">
                    @csrf
                    @method('patch')
                    <h2 class="heading-2 flex-group align-items-center">
                        {!! icon('user', 'small') !!}
                        {{ __('core-cms::auth.account.username') }}
                    </h2>
                    @if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <alert-message type="warning" duration="0">
                            {{ __('core-cms::core.profile.email.unverified') }}
                            @include('core-cms::shared.button', ['type' => 'submit', 'label' => __('core-cms::core.profile.email.verify.value'), 'form' => 'send-verification', 'color' => 'link'])
                        </alert-message>
                    @endif
                    @include('core-cms::shared.form-field', ['label' => __('core-cms::auth.account.username'), 'name' => 'username', 'value' => $user->username])
                    @include('core-cms::shared.form-field', ['label' => __('core-cms::auth.account.email'), 'name' => 'email', 'value' => $user->email])
                    @include('core-cms::shared.button', ['type' => 'submit', 'label' => __('core-cms::core.profile.details.edit'), 'name' => 'action', 'value' => 'update', 'color' => 'primary'])
                </form>
            </div>
            <div class="card margin-block-end-6">
                <form method="post" class="grid" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')
                    <h2 class="heading-2 flex-group align-items-center">
                        {!! icon('lock', 'small') !!}
                        {{ __('core-cms::auth.account.password.value') }}
                    </h2>
                    @include('core-cms::shared.form-field', ['label' => __('core-cms::auth.account.password.current'), 'name' => 'current_password', 'type' => 'password', 'errorLocation' => 'updatePassword'])
                    @include('core-cms::shared.form-field', ['label' => __('core-cms::auth.account.password.new'), 'name' => 'password', 'type' => 'password', 'errorLocation' => 'updatePassword'])
                    @include('core-cms::shared.form-field', ['label' => __('core-cms::auth.account.password.confirm'), 'name' => 'password_confirmation', 'type' => 'password', 'errorLocation' => 'updatePassword'])
                    @include('core-cms::shared.button', ['type' => 'submit', 'label' => __('core-cms::core.profile.password.edit'), 'name' => 'action', 'value' => 'password', 'color' => 'primary'])
                </form>
            </div>
            <div class="card">
                <form method="post" action="{{ route('profile.destroy') }}" class="grid">
                    @csrf
                    @method('delete')
                    <h2 class="heading-2 clr-red-300 flex-group align-items-center">
                        {!!  icon('trash', 'small') !!}
                        {{ __('core-cms::core.profile.warning.value') }}
                    </h2>
                    <p>
                        {{ __('core-cms::core.profile.warning.confirm.value') }}<br>
                        {{ __('core-cms::core.profile.warning.confirm.info') }}<br>
                        {{ __('core-cms::core.profile.warning.confirm.password') }}
                    </p>
                    @include('core-cms::shared.form-field', ['label' => __('core-cms::auth.account.password.current'), 'name' => 'password', 'type' => 'password', 'errorLocation' => 'userDeletion'])
                    @include('core-cms::shared.button', ['type' => 'submit', 'label' => __('core-cms::core.profile.warning.delete'), 'icon' => 'trash', 'color' => 'primary'])
                </form>
            </div>
        </main>
    </section>
@endsection
