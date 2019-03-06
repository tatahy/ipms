// app/main.js

//conf.js中采用默认输出，c为本文件中使用的hash对象，其键值为'./conf.js'各个导出键值对
import c from './conf.js';

import {SearchFormCollapse} from './SearchFormCollapse.class.js';
import {Barcode} from './Barcode.js';

var App={
	data:{
		glyPrex:c.glyPrex,
		bs3Color:c.bs3Color,
		year:c.year,
		entNum:c.entNum,
		loadStr:c.loadStr,
		topNavProp:c.topNavProp,
		entProp:c.entProp,
		rqData:c.rqData,
		urlObj:c.urlObj,
		searchResultNum:c.searchResultNum,
		sfcCls:SearchFormCollapse,
		Barcode:Barcode
	},
	initData:async function initData(){
		let d=this.data;
		let resData = await $.post('index/index/index',{getPageInitData:true});
		
	/* Promise.all(resData.urlObj.domain).then(function(val){
		console.log(val);
	}); */
	
		console.log(resData);
		//全局变量赋值
		d.urlObj=resData.urlObj;
		d.entNum=resData.entNum;
		d.rqData=initRqData();
		//完善entProp中的num属性值
		for(let ent in d.entProp){
			for(let per in d.entProp[ent].period.detail){
				d.entProp[ent].period.detail[per].title.num=d.entNum[ent][per];
			}
		}
	},
	exit:function(err){
		$.alert(err);
	},
	start:async function start(){
		let self=this;
		let result=await self.initData();
		// let val=await Promise.all([result1,result2]);
		
		if(result){
			self.pageInit();
			self.pageReady();
		}
	},
	pageInit:function() {
		
	},
	pageReady:function() {
		
	},
};

/* App.initData().then(
	App.pageInit();
	App.pageReady();
).catch(
	App.exit(err);
);
 */
App.start().catch(
	App.exit(err)
);


