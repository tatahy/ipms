//app/index.js

//conf.js中采用默认输出，c为本文件中使用的hash对象，其键值为'./conf.js'各个导出键值对
// import c from './conf.js';
import {Event as eve} from './Event.js';

import {App} from './main.js';

import {Modal} from './Modal.js';

//定义async函数asyInitData()进行全局变量赋值，默认返回的是一个promise对象。
async function asyInitData() {
	let d=App.data;	//$.post()方法返回的是jqXHR对象，这个jqXHR对象是对所发起的request的响应结果，无需解析
	// let resData = await $.post('/index/index/getInitData');
	//fetch()方法返回的是一个Promise对象，这个promise对象resolve成一个response对象，这个response对象是对所发起的request的响应结果。
	let opt={
			method:'POST',
			headers:{
				'Content-Type':'application/json'
			},
			//带上cookie
			credentials:'include'
		};
	let resObj = await fetch('/index/index/getInitData',opt);
	// 完成response对象内容解析
	let resData =await resObj.json();
	// let resData =await resObj.blob();
	
	// console.log(resObj);
	// console.log(resData);
	
	//全局变量赋值
	d.userName=resData.userName;
	d.urlObj=resData.urlObj;
	d.entNum=resData.entNum;
	d.rqData=initRqData();
	//完善entProp中的num属性值
	for(let ent in d.entProp){
		for(let per in d.entProp[ent].period.detail){
			d.entProp[ent].period.detail[per].title.num=d.entNum[ent][per];
		}
	}
	
	return d;
}

