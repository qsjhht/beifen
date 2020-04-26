/**
 * Created by Administrator on 2019/1/7.
 */
// ------------------------------------------table列表------------------------------------------
function table1(){
    var xhr = new XMLHttpRequest();
    // 设备台账
    xhr.open('GET','../json/equipment.json',false);
    xhr.onload = function(){
        var urs = JSON.parse(this.responseText);
        var num = 1;
        var content = '';
        for(var i in urs){
            content += '<tr>'+
                '<th>'+ num++ +'</th>'+
                '<th>'+urs[i].eqName +'</th>'+
                '<th>'+urs[i].eqType +'</th>'+
                '<th>'+urs[i].eqLocation +'</th>'+
                '<th class="eqState" >'+urs[i].eqLoca+'</th>'+
                '<th>'+urs[i].eqBrand +'</th>'+
                '<th>'+
                '<button class="butAsk bookDet">详情</button>'+
                '<button class="butRedact bookEdit">编辑</button>'+
                '</th></tr>'
        }
        document.getElementById('table1').innerHTML += content;


    };
    xhr.send();
    //设备消账
    var cross = new XMLHttpRequest();
    cross.open('GET','../json/equipment.json',false);
    cross.onload = function(){
        var urs = JSON.parse(this.responseText);
        var content = '';
        var num1 = 1;
        for(var i in urs){
            content +=  '<tr>'+
                '<th>'+num1++ +'</th>'+
                '<th>'+ urs[i].eqName +'</th>'+
                '<th>'+ urs[i].eqType +'</th>'+
                '<th>'+ urs[i].eqLocation +'</th>'+
                '<th>'+urs[i].eqBrand +'</th>'+
                '<th>'+
                '<button class="butAsk">详情</button>'+
                '<button class="butRemove">消账</button>'+
                '</th> </tr>'
        }
        document.getElementById('table2').innerHTML += content;

    };
    cross.send();
    // 设备维修  ----未处理
    var maintain = new XMLHttpRequest();
    maintain.open('GET','../json/equipment.json',false);
    maintain.onload = function(){
        var urs = JSON.parse(this.responseText);
        var content = '';
        for(var i in urs){
            content += '<tr>'+
                '<th>'+urs[i].eqWork +'</th>'+
                '<th>'+ urs[i].eqName +'</th>'+
                '<th>'+ urs[i].eqUrgency +'</th>'+
                '<th>'+urs[i].eqTeam +'</th>'+
                '<th>' +urs[i].eqDistribute +'</th>'+
                '<th>'+ urs[i].eqTime +'</th>'+
                '<th>'+
                '<button class="butAsk mainDet">详情</button>'+
                '<button class="butRedact mainEdit">编辑</button>'+
                '<button class="butRemove">删除</button>'+
                '</th> </tr>'
        }
        document.getElementById('table4').innerHTML += content;
    };
    maintain.send();
    // 设备维修  ----已处理
    var mainTain = new XMLHttpRequest();
    mainTain.open('GET','../json/equipment.json',false);
    mainTain.onload = function(){
        var urs = JSON.parse(this.responseText);
        var content = '';
        for(var i in urs){
            content += '<tr>'+
                '<th>'+urs[i].eqWork +'</th>'+
                '<th>'+ urs[i].eqName +'</th>'+
                '<th>'+ urs[i].eqTeam +'</th>'+
                '<th>'+urs[i].eqTime +'</th>'+
                '<th>'+ urs[i].eqFault +'</th>'+
                '<th>'+
                '<button class="butAsk mainDet">详情</button>'+
                '<button class="butRedact mainEdit">编辑</button>'+
                '</th> </tr>'
        }
        document.getElementById('table4_1').innerHTML += content;
    };
    mainTain.send();

    poput();//打开弹窗的方法
    state();//台账列表切换select
}






// ------------------------------------------设备台账------------------------------------------------


// 二级联动a  防火分区
cities = new Object();
cities['园博园大街']=new Array('YBY-001', 'YBY-002', 'YBY-003', 'YBY-004', 'YBY-005', 'YBY-006', 'YBY-007');
cities['迎旭路']=new Array('YX-001', 'YX-002', 'YX-003', 'YX-004', 'YX-005', 'YX-006', 'YX-007');
cities['太行大街'] = new Array('TH-001','TH-002','TH-003','TH-004','TH-005','TH-006','TH-007');
cities['隆兴路'] = new Array('LX-001','LX-002','LX-003','LX-004','LX-005','LX-006','LX-007');
cities['新城大道'] = new Array('XC-001','XC-002','XC-003','XC-004');

