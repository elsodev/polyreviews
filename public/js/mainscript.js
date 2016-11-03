var main = new Vue({
    el: '#main',
    data: {
        fsq_domain: 'https://foursquare.com/v/',
        isRightPaneOpen: false,
        isMapLoading: true,
        searchInput : '',
        map : null,

        activePanel: {
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
    
    ready:function() {
        // loads google mapa
        this.map = this.initMap();
        this.getNearby();
    },
    
    methods: {

        getNearby: function()
        {
            var me = this;

            ajaxGetJson('/getStartingPins').success(function(data) {

                // get places
                $.each(data.response.groups[0].items, function(index, item) {

                    // get categories
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
                        item
                    );
                });

                me.isMapLoading = false;
            }).error(function() {
                me.isMapLoading = false;
            });
        },

        createMarker: function(map, place, title, infowindow, data) {

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

            // set data to active panel(rightpane)
            this.activePanel.primary = {
                title : data.venue.name,
                categories: categories,
                address: data.venue.location.formattedAddress.join(", "),
            };

            // -------------------FOURSQUARE ----------------
            var fsq_rating =  Math.round((data.venue.rating / 10) * 5)
            var price = '';
            for(var i = 0; i < data.venue.price.tier; i++) {
                price += '<i class="ui dollar small icon"></i>';
            }
            price += '&nbsp;' + data.venue.price.message;

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

                    // place id for foursquare data
                    me.activePanel.fsq.id = syncData.place_id;

                    var query;

                    //----------------------- GOOGLE -----------------------------

                    // LFC ss15/4c review
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
                    query = data.venue.name + ' ' + data.venue.location.formattedAddress[1].replace(/[0-9]/g, '');

                    if(syncData.facebook != null) {

                        me.activePanel.fb = {
                            isLoading: false,
                            data: syncData.facebook
                        };


                    } else {
                        // get facbeook data
                        ajaxGetJson('/get/facebook', {place_id: syncData.place_id, query : query})
                            .success(function(data) {
                                
                                me.activePanel.fb = {
                                    isLoading: false,
                                    data: data
                                };

                            })
                            .error(function() {
                                infoPopUp.show('error', 'Unable to obtain Facebook data, try again later')
                            });
                    }
                    
                    

                }).error(function() {
                    infoPopUp.show('error', 'Something went wrong while syncing data to server');
                });

        },

        closeRightPane: function()
        {
            if(this.isRightPaneOpen) $('#rightPane').animate({'right': '-400px'}, 200);
            this.isRightPaneOpen = false;
        },

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
                zoom: 17,
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


        _loadGoogleData: function(data, query)
        {
            this.activePanel.g = {
                link: 'https://www.google.com/search?q=' + encodeURIComponent(query),
                isLoading: false,
                results : data
            };
        },

        /**
         * Up Vote and Down Vote
         *
         * @param type
         * @param id
         * @param vote_type
         * @param $index
         */
        vote: function(type, id, vote_type, $index)
        {
            var me = this;

            ajaxPostJson('/vote', {type: type, id: id, vote_type: vote_type})
                .success(function(data) {

                    if(data.success) {
                        
                        var current_set;
                        var selected;

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


                        if (vote_type == 1) { // UP VOTE

                                selected.upVotes++;
                                selected.userUpVoted = true;

                                console.log(selected.userUpVoted);

                                // since user can either upvote or downvote only(one)
                                // so if user upvote, and if user before had downvoted, remove their downvote
                                if(selected.userDownVoted) {
                                    selected.downVotes--;
                                    selected.userDownVoted = false;
                                }
                            

                        } else if(vote_type == 0){ // DOWN VOTE

                                selected.downVotes++;
                                selected.userDownVoted = true;

                                if (selected.userUpVoted) {
                                    selected.upVotes--;
                                    selected.userUpVoted = false;
                                }
                        } else {

                        }

                    }

                })
                .error(function() {
                     infoPopUp.show('error', 'Please <a href="'+ site.url +'/login">log in</a> to vote')
                });
        }

    }
});