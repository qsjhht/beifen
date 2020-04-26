// 管廊信息
let date = new Date();

var d0 = new Date(date.getTime() - 24*60*60*1000*2);
var d1 = new Date(date.getTime() - 24*60*60*1000*3);
var d2 = new Date(date.getTime() - 24*60*60*1000*5);
var d3 = new Date(date.getTime() - 24*60*60*1000*7);

let time = {
    year : function(){
        return date.getFullYear()+'年'+(date.getMonth()+1)+'月'+ date.getDate()+'日'
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
        return '<img src="../../../static/new/image/user-fill.png" width:="30" height="26" style="position:relative;top:-1px"/>在岗15人'
    }
};
let infoDate = document.getElementById('infoDate');
let content = time.year()+'&nbsp;&nbsp;'+time.dayDate()+'&nbsp;&nbsp;|&nbsp;&nbsp;'+time.crew();
infoDate.innerHTML = content;
// 值班人员设置
/*let duty = {
    crew:'李勇',
    strip: '<div class="progress">'+
            '<div class="progressFro"></div>'+
            '<div class="progressBack"></div>'+
            '</div>'
    ,
    sum:function(){
        return '值班：'+this.crew+'&nbsp;&nbsp;8:30'+this.strip+'18:30'
    }
}
let infoDty = document.getElementById('infoDty');
infoDty.innerHTML = duty.sum();*/
// 设备使用率
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
    legend: {
        data: [ '使用智能照明（%）'],
        textStyle:{
            color:'#ffffff'
        }
    },
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
        radius: '70%',
        indicator: [
            { name: '顺平大街', max:50},
            { name: '园博园大街', max:50},
            { name: '安济路', max:50},
            { name: '隆兴路', max:50},
            { name: '华阳路', max:50},
            { name: '尉佗路', max:50},
            { name: '迎旭路', max:50},
            { name: '奥体街', max:50},
            { name: '太行大街', max:50},
            { name: '新城大道', max:50}
        ],
        center:['50%','55%']
    },
    series: [{
        name: '设备使用量',
        type: 'radar',
        data : [{
                value : [11.5, 13, 8, 0, 14.3, 7.8, 10.7,13,16  , 14],
                name : '使用智能照明（%）',
                itemStyle:{
                    color:'yellow'
                }
            }
        ]
    }]
};
botUse.setOption(useOption);
// 动力配电
let contentEle = echarts.init(document.getElementById('contentEle'));
let arr = [];
let data = {
    timeLcale:date.getFullYear()+'/'+(date.getMonth()+1)+'/'+date.getDate(),
    getTime:date.getHours(),
    weeHours:date.getMinutes(),
    number:function(){
        return (this.getTime*60+this.weeHours)*4
    },
    // 水位
    water: function(){
        let hour = 0;
        let minu = 0;
        let second = 0;
        let num = 0;
        for(var i = 0; i < 5760; i++){
            num += parseInt((Math.random()-0.45)*5);
            hour = Math.floor(i/240);
            if(hour <10){
                hour = '0' +hour
            }
            minu = Math.floor(i%240/4);
            if(minu < 10){
                minu = '0'+ minu
            }
            second = i%4*15;
            if(second == 0){
                second = '00'
            }
            arr.push([this.timeLcale+' '+hour+':'+minu+':'+second,num+30])
        }
    }
};
data.water();
console.dir(arr.slice(0,data.number()));
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
 start:95,end:100,backgroundColor:'rgb(ff,ff,ff)',height:'15',bottom:'3%'  },{
        type:'slider',height:'15',bottom:'3%'
    }],
    series: [{
        name: '用电量(kw/h)',
        type: 'line',
        showSymbol: false,
        hoverAnimation: false,
        data:arr.slice(0,data.number())
    }]

};

contentEle.setOption(eleOption);
setInterval(function(){
    arr.shift();
    contentEle.setOption({
        series: [{
            data:arr.slice(0,data.number())
        }]
    })
},15000);



let alarmnum = $('#alarmnum').val();
alarmnum = JSON.parse(alarmnum);


console.dir(alarmnum);
console.dir(typeof (alarmnum));

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
        // data: ["严重","紧急","普通","设备"],
        textStyle:{
            color:'#0ff',
        },
        right:'-1%',
        itemWidth :20,
        itemHeight:11,
    },

    series : [
        {
            name: '报警类型',
            type: 'pie',
            radius: ['40%', '70%'],
            center: ['50%', '56%'],
            // roseType:true,
            data: alarmnum,
            label:{            //饼图图形上的文本标签
                show : true,
                // formatter: "{b} : {c} ({d}%)"
                formatter: "{b} :  {c}  \n ({d}%)",
                fontSize:15
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
        },
    },
    grid: {
        bottom: 25,
        right: 10
    },
    yAxis:{
        name:'数量',
        type:'value',
        axisLine:{
            lineStyle:{
                color:'#FFFFFF'
            }
        },
        splitLine: {
            show: false
        },
        min:0
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
/*

// 报警设置表格
// 时间
let callTime = document.getElementsByClassName('callTime');
let callGrade = document.getElementsByClassName('callGrade');
for(var a = 0 ;a<callGrade.length;a++){
    if(callGrade[a].innerHTML == '严重'){
        callGrade[a].style.color = '#c23531'
    }else if(callGrade[a].innerHTML == '紧急'){
        callGrade[a].style.color = '#ca8622'
    }else if(callGrade[a].innerHTML == '普通'){
        callGrade[a].style.color = 'yellow'
    }else if(callGrade[a].innerHTML == '设备'){
        callGrade[a].style.color = '#32a5cf'
    }
}
let getYear = date.getFullYear()+'年'+(date.getMonth()+1)+'月'+ (d0.getDate())+'日';
let getYear1 = date.getFullYear()+'年'+(date.getMonth()+1)+'月'+ (d1.getDate())+'日';
let getYear2 = date.getFullYear()+'年'+(date.getMonth()+1)+'月'+ (d2.getDate())+'日';
let getYear3 = date.getFullYear()+'年'+(date.getMonth()+1)+'月'+ (d3.getDate())+'日';
callTime[0].innerHTML = getYear+' '+'5:30';
callTime[1].innerHTML = getYear+' '+'5:30';
callTime[2].innerHTML = getYear+' '+'5:33';
callTime[3].innerHTML = getYear1+' '+'12:30';
callTime[4].innerHTML = getYear1+' '+'12:30';
callTime[5].innerHTML = getYear1+' '+'13:05';
callTime[6].innerHTML = getYear2+' '+'13:32';
callTime[7].innerHTML = getYear2+' '+'13:32';
callTime[8].innerHTML = getYear2+' '+'14:14';
callTime[9].innerHTML = getYear3+' '+'15:21';
callTime[10].innerHTML = getYear3+' '+'15:21';
callTime[11].innerHTML = getYear3+' '+'16:11';
callTime[12].innerHTML = getYear3+' '+'19:37';


*/





