
// app/Event.js

import {App} from './main.js';
import {initRqData,setRqData,setRqSearchDataBy,setRqQueryFieldBy,consoleColor,getRqUrl} from './utility.js';
import {Modal} from './Modal.js';
import Barcode from './Barcode.js';
import sFCCls from './SearchFormCollapse.class.js';

export {Event,resetSearchForm,asyRefreshEntObj};

//定义导出对象Event。封装各个事件处理函数
var Event={
	init:function() {
		let self=this;
		let d=App.data;
		// let rData=App.data.rqData;
		let sumNod=$('#entChart').show(),
			perNod=$('#entPeriod').hide();
		let entASet=$('nav .navbar-collapse ul').eq(0).find('a'),
			btnEntPeriod=sumNod.find('.btnPeriod'),
			btnTopnavToggle=$('nav .navbar-header').children('.navbar-toggle');
		let getReady=(ent,period='')=>{
				initRqData();
				d.rqData.ent=ent;
				d.rqData.period=!period?d.topNavProp[ent].period:period;
				if(ent!='index'){
					//生成ent的nav-pills
					buildEntPeriodNavPills();
					//生成ent的period的title
					buildEntPeriodTitle();
					return self.reloadEvent();
				}
			};
	
		btnTopnavToggle.click(function(){
			showTopNavbar();
		});

		entASet.click(function(){
			let cls=$(this).closest('li').attr('class')?$(this).closest('li').attr('class'):'no class',
				ent=$(this).data('ent');
		
			if(cls.indexOf('disabled')!=-1 ){
				return true;			
			}
		
			$(this).tab('show');
			sumNod.hide();
			perNod.hide();
			
			ent=='index'?sumNod.show():perNod.show();
		
			if(d.rqData.ent!=ent){
				return getReady(ent);
			}
		});
	
		btnEntPeriod.click(function(){
			let ent=$(this).data('ent');
			let period=$(this).data('period');
			sumNod.hide();
			perNod.show();
		
			//ent对应的navTop的li添加'active'
			entASet.closest('li').removeClass('active').find('[data-ent="'+ent+'"]').tab('show');
			return getReady(ent,period);
		});		
	},
	addEvent:function(){
		let self=this;
		let clpsSwitchSet=$('[data-collapse-switch]'),
		nodBarcode=$('#divFmBarcode');
	
		self.pageRefresh();	
	
		self.entPeriod();
	
		//其他的event
		if(nodBarcode.length){
			self.barcodeForm();
		}
		
		if(clpsSwitchSet.length){
			self.clpsSwitch();
		}
	
		self.list();
	
		self.queryForm();
	},
	//使用Promise对象实现异步的顺序处理？？
	reloadEvent:function() {
		let self=this;
		asyEntLoad()
		//已成功加载list和form
		.then(result=>{
		
			//设置rqData
			setRqData();
			//加载addEvent
			// addEntEvent();
			self.addEvent();
		})
		.catch(err=>{console.log(err);});
	},
	//BarcodeForm的event
	/* barcodeForm:function() {
		//动态加载模块
		import('./Barcode.js')
		.then(module=>{
			let Barcode=module.Barcode;
			Barcode.init($('#fmBarcode'));
		})
		.catch(err=>{
			console.log(err);
		});			
	}, */
	//多个查询表单的显示隐藏
	/* clpsSwitch:function() {	
		//动态加载类
		import('./SearchFormCollapse.class.js')
		.then(sFCCls=>{
			let clpsSwitchSet=$('[data-collapse-switch]');
			let sFCObj=new sFCCls.default;
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
	}, */
	barcodeForm:function() {
		Barcode.init($('#fmBarcode'));
	},
	//多个查询表单的显示隐藏
	clpsSwitch:function() {	
		
		let clpsSwitchSet=$('[data-collapse-switch]');
		let sFCObj=new sFCCls;
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
		let self=this;
		let d=App.data;
		let tblNod=$('#entList table');
		let	aHeadSet=tblNod.find('thead a'),
			aBodySet=tblNod.find('tbody a'),
			shCheckBoxSet=tblNod.find('[name="sheetId"]'),
			aPageSet=$('#divListRows').find('a'),
			listRowNod=$('#listRows'),
			shRadioNod=$('#divSheetMode').find('input'),
			shSelectNod=$('#divSheetType').find('select'),
			btnOutputFile=$('#divSheetType').find('button');
		
		setEntPeriodList();	
		
		shRadioNod.click(function(){
			let mode=$(this).val();
			if(mode!=App.data.rqData.sheet.mode){
				return setSheetChkRdCom(mode);
			}
		});
		shSelectNod.change(function(){
			App.data.rqData.sheet.type=$(this).val();
		});
		shCheckBoxSet.click(function(){
			let id=parseInt($(this).val());
			let arr=App.data.rqData.sheet.idArr;
			let index=arr.indexOf(id);
			//添加id值，没选中且不在数组内
			if(!$(this).prop('checked') && index==-1){
				arr.push(id);
			}
			//删除id值，选中且在数组内
			if($(this).prop('checked') && index!=-1){
				arr.splice(index, 1);
			}
			
			App.data.rqData.sheet.idArr=arr;
			
		});
		
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
		btnOutputFile.click(function(){
			let urlObj=App.data.urlObj;
			
			urlObj.ctrl='list';
			urlObj.action='listFileMake';
			//导出表格的标题行信息
			App.data.rqData.sheet.head=getListSortField();
			
			$.post(getRqUrl(urlObj),App.data.rqData,function(res){
				let aNod=$('<a></a>').css('font-size','16px');
				let pNod=$('<p></p>').addClass('text-center');
				let opt={headBg:'bg-warning',title:'记录导出失败'};
				if(res.result){
					urlObj.action='listFileDownload';
					//生成下载文件的链接
					aNod.attr({'href':getRqUrl(urlObj)+'/fileName/'+res.msg,'target':'_self',title:'点击下载文件'}).text('下载导出文件');

					opt.headBg='bg-info';
					opt.title='记录导出成功';
				}else{
					aNod.addClass('text-warning').text(res.msg);
				}
				// Modal.small(pNod.append($('<br />'),aNod),opt);
				Modal.large(pNod.append($('<br />'),aNod),opt);
				
				Modal.addEvent('hidden.bs.modal',function(){
					let fName=res.msg;
					let flag=res.result;
					
					if(flag){
						urlObj.ctrl='list';
						urlObj.action='listFileDelete';
						//发出删除文件的请求
						$.post(getRqUrl(urlObj),{fileName:fName});
					}
				});
			});
			
		});
	},
	
	//页面刷新
	pageRefresh:function() {
		let self=this;
		let refreshBtn=$('.btnPageRefresh');
		refreshBtn.click(function(){
			resetSearchForm();
			return self.reloadEvent();
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
				//更新标题
				buildEntPeriodTitle();
				//异步更新list、queryForm
				return asyRefreshEntObj();	
			}
		});
	}
};

