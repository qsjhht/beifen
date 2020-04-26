/**
 * Created by Administrator on 2020/4/3.
 */
$(function () {
    // alert('11');

    var url = "http://192.168.10.18:8090/iserver/services/map-zhengDing/rest/maps/zhengDing";
    var map = new ol.Map({
        target: 'map',
        view: new ol.View({
            center: [114.62 , 38.14],
            zoom: 14,
            maxZoom:20,
            minZoom:12,
            projection: 'EPSG:4326'
        })
    });

//获取坐标系统
// ol.View.getProjection();
    var layer = new ol.layer.Tile({
        source: new ol.source.TileSuperMapRest({
            url: url,
            wrapX: true
        }),
        projection: 'EPSG:4326'
    });
    map.addLayer(layer);

 query();

 // 鼠标事件
 var stylesOptions = {
 url: url,
 view: map.getView()
 };
 var sections;
 var vectorTileStyles = new ol.supermap.VectorTileStyles(stylesOptions);

 var Ts,Ms,O2s,CH4s,H2Ss,LTs,Td,Md,O2d,CH4d,H2Sd,LTd,Section,urld,urls,urlT = 'http://192.168.10.18/real/real_time';
 map.on('pointermove',function (e) {
 if(typeof(feature)== 'undefined'){
 $('#popu').css('display','none');
 }
 var features = map.forEachFeatureAtPixel(e.pixel,function (feature) {

 //            console.log(feature);
 if(sections !== feature.N.Section){
 $('#popu').css("display","block");
 Section = feature.N.Section;
 urls = urlT+'?zone='+Section+'S';
 urld = urlT+'?zone='+Section+'D';
 Ts = 'T_'+Section+'S';
 Ms = 'M_'+Section+'S';
 O2s = 'O2_'+Section+'S';
 CH4s = 'CH4_'+Section+'S';
 H2Ss = 'H2S_'+Section+'S';
 LTs = 'LT_'+Section+'S';

 Td = 'T_'+Section+'D';
 Md = 'M_'+Section+'D';
 O2d = 'O2_'+Section+'D';
 CH4d = 'CH4_'+Section+'D';
 H2Sd = 'H2S_'+Section+'D';
 LTd = 'LT_'+Section+'D';
 // console.log(Ts);
 // console.log(Section);


 $("#galRoad").html(feature.N.Name);
 $("#section").html(Section);
 //                请求水仓
 $.ajax({
 url:urls,
 async:true,
 success:function(result){
 var obj = eval('('+result+')');
 Ts = 'T_'+feature.N.Section+'S';
     if (obj.length !==0){
         $("#Ts").html(obj[Ts]);
         $("#Ms").html(obj[Ms]);
         $("#O2s").html(obj[O2s]);
         $("#CH4s").html(obj[CH4s]);
         $("#H2Ss").html(obj[H2Ss]);
         $("#LTs").html(obj[LTs]);
     }else {
         $("#Ts").html("--");
         $("#Ms").html("--");
         $("#O2s").html("--");
         $("#CH4s").html("--");
         $("#H2Ss").html("--");
         $("#LTs").html("--");
     }


 }
 });
 //                请求电仓
 $.ajax({
 url:urld,
 async:true,
 success:function(result){
 var obj = eval('('+result+')');

     if (obj.length !==0){
         $("#Td").html(obj[Td]);
         $("#Md").html(obj[Md]);
         $("#O2d").html(obj[O2d]);
         $("#CH4d").html(obj[CH4d]);
         $("#H2Sd").html(obj[H2Sd]);
         $("#LTd").html(obj[LTd]);
     }else {
         $("#Td").html("--");
         $("#Md").html("--");
         $("#O2d").html("--");
         $("#CH4d").html("--");
         $("#H2Sd").html("--");
         $("#LTd").html("--");
     }

 }
 });
 }
 //            sections = feature.N.Section;
 // alert(feature.N.Section);
 // alert(feature.N.Section);

 // console.log(e.pixel);
 // var x = e.pixel[0]+"px";
 // var y = e.pixel[1]+'px';
 // popu.style.top = y;
 // popu.style.left = x;
 // // vectorTileStyles.dispatchEvent({type: 'featureSelected',
 //     selectedId: feature.getProperties().id,
 //     layerName: feature.getProperties().layerName
 // });
 return true;
 },{hitTolerance:5})});


 // SQL查询
 function query() {
 var param = new SuperMap.QueryBySQLParameters({
 queryParams: {
 name: "gallery_L@zhengDing.1",
 // attributeFilter: "NAME = 新城大街",
 fields:["Name",'Section']
 }
 });
 new ol.supermap.QueryService(url).queryBySQL(param, function (serviceResult) {
  // console.log('111');
  // console.log(serviceResult);
 /*console.log(serviceResult.result.recordsets[0].features.forEach);
 console.log(serviceResult.result.recordsets[0].features.features[0].properties.Section);*/

 var vectorSource = new ol.source.Vector({
 features: (new ol.format.GeoJSON()).readFeatures(serviceResult.result.recordsets[0].features),
 wrapX: false,
 });
 // console.log(vectorSource);
 resultLayer = new ol.layer.Vector({
 source: vectorSource
 });
 map.addLayer(resultLayer);
 });
 }
 })

