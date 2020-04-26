$(function () {

    var cls;
    //编辑以及添加事件
    $('.editor,.middleAdd').click(function(){
        $('.mask, .layer').show();
        cls = $(this).attr('class');
        if(cls == 'editor'){

            //编辑事件内容
            // $('.popTit').text($('.linkName').text());
            // $('.addPop').hide();
            // $('.ediPop').show();
            layer.open({
                type:2,
                title:'编辑事件',
                shadeClose:true,
                shade:0.5,
                area:['1080px','700px'],
                content:['../html/dataLinkEditor.html','no']
            })

        }else if(cls == 'middleAdd'){

            //添加事件内容
            // $('.popTit').text('添加事件');
            // $('.ediPop').hide();
            // $('.addPop').show();
            layer.open({
                type:2,
                title:'添加事件',
                shadeClose:true,
                shade:0.5,
                area:['1080px','700px'],
                content:['../html/dataLinkAdd.html','no']
            })
        }
    });

    var filter = 'TJXT';
    var id = 'SHB';
    var filterGo = 'ZXXT';
    var idGo = 'ZX';

layui.use('form',function(){
    var form = layui.form;// 添加条件
    var num1 = 1;
    var num2 = 1;
    $('#addBtn').click(function(){
        // console.log($('.conNumCon'));
        filter = filter+'1';
        id = id+'1';
        num1 ++;
        var html = '<div class="conNum conNumCon"><div>条件：'+ num1 +'</div><div class="conSunMsg"><div class="layui-form-item"><label class="layui-form-label">系统名称：</label> <div class="layui-input-inline"><select name="SHB[]"  class="select" lay-filter="'+filter+'"> <option value="">请选择</option><option value="0">安防系统</option><option value="1">消防系统</option><option value="2">机器人系统</option><option value="3">环境控制系统</option></select></div> <label class="layui-form-label">设备名称：</label><div class="layui-input-inline"><select name="SHBsub[]"  class="SHB" id="'+id+'"><option value="">请选择</option></select></div><label class="layui-form-label">参数名称：</label><div class="layui-input-inline"><select name="paramset[]"  class="select"><option value="0">请选择</option><option value="1">入侵报警</option><option value="2">水灾报警</option><option value="3">火灾报警</option></select></div><label class="layui-form-label">参数设值：</label><div class="layui-input-inline"><input class="layui-input"  name="paramsetnum[]" type="text"></div><label class="layui-form-label">联动方式：</label><div class="layui-input-inline"><select name="lingtypeset[]"  class="select"><option value="0">请选择</option><option value="1">与</option><option value="2">或</option><option value="3">非</option> </select></div></div></div></div>';
        $('.conNumCon:last').after(html);
        form.render();
    })

    $('#addGo').click(function(){ //添加执行
        num2 ++;
        filterGo = filterGo+'1';
        idGo = idGo+'1';
        var html = '<div class="conNum conNumGo"><div>执行：'+ num2 +'</div><div class="conSunMsg"><div class="layui-form-item"><label class="layui-form-label">系统名称：</label> <div class="layui-input-inline"><select name="ZX[]"  class="select" lay-filter="'+filterGo+'"> <option value="">请选择</option><option value="0">安防系统</option><option value="1">消防系统</option><option value="2">机器人系统</option><option value="3">环境控制系统</option></select></div> <label class="layui-form-label">设备名称：</label><div class="layui-input-inline"><select name="ZXsub[]"  class="SHB" id="'+idGo+'"><option value="">请选择</option></select></div><label class="layui-form-label">参数名称：</label><div class="layui-input-inline"><select name="paramdo[]"  class="select"><option value="0">请选择</option><option value="1">入侵报警</option><option value="2">水灾报警</option><option value="3">火灾报警</option></select></div><label class="layui-form-label">参数设值：</label><div class="layui-input-inline"><input class="layui-input" name="paramdonum[]" type="text"></div><label class="layui-form-label">联动方式：</label><div class="layui-input-inline"><select name="linktypedo[]"  class="select"><option value="0">请选择</option><option value="1">与</option><option value="2">或</option><option value="3">非</option> </select></div></div></div></div>';
        $('.conNumGo:last').after(html);
        form.render();
    })

    var jsonStr  = $('#cates').val();

    // var obj = eval("([" + jsonStr + "])");
   // var obj = eval('(' + jsonStr + ')');

    var obj = JSON.parse(jsonStr);

    // console.dir(obj);
    var arr = [];
    function toArr(arr) {
        for (var i=0; i<arr.length; i++) {
            var tmpArr = []
            for (var attr in arr[i]) {
                tmpArr.push(arr[i][attr])
            }
            arr[i] = tmpArr
        }
        return arr
    }
    var arr = toArr(obj);


    // let jsonObj = $.parseJSON(jsonStr);

    /*function json2Array(jsonData) {
        var jsonsString = jsonData.slice(1, jsonData.length - 1);
        var jsonStrings = jsonsString.split("},");
        var length = jsonStrings.length;
        var jsons = [];
        for (var i = 0; i != length-1; ++i) {
            jsonStrings[i] += '}';
        }
        var source = [[]];
        for (var i = 0; i != length; ++i) {
            jsons[i] = eval('(' + jsonStrings[i] + ')');
            var data = [];
            for(var key in jsons[i]) {
                if(i == 0) {
                    source[0].push(key);
                }
                data.push(jsons[i][key]);
            }
            source.push(data);
        }
        return source;
    }

    let arr1=[];
    for(let i in jsonObj){
        //var o={};
        //o[i]=jsonObj[i];
        arr1.push(jsonObj[i]);
    }
    console.dir(arr1);
*/
    var data = [{'安防系统':['AF-RQBJ-01','AF-RQBJ-02','AF-RQBJ-03','AF-RQBJ-04','AF-RQBJ-05','AF-RQBJ-06']}
    ,{'消防系统':['XF-RQBJ-01','XF-RQBJ-02','XF-RQBJ-03','XF-RQBJ-04','XF-RQBJ-05','XF-RQBJ-06']}
    ,{'机器人系统':['JQR-01','JQR-02','JQR-03','JQR-04','JQR-05','JQR-06','JQR-07']}
    ,{'环境控制系统':['HK-WSD-01','HK-WSD-02','HK-WSD-03','HK-WSD-04','HK-WSD-05','HK-WSD-06']}];

    console.dir(data);
    
    form.on('select(TJXT)', function(dataS){
        console.log(dataS.value)
      
     //    $.getJSON("/api/getCity?pid="+data.value, function(data){
      var optionstring = "";
      for(var key in data[dataS.value]){
          $.each(data[dataS.value][key], function(i,item){
           optionstring += "<option>" + item + "</option>";
           });
         $("#SHB").html('<option value=""></option>' + optionstring);
          form.render('select');
        }
      
    });
    form.on('select(TJXT1)', function(dataS){
        console.log(dataS.value)
      
     //    $.getJSON("/api/getCity?pid="+data.value, function(data){
      var optionstring = "";
      for(var key in data[dataS.value]){
          $.each(data[dataS.value][key], function(i,item){
           optionstring += "<option>" + item + "</option>";
           });
         $("#SHB1").html('<option value=""></option>' + optionstring);
          form.render('select');
        }
      
    });
    form.on('select(TJXT11)', function(dataS){
        console.log(dataS.value)
      
     //    $.getJSON("/api/getCity?pid="+data.value, function(data){
      var optionstring = "";
      for(var key in data[dataS.value]){
          $.each(data[dataS.value][key], function(i,item){
           optionstring += "<option>" + item + "</option>";
           });
         $("#SHB11").html('<option value=""></option>' + optionstring);
          form.render('select');
        }
      
    });
    form.on('select(ZXXT)', function(dataS){
        console.log(dataS.value)
      
     //    $.getJSON("/api/getCity?pid="+data.value, function(data){
      var optionstring = "";
      for(var key in data[dataS.value]){
          $.each(data[dataS.value][key], function(i,item){
           optionstring += "<option>" + item + "</option>";
           });
         $("#ZX").html('<option value=""></option>' + optionstring);
          form.render('select');
        }
      
    });
    form.on('select(ZXXT1)', function(dataS){
        console.log(dataS.value)
      
     //    $.getJSON("/api/getCity?pid="+data.value, function(data){
      var optionstring = "";
      for(var key in data[dataS.value]){
          $.each(data[dataS.value][key], function(i,item){
           optionstring += "<option>" + item + "</option>";
           });
         $("#ZX1").html('<option value=""></option>' + optionstring);
          form.render('select');
        }
      
    });
    form.on('select(ZXXT11)', function(dataS){
        console.log(dataS.value)
      
     //    $.getJSON("/api/getCity?pid="+data.value, function(data){
      var optionstring = "";
      for(var key in data[dataS.value]){
          $.each(data[dataS.value][key], function(i,item){
           optionstring += "<option>" + item + "</option>";
           });
         $("#ZX11").html('<option value=""></option>' + optionstring);
          form.render('select');
        }
      
    });

})
    



  // 关闭按钮方法
    $('.close, .cancel').click(function(){
        alert('1')
        // $('.mask, .layer').hide();
        layer.closeAll('loading')

    })



   
});