//所有查询表单重置
function resetSearchForm(){
	let fm=$('form');
	
	$('#searchNum').children().hide();
	$('#entList').show();
	
	fm.each(function(){
		$(this)[0].reset();
		$(this).find('.form-control').removeClass('alert-info');
		// console.log($(this).siblings().length);
		if($(this).data('formType')=='barcode'){
			$(this).siblings().hide();
		}
		// if($(this).data('formType')=='query'){
			// asyRefreshEntObj();
		// }
	});
	//清空查询数据
	return setRqSearchDataBy();
}

//设置list
function setEntPeriodList(){	
	let d=App.data;
	let rData=d.rqData;
	let ent=rData.ent,
		period=rData.period,
		periodChi=d.entProp[ent].period.detail[period].txt;

	//显示分页的第一页
	rData.sortData.pageNum=1;
	
	//tbl标题加内容
	$('#entList table').find('caption strong').text(periodChi);
	//tbl排序	
	sortEntListTbl();
	//指定行加底色	
	setTrBgColor();
	//设定entList的checkBox、radio组件
	setSheetChkRdCom();
	//显示搜索结果数
	showSearchResult();	
}
//对ent List进行排序
//列表字段进行升序降序转换，改变该字段表头显示格式
function sortEntListTbl() {
	let rData=App.data.rqData;
	let glyAsc=$('<span></span>').addClass('small glyphicon glyphicon-sort-by-attributes'),
		glyDesc=$('<span></span>').addClass('small glyphicon glyphicon-sort-by-order-alt'),
		sortOrder=rData.sortData.sortOrder,
		sortName=rData.sortData.sortName,
		tblNod=$('#entList table'),
		aHSet=tblNod.find('[data-sort-name]'),
		trSet=tblNod.find('tr'),
		column='',
		columnArr=['num'];
	
	if(sortName==''){
		sortName=aHSet.eq(0).data('sortName');
		rData.sortData.sortName=sortName;
	}
	
	aHSet.each(function(){
		let gly=(sortOrder == 'asc')?glyAsc:glyDesc,
			sort=(sortOrder == 'asc')?'desc':'asc',
			title=(sortOrder == 'asc')?'当前为升序。点击后降序':'当前为降序。点击后升序',
			column=$(this).data('sortName');
		columnArr.push(column);
		//每个添加属性
		$(this).removeClass('label label-primary').attr({'data-period':rData.period,'title':'点击排序'}).css({'cursor':'pointer','font-size':'14px'},).closest('th').addClass('text-center');
		
		(sortName==column)?$(this).attr({'class':'label label-primary','data-sort-order':sort,'title':title}).tooltip({placement:'top'}).append('&nbsp;',gly):$(this).tooltip({placement:'bottom'});	
		
	});
	//每个单元格添加data-column=""，相同的data-column用于组合成一个column
	columnArr.forEach(function(el,index){
		trSet.each(function(){ 
			$(this).children().eq(index).attr('data-column',el);
		});	
	});
		
	//选中进行排序的列(除标题行外的)向左对齐，添加底色
	tblNod.find('tbody [data-column="'+sortName+'"]').addClass('text-left bg-info');
}

