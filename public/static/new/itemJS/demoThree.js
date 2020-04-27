
    // 数据


let date = new Date();
var preDate = new Date(date.getTime() - 24*60*60*1000);
var nextDate = new Date(date.getTime() + 24*60*60*1000);
    let time = {
        year1 : function(){
            return preDate.getFullYear()+'-'+(preDate.getMonth()+1)+'-'+ preDate.getDate()
        },
        year2 : function(){
            return date.getFullYear()+'-'+(date.getMonth()+1)+'-'+ date.getDate()
        },
        year3 : function(){
            return nextDate.getFullYear()+'-'+(nextDate.getMonth()+1)+'-'+ nextDate.getDate()
        },
    };

    let date1 = document.getElementById('date1');
    let date2 = document.getElementById('date2');
    let date3 = document.getElementById('date3');
    date1.innerHTML = time.year1();
    date2.innerHTML = time.year2();
    date3.innerHTML = time.year3();

let data = {
    getYear:date.getFullYear(),//年份
    getMonth:date.getMonth()-1,//月份
    getDate:date.getDate(),//日
    getTime:date.getHours(),//当前时间
    weeHours:date.getMinutes(),//当前分钟
    timeLcale:function(){
        return this.getYear+'/'+this.getMonth+'/'+this.getDate  // 当前日期
    },
    // 遍历次数
    number:function(){
        return (this.getTime*60+this.weeHours)*4
    },
    arr:[],//水位
    // arr1:[],//环境
    water: function(aaa,bbb) {
        let hour = 0;
        let minu = 0;
        let second = 0;
        let num = 0;
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
    },
    environ:function(ccc,ddd){//环境
        let num = 0;// 随机数
        let arr1 = [];  // 返回数组
	/*
	for(var y = 0; y < 31 ;y++){
	    num = ((Math.random()-0.5)*ccc+ddd).toFixed(2);
	    if( [y + this.getDate] < 32){
		arr1.push([this.getYear+'/'+[this.getMonth-1] + '/'+ [y+this.getDate],num])
	    }else{
		
		arr1.push([this.getYear+'/'+ this.getMonth + '/'+ [y+this.getDate-30],num])
	    }

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
data.water(6,60);//水位
    // console.dir(data.arr.slice(0,data.number()));
    // console.dir(data.arr.slice(0,data.number()));
    // 水位监测
    //获取历史数据
    let sensor_waters = [];
    $.ajax({
        url:"http://192.168.10.18/real/get_s_logs?sensor=LT",
        async:false,
        success:function(json){
            let data = JSON.parse(json);
            sensor_waters = data.data.avg;
            let firmOption = {
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
                    },
                    axisLabel:{
                        formatter:function (value,index) {
                            var date = new Date(value);
                            return date.format("hh:mm");
                        }
                    }
                },
                grid: {
                    top: '15%',
                    bottom: '9%',
                    left: '15',
                    right: '7%',
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
            yAxis: {
                name:e_unit,
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
                        top: '10%',
                        bottom: '15%',
                        left: '2%',
                        right: '7%',
                        containLabel: true
                    },
                    yAxis: {
                        name:e_unit,
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
                        bottom:'10%'
                    },{
                        type:'slider',
                        //borderColor:'rgb(30,255,05)',
                        height:'15',
                        bottom:'10%'
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
    res = {};

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

// -----------------------------------------------------------
let robotClass = document.getElementsByClassName('robotClass');
for(var i =0 ;i<robotClass.length ; i++ ){
    if(robotClass[i].innerHTML == '已完成'){
        //robotClass[i].style.color = '#32a5cf'
        robotClass[i].style.backgroundColor = '#32a5cf'
    }else if(robotClass[i].innerHTML == '进行中'){
        //robotClass[i].style.border = '1px solid #ca8622'
        robotClass[i].style.backgroundColor = '#ca8622'
    }else if(robotClass[i].innerHTML == '待执行'){
        //robotClass[i].style.border = '1px solid  #c23531'
        robotClass[i].style.backgroundColor = '#c23531'
    }
}


// 巡查巡检
//     let borPatrol = document.getElementsByClassName('borPatrol');
//     for(let t = 0 ; t<borPatrol.length ; t++){
//         borPatrol[t].index = t;
//         borPatrol[t].onclick = function(){
//             for(var s = 0; s<3;s++){
//                 this.parentNode.children[s].classList.remove('borUlClass');
//             }
//             this.classList.add('borUlClass');
//         }
//     }
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



















