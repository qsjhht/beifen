let date = new Date();
var preDate = new Date(date.getTime() - 24*60*60*1000);
var nextDate = new Date(date.getTime() + 24*60*60*1000);


var d0 = new Date(date.getTime() - 24*60*60*1000*2);
var d1 = new Date(date.getTime() - 24*60*60*1000*3);
var d2 = new Date(date.getTime() - 24*60*60*1000*5);
var d3 = new Date(date.getTime() - 24*60*60*1000*7);

/*let time = {
    year : function(){
        return date.getFullYear()+'年'+(date.getMonth()+1)+'月'+ date.getDate()+'日'
    },
    year1 : function(){
        return preDate.getFullYear()+'-'+(preDate.getMonth()+1)+'-'+ preDate.getDate()
    },
    year2 : function(){
        return date.getFullYear()+'-'+(date.getMonth()+1)+'-'+ date.getDate()
    },
    year3 : function(){
        return nextDate.getFullYear()+'-'+(nextDate.getMonth()+1)+'-'+ nextDate.getDate()
    },
    day: function(){
        return date.getDay()
    },
    dayDate: function(){
        switch(this.day()){
            case 0:return '星期日'
            break;
            case 1:return '星期一'
            break;
            case 2:return '星期二'
            break;
            case 3:return '星期三'
            break;
            case 4:return '星期四'
            break;
            case 5: return '星期五'
            break;
            case 6:return '星期六'
            break;
        }
    },
    crew:function(){
        return '<img src="../../../static/new/image/user-fill.png" width:="20" height="18" style="position:relative;top:-1px"/>在岗<li class="layui-inline" id="person_num">－－</li>人'
    }
};
let infoDate = document.getElementById('infoDate');
let content = time.year()+'&nbsp;&nbsp;'+time.dayDate()+'&nbsp;&nbsp;|&nbsp;&nbsp;'+time.crew();
infoDate.innerHTML = content;
let date1 = document.getElementById('date1');
let date2 = document.getElementById('date2');
let date3 = document.getElementById('date3');
date1.innerHTML = time.year1();
date2.innerHTML = time.year2();
date3.innerHTML = time.year3();*/
/*// 值班人员设置
let duty = {
    crew:'李勇',
    strip:'<div class="progress">'+
        '<div class="progressFro"></div>'+
            '<div class="progressBack"></div>'+
            '</div>',
    sum:function(){
        return '值班：'+this.crew+'&nbsp;&nbsp;8:30'+this.strip+'18:30'
    }
}
let infoDty = document.getElementById('infoDty');
infoDty.innerHTML = duty.sum();*/

let data = {
    getYear:date.getFullYear(),//年
    getMonth:date.getMonth()+1,//月
    getDate:date.getDate(),//日
    // 当前日期
    timeLcale:function(){
        return this.getYear+'/'+this.getMonth+'/'+this.getDate
    },
    getTime: date.getHours(),//小时
    weeHours: date.getMinutes(),//分钟
    // 遍历次数
    number:function(){
        return (this.getTime*60+this.weeHours)*4
    },
    // 数组
    arr:[],// 水位
    arr10:[],//用电量
    water: function(aaa,bbb) {
        let hour = 0;
        let minu = 0;
        let second = 0;
        let num = 0;
        switch (aaa) {
            case 6 ://水位
                for (var i = 0; i < 5760; i++) {
                    num = parseInt((Math.random() - 0.5) * aaa)+ bbb;
                    hour = Math.floor(i / 240);
                    if (hour < 10) {
                        hour = '0' + hour
                    }
                    minu = Math.floor(i % 240 / 4)
                    if (minu < 10) {
                        minu = '0' + minu
                    }
                    second = i % 4 * 15;
                    if (second == 0) {
                        second = '00'
                    }
                    this.arr.push([this.timeLcale() + ' ' + hour + ':' + minu + ':' + second, num])
                }
                break;
            case 5://用电量
                for (var i = 0; i < 5760; i++) {
                    num += parseInt((Math.random() - 0.45) * aaa);
                    hour = Math.floor(i / 240);
                    if (hour < 10) {
                        hour = '0' + hour
                    }
                    minu = Math.floor(i % 240 / 4)
                    if (minu < 10) {
                        minu = '0' + minu
                    }
                    second = i % 4 * 15;
                    if (second == 0) {
                        second = '00'
                    }
                    this.arr10.push([this.timeLcale() + ' ' + hour + ':' + minu + ':' + second, num + bbb])
                }
                break;
        }
    },
    environ:function(ccc,ddd){
        let num = 0;
        let arr1 = [];
       /* for(var y = 0 ; y<this.getDate; y++){
            num = ((Math.random()-0.5)*ccc+ddd).toFixed(2);
            arr1.push([this.getYear+'/'+this.getMonth+'/'+(y+1),num])
        }*/
    for(var y = 0 ; y < 30 ;y++){
       num = ((Math.random()-0.5)*ccc+ddd).toFixed(2);
       if( [this.getDate - y] > 0 ){
        arr1.push( [this.getYear + '/' + this.getMonth + '/'+[this.getDate-y] ,num])
       }else{
        arr1.push([this.getYear + '/'+[this.getMonth-1] + '/' + [this.getDate- y + 30] , num])
       }
    }
        return arr1.reverse();
    }
};

