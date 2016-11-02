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
                tips: data.tips
            };
            
            $('#foursquare_col .data_ratings .rating').rating('set rating', fsq_rating);
            
            // sync with server, cache the data
            ajaxPostJson('/sync', {fsq: data})
                .success(function(syncData) {
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
        
        
        voteUp: function(type, index)
        {
            
        },
        
        voteDown: function(type, index)
        {
            
        }
    }
});