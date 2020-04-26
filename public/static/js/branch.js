/**
 * Created by Administrator on 2019/1/15.
 */
function table1(){
    // 部门管理
    var branch = new XMLHttpRequest();
    branch.open('GET','/static/json/branch.json',true);
    branch.onload = function(){
        var urs = JSON.parse(this.responseText);
        // console.log(this.responseText)
        var num = 1;
        var content = '';
        for(var i in urs){
            content += ' <tr>'+
                '<th>'+ num++ +'</th>'+
                '<th>'+ urs[i].section +'</th>'+
                '<th>'+ urs[i].place +'</th>'+
                '<th>'+ urs[i].function +'</th>'+
                '<th>'+ urs[i].numb +'</th>'+
                '<th>'+
                '<button class="butAsk">详情</button>'+
                '<button class="butRedact">编辑</button>'+
                '<button class="butRemove">删除</button>'+
                '</th></tr>'
        }
        document.getElementById('table1').innerHTML += content;
    };
    branch.send();
    // 人员管理
    var personnel = new XMLHttpRequest();
    personnel.open('GET','/static/json/branch.json',true);
    personnel.onload = function(){
        var urs = JSON.parse(this.responseText);
        // console.log(this.responseText)
        var num = 1;
        var content = '';
        for(var i in urs){
            content += ' <tr>'+
                '<th>'+ num++ +'</th>'+
                '<th>'+ urs[i].name +'</th>'+
                '<th>'+ urs[i].gender +'</th>'+
                '<th>'+ urs[i].section +'</th>'+
                '<th>'+ urs[i].duty +'</th>'+
                '<th>'+ urs[i].phon +'</th>'+
                '<th>'+
                '<button class="butClear">锁定</button>'+
                '<button class="butRedact">编辑</button>'+
                '<button class="butRemove">删除</button>'+
                '</th></tr>'
        }
        document.getElementById('table2').innerHTML += content;
    };
    personnel.send();

    // 角色设置
    var role = new XMLHttpRequest();
    role.open('GET','/static/json/personnel.json',true);
    role.onload = function(){
        var urs = JSON.parse(this.responseText);
        // console.log(this.responseText)
        var content = '';
        for(var i in urs){
            content += ' <tr>'+
                '<th>'+ urs[i].ID +'</th>'+
                '<th>'+ urs[i].rollName +'</th>'+
                '<th>'+ urs[i].limited +'<br>'+ urs[i].limited1 +'</th>'+
                '<th>'+ urs[i].function +'</th>'+
                '<th>'+ urs[i].numb +'</th>'+
                '<th>'+
                '<button class="butRedact">编辑</button>'+
                '<button class="butRemove">删除</button>'+
                '</th></tr>'
        }
        document.getElementById('table3').innerHTML += content;
    };
    role.send();



    // 权限设置
    var jurisdiction = new XMLHttpRequest();
    jurisdiction.open('GET','/static/json/jurisdiction.json',true);
    jurisdiction.onload = function(){
        var urs = JSON.parse(this.responseText);
        // console.log(this.responseText)
        var content = '';
        for(var i in urs){
            content += ' <tr>'+
                '<th>'+ urs[i].ID +'</th>'+
                '<th>'+ urs[i].name +'</th>'+
                '<th>'+ urs[i].pare +'</th>'+
                '<th>'+ urs[i].c +'</th>'+
                '<th>'+ urs[i].a +'</th>'+
                '<th>'+ urs[i].url +'</th>'+
                '<th>'+ urs[i].zIndex +'</th>'+
                '<th>'+
                '<button class="butRedact">编辑</button>'+
                '<button class="butRemove">删除</button>'+
                '</th></tr>'
        }
        document.getElementById('table4').innerHTML += content;
    };
    jurisdiction.send();


    // 操作日志
    var handle = new XMLHttpRequest();
    handle.open('GET','/static/json/handle.json',true);
    handle.onload = function(){
        var urs = JSON.parse(this.responseText);
        var content = '';
        var num = 1;
        for(var i in urs){
            content += ' <tr>'+
                '<th>'+ num++ +'</th>'+
                '<th>'+ urs[i].coding +'</th>'+
                '<th>'+ urs[i].crew +'</th>'+
                '<th>'+ urs[i].ip +'</th>'+
                '<th>'+ urs[i].time +'</th>'+
                '<th>'+ urs[i].record +'</th>'+
              '</tr>'
        }
        document.getElementById('table5').innerHTML += content;
    };
    handle.send();

}