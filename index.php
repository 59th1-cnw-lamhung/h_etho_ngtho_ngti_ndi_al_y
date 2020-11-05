<!DOCTYPE html>
<html lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>GG_MAP</title>

        <!-- Bootstrap CSS 
        

        <link rel="stylesheet" href="https://openlayers.org/en/v4.6.5/css/ol.css" type="text/css" />
        <script src="https://openlayers.org/en/v4.6.5/build/ol.js" type="text/javascript"></script>
        <script src="https://code.jquery.com/jquery-1.12.3.min.js"></script>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js" type="text/javascript"></script>

        <link href="asset/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
        -->

        <!-- Custom styles FOR this template-->

        <script src="https://openlayers.org/en/v4.6.5/build/ol.js" type="text/javascript"></script>

        <link rel="stylesheet" href="https://openlayers.org/en/v4.6.5/css/ol.css" type="text/css" />

        <link href="asset/css/sb-admin-2.min.css" rel="stylesheet" type="text/css">
        
        <link href="asset/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

        <!-- Custom styles FOR this template-->
        

        <link rel="shortcut icon" href="asset/client/user/img/mango.ico" />

        

        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js" type="text/javascript"></script>


        

        


        <style>
            .map {
                width: 100%;
                height:400px;
            }
            .ol-popup {
                position: absolute;
                background-color: white;
                -webkit-filter: drop-shadow(0 1px 4px rgba(0,0,0,0.2));
                filter: drop-shadow(0 1px 4px rgba(0,0,0,0.2));
                padding: 15px;
                border-radius: 10px;
                border: 1px solid #cccccc;
                bottom: 12px;
                left: -50px;
                min-width: 280px;
            }
            .ol-popup:after, .ol-popup:before {
                top: 100%;
                border: solid transparent;
                content: " ";
                height: 0;
                width: 0;
                position: absolute;
                pointer-events: none;
            }
            .ol-popup:after {
                border-top-color: white;
                border-width: 10px;
                left: 48px;
                margin-left: -10px;
            }
            .ol-popup:before {
                border-top-color: #cccccc;
                border-width: 11px;
                left: 48px;
                margin-left: -11px;
            }
            .ol-popup-closer {
                text-decoration: none;
                position: absolute;
                top: 2px;
                right: 8px;
            }
            .ol-popup-closer:after {
                content: "✖";
            }

            .flex {
            display: flex;
            }

            .footer.container-fluid {
                position: absolute;
                bottom: -5px;
                background-color: cornflowerblue;
                height: 39px;
                align-items: center;
                justify-content: space-around;
                width: 99%;
            }
            .footer p {
                padding-top: 16px;
                color: #FFFFFF;
                display: inline-block;
            }
        </style>

        <?php include ('getData.php'); ?>

        <script type="text/javascript">
        

        var map;
        var minX = 102.144584655762;
        var minY = 8.38135528564453;
        var maxX = 109.469177246094;
        var maxY = 23.3926944732666;
        
        var cenX = (minX + maxX) / 2;
        var cenY = (minY + maxY) / 2;
        var mapLat = cenY;
        var mapLng = cenX;
        var mapDefaultZoom = 6;

        function initialize_map(){
            layerBG = new ol.layer.Tile({source: new ol.source.OSM({})});
            
            
            var format = 'image/png';
            
            //var bounds = [8.49874877929688, 1.652547955513, 16.1921157836914, 13.0780601501465];
            var VietNamGGM = new ol.layer.Image({
            source: new ol.source.ImageWMS({
            ratio: 1,
            url: 'http://localhost:8082/geoserver/ggmap_v1/wms?',
            params: {
            'FORMAT': format,
            'VERSION': '1.1.1',
            STYLES: 'style_map',
            LAYERS: 'ggmap_v1:gadm36_vnm_2',
            }
            })
            });

            var VietNamGGM_roads = new ol.layer.Image({
            source: new ol.source.ImageWMS({
            ratio: 1,
            url: 'http://localhost:8082/geoserver/ggmap_v1/wms?',
            params: {
            'FORMAT': format,
            'VERSION': '1.1.1',
            STYLES: 'style_map',
            LAYERS: 'ggmap_v1:vnm_roads',
            }
            })
            });

            var VietNamGGM_dot = new ol.layer.Image({
            source: new ol.source.ImageWMS({
            ratio: 1,
            url: 'http://localhost:8082/geoserver/ggmap_v1/wms?',
            params: {
            'FORMAT': format,
            'VERSION': '1.1.1',
            STYLES: '',
            LAYERS: 'ggmap_v1:world_cities',
            }
            })
            });


            var VietNamGGM_river = new ol.layer.Image({
            source: new ol.source.ImageWMS({
            ratio: 1,
            url: 'http://localhost:8082/geoserver/ggmap_v1/wms?',
            params: {
            'FORMAT': format,
            'VERSION': '1.1.1',
            STYLES: '',
            LAYERS: 'ggmap_v1:vnm_water_lines_dcw',
            }
            })
            });

            var container = document.getElementById('popup');
            var content = document.getElementById('popup-content');
            var closer = document.getElementById('popup-closer');

            closer.onclick = function () {
                overlay.setPosition(undefined);
                closer.blur();
                return false;
            };


            var overlay = new ol.Overlay({
                element: container,
                autoPan: true,
                autoPanAnimation: {
                duration: 250
                }
            });

            
            var viewMap = new ol.View({
            center: ol.proj.fromLonLat([mapLng, mapLat]),
            zoom: mapDefaultZoom
            });

            var map = new ol.Map({
            target: 'map',
            layers: [layerBG, VietNamGGM, VietNamGGM_roads, VietNamGGM_dot, VietNamGGM_river],
            overlays: [overlay],
            view: viewMap
            });

            var styles = {
                'MultiPolygon': new ol.style.Style({
                    fill: new ol.style.Fill({
                        color: 'orange'
                    }),
                    stroke: new ol.style.Stroke({
                        color: 'yellow', 
                        width: 2
                    })
                }),
                'MultiLineString': new ol.style.Style({
                    stroke: new ol.style.Stroke({
                        color: 'green', 
                        width: 3
                    })
                }),
                'Point': new ol.style.Style({
                    
                    image: new ol.style.Circle({
                        radius: 7,
                        fill: new ol.style.Fill({color: 'green'}),
                        stroke: new ol.style.Stroke({
                        color: 'green', width: 2
                        })
                    })
                })
                
                
            };

            var styleFunction = function (feature) {
                return styles[feature.getGeometry().getType()];
            };

            var vectorLayer = new ol.layer.Vector({
                style: styleFunction
            });

            //map.addLayer(vectorLayer);



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
            
            function highLightGeoJsonObj(paObjJson) {

                var vectorSource = new ol.source.Vector({
                    features: (new ol.format.GeoJSON()).readFeatures(paObjJson, {
                        dataProjection: 'EPSG:4326',
                        featureProjection: 'EPSG:3857'
                    })
                });
                
                vectorLayer.setSource(vectorSource);
    
            }
            function highLightObj(result) {
                
                var strObjJson = createJsonObj(result);
               
                var objJson = JSON.parse(strObjJson);
                
                highLightGeoJsonObj(objJson);
            }

            function infoObj(result){
                $("#info").html(result);
                
            }

            function infoData(result){
                
                $("#tinh-thanhpho").html(result);
            }
            
            var CBVung = document.getElementById('cbvung');
            if (CBVung.checked==false)
            {
                VietNamGGM.setVisible(false);
                
            }
            
            var CBDuong = document.getElementById('cbduong');
            if (CBDuong.checked==false)
            {
                VietNamGGM_roads.setVisible(false);
                
            }

            var CBDiem = document.getElementById('cbdiem');
            if (CBDiem.checked==false)
            {
                VietNamGGM_dot.setVisible(false);
                
            }

            var CBSong = document.getElementById('cbsong');
            if (CBSong.checked==false)
            {
                VietNamGGM_river.setVisible(false);
                
            }

            $("#cbvung").change(function () {
                if($("#cbvung").is(":checked"))
                {
                    VietNamGGM.setVisible(true);
                    map.addLayer(vectorLayer);
                    map.on('click', function (evt) {
                        //alert("coordinate: " + evt.coordinate);
                        //var myPoint = 'POINT(12,5)';
                        var lonlat = ol.proj.transform(evt.coordinate, 'EPSG:3857', 'EPSG:4326');
                        var lon = lonlat[0];
                        var lat = lonlat[1];
                        var myPoint = 'POINT(' + lon + ' ' + lat + ')';
                        console.log(myPoint)

                        
                        
                        $.ajax({
                            type: "POST",
                            url: "pgsqlAPI.php",
                            //dataType: 'json',
                            data: {functionname: 'getGeoCMRToAjax', paPoint: myPoint},
                            success : function (result, status, error) {
                                if($("#cbvung").is(":checked"))
                                {
                                    highLightObj(result);
                                                   
                                }else{

                                }
                            },
                            error: function (req, status, error) {
                                alert(req + " " + status + " " + error);
                                console.log('that bai');
                            }
                        });

                        $.ajax({
                            type: "POST",
                            url: "pgsqlAPI.php",
                            //dataType: 'json',
                            data: {functionname: 'getInfoCMRToAjax', paPoint: myPoint},
                            success : function (result, status, error) {
                                if($("#cbvung").is(":checked"))
                                {
                                    $("#popup-content").html(result);
                                    overlay.setPosition(evt.coordinate);
                                    //console.log('thanh cong');
                                    console.log(result);
                                }
                                //infoObj(result);
                                
                            },
                            error: function (req, status, error) {
                                alert(req + " " + status + " " + error);
                                console.log('that bai');
                            }
                        });

                        

                    });
                }
                else
                {
                    map.removeLayer(vectorLayer);
                    VietNamGGM.setVisible(false);
    
                }
            });

            $("#cbduong").change(function () {
                if($("#cbduong").is(":checked"))
                {
                    VietNamGGM_roads.setVisible(true);
                    map.addLayer(vectorLayer);

                    map.on('click', function (evt) {
                        //alert("coordinate: " + evt.coordinate);
                        //var myPoint = 'POINT(12,5)';
                        var lonlat = ol.proj.transform(evt.coordinate, 'EPSG:3857', 'EPSG:4326');
                        var lon = lonlat[0];
                        var lat = lonlat[1];
                        var myPoint = 'POINT(' + lon + ' ' + lat + ')';
                        console.log(myPoint)
                        
                        $.ajax({
                            type: "POST",
                            url: "roads_pgsqlAPI.php",
                            //dataType: 'json',
                            data: {functionname: 'getGeoEagleToAjax', paPoint: myPoint},
                            success : function (result, status, error) {
                                if($("#cbduong").is(":checked"))
                                {
                                    highLightObj(result);
                                    
                                }
                            },
                            error: function (req, status, error) {
                                alert(req + " " + status + " " + error);
                                console.log('that bai');
                            }
                        });

                        $.ajax({
                            type: "POST",
                            url: "roads_pgsqlAPI.php",
                            //dataType: 'json',
                            data: {functionname: 'getInfoEagleToAjax', paPoint: myPoint},
                            success : function (result, status, error) {
                                if($("#cbduong").is(":checked"))
                                {
                                    $("#popup-content").html(result);
                                    overlay.setPosition(evt.coordinate);
                                    //console.log('thanh cong');
                                    console.log(result);
                                }
                                //infoObj(result);
                                
                            },
                            error: function (req, status, error) {
                                alert(req + " " + status + " " + error);
                                console.log('that bai');
                            }
                        });



                    });
                }
                else
                {
                    map.removeLayer(vectorLayer);
                    VietNamGGM_roads.setVisible(false);
                }
            });



            $("#cbdiem").change(function () {
                if($("#cbdiem").is(":checked"))
                {
                    VietNamGGM_dot.setVisible(true);
                    map.addLayer(vectorLayer);

                    map.on('click', function (evt) {
                        //alert("coordinate: " + evt.coordinate);
                        //var myPoint = 'POINT(12,5)';
                        var lonlat = ol.proj.transform(evt.coordinate, 'EPSG:3857', 'EPSG:4326');
                        var lon = lonlat[0];
                        var lat = lonlat[1];
                        var myPoint = 'POINT(' + lon + ' ' + lat + ')';
                        console.log(myPoint)
                        
                        $.ajax({
                            type: "POST",
                            url: "dots_pgsqlAPI.php",
                            //dataType: 'json',
                            data: {functionname: 'getGeoDotsToAjax', paPoint: myPoint},
                            success : function (result, status, error) {
                                if($("#cbdiem").is(":checked"))
                                {
                                    highLightObj(result);
                                    
                                }
                            },
                            error: function (req, status, error) {
                                alert(req + " " + status + " " + error);
                                console.log('that bai');
                            }
                        });

                        $.ajax({
                            type: "POST",
                            url: "dots_pgsqlAPI.php",
                            //dataType: 'json',
                            data: {functionname: 'getInfoDotsToAjax', paPoint: myPoint},
                            success : function (result, status, error) {
                                if($("#cbdiem").is(":checked"))
                                {
                                    $("#popup-content").html(result);
                                    overlay.setPosition(evt.coordinate);
                                    //console.log('thanh cong');
                                    console.log(result);
                                }
                                //infoObj(result);
                                
                            },
                            error: function (req, status, error) {
                                alert(req + " " + status + " " + error);
                                console.log('that bai');
                            }
                        });



                    });
                }
                else
                {
                    map.removeLayer(vectorLayer);
                    VietNamGGM_roads.setVisible(false);
                }
            });




            $("#cbdiem").change(function () {
                if($("#cbdiem").is(":checked"))
                {
                    VietNamGGM_dot.setVisible(true);
                    //map.addLayer(vectorLayer);

                    
                }
                else
                {
                    //map.removeLayer(vectorLayer);
                    VietNamGGM_dot.setVisible(false);
                }
            });

            $("#cbsong").change(function () {
                if($("#cbsong").is(":checked"))
                {
                    VietNamGGM_river.setVisible(true);
                    //map.addLayer(vectorLayer);

                    
                }
                else
                {
                    //map.removeLayer(vectorLayer);
                    VietNamGGM_river.setVisible(false);
                }
            });



            /*$("#tinh-thanhpho").change(function () {
                
                    $.ajax({
                    type: "POST",
                    url: "getData.php",
                    //dataType: 'json',
                    data: {functionname: 'getProvincialToAjax'},
                    success : function (result, status, error) {
                        infoData(result);
                        //sconsole.log(result);
                        /*var len = result.length;
                       
                        $("#tinh-thanhpho").empty();
                        for(var i = 0; i<len; i++){
                            var id = result[i]['gid_1'];
                            var name = result[i]['name_1'];
                            
                            $("#tinh-thanhpho").append("<option value='"+id+"'>"+name+"</option>");

                        }
                    },
                    error: function (req, status, error) {
                        alert(req + " " + status + " " + error);
                        console.log('that bai');
                    }
                });


                $("#tinh-thanhpho").val();

                
            });*/
            
            
            

            $("#btn-search").click(function (event){
                
                name = $("#search").val();
                
                if(name == ""){
                    alert("Chưa nhập tên quận (huyện)");
                }
                else{      
                    
                    $.ajax({
                        type: "POST",
                        url: "searchData.php",
                        //dataType: 'json',
                        data: {"name_2":name},
                        success : function (result, status, error) {
                            //console.log(data);
                            //highLightObj(result);
                            //console.log('thanh cong');
                            if($("#cbvung").is(":checked"))
                            {
                                highLightObj(result);
                                map.addLayer(vectorLayer);
                                
                            }
                            //highLightObj(result);
                            
                            //$("#info").html("Quận (Huyện): " + id1);
                            /*$("#popup-content").html(result);
                            overlay.setPosition(event.coordinate);*/
                            
                        },
                        error: function (req, status, error) {
                            alert(req + " " + status + " " + error);
                            console.log('that bai');
                        }
                    });
                    
                    event.preventDefault();
                }
                map.removeLayer(vectorLayer);
            });


            $("#btn-display").click(function (evt) {
                id1 = $("#quan-huyen").val();
                
                if(id1 == ""){
                    alert("Chưa chọn địa điểm");
                }
                else{
                
                
                    $.ajax({
                        type: "POST",
                        url: "pgsqlAPI1.php",
                        //dataType: 'json',
                        data: {"name2":id1},
                        success : function (result, status, error) {
                            //console.log(data);
                            //highLightObj(result);
                            //console.log('thanh cong');
                            
                            if($("#cbvung").is(":checked"))
                            {
                                highLightObj(result);
                                map.addLayer(vectorLayer);
                                $("#info").html("Quận (Huyện): " + id1);
                            }
                            
                            /*$("#popup-content").html(result);
                            overlay.setPosition(evt.coordinate);*/
                            
                        },
                        error: function (req, status, error) {
                            alert(req + " " + status + " " + error);
                            console.log('that bai');
                        }
                    });
                }

                map.removeLayer(vectorLayer);

                

                /*$.post('pgsqlAPI1.php', {"name_2":id}, function(data){
                    console.log(data);
                    //$("#quan-huyen").html(data);
                    
                    highLightObj(data);

                   
                });*/

                
                
            });

            // lọc huyện theo diện tích

            $("#btn-dientich").click(function (evt) {
                
                gid_1 = $("#tinh-thanhpho").val();
                //console.log(gid_1);
                dt = $("#dientich").val();
                //console.log(dt);

                if(gid_1 == "" || dt == ""){
                    alert("Chưa chọn Tỉnh (thành phố) hoặc chưa nhập khoảng diện tích muốn tìm");
                }
                else{
                    $.ajax({
                    type: "POST",
                    url: "searchDT.php",
                    //dataType: 'json',
                    data: {"gid_1":gid_1, "dt": dt},
                    success : function (result, status, error) {
                        if($("#cbvung").is(":checked"))
                        {
                            $("#quan-huyen").html(result);
                            map.addLayer(vectorLayer);                         
                        }
                        
                        //console.log(result)
                        
                    },
                    error: function (req, status, error) {
                        alert(req + " " + status + " " + error);
                        console.log('that bai');
                    }
                });

                }

                map.removeLayer(vectorLayer);
                
                
                
            });

            $("#btn-danso").click(function (evt) {
                
                gid_1 = $("#tinh-thanhpho").val();
                console.log(gid_1);
                ds = $("#danso").val();
                //console.log(dt);

                if(gid_1 == "" || ds == ""){
                    alert("Chưa chọn Tỉnh (thành phố) hoặc chưa nhập khoảng dân số muốn tìm");
                }
                else{
                    $.ajax({
                    type: "POST",
                    url: "searchDS.php",
                    //dataType: 'json',
                    data: {"gid_1":gid_1, "ds": ds},
                    success : function (result, status, error) {
                        if($("#cbvung").is(":checked"))
                        {
                            $("#quan-huyen").html(result);
                            map.addLayer(vectorLayer);                         
                        }
                        
                        //console.log(result)
                        
                    },
                    error: function (req, status, error) {
                        alert(req + " " + status + " " + error);
                        console.log('that bai');
                    }
                });

                }

                map.removeLayer(vectorLayer);
                
                
                
            });

            

        }


        jQuery(document).ready(function($){
            $("#tinh-thanhpho").change(function (event) {
                id = $("#tinh-thanhpho").val();
                console.log(id);
                $.post('getData1.php', {"gid_1":id}, function(data){
                    console.log(data);
                    $("#quan-huyen").html(data);
                    
                
                })
           });
        });


    </script>


    </head>
    <body onload="initialize_map();">
    
    <!--<div class="col-md-1">
        
        </div>
        
            <table class="table table-bordered">
                
                <tr>
                    
                    <td>
                        
                        
                    <div id="popup" class="ol-popup">
                    <a href="#" id="popup-closer" class="ol-popup-closer"></a>
                    <div id="popup-content"></div>
                        </div>
                        
                        <form class="form-group">

                            <div class="form-group">
                                <label for="">Tìm kiếm:</label>
                                <input type="text" id="search" placeholder="Tên huyện" class="form-control"/>
                            </div>

                            <button type="button" id="btn-search"  class="btn btn-success">Tìm kiếm</button>

                            <span id="result"></span>
                        </form>

                        <form class="form-group">

                            <div class="form-group">

                                <label>Tỉnh (thành phố)</label>
                                <select class="form-control" id="tinh-thanhpho" name="tinh-thanhpho">
                                    <option value="">Chọn tỉnh (thành phố)</option>
                                    
                                </select>
                            </div>

                            <div class="form-group">

                                <label>Quận (huyện)</label>

                                <select class="form-control" id="quan-huyen" name="quan-huyen">
                                    <option>Chọn quận (huyện)</option>
                                </select>
                            </div>

                            <button type="button" id="btn-display" class="btn btn-success" >Bấm vào đây</button>

                        </form>

                        

                        <form class="form-group">
                            <div class="form-group">
                                <label>Diện tích</label>

                                <input type="number" id="dientich" placeholder="Nhập diện tích" class="form-control"/>

                                <select class="form-control" id="dientich" name="dientich">
                                    <option>Chọn diện tích</option>
                                    <option value="5">Lớn hơn 5 km2</option>
                                    <option value="50">Lớn hơn 50 km2</option>
                                    <option value="100">Lớn hơn 100 km2</option>
                                    <option value="150">Lớn hơn 150 km2</option>
                                    <option value="200">Lớn hơn 200 km2</option>
                                </select>
                            </div>

                            <button class="btn btn-success" type="button" id="btn-dientich">Lọc và tìm kiếm</button>

                        </form>

                        <h4>Thay đổi</h4>
                        
                        <input type="checkbox" id="cbvung" checked /><label for="cbvung"> Vùng</label><br>
                        <input type="checkbox" id="cbduong" checked /><label for="cbduong"> Đường</label><br>
                        

                        <div id="info">Thông tin</div>
                    </td>

                    <td>
                        <div id="map" class="map" style="width: 80vw; height: 100vh;"></div>
                    </td>
                    
                </tr>
                
            </table>
        </div>
    </div>-->

    <div id="wrapper">

        

        <ul class="navbar-nav bg-light sidebar sidebar-light accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="http://localhost:8080/ggmap_v1/">
                <div class="sidebar-brand-icon rotate-n-15">

                <i class="fa fa-globe"></i>
                
                </div>
                <div class="sidebar-brand-text mx-3">GOOGLE EARTH</div>



            </a>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading" style="display: none;">
                Interface
            </div>
            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="../../../MainAdmin/Index">
                    <i class="fas fa-home"></i>
                    <span>Trang chủ</span>
                </a>
            </li>
            
            <hr class="sidebar-divider">

            
            

            
            
                        
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseTwo">
                    
                <i class="fa fa-globe-asia"></i>
                    <span>Vùng dữ liệu</span>
                </a>
                <div id="collapseThree" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-gray py-2 collapse-inner rounded">
                    <form class="form-group">

                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="cbvung" >
                            <label class="form-check-label" for="exampleCheck1">Chọn vùng</label>
                        </div>

                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="cbduong" >
                            <label class="form-check-label" for="exampleCheck1">Chọn đường</label>
                        </div>

                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="cbdiem">
                            <label class="form-check-label" for="exampleCheck1">Chọn điểm</label>
                        </div>

                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="cbsong">
                            <label class="form-check-label" for="exampleCheck1">Chọn sông</label>
                        </div>

                    </div>
                </div>

            </li>

                        


            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseTwo">
                    
                    <i class="fa fa-map" aria-hidden="true"></i>
                    <span>Tìm kiếm</span>
                </a>
                <div id="collapseOne" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-gray py-2 collapse-inner rounded">
                    <form class="form-group">

                        <div class="form-group">
                            <label for="">Tìm kiếm:</label>
                            <div class="input-group input-group-sm mb-3">
                                <input type="text" id="search" placeholder="Tên huyện" class="form-control"/>
                            </div>
                        </div>

                        <button type="button" id="btn-search" class="btn btn-dark btn-sm">Tìm kiếm</button>

                        <span id="result"></span>
                        </form>
                        <!--<a class="collapse-item" href="manager_changepass.php" target="iframe">Thông tin cá nhân</a>-->


                    </div>
                </div>

            </li>


            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                    
                    <i class="fa fa-search-plus"></i>
                    <span>Lọc và Hiển thị</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-gray py-2 collapse-inner rounded">
                        <form class="form-group">

                            <div class="form-group">

                                <label>Tỉnh (thành phố)</label>
                                <div class="input-group input-group-sm mb-3">
                                    <select class="form-control" id="tinh-thanhpho" name="tinh-thanhpho">
                                        <option value="">Chọn tỉnh (thành phố)</option>
                                        <?php getProvincialToAjax(initDB());?>
                                        
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">

                                <label>Quận (huyện)</label>
                                <div class="input-group input-group-sm mb-3">
                                    <select class="form-control" id="quan-huyen" name="quan-huyen">
                                        <option>Chọn quận (huyện)</option>
                                    </select>
                                </div>
                            </div>

                            <button type="button" id="btn-display" class="btn btn-dark btn-sm">Bấm vào đây</button>

                        </form>
                        <!--<a class="collapse-item" href="manager_changepass.php" target="iframe">Thông tin cá nhân</a>-->
                        <form class="form-group">
                            <div class="form-group">
                                <label>Diện tích</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="number" id="dientich" placeholder="Nhập diện tích" class="form-control"/>
                                </div>
                                
                            </div>

                            <button type="button" id="btn-dientich" class="btn btn-dark btn-sm">Lọc và tìm kiếm</button>

                        </form>

                        <form class="form-group">
                            <div class="form-group">
                                <label>Dân số</label>
                                <div class="input-group input-group-sm mb-3">
                                    <input type="number" id="danso" placeholder="Nhập số dân" class="form-control"/>
                                </div>
                                
                            </div>

                            <button type="button" id="btn-danso" class="btn btn-dark btn-sm">Lọc và tìm kiếm</button>

                        </form>

                    </div>
                </div>

            </li>

            
           
            
                                

            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>

        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                <!-- Sidebar Toggle (Topbar) -->
                <button id="sidebarToggleTop" class="btn btn-light d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>

                <!-- Topbar Search -->

                <div class="input-group">
                
                    <input type="text" class="form-control bg-light border-0 small" placeholder="Tìm kiếm..." aria-label="Search" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                        <button class="btn btn-light" type="button">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                    </div>
                </div>


                <!-- Topbar Navbar -->
                <ul class="navbar-nav ml-auto">

                    <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                    <li class="nav-item dropdown no-arrow d-sm-none">
                        <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-search fa-fw"></i>
                        </a>
                        <!-- Dropdown - Messages -->
                        <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
                            <form class="form-inline mr-auto w-100 navbar-search">
                                <div class="input-group">
                                    <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                                    <div class="input-group-append">
                                        <button class="btn btn-success" type="button">
                                            <i class="fas fa-search fa-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </li>

                    <!-- Nav Item - Alerts -->


                    <div class="topbar-divider d-none d-sm-block"></div>

                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">






                            </span>
                            <img class="img-profile rounded-circle" src="https://www.mandarinstone.com/app/uploads/2017/09/Geometric-Cube-Decor-Ebony_Dove_White-Swatch.jpg">
                        </a>
                        <!-- Dropdown - User Information -->
                        <div class="dropdown-menu dropdown-menu-right shadow animated-grow-in" aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="#" id="profile" role="button" data-toggle="dropdown">
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                Cá nhân
                            </a>
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated-grow-in" aria-labelledby="profile" style="top: 12px; right: 165px; ">
                                <h6 class="dropdown-header">
                                    Thông tin cá nhân
                                </h6>

                            </div>

                            
                            <div class="dropdown-divider"></div>
                            
                        </div>
                        

                    </li>

                </ul>

            </nav>


            <div class="container-fluid">
                <div id="popup" class="ol-popup">
                <a href="#" id="popup-closer" class="ol-popup-closer"></a>
            <div id="popup-content"></div>
            </div>

                <div id="map" style="width: 80vw; height: 120vh;"></div>

            </div>

        </div>
    </div>

    </body>

    

    <script src="asset/vendor/jquery/jquery.min.js"></script>

    <script src="asset/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="asset/vendor/jquery-easing/jquery.easing.min.js"></script>

    <script src="asset/js/sb-admin-2.min.js"></script>

</html>

