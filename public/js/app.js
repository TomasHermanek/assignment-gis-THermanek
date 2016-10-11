$(document).ready(function () {
    map.on('load', function () {

        map.addSource("points", {
            "type": "geojson",
            "data": {
                "type": "FeatureCollection",
                "features": [
                    {
                        "type": "Feature",
                        "properties": {},
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                17.10468292236328,
                                48.158814559520785
                            ]
                        }
                    },
                    {
                        "type": "Feature",
                        "properties": {},
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                17.12571144104004,
                                48.154004920838986
                            ]
                        }
                    },
                    {
                        "type": "Feature",
                        "properties": {},
                        "geometry": {
                            "type": "Point",
                            "coordinates": [
                                17.103652954101562,
                                48.14444156183988
                            ]
                        }
                    }
                ]
            }
        });

        map.addLayer({
            "id": "points",
            "type": "symbol",
            "source": "points",
            "layout": {
                "icon-image": "{icon}-15",
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
                        "data": obj
                    });
                }
            }
        });
    });
});