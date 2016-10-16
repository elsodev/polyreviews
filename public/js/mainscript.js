var main = new Vue({
    el: '#main',
    data: {
        searchInput : '',
        map : null
    },
    
    ready:function() {
        // loads google map
        this.map = this.initMap();
        this.getNearby(map);
    },
    
    methods: {

        getNearby: function()
        {
            var me = this;
            var service = new google.maps.places.PlacesService(this.map);

            var types = [ 'cafe', 'restaurant', 'bar', 'meal_takeaway', 'meal_delivery' , 'bakery', 'food'];
                $.each(types, function(index, value) {
                    service.nearbySearch({
                        location : {lat: locations.default_center.lat, lng: locations.default_center.lng},
                        radius : 2000,
                        type : [value]
                    }, me.nearByCallback);
                });
        },

        nearByCallback: function(results, status) {
            var infoWindow = new google.maps.InfoWindow();
            var me = this;
            if (status == google.maps.places.PlacesServiceStatus.OK) {
                for (var i = 0; i < results.length; i++) {

                    var place = results[i];
                    console.log(place);
                    me.createMarker(results[i], me.map, infoWindow);
                }
            } else {
                console.log('Error getting nearby:' + status);
            }
        },

        createMarker: function(place, map, infoWindow) {

                var placeLoc = place.geometry.location;
                var marker = new google.maps.Marker({
                    map : map,
                    position : place.geometry.location
                });

                google.maps.event.addListener(marker, 'click', function() {
                    infoWindow.setContent(place.name);
                    infoWindow.open(map, this);
                });
        },

        initMap : function() {
            var maps_center = {lat: locations.default_center.lat, lng: locations.default_center.lng};
            return new google.maps.Map(document.getElementById('map'), {
                zoom: 16,
                center: maps_center
            });
        }
    }
});
