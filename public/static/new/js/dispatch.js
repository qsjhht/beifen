
let  dispatchArray = [],
    dispatch,
    dispatchRed,
    dispatchCon,
    a = 0 ,
    b = 0 , //右侧菜单选择
    c
let  switchValue,  // 调度台显示
    showHide = new Array();  // 已拨号数组

layui.use('layer',()=>{
    let layer = layui.layer
})


// 获取信息
function ajax(){
    // 获取content 数据
    let num,S,D   // 数 ， 获取的数据 ， 水仓数据 ，电仓数据
    $.ajax({
        url:'http://192.168.10.18/real/get_phone',
        type:'get',
        async:false,
        success:(data) => {
            dispatch = JSON.parse(data).data  // 获取信息
            dispatchRight()
        },
        error:(err) => {
            console.log('请求数据失败!')
        }
    })
}
ajax()

// 写右侧数组
function dispatchRight(){
    for(let i in dispatch){
        dispatchArray.push(i)
        // 右侧菜单
        dispatchRed = `<div class="rightMenu menu_blue">${i}</div>`
        $('#dispatchBottom_right').append(dispatchRed)

        // 左侧话机
        dispatchCon = `<div class="dispatchShowHide" style="height: 98%">
               <fieldset class="layui-elem-field layui-field-title">
                    <legend> <img src="../../static/new/dispatch-img/Array.png" width="35" height="35"> ${i}</legend>
               </fieldset>
               <div class="scrollber dispatchContent"></div>
          </div>`
        $('#dispatchBottom_left').append(dispatchCon)
        dispatchItem(dispatch[i],i)
    }
}

// 写水电仓 内容
function dispatchItem(a,b){
    let dispatchState,numContent,numDispatch
    numDispatch = dispatchArray.indexOf(b)
    for(let i in a){
        if(a[i]['分机状态'] == '' || a[i]['分机状态'] == '空闲'){
            dispatchState = 'leisure'
        }else if(a[i]['分机状态'] == '呼叫失败'){
            dispatchState = 'goUnder'
        }else if(a[i]['分机状态'] == '摘机'){
            dispatchState = 'outboard'
        }else if(a[i]['分机状态'] == '正在呼叫'){
            dispatchState = 'callOut'
        }
        numContent = ` <div class="${dispatchState}"  data-name="${a[i]["分机号码"]}">
                              <div class="dispatch_div">
                                   <span>${a[i]["分机号码"]}</span>
                                   </br></br>
                                   <span>${a[i]["分机名称"]}</span>
                              </div>
                         </div>  `
        $(`.dispatchShowHide:eq(${numDispatch}) .dispatchContent:eq(0)`).append(numContent)
    }
    $(`.dispatchShowHide:eq(0)`).css('display','block')
}

// 右侧二级菜单 单机事件
function dispatchClick(){
    let rightMenu = document.getElementsByClassName('rightMenu')
    for(let i = 0 ; i < rightMenu.length ; i++){
        rightMenu[i].onclick = function(){
            $('#jiao').css('top',`${15+(i*70)}px`)
            $('.dispatchShowHide').each(function(){
                $(this).css('display','none')
            })
            $(`.dispatchShowHide:eq(${i})`).css('display','block')
            b = i
            dispatchClickItem(i)
        }
    }
}
dispatchClick()
// 话机 单机事件
function dispatchClickItem(a){
    $(`.dispatchShowHide:eq(${a}) .dispatchContent:eq(0) > div`).each(function(e){
        $(this).click(function(){
            let itemData = dispatch[dispatchArray[a]];
            if(switchValue == '8001' || switchValue == '8002'){
                if(!hasitem(showHide,itemData[e]["分机号码"])){
                    $.ajax({
                        url:`http://192.168.10.18/real/call_phone?type=调度单呼&from_num=${switchValue}&call=${itemData[e]["分机号码"]}`,
                        type:'get',
                        success:(res) => {
                            console.log(res);
                        }
                    })
                    showHide.push(itemData[e]["分机号码"]);
                }else{
                    layer.msg(itemData[e]["分机号码"]+'  已呼叫！')
                }
            }else{
                layer.msg('调度台未摘机！')
            }
        })
    })
}

