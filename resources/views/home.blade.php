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
                    <input type="text" class="searchBox" placeholder="Find a Restaurant" v-model="searchInput"/>
                </li>

                <li style="max-width: 200px">
                    <select class="ui search dropdown" id="hood_dropdown" style="max-width: 200px;">
                        @foreach($areas as $area)
                            <optgroup label="{{ $area->name }}">
                                @foreach($area->neighbourhoods as $hood)
                                    <option value="{{ $hood->name }}">{{ $hood->name }} {{ ($hood->other_name) ? '- '.$hood->other_name : '' }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </li>
                @if(count($categories) > 0)
                <li  style="max-width: 200px">
                    <select class="ui search dropdown" id="category_dropdown" style="max-width: 200px;">
                        <option value="all">All</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </li>
                @endif
            </ul>
        </div>
        <div id="map"></div>
        <div id="rightPane">

            <div class="ui stackable grid">
                <!--Primary Column-->
                <div id="primary_col" class="ui sixteen wide column" style="position: relative;">
                    <h1>@{{ activePanel.primary.title }}</h1>
                    <p class="category"><b>@{{ activePanel.primary.categories }}</b></p>
                    <p class="address"><i class="ui location arrow icon"></i>
                       <a :href="activePanel.primary.addressLink" class="popMe" data-content="Get Directions" data-variation="mini"> @{{ activePanel.primary.address }} <i class="ui car icon"></i></a>
                    </p>
                    <div class="avg_ratings" style="margin-top:1px">
                        <i class="ui star icon"></i> Average Ratings
                        <div class="ui star rating" data-rating="0"></div>
                    </div>

                    <div class="x hideThis" :class="{hideThis: !isRightPaneOpen}" v-on:click="closeRightPane"></div>
                </div>
                <!--/Primary Column-->

                <!--Foursquare Column-->
                <div id="foursquare_col" class="ui sixteen wide column">
                    <i class="ui foursquare icon"></i> From Forsquare
                    <i title="Loading" class="hideThis loadingIcon ui  circle notched  loading icon" :class="{hideThis: !activePanel.fsq.isLoading}"></i>

                    <div class="ui relaxed list">
                        <div class="item">
                            <div class="content">
                                <div class="data_ratings hideThis" :class="{hideThis: activePanel.fsq.isLoading}">
                                    <b>Ratings</b>
                                    <div class="ui star rating" data-rating="0"></div>
                                    <small>@{{ activePanel.fsq.no_of_ratings }} ratings</small>
                                </div>
                            </div>
                        </div><!-- /Ratings-->

                        <div class="item">
                            <div class="content">
                                <b>Price</b> <span class="pricing" v-html="activePanel.fsq.price"></span>
                            </div>
                        </div><!-- /Price -->

                        <div class="item">
                            <div class="content">
                                <b>Tips</b>
                                <ul class="tipSlide">
                                    <li v-for="tip in activePanel.fsq.tips">
                                        <a :href="tip.canonicalUrl" target="_blank">
                                            @{{ tip.user.firstName }} says "@{{ tip.text }}"
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="item">
                            <div class="content">
                                <a :href="activePanel.fsq.link" target="_blank" class="ui icon small fluid basic button hideThis" :class="{hideThis: activePanel.fsq.isLoading}">
                                    View on Foursquare&nbsp;&nbsp;<i class="ui external icon"></i>
                                </a>
                            </div>
                        </div><!-- /View button-->

                    </div>



                </div>
                <!--/Foursquare Column-->

                <!--Facebook Column-->
                <div id="facebook_col" class="ui sixteen wide column">
                    <i class="ui facebook icon"></i> From Facebook
                    <i title="Loading" class="hideThis loadingIcon ui circle notched loading icon" :class="{hideThis: !activePanel.fb.isLoading}"></i>

                    <a :href="activePanel.fb.link" target="_blank" class="ui icon small fluid basic button hideThis" :class="{hideThis : activePanel.fb.isLoading}">
                        View on Facebook&nbsp;&nbsp;<i class="ui external icon"></i>
                    </a>

                </div>
                <!--/Facebook Column-->

                <!--Google Column-->
                <div id="google_col" class="ui sixteen wide column">

                    <i class="ui google icon"></i> From Google
                    <i title="Loading" class="hideThis loadingIcon ui  circle notched  loading icon" :class="{hideThis: !activePanel.g.isLoading}"></i>
                    <div class="ui relaxed divided list hideThis" :class="{hideThis: activePanel.g.isLoading}">
                        <div class="item" v-for="item in activePanel.g.results">
                            <div class="content">
                                <a :href="item.link" target="_blank">
                                    <b>@{{ item.title }}</b><br>
                                    <small>@{{ item.description }}</small>
                                </a>
                            </div>
                        </div>

                        <div class="item">
                            <div class="content">
                                <a :href="activePanel.g.link" target="_blank" class="ui icon small fluid basic button hideThis" :class="{hideThis : activePanel.g.isLoading}">
                                    View on Google&nbsp;&nbsp;<i class="ui external icon"></i>
                                </a>
                            </div>
                        </div>
                    </div>



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
                    maxRating: 5,
                    interactive: false
                });

        $('.data_ratings .rating').rating({maxRating: 5, interactive: false});

        $('.popMe')
                .popup({
                    inline     : true,
                    hoverable  : true,
                    position   : 'top center',
                })
        ;
        $('#hood_dropdown').dropdown({
            maxSelections: 1,
            metadata : {
                placeholderText : 'Neighbourhood',
            }
        });

        $('#category_dropdown').dropdown({
            maxSelections: 1,
            metadata : {
                placeholderText : 'Categories',
            }
        });
    </script>
@endsection