data.water(5,30);//用电量

// 动力配电
    let contentEle = echarts.init(document.getElementById('contentEle'));
    let eleOption = {
        title:{
            text:'动力配电监测',
            textStyle:{
                color:'#00ffff',
                fontWeight:'normal',
                fontSize:16
            },
            padding:[15,10]
        },
        tooltip: {
            trigger: 'axis'
        },
        xAxis: {
            name:'时间',
            type: 'time',
            axisLine:{
                lineStyle:{
                    color:'#FFFFFF'
                }
            },
            splitLine: {
                show: false
            }
        },
        yAxis: {
            name:'用电量（kw/h）',
            type: 'value',
            boundaryGap: ['20%', '100%'],
            axisLine:{
                lineStyle:{
                    color:'#FFFFFF'
                }
            },
            splitLine: {
                show: false
            },
            inverse:false,
            min:0
        },
        dataZoom:[{
            start:95,
            end:100,
        backgroundColor:'rgb(ff,ff,ff)',
        height:'15',
        bottom:'3%'
        },{
            type:'slider',
        height:'15',
        bottom:'3%'
        }],
        series: [{
            name: '用电量(kw/h)',
            type: 'line',
            showSymbol: false,
            hoverAnimation: false,
            data:data.arr10.slice(0,data.number())
        }]
    }
contentEle.setOption(eleOption);


let alarmnum = $('#alarmnum').val();

alarmnum = JSON.parse(alarmnum);

// console.dir(alarmnum);

alarmoption = {
    title : {
        text:'报警数据',
        textStyle:{
            color:'#00ffff',
            fontWeight:'normal',
            fontSize:16
        },
        padding:[15,10]
    },

    color:['#c23531','#d48265','#91c7ae', '#c4ccd3', '#91c7ae','#749f83','#ca8622', '#bda29a','#6e7074', '#546570', '#c4ccd3'],
    tooltip : {
        trigger: 'item',
        formatter: "{a} <br/>{b} : {c} ({d}%)"
    },
    legend: {
        type: 'scroll',
        orient: 'vertical',
        right: 10,
        top: 20,
        bottom: 20,
        textStyle:{
            color:'#0ff',
            fontSize:8
        },
        right:'-1%',
        itemWidth :13,
        itemHeight:7,
        // data: ["严重","紧急","普通","设备"],
    },
    grid: {
        bottom: 5
    },
    series : [
        {
            name: '报警类型',
            type: 'pie',
            radius: ['40%', '60%'],
            center: ['50%', '55%'],
            // roseType:true,
            data: alarmnum,
            label:{            //饼图图形上的文本标签
                show : true,
                // formatter: "{b} : {c} ({d}%)"
                formatter: "{b} :  {c}  \n ({d}%)",
                fontSize:10
            },

            labelLine : {
                show : true   //隐藏标示线
            },
            itemStyle: {

                emphasis: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            }
        }
    ]
};

