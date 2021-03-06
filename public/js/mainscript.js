/**
 * @author  Elson Tan (nedex.io)
 */

var main = new Vue({
    el: '#main', // main element where vue will attaces itself to
    data: {
        // global data
        fsq_domain: 'https://foursquare.com/v/',
        isMapLoading: true, // to show an while overlay during loading
        map : null, // main map object
        markersArray: [], // storing google maps markers
        circlesArray: [], // storing google maps circle drawing

        // search data
        searchInput : '', // model for search input textbox
        isSearching: false, // to show and hide search results dropdown
        isLoadingSearchResults: true,   // to show and hide search results loading text
        searchResults: [], // to store search results
        searchMarker: null, // to temporary hold the search marker
        tempCenter: null, // Google LatLng obj,
        // for search usage, we need to back to default center when user cancel search


        // Right panel data
        isRightPaneOpen: false, // to show and hide right panel
        activePanel: { // active panel(right panel) data structures
            primary: {
                title: '',
                categories: '',
                avg_ratings: 0,
                isLoading: true
            },
            fsq: {
                isLoading: true
            },
            fb: {
                isLoading: true
            },
            g: {
                isLoading: true
            }
        }
    },

    /**
     * On Vue.js Ready
     */
    ready:function() {
        // loads google maps
        this.map = this.initMap();

        // get nearby places based on default settings
        this.getNearby();
    },

    /**
     * Vue.js Obj Methods
     */
    methods: {

        /**
         * Initialize Google Maps with Default Settings
         *
         * @returns {google.maps.Map}
         */
        initMap : function() {

            // removes all point of interest, eg. shops, restaurants icons
            var noPoi = [{
                featureType: "poi",
                stylers: [
                    { visibility: "off" }
                ]
            }
            ];

            var maps_center = {lat: locations.default_center.lat, lng: locations.default_center.lng};
            return new google.maps.Map(document.getElementById('map'), {
                zoom: 16,
                center: maps_center,
                streetViewControl: false, // disables street view
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                    position: google.maps.ControlPosition.BOTTOM_LEFT
                },
                zoomControlOptions: {
                    position: google.maps.ControlPosition.LEFT_CENTER
                },
                styles: noPoi
            });
        },

        /**
         * Obtain Nearby Locations of default settings
         */
        getNearby: function()
        {
            var me = this;

            ajaxGetJson('/get/start').success(function(data) {
                me._drawRadiusCircle({lat: parseFloat(locations.default_center.lat), lng: parseFloat(locations.default_center.lng)});
                me.loadLocations(data);
            }).error(function() {
                me.isMapLoading = false;
            });
        },

        /**
         * Search for a Place
         */
        search: function()
        {
            if($.trim(this.searchInput.length) > 2) {

                var me = this;
                var geocoder = new google.maps.Geocoder();
                var area = $('#hood_dropdown').val();

                me.isSearching = true;
                me.isLoadingSearchResults = true;

                // use google geocoder, much more accurate than foursquare's own geocoder
                geocoder.geocode({
                    'address': area
                }, function (results, status) {

                    if (status == google.maps.GeocoderStatus.OK) {

                        // send to server to use foursquare venue search api
                        ajaxGetJson('/search', {
                            'category' : $('#category_dropdown').val().split('|')[1],
                            'query' : me.searchInput,
                            'lat' :  parseFloat(results[0].geometry.location.lat()),
                            'lng' :  parseFloat(results[0].geometry.location.lng()),
                        }).success(function(data) {
                            if(data == 'null') {
                                console.log('no results');
                            } else {
                                me.searchResults = data.response.venues;
                                me.isLoadingSearchResults = false; //hides searching text
                                if(me.searchResults.length <= 0) {
                                    me.isSearching = false;
                                    infoPopUp.show('error', 'Sorry, No results found <i class="ui frown icon"></i>');
                                }
                            }
                        });

                    } else {
                        console.log('Failed to Geo code:' + $(this).val());
                    }
                });


            } else {
                infoPopUp.show('error', 'Please provide a longer search query');
            }
        },


        /**
         * Search Results Dropdown Item onClick
         *
         * @param searchItem
         */
        clickSearchResult: function(searchItem)
        {
            // check if current pins have this data
            var found = '';
            var me = this;
            for(var i in this.markersArray) {
                if(this.markersArray[i].data.venue.id == searchItem.id) {
                    found = this.markersArray[i].data;
                } else {
                    this.markersArray[i].setVisible(false); // hide all unrelated
                }
            }

            this.isSearching = false; // hides search results
            this.isLoadingSearchResults = true;
            this.closeRightPane();

            // not found
            if(found == '') {
                // find using server
                this.isMapLoading = true;

                ajaxGetJson('/get/location', {'venue_id': searchItem.id})
                    .success(function(data) {
                        // change map center
                        me.tempCenter = me.map.getCenter();

                        // set new center for search place marker
                        me.map.setCenter(new google.maps.LatLng(
                            data.response.venue.location.lat,
                            data.response.venue.location.lng
                        ));

                        // create marker
                        me._loadSingleLocation(data.response, true);

                        me.isMapLoading = false;

                    });
            }

        },

        /**
         * Cancel Search (normally trigger by ESC button)
         */
        cancelSearch: function() {
            // reset
            this.searchInput = ''; // empty search query
            this.isSearching = false;
            this.isLoadingSearchResults = true;
            this.searchResults = [];
            this._showAllMarkers(); // show all hidden markers from search

            if(this.tempCenter != null)
                this.map.setCenter(this.tempCenter); // set back original

            if(this.searchMarker != null)
                this.searchMarker.setMap(null);

        },

        /**
         * Load Multiple Locations
         *
         * @param data  Foursquare venue items
         */
        loadLocations: function(data){
            // get places
            var me = this;
            this.closeRightPane();

            $.each(data.response.groups[0].items, function(index, item) {
                // get categories
               me._loadSingleLocation(item, false);
            });

            me.isMapLoading = false;
        },


        /**
         *
         * Load a Single Location
         *
         * @param item  Foursquare venue item
         * @param searchMarker  true|false  Identify this location is a Searched location
         * @private
         */
        _loadSingleLocation: function(item, searchMarker) {
            var me = this;
            var categories = '';
            $.each(item.venue.categories, function(index, cat) {
                categories += cat.name + ((item.venue.categories.length < (index+1)) ? ', ' : '');
            });

            // create a marker on map
            me.createMarker(
                me.map,
                {lat: item.venue.location.lat, lng: item.venue.location.lng},
                item.venue.name,
                new google.maps.InfoWindow({
                    content: '<b>'+ item.venue.name +'</b><br><small>'+ categories +'</small>'
                }),
                item,
                searchMarker
            );
        },

        /**
         * Change Map's Center point
         *
         * @param geometryLoc   Google LatLng obj
         */
        changeMapCenter: function(geometryLoc)
        {
            var me =  this;

            this.map.setCenter(geometryLoc);
            this.isMapLoading = true;
            me._clearMap();

            ajaxGetJson('/get/loc', {lat: geometryLoc.lat, lng:geometryLoc.lng})
                .success(function(data) {
                    me.loadLocations(data);
                    me._drawRadiusCircle({lat: parseFloat(geometryLoc.lat()), lng: parseFloat(geometryLoc.lng())});
                    me.isMapLoading = false;
                })
                .error(function() {
                    infoPopUp.show('error', 'Unable to load places for this area');
                    me.getNearby(); // switch back to default nearby
                    me.isMapLoading = false;
                });

        },

        /**
         *
         * Create a Marker on Google maps
         * Then push it to markersArray[]
         *
         * @param map   current map
         * @param place lat lng object used for pin on map
         * @param title title of place
         * @param infowindow    google.Infowindow
         * @param data  Foursquare venue item
         * @param searchMarker  true|false Identify whether this place is a searched place
         */
        createMarker: function(map, place, title, infowindow, data, searchMarker) {

            var marker = new google.maps.Marker({
                map : map,
                position : place,
                title: title
            });

            marker.data = data;

            marker.addListener('click', function() {
                infowindow.open(map, marker);
                this.openRightPane(marker.data); // load it with fsq data
            }.bind(this));

            marker.addListener('mouseover', function() {
                infowindow.open(map, marker);
            });

            marker.addListener('mouseout', function() {
                infowindow.close();
            });

            // so we can keep track of on map markers
            if(!searchMarker) {
                this.markersArray.push(marker);
            } else {
                // since we need to delete it later on user cancel search, so search markers need
                // to be seperated from main markers array
                this.searchMarker = marker;
            }
        },

        /**
         * Opens up Right Panel (Important Func)
         *
         * @param data  foursquare results' item data object
         */
        openRightPane: function(data)
        {


            var me = this;
            var $rightPane = $('#rightPane');
            if(!this.isRightPaneOpen) $rightPane.animate({'right': '0'}, 300);

            this.isRightPaneOpen = true;

            // make sure each time set all loading to true first
            me.activePanel.fsq.isLoading = true;
            me.activePanel.g.isLoading = true;
            me.activePanel.fb.isLoading = true;

            // convert categories into string
            var categories = '';
            $.each(data.venue.categories, function(index, cat) {
                categories += cat.name + ((data.venue.categories.length < (index+1)) ? ', ' : '');
            });

            var address = data.venue.location.formattedAddress.join(", ");
            // set data to active panel(rightpane)
            this.activePanel.primary = {
                title : data.venue.name,
                categories: categories,
                address: address,
                addressLink: 'https://google.com/maps/search/' + encodeURIComponent(address)
            };

            // -------------------FOURSQUARE ----------------
            var fsq_rating =  Math.round((data.venue.rating / 10) * 5);
            var price = '';
            if(typeof data.venue.price !== 'undefined') {

                for(var i = 0; i < data.venue.price.tier; i++) {
                    price += '<i class="ui dollar small icon"></i>';
                }
                price += '&nbsp;' + data.venue.price.message;

            } else {
                price = 'No price available';
            }


            this.activePanel.fsq = {
                isLoading: false,
                link: this.fsq_domain + data.venue.id,
                ratings: fsq_rating,
                no_of_ratings: data.venue.ratingSignals,
                price: price,
                tips: data.tips,
                upVotes: 0,
                downVotes: 0,
                userUpVoted: false,
                userDownVoted: false
            };
            
            $('#foursquare_col .data_ratings .rating').rating('set rating', fsq_rating);

            // sync with server, cache the data
            ajaxPostJson('/sync', {fsq: data})
                .success(function(syncData) {

                    // set Foursqaure(place) sync results to local
                    me.activePanel.fsq.id = syncData.place_id;
                    me.activePanel.fsq.upVotes = syncData.upVotes;
                    me.activePanel.fsq.downVotes = syncData.downVotes;
                    me.activePanel.fsq.userUpVoted = syncData.userUpVoted;
                    me.activePanel.fsq.userDownVoted = syncData.userDownVoted;
                    

                    var query;

                    //----------------------- GOOGLE -----------------------------

                    // Example: LFC ss15/4c review
                    query = data.venue.name + ' ' + data.venue.location.formattedAddress[0] + ' review';

                    if(syncData.google != null) {
                        // display
                        me._loadGoogleData(syncData.google, query);

                    } else {
                        // get google data
                        ajaxGetJson('/get/google', {place_id: syncData.place_id, query : query})
                            .success(function(data) {
                                me._loadGoogleData(data, query);
                            })
                            .error(function() {
                                infoPopUp.show('error', 'Unable to obtain Google data, try again later');
                            })
                    }

                    // ----------------------- FACEBOOK -------------------------------

                    // sometimes foursquare formatted address only supply one address
                    var getAddress;
                    if(typeof data.venue.location.formattedAddress[1] == 'undefined') {
                        getAddress = data.venue.location.formattedAddress[0];
                    } else {
                        getAddress =  data.venue.location.formattedAddress[1];
                    }

                    // generate query
                    query = data.venue.name + ' ' + getAddress.replace(/[0-9]/g, '');

                    if(syncData.facebook != null) {

                        me._loadFacebookData(syncData.facebook);
                        me.calculate_average_ratings(fsq_rating,  syncData.facebook[0].ratings);

                    } else {
                        // get facbeook data
                        ajaxGetJson('/get/facebook', {place_id: syncData.place_id, query : query})
                            .success(function(data) {

                                me._loadFacebookData(data);
                                me.calculate_average_ratings(fsq_rating, data[0].ratings);

                            })
                            .error(function() {
                                infoPopUp.show('error', 'Unable to obtain Facebook data, try again later')
                            });
                    }
                    
                    

                }).error(function() {
                    infoPopUp.show('error', 'Something went wrong while syncing data to server');
                });

        },

        calculate_average_ratings: function(fsq_ratings, fb_ratings)
        {
            if(typeof fb_ratings == 'undefined') fb_ratings = 0;

            var result = fsq_ratings;
            if(fb_ratings != 0) {
                result =  Math.round(((fsq_ratings + fb_ratings) / 10) * 5);
            }

            $('.avg_ratings .rating').rating('set rating', result);

        },

        /**
         * Close Right Panel
         */
        closeRightPane: function()
        {
            if(this.isRightPaneOpen) $('#rightPane').animate({'right': '-400px'}, 200);
            this.isRightPaneOpen = false;
        },

        /**
         * Up Vote and Down Vote
         *
         * @param type  type of object, foursquare/google/facebook
         * @param id    id of upvoted/down voted object
         * @param vote_type 1 for upvote, 0 for downvote
         * @param $index    Vue.js object array index
         */
        vote: function(type, id, vote_type, $index)
        {
            var me = this;

            if(is_user_guest) {
                infoPopUp.show('error', 'Please <a href="'+ site.url +'/login" style="color:#fff;text-decoration: underline">log in</a> to vote')
                return;
            }

            ajaxPostJson('/vote', {type: type, id: id, vote_type: vote_type})
                .success(function(data) {
                    if(data.success) {
                    }
                })
                .error(function() {
                    infoPopUp.show('error', 'Please <a href="'+ site.url +'/login" style="color:#fff;text-decoration: underline">log in</a> to vote')
                });


            var current_set;
            var selected;

            // set current set of data to appropiate data structures, based on object type(type)
            if(type == 'google') {
                current_set = me.activePanel.g.results;
                selected = current_set[$index];
            } else if(type == 'facebook') {
                current_set = me.activePanel.fb.data;
                selected = current_set[$index];
            } else if(type == 'foursquare') {
                current_set = me.activePanel.fsq; // foursquare one data only so no index
                selected = current_set;
            }

            selected.justVoted = true;

            if (vote_type == 1) { // UP VOTE

                if(!selected.userUpVoted) {
                    selected.upVotes++;
                    selected.userUpVoted = true;
                }

                // since user can either upvote or downvote only(one)
                // so if user upvote, and if user before had downvoted, remove their downvote
                if(selected.userDownVoted) {
                    selected.downVotes--;
                    selected.userDownVoted = false;
                }


            } else if(vote_type == 0){ // DOWN VOTE

                if(!selected.userDownVoted) {
                    selected.downVotes++;
                    selected.userDownVoted = true;
                }

                if (selected.userUpVoted) {
                    selected.upVotes--;
                    selected.userUpVoted = false;
                }
            } else {

            }

            window.setTimeout(function(){
                $('.justVoted').animate({ backgroundColor: "rgba(245, 246, 206, 0)" }, 1000);
                selected.justVoted = false;
            }, 2000);

        },

        /**
         * Filter Map markers by selected Category in Category Dropdown
         *
         * @param rawCategory   Selected category's value foursquare_category_id|category_name
         */
        filterByCategory: function(rawCategory) {
            var category = rawCategory.split("|")[0]; // split up to obtain foursquare_category_id

            if(category == 'all') {
                // show all markers
               $.each(this.markersArray, function(i, m) {
                   m.setVisible(true);
               });
            } else {
                var found = false;
                $.each(this.markersArray, function(i, m) {
                    found = false;

                    $.each(m.data.venue.categories, function(i, cat) {
                        if(cat.name == category) {
                            m.setVisible(true);
                            found = true;
                        }
                    });

                    if(!found) m.setVisible(false);

                });
            }

        },


        /**
         * Loads Google's Data into Vue.js activePanel.g obj
         *
         * @param data  Google search results object array
         * @param query
         * @private
         */
        _loadGoogleData: function(data, query)
        {
            this.activePanel.g = {
                link: 'https://www.google.com/search?q=' + encodeURIComponent(query),
                isLoading: false,
                results : data
            };
        },

        _loadFacebookData: function(data)
        {
            var me = this;

            this.activePanel.fb = {
                isLoading: false,
                data: data
            };

            window.setTimeout(function() {
                $.each(me.activePanel.fb.data, function(i, item) {
                    $('#facebook_col .list .item:nth-child('+ (i + 1) +') .data_ratings .rating').rating('set rating', Math.round(item.ratings));
                });
            }, 100);


        },

        /**
         * Clear Map
         * Removes all markers from map
         *
         * @private
         */
        _clearMap: function()
        {
            if (this.markersArray) {
                for (i in this.markersArray) {
                    this.markersArray[i].setMap(null);
                }
                this.markersArray.length = 0;
            }

            if (this.circlesArray) {
                for (i in this.circlesArray) {
                    this.circlesArray[i].setMap(null);
                }
                this.circlesArray.length = 0;
            }
        },

        /**
         * Unhide all Hidden markers on map
         * Normally used when cancel search
         *
         * @private
         */
        _showAllMarkers: function()
        {
            for (i in this.markersArray) {
                this.markersArray[i].setVisible(true);
            }  
        },

        /**
         * Using Google MAPs Circle API to draw circles to
         * highlight area of interest
         *
         * @param center
         * @private
         */
        _drawRadiusCircle: function(center)
        {
            var circle = new google.maps.Circle({
                strokeColor: '#CDD15B',
                strokeOpacity: 0.5,
                strokeWeight: 2,
                fillColor: '#F9FACF',
                fillOpacity: 0.45,
                map: this.map,
                center: center,
                radius: 1000
            });

            this.circlesArray.push(circle);
        }


    }
});

$(document).ready(function(e) {

    var geocoder = new google.maps.Geocoder();

    // when neighbourhood dropdown change
    // create geocode, tell main program to change the map center
    $('#hood_dropdown').on('change', function() {

        // Define address to ce
        geocoder.geocode({
            'address':  $(this).val()
        }, function (results, status) {

            if (status == google.maps.GeocoderStatus.OK) {
                main.changeMapCenter(results[0].geometry.location);
            } else {
                console.log('Failed to Geo code:' + $(this).val());
            }
        });
    });

    // when category drodown change
    // tell mainprogram to filter by category by category value
    $('#category_dropdown').on('change', function() {
        main.filterByCategory($(this).val());
    });

});

// cancel search when click escape anywhere in document
$(document).keyup(function(e) {
    if (e.keyCode == 27) {
       main.cancelSearch();
    }
});
