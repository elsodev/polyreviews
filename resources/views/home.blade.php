@extends('master')

@section('title')
    Home
@endsection

@section('content')
    <div class="sixteen wide columns" id="main">
        <div id="map"></div>
    </div>
@endsection

@section('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('app.keys.googlemaps') }}">
    </script>
    <script src="{{ asset('js/mainscript.js') }}"></script>
@endsection