let callOption = {
    title:{
        text:'报警数据',
        textStyle:{
            color:'#00ffff',
            fontWeight:'normal',
            fontSize:16
        },
        padding:[15,10]
    },
    tooltip: {
        trigger: 'axis'
    },
    xAxis:{
        // name:'类型',
        type:'category',
        // data:['严重','紧急','普通','设备'],
        axisLine:{
            lineStyle:{
                color:'#FFFFFF'
            }
        }
    },
    grid: {
        bottom: 25
    },
    yAxis:{
        name:'数量',
        type:'value',
        axisLine:{
            lineStyle:{
                color:'#FFFFFF'
            }
        },
        min:0,
        splitLine: {
            show: false
        }
    },
    series:{
        type:'bar',
        name:'报警数量',
        barWidth:'60%',
        itemStyle:{
            color:'#00FFFF'
        },
        data:alarmnum
    }
};
callIcon.setOption(alarmoption);

// 水位监测
//获取历史数据
let sensor_waters = [];
$.ajax({
    url:"http://192.168.10.18/real/get_s_logs?sensor=LT",
    async:true,
    success:function(json){
        let data = JSON.parse(json);
        sensor_waters = data.data.avg;
        let firmOption = {
            tooltip: {
                trigger: 'axis'
            },
            title:{
                text:'水位监测',
                textStyle:{
                    color:'#00ffff',
                    fontWeight:'normal',
                    fontSize:16
                },
                padding:[15,10]
            },
            xAxis:{
                name:'时间',
                type:'time',
                boundarGap:false,
                axisLine:{
                    lineStyle:{
                        color:'#FFFFFF'
                    }
                },
                splitLine: {
                    show: false
                }
            },
            grid: {
                top: '20%',
                bottom: '15%',
                left: '15',
                containLabel: true
            },
            yAxis:{
                type:'value',
                boundarGap:[0,'100%'],
                axisLine:{
                    lineStyle:{
                        color:'#FFFFFF'
                    }
                },
                splitLine: {
                    show: false
                },
                min:0,
                max:100
            },
            dataZoom:[{
                start:95,
                end:100,
                backgroundColor:'rgb(ff,ff,ff)',
                height:'15',
                bottom:'3%'
            },{
                type:'slider',
                //borderColor:'rgb(30,255,05)',
                height:'15',
                bottom:'3%'
            }],
            series:[{
                name:'水位高度',
                type:'line',
                smooth:true,
                itemStyle:{
                    color:'#0e628e'
                },
                areaStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                        offset: 0,
                        color: '#1396bc'
                    }, {
                        offset: 1,
                        color: '#0e628e'
                    }])
                },
                data:sensor_waters,
                markLine:{
                    silent:true,
                    lineStyle:{
                        color:'red',
                        width:2
                    },
                    data:[{
                        yAxis:80
                    },{
                        yAxis:50
                    }]
                }
            }]
        };
        $.ajax({
            url:"http://192.168.10.18/real/get_sensor?sensor=LT",
            async:true,
            success:function(result){
                let water_obj = JSON.parse(result).data.s_avg;
                let firstr =  sensor_waters[0][0];
                let firsto =  water_obj[0];
                // console.dir(firstr);
                // console.dir(firsto);
                if(firsto !== firstr) {
                    sensor_waters.unshift(water_obj);
                    sensor_waters.pop();
                }
                contentFirm.setOption(firmOption);
            }
        });

        // 实时更新
        setInterval(function(){
            $.ajax({
                url:"http://192.168.10.18/real/get_sensor?sensor=LT",
                async:true,
                success:function(result){
                    let water_obj = JSON.parse(result).data.s_avg;
                    let firstr =  sensor_waters[0][0];
                    let firsto =  water_obj[0];
                    console.dir(firstr);
                    console.dir(firsto);
                    if(firsto !== firstr) {
                        sensor_waters.unshift(water_obj);
                        sensor_waters.pop();
                        // 水位更新
                        contentFirm.setOption({
                            series: [{
                                data: sensor_waters
                            }]
                        });
                    }
                }
            });
        },60000);
    },
    error: function(){
        console.error('ajax请求错误 ');
    }
});

let contentFirm = echarts.init(document.getElementById('contentFirm'));


