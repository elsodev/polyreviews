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
            <div class="ui stackable grid">
                <div id="primary_col" class="ui sixteen wide column">
                    <h1>Somewhere</h1>
                    <h4 class="category"> Japanese</h4>
                    <div class="address"><i class="ui location arrow icon"></i> Somehwere</div>
                </div>
                <div id="foursquare_col" class="ui sixteen wide column">
                    <i class="ui foursquare icon"></i> From Forsquare
                </div>
                <div id="facebook_col" class="ui sixteen wide column">
                    <i class="ui facebook icon"></i> From Facebook

                </div>
                <div id="google_col" class="ui sixteen wide column">
                    <i class="ui google icon"></i> From Google

                </div>
            </div>
        </div>

        <div class="info success"><i class="ui check icon"></i> Something went wrong</div>
    </div>
@endsection

@section('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('app.keys.googlemaps') }}&libraries=places">
    </script>
    <script src="{{ asset('js/mainscript.js') }}"></script>
@endsection