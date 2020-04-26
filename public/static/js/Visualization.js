/**
 * Created by Administrator on 2018/12/25.
 */
// F

// 入廊企业
var dv1 = echarts.init(document.getElementById("dv1"));
var rulang = {
    tooltip: {
        trigger: 'item',
        formatter: "{a} <br/>{b}: {c} ({d}%)"
    },
    legend: {
        orient: 'vertical',
        x: 'left',
        data:['中国移动','中国联通','中国电信','电力有限公司'],
        textStyle: {
            color: 'rgba(255, 255, 255, 0.8)'
        }
    },
    series: [
        {
            name:'当前各公司所占比例',
            type:'pie',
            radius: ['50%', '70%'],
            avoidLabelOverlap: true,
            label: {
                normal: {
                    show: false,
                    position: 'center'
                },
                emphasis: {
                    show: true,
                    textStyle: {
                        fontSize: '25',
                        fontWeight: 'bold'
                    }
                }
            },
            labelLine: {
                normal: {
                    show: false
                }
            },
            data:[
                {value:300, name:'中国移动'},
                {value:300, name:'中国联通'},
                {value:300, name:'中国电信'},
                {value:200, name:'电力有限公司'}
            ]
        }
    ]
};
dv1.setOption(rulang);
//报警类型
var dv2 = echarts.init(document.getElementById("dv2"));
var leixing = {
    tooltip : {
        trigger: 'item',
        formatter: "{a} <br/>{b} : {c} ({d}%)"
    },
    visualMap: {
        show: false,
        min: 0,
        max: 600,
        inRange: {
            colorLightness: [0, 4]
        }
    },
    series : [
        {
            name:'近一个月报错分析',
            type:'pie',
            radius : '55%',
            center: ['50%', '50%'],
            data:[
                {value:104, name:'管廊沉降'},
                {value:78, name:'水位异常'},
                {value:83, name:'温湿度异常'},
                {value:55, name:'气体警报'}

            ].sort(function (a, b) { return a.value - b.value; }),
            roseType: 'radius',
            label: {
                normal: {
                    textStyle: {
                        color: 'rgba(255, 255, 255, 0.8)'
                    }
                }

            },
            labelLine: {
                normal: {
                    lineStyle: {
                        color: 'rgba(255, 255, 255, 0.8)'
                    },
                    smooth: 0.2,
                    length: 10,
                    length2: 20
                }
            },
            itemStyle: {
//                        color:'#00ffff',
                normal: {
//                            color: '#00ffff',
                    shadowBlur: 200

                }
            },
            animationType: 'scale',
            animationEasing: 'elasticOut',
            animationDelay: function (idx) {
                return Math.random() * 200;
            }
        }
    ]
};
dv2.setOption(leixing);

//近六个月
var dv3 = echarts.init(document.getElementById("dv3"));
var jinliu = {
    tooltip: {
        trigger: 'item',
        formatter: '{a} <br/>{b} : {c}'
    },
    legend: {
        left: 'left',
        data: ['管廊沉降', '水位异常','温湿度超标','气体警报'],
        textStyle: {
            color: 'rgba(255, 255, 255, 0.8)'
        }
    },
    xAxis: {
        type: 'category',
        name: 'x',
        splitLine: {show: false},
        data: ['周一', '周二', '周三', '周四', '周五', '周六', '周七'],
        axisLine:{
            lineStyle:{
                color:'#e4e4e4'
            }
        }
    },
    grid: {
        left: '3%',
        right: '4%',
        bottom: '3%',
        containLabel: true
    },
    yAxis: {
        axisLine:{
            lineStyle:{
                color:'#e4e4e4'
            }
        }
    },
    series: [
        {
            name: '管廊沉降',
            type: 'line',
            data: [10,22, 14, 20, 11, 10, 21]
        },
        {
            name: '水位异常',
            type: 'line',
            data: [7, 14, 23, 20, 3, 16, 20]
        },
        {
            name: '温湿度超标',
            type: 'line',
            data: [8,3, 17, 26, 24,12, 26]
        },
        {
            name: '气体警报',
            type: 'line',
            data: [26, 19,17, 11, 6, 21, 13]
        }
    ]
};
dv3.setOption(jinliu);

//巡查进度
// var dv5 = echarts.init(document.getElementById("dv5"));
// var shebei = {
//     color:['#0099CC'],
//     tooltip: {
//         trigger: 'axis',
//         axisPointer: {
//             type: 'shadow'
//         }
//     },
//     grid: {
//         left: '3%',
//         right: '4%',
//         bottom: '3%',
//         containLabel: true
//     },
//     xAxis: {
//         type: 'log',
//         boundaryGap: [0, 0.01],
//         axisLine:{
//             lineStyle:{
//                 color:'#e4e4e4'
//             }
//         }
//     },
//     yAxis: {
//         type: 'category',
//         data: ['一号线','二号线','三号线','四号线','五号线'],
//         axisLine:{
//             lineStyle:{
//                 color:'#e4e4e4',
//                 backgroundColor:'#0ff'
//             }
//         }
//     },
//     series: [
//         {
//             name: '当前巡检进度（%）',
//             type: 'bar',
//             data: [20,70, 50, 99,60],
//             emphasis:{
//                 label:{
//                     backgroundColor:'yellow'
//                 }
//             }
//
//         }
//     ]
// };
// dv5.setOption(shebei);

