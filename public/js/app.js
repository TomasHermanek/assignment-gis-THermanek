var customLayers = [];
var customSources = [];
var pointerSource;

var clickedLat = 16.995689264159978;
var clickedLng = 49.00250129786536;

var centerLat = 19.136260;
var centerLng = 48.728650;

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

function showPopulationLegend() {
    $('.population-legend').css('display', 'inline');
}

function hidePopulationLegend() {
    $('.population-legend').css('display', 'none');
}

function addMarker(map, lat, lng) {
    var marker = {
        "type": "Feature",
        "geometry": {
            "type": "Point",
            "coordinates": [lng, lat]
        },
        "properties": {
            'marker-color': '#3ca0d3',
            'marker-size': 'large',
            'marker-symbol': 'rocket'
        }
    };

    if (typeof  lat !== 'undefined' && typeof lng !== 'undefined') {
        if (typeof pointerSource !== 'undefined') {
            map.removeSource(pointerSource);
            map.removeLayer(pointerSource);
            pointerSource = null;
        }

        map.addSource('point', {
            "type": "geojson",
            "data": marker
        });

        map.addLayer({
            "id": "point",
            "type": "circle",
            "source": "point"
        });

        pointerSource = 'point';
    }
}

function addPolygon(map, sourceName, object, colour) {
    map.addSource(sourceName, {
        "type": "geojson",
        "data": {
            "type": "FeatureCollection",
            "features": object
        }
    });

    map.addLayer({
        "id": sourceName,
        'type': 'fill',
        'source': sourceName,
        'paint': {
            'fill-color': 'rgba('+colour+', 0.4)',
            'fill-outline-color': 'rgba('+colour+', 1)'
        }
    });

    customLayers[customLayers.length]=sourceName;
    customSources[customSources.length]=sourceName;
}

function makeLine(startLat, startLng, endLat, endLng) {
    line = {
        "type": "Feature",
        "geometry": {
            "type": "LineString",
            "coordinates": [
                [startLat, startLng],
                [endLat, endLng]
            ]
        }
    };
    return line;
}

function makePath(startLat, startLng, points) {
    var routeFeatures = [];

    $.each(points, function (index, value) {
        if (index == 0) {
            line = makeLine(startLng, startLat, value['geometry']['coordinates'][0], value['geometry']['coordinates'][1])
        }
        else {
            line = makeLine(points[index-1]['geometry']['coordinates'][0], points[index-1]['geometry']['coordinates'][1],
                value['geometry']['coordinates'][0], value['geometry']['coordinates'][1])
        }

        routeFeatures.push(line);
    });

    var routeSource = {
        "type": "FeatureCollection",
        "features": routeFeatures
    };

    map.addSource('route', {
        "type": "geojson",
        "data": routeSource
    });

    map.addLayer({
        "id": "route",
        "source": "route",
        "type": "line",
        "paint": {
            "line-width": 2,
            "line-color": "#007cbf"
        }
    });

    customLayers[customLayers.length]='route';
    customSources[customSources.length]='route';
}

$(document).ready(function () {
    map.on('load', function () {

    });

    map.on('click', function (e) {
        clickedLat = e.lngLat['lat'];
        clickedLng = e.lngLat['lng'];

        addMarker(this, clickedLat, clickedLng);
    });

    map.on('click', function (e) {
        var features = map.queryRenderedFeatures(e.point, { layers: ['extraLow','low', 'medium', 'high', 'extraHigh'] });
        if (!features.length) {
            return;
        }

        var feature = features[0];

        var popup = new mapboxgl.Popup()
            .setLngLat(map.unproject(e.point))
            .setHTML(feature.properties.name)
            .addTo(map);
    });

    $('.bar-path').on('click', function () {
        clearMap(map);
        hidePopulationLegend();

        $.ajax({
            type: "GET",
            url: "http://localhost:8080/bar-route",
            data: {
                lat: clickedLat,
                lng: clickedLng
            },
            dataType: "jsonp",

            statusCode: {
                200: function (response) {
                    var responseJson= response.responseText;
                    var obj = jQuery.parseJSON(responseJson);

                    map.flyTo({
                        center: [obj[0]['geometry']['coordinates'][0], obj[0]['geometry']['coordinates'][1]],
                        zoom: 14,
                        speed: 0.5,
                        curve: 1
                    });

                    map.addSource("tour", {
                        "type": "geojson",
                        "data": {
                            "type": "FeatureCollection",
                            "features": obj
                        }
                    });

                    map.addLayer({
                        "id": "tour",
                        "type": "symbol",
                        "source": "tour",
                        "layout": {
                            "icon-image": "{icon}-11",
                            "text-field": "{title}",
                            "text-font": ["Open Sans Semibold", "Arial Unicode MS Bold"],
                            "text-offset": [0, 0.6],
                            "text-anchor": "top"
                        }
                    });

                    customLayers[customLayers.length]='tour';
                    customSources[customSources.length]='tour';

                    makePath(clickedLat, clickedLng, obj);
                }
            }
        });
    });

    $('.bar-parking').on('click', function () {
        clearMap(map);
        hidePopulationLegend();

        var barName = $('#bar-parking-search').val();

        $.ajax({
            type: "POST",
            url: "http://localhost:8080/bar-parking",
            data: {
                lat: clickedLat,
                lng: clickedLng,
                barName: barName
            },
            dataType: "jsonp",

            statusCode: {
                200: function (response) {
                    var responseJson= response.responseText;
                    var obj = jQuery.parseJSON(responseJson);

                    map.flyTo({
                        center: [obj[0]['geometry']['coordinates'][0], obj[0]['geometry']['coordinates'][1]],
                        zoom: 16,
                        speed: 0.5,
                        curve: 1
                    });

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
    });
    $('.bar-population').on('click', function () {
        clearMap(map);
        showPopulationLegend();

        $.ajax({
            type: "GET",
            url: "http://localhost:8080/bar-population",
            data: {
                lat: clickedLat,
                lng: clickedLng
            },
            dataType: "jsonp",

            statusCode: {
                200: function (response) {
                    var responseJson= response.responseText;
                    var obj = jQuery.parseJSON(responseJson);

                    map.flyTo({
                        center: [centerLat, centerLng],
                        zoom: 8,
                        speed: 0.5,
                        curve: 1
                    });

                    addPolygon(map, 'extraLow', obj['extraLow'], '168, 249, 167');
                    addPolygon(map, 'low', obj['low'], '8, 96, 6');
                    addPolygon(map, 'medium', obj['medium'], '230, 232, 109');
                    addPolygon(map, 'high', obj['high'], '234, 151, 42');
                    addPolygon(map, 'extraHigh', obj['extraHigh'], '234, 64, 42');
                }
            }
        });
    })
});