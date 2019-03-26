// app/main.js

//conf.js中采用默认输出
import c from './conf.js';
import {asyInitData,buildTopNavbar,buildEntCharts} from './utility.js';
import {Modal} from './Modal.js';
import {Event as eve} from './Event.js';

export {App};

var App={
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
	initData: async function(){
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
		let self=this;
		//生成组件
		buildTopNavbar();
		buildEntCharts();
		// 激活并设置tooltip
		$('body').tooltip({selector:'[title]',triger:'hover click',placement:'auto top',delay: {show: 200, hide: 100},html: true });
		//设置页脚年份
		$('footer .year').html('2017-'+self.data.year);
	},
	pageReady:function() {
		return eve.init();
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
		// return Modal.small(str);		
	})
	.catch(err=>{
		return App.exit(err);
	});
