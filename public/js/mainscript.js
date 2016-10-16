var main = new Vue({
    el: '#main',
    data: {
        'searchInput' : ''
    },
    
    ready:function() {
        // loads google map
        console.log(locations.default_center.lat, locations.default_center.lng);
        this.initMap();
    }, 
    
    methods: {

        initMap : function() {
            var maps_center = {lat: locations.default_center.lat, lng: locations.default_center.lng};
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 16,
                center: maps_center
            });
        }
    }
});