//页面生命周期函数-init，页面初始化
function pageInit(){
	//生成组件
	buildTopNavbar();
	buildEntCharts();
	// 激活并设置tooltip
	$('body').tooltip({selector:'[title]',triger:'hover click',placement:'auto top',delay: {show: 200, hide: 100},html: true });
	//设置页脚年份
	$('footer .year').html('2017-'+App.data.year);
}
//page-init成功后处理各种事件
function pageReady(){
	let d=App.data;
	// let rData=App.data.rqData;
	let sumNod=$('#entChart').show(),
		perNod=$('#entPeriod').hide();
	let entASet=$('nav .navbar-collapse ul').eq(0).find('a'),
		btnEntPeriod=sumNod.find('.btnPeriod'),
		btnTopnavToggle=$('nav .navbar-header').children('.navbar-toggle');
	
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
			initRqData();
			d.rqData.ent=ent;
			d.rqData.period=d.topNavProp[ent].period;
			
			//添加锚点？
			// window.location.hash=ent;
			//url添加查询字段？？
			// window.location.search=`ent=${ent}`;
						
			if(ent!='index'){
				
				return entGetReady();
			}
		}
		
		/* if(rData.ent!=ent){
			initRqData();
			rData.ent=ent;
			rData.period=App.data.topNavProp[ent].period;
			if(ent!='index'){
				return entGetReady();
			}
		} */
	});
	
	btnEntPeriod.click(function(){
		let ent=$(this).data('ent');
		sumNod.hide();
		perNod.show();
		
		//ent对应的navTop的li添加'active'
		entASet.closest('li').removeClass('active').find('[data-ent="'+ent+'"]').tab('show');
		initRqData();
		d.rqData.ent=ent;
		d.rqData.period=$(this).data('period');
		
		return entGetReady();
	});
}
//使用Promise对象实现异步的顺序处理？？
function entGetReady() {
	// Promise.resolve(asyEntLoad())
	asyEntLoad()
	//已成功加载list和form
	.then(result=>{
		//生成ent的period的title
		buildEntPeriodTitle();
		//生成ent的nav-pills
		buildEntPeriodNavPills();
		//设置list
		setEntPeriodList();
		//设置rqData
		setRqData();
		//加载entEvent()
		entEvent();
	})
	/* .then(()=>{
		//加载entEvent()
		entEvent();
	}) */
	.catch((e)=>console.log(e))
	.finally(()=>{
		// console.log('finally');
		// return entEvent();
	});
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

function entEvent(){
	let clpsSwitchSet=$('[data-collapse-switch]'),
		nodBarcode=$('#divFmBarcode');
	
	eve.pageRefresh();	
	
	eve.entPeriod();
	
	//其他的event
	if(nodBarcode.length){
		eve.barcodeForm();
	}
		
	if(clpsSwitchSet.length){
		eve.clpsSwitch();
	}
		
	eve.list();
	eve.queryForm();
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
	
	if(r2){
		//更新标题
		buildEntPeriodTitle();
	}
	
	if(valArr.reduce(totalTrue,0)){
		result=true;
		setRqData(); 
		setEntPeriodList();		
	}
	//更新完后回到list的event中，为什么？
	//因为是异步更新，要保证list中的其他event能够响应，就需要返回到原来list的event中
	return eve.list();

}

//setRqData
function setRqData() {	
	let rData=App.data.rqData;
	let fm=$('form.fmQuery'),
		entHArr=$('#entPeriod').find('.nav-pills li.active a').data(),
		sortName=$('#entList').find('[data-sort-name]').eq(0).data('sortName'),
		showId=$('#entList').find('[data-show-id]').eq(0).data('showId');
		
	rData.ent=entHArr.ent;
	rData.period=entHArr.period;
	
	if(rData.sortData.sortName==''){
		rData.sortData.sortName=sortName;	
	}
	
	fm.find('[name]').each(function(){
		let name=$(this).attr('name'),
			//null转为0
			v=$(this).val()==null?0:$(this).val(),
			lName=$(this)[0].localName;
			
		//赋值rqData.searchData，非0非空的值才进行组装
		if(v!=0){
			rData.searchData[name]=v;
		}
		
		//赋值rqData.queryField，表单输入项及其特征值
		rData.queryField[name]={val:v,tagName:lName};
		//添加select的option特征值
		if(lName=='select'){
			let len=$(this).find('option').length,
				opt={num:len,val:[''],txt:['']};
			/* //将option的value,text都带上
			if(len){
				$(this).find('option').each(function(index,el){
					opt.val[index]=$(el).val();
					opt.txt[index]=$(el).text();
				});
			} */
			rData.queryField[name]['option']=opt;
		}
	});
	
	return rData;
}
//设置list
function setEntPeriodList(){	
	let d=App.data;
	let rData=d.rqData;
	let ent=rData.ent,
		period=rData.period,
		capSNod=$('#entList').find('caption strong'),
		periodChi=d.entProp[ent].period.detail[period].txt;

	//显示分页的第一页
	rData.sortData.pageNum=1;
	
	//tbl标题加内容
	capSNod.text(periodChi);
	//tbl排序	
	sortEntListTbl();
	//指定行加底色	
	setTrBgColor();
	//显示搜索结果数
	showSearchResult();	
}

//让rqData回到初始值
function initRqData(){	
	App.data.rqData={
		ent:'index',
		period:'',
		sortData:{listRows:10,sortName:'',sortOrder:'asc',pageNum:1,showId:''},
		searchSource:'',
		searchData:{},
		queryField:{}
	};	
	return App.data.rqData;
}
//设定向后端请求的url
function getRqUrl(){
	let kArr=['domain','module','ctrl','action'],
	// let kArr=['module','ctrl','action'],
		arr=[],
		url='';
	App.data.urlObj=$.extend({},{module:'index',ctrl:'index',action:'index'},App.data.urlObj);
	
	kArr.forEach(function(e,i){
		arr[i]=(App.data.urlObj[e]);
	});
	
	return arr.join('/');
	// return url;
}

//根据定义的topNavProp生成"navLi"组件
function buildTopNavbar(){ 
	let d=App.data;
	let r=$('nav .navbar-collapse ul').eq(0),
		entity=d.rqData.ent;
	r.empty();
	for(let ent in d.topNavProp){
		let e=d.topNavProp[ent],
			n=d.entNum[ent],
			a=$('<a></a>').attr('data-ent',ent).append($('<span></span>').addClass(e.gly),'&nbsp;',e.chi),
			rC=$('<li></li>');
		
		if(ent!='index' && n==0){
			rC.addClass('disabled');
		}	
		
		if(ent=='index' || typeof n=='object'){
			a.css('cursor','pointer');
		}	
		rC.append(a);
		r.append(rC);
	}
	//为li标签添加.active
	r.find('[data-ent="'+entity+'"]').tab('show');
}
//根据定义的entProp生成"canvas"组件
function buildEntCharts() {
	let d=App.data;
	let //计数器
		n=0,
		divSet=[];
		
	$('#entChart').empty();
	
	//挨个生成ent组件
	for (let ent in d.entNum){
		let numObj=(typeof d.entNum[ent]=='object')?d.entNum[ent]:0,
			entObj=d.entProp[ent],
			perObj=entObj.period.summary,
			p=$('<p></p>').addClass('text-center').text(entObj.noneTxt),
			panH=$('<div></div>').addClass('panel-heading').append($('<span></span>').addClass(entObj.gly),'&nbsp;',$('<strong></strong>').html(entObj.chi)),
			panB=$('<div></div>').addClass('panel-body text-center'),
			pan=$('<div></div>').addClass('panel-group'),
			panType=$('<div></div>').addClass('panel panel-default'),
			div=$('<div></div>').addClass('col-sm-6');
		let idStr=`cvs-${ent}`;
		
	
	
		if(typeof numObj=='object' && Object.values(numObj).length){
			let cvsNod=$('<canvas></canvas>').attr({'id':idStr,'height':'200'}).text('canvas coming');
			let dhntDataSet={
				data:[],
				backgroundColor:[],
				label:'xxx'
			};
			let perName=[];
			let chartDhnut='';
			let btnSet=[];
			let other={
				num:Object.values(numObj)[0],
				rgb:'#f5f5f5',
				txt:'其他'
			};
			//计数器
			let m=0;
			
			//生成btnSet
			for(let per in perObj){
				let num=numObj[per],
					el=perObj[per],
					btn=$('<button></button>').attr({'class':'btn btn-xs btnPeriod','title':'查看详情'}).css({'margin':'2px','font-size':'14px'}),
					spBdg=$('<span></span>').addClass('badge');
				
				if(num){
					spBdg.text(num);
					btn.attr({'data-ent':ent,'data-period':per}).addClass(el.color).append(el.txt+'&nbsp;',spBdg);
				
					if(el.color=='btn-default'){
						btn.css('backgroundColor','#ccc');
					}
					
					btnSet[m]=btn;
					
					//chart中的数组赋值
					dhntDataSet.data[m]=num;
					dhntDataSet.backgroundColor[m]=el.rgb;
					perName[m]=el.txt;
					//计算其他项数量
					other.num-=num;
					
					m++;
				}
			}
			// chart中添加'其他'项
			if(other.num){				
				dhntDataSet.data.push(other.num);
				dhntDataSet.backgroundColor.push(other.rgb);
				perName.push(other.txt);
			}
			
			//生成chart
			chartDhnut=new Chart(cvsNod,{
				// type:'doughnut',
				type:'pie',
				data:{
					datasets:[dhntDataSet],
					labels:perName
				},
				options:{
					responsive:true,
					legend:{
						display:true,
						position:'right',
					},
					title:{
						display:false,
						text:'圆环图'
					},
					animation:{
						animateScale:true,
						animateRotate:true
					}
				}
			});
			//组装panB
			panB.append(cvsNod,'<br />',btnSet);
		}		
		
		if(numObj==0){
			panB.append(p);
		}
		
		panType.append(panH,panB);
		pan.append(panType);
		//生成一个ent组件
		divSet[n]=div.append(pan);
		n++;
	}
	
	//每个row放置2个ent组件
	for(let i=0;i<Math.ceil(n/2);i++){		
		let r=$('<div></div>').addClass('row');
		r.append(divSet[2*i],divSet[(2*i+1)]);
		$('#entChart').append(r);
	}
}

//根据定义的entProp生成"row"组件
function buildEntSummary(){
	let d=App.data;
	let //计数器
		n=0,
		divSet=[];
	$('#entSummary').empty();
	//挨个生成ent组件
	for (let ent in d.entNum){
		let numObj=(typeof d.entNum[ent]=='object')?d.entNum[ent]:0,
			entObj=d.entProp[ent],
			perObj=entObj.period.summary,
			p=$('<p></p>').addClass('text-center').text(entObj.noneTxt),
			panH=$('<div></div>').addClass('panel-heading').append($('<span></span>').addClass(entObj.gly),'&nbsp;',$('<strong></strong>').html(entObj.chi)),
			panB=$('<div></div>').addClass('panel-body'),
			pan=$('<div></div>').addClass('panel-group'),
			panType=$('<div></div>').addClass('panel panel-default'),
			div=$('<div></div>').addClass('col-sm-6');
	
		if(typeof numObj=='object' && Object.values(numObj).length){
			for(let per in perObj){
				let num=numObj[per],
					el=perObj[per],
					btn=$('<button></button>').attr({'class':'btn btn-sm btnPeriod','title':'查看详情'}).css({'margin':'2px','font-size':'14px'}),
					spBdg=$('<span></span>').addClass('badge');
				
				spBdg.text(num);
				btn.attr({'data-ent':ent,'data-period':per}).addClass(el.color).append(el.txt+'&nbsp;',spBdg);
				if(el.color=='btn-default'){
					btn.css('backgroundColor','#ccc');
				}
				
				panB.append(btn);
			}
		}		
		if(numObj==0){
			panB.append(p);
		}
		
		panType.append(panH,panB);
		pan.append(panType);
		//生成一个ent组件
		divSet[n]=div.append(pan);
		n++;
	}
	//每个row放置2个ent组件
	for(let i=0;i<Math.ceil(n/2);i++){		
		let r=$('<div></div>').addClass('row');
		r.append(divSet[2*i],divSet[(2*i+1)]);
		$('#entSummary').append(r);
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
	opt.body= JSON.stringify(rData);
	
	resObj=await fetch('/index/searchForm/getSelOptData',opt);
	optData=await resObj.json();
	result=resObj.ok;
	
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
			//option的value中无v
			// if(optObj.val.length && !optObj.val.includes(v)){
				// $.alert('option 添加'+v);
			// }
			
			//设定select的显示值
		$(this).val(v);	
				
	});	
	result=true;
	}
	//barcode表单查询时，仅有一个查询项
	if(rData.searchSource=='barcode' && rData.searchData.bar_code){
		len=len-1;
	}
	
	if(len){
		//显示整个form
		fm.closest('.collapse').collapse('show');
		inSet.each(function(){
			let n=$(this).attr('name');
			if(sNameArr.includes(n)){
				//赋值
				$(this).val(d.rqData.searchData[n]);
				//上底色
				$(this).addClass('alert-info');
				//显示
				$(this).closest('.collapse').collapse('show');
			}
		});
	}
	
	return result;
}