function set_city(province, city)
{
    var pv, cv;
    var i, ii;
    pv=province.value;
    cv=city.value;
    city.length=1;
    if(pv=='0') return;
    if(typeof(cities[pv])=='undefined') return;
    for(i=0; i<cities[pv].length; i++)
    {
        ii = i+1;
        city.options[ii] = new Option();
        city.options[ii].text = cities[pv][i];
        city.options[ii].value = cities[pv][i];
    }
}
// 二级联动 设备类型
function state(){
    var eqState = document.getElementsByClassName('eqState');
    for(var a in eqState){
        eqState[a].index = a;
        // 颜色切换
        if(eqState[a].innerHTML == '故障'){
            eqState[a].style.color = '#ed9e2a'
        }else if(eqState[a].innerHTML == '报废'){
            eqState[a].style.color = '#df3d38'
        }else if(eqState[a].innerHTML == '正常'){
            eqState[a].style.color = '#ffffff';
        }
        var aaa = 0;
        eqState[a].onclick = function(){
            var ccc = this.index;
            var select = '<select class="sele" id="sele">' +
                '<option value="正常">正常</option>'+
                '<option value="故障">故障</option>'+
                '<option value="报废">报废</option>'+
                '</select>';
            var inn = eqState[this.index].innerHTML;//改变之前的值
            if (inn.length <3){
                if(aaa == 0) {
                    eqState[this.index].innerHTML = select;
                    aaa = 1
                }else{
                    layer.msg('设备状态已返回');
                    var bbb = document.getElementsByClassName('sele')[0];
                    bbb.parentNode.innerHTML = bbb.value;
                    aaa = 0;
                }
            } else{
                return false;
            }
            // 建立对象
            var price = {
                '正常':0,
                '故障':1,
                '报废':2
            };
            // 改变之后获取改变之前的值
            var sele =document.getElementById('sele');
            sele.options[price[inn]].setAttribute('selected','selected');
            // 下拉框获取到的值切换成之后的值
            sele.addEventListener('change',function(price){
                price = this;
                layui.use('layer',function(){
                    var layer = layui.layer;
                    layer.open({
                        type: 0 //此处以iframe举例
                        ,title:'警告'
                        ,content:'确定将设备状态改为['+sele.value+']吗？'+'</br>'+'此操作将直接进入工单派发页面。'
                        ,area: ['390px', '260px']
                        ,skin:'demo-class'
                        ,offset: 'auto'
                        ,btn: ['确定', '取消']
                        ,yes: function(){
                            price.parentNode.innerHTML = sele.value;
                            layer.closeAll();
                            aaa = 0;
                            if(sele.value === '报废'){
                                eqState[ccc].style.color = '#df3d38'
                            }else if(sele.value === '故障'){
                                eqState[ccc].style.color = '#ed9e2a';
                                hhh();
                            }else if(sele.value === '正常'){
                                eqState[ccc].style.color = '#ffffff'
                            }
                        }
                        ,btn2: function(){
                            price.parentNode.innerHTML = inn;
                            layer.closeAll();
                            aaa = 0;
                        }
                    })
                });
            });
        };
    }
}
// 台账页面打开维修弹窗
function hhh(){
    var layer = document.getElementsByClassName('layer'); // 弹窗
    var mask = document.getElementsByClassName('mask')[0]; //遮罩层
    mask.style.display = 'block';
    layer[3].style.display = 'block'
}
// 打开弹窗
function poput(){
    var layer = document.getElementsByClassName('layer'); // 弹窗
    var mask = document.getElementsByClassName('mask')[0]; //遮罩层
    var close = document.getElementsByClassName('close'); //关闭按钮
    var cancel = document.getElementsByClassName('cancel'); //取消关闭按钮
    var middleAdd = document.getElementsByClassName('middleAdd');//添加按钮
// 台账弹窗
    var bookDet = document.getElementsByClassName('bookDet'); //台账详情
    var bookEdit = document.getElementsByClassName('bookEdit'); //台账编辑
    var compile = document.getElementsByClassName('compile')[0];//查看里面编辑
    var redactBook = document.getElementById('redactBook');//台账编辑
    var checkBook = document.getElementById('checkBook');//台账查看
//维修
    var btn1 = document.getElementById('btn1');//已处理按钮
    var btn2 = document.getElementById('btn2');//未处理按钮
    var table4 = document.getElementById('table4');//未处理页面
    var table4_1 = document.getElementById('table4_1');//已处理页面
    var mainDet = document.getElementsByClassName('mainDet');//页面详情
    var mainEdit = document.getElementsByClassName('mainEdit');//页面编辑
    var mainEditId = document.getElementById('mainEdit');//弹窗编辑页面
    var mainDetId = document.getElementById('mainDet');//弹窗详情页面
    var compile1 = document.getElementById('compile1');//弹窗页面中的编辑按钮
    var serTime = document.getElementById('serTime');//弹窗中详情页面时间
    //  添加按钮打开弹窗
    for(var p = 0 ; p<middleAdd.length; p++){
        middleAdd[p].index = p;
        // 选择打开弹出框
        middleAdd[p].onclick = function(){
            mask.style.display = 'block';
            layer[this.index].style.display = 'block';
            redactBook.style.display = 'block';
            checkBook.style.display = 'none';
        }
    }

    // 关闭弹窗
    for(var k = 0; k < close.length; k++){
        close[k].onclick = function(){
            mask.style.display = 'none';
            for(var m = 0; m< layer.length ; m++){
                layer[m].style.display ='none';
            }
        };
    }

    //取消关闭按钮
    for(var u = 0 ;u<cancel.length;u++){
        cancel[u].onclick = function(){
            mask.style.display = 'none';
            for(var m = 0;m<layer.length ; m++){
                layer[m].sytle.display = 'none'
            }
        }
    }


    //    制作三角形背景
    var camera1 = document.getElementById('3D');
    var can = camera1.getContext('2d');
    can.fillStyle ='#333333';
    can.moveTo(0,50);
    can.lineTo(50,0);
    can.lineTo(50,50);
    can.closePath();
    can.fill();
//三角的hover事件
    var canvas = document.getElementsByClassName('canvas')[0];
    var img1 = document.getElementById('img1');
    camera1.onmouseover = function(){
        canvas.style.bottom = '0';
    };
    img1.onmouseover = function(){
        canvas.style.bottom = '0';
    };
    camera1.onmouseout = function(){
        canvas.style.bottom = '-20px';
    };

//三角形的click事件
    var camera = document.getElementById('camera');
    var image3D = document.getElementById('image3D');
    img1.onclick = function(){
        if(this.getAttribute('src') == '../image/camera_1.png'){
            this.setAttribute('src','../image/3D_1.png');
            image3D.style.display = 'none';
            camera.style.display = 'block';
        }else{
            this.setAttribute('src','../image/camera_1.png');
            image3D.style.display = 'block';
            camera.style.display = 'none';
        }
    };

//--------------------------------------------------------台账

    // 添加配件
    var parts = document.getElementById('parts');
    var layerPar = document.getElementsByClassName('layerPar')[0];
    parts.addEventListener('click',function(){
        var layerParets = '<div class="layerParets">'+
            '<p>配件信息</p>'+
            '<div class="layerImage1">'+
            '<img src="../image/camera_1.png" alt="" width="50" height="45">'+
            '<p>单机上传图像</p>'+
            '</div>'+
            '<div class="layerFrame" style="margin-top:20px">'+
            '<div class="layerSelect">'+
            '<label for="" class="label">配件名称</label>'+
            '<input type="text" class="input">'+
            '<label for="" class="label">规格型号</label>'+
            '<input type="text" class="input">'+
            '</div>'+
            '<div class="layerSelect">'+
            '<label for="" class="label">需要数量/个</label>'+
            '<input type="text" class="input"> '+
            '</div>'+
            '<div class="layerSelect">'+
            '<label for="" class="label">备注信息</label>'+
            '<input type="text" class="input" style="width:540px">'+
            '</div> </div> </div>';
        layerPar.innerHTML += layerParets;
    });

    //台账编辑打开弹窗
    for(var i = 0 ; i < bookEdit.length ; i++){
        bookEdit[i].index = i;
        bookEdit[i].onclick = function(){
            mask.style.display = 'block';//遮罩层
            layer[0].style.display = 'block';
            redactBook.style.display = 'block';
            checkBook.style.display = 'none';
        }
    }

    // 台账查看打开窗口
    for(var o = 0 ; o < bookDet.length ; o++){
        bookDet[o].index = o;
        bookDet[o].onclick = function(){
            // console.log(this.length);
            mask.style.display ='block';
            layer[0].style.display = 'block';
            redactBook.style.display = 'none';
            checkBook.style.display = 'block';
        }
    }
    //查看编辑
    compile.onclick = function(){
        redactBook.style.display = 'block';
        checkBook.style.display = 'none';
    };
// -------------------------------------------------------维修
//     按钮未处理
    btn1.onclick = function(){
        this.classList.add('btn');
        btn2.classList.remove('btn');
        table4_1.style.display = 'none';
        table4.style.display = '';
    };
    // 按钮已处理
    btn2.onclick = function(){
        this.classList.add('btn');
        btn1.classList.remove('btn');
        table4.style.display = 'none';
        table4_1.style.display = '';
    };

    // 页面编辑按钮
    for(var t = 0 ; t < mainEdit.length ; t++){
        mainEdit[t].onclick = function(){
            mask.style.display ='block';
            layer[3].style.display = 'block';
            mainEditId.style.display = 'block';
            mainDetId.style.display = 'none';
        }
    }

    // 页面详情按钮
    for(var h = 0 ; h < mainDet.length ;h++){
        mainDet[h].onclick = function(){
            mask.style.display = 'block';
            layer[3].style.display = 'block';
            mainEditId.style.display = 'none';
            mainDetId.style.display = 'block';
            serTime.innerHTML += new Date().toLocaleDateString();
        }
    }

    compile1.onclick = function(){
        mainEditId.style.display = 'block';
        mainDetId.style.display = 'none';
    };

}