// -----------------------------环境监测-----------------------------------
// 环境监测
//日期格式化
Date.prototype.format = function(fmt) {
    var o = {
        "M+" : this.getMonth()+1,                 //月份
        "d+" : this.getDate(),                    //日
        "h+" : this.getHours(),                   //小时
        "m+" : this.getMinutes(),                 //分
        "s+" : this.getSeconds(),                 //秒
        "q+" : Math.floor((this.getMonth()+3)/3), //季度
        "S"  : this.getMilliseconds()             //毫秒
    };
    if(/(y+)/.test(fmt)) {
        fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));
    }
    for(var k in o) {
        if(new RegExp("("+ k +")").test(fmt)){
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
        }
    }
    return fmt;
}
// 数据变更 数组
function objToArray(obj,shift) {
    console.dir('ooooooooooooooooooooooooooooooooooooooooooooooo');

    let firstr =  sensor_res.avg[0][0];
    let firsto =  obj.s_avg[0];

    if(firsto !== firstr){
        sensor_res.avg.unshift(obj.s_avg);
        sensor_res.max.unshift(obj.s_max);
        sensor_res.min.unshift(obj.s_min);
        // console.dir('nnnnnnnnnnnnnnnnnnnn');
        // console.dir(res.avg);
        if (shift) {
            sensor_res.avg.pop();
            sensor_res.max.pop();
            sensor_res.min.pop();
            // console.dir('sssssssssssssssssssssssssssssssss');
            // console.dir(res.avg);
            // console.dir('aaaaaaaaaaaaaaaaaavvvvvvvvvvvvvvvvvvvvvvggggggggggggggggg');
        }
    }
}
// 后续修改echarts option
function set_option(e_y_name,e_unit) {
    e_option = {

        legend: {
            textStyle:{
                color:'#00ffff'
            },
            data:['最高'+e_y_name,'平均'+e_y_name,'最低'+e_y_name]
        },
        series: [
            {
                name:'最高'+e_y_name,
                type:'line',
                smooth:true,
                data:sensor_res.max
            },
            {
                name:'平均'+e_y_name,
                type:'line',
                smooth:true,
                data:sensor_res.avg
            },
            {
                name:'最低'+e_y_name,
                type:'line',
                smooth:true,
                data:sensor_res.min
            }
        ]
    };
}
//获取初始历史数据 并定期更新
function get_sensor(sensor) {
    $.ajax({
        url:"http://192.168.10.18/real/get_s_logs?sensor="+sensor,
        async:true,
        success:function(json){
            //初始化  获取一天内温度 赋值res
            let data = JSON.parse(json);
            sensor_res = data.data;

            //初始化option 赋值 res.data
            e_i_option = {
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    top:0,
                    z:10,
                    textStyle:{
                        color:'#00ffff'
                    },
                    data:['最高'+e_y_name,'平均'+e_y_name,'最低'+e_y_name]
                },
                xAxis: {
                    name:'时间',
                    type: 'time',
                    axisLine:{
                        lineStyle:{
                            color:'#FFFFFF'
                        }
                    },
                    splitLine: {
                        show: false
                    },
                    axisLabel:{
                        formatter:function (value,index) {
                            var date = new Date(value);
                            return date.format("hh:mm");
                        }
                    }
                },
                grid: {
                    top: '20%',
                    bottom: '15%',
                    left: '15',
                    containLabel: true
                },
                yAxis: {
                    type: 'value',
                    axisLine:{
                        lineStyle:{
                            color:'#FFFFFF'
                        }
                    },
                    min:0,
                    splitLine: {
                        show: false
                    }
                },
                dataZoom:[{
                    start:96,
                    end:100,
                    backgroundColor:'rgb(ff,ff,ff)',
                    height:'15',
                    bottom:'3%'
                },{
                    type:'slider',
                    //borderColor:'rgb(30,255,05)',
                    height:'15',
                    bottom:'3%'
                }],
                series: [
                    {
                        name:'最高'+e_y_name,
                        type:'line',
                        smooth:true,
                        data:sensor_res.max
                    },
                    {
                        name:'平均'+e_y_name,
                        type:'line',
                        smooth:true,
                        data:sensor_res.avg
                    },
                    {
                        name:'最低'+e_y_name,
                        type:'line',
                        smooth:true,
                        data:sensor_res.min
                    }
                ]
            };
            // 初始化echarts图表
            borCont.setOption(e_i_option);
            $.ajax({
                url:"http://192.168.10.18/real/get_sensor?sensor="+sensor,
                async:true,
                success:function(result){
                    // 获取实时最新数据
                    let jsonObj = JSON.parse(result).data;
                    // console.dir('jsonObj');
                    // console.dir(sensor);
                    // console.dir(jsonObj);
                    //加入数组
                    objToArray(jsonObj,true);
                    set_option(e_y_name,e_unit);
                    borCont.setOption(e_option);
                }
            });
            if(typeof(Reallog) !== undefined){
                window.clearInterval(Reallog);
            }
            //定期更新echarts数据
            Reallog = setInterval(function(){
                $.ajax({
                    url:"http://192.168.10.18/real/get_sensor?sensor="+sensor,
                    async:true,
                    success:function(result){
                        // 获取实时最新数据
                        let jsonObj = JSON.parse(result).data;
                        console.dir('jsonObj');
                        // console.dir(sensor);
                        // console.dir(jsonObj);
                        //加入数组
                        objToArray(jsonObj,true);

                        // console.dir(sensor_res);
                        // console.dir(sensor_res);
                        // console.dir(sensor_res);
                        set_option(e_y_name,e_unit);
                        borCont.setOption(e_option);
                    }
                });

                // console.dir(res);
            },60000);
        },
        error: function(){
            console.error('ajax请求错误 ');
        }
    });
}

