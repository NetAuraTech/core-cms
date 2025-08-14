@extends('core-cms::base')

@section('title', __('core-cms::auth.login.value'))

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
        @if (session('status'))
            <alert-message type="success" is-floating="true">
                {{ __('core-cms::auth.account.password.reset.confirmed') }}
            </alert-message>
        @endif
        @if (session('error') === 'user-banned')
            <alert-message type="error" is-floating="true">
                {{ __('core-cms::auth.account.account.banned') }}
            </alert-message>
        @endif
        @if(count($errors) > 0)
            @foreach( $errors->all() as $message )
                <alert-message type="danger" is-floating="true">
                    {{ $message }}
                </alert-message>
            @endforeach
        @endif
        <div class="card margin-block-end-6">
            <form class="grid" method="post" action="{{ route('login') }}">
                <h1 class="heading-1 text-center">{{ __('core-cms::auth.login.value') }}</h1>
                @csrf
                @include('core-cms::shared.input', ['label' => __('core-cms::auth.account.email'), 'name' => 'email', 'value' => old('email'), 'displayError' => false])
                @include('core-cms::shared.input', ['label' => __('core-cms::auth.account.password.value'), 'name' => 'password', 'type' => 'password', 'displayError' => false])
                <div class="flex-group justify-content-space-between" style="width: initial">
                    <div class="form-group">
                        <div class="form-switch">
                            <input type="checkbox"
                                   id="checkbox-remember"
                                   name="remember"
                                   role="switch"
                                {{ old('remember') ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="checkbox-remember"><span class="switch"></span>
                                {{ __('core-cms::auth.remember.value') }}
                            </label>
                        </div>
                    </div>
                    <a href="{{ route('password.request') }}">{{ __('core-cms::auth.account.password.forgotten.value') }}</a>
                </div>
                @include('core-cms::shared.button', ['type' => 'submit', 'label' => __('core-cms::auth.login.value'), 'color' => 'primary'])
                <div class="auth-actions">
                    <a href="{{ route('register') }}">{{ __('core-cms::auth.account.no') }} {{ __('core-cms::auth.register.value') }}</a>
                </div>
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

