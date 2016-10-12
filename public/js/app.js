$(document).ready(function () {
    map.on('load', function () {

        map.addSource("points", {
            "type": "geojson",
            "data": {
                "type": "FeatureCollection",
                "features": [{
                    "geometry": {
                        "type": "Point",
                        "coordinates": [17.077699, 48.1400]
                    },
                    "properties": {
                        "title": "Mapbox DC",
                        "icon": "anchor"
                    }
                }, {
                    "geometry": {
                        "type": "Point",
                        "coordinates": [17.077615, 48.145361]
                    },
                    "properties": {
                        "title": "Mapbox SF",
                        "icon": "anchor"
                    }
                }]
            }
        });

        map.addLayer({
            "id": "points",
            "type": "symbol",
            "source": "points",
            "layout": {
                "icon-image": "{icon}-11",
                "text-field": "{title}",
                "text-font": ["Open Sans Semibold", "Arial Unicode MS Bold"],
                "text-offset": [0, 0.6],
                "text-anchor": "top"
            }
        });

        $.ajax({
            type: "GET",
            url: "http://localhost:8080/wtf",
            dataType: "jsonp",

            statusCode: {
                200: function (response) {
                    var responseJson= response.responseText;
                    console.log(responseJson);
                    var obj = jQuery.parseJSON(responseJson);
                    console.log(obj);

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
                }
            }
        });
    });
});