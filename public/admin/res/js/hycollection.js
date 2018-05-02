//hy,jQuery function collection

//2018-04-29

//自调用匿名函数
;(function($){
	
})(jQuery);

//jQuery version:1.12.4 
//**函数名：ajaxSelectOption
 	//* 作用：本页面ajax过程向服务器请求特定单选框内容所需数据，并组装好HTML语句
	//* 参数obj，类型：对象。值：不为空。说明：调用函数的select对象。
	//* 参数param，类型：json。值：param={"async":true,"url":"selectResponse","ajdxdata":{"req":"_DEPT"},"first":{"val":"0","text":"t"},"show":{"val":"1,2,3","text":"t"},"remove":{"val":"v","text":"t"},"selected":{"val":"v","text":"t"},'multiple':0}
	//*param.async,类型：Boolean。值：必须。说明：函数同步/异步操作。	
	//*param.url,类型：字符串。值：必须。说明：函数中ajax操作的URL。	//*param.ajaxdata,类型：json。值：必须。说明：函数中ajax传送的data。	//*param.first,类型：json。值：默认为{"val":"0","text":"第一项"}。说明：调用函数后创建的第一项option的val值（字符串），text值（字符串）。
	//*param.show,类型：json。值：默认为0。说明：调用函数后需要的option项val值字符串，text字符串。
	//*param.remove,类型：json。值：默认为0。说明：调用函数后要不需要的option项val值字符串，text字符串。
	//*param.selected,类型：json。值：默认为0。说明：调用函数后选定的option项val值字符串，text字符串。
	//*param.multiple,类型：boolean。值：默认为0。说明：调用函数后obj是否为单选框，默认为单选框。
	//* 返回值：optionHtml
function ajaxSelectOption(obj,param){
	var defaultParam={"async":true,"url":0,"ajaxData":0,"first":{"val":"0","text":"first"},"show":0,"remove":0,"selected":0,'multiple':0};
    param=$.extend({},defaultParam,param); 
    var str='';
	var optionHtml='';
	$.ajax({
		type : 'post',
		async : param.async,  //true:异步请求，false：同步请求
		url : param.url,
		data : param.ajaxData,
		timeout:2000,
		<!-- // 指定服务器端response的数据类型为json-->
		dataType:'json',
		success:function(backData){
			var str='<option value="'+param.first.val+'">'+param.first.text+'</option>';
			<!-- // 遍历backData数组组装HTML语句 -->
			<!-- // backData的结构是数组类的：[{id:1,name:"dept1", abbr:"d1"},{}],所以要进行嵌套取出id，name和abbr的值组装HTML语句-->
			$.each(backData,function(n1,v1){
				$.each(v1,function(n2,v2){
					//根据data的内容安排backData
					switch(param.ajaxData.req){
						case '_DEPT':
							if(n2=='name'){
								str+='<option value="'+v2+'">'+v2+' （简称：';
							}
							if(n2=='abbr'){
								str+=v2+'）</option>';
							}
						break;
						
						case '_USERGROUP':
							if(n2=='id'){
								str+='<option value="'+v2+'">';
							}
							if(n2=='name'){
								str+=v2+'</option>';
							}
						break;	
					}
				});
			});
			obj.empty();
			obj.append(str);
			
			//showSelectOption
			if(param.multiple==1){
        		obj.attr('multiple',true);
    		}else{
				obj.attr('multiple',false);
			}
			if(param.show!=0){
        		obj.find('option').each(function(){
					if(param.show.val.indexOf($(this).val())==-1 && $(this).val()!=0){
            			$(this).remove();
        			}
    			});
				obj.closest('.form-group').find('.spNum').text(obj.find('option').length-1);
				
    		}
			if(param.remove!=0){
				obj.find('option').each(function(){
					if(param.remove.val.indexOf($(this).val())!=-1 && $(this).val()!=0){
            			$(this).remove();
        			}
    			});
				obj.closest('.form-group').find('.spNum').text(obj.find('option').length-1);
    		}
    		if(param.selected==0){
        		//第一项为选中项
				obj.find('option:first').attr('selected','selected');
    		}else{
				//param.selected.val是option的value值字符串，设定值在param.selected.val中的option为选定项
				obj.find('option').each(function(){
	    			if(param.selected.val.indexOf($(this).val())!=-1){
                		$(this).attr('selected',true);
            		}else{
                		$(this).attr('selected',false);
           			}
				});	
    		}
			
			optionHtml=obj.html();
		},
		error: function() {
            $.alert('与服务器通讯超时，请稍后再试！');
        }
	});
	//同步就返回值
	if(param.async==false){
		return optionHtml;
	}
}

//jQuery version:1.12.4 
//**函数名：ajaxProcess
 	//* 作用：本页面ajax过程
	//* 参数url，类型：字符串。值：不为空。说明：ajax操作的URL。
	//* 参数data，类型：数值。值：传送给后端的键值对数据。
	//* 参数archor，类型：字符串。值：本页面锚点值：window.location.hash。
	//* 返回值：无
function ajaxProcess(url,data,archor){
	$.ajax({
		type: 'post',
        url: url,
        data: data,
		timeout:2000,
		<!-- datatype: 'JSON', -->
        contentType: false,// 当有文件要上传时，此项是必须的，否则会导致后台无法识别文件流的起始位置
        processData: false,// 是否序列化data属性，默认true(注意：false时type必须是post)。当使用FormData()，必须设为false，否则jqurey默认将FormData()转换为查询字符串。
		success: function(dataBack){
			<!-- $('.modal').modal('hide'); -->
			//dataBack的5个返回值id,result,name,msg,msgPatch
			if(dataBack.result=='success'){
				//ajax更新页面信息
				$('#content').load(
					'tplFile',
					{'sId':archor},
					function(){
						//所在行上色'bg-warning'
						$('button[data-id="'+dataBack.id+'"]').closest('tr').addClass('bg-warning').focus();
						$.alert('用户【'+dataBack.name+'】<span class="label label-success">'+dataBack.msg+'</span><br>'+dataBack.msgPatch);
					}
				);
			}else{
				$.alert('用户【'+dataBack.name+'】<span class="label label-danger">'+dataBack.msg+'</span><br>'+dataBack.msgPatch);	
			}
		},
		error: function(){
			$.alert('与服务器通讯超时，请稍后再试！');
		}
	});	
}
