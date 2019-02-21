// app/searchForm.js


// rqData.sortData.sortName='{$sortName}';

//searchFormCollapse类
var searchFormCollapse=function(){
	//要求liSet.length==divSet.length
	var divSet=$('[data-collapse-switch="div"]').parent();
	var liSet=$('[data-collapse-switch="li"]').parent();
	//自调用匿名函数立即执行，进行赋值
	var formId=(function(){
			let arr=[];
			divSet.each(function(index){
				arr[index]=$(this).attr('id');
			});
			return arr;
		}());
	//箭头函数定义产生初始数组的方法
	var zArr=()=>{
			return Array(formId.length).fill(false);	
		};
	//箭头函数，定义数组的reduce方法中使用的reducer	
	var totalTrue=(acc,cur)=>{
			if(cur){
				acc++;
			}
			return acc;
		};
	//BootStrap3的HTML标签class属性及函数参数值fn	
	var bs3Cls={liShow:'active',divShow:'in',fn:{show:'show',hide:'hide'}};
	/* 状态对象。
	 * multishow，bool，是否为多个组件可同时显示。
	 * type:'main'且multishow:true时，index为数组，记录要显示组件index。
	 * type:'main'且multishow:false时，index为数字，记录要显示组件index。
	 * type:'li'/'div'时，index为数字，值为触发状态改变的组件index。
	 * showArr，array，index为组件的index，value为组件是否显示true/false
	 */
	var status={trigger:{type:'',index:'',multishow:false},showArr:zArr()};
	
	var getStatus=()=>{
		let idx='';
		divSet.each(function(){
			idx=formId.indexOf($(this).attr('id'));
			//jQ中的hasClass()方法不起作用？
			<!-- $(this).hasClass(bs3Cls.divShow)?status.showArr[idx]=true:status.showArr[idx]=false; -->
			$(this).attr('class').indexOf(bs3Cls.divShow)!=-1?status.showArr[idx]=true:status.showArr[idx]=false;
		});
		
		return status;
	};
	
	var reset=()=>{
		status=setCollapse({trigger:{type:'main',index:'0',multishow:''},showArr:zArr()});
		
		return status;
	};
	//设定status中的值
	var setStatus=(opt={})=>{
		let type='',
			idx='',
			showVal='';
		
		status =$.extend({},status,opt);	
		type=status.trigger.type;
		idx=status.trigger.index;
		
		if(status.trigger.multishow==false){
			return setStatusSingle();
		}

		if(type=='main'){
			if(idx === ''){
				status.showArr=zArr();
				<!-- status.trigger.index=0; -->
				status.trigger.index=[0];
				status.showArr[0]=true;
			}
			if(Array.isArray(idx)){
				//根据数组idx改变对应status.showArr的值
				status.showArr.forEach(function(v,i){
					if(idx.includes(i)){
						status.showArr[i]=!v;
					}
				});
			}
			return status;
		}
		
		//根据idx的值修改对应status.showArr值
		if(type=='li'){
			showVal=status.showArr[idx];
			status.showArr[idx]=!showVal;
		}
		if(type=='div'){
			status.showArr[idx]=false;
		}
		//根据status.showArr的值再修改status.trigger.index为数组
		if(status.showArr.reduce(totalTrue,0)){
			idx=[];
			status.showArr.forEach(function(v,i){
				if(v){
					idx.push(i);
				}
			});
		}
		status.trigger.index=idx;
		return status;
	};
	//设定status中的值，只能显示一个searchForm及其对应组件
	var setStatusSingle=()=>{
		let type=status.trigger.type,
			idx=status.trigger.index,
			showVal='';;
		
		//只有showIndex[idx]的值有改变，其他的都为false	
		if(type=='li'){
			showVal=status.showArr[idx];
			status.showArr=zArr();
			status.showArr[idx]=!showVal;
		}
		if(type=='main'){
			if(idx === ''){
				status.showArr=zArr();
				status.trigger.index=0;
				status.showArr[0]=true;
			}
			if((typeof idx) == 'number'){
				showVal=status.showArr[idx];
				status.showArr=zArr();
				status.showArr[idx]=!showVal;
			}
		}
		if(type=='div'){
			status.showArr[idx]=false;
		}
		return status;
	};
	//根据status对有关组件进行显示、隐藏
	var setCollapse=(opt={})=>{
		setStatus(opt);
		
		status.showArr.forEach(function(v,i){
			if(v){
				divSet.eq(i).collapse(bs3Cls.fn.show);
				liSet.eq(i).addClass(bs3Cls.liShow);
			}else{
				divSet.eq(i).collapse(bs3Cls.fn.hide);
				liSet.eq(i).removeClass(bs3Cls.liShow);
			}
		});
		<!-- consoleColor('setCollapse():'); -->
		return status;
	};
	
	return {
		formId:formId,
		//自调用匿名函数立即执行
		zArr:(function(){let arr=zArr(); return arr;}()),
		status:status,
		//返回本对象，实现链式操作。
		// setStatus:function(opt){ setStatus(opt); return this;},
		setCollapse:function(opt){return setCollapse(opt);},
		reset:function(){return reset();}
	}
};

$(document).ready(function(){
//实例化对象
let sFCObj=searchFormCollapse(),
	fmId=sFCObj.formId,
	trgObj=sFCObj.status.trigger;
	
let fmCom=$('form.common');

fmCom.find('.form-control').change(function(){
	//有变化加底色
	($(this).val()=='' || $(this).val()==0)?$(this).removeClass('alert-info'):$(this).addClass('alert-info');
});
//表单提交
fmCom.submit(function(evt){
	evt.preventDefault();
	//设置查询数据
	setRqSearchDataBy(fmCom);
	consoleColor('searchForm.js fmCom.submit, rqData:');
	console.log(rqData);
	//载入patList
	loadEntPeriodList();
});
//表单重置时附加的操作
fmCom.find('[type="reset"]').click(function(evt){
	resetSearchForm();
});

// 页面刷新 
$('.btnPageRefresh').click(function(){
	resetSearchForm();
	sFCObj.reset();
	loadEntPeriodList();
});	

//3类共5个collapse-switch组件的click事件处理函数，
//任意时刻只有一个组件能click。记录触发click的组件特征值，再由特征值决定组件的显示
$('[data-collapse-switch]').click(function(){
	//特征值1
	let	type=$(this).data('collapseSwitch');
	//特征值2
	let	idx='';
	//特征值3
	let	mul=$('[data-collapse-switch="main"]').data('collapseMultishow');
	
	if(type=='li'){	
		idx=fmId.indexOf($(this).attr('href').slice(1));
	}
	if(type=='div'){
		idx=fmId.indexOf($(this).data('target').slice(1));
	}
	if(type=='main'){
		idx=trgObj.index;
	}	
	
	trgObj.type=type;
	trgObj.index=idx;
	trgObj.multishow=mul;
	
	sFCObj.setCollapse({trigger:trgObj});
});
});