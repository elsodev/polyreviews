@extends('master')

@section('title')
    Home
@endsection

@section('content')
    <div class="sixteen wide columns" id="main">
        <div class="overlay" :class="{hideThis: !isMapLoading}"></div>
        <div id="mainMenu">
            <ul>
                <li>
                    <a href="#" class="siteLogo">{{ config('app.name') }}</a>
                </li>
                <li>
                    <input type="text" class="search" placeholder="Find a Restaurant" v-model="searchInput"/>
                </li>
            </ul>
        </div>
        <div id="map"></div>
        <div id="rightPane">

        </div>

        <div class="info success"><i class="ui check icon"></i> Something went wrong</div>
    </div>
@endsection

@section('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('app.keys.googlemaps') }}&libraries=places">
    </script>
    <script src="{{ asset('js/mainscript.js') }}"></script>
@endsection