//巡检线路数据
var dv6 = echarts.init(document.getElementById("dv6"));
var xunjian = {
    angleAxis: {

        type: 'category',
        data: ['一月', '二月', '三月', '四月', '五月', '六月'],
        z: 10,
        axisLine:{
            lineStyle:{
                color:'#e4e4e4'
            }
        }
    },
    radiusAxis: {
        axisLine:{
            lineStyle:{
                color:'#e4e4e4'
            }
        }
    },
    polar: {
    },
    series: [{
        type: 'bar',
        data: [1, 2, 3, 4, 3, 5],
        coordinateSystem: 'polar',
        name: '丢失',
        stack: 'a'
    }, {
        type: 'bar',
        data: [2, 4, 6, 1, 3, 2],
        coordinateSystem: 'polar',
        name: '损坏',
        stack: 'a'
    }],
    legend: {
        show: true,
        data: ['丢失', '损坏'],
        textStyle: {
            color: 'rgba(255, 255, 255, 0.8)'
        }
    }
};
dv6.setOption(xunjian);

//  维修设备类别
var dv7 = echarts.init(document.getElementById("dv7"));
var weixiu =  {
    angleAxis: {
        axisLine:{
            lineStyle:{
                color:'#e4e4e4'
            }
        }
    },
    radiusAxis: {
        type: 'category',
        data: ['周一', '周二', '周三', '周四'],
        color:'#ffffff',
        z: 10,
        axisLine:{
            lineStyle:{
                color:'#e4e4e4'
            }
        }
    },
    polar: {
    },
    series: [{
        type: 'bar',
        data: [1, 3, 2, 3],
        coordinateSystem: 'polar',
        name: '维修',
        stack: 'a'
    }, {
        type: 'bar',
        data: [2, 4, 6, 8],
        coordinateSystem: 'polar',
        name: '维护',
        stack: 'a'
    }, {
        type: 'bar',
        data: [1, 2, 3, 4],
        coordinateSystem: 'polar',
        name: '更换',
        stack: 'a'
    }],

    legend: {
        show: true,
        data: ['维修', '维护', '更换'],
        textStyle: {
            color: 'rgba(255, 255, 255, 0.8)'
        }
    }
};
dv7.setOption(weixiu);

//环境数据类别
var dv9 = echarts.init(document.getElementById("dv9"));
var baojing ={

    tooltip: {
        trigger: 'axis'
    },
    legend: {
        data:['最高气温','最低气温'],
        textStyle:{
            color:'#e4e4e4'
        }
    },

    xAxis:  {
        type: 'category',
        boundaryGap: false,
        data: ['周一','周二','周三','周四','周五','周六','周日'],
        axisLine:{
            lineStyle:{
                color:'#e4e4e4'
            }
        }
    },
    yAxis: {
        type: 'value',
        axisLabel: {
            formatter: '{value} °C'
        },
        axisLine:{
            lineStyle:{
                color:'#e4e4e4'
            }
        }
    },
    series: [
        {
            name:'最高气温',
            type:'line',
            data:[28, 26, 27, 23, 28, 25, 26],
            markPoint: {
                data: [
                    {type: 'max', name: '最大值'},
                    {type: 'min', name: '最小值'}
                ]
            },
            markLine: {
                data: [
                    {type: 'average', name: '平均值'}
                ]
            }
        },
        {
            name:'最低气温',
            type:'line',
            data:[15, 13, 14, 16, 17, 15, 16],
            markPoint: {
                data: [
                    {name: '周最低', value: -2, xAxis: 1, yAxis: -1.5}
                ]
            },
            markLine: {
                data: [
                    {type: 'average', name: '平均值'},
                    [{
                        symbol: 'none',
                        x: '90%',
                        yAxis: 'max'
                    }, {
                        symbol: 'circle',
                        label: {
                            normal: {
                                position: 'start',
                                formatter: '最大值'
                            }
                        },
                        type: 'max',
                        name: '最高点'
                    }]
                ]
            }
        }
    ]
};
dv9.setOption(baojing);

