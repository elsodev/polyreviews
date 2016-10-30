@extends('master')

@section('title')
    Login
@endsection

@section('content')
    <div class="ui grid">
        <div class="ui computer only six wide column"></div>
        <div class="ui sixteen wide mobile four wide computer column">
            <div class="ui row mt20 mb15">
                <h2 class="ui header centered">Polyreview Login</h2>
            </div>
            <div class="ui row">
                <div class="ui segments">
                    <div class="ui secondary segment">
                        <form class="ui form warning" role="form" method="POST" action="{{ url('/login') }}">
                            {{ csrf_field() }}
                            @if( $errors->has('email') || $errors->has('password'))
                                <div class="ui warning message">
                                    <ul class="list">
                                        @if( $errors->has('email'))
                                            <li>{{ $errors->first('email') }}</li>
                                        @endif

                                        @if( $errors->has('password') )
                                            <li>{{ $errors->first('password') }}</li>
                                        @endif
                                    </ul>
                                </div>
                            @endif

                            <div class="field">
                                <div class="ui right labeled left icon input">
                                    <i class="mail outline icon"></i>
                                    <input type="email" placeholder="Email Address" name="email" required>
                                </div>
                            </div>

                            <div class="field">
                                <div class="ui right labeled left icon input">
                                    <i class="key icon"></i>
                                    <input type="password" placeholder="Password" name="password" required>
                                </div>
                            </div>

                            <div class="field clearfix">
                                <div class="ui checkbox">
                                    <input type="checkbox" tabindex="0" class="" name="remember">
                                    <label>Remember Me</label>
                                </div>
                            </div>

                            <div class="field">
                                <input type="submit" class="ui green button" value="Login"/>

                                <div style="float: right;">
                                    <a href="{{ url('/register') }}">Register an Account</a>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
