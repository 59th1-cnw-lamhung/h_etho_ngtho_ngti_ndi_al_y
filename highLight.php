<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>OpenStreetMap &amp; OpenLayers - Marker Example</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

        <link rel="stylesheet" href="https://openlayers.org/en/v4.6.5/css/ol.css" type="text/css" />
        <script src="https://openlayers.org/en/v4.6.5/build/ol.js" type="text/javascript"></script>
        <script src="https://code.jquery.com/jquery-1.12.3.min.js"></script>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js" type="text/javascript"></script>

    <body onload="initialize_map();">
        <table>
            <tr>
                <td>
                    <div id="map" style="width: 80vw; height: 100vh;"></div>
                </td>
                <td>
                    <button>Button</button>
                </td>
            </tr>
        </table>
        <!--<?php include 'eagle_pgsqlAPI.php' ?>-->
        <?php
            //$myPDO = initDB();
            //$mySRID = '4326';
            //$pointFormat = 'POINT(12,5)';

            //example1($myPDO);
            //example2($myPDO);
            //example3($myPDO,'4326','POINT(12,5)');
            //$result = getResult($myPDO,$mySRID,$pointFormat);

            //closeDB($myPDO);
        ?>
        <script>
            var format = 'image/png';
            var map;
            /*var minX = -105.231735229492;
            var minY = 39.8262786865234;
            var maxX = -104.006164550781;
            var maxY = 40.6606330871582;*/

            var minX = 7.29330825805664;
            var minY = 46.9186744689941;
            var maxX = 7.49656248092651;
            var maxY = 46.9905090332031;

            var cenX = (minX + maxX) / 2;
            var cenY = (minY + maxY) / 2;
            var mapLat = cenY;
            var mapLng = cenX;
            var mapDefaultZoom = 9;

            function initialize_map() {
                //*
                layerBG = new ol.layer.Tile({source: new ol.source.OSM({})});
                //*/
                var layerEagle = new ol.layer.Image({
                    source: new ol.source.ImageWMS({
                    ratio: 1,
                    url: 'http://localhost:8082/geoserver/ggmap_v1/wms?',
                    params: {
                    'FORMAT': format,
                    'VERSION': '1.1.1',
                    STYLES: '',
                    LAYERS: 'ggmap_v1:cmr_adm1',
                });
                var viewMap = new ol.View({
                    center: ol.proj.fromLonLat([mapLng, mapLat]),
                    zoom: mapDefaultZoom
                    //projection: projection
                });
                map = new ol.Map({
                    target: "map",
                    layers: [layerBG, layerEagle],
                    //layers: [layerDuongDiaGioi],
                    view: viewMap
                });
                //map.getView().fit(bounds, map.getSize());
                
                
                var styleFunction = function (feature) {
                    return styles[feature.getGeometry().getType()];
                };
                var stylePoint = new ol.style.Style({
                    image: new ol.style.Icon({
                        anchor: [0.5, 0.5],
                        anchorXUnits: "fraction",
                        anchorYUnits: "fraction",
                        src: "http://localhost:8080/ggmap_v1/Yellow_dot.svg"
                    })
                });
                var vectorLayerPoint = new ol.layer.Vector({
                    //source: vectorSource,
                    //style: styleFunction
                    style: stylePoint
                });
                map.addLayer(vectorLayerPoint);

                function createJsonObj(result) {                    
                    var geojsonObject = '{'
                            + '"type": "FeatureCollection",'
                            + '"crs": {'
                                + '"type": "name",'
                                + '"properties": {'
                                    + '"name": "EPSG:4326"'
                                + '}'
                            + '},'
                            + '"features": [{'
                                + '"type": "Feature",'
                                + '"geometry": ' + result
                            + '}]'
                        + '}';
                    return geojsonObject;
                }
                function drawGeoJsonObj(paObjJson) {
                    var vectorSource = new ol.source.Vector({
                        features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
                            dataProjection: 'EPSG:4326',
                            featureProjection: 'EPSG:3857'
                        })
                    });
                    var vectorLayer = new ol.layer.Vector({
                        source: vectorSource
                    });
                    map.addLayer(vectorLayer);
                }
                function highLightGeoJsonObj(paObjJson) {
                    var vectorSource = new ol.source.Vector({
                        features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
                            dataProjection: 'EPSG:4326',
                            featureProjection: 'EPSG:3857'
                        })
                    });
					vectorLayerPoint.setSource(vectorSource);
                    /*
                    var vectorLayerPoint = new ol.layer.Vector({
                        source: vectorSource
                    });
                    map.addLayer(vectorLayerPoint);
                    */
                }
                function highLightObj(result) {
                    //alert("result: " + result);
                    var strObjJson = createJsonObj(result);
                    //alert("Full Json: " + strObjJson);
                    var objJson = JSON.parse(strObjJson);
                    //alert("Full Json: " + JSON.stringify(objJson));
                    //drawGeoJsonObj(objJson);
                    highLightGeoJsonObj(objJson);
                }
                map.on('singleclick', function (evt) {
                    //alert("coordinate: " + evt.coordinate);
                    //var myPoint = 'POINT(12,5)';
                    var lonlat = ol.proj.transform(evt.coordinate, 'EPSG:3857', 'EPSG:4326');
                    var lon = lonlat[0];
                    var lat = lonlat[1];
                    var myPoint = 'POINT(' + lon + ' ' + lat + ')';
                    //alert("myPoint: " + myPoint);
                    $.ajax({
                        type: "POST",
                        url: "eagle_pgsqlAPI.php",
                        //dataType: 'json',
                        data: {functionname: 'getGeoEagleToAjax', paPoint: myPoint},
                        success : function (result, status, erro) {
                            highLightObj(result);
                        },
                        error: function (req, status, error) {
                            alert(req + " " + status + " " + error);
                        }
                    });
                });
            };
        </script>
    </body>
</html>