//水仓流量监测
var dv11 = echarts.init(document.getElementById("dv11"));
var shuicang = {
    tooltip : {
        trigger: 'axis',
        axisPointer: {
            type: 'cross',
            label: {
                backgroundColor: '#6a7985'
            }
        }
    },
    legend: {
        data:['给水','中水','供热'],
        textStyle:{
            color:'#e4e4e4'
        }
    },
    grid: {
        left: '3%',
        right: '4%',
        bottom: '3%',
        containLabel: true
    },
    xAxis : [
        {
            type : 'category',
            boundaryGap : false,
            data : ['一月','二月','三月','四月','五月','六月','七月'],
            axisLine:{
                lineStyle:{
                    color:'#e4e4e4'
                }
            }
        }
    ],
    yAxis : [
        {
            type : 'value',
            axisLine:{
                lineStyle:{
                    color:'#e4e4e4'
                }
            }
        }
    ],
    series : [
        {
            name:'给水',
            type:'line',
            stack: '总量',
            areaStyle: {normal: {}},
            data:[120, 132, 101, 134, 90, 230, 210]
        },
        {
            name:'中水',
            type:'line',
            stack: '总量',
            areaStyle: {normal: {}},
            data:[220, 182, 191, 234, 290, 330, 310]
        },
        {
            name:'供热',
            type:'line',
            stack: '总量',
            areaStyle: {normal: {}},
            data:[150, 232, 201, 154, 190, 330, 410]
        }
    ]
};
dv11.setOption(shuicang);
//当前电压
var dv10_1 = echarts.init(document.getElementById("dv10_1"));
var dianya ={
    tooltip : {
        formatter: "{a} <br/>{b} : {c}kV"
    },
    series: [
        {
            name: 'kV',
            // 格式
            type: 'gauge',
            // 大小
            radius:'130%',
            // 最小数
            min:50,
            // 最大数
            max:110,
            center : ['50%', '70%'],
            startAngle: 180,
            endAngle: 0,
            splitNumber: 5,
            detail: {
                formatter:'{value}kv',
                fontSize:15,
                fontWeight:'bolder',
                color:'#ffffff'
            },
            // 坐标轴线
            axisLine:{
                lineStyle:{
                    width:15
                }
            },
            // 分割线
            splitLine:{
                length:15
            },
            // 名字样式
            title:{
                fontWeight:'bolder',
                color:'#fff',
                fontSize:15
            },
            data: [{value: 60, name: 'kV'}]
        }]
};
setInterval(function () {
    dianliu.series[0].data[0].value = (Math.random() * 10+20).toFixed(2) - 0;
    dv10_1.setOption(dianya, true);
},2000);
//      当前电流
var dv10_2 = echarts.init(document.getElementById("dv10_2"));
var dianliu ={
    tooltip : {
        formatter: "{a} <br/>{b} : {c}kA"
    },
    series: [
        {
            name: 'kA',
            type: 'gauge',
            radius:'130%',
            min:0,
            max:50,
            startAngle: 180,
            center : ['50%', '70%'],
            endAngle: 0,
            splitNumber: 5,
            detail: {
                formatter:'{value}kA',
                fontSize:15,
                fontWeight:'bolder',
                color:'#ffffff'
            },

            axisLine:{
                lineStyle:{
                    width:15  //宽度
                }
            },
            splitLine:{
                length:15
            },
            title:{
                fontWeight:'bolder',
                color:'#fff',
                fontSize:15
            },
            data: [{value: 7, name: 'kA'}]
        }
    ]
};
setInterval(function () {
    dianliu.series[0].data[0].value = (Math.random() * 5+10).toFixed(2) - 0;
    dv10_2.setOption(dianliu, true);
},2000);
//      当前电量
var dv10_3 = echarts.init(document.getElementById("dv10_3"));
var dianliang ={
    tooltip : {
        formatter: "{a} <br/>{b} : {c}kW·h"
    },
    series: [
        {
            name: 'kW·h',
            type: 'gauge',
            radius:'130%',
            min:0,
            max:200,
            center : ['50%', '70%'],
            startAngle: 180,
            endAngle: 0,
            splitNumber: 5,
            detail: {
                formatter:'{value}kW·h',
                fontSize:15,
                fontWeight:'bolder',
                color:'#ffffff'
            },

            axisLine:{
                lineStyle:{
                    width:15
                }
            },
            splitLine:{
                length:15
            },
            title:{
                fontWeight:'bolder',
                color:'#fff',
                fontSize:15

            },
            axisLabel:{

            },
            data: [{value:70, name: 'kW·h'}]
        }
    ]
};
setInterval(function () {
    dianliang.series[0].data[0].value = (Math.random() * 10+20).toFixed(2) - 0;
    dv10_3.setOption(dianliang, true);
},2000);

//      当前温度
var dv10_4 = echarts.init(document.getElementById("dv10_4"));
var wendu ={
    tooltip : {
        formatter: "{a} <br/>{b} : {c}℃"
    },
    series: [
        {
            name: '℃',
            type: 'gauge',
            radius:'130%',
            min:0,
            max:100,
            startAngle: 180,
            center : ['50%', '70%'],
            endAngle: 0,
            splitNumber: 5,
            detail: {
                formatter:'{value}℃',
                fontSize:15,
                fontWeight:'bolder',
                color:'#ffffff'
            },
            axisLine:{
                lineStyle:{
                    width:15
                }
            },

            splitLine:{
                length:15
            },
            title:{
                fontWeight:'bolder',
                color:'#fff',
                fontSize:15

            },
            axisLabel:{

            },
            data: [{value: 30, name: '℃'}]
        }
    ]
};
setInterval(function () {
    wendu.series[0].data[0].value = (Math.random() * 5+25).toFixed(2) - 0;
    dv10_4.setOption(wendu, true);
},2000);