function setTrBgColor() {
	let id=App.data.rqData.sortData.showId,
		nod=$('#entList table');
	//去掉所有行的底色
	nod.find('tr').removeClass('bg-warning');
	
	//给showId所在行上底色
	nod.find('[data-show-id="'+id+'"]').addClass('bg-warning');
}
//设定entList的checkBox、radio组件
function setSheetChkRdCom(mode='') {
	let sheet=App.data.rqData.sheet;
	let modeArr=['none','all','excluded'];
	let sheetIdSet=$('#entList table').find('[name="sheetId"]'),
		sheetTypeRoot=$('#divSheetType'),
		sheetTypeNod=sheetTypeRoot.find('select'),
		sheetModeRoot=$('#divSheetMode'),
		labStr='label-primary';
	
	if(modeArr.indexOf(mode)==-1){
		mode=sheet.mode;
	}else{
		sheet.mode=mode;
	}
	//默认显示（mode=='none'）
	sheetTypeRoot.prop('hidden',true);
	sheetIdSet.css('margin',0).prop({'checked':false,'disabled':false}).parent().prop('hidden',true).css('margin',0);
	sheet.type='';
	if(mode=="none"){		
		sheet.idArr=[];
		labStr='label-default';
	}
	
	if(mode=="all"){
		sheetTypeRoot.prop('hidden',false);
		sheetIdSet.prop({'checked':true,'disabled':true}).parent().prop('hidden',false);
		sheet.type=sheetTypeNod.val();
		sheet.idArr=[];
	}
	if(mode=="excluded"){
		sheetTypeRoot.prop('hidden',false);
		sheetIdSet.prop('checked',true).parent().prop('hidden',false);
		sheet.type=sheetTypeNod.val();
	}
	//全局sheet变量重新赋值
	App.data.rqData.sheet=sheet;
	
	//恢复radio中label的class初始值
	sheetModeRoot.find('label').attr('class','radio-inline label label-default');
	//设定radio选中项及添加label的class值
	sheetModeRoot.find('[value="'+mode+'"]').prop('checked',true).parent().removeClass('label-default').addClass(labStr);
	
	return sheet;
}

