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

            $.ajax({
               url: site.url + '/getStartingPins', dataType: 'json', type: 'get'
            }).success(function(data) {
                $.each(data.response.groups[0].items, function(index, item) {
                    me.createMarker(me.map, {lat: item.venue.location.lat, lng: item.venue.location.lng}, item.venue.name)
                });
            }).error(function() {

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
            var maps_center = {lat: locations.default_center.lat, lng: locations.default_center.lng};
            return new google.maps.Map(document.getElementById('map'), {
                zoom: 16,
                center: maps_center
            });
        }
    }
});