//组装向后端请求时的searchData，不带参数就是清空searchData
function setRqSearchDataBy(fm=''){
	let rData=App.data.rqData;
	let formData='';
	rData.searchData={};
	
	if(typeof fm !='object' || fm[0].localName !=='form'){
		return rData.searchData;
	}
	rData.searchSource=fm.data('formType');
	
	//用户搜索有关键值对从表单searchObj获取
	formData=new FormData(fm[0]);
	//使用Map对象的forEach方法组装hash数组
	formData.forEach(function(val,key){
		//非0非空的值才进行组装
		if(val!='' && val!=0){
			rData.searchData[key]=val;
		}
	});		
	return rData.searchData;
}
//组装向后端请求时的queryField，不带参数就是清空queryField
function setRqQueryFieldBy(fm=''){
	let rData=App.data.rqData;
	rData.queryField={};
	
	if(typeof fm !='object' || fm[0].localName !=='form'){
		return rData.queryField;
	}
	
	fm.find('[name]').each(function(){
		let n=$(this).attr('name'),
			v=$(this).val()==null?0:$(this).val(),
			lName=$(this)[0].localName;
		//表单输入项及其特征值
		rData.queryField[n]={val:v,tagName:lName};
		//添加select的option特征值
		if(lName=='select'){
			let len=$(this).find('option').length,
				opt={num:len,val:[''],txt:['']};
			/* //将option的value,text都带上
			if(len){
				$(this).find('option').each(function(index,el){
					opt.val[index]=$(el).val();
					opt.txt[index]=$(el).text();
				});
			} */
			rData.queryField[n]['option']=opt;
		}
	});
	
	return rData.queryField;
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
	if(n==0) listObj.show();
	if(n && d.searchResultNum){
		bingoObj.show();
		listObj.show();
		
		// Modal.small({cont:'<div class="text-center"><button class="btn btn-warning btn-sm" style="cursor:unset;font-size:14px;">搜索结果：<span class="badge">'+d.searchResultNum+'</span></button></div>'});
		
		$.alert('<div class="text-center"><button class="btn btn-warning btn-sm" style="cursor:unset;font-size:14px;">搜索结果：<span class="badge">'+d.searchResultNum+'</span></button></div>');

	}
	if(n && d.searchResultNum==0) noneObj.show();
}
//显示topnav
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
//给console.log的内容加上颜色
function consoleColor(str='无内容',color='blue'){
	let arr={red:'red',blue:'blue',yellow:'#f0ad4e',green:'#5cb85c'};
	
	color=Object.keys(arr).includes(color)?arr[color]:'red';
	//字符串中插入变量color，字符串的首尾要以'`'来代替引号，变量名要放入${}的花括号中。
	return console.log('%c%s',`font-size:16px;color:${color};`,str);
	
}

export {asyEntLoad,asyInitData,asyLoadEntObj,asyRefreshEntObj,asySetEntQueryForm,	buildEntPeriodNavPills,buildEntPeriodTitle,buildEntSummary,	buildTopNavbar,consoleColor,entEvent,entGetReady,getRqUrl,	initRqData,pageInit,pageReady,resetSearchForm,setEntPeriodList,setRqData,setRqQueryFieldBy,setRqSearchDataBy,setTrBgColor,showSearchResult,showTopNavbar,sortEntListTbl};
	
	
	
	
	