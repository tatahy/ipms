// app/searchFormCollapse.js

//searchFormCollapse类
var searchFormCollapse={
	//要求liSet.length==divSet.length
	divSet:$('[data-collapse-switch="div"]').parent(),
	liSet:$('[data-collapse-switch="li"]').parent(),
	//BootStrap3的HTML标签class属性及函数参数值fn	
	bs3Cls:{liShow:'active',divShow:'in',fn:{show:'show',hide:'hide'}},
	/* 状态对象。
	 * multishow，bool，是否为多个组件可同时显示。
	 * type:'main'且multishow:true时，index为数组，记录要显示组件index。
	 * type:'main'且multishow:false时，index为数字，记录要显示组件index。
	 * type:'li'/'div'时，index为数字，值为触发状态改变的组件index。
	 * showArr，array，index为组件的index，value为组件是否显示true/false
	 */
	status;{trigger:{type:'',index:'',multishow:false},showArr:zArr()},
	
	formId:function(){
		let self=this,
			arr=[];
		self.divSet.each(function(index){
			arr[index]=$(this).attr('id');
		});
		return arr;
	},
	//箭头函数定义产生初始数组的方法
	zArr:function(){
		let self=this;
		return Array(slef.formId.length).fill(false);	
	},
	//定义数组的reduce方法中使用的reducer	
	totalTrue:function(acc,cur){
		if(cur){
			acc++;
		}
		return acc;
	},
	getStatus:function() {
		let self=this,
			idx='';
		self.divSet.each(function(){
			idx=self.formId.indexOf($(this).attr('id'));
			//jQ中的hasClass()方法不起作用？
			<!-- $(this).hasClass(self.bs3Cls.divShow)?status.showArr[idx]=true:status.showArr[idx]=false; -->
			$(this).attr('class').indexOf(self.bs3Cls.divShow)!=-1?self.status.showArr[idx]=true:self.status.showArr[idx]=false;
		});
		return self.status;
	},
	reset:function() {
		let self=this;
		self.status= self.setCollapse({self.trigger:{type:'main',index:'0',multishow:''},showArr:zArr()});
		
		return self.status;
	},
	//设定status中的值
	setStatus:function(opt={}) {
		let self=this;
		let type='',
			idx='',
			showVal='';
		
		self.status =$.extend({},self.status,opt);	
		type=self.status.trigger.type;
		idx=self.status.trigger.index;
		
		if(self.status.trigger.multishow==false){
			return self.setStatusSingle();
		}

		if(type=='main'){
			if(idx === ''){
				self.status.showArr=zArr();
				// status.trigger.index=0;
				self.status.trigger.index=[0];
				self.status.showArr[0]=true;
			}
			if(Array.isArray(idx)){
				//根据数组idx改变对应status.showArr的值
				self.status.showArr.forEach(function(v,i){
					if(idx.includes(i)){
						self.status.showArr[i]=!v;
					}
				});
			}
			return self.status;
		}
		
		//根据idx的值修改对应status.showArr值
		if(type=='li'){
			showVal=self.status.showArr[idx];
			self.status.showArr[idx]=!showVal;
		}
		if(type=='div'){
			self.status.showArr[idx]=false;
		}
		//根据status.showArr的值再修改status.trigger.index为数组
		if(self.status.showArr.reduce(totalTrue,0)){
			idx=[];
			self.status.showArr.forEach(function(v,i){
				if(v){
					idx.push(i);
				}
			});
		}
		self.status.trigger.index=idx;
		return self.status;
	};
	//设定status中的值，只能显示一个searchForm及其对应组件
	setStatusSingle:function(){
		let self=this;
		let type=self.status.trigger.type,
			idx=self.status.trigger.index,
			showVal='';;
		
		//只有showIndex[idx]的值有改变，其他的都为false	
		if(type=='li'){
			showVal=self.status.showArr[idx];
			self.status.showArr=zArr();
			self.status.showArr[idx]=!showVal;
		}
		if(type=='main'){
			if(idx === ''){
				self.status.showArr=zArr();
				self.status.trigger.index=0;
				self.status.showArr[0]=true;
			}
			if((typeof idx) == 'number'){
				showVal=self.status.showArr[idx];
				self.status.showArr=zArr();
				self.status.showArr[idx]=!showVal;
			}
		}
		if(type=='div'){
			self.status.showArr[idx]=false;
		}
		return self.status;
	};
	//根据status对有关组件进行显示、隐藏
	setCollapse:function(opt={}) {
		let self=this;
		self.setStatus(opt);
		
		self.status.showArr.forEach(function(v,i){
			if(v){
				self.divSet.eq(i).collapse(self.bs3Cls.fn.show);
				self.liSet.eq(i).addClass(self.bs3Cls.liShow);
			}else{
				self.divSet.eq(i).collapse(self.bs3Cls.fn.hide);
				self.liSet.eq(i).removeClass(self.bs3Cls.liShow);
			}
		});
		<!-- consoleColor('setCollapse():'); -->
		return self.status;
	}
};

$(document).ready(function(){
//实例化对象
let sFCObj=new searchFormCollapse,
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