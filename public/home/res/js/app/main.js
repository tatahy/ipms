// app/main.js

//conf.js中采用默认输出
import c from './conf.js';

import {asyInitData,pageInit,pageReady} from './utility.js';

import {Modal} from './Modal.js';

export var App={
	data:{
		glyPrex:c.glyPrex,
		bs3Color:c.bs3Color,
		year:c.year,
		userName:c.userName,
		entNum:c.entNum,
		loadStr:c.loadStr,
		topNavProp:c.topNavProp,
		entProp:c.entProp,
		rqData:c.rqData,
		urlObj:c.urlObj,
		searchResultNum:c.searchResultNum
	},
	//定义异步函数进行data的初始化
	initData:async function(){
		let self=this;
		//使用utility.js中的异步函数
		self.data=await asyInitData();
		
		return self.data.userName;
	},
	exit:function(err){
		// $.alert(err);
		console.log(err);
	},
	pageInit:function() {
		return pageInit();
	},
	pageReady:function() {
		return pageReady();
	},
};

App.initData()
	.then(uName=>{
		let str='数据初始化失败。页面无法正常显示。';
		if(!uName){
			return Modal.small(str);
		}
		str='<p></p><div class="text-center" style="font-size:18px;">已成功登录!</div>';
		
		App.pageInit();
		App.pageReady();
		
		return Modal.small(str);		
	})
	.catch(err=>{
		return App.exit(err);
	});