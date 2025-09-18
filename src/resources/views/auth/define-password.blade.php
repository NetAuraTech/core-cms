@extends('core-cms::base')

@section('title', __('core-cms::auth.account.password.define.value'))

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
        <div class="card">
            <form method="post" class="grid" action="{{ route('password.define') }}">
                <h1 class="heading-1 text-center">{{ __('core-cms::auth.account.password.define.value') }}</h1>
                <p>{{ __('core-cms::auth.account.password.define.info') }}</p>
                @csrf
                @include('core-cms::shared.form-field', ['label' => __('core-cms::auth.account.password.new'), 'name' => 'password', 'type' => 'password', 'errorLocation' => 'updatePassword'])
                @include('core-cms::shared.form-field', ['label' => __('core-cms::auth.account.password.confirm'), 'name' => 'password_confirmation', 'type' => 'password', 'errorLocation' => 'updatePassword'])
                @include('core-cms::shared.button', ['type' => 'submit', 'label' => __('core-cms::auth.account.password.define.value'), 'name' => 'action', 'value' => 'password', 'color' => 'primary'])
            </form>
        </div>
    </section>
@endsection