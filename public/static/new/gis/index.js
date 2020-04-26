/**
 * Created by Administrator on 2019/8/28.
 */
// var ol = require('openlayers');
// var logo = require("@supermap/iclient-openlayers").Logo;
// var TileSuperMapRest = require('@supermap/iclient-openlayers').TileSuperMapRest;
var url = "http://10.3.100.6:8090/iserver/services/map-zhengDing/rest/maps/zhengDing";
var map = new ol.Map({
    target: 'map',
    /*controls: ol.control.defaults({attributionOptions: {collapsed: false}})
        .extend([new ol.supermap.control.Logo()]),*/
    view: new ol.View({
        center: [114.62 , 38.14],
        zoom: 14,
        projection: 'EPSG:4326'
    })
});
var layer = new ol.layer.Tile({
    source: new ol.source.TileSuperMapRest({
        url: url,
        wrapX: true
    }),
    projection: 'EPSG:4326'
});
map.addLayer(layer);
map.addControl(new ol.supermap.control.ScaleLine());