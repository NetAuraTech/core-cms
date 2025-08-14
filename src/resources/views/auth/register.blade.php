@extends('core-cms::base')

@section('title', __('core-cms::auth.register.value'))

@section('description')
    @parent
@endsection

@section('header')
    @if($options['header'] !== "")
        @foreach($options['header']->getContent() as $block)
            @includeIf('content-manager::shared.blocks.renderer', ['block' => $block])
        @endforeach
    @endif
@endsection

@section('footer')
    @if($options['footer'] !== "")
        @foreach($options['footer']->getContent() as $block)
            @includeIf('content-manager::shared.blocks.renderer', ['block' => $block])
        @endforeach
    @endif
@endsection

@section('body')
    <section class="container padding-block-6">
        <div class="card margin-block-end-6">
            <form class="grid" method="post" action="{{ route('register') }}">
                <h1 class="heading-1 text-center">{{ __('core-cms::auth.register.value') }}</h1>
                @csrf
                @include('core-cms::shared.input', ['label' => __('core-cms::auth.account.username'), 'name' => 'username', 'value' => old('username')])
                @include('core-cms::shared.input', ['label' => __('core-cms::auth.account.email'), 'name' => 'email', 'value' => old('email')])
                @include('core-cms::shared.input', ['label' => __('core-cms::auth.account.password.value'), 'name' => 'password', 'type' => 'password'])
                @include('core-cms::shared.input', ['label' => __('core-cms::auth.account.password.confirm'), 'name' => 'password_confirmation', 'type' => 'password'])
                <div class="flex-group justify-content-space-between" style="width: initial">
                    <a href="{{ route('login') }}" class="auth-password-forgot">{{ __('core-cms::auth.account.has') }}</a>
                </div>
                @include('core-cms::shared.button', ['type' => 'submit', 'label' => __('core-cms::auth.register.value'), 'color' => 'primary'])
                <p class="clr-neutral-400">
                    {{ __('core-cms::auth.register.info') }}
                </p>
            </form>
        </div>
        @php
            $hasSSO = config('services.facebook.client_id')
                || config('services.google.client_id');
        @endphp
        @if($hasSSO)
            <div class="card">
                <div
                        class="grid"
                        style="grid-template-columns: 375px; justify-content: center;"
                >
                    <h2 class="heading-2 text-center">{{ __('core-cms::auth.social') }}</h2>
                    @if(config('services.facebook.client_id'))
                        <a href="{{ route('oauth.connect', 'facebook') }}"
                           title="{{ __('core-cms::auth.login.with') }} Facebook"
                           class="button flex-group align-items-center"
                           data-type="facebook"
                           style="width: initial"
                        >
                            <svg class="icon small">
                                <use xlink:href="/social.svg#facebook"></use>
                            </svg>
                            {{ __('core-cms::auth.login.with') }} Facebook
                        </a>
                    @endif
                    @if(config('services.google.client_id'))
                        <a href="{{ route('oauth.connect', 'google') }}"
                           title="{{ __('core-cms::auth.login.with') }} Google"
                           class="button flex-group align-items-center"
                           data-type="google"
                           style="width: initial"
                        >
                            <svg class="icon small">
                                <use xlink:href="/social.svg#google"></use>
                            </svg>
                            {{ __('core-cms::auth.login.with') }} Google
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </section>
@endsection