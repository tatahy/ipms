
// app/Event.js

//导入index.js中定义的变量、函数
// import {rqData,resetSearchForm,asyRefreshEntObj,setRqSearchDataBy,setTrBgColor,entGetReady,consoleColor} from './index.js';
import {App} from './main.js';
import {resetSearchForm,asyRefreshEntObj,setRqSearchDataBy,setTrBgColor,entGetReady,consoleColor} from './utility.js';

//定义导出对象Event。封装各个事件处理函数
export var Event={
	//BarcodeForm的event
	barcodeForm:function() {
		//动态加载模块
		import('./Barcode.js')
		.then(module=>{
			module.Barcode.init($('#fmBarcode'));
		})
		.catch(err=>{
			console.log(err);
		});		
	},
	//多个查询表单的显示隐藏
	clpsSwitch:function() {	

		//动态加载类
		import('./SearchFormCollapse.class.js')
		.then(cls=>{
			let clpsSwitchSet=$('[data-collapse-switch]');
			let sFCObj=new cls.default;
			let fmId=sFCObj.formId,
				trgObj=sFCObj.status.trigger;
			//3类共5个collapse-switch组件的click事件，
			//任意时刻只有一个组件能click。记录触发click的组件特征值，再由特征值决定组件的显示
			clpsSwitchSet.click(function(){
				let fmSet=$('#entSearchForm form');
				let mSwitch=$('[data-collapse-switch="main"]');
				//特征值1
				let	type=$(this).data('collapseSwitch');
				//特征值2
				let	idx='';
				//特征值3
				let	mul=mSwitch.length?mSwitch.data('collapseMultishow'):false;
	
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
	
		})
		.catch(err=>{
			console.log(err);
		});	
		// };
	},
	//一般查询表单的event
	queryForm: function() {
		let d=App.data;
		let fmQ=$('form.fmQuery');
	
		fmQ.find('.form-control').change(function(){
			//有变化加底色
			($(this).val()=='' || $(this).val()==0)?$(this).removeClass('alert-info'):$(this).addClass('alert-info');
		});
		//表单提交
		fmQ.submit(function(evt){
			evt.preventDefault();
			//设置查询数据
			setRqSearchDataBy(fmQ);
		
			//异步更新list、queryForm
			asyRefreshEntObj();
		});
		//表单重置时附加的操作
		fmQ.find('[type="reset"]').click(function(evt){
			let len=Object.keys(d.rqData.searchData).length;
			resetSearchForm();
		
			//异步更新list、queryForm
			asyRefreshEntObj();
		});
	},
	//list的event
	list:function() {
		let d=App.data;
		let tblNod=$('#entList table'),
			listRowNod=$('#listRows'),
			aHeadSet=tblNod.find('thead a'),
			aBodySet=tblNod.find('tbody a'),
			aPageSet=$('#divListRows').find('a');
	
		//表格每页显示记录行数；表格按选定行数显示
		listRowNod.val(d.rqData.sortData.listRows).change(function(){
			//排序有关的值向sortData汇集
			d.rqData.sortData.listRows=$(this).val()*1;
			//分页从第一页开始
			d.rqData.sortData.pageNum=1;
		
			//异步更新list
			asyRefreshEntObj('list');
		
		});	
		//表格按选定字段排序
		aHeadSet.click(function(){
			let sortName=$(this).data('sortName');

			d.rqData.sortData.sortOrder=(d.rqData.sortData.sortOrder=='asc')?'desc':'asc';
	
			//排序有关的值向sortData汇集
			if(d.rqData.sortData.sortName!=sortName){
				d.rqData.sortData.sortName=sortName;
				d.rqData.sortData.sortOrder='asc';	
			}
			d.rqData.sortData.pageNum=1;
	
			//异步更新list
			asyRefreshEntObj('list');
		});	
		//表格中点击a后，标签所在行上底色
		aBodySet.click(function(){
			d.rqData.sortData.showId=$(this).closest('tr').data('showId');
		
			return setTrBgColor();
		});
		//表格显示分页内容
		aPageSet.click(function(evt){
			// a所在分页页数
			let pageStr=$(this).text(),
				pageNum=0,
				showId=$('#entList').find('tbody tr').eq(0).data('showId'),
			//(li.active)所代表的页数
				pageNumActive=$('#divListRows li.active').children('span').text(),
				sortName=$('thead').find('.label').data('sortName');
			evt.preventDefault();
			//用"*"确保进行数字运算，而不是字符串
			switch(pageStr){
				case'»':
					pageNum=(pageNumActive*1+1);
					break;
				case'«':
					pageNum=(pageNumActive*1-1);
					break;
				default:
					pageNum=pageStr*1;
					break;
			}
			//排序、分页有关的值向sortData汇集
			if(pageNum){
				d.rqData.sortData.pageNum=pageNum;
			}
		
			//异步更新list
			asyRefreshEntObj('list');
		});
	},
	//页面刷新
	pageRefresh:function() {
		let refreshBtn=$('.btnPageRefresh');
		refreshBtn.click(function(){
			resetSearchForm();
			return entGetReady();
		});	
	},
	//周期导航菜单click事件
	entPeriod:function() {
		let d=App.data;
		let periodASet=$('[data-period]');
		
		periodASet.click(function(){
			let sData=$(this).data();
			$(this).tab('show');
			if(d.rqData.period!=sData.period){
				d.rqData.ent=sData.ent;
				d.rqData.period=sData.period;

				//异步更新list、queryForm
				asyRefreshEntObj();	
			}
		});
	}
};