function showSearchResult() {
	let d=App.data;
	let bingoObj=$('#searchNum .bingo'),
		noneObj=$('#searchNum .none'),
		listObj=$('#entList'),
		sData=d.rqData.searchData,
		//计数器
		n=0;
	d.searchResultNum=parseInt($('#searchResultNum').text());
	
	bingoObj.find('.badge').text(d.searchResultNum);
	for(let el in sData){
		n=(sData[el])?n+1:n;
	}
	
	//搜索结果显示
	bingoObj.hide();
	noneObj.hide();
	listObj.hide();
	if(n==0) {
		listObj.show();
	}
	if(n && d.searchResultNum){
		bingoObj.show();
		listObj.show();
		
		// Modal.small({cont:'<div class="text-center"><button class="btn btn-warning btn-sm" style="cursor:unset;font-size:14px;">搜索结果：<span class="badge">'+d.searchResultNum+'</span></button></div>'});
		
		$.alert('<div class="text-center"><button class="btn btn-warning btn-sm" style="cursor:unset;font-size:14px;">搜索结果：<span class="badge">'+d.searchResultNum+'</span></button></div>');

	}
	if(n && d.searchResultNum==0) {
		$.alert('<div class="text-center"><span class="label label-default" style="cursor:unset;font-size:14px;">无符合条件信息</span></div>');
		noneObj.show();
	}
}
//得到列表的排序字段en和chi值
function getListSortField(){
	let aSet=$('#entList').find('thead [data-sort-name]');
	let field={fieldEn:[],fieldChi:[]};
	
	aSet.each(function(index,el){
		field.fieldEn[index]=$(el).data('sortName');
		field.fieldChi[index]=$(el).text();
	});
	
	return field;
}

//显示页面顶端菜单栏
function showTopNavbar() {
	let topNavObj=$('body').find('.navbar-collapse');
	
	if($('body').find('.topnav-backdrop').length){
		$('.topnav-backdrop').remove();
		return topNavObj.removeClass('in');
	}
	topNavObj.addClass('in');
	//添加div组件及其click事件函数，实现点击该组件就隐藏topnav	
	return $('body').append($('<div></div>').addClass('topnav-backdrop').one('click',function(){
    		showTopNavbar();
		})
	);
}
//构建title组件
function buildEntPeriodTitle(){
	let d=App.data;
	let rData=d.rqData;
	let ent=(rData.ent=='index')?'pat':rData.ent,
		tProp=d.entProp[ent].period.detail,
		spObj=$('<span></span>').addClass('btn').css({'cursor':'unset'}),
		spBdg=$('<span></span>').addClass('badge'),
		//生成title的根节点
		nod=$('#entPeriod').children('h4.title');
		// obj=$('[id="'+ent+'Title"]');
	nod.empty();
	for(let p in tProp){
		let e=tProp[p].title;
		if(p==rData.period){
			spObj.addClass(e.btn).append(e.txt,spBdg.text(e.num));
			if(e.btn=='btn-default'){
				spObj.css('backgroundColor','#ccc');
			}
			nod.empty().append(spObj);
			
			
			break;
		}
	}
}
//根据定义的entProp生成"Nav"组件
function buildEntPeriodNavPills(){
	let d=App.data;
	let ent=d.rqData.ent,
		period=d.rqData.period,
		//生成nav-pills的根节点
		obj=$('#entPeriod').children('ul.nav-pills').css('border-bottom','1px solid #ddd'),
		perObj=d.entProp[ent].period.detail;
	
	obj.empty();
	for(let per in perObj){
		let e=perObj[per],
			a=$('<a></a>').css({'padding':'3px 10px','border-bottom-right-radius':'0px','border-bottom-left-radius':'0px','cursor':'pointer'}).attr({'data-ent':ent,'data-period':per}).html(e.txt),
			liObj=$('<li></li>').append(a);
		
		//为li标签添加.active 
		if(period==a.data('period')){
			liObj.addClass('active');
		}
		obj.append(liObj);
	}
}

