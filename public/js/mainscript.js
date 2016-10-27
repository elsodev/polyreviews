
var main = new Vue({
    el: '#main',
    data: {
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
        // loads google map
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

        openRightPane: function(data)
        {
            var $rightPane = $('#rightPane');
            if(!this.isRightPaneOpen) $rightPane.animate({'right': '0'}, 300);

            this.isRightPaneOpen = true;

            // convert categories into string
            var categories = '';
            $.each(data.venue.categories, function(index, cat) {
                categories += cat.name + ((data.venue.categories.length < (index+1)) ? ', ' : '');
            });

            // set data to active panel(rightpane)
            this.activePanel.primary = {
                title : data.venue.name,
                categories: categories,
                address: data.venue.location.formattedAddress.join(", ")
            };

            // sync with server, cache the data
            ajaxPostJson('/sync', {fsq: data})
                .success(function() {

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

    }
});