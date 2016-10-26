/**
 * Shows Pop Up
 * @type {{$el: (*|jQuery|HTMLElement), defaultSpeed: number, waitingTime: number, show: infoPopUp.show, hide: infoPopUp.hide}}
 */
var infoPopUp = {
    $el : $('.info'),
    defaultSpeed: 300,
    waitingTime: 5000,
    show: function(type, msg) {
        if(type == 'success') {
            this.$el.removeClass('error').addClass('success');
        } else if(type == 'error') {
            this.$el.removeClass('success').addClass('error');
        } else {
            return;
        }

        this.$el.html(msg);
        this.$el.animate({bottom: '50px'}, this.defaultSpeed);

        window.setTimeout(function() {
           this.hide()
        }.bind(this), this.waitingTime);
    },
    hide: function()
    {
        this.$el.animate({bottom: '-50px'}, this.defaultSpeed);
    }
};



var main = new Vue({
    el: '#main',
    data: {
        isMapLoading: true,
        searchInput : '',
        map : null
    },
    
    ready:function() {
        // loads google map
        this.map = this.initMap();
        this.getNearby(map);

        infoPopUp.show('a', 'test');
    },
    
    methods: {

        getNearby: function()
        {
            var me = this;

            $.ajax({
               url: site.url + '/getStartingPins', dataType: 'json', type: 'get'
            }).success(function(data) {
                $.each(data.response.groups[0].items, function(index, item) {
                    me.createMarker(me.map, {lat: item.venue.location.lat, lng: item.venue.location.lng}, item.venue.name)
                });

                me.isMapLoading = false;
            }).error(function() {
                me.isMapLoading = false;
            });
        },

        createMarker: function(map, place, title, infoWindow) {

                var marker = new google.maps.Marker({
                    map : map,
                    position : place,
                    title: title
                });

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
        }
    }
});