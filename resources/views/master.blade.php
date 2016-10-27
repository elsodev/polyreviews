<html>

<head>
    <title>
        @yield('title')
        - {{ config('app.name') }}
    </title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Droid+Sans" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="{{ asset('semantic-ui/semantic.min.css') }}"/>
    <link type="text/css" rel="stylesheet" href="{{  asset('css/app.css') }}"/>
    <meta name="_token" content="{!! csrf_token() !!}"/>

    @yield('head')

    <script>
        var site = {
            'url' : '{{ url('/') }}'
        };
        var locations = {!! json_encode(config('app.locations')) !!}
    </script>

</head>

<body>
@yield('content')
</body>

<script src="{{ asset('js/vue-1.0.10.min.js') }}"></script>
<script src="{{ asset('js/jquery-2.1.4.min.js') }}"></script>
<script src="{{ asset('js/xss.min.js') }}"></script>
<script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('semantic-ui/semantic.min.js') }}"></script>

<script src="{{ asset('js/app.js') }}"></script>

@yield('scripts')


<footer>
    &copy; {{ date('Y') }} {{ config('app.name') }} | Data provided by <a href="http://foursquare.com">Foursquare</a>
</footer>

</html>