// 环境监测
//获取 echarts 对象
let borCont = echarts.init(document.getElementById('borCont'));
// 初始变量 及 echart初始参数设置
sensor_res = {};
var Reallog;
e_y_name = "温度";
e_unit = "温度(℃)";

// 页面初始化 获取T值 并初始化echarts
get_sensor("T");

// 下拉框判断
function select(e){
    if(e == 'T'){

        e_y_name = "温度";
        e_unit = "温度(℃)";

    }else if(e == "M"){
        e_y_name = "湿度";
        e_unit = "湿度(%)";

    }else if(e == "O2"){
        e_y_name = "氧气浓度";
        e_unit = "氧气(%VOL)";

    }else if(e == "CH4"){
        e_y_name = "甲烷浓度";
        e_unit = "甲烷(%LEL)";

    }else if(e == "H2S"){
        e_y_name = "硫化氢浓度";
        e_unit = "硫化氢(ppm)";
    }
    get_sensor(e);
}
// ------------------------------------设备使用率---------------------------
// 设备使用率

let num = [],streetMakeUp

let xmlHttp=new XMLHttpRequest();
xmlHttp.open("GET","http://192.168.10.21:8080/api/v1/GetSrc",false);
xmlHttp.onreadystatechange=function(){
    if(xmlHttp.readyState==4&&xmlHttp.status==200){
        let data=JSON.parse(xmlHttp.responseText);
        num = data.src
    }
}
xmlHttp.send(null)

function street(num1,num2){
    let aaa = [],bbb = []
    num.filter(data => {
        if(data.strToken > num1 && data.strToken <= num2) {
            aaa.push(data)
        }
    })
    if(aaa.length == 0){
        return 0;
    }else{
        aaa.filter((data) => {
            if(data.bOnline){
                bbb.push(data)
            }
        })
        return parseFloat(((bbb.length)/(aaa.length)*100).toFixed(2)) /*+ "%"*/
    }
}

streetMakeUp = [{name:'YBY',value:street(50,550)},
    {name:'LX',value:street(550,1050)},
    {name:'XC',value:street(1050,1550)},
    {name:'TH',value:street(1550,2050)},
    {name:'YX',value:street(2050,2550)},
    {name:'YN',value:street(2550,3050)},
    {name:'WT',value:street(3050,3550)},
    {name:'TN',value:street(3550,4050)},
    {name:'SP',value:street(4050,4550)},
    {name:'HY',value:street(4550,5050)},
    {name:'AT',value:street(5050,6000)}
]
eqptvalue = [
    street(50,550),
    street(550,1050),
    street(1050,1550),
    street(1550,2050),
    street(2050,2550),
    street(2550,3050),
    street(3050,3550),
    street(3550,4050),
    street(4050,4550),
    street(4550,5050),
    street(5050,6000)
]



