//layui插件
layui.use(['element','form','table','laypage'], function(){
    var element = layui.element,//元素
        form = layui.form, //列表
        table = layui.table,//表格.
        laypage = layui.laypage; // 翻页
    // 1
    laypage.render({
        elem:'laypage'
        ,limit:16
        ,groups:2
        ,count:50
        ,prev: '上一页'
        ,next:'下一页'
        ,theme:'#237a98'
    });
    // 2
    laypage.render({
        elem:'laypage1'
        ,limit:16
        ,groups:2
        ,count:50
        ,prev: '上一页'
        ,next:'下一页'
        ,theme:'#237a98'
    });
    // 3
    laypage.render({
        elem:'laypage2'
        ,limit:16
        ,groups:2
        ,count:50
        ,prev: '上一页'
        ,next:'下一页'
        ,theme:'#237a98'
    });
    // 4
    laypage.render({
        elem:'laypage3'
        ,limit:16
        ,groups:2
        ,count:50
        ,prev: '上一页'
        ,next:'下一页'
        ,theme:'#237a98'
    });
    // 5
    laypage.render({
        elem:'laypage4'
        ,limit:16
        ,groups:2
        ,count:50
        ,prev: '上一页'
        ,next:'下一页'
        ,theme:'#237a98'
    });
});

// 一级菜单 span 消失
function menu1(){
    var header = document.getElementsByClassName('headerNav')[0];
    var headerNav = header.childNodes;
    for(var i = 0; i<headerNav.length;i++){
        headerNav[i].index = i;

        //移动上去显示
        headerNav[i].onmouseover = function(){

            if(this.index < 38 && this.index > 4){
                headerNav[this.index+2].style.color = 'black';
                headerNav[this.index-2].style.color = 'black';
            }else if(this.index <5 && this.index>1){
                headerNav[this.index+2].style.color = 'black';
            }else{}
        };
        // 移动下去显示
        headerNav[i].onmouseout = function() {
            if (this.index < 38 && this.index > 4) {
                headerNav[this.index + 2].style.color = '#00ffff';
                headerNav[this.index - 2].style.color = '#00ffff';
            } else if (this.index < 5 && this.index > 1) {
                headerNav[this.index + 2].style.color = '#00ffff';
            } else {
            }
        };
        // 单机显示隐藏
        headerNav[i].onclick = function(){
            if(this.index < 38 && this.index > 4){
                if(this.nodeName == "SPAN"){
                    return false;
                }
                var ggg = header.children;
                for(var kkk = 0 ; kkk < ggg.length; kkk++){
                    ggg[kkk].style.outline = '';
                    ggg[kkk].style.boxShadow= '';
                    ggg[kkk].style.visibility = '';
                }
                this.style.outline = "1px solid #247998";
                this.style.boxShadow = "0 0 6px #247998 inset";
                headerNav[this.index+2].style.visibility = 'hidden';
                headerNav[this.index-2].style.visibility = 'hidden';

            }
        }
    }
}

// -------------------------------------------------------------------------
// 二级菜单 选择展示页面
function skip(){
    var mainLeft = document.getElementsByClassName('mainLeft')[0];
    var mainMiddle = document.getElementsByClassName('mainMiddle'); //内容
    var li = mainLeft.getElementsByTagName('li');               //二级菜单
    // var layer = document.getElementsByClassName('layer'); // 弹窗
    // var mask = document.getElementsByClassName('mask')[0]; //遮罩层
    // var close = document.getElementsByClassName('close'); //关闭按钮
    // var cancel = document.getElementsByClassName('cancel'); //取消关闭按钮

    // 打开内容
    for(var j = 0 ; j<li.length ; j++){
       li[j].index = j;
        // 二级菜单打开内容
        li[j].onclick = function(){
            for(var y = 0;y<li.length;y++){
                li[y].setAttribute('class','');
                mainMiddle[y].style.display = 'none';
            }
            mainMiddle[this.index].style.display = 'inline-block';
            li[this.index].setAttribute('class','outline');
        };
    }

    // // 打开弹窗
    // for(var p = 0 ; p<middleAdd.length ; p++){
    //     middleAdd[p].index = p;
    //     // 选择打开弹出框
    //     middleAdd[p].onclick = function(){
    //         mask.style.display = 'block';
    //         layer[this.index].style.display = 'block';
    //         console.log(this.index);
    //     }
    // }

    // // 关闭弹窗
    // for(var k = 0; k < close.length; k++){
    //     close[k].onclick = function(){
    //         mask.style.display = 'none';
    //         for(var m = 0; m< layer.length ; m++){
    //             layer[m].style.display ='none';
    //         }
    //     };
    // }

    // //取消关闭按钮
    // for(var u = 0 ;u<cancel.length;u++){
    //     cancel[u].onclick = function(){
    //         mask.style.display = 'none';
    //         for(var m = 0;m<layer.length ; m++){
    //             layer[m].sytle.display = 'none'
    //         }
    //     }
    // }

}