//异步加载ent对应的searchForm和list，加载成功后异步设置searchForm
async function asyEntLoad(){	
	let totalTrue=(acc,cur)=>{
		if(cur){
			acc++;
		}
		return acc;
	};
	let r1= asyLoadEntObj('form');
	let r2= asyLoadEntObj('list');
	let r3='';
	//await关键字，表示开启异步过程并等待结果
	let valArr=await Promise.all([r1,r2]);
	
	if(valArr.reduce(totalTrue,0)!=2){
		return '内容载入失败。'
	}
	
	r3=await asySetEntQueryForm();
	
	if(!r3){
		return '表单设置失败。';
	}
	
	return true;
}
//async 定义了一个promise对象
//异步加载ent对应的searchForm和list
async function asyLoadEntObj(type){
	let rData=App.data.rqData;
	let conf={
			list:{node:$('#entList'),url:'/index/list/index'},
			form:{node:$('#entSearchForm'),url:'/index/searchForm/index'}
		};
	let opt={
			method:'POST',
			body:JSON.stringify(rData),
			headers:{
				'Content-Type':'application/json'
				// 'Content-Type':''
			},
			credentials:'include'
		};
	let result=false;
	//load节点
	let loadNod=conf[type].node;
	
	// fetch() 返回的是一个response对象，await让代码暂停在该行直至返回所要求的数据。
	// let resObj=await fetch(conf[type].url,opt);
	// let content=await resObj.text();
	// result=resObj.ok;
	// console.log(resObj);
	//$.post()返回的是一个jqXHR对象，该对象也是Promise对象，await该jqXHR对象得到其responseText属性值，大小要比上述resObj对象小
	let content=await $.post(conf[type].url,rData);
	
	result=(content)?true:false;
	
	loadNod.html(content);
	// console.log(resObj.body);
	return result;
}
//异步更新list、queryForm，或其中之1.
//objStr为'list'，仅更新list;
//objStr为'form'，仅更新queryForm;
//objStr为空，list与queryForm都更新。
async function asyRefreshEntObj(objStr='') {
	
	let totalTrue=(acc,cur)=>{
		if(cur){
			acc++;
		}
		return acc;
	};
	let r1= false;
	let r2= false;
	let result=false;	
	let valArr='';
		
	if(!objStr.length){
		//异步更新queryform内容。
		r1=asySetEntQueryForm();
		//异步更新list内容。
		r2=asyLoadEntObj('list');
	}
	if(objStr=='list'){
		r1=true;
		//异步更新list内容。
		r2=asyLoadEntObj('list');
	}
	if(objStr=='form'){
		//异步更新queryform内容。
		r1=asySetEntQueryForm();
		r2=true;
	}
	
	//await关键字，表示开启异步过程并等待结果
	valArr=await Promise.all([r1,r2]);
	
	if(valArr.reduce(totalTrue,0)){
		result=true;
		setRqData(); 		
	}
	//更新完后回到list的event中，为什么？
	//因为是异步更新，要保证list中的其他event能够响应，就需要返回到原来list的event中
	return Event.list();
}

//设定$('form.fmQuery')的各个表单项
async function asySetEntQueryForm(){
	let rData=App.data.rqData;
	let fm=$('form.fmQuery'),
		selSet=fm.find('select'),
		inSet=fm.find('input'),
		//查询字段名数组
		sNameArr=Object.keys(rData.searchData);
	let len=sNameArr.length;
	let resObj='';
	let opt={
			method:'POST',
			body:'',
			headers:{
				'Content-Type':'application/json'
			},
			//带上cookie
			credentials:'include'
		};
	let optData='';
	let result=false;
	
	setRqQueryFieldBy(fm);
	
	//使用fetch
	/* opt.body= JSON.stringify(rData);
	resObj=await fetch('/index/searchForm/getSelOptData',opt);
	optData=await resObj.json();
	result=resObj.ok; */
	
	//使用jQuery的post要比fetch花费更少时间得到数据。
	optData=await $.post('/index/searchForm/getSelOptData',rData);
	
	// console.log(rData);

	if(optData){
		//组装select的option，并设定显示值和底色
		selSet.each(function(){
			let selName=$(this).attr('name'),
				optObj=optData[selName],
				v=0;
			//组装option
			$(this).empty().append($('<option></option>').val(0).text('…不限'));	
			for(var m=0;m<optObj.num;m++){
				$(this).append($('<option></option>').val(optObj.val[m]).text(optObj.txt[m]));
			}
			
			if(len && sNameArr.includes(selName)){
				v=rData.searchData[selName];
				//上底色
				$(this).addClass('alert-info');
				if(v){
					//显示
					$(this).closest('.collapse').collapse();
				}
			}
			//设定select的显示值
			$(this).val(v);	
				
		});	
		result=true;
	}
	//barcode表单查询时，仅有一个查询项。
	if(rData.searchSource=='barcode' && rData.searchData.bar_code){
		//不需要显示普通查询表单
		len=len-1;
	}
	
	if(len){
		//显示整个form
		fm.closest('.collapse').collapse('show');
		inSet.each(function(){
			let n=$(this).attr('name');
			if(sNameArr.includes(n)){
				//赋值
				$(this).val(rData.searchData[n]);
				//上底色
				$(this).addClass('alert-info');
				//显示
				$(this).closest('.collapse').collapse('show');
			}
		});
	}
	
	return result;
}