let botUse = echarts.init(document.getElementById('botUse'));
let useOption = {
    title:{
        text:'设备使用率',
        textStyle:{
            color:'#00ffff',
            fontWeight:'normal',
            fontSize:16
        },
        padding:[15,10]
    },
    tooltip: {},
    // legend: {
    //     data: ['使用风机（数量）', '使用防火门（数量）','使用水泵（数量）'],
    //     textStyle:{
    //         color:'#ffffff'
    //     }
    // },
    radar: {
        name: {
            textStyle: {
                color: '#72acd1',
                backgroundColor: ''
            }
        },
        splitArea:{
            areaStyle:{
                color:['rgba(114, 172, 209, 0.2)',
                    'rgba(114, 172, 209, 0.4)', 'rgba(114, 172, 209, 0.6)',
                    'rgba(114, 172, 209, 0.8)', 'rgba(114, 172, 209, 1)']
            }
        },
        radius: '65%',
        indicator: [
            { name: '园博园大街', max:100},
            { name: '隆兴路', max:100},
            { name: '新城大街', max:100},
            { name: '太行大街', max:100},
            { name: '迎旭路', max:100},
            { name: '永宁路', max:100},
            { name: '尉佗街', max:100},
            { name: '天宁路', max:100},
            { name: '顺平大街', max:100},
            { name: '华阳路', max:100},
            { name: '奥体街', max:100}
        ],
    },
    series: [{
        name: '设备使用量',
        type: 'radar',
        data : [{
                value :eqptvalue,
                name : '使用智能照明（%）',
                itemStyle:{
                    color:'yellow'
                }
            }
        ]
    }]
};
botUse.setOption(useOption);



// --------------------------------------机器人----------------------
let robotClass = document.getElementsByClassName('robotClass');
for(var i =0 ;i<robotClass.length ; i++ ){
    if(robotClass[i].innerHTML == '已完成'){
        robotClass[i].style.color = '#32a5cf'
        robotClass[i].style.border = '1px solid #32a5cf'
    }else if(robotClass[i].innerHTML == '进行中'){
        robotClass[i].style.border = '1px solid #ca8622'
        robotClass[i].style.color = '#ca8622'
    }else if(robotClass[i].innerHTML == '待执行'){
        robotClass[i].style.border = '1px solid  #c23531'
        robotClass[i].style.color = '#c23531'
    }
}

// --------------------------巡查巡检--------------------------
// 巡查巡检
let borPatrol = document.getElementsByClassName('borPatrol');
for(let t = 0 ; t<borPatrol.length ; t++){
    borPatrol[t].index = t;
    borPatrol[t].onclick = function(){
        for(var s = 0; s<3;s++){
            this.parentNode.children[s].classList.remove('borUlClass');
        }
        this.classList.add('borUlClass');
    }
}


// --------------------------机器人显示视频页面----------------------
let bb = true;
function aaa(){
    let robotHide = document.getElementsByClassName('robotHide')[0];
    if(bb == true){
        robotHide.style.display = '';
        bb = false;
    }else if(bb == false){
        robotHide.style.display = 'none';
        bb = true
    }
}

// -----------------------列表样式--------------------------
// 时间
let callTime = document.getElementsByClassName('callTime');
// let callGrade = document.getElementsByClassName('callGrade');
// for(var a = 0 ;a<callGrade.length;a++){
//     // callTime[a].innerHTML = time.year()+' '+(a+2)+':'+(a+5);
//     if(callGrade[a].innerHTML == '严重'){
//         callGrade[a].style.color = '#FFB800'
//     }else if(callGrade[a].innerHTML == '紧急'){
//         callGrade[a].style.color = '#FF5722'
//     }else if(callGrade[a].innerHTML == '普通'){
//         callGrade[a].style.color = '#00F000'
//     }else if(callGrade[a].innerHTML == '设备'){
//         callGrade[a].style.color = '#00FFFF'
//     }
// }
// let getYear = date.getFullYear()+'年'+(date.getMonth()+1)+'月'+ (d0.getDate())+'日';
// let getYear1 = date.getFullYear()+'年'+(date.getMonth()+1)+'月'+ (d1.getDate())+'日';
// let getYear2 = date.getFullYear()+'年'+(date.getMonth()+1)+'月'+ (d2.getDate())+'日';
// let getYear3 = date.getFullYear()+'年'+(date.getMonth()+1)+'月'+ (d3.getDate())+'日';
// callTime[0].innerHTML = getYear+' '+'5:30';
// callTime[1].innerHTML = getYear+' '+'5:30';
// callTime[2].innerHTML = getYear+' '+'5:33';
// callTime[3].innerHTML = getYear1+' '+'12:30';
// callTime[4].innerHTML = getYear1+' '+'12:30';
// callTime[5].innerHTML = getYear1+' '+'13:05';
// callTime[6].innerHTML = getYear2+' '+'13:32';
// callTime[7].innerHTML = getYear2+' '+'13:32';
// callTime[8].innerHTML = getYear2+' '+'14:14';
// callTime[9].innerHTML = getYear3+' '+'15:21';
// callTime[10].innerHTML = getYear3+' '+'15:21';
// callTime[11].innerHTML = getYear3+' '+'16:11';
// callTime[12].innerHTML = getYear3+' '+'19:37';