function hasitem(arr,value){
    let res = false;
    arr.forEach(item=>{
        if(item == value){
            res = true;
        }
    })
    return res;
}


function delitem(arr,value){
    let index = arr.indexOf(value);
        arr.forEach(item=>{
        if(index > -1){
            arr.splice(index,1);
        }
    })
}

// 点击进行组呼
$('.dispatchImg1').click(()=>{
    if(switchValue == 8001 || switchValue == 8002){
        if(!hasitem(showHide,dispatchArray[b])){
            $.ajax({
                url:`http://192.168.10.18/real/call_phone?type=调度群呼&from_num=${switchValue}&call=${dispatchArray[b]}`,
                type:'get',
                success:(data) => {
                    console.log(data)
                }
            })
            showHide.push(dispatchArray[b]);
        }else{
            layer.msg(dispatchArray[b]+'  已呼叫！')
            delitem(showHide,dispatchArray[b])
        }
    }else{
        layer.msg('调度台未摘机！')
    }
})

// WebSocket
var socket = io('http://192.168.10.18:2120');
socket.emit('login',33333)
socket.on('new_msg',(data) => {
    c = eval("("+data+")");
    if(c.category == '号码更新'){
        ajax();
    }else if(c.category == '分机状态'){

        if(c['号码'] == 8001){
            if(c['状态'] == "空闲"){
                switchValue = '';
                $('#display1').css('backgroundColor','#00FFFF')
            }else if(c['状态'] == "摘机"){
                switchValue = c['号码']
                $('#display1').css('backgroundColor','#1ac036')
            }else{
                layer.msg('未找到该状态')
            }
        }else if(c['号码'] == 8002){
            if(c['状态'] == '空闲'){
                switchValue = '';
                $('#display2').css('backgroundColor','#00FFFF')
            }else if(c['状态'] == '摘机'){
                switchValue = c['号码'];
                $('#display2').css('backgroundColor','#1ac036')
            }else{
                console.log('未找到该状态');
            }
        }else{
            for(let i in dispatch){
                dispatch[i].filter((data,index) => {
                    if(data['分机号码'] == c['号码']){
                        core(i,index,c)
                    }
                })
            }
        }
    }
})

// 单个话机
function core(a,b,c){
    switch(c['状态']){

        case '空闲':
            // console.dir(showHide);
            let values= $(`.dispatchShowHide:eq(${dispatchArray.indexOf(a)}) .dispatchContent:eq(0)> div:eq(${b})`)[0].dataset.name;
            delitem(showHide,values)
            // console.dir(showHide);
            $(`.dispatchShowHide:eq(${dispatchArray.indexOf(a)}) .dispatchContent:eq(0)> div:eq(${b})`).removeAttr('class').addClass('leisure')
            // showHide = true
            break;
        case '摘机':
            $(`.dispatchShowHide:eq(${dispatchArray.indexOf(a)}) .dispatchContent:eq(0)> div:eq(${b})`).removeAttr('class').addClass('outboard')
            // showHide = false
            break;
        case '正在呼叫':
            // showHide = false
            $(`.dispatchShowHide:eq(${dispatchArray.indexOf(a)}) .dispatchContent:eq(0)> div:eq(${b})`).removeAttr('class').addClass('callOut')
            break;
        case '呼叫失败':
            // console.dir(showHide);
            let value= $(`.dispatchShowHide:eq(${dispatchArray.indexOf(a)}) .dispatchContent:eq(0)> div:eq(${b})`)[0].dataset.name;
            delitem(showHide,value)
            // console.dir(showHide);
            $(`.dispatchShowHide:eq(${dispatchArray.indexOf(a)}) .dispatchContent:eq(0)> div:eq(${b})`).removeAttr('class').addClass('goUnder')
            // showHide = true
            break;
        default:
            layer.msg('没有找到该状态！')
            break;
    }
}
