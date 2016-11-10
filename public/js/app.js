var customLayers = [];
var customSources = [];

function clearMap(map) {
    $.each(customLayers, function (index, value) {
        map.removeLayer(value);
    });
    $.each(customSources, function (index, value) {
        map.removeSource(value);
    });

    customLayers = [];
    customSources = [];
}

$(document).ready(function () {
    map.on('load', function () {

    });

    $('.ajax-call').on('click', function () {
        clearMap(map);
        $.ajax({
            type: "GET",
            url: "http://localhost:8080/wtf",
            dataType: "jsonp",

            statusCode: {
                200: function (response) {
                    var responseJson= response.responseText;
                    var obj = jQuery.parseJSON(responseJson);

                    map.addSource("supermarkets", {
                        "type": "geojson",
                        "data": {
                            "type": "FeatureCollection",
                            "features": obj
                        }
                    });

                    map.addLayer({
                        "id": "supermarkets",
                        "type": "symbol",
                        "source": "supermarkets",
                        "layout": {
                            "icon-image": "{icon}-11",
                            "text-field": "{title}",
                            "text-font": ["Open Sans Semibold", "Arial Unicode MS Bold"],
                            "text-offset": [0, 0.6],
                            "text-anchor": "top"
                        }
                    });

                    customLayers[customLayers.length]='supermarkets';
                    customSources[customSources.length]='supermarkets';
                }
            }
        });
    })
});