$('.callTime').each(function(){

    // console.dir($(this).text());

    $(this).text(DateToTime($(this).text()));
});

// 入廊企业
let firmConMain = document.getElementsByClassName('firmConMain');
let firmConTop = document.getElementById('firmConTop');
for(let h = 0; h<firmConMain.length; h++){
    firmConMain[h].index = h;
    firmConMain[h].onmousemove = function(e){
        switch(this.index){
            case 0:
                firmConTop.style.left = (e.clientX+10)+'px';
                firmConTop.style.top = (e.clientY+10)+'px';
                firmConTop.style.display = 'inline';
                firmConTop.innerHTML = '<div>通信光纤</div>'+
                    '<div>全长：5.3km</div>'
                break;
            case 1:
                firmConTop.style.left = (e.clientX+10)+'px';
                firmConTop.style.top = (e.clientY+10)+'px';
                firmConTop.style.display = 'inline';
                firmConTop.innerHTML = '<div>热力管</div>'+
                    '<div>全长：16km</div>'
                break;
            case 2:
                firmConTop.style.left = (e.clientX+10)+'px';
                firmConTop.style.top = (e.clientY+10)+'px';
                firmConTop.style.display = 'inline';
                firmConTop.innerHTML = '<div>供电线缆</div>'+
                    '<div>110KV:5.3km</div>'+
                    '<div>35KV:3.5km</div>'+
                    '<div>10KV:16km</div>'
                break;
            case 3:
                firmConTop.style.left = (e.clientX+10)+'px';
                firmConTop.style.top = (e.clientY+10)+'px';
                firmConTop.style.display = 'inline';
                firmConTop.innerHTML = '<div>供水管道</div>'+
                    '<div>中水：16km</div>'+
                    '<div>供水：16km</div>'
                break;
        }
    }
    firmConMain[h].onmouseout = function(){
        firmConTop.style.display = 'none'
    }

}

function DateToTime(unixTime,type="Y-M-D H:i"){
    var date = new Date(unixTime * 1000);//时间戳为10位需*1000，时间戳为13位的话不需乘1000
    var datetime = "";
    datetime += date.getFullYear() + type.substring(1,2);
    datetime += (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + type.substring(3,4);
    datetime += (date.getDate() < 10 ? '0'+(date.getDate()) : date.getDate());
    if (type.substring(5,6)) {
        if (type.substring(5,6).charCodeAt() > 255) {
            datetime += type.substring(5,6);
            if (type.substring(7,8)) {
                datetime += " " + (date.getHours() < 10 ? '0'+(date.getHours()) : date.getHours());
                if (type.substring(9,10)) {
                    datetime += type.substring(8,9) + (date.getMinutes() < 10 ? '0'+(date.getMinutes()) : date.getMinutes());
                    if (type.substring(11,12)) {
                        datetime += type.substring(10,11) + (date.getSeconds() < 10 ? '0'+(date.getSeconds()) : date.getSeconds());
                    };
                };
            };
        }else{
            datetime += " " + (date.getHours() < 10 ? '0'+(date.getHours()) : date.getHours());
            if (type.substring(8,9)) {
                datetime += type.substring(7,8) + (date.getMinutes() < 10 ? '0'+(date.getMinutes()) : date.getMinutes());
                if (type.substring(10,11)) {
                    datetime += type.substring(9,10) + (date.getSeconds() < 10 ? '0'+(date.getSeconds()) : date.getSeconds());
                };
            };
        };
    };
    return datetime;
}






















