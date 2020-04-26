console.log('调用了GIS');
var map,layer,utfgrid,control,infowin,name,
//            url = "http://localhost:8090/iserver/services/map-first/rest/maps/TrafficMap@BaiduMap";
    url = 'http://192.168.10.18:8090/iserver/services/map-map/rest/maps/map';

function init() {
    map = new SuperMap.Map ("map",{controls:[
            /* new SuperMap.Control.ScaleLine(),
             new SuperMap.Control.PanZoomBar({showSlider:true}),
             new SuperMap.Control.LayerSwitcher(),*/
            new SuperMap.Control.Navigation({
                dragPanOptions: {
                    enableKinetic: true
                }
            })],
        projection: "EPSG:3857"
    });
    map.numZoomLevels = 5;
    map.minZoom = 0;
    map.addControl(new SuperMap.Control.MousePosition());
    //map.addControl (new  SuperMap.Control.OverviewMap());
    //创建分块动态 REST 图层，该图层显示 iserver 8C 服务发布的地图,
    //其中"world"为图层名称，url 图层的服务地址，{transparent: true}设置到 url 的可选参数
    layer = new SuperMap.Layer.TiledDynamicRESTLayer("map", url, {transparent: true}, {useCanvas: true, maxResolution:"auto"});

    utfgrid = new SuperMap.Layer.UTFGrid("UTFGridLayer",url,
        {
//                    layerName: "New_Line@first#1",
            layerName:'New_Line_1@googleLine2#1',
            utfTileSize: 256,
            pixcell: 8,
            isUseCache: false
        },
        {
            utfgridResolution: 8
        });


    layer.events.on({"layerInitialized": addLayer});
}

function addLayer() {
    var center = new SuperMap.LonLat(114.61663,38.13874);
    //将 Layer 图层加载到 Map 对象上
    map.addLayers([layer,utfgrid]);
    //出图，map.setCenter 函数显示地图  设置中心点及缩放级别
    map.setCenter(center,0);
    //加载要素选择器控件 绑定事件 hover 是否悬浮
    /*selectFeature = new SuperMap.Control.SelectFeature(marks,
     {
     onSelect: OnFeatureSelect,
     onUnselect: OnFeatureUnselect,
     hover:true
     });
     //添加 激活
     map.addControl(selectFeature);
     selectFeature.activate();*/
    utfgrid.maxExtent=layer.maxExtent;
//            utfgrid2.maxExtent=layer.maxExtent;
}