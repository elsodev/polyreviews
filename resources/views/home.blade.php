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
                <!--Primary Column-->
                <div id="primary_col" class="ui sixteen wide column">
                    <h1>@{{ activePanel.primary.title }}</h1>
                    <p class="category"><b>@{{ activePanel.primary.categories }}</b></p>
                    <p class="address"><i class="ui location arrow icon"></i>
                       <a :href="activePanel.primary.addressLink" class="popMe" data-content="Get Directions" data-variation="mini"> @{{ activePanel.primary.address }} <i class="ui car icon"></i></a>
                    </p>
                    <div class="avg_ratings" style="margin-top:1px">
                        <i class="ui star icon"></i> Average Ratings
                        <div class="ui star rating" data-rating="0"></div>
                    </div>
                </div>
                <!--/Primary Column-->

                <!--Foursquare Column-->
                <div id="foursquare_col" class="ui sixteen wide column">
                    <i class="ui foursquare icon"></i> From Forsquare
                    <i title="Loading" class="hideThis loadingIcon ui  circle notched  loading icon" :class="{hideThis: !activePanel.fsq.isLoading}"></i>
                </div>
                <!--/Foursquare Column-->

                <!--Facebook Column-->
                <div id="facebook_col" class="ui sixteen wide column">
                    <i class="ui facebook icon"></i> From Facebook
                    <i title="Loading" class="hideThis loadingIcon ui circle notched loading icon" :class="{hideThis: !activePanel.fb.isLoading}"></i>

                </div>
                <!--/Facebook Column-->

                <!--Google Column-->
                <div id="google_col" class="ui sixteen wide column">
                    <i class="ui google icon"></i> From Google
                    <i title="Loading" class="hideThis loadingIcon ui  circle notched  loading icon" :class="{hideThis: !activePanel.g.isLoading}"></i>

                </div>
                <!--/Google Column-->
            </div>
        </div>

        <div class="info success"><i class="ui check icon"></i> Something went wrong</div>
    </div>
@endsection

@section('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('app.keys.googlemaps') }}&libraries=places">
    </script>
    <script src="{{ asset('js/mainscript.js') }}"></script>

    <script>
        $('.avg_ratings .rating')
                .rating({
                    initialRating: 0,
                    maxRating: 5
                });

        $('.popMe')
                .popup({
                    inline     : true,
                    hoverable  : true,
                    position   : 'top center',
                })
        ;
    </script>
@endsection