/**
 * Created by Administrator on 2019/9/2.
 */
/**
 * Created by Administrator on 2019/8/28.
 * 
 */

//  添加底图
var url = "http://10.3.100.6:8090/iserver/services/map-zhengDing/rest/maps/zhengDing";
var container = document.getElementById('popup');
var content = document.getElementById('popup-content');
var overlay = new ol.Overlay(({
    element: container,
    autoPan: true,
    autoPanAnimation: {
        duration: 250
    }
}));
var map = new ol.Map({
    target: 'map',
    controls: ol.control.defaults({attribution:false,zoom:false}),
    view: new ol.View({
        center: [114.62 , 38.14],
        zoom: 14,
        projection: 'EPSG:4326'

    }),
    overlays: [overlay]
});

var layer = new ol.layer.Tile({
    source: new ol.source.TileSuperMapRest({
        url: url,
        wrapX: true
    }),
    projection: 'EPSG:4326'
});

map.addLayer(layer);


var xunJianData;

var socket = io('192.168.10.18:2120');
// uid可以是自己网站的用户id，以便针对uid推送以及统计在线人数
uid = 222;        
var data,flag,data3;
var arr = new Array();
// console.log(att);
var time;
var styleArr = [];


// 设置初始点线样式
var initPoint = new ol.style.Circle({
    radius: 5,
    fill: null,
    stroke: new ol.style.Stroke({color: 'red', width: 3})
});

var initPointstyles = {
    'Point': new ol.style.Style({
        image: initPoint
    }),
    'LineString': new ol.style.Style({
        stroke: new ol.style.Stroke({
            color: 'yellow',
            width: 4
        })
    }),
};
var initPointStyleFunction= function(feature) {
    return initPointstyles[feature.getGeometry().getType()];
    
};

//设置实时点样式
var image = new ol.style.Icon({
    // crossOrigin:'anonymous',
    // scale:0.3,
    imgSize:[32,32],
    src:"../../../static/new/image/people.png"
});
var imageStyles = {
    'Point': new ol.style.Style({
        image: image
    }),
    'LineString': new ol.style.Style({
        stroke: new ol.style.Stroke({
            color: 'yellow',
            width: 4
        })
    }),
};
var imgStyleFunction= function(feature) {
    return imageStyles[feature.getGeometry().getType()];
    
};

//设置实时线样式
var shiLineStyles = {
    'LineString': new ol.style.Style({
        stroke: new ol.style.Stroke({
            color: 'green',
            width: 10
        })
    }),
};
var shiLineStyleFunction= function(feature) {
    return shiLineStyles[feature.getGeometry().getType()];
    
};




function flash(feature) {
    var start = new Date().getTime();
    var listenerKey;

    function animate(event) {
        var duration = 3000;
        var vectorContext = event.vectorContext;
        var frameState = event.frameState;
        var flashGeom = feature.getGeometry().clone();
        var elapsed = frameState.time - start;
        var elapsedRatio = elapsed / duration;
        var radius = ol.easing.easeOut(elapsedRatio) * 25 + 5;
        var opacity = ol.easing.easeOut(1 - elapsedRatio);
        var style = new ol.style.Style({
            image: new ol.style.Circle({
                radius: radius,
                snapToPixel: false,
                stroke: new ol.style.Stroke({
                    color: 'rgba(255, 0, 0, ' + opacity + ')',
                    width: 0.25 + opacity
                })
            })
        });
        vectorContext.setStyle(style);
        vectorContext.drawGeometry(flashGeom);
        if (elapsed > duration) {
            ol.Observable.unByKey(listenerKey);
            return;
        }
        map.render();
    }

    listenerKey = map.on('postcompose', animate);
}







// socket连接后以uid登录
var point;




// 添加实时人员位置
function addPointLayer(pointData){
    //console.log(pointData);
    
 
    var geojsonObject = {
        'type': 'FeatureCollection',
        'crs': {
            'type': 'name',
            'properties': {
                'name': 'EPSG:4326'
            }
        },
        'features': [{
            'type': 'Feature',
            'geometry': {
                'type': 'Point',
                 'coordinates': [pointData.lng,pointData.lat]
                // 'coordinates': [114.61520860247, 38.123817756155]
                
            }
        }]
    };
    var vectorPointSource = new ol.source.Vector({
        features: (new ol.format.GeoJSON()).readFeatures(geojsonObject)
    });
    vectorPointSource.addFeature(new ol.Feature(new ol.geom.Circle([5e6, 7e6], 1e6)));
    var vectorPointLayer = new ol.layer.Vector({
        source: vectorPointSource,
        style: imgStyleFunction
    });
    return vectorPointLayer;
}