// 上传图片
function createObjectURL(blob){
    if (window.URL){
        return window.URL.createObjectURL(blob);
    } else if (window.webkitURL){
        return window.webkitURL.createObjectURL(blob);
    } else {
        return null;
    }
}

var box = document.querySelector("#camera"); //显示图片box
var file = document.querySelector("#file"); //file对象
var domFragment = document.createDocumentFragment(); //文档流优化多次改动dom

// 触发选中文件事件
file.onchange = function(e){
    box.innerHTML =""; //清空上一次显示图片效果
    e = e || event;
    var file = this.files; //获取选中的文件对象
    for(var i = 0, len = file.length; i < len; i++){
        var imgTag = document.createElement("img");
        var fileName = file[i].name; //获取当前文件的文件名
        var url = createObjectURL(file[i]); //获取当前文件对象的URL
        //忽略大小写
        var jpg = (fileName.indexOf(".jpg") > -1) || (fileName.toLowerCase().indexOf(".jpg") > -1);
        var png = (fileName.indexOf(".png") > -1) || (fileName.toLowerCase().indexOf(".png") > -1);
        var jpeg = (fileName.indexOf(".jpeg") > -1) || (fileName.toLowerCase().indexOf(".jpeg") > -1);

        //判断文件是否是图片类型
        if(jpg || png || jpeg){
            imgTag.src = url;
            domFragment.appendChild(imgTag);
        }else{
            alert("请选择图片类型文件！");
        }
    }
    //最佳显示
    box.appendChild(domFragment);
};

