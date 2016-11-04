@extends('master')

@section('title')
    Register
@endsection

@section('content')
<div class="ui grid">
    <div class="ui computer only six wide column"></div>
    <div class="ui sixteen wide mobile four wide computer column">
        <div class="ui row mt20 mb15">
            <h2 class="ui header centered">
                {{ config('app.name') }} Register
            </h2>
        </div>

        <div class="ui row">
            <div class="ui raised segment">
                <form class="ui form warning" role="form" method="POST" action="{{ url('/register') }}">
                    {{ csrf_field() }}
                    @if( $errors->has('name') || $errors->has('password') || $errors->has('email') || $errors->has('password_confirmation'))
                        <div class="ui warning message">
                            <ul class="list">
                                @if($errors->has('email'))
                                    <li>{{ $errors->first('email') }}</li>
                                @endif

                                @if( $errors->has('name'))
                                    <li>{{ $errors->first('name') }}</li>
                                @endif

                                @if( $errors->has('password') )
                                    <li>{{ $errors->first('password') }}</li>
                                @endif

                                @if ($errors->has('password_confirmation'))
                                    <li>{{ $errors->first('password_confirmation') }}</li>
                                @endif
                            </ul>
                        </div>
                    @endif

                    <!-- Name -->
                    <div class="field">
                        <div class="ui right labeled left icon input">
                            <i class="user icon"></i>
                            <input id="name" type="text" placeholder="Name" name="name" required autofocus>
                        </div>
                    </div>
                    <!-- Email -->
                    <div class="field">
                        <div class="ui right labeled left icon input">
                            <i class="mail outline icon"></i>
                            <input id="email" type="email" placeholder="Email Address" name="email" required>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="field">
                        <div class="ui right labeled left icon input">
                            <i class="key icon"></i>
                            <input id="password" type="password" placeholder="Password" name="password" required>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="field">
                        <div class="ui right labeled left icon input">
                            <i class="key icon"></i>
                            <input id="password-confirm" type="password" placeholder="Confirm Password" name="password_confirmation" required>
                        </div>
                    </div>

                    <div class="field">
                        <input type="submit" class="ui button green" value="Register">
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection
