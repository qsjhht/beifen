
// 开关监听
// let check = document.getElementsByClassName('check');
// for(let v in check){
    // check[v].onclick = function(){
        // if(this.checked){
        //     console.log(true)
        // }else{
        //     console.log(false)
        // }
//     }
// }


let evirEchart0 = echarts.init(document.getElementById('evirEchart0'));//折线
let evirEchart1 = echarts.init(document.getElementById('evirEchart1'))//条形
let evirEchart2 = echarts.init(document.getElementById('evirEchart2'))//面积
let evirEchart3 = echarts.init(document.getElementById('evirEchart3'))//条+面
// 折线图
let dv0 = {
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
        type:'category',
        axisLine:{
            lineStyle:{
                color:'#e4e4e4'
            }
        }
    },
    yAxis:{
        type:'value',
        axisLine:{
            lineStyle:{
                color:'#e4e4e4'
            }
        }
    },
    series:[{
        data:[['Mon',820],['Tue',932],['Wed',901],['Thu',1290],['Fri',1330],['Sat',934],['Sun',1320]],
        type:'line'
    }]
};
// 条形图
let dv1 = {
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
        type:'category',
        axisLine:{
            lineStyle:{
                color:'#e4e4e4'
            }
        }
    },
    yAxis:{
        type:'value',
        axisLine:{
            lineStyle:{
                color:'#e4e4e4'
            }
        }
    },
    series:[{
        data:[['Mon',820],['Tue',932],['Wed',901],['Thu',1290],['Fri',1330],['Sat',934],['Sun',1320]],
        type:'bar'
    }]
}
// 折线面积图
let dv2 = {
    title:{
        text:'折线面积图',
        textStyle:{
            color:'#00ffff',
            fontWeight:'normal',
            fontSize:16
        },
        padding:[15,10]
    },
    xAxis:{
        type:'category',
        axisLine:{
            lineStyle:{
                color:'#e4e4e4'
            }
        }
    },
    yAxis:{
        type:'value',
        axisLine:{
            lineStyle:{
                color:'#e4e4e4'
            }
        }
    },
    series:[{
        data:[['Mon',820],['Tue',932],['Wed',202],['Thu',1290],['Fri',1330],['Sat',934],['Sun',1320]],
        areaStyle: {normal: {}},
        type:'line'
    }]
}
// 折线+条形
let dv3 ={
    title:{
        text:'折线面积图',
        textStyle:{
            color:'#00ffff',
            fontWeight:'normal',
            fontSize:16
        },
        padding:[15,10]
    },
    xAxis:{
        type:'category',
        axisLine:{
            lineStyle:{
                color:'#e4e4e4'
            }
        }
    },
    yAxis:{
        type:'value',
        axisLine:{
            lineStyle:{
                color:'#e4e4e4'
            }
        }
    },
    series:[{
        data:[['Mon',820],['Tue',932],['Wed',202],['Thu',1290],['Fri',1330],['Sat',934],['Sun',1320]],
        type:'line'
    },{
        data:[['Mon',820],['Tue',932],['Wed',202],['Thu',1290],['Fri',1330],['Sat',934],['Sun',1320]],
        type:'bar'
    }]
}
evirEchart0.setOption(dv0);
evirEchart1.setOption(dv1);
evirEchart2.setOption(dv2);
evirEchart3.setOption(dv3);


let evirEchart = document.getElementsByClassName("evirEchart");
function evirFor(b){
    for(b ;b<evirEchart.length;b++){
        evirEchart[b].style.display = 'none'
    }
}
evirFor(1)
// 监控种类
let seleTwo = document.getElementById('seleTwo');
seleTwo.onchange = function(){
    console.log(this.value);
}
// --------------------------------自制Select--------------------------
//option
let evirUl = document.getElementsByClassName('evirUl')[0];
//select
let evirSele = document.getElementsByClassName('evirSele')[0];

let evir = true;
evirSele.onclick = function(){
    if(evir == true){
        evirUl.style.display = 'inline-block';
        evir = false;
    }else{
        evirUl.style.display = 'none';
        evir = true;
    }
}
let evirUlLi = evirUl.getElementsByTagName('li');
for(let u  in evirUlLi){
    evirUlLi[u].onclick = function(){
        switch(u){
            case "0"://折线图
                evirSele.innerHTML= '<i class="icon-chart-01"></i>'
                evirUl.style.display = 'none'
                evir = true;
                evirFor(0);
                evirEchart[0].style.display = "inline-block"
                break;
            case "1": //条形图   
                evirSele.innerHTML= '<i class="icon-chart-02"></i>'
                evirUl.style.display = 'none'
                evir = true;
                evirFor(0);
                evirEchart[1].style.display = "inline-block"
                break;
            case "2"://面积图   
                evirSele.innerHTML= '<i class="icon-chart-03"></i>'
                evirUl.style.display = 'none'
                evir = true;
                evirFor(0);
                evirEchart[2].style.display = "inline-block"
                break;
            case "3"://条形＋折线
                evirSele.innerHTML= '<i class="icon-chart-04"></i>'
                evirUl.style.display = 'none'
                evir = true;
                evirFor(0);
                evirEchart[3].style.display = "inline-block"
                break;
        }       
    }
}





