// -----------------------------------------设备更换-------------------------------

function serviceBtn(){
    var layerFrame2 = document.getElementById('layerFrame2');
    layerFrame2.style.display = 'block';
    layerFrame2.classList = 'animation';
}

// -------------------------------------设备拆解---------------------------------------

var fjLeft = document.getElementById('fjLeft');
var fjRight = document.getElementById('fjRight');
var fjPop1 = document.getElementsByClassName("fjPop1")[0];
var fjPop2 = document.getElementsByClassName("fjPop2")[0];
var h = true;
fjLeft.addEventListener('click',function(){
    if( h == true ){
        h = false;
        this.style.background = 'url(../image/fjLeft2.png) no-repeat';
        this.style.backgroundSize = '100%';
        fjPop2.style.display = "block";
    }else{
        h = true;
        this.style.background = 'url(../image/fjLeft1.png) no-repeat';
        this.style.backgroundSize = '100%';
        fjPop2.style.display = "none";
    }
});
fjRight.addEventListener('click',function(){
    if( h == true ){
        h = false;
        this.style.background = 'url(../image/fjRight2.png) no-repeat';
        this.style.backgroundSize = '100%';
        fjPop1.style.display = "block";
    }else{
        h = true;
        this.style.background = 'url(../image/fjRight1.png) no-repeat';
        this.style.backgroundSize = '100%';
        fjPop1.style.display ="none";
    }
});























