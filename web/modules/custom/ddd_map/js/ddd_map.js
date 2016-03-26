(function ($, Drupal) {

    "use strict";

    Drupal.behaviors.ddd_map = {
        attach: function (context) {
            var mymap = L.map('mapid').setView([45.5173272, 9.2134192], 17);

            L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
                attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
                maxZoom: 18,
                id: 'mapbox.streets',
                accessToken: 'pk.eyJ1IjoibHVzc29sdWNhIiwiYSI6ImNpbTk5N3JnZzAwMHJ3Zm02NnM0a3V4NjkifQ.jWHAimsYEERbCYlQzSb_IA'
            }).addTo(mymap);

            var marker = L.marker([45.5173272, 9.2134192]).addTo(mymap);
            marker.bindPopup("<b>Bicocca University</b><br>Building U7").openPopup();
        }
    };

}(jQuery, Drupal));