var linePointArr = [];
var lineShiShiPoint = [];

//添加巡检路线
// addPatRouLayer();
function addPatRouLayer(linePoint){
    var geojsonObject = {
        'type': 'FeatureCollection',
        'crs': {
            'type': 'name',
            'properties': {
                'name': 'EPSG:4326'
            }
        },
        'features': [{
            'type': 'Feature',
            'geometry': {
                'type': 'LineString',
                'coordinates': [linePoint]
                // 'coordinates': [[114.61465703896, 38.122961340611],[114.61520860247, 38.123817756155]]
            }
        }]
    };
    var vectorLineSource = new ol.source.Vector({
        features: (new ol.format.GeoJSON()).readFeatures(geojsonObject)
    });
    vectorLineSource.addFeature(new ol.Feature(new ol.geom.Circle([5e6, 7e6], 1e6)));
     var vectorLineLayerT = new ol.layer.Vector({
        source: vectorLineSource,
        style: imgStyleFunction
    });
    // map.addLayer(vectorLayerT);
    // vectorLayerT.on('click',function(){
    //    console.log('dianji l ');
    // })
    return vectorLineLayerT;
}
// addPatRouLayer();

// socket链接 判断是否有正在巡检任务
socket.on('connect', function(){
    console.log('socket链接');
    socket.emit('login', uid);
    // 添加起点和终点以及线路
    // 获取数据库内巡检信息 进行后续判断
    $.ajax({
        url:"http://192.168.10.18/patrol/get_patrol",
        async:true,
        success:function(result){
            
        var obj = eval('('+result+')');
        // console.log(obj);
        // 没有正在巡检任务
        if(obj.code){
            // console.log(obj.msg);
        // 有正在巡检任务
        }else{
            
            var objs = obj.replace(/\'/g,'"');
            var objs =  eval('('+objs+')');
            // console.dir(objs);
            // 获取巡检信息 转换成obj
            data = objs.routeList;
            //console.dir(data);
            flag = objs.flag;
            user_id = objs.qs_u_ids;
            checkCode = objs.checkRecordInfo.checkCode;
            xunJianData = objs.checkRecordInfo;

            // 根据正在巡检的路线信息 制作巡检路线
            arr[checkCode] = aaalayer(data);

            // 将首个点存进数组 制作巡检路线起始结束点
            linePointArr.push(data[0]);
            // 画巡检路线
            map.addLayer(arr[checkCode]);
           // console.dir(arr);
            
           //将 定时器添加到数组中 待删除 
            time = checkCode + 'time';
            arr[time] = window.setInterval("real_time(user_id)",5000);
            // console.dir('1111111111111');
            // console.log(data);
        }     
        
        }
    });


    
   
});
// 定时器方法 实现 根据人员id 获取实时位置点位 画点 画线
function real_time(user_id){

        $.ajax({
            url:"http://192.168.10.18/patrol/get_user_position?check_user_id="+user_id,
            async:true,
            success:function(result){
                //将 获取到的实时信息 转换成obj
                points = eval('('+result+')');

                  //将点位坐标 str转浮点 并存入点位数组 后续画线用
                linePointArr.push([parseFloat(points.lng),parseFloat(points.lat)]);
                //console.dir(linePointArr);
        
                //var arrs = [linePointArr[linePointArr.length-2],linePointArr[linePointArr.length-1]];
                //console.dir(linePointArr);
                line = checkCode + 'line';
                // 生成其他颜色线 当做实时巡检路线
                map.removeLayer(arr[line]);
                arr[line] = bbblayer(linePointArr);
                map.addLayer(arr[line]);
                //console.dir(point);
                //点 生成 并 存入数组 待删除
                point= checkCode + 'point';
                
                //arr[point] = addPointLayer(points);
                // console.dir(arr[point]);
                // console.log(arr[checkCode+='point']);
                // if(arr[checkCode+='point'])
                //有的话删除 并新建


               if (arr[point]) {
                    map.removeLayer(arr[point]);
                    arr[point] = addPointLayer(points);
                    map.addLayer(arr[point]);
                    // console.dir(arr);
                    // 没有 新建
               } else {
                    map.removeLayer(arr[point]);
                    arr[point] = addPointLayer(points);
                    map.addLayer(arr[point]);
               }
              
                //console.dir(arr[line]);


                // 线生成
                //map.addLayer(addPatRouLayer(arrs));
            

                //console.log(point.lng);
                //console.log(point.lat);
            }
        });
        //console.dir(arr);
}

// 后端推送来消息时
socket.on('new_msg', function(msg){

    console.log('后端推送点消息');
    // console.dir(msg);
    data = eval('('+msg+')').routeList;
    flag = eval('('+msg+')').flag;
    user_id = eval('('+msg+')').qs_u_ids;
    //结束巡检
    if(flag === '0'){
        console.log('结束巡检');
        checkCode = eval('('+msg+')').checkCode;

        // console.log(eval('('+msg+')'));

        
        time = checkCode + 'time';
        line = checkCode + 'line';
        point = checkCode + 'point';
           //删除 指定的 定时器
           window.clearInterval(arr[time]);
        //如果打开页面时有正在巡检的路线  此时将根据 checkcode 该巡检路线删除
        map.removeLayer(arr[checkCode]);
     
        //删除 最后一个实时点
        map.removeLayer(arr[point]);
        // 删除实时巡检路线
        map.removeLayer(arr[line]);

        // 开始巡检
    }else if(flag === '1'){
        console.log('开始巡检');
        checkCode = eval('('+msg+')').checkRecordInfo.checkCode;
        arr[checkCode] = aaalayer(data);
        //console.log(point);
        // console.log(point.lng);
                //  .console.dir(obj.lat);
                // console.dir(obj.lng);
        map.addLayer(arr[checkCode]);

         // 定时器
         var objs =  eval('('+msg+')');
         data = objs.routeList;
         flag = objs.flag;
         user_id = objs.qs_u_ids;
         time = checkCode + 'time';
         arr[time] = window.setInterval("real_time(user_id)",5000);
         // console.dir(arr);
    }
    //console.dir(arr[checkCode]);

// map.addControl(new ol.supermap.control.ScaleLine());
});

function aaalayer(data){
    
    var geojsonObject = {
        'type': 'FeatureCollection',
        'crs': {
            'type': 'name',
            'properties': {
                'name': 'EPSG:4326'
            }
        },
        'features': [{
            'type': 'Feature',
            'geometry': {
                'type': 'Point',
                'coordinates': data[0]
            }
        },{
            'type': 'Feature',
            'geometry': {
                'type': 'Point',
                'coordinates': data[data.length-1]
            }
        },{
            'type': 'Feature',
            'geometry': {
                'type': 'LineString',
                'coordinates':data
            }
        }]
    };
    var vectorSource = new ol.source.Vector({
        features: (new ol.format.GeoJSON()).readFeatures(geojsonObject)
    });
    vectorSource.addFeature(new ol.Feature(new ol.geom.Circle([5e6, 7e6], 1e6)));
    var vectorLayer = new ol.layer.Vector({
        source: vectorSource,
        style: initPointStyleFunction
    });
    return vectorLayer;

}

function bbblayer(data){
    
    var geojsonObject = {
        'type': 'FeatureCollection',
        'crs': {
            'type': 'name',
            'properties': {
                'name': 'EPSG:4326'
            }
        },
        'features': [{
            'type': 'Feature',
            'geometry': {
                'type': 'Point',
                'coordinates': data[0]
            }
        },{
            'type': 'Feature',
            'geometry': {
                'type': 'Point',
                'coordinates': data[data.length-1]
            }
        },{
            'type': 'Feature',
            'geometry': {
                'type': 'LineString',
                'coordinates':data
            }
        }]
    };
    var vectorSource = new ol.source.Vector({
        features: (new ol.format.GeoJSON()).readFeatures(geojsonObject)
    });
    vectorSource.addFeature(new ol.Feature(new ol.geom.Circle([5e6, 7e6], 1e6)));
    var vectorLayer = new ol.layer.Vector({
        source: vectorSource,
        style: shiLineStyleFunction
    });
    return vectorLayer;

}

// 巡检路线点鼠标事件展示巡检信息
map.on('click', function (e) {
    var coordinate = e.coordinate;
    if(typeof(feature)== 'undefined'){
        // console.dir('asdf');
        $('#popup').css('display','none');
    }
    map.forEachFeatureAtPixel(e.pixel, function (feature) {
        $('#popup').css('display','block');
        mapService(coordinate);
        // console.log(e.coordinate);
        // console.log(feature.getProperties());
        // vectorTileStyles.dispatchEvent({type: 'featureSelected',
        //     selectedId: feature.getProperties().id,
        //     layerName: feature.getProperties().layerName
        // });
        return true;
    }, {hitTolerance: 5});
    // vectorLayer.changed();
});



    // map.removeLayer(vectorLayer);
    // map.removeLayer(vectorLayer);
    // console.dir('0000000000000');

// map.addControl(new ol.supermap.control.ScaleLine());


function mapService(coordinate) {

    var innerHTML;
    // for(var i in xunJianData){
    //     var property = xunJianData[i];
    //     innerHTML += i+"："+property+"<br>";
    // }
    // content.innerHTML = innerHTML;

    var innerHTML= '编号：'+xunJianData.checkCode+"<br>";
    innerHTML += '巡更人员：'+xunJianData.checkUser+'<br>';
    innerHTML += '街道名称：'+xunJianData.street+'<br>';
    innerHTML += '防火分区：'+xunJianData.preventFireArea+"<br>";
    // innerHTML += '开始时间：'+xunJianData.checkStartTime+'<br>';
    content.innerHTML = innerHTML;

//    new ol.supermap.MapService(url, {
//     "projection": "4326"}).getMapInfo(function (serviceResult) {
//     var innerHTML = "(" + serviceResult.text_mapInfoPrint + ")" + "<br><br>";
//     innerHTML += serviceResult.text_mapName + "：" + JSON.stringify(serviceResult.result.name, null, 2) + "<br>";
//     innerHTML += serviceResult.text_center + ":" + JSON.stringify(serviceResult.result.center, null, 2) + "<br>";
//     innerHTML += "Bounds:" + JSON.stringify(serviceResult.result.bounds, null, 2) + "<br>";
//     content.innerHTML = innerHTML;
    overlay.setPosition(coordinate);
// });
}

// function aaalayer(data){
//     var image = new ol.style.Circle({
//         radius: 5,
//         fill: null,
//         stroke: new ol.style.Stroke({color: 'red', width: 3})
//     });
//     var styles = {
//         'Point': new ol.style.Style({
//             image: image
//         }),
//         'LineString': new ol.style.Style({
//             stroke: new ol.style.Stroke({
//                 color: 'yellow',
//                 width: 4
//             })
//         }),
//     };
//     var styleFunction = function(feature) {
//         return styles[feature.getGeometry().getType()];
//     };
//     var geojsonObject = {
//         'type': 'FeatureCollection',
//         'crs': {
//             'type': 'name',
//             'properties': {
//                 'name': 'EPSG:4326'
//             }
//         },
//         'features': [{
//             'type': 'Feature',
//             'geometry': {
//                 'type': 'Point',
//                 'coordinates': [114.614657038962, 38.1229613406109]
//             }
//         },{
//             'type': 'Feature',
//             'geometry': {
//                 'type': 'Point',
//                 'coordinates': [114.6207409421,38.1314738181938]
//             }
//         },{
//             'type': 'Feature',
//             'geometry': {
//                 'type': 'LineString',
//                 'coordinates': data}
//         }]
//     };
//     var vectorSource = new ol.source.Vector({
//         features: (new ol.format.GeoJSON()).readFeatures(geojsonObject)
//     });
//     vectorSource.addFeature(new ol.Feature(new ol.geom.Circle([5e6, 7e6], 1e6)));
//     vectorLayer = new ol.layer.Vector({
//         source: vectorSource,
//         style: styleFunction
//     });
//     return vectorLayer;

// }
function deletee(){
    map.removeLayer(arr[checkCode]);
     
    //删除 最后一个实时点
    map.removeLayer(arr[point]);
    // 删除实时巡检路线
    map.removeLayer(arr[line]);
}