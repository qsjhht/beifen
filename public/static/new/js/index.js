
//设置参数
let username = 'admin'; //账户名
let userpass = 'admin'; // 密码
let did = 'hk20190521047-1'; //采集箱

let num = 0   // 参数 0 || 1  采集箱
let a = 2;

// let  
let date = new Date()  //日期
let getDate = date.getFullYear()+'-'+(date.getMonth()+1)+'-'+ date.getDate(); //当前日期

let data; // 数据
let oo;  //时间
let socketData; //socket 数据 
let numData = new Array(); //数据
let stayguyData = new Array(); // 拉线
let angle_X = new Array(); //角度-X
let angle_Y = new Array(); //角度-Y


// ajxa 使用
const XML = new XMLHttpRequest();
let url = `http://10.3.100.8:88/OpenPlatform/openApi?u=${username}&p=${userpass}&did=${did}&date=${getDate}`;
XML.open('POST',url,false);
XML.onreadystatechange = function(){
    if (XML.readyState == 4 && XML.status == 200) { //回传成功
        let evirData = JSON.parse(XML.responseText).result.data;
        data = evirData.slice(evirData.length-800,evirData.length);
        if(data[1].data.length == 10){
            num += 2
            console.log('10')
        }else if(data[1].data.length == 4){
            console.log('4')
            a = 1;
            num = 0;
        }
    }else{
        console.log('数据获取失败')
    }
}
XML.send();
// 获取历史数据
for(let i in data){
    oo = data[i].date.substring(11,20);
    numData.push([oo,data[i].data[num].data]);
    stayguyData.push([oo,data[i].data[num+(1*a)].data]);
    angle_X.push([oo,data[i].data[num+(2*a)].data])
    angle_Y.push([oo,data[i].data[num+(3*a)].data])
}
// evirEchart
function evirEchart(echaId,name,dataEvir){
    settling = echarts.init(document.getElementById(echaId));
    console.dir(settling);
    settling.setOption({
        title:{
            text:name,
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
            name:name,
            type:'value',
            axisLine:{
                lineStyle:{
                    color:'#e4e4e4'
                }
            }
        },
        series:[{
            data:dataEvir,
            type:'line'
        }]
    })
}

evirEchart('settling1','沉降数据（mm）', numData);
evirEchart('settling2','拉线数据（mm）',stayguyData);
evirEchart('settling3','倾角-X（度）',angle_X);
evirEchart('settling4','倾角-Y（度）',angle_Y)

// webSocket  使用
wsUrl = 'ws://10.3.100.8:88/OpenPlatform/webSocket?projectId=211'
let ws = new WebSocket(wsUrl);
ws.onopen = function(){
    console.log("client:打开链接");
    ws.send('{"u":"admin", "p":"admin", "code": 201 }')
};
ws.onmessage = function(e){
    socketData = JSON.parse(e.data);  //报错
    if(socketData.deviceId == did){
        oo = socketData.date.substring(11,20);
        numData.push([oo,socketData.data[num].data]);
        numData.shift();
        evirEchart('settling1','沉降数据（mm）', numData);

        stayguyData.push([oo,socketData.data[num+(1*a)].data]);
        stayguyData.shift();
        evirEchart('settling2','拉线数据（mm）',stayguyData);

        angle_X.push([oo,socketData.data[num+(2*a)].data])
        angle_X.shift();
        evirEchart('settling3','倾角-X（度）',angle_X);

        angle_Y.push([oo,socketData.data[num+(3*a)].data])
        angle_Y.shift();
        evirEchart('settling4','倾角-Y（度）',angle_Y)

    }
};
// 断开按钮
// function err(){
//     ws.close();
// };
ws.onclose = function(){
    console.log("webSocket 已经断开链接")
}
ws.onerror = (err)=>{
    console.log("错误"+err)
}


