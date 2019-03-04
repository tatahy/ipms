// app/SearchFormCollapse.class.js

export default class SearchFormCollapse {
	constructor(){
		let self=this;
		self.name=SearchFormCollapse;
		self.divSet=$('[data-collapse-switch="div"]').parent();
		self.liSet=$('[data-collapse-switch="li"]').parent();
		self.bs3Cls={liShow:'active',divShow:'in',fn:{show:'show',hide:'hide'}};
		self.formId=self.initFormId();
		self.status=self.initStatus();		
	}
	/* //定义伪属性formId
	get formId() {
        let arr=[];
		this.divSet.each(function(index){
			arr[index]=$(this).attr('id');
		});
		return arr;
    }
	//定义伪属性status
	get status() {
        return {trigger:{type:'',index:'',multishow:false},showArr:Array(this.formId.length).fill(false)};
    }
	//向伪属性status赋值
	set status(opt) {
		return this.setStatus(opt);
	}*/
	initFormId(){
		let arr=[];
		this.divSet.each(function(index){
			arr[index]=$(this).attr('id');
		});
		return arr;
	} 
	initStatus(){		
		/* 状态对象。
	 * multishow，bool，是否为多个组件可同时显示。
	 * type:'main'且multishow:true时，index为数组，记录要显示组件index。
	 * type:'main'且multishow:false时，index为数字，记录要显示组件index。
	 * type:'li'/'div'时，index为数字，值为触发状态改变的组件index。
	 * showArr，array，index为组件的index，value为组件是否显示true/false
	 */
		return {trigger:{type:'',index:'',multishow:false},showArr:this.zArr()};
    }
	zArr(){
		return Array(this.formId.length).fill(false);
	}
	
	//定义数组的reduce方法中使用的reducer	
	totalTrue(acc,cur){
		if(cur){
			acc++;
		}
		return acc;
	}
	getStatus(){
		let self=this,
			idx='';
		self.divSet.each(function(){
			idx=self.formId.indexOf($(this).attr('id'));
			//jQ中的hasClass()方法不起作用？
			// $(this).hasClass(self.bs3Cls.divShow)?status.showArr[idx]=true:status.showArr[idx]=false; 
			$(this).attr('class').indexOf(self.bs3Cls.divShow)!=-1?self.status.showArr[idx]=true:self.status.showArr[idx]=false;
		});
		return self.status;
	}
	reset() {
		let self=this;
		self.status= self.setCollapse({trigger:{type:'main',index:'0',multishow:''},showArr:self.zArr()});
		
		return self.status;
	}
	//设定status中的值
	setStatus(opt={}) {
		let self=this;
		let type='',
			idx='',
			showVal='',
			status=$.extend({},self.status,opt);
		
		type=status.trigger.type;
		idx=status.trigger.index;
		
		if(status.trigger.multishow==false){
			self.status=status;
			return self.setStatusSingle();
		}

		if(type=='main'){
			if(idx === ''){
				status.showArr=self.zArr();
				// status.trigger.index=0;
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
			
			return self.status=status;
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
		if(status.showArr.reduce(self.totalTrue(),0)){
			idx=[];
			status.showArr.forEach(function(v,i){
				if(v){
					idx.push(i);
				}
			});
		}
		status.trigger.index=idx;
		return self.status=status;
	}
	//设定status中的值，只能显示一个searchForm及其对应组件
	setStatusSingle(){
		let self=this,
			status=self.status;
		let type=status.trigger.type,
			idx=status.trigger.index,
			showVal='';;
		
		//只有showIndex[idx]的值有改变，其他的都为false	
		if(type=='li'){
			showVal=status.showArr[idx];
			status.showArr=self.zArr();
			status.showArr[idx]=!showVal;
		}
		if(type=='main'){
			if(idx === ''){
				status.showArr=self.zArr();
				status.trigger.index=0;
				status.showArr[0]=true;
			}
			if((typeof idx) == 'number'){
				showVal=status.showArr[idx];
				status.showArr=self.zArr();
				status.showArr[idx]=!showVal;
			}
		}
		if(type=='div'){
			status.showArr[idx]=false;
		}
		return self.status=status;
	}
	//根据status对有关组件进行显示、隐藏
	setCollapse(opt={}) {
		let self=this;
		let	status=self.setStatus(opt),
			bs3Cls=self.bs3Cls;
		
		status.showArr.forEach(function(v,i){
			if(v){
				self.divSet.eq(i).collapse(bs3Cls.fn.show);
				self.liSet.eq(i).addClass(bs3Cls.liShow);
			}else{
				self.divSet.eq(i).collapse(bs3Cls.fn.hide);
				self.liSet.eq(i).removeClass(bs3Cls.liShow);
			}
		});
		return self.status=status;
	}
}
