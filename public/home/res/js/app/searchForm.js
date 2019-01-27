// app/searchForm.js

let fm=$('[id="'+rqData.ent+'SearchForm"]').find('form');

buildFmSelectCom();

fm.find('.form-control').change(function(){
	//有变化加底色
	($(this).val()=='' || $(this).val()==0)?$(this).removeClass('alert-info'):$(this).addClass('alert-info');
});
//表单提交
fm.submit(function(evt){
	evt.preventDefault();
	//设置查询数据
	setRqSearchData(fm);
	console.log(rqData);
	//载入patList
	loadEntPeriodList();
});
//表单重置时附加的操作
fm.find('[type="reset"]').click(function(evt){
	resetSearchForm();
});
// });

//设定搜索表单的各项内容
function setFmSearchData(opt={}){
	opt=$.extend({},{topic:'',author:'',type:0,dept:0,status:0},opt);
	
	for(let el in opt){
		let obj=fm.find('[name="'+el+'"]'),
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
//挨个组装好所有的select组件
function  buildFmSelectCom(){
	let selSet=fm.find('select'),
		rData={period:rqData.period,name:[]};
	urlObj.method='getSelComData';

	selSet.each(function(){
		rData.name.push($(this).attr('name'));
	});
	//向后端请求组装select组件所需数据
	$.post(getRqUrl(),rData,function(resData){
		// console.log(resData);
		for(var i=0;i<selSet.length;i++){
			let selObj=selSet.eq(i),
				selName=selObj.attr('name'),
				optionArr=resData[selName];
			
			selObj.empty().append($('<option></option>').val(0).text('…不限'));	
			//组装option 
			optionArr.forEach(function(e){
				selObj.append($('<option></option>').val(e.val).text(e.txt));
			});
			selObj.val(0);		
		}
		//设定搜索表单的各项内容，要完成组装后才能生效。
		setFmSearchData(rqData.searchData);
	});
}