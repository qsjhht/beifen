
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
    let waters = [];
    $.ajax({
        url:"http://192.168.10.18/real/get_s_logs?sensor=LT",
        async:false,
        success:function(json){
            let data = JSON.parse(json);
            waters = data.data.avg;
        },
        error: function(){
            console.error('ajax请求错误 ');
        }
    });
    console.dir(waters);
    console.dir(waters);
    console.dir(waters);
let contentFirm = echarts.init(document.getElementById('contentFirm'));
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
        }
    },
    yAxis:{
        name:'水位（cm）',
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
        data:waters,
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
contentFirm.setOption(firmOption);

    // 实时更新
    setInterval(function(){
        $.ajax({
            url:"http://192.168.10.18/real/get_sensor?sensor=LT",
            async:false,
            success:function(result){
                let water_obj = JSON.parse(result).data.s_avg;
                waters.unshift(water_obj);
                waters.pop();
            }
        });
        // 水仓
        contentFirm.setOption({
            series: [{
                data: waters
            }]
        });
        console.dir(waters);
    },60000);
// 环境监测
let borCont = echarts.init(document.getElementById('borCont'));
// 温度
//     console.dir(data.environ(1,21));
//     console.dir(data.environ(1,19));
//     console.dir(data.environ(1,17));
    res = {};
    function get_sensor(sensor) {
        $.ajax({
            url:"http://192.168.10.18/real/get_s_logs?sensor="+sensor,
            async:false,
            success:function(json){
                let data = JSON.parse(json);
                res = data.data;
            },
            error: function(){
                console.error('ajax请求错误 ');
            }
        });
    }

    function objToArray(obj,shift) {

        // console.dir('ooooooooooooooooooooooooooooooooooooooooooooooo');
        // console.dir(res.avg);

        res.avg.unshift(obj.s_avg);
        res.max.unshift(obj.s_max);
        res.min.unshift(obj.s_min);
        // console.dir('nnnnnnnnnnnnnnnnnnnn');
        // console.dir(res.avg);
        if (shift) {
            res.avg.pop();
            res.max.pop();
            res.min.pop();
            // console.dir('sssssssssssssssssssssssssssssssss');
            // console.dir(res.avg);
            // console.dir('aaaaaaaaaaaaaaaaaavvvvvvvvvvvvvvvvvvvvvvggggggggggggggggg');
        }
    }

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
                data:res.max
            },
            {
                name:'平均'+e_y_name,
                type:'line',
                smooth:true,
                data:res.avg
            },
            {
                name:'最低'+e_y_name,
                type:'line',
                smooth:true,
                data:res.min
            }
        ]
    };
}
//初始化  获取一天内温度 赋值res
    get_sensor("T");
    e_y_name = "温度";
    e_unit = "温度(℃)";
    //option 赋值
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
            }
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
            start:98,
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
                data:res.max
            },
            {
                name:'平均'+e_y_name,
                type:'line',
                smooth:true,
                data:res.avg
            },
            {
                name:'最低'+e_y_name,
                type:'line',
                smooth:true,
                data:res.min
            }
        ]
    };
    //初始化 echart
    borCont.setOption(e_i_option);
    //定时更新 echart 数据
    Reallog = setInterval(function(){
        $.ajax({
            url:"http://192.168.10.18/real/get_sensor?sensor=T",
            async:false,
            success:function(result){
                let jsonObj = JSON.parse(result).data;
                //更新 res 数组
                objToArray(jsonObj,true);
            }
        });
        set_option(e_y_name,e_unit);
        borCont.setOption(e_option);
    },60000);


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
    set_option(e_y_name,e_unit);
    borCont.setOption(e_option);
    window.clearInterval(Reallog);
    //定时更新 echart 数据
    Reallog = setInterval(function(){
        $.ajax({
            url:"http://192.168.10.18/real/get_sensor?sensor="+e,
            async:false,
            success:function(result){
                let jsonObj = JSON.parse(result).data;
                objToArray(jsonObj,true);
            }
        });
        set_option(e_y_name,e_unit);
        borCont.setOption(e_option);
        // console.dir(res);
    },60000);
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



















