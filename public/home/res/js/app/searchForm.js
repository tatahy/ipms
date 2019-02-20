// app/searchForm.js

let fmCom=$('form.common');

fmCom.find('.form-control').change(function(){
	//有变化加底色
	($(this).val()=='' || $(this).val()==0)?$(this).removeClass('alert-info'):$(this).addClass('alert-info');
});
//表单提交
fmCom.submit(function(evt){
	evt.preventDefault();
	//设置查询数据
	setRqSearchData(fmCom);
	console.log(rqData);
	//载入patList
	loadEntPeriodList();
});
//表单重置时附加的操作
fmCom.find('[type="reset"]').click(function(evt){
	resetSearchForm();
});
// });

//设定搜索表单的各项内容
function setFmSearchData(opt={}){
	opt=$.extend({},{topic:'',author:'',type:0,dept:0,status:0},opt);
	
	for(let el in opt){
		let obj=fmCom.find('[name="'+el+'"]'),
			v=opt[el];
		//select为多选
		if(typeof v== 'object'){
			v.forEach(function(item){
				obj.find('option[value="'+item+'"]').prop('selected',true);
			});
		}
		//select为单选
		obj.val(v);
		
		if(v=='' || v==0){
			obj.removeClass('alert-info');
		}else{
			//不为0或空加底色
			obj.addClass('alert-info');
		}
	}
}
