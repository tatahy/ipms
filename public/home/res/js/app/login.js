//login.js

$('[title]').tooltip();   

$(document).ready(function(){
	let fm=$('form'),
		urlLogin=fm.attr('action'),
		urlIndex=fm.data('urlIndex'),
		inUesrName=fm.find('[name="username"]'),
		alertDiv=$('#alertDiv'),
		alertBtn=alertDiv.children('button'),
		alertSp=alertDiv.children('span'),
		errTxt=alertSp.text();
	//alert显示、隐藏函数
	let alertDivHide=(msg='success')=>{
			let hBool=true;
		
			//兼容msg==true
			if(msg!='success' && msg!=true){
				hBool=false;
			}
		
			alertSp.empty().text(msg);
			alertDiv.prop('hidden',hBool);
			
			return hBool;
		};
	//检查设置AlertDiv状态
	let checkAlertDiv=()=>{
		let hBool=alertDiv.prop('hidden'),
			txt=alertSp.text();
		//alertDiv显示
		if(!hBool){
			return inUesrName.focus();
		}
		//有alert内容
		if(txt){
			alertDiv.prop('hidden',false);
			return inUesrName.focus();
		}		
		
	};
	//提交表单数据
	let submitFmData=(data='')=>{	
		
		if(!String(data).length){
			data={salt:String(Math.ceil(Math.random()*10000))};
			fm.find('[name]').each(function(k,v){
				let name=$(v).attr('name'),
					val=$(v).val();
				// d[name]=val; 
				data[name]=val;
			
				if('pwd'==name){
					//增加随机因子加密pwd
					data['pwd']=md5(md5(val)+data.salt);
				}
			});
		}
		
		$.post(urlLogin,data,function(msg){
			//跳转到urlIndex或显示错误
			(msg=='success')?window.location.assign(urlIndex):alertDivHide(msg);
		});
	};
	
	alertBtn.click(function() {
		alertDiv.prop('hidden',true);
		inUesrName.focus();
		inUesrName.one('focusout',function() {
			let val=String($(this).val());
			if(val.length){
				return submitFmData({username:val});
			}
		});
	});
	
	fm.submit(function(evt) {
		evt.preventDefault();	
		return submitFmData();
		
	});
	
	fm.find('[name]').change(function() {
		let bgCls='alert-info',
			val=String($(this).val()),
			name=$(this).attr('name');
		//change增、消底色
		// $(this).addClass(bgCls); 
		// $(this).removeClass(bgCls);
		if(name=='username'){
			return (val.length)?submitFmData({username:val}):alertDiv.prop('hidden',true);
		}
		
	});
	
});