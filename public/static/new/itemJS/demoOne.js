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
    //     data: [ '在线IPC（%）'],
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
        radius: '70%',
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
        center:['50%','55%']
    },
    series: [{
        name: '设备使用率',
        type: 'radar',
        data : [{
                value : eqptvalue,
                name : '摄像机（%）',
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
            num += parseInt((Math.random()-0.49)*5);
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

//获取历史数据
let eleobj = [];
$.ajax({
    url:"http://192.168.10.18/real/get_j_log",
    async:true,
    success:function(result){
        let eleobj = JSON.parse(result).data;
        // console.dir(firstr);
        console.dir('eeeeeeeeeeeeeeeeeeeeeeeeeeeee');
        console.dir(eleobj);
        console.dir(eleobj);
        console.dir(eleobj);
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
                min:function (value) {
                    return value.min - 200;
                }
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
                data:eleobj
            }]
        }
        contentEle.setOption(eleOption);
        // 实时更新
        setInterval(function(){
            $.ajax({
                url:"http://192.168.10.18/real/get_j_real",
                async:true,
                success:function(result){
                    let ele_real = JSON.parse(result).data;
                    let firstr =  eleobj[0][0];
                    let firsto =  ele_real[0][0];
                    console.dir('fffffffffffffffffffff');
                    console.dir(firstr);
                    console.dir(firsto);
                    if(firsto !== firstr) {
                        eleobj.unshift(ele_real[0]);
                        eleobj.pop();
                        // 水位更新
                        contentEle.setOption({
                            series: [{
                                data: eleobj
                            }]
                        });
                    }
                    console.dir(eleobj);
                }
            });
        },60000);
    }
});



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
                fontSize:8
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





