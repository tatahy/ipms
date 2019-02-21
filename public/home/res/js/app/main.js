// app/main.js
// import {glyPrex,domain,loadStr,bs3Color,topNavProp,entProp,rqData,urlObj,searchResultNum,year} from './conf.js';
// import * from './conf.js';

pageInit();

//页面生命周期函数-init
function pageInit(){
	let resData=ajaxResByPost({getPageInitData:true,async:false});
	//全局变量赋值
	rqData=initRqData();
	urlObj=resData.urlObj;
	entNum=resData.entNum;
	console.log(resData);
	setEntProp();
	console.log(getRqUrl());
	
	//生成组件
	buildTopNavbar();
	buildEntSummary();
	
	// 激活并设置tooltip
	$('body').tooltip({selector:'[title]',triger:'hover click',placement:'auto top',delay: {show: 200, hide: 100},html: true });
	
	//jQuery中的HTML文件准备好的函数，操作组件
	$(document).ready(function(){
		//本函数中有效的变量
		let sumObj=$('#entSummary'),
			perObj=$('#entPeriod');
		let navTopEntASet=$('nav .navbar-collapse ul').eq(0).find('a'),
			btnEntPeriod=sumObj.find('.btnPeriod'),
			btnTopnavToggle=$('nav .navbar-header').children('.navbar-toggle');
	
		sumObj.show();
		perObj.hide();
		
		$('footer .year').html('2017-'+year);

		btnTopnavToggle.click(function(){
			showTopNavbar();
		});

	
		navTopEntASet.click(function(){
			let cls=$(this).closest('li').attr('class')?$(this).closest('li').attr('class'):'no class',
				ent=$(this).data('ent');
			
			$(this).tab('show');
			sumObj.hide();
			perObj.hide();
			
			ent=='index'?sumObj.show():perObj.show();
			
			initRqData();
			rqData.ent=ent;
			rqData.period=topNavProp[ent].period;
			
			if(cls.indexOf('disabled')==-1 && ent!='index'){
				entLoad();
				// entReady();
			}
		});
	
		btnEntPeriod.click(function(){
			let ent=$(this).data('ent');
			sumObj.hide();
			perObj.show();
		
			//ent对应的navTop的li添加'active'
			navTopEntASet.closest('li').removeClass('active').find('[data-ent="'+ent+'"]').tab('show');
		
			rqData.ent=ent;
			rqData.period=$(this).data('period');
		
			entLoad();
			// entReady();
		});
	});
}
//ent-change
function entLoad(){
	let ent=rqData.ent;
	
	let	sfSet=$('#entSearchForm').children().prop('hidden',true),
		fmCom=sfSet.eq(0),
		fmSpe=sfSet.eq(1);
	//选择操作节点并显示
	let searchFmP=(rqData.ent=='ass')?fmSpe.prop('hidden',false):fmCom.prop('hidden',false).find('div.searchForm');
	// let url=urlObj.domain+'/index/SearchForm/index';
	let uForm='/index/SearchForm/index';
	let listP=$('#entList');
	let uList='/index/List/index';
	
	consoleColor('entLoad(), rqData:');
	console.log(rqData);
	// initUrlObj(ent);
	//从上到下依次生成、载入页面中的各个组件
	//1 生成ent的nav-pills
	buildEntPeriodNavPills();
	
	//2 生成ent的period的title
	buildEntPeriodTitle();
		
	/* //3 载入ent的searchForm
	loadEntSearchForm();
	
	//4 载入ent对应的List
	loadEntPeriodList();
	
	entReady(); */
	
	$.when(
		//载入entSearchForm
		$.post(uForm,rqData),
		//载入entPeriodList
		$.post(uList,rqData),
	).then(function(p1,p2){
		
		searchFmP.html(p1[0]);
		listP.html(p2[0]);
		console.log(p1);
		
		entReady();
	},function(){
		//'entLoad()--error'
		$.alert('failed');
	});
}
//ent-ready
function entReady(){
	let //在entLoad中已生成
		navEntPeriodASet=$('#entPeriod').children('.nav-pills').find('a');

	navEntPeriodASet.click(function(){
		let sData=$(this).data();
		$(this).tab('show');
		rqData.ent=sData.ent;
		rqData.period=sData.period;
		// 生成ent的period的title
		buildEntPeriodTitle();
		loadEntSearchForm();
		loadEntPeriodList();
	});
	
	//其他操作
	// 页面刷新
	// $('.btnPageRefresh').click(function(){
		// resetSearchForm();
		// loadEntPeriodList();
	// });	
	$.alert('entReady()--finished');
}
//让urlObj的取值都在已定义的范围内
function initUrlObj(ent=''){	
	let arr=Object.keys(entProp),
		module='index',
		ctrl='index',
		action='index',
		obj='',
		cArr=[],
		mArr=[];
	
	if(arr.indexOf(ent)!=-1){
		obj=entProp[ent];
		cArr=obj.ctrl;
		mArr=obj.action;
		module=obj.module;
	}
	
	if(typeof obj=='object'){
		ctrl=(cArr.indexOf(urlObj.ctrl)!=-1)?urlObj.ctrl:ctrl;
		action=(mArr.indexOf(urlObj.action)!=-1)?urlObj.action:action;
	}
	
	urlObj.module=module;
	urlObj.ctrl=ctrl;
	urlObj.action=action;
}

//让rqData回到初始值
function initRqData(){	
	return {
		ent:'index',
		period:'',
		sortData:{listRows:10,sortName:'',sortOrder:'asc',pageNum:1},
		searchData:{},
		queryField:{}
	};	
}
//设定向后端请求的url
function getRqUrl(){
	let kArr=['domain','module','ctrl','action'],
	// let kArr=['module','ctrl','action'],
		arr=[],
		url='';
	urlObj=$.extend({},{module:'index',ctrl:'index',action:'index'},urlObj);
	
	kArr.forEach(function(e,i){
		arr[i]=(urlObj[e]);
	});
	
	return arr.join('/');
	// return url;
}

//根据定义的topNavProp生成"navLi"组件
function buildTopNavbar(){ 
	let r=$('nav .navbar-collapse ul').eq(0),
		entity=rqData.ent;
	r.empty();
	for(let ent in topNavProp){
		let e=topNavProp[ent],
			n=entNum[ent],
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

//根据定义的entProp生成"row"组件
function buildEntSummary(){
	let //计数器
		n=0,
		divSet=[];
	$('#entSummary').empty();
	//挨个生成ent组件
	for (let ent in entNum){
		let numObj=(typeof entNum[ent]=='object')?entNum[ent]:0,
			entObj=entProp[ent],
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
				btn.attr({'data-ent':ent,'data-period':per}).addClass(el.color).append(el.txt,spBdg);
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
	let ent=rqData.ent,
		period=rqData.period,
		//生成nav-pills的根节点
		obj=$('#entPeriod').children('ul.nav-pills').css('border-bottom','1px solid #ddd'),
		perObj=entProp[ent].period.detail;
	
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

//载入ent的搜索表单
function loadEntSearchForm(){
	let	sfSet=$('#entSearchForm').children().prop('hidden',true),
		fmCom=sfSet.eq(0),
		fmSpe=sfSet.eq(1);
	//选择操作节点并显示
	let obj=(rqData.ent=='ass')?fmSpe.prop('hidden',false):fmCom.prop('hidden',false).find('div.searchForm');
	
	//
	urlObj.module='index';
	urlObj.ctrl='SearchForm';
	urlObj.action='index';
	
	consoleColor('loadEntSearchForm() before load, obj:','red');
	console.log(rqData);
	
	//searchForm的加载
	/* obj.html(loadStr).load(getRqUrl(),rqData,function(){	
		let fm=obj.find('form.common');
		consoleColor('loadEntSearchForm() after load, rqData:','red');
		
		rqData.searchData=setRqSearchDataBy(fm);
		rqData.queryField=setRqQueryFieldBy(fm);
		console.log(rqData);
		setEntSearchForm(fm);
	}); */
	
	return $.post(getRqUrl(),rqData,function(res){	
		// console.log(res.data);
		// obj.html(res);
	});
		
}
//设定指定form的各个表单项
function setEntSearchForm(fm){
	let selSet=fm.find('select'),
		inSet=fm.find('input'),
		//查询字段名数组
		sNameArr=Object.keys(rqData.searchData);
	
	urlObj.ctrl='searchForm';
	urlObj.action='getSelOptData';
	
	consoleColor('main.js setEntSearchForm() before post, rqData:','red');
	console.log(rqData);
	console.log(getRqUrl());
	
	//设定input的显示值和底色
	if(sNameArr.length){
		inSet.each(function(){
			let n=$(this).attr('name');
			if(sNameArr.includes(n)){
				//赋值
				$(this).val(rqData.searchData[n]);
				//上底色
				$(this).addClass('alert-info');
			}
		});
	}
	
	//向后端请求组装select组件所需数据
	$.post(getRqUrl(),rqData,function(optData){
		consoleColor('main.js setEntSearchForm() after post resData:','red');
		console.log(optData);
		
		//组装select的option，并设定显示值和底色
		selSet.each(function(){
			let selName=$(this).attr('name'),
				optObj=optData[selName],
				v=0;
			
			$(this).empty().append($('<option></option>').val(0).text('…不限'));	
			//组装option
			for(var m=0;m<optObj.num;m++){
				$(this).append($('<option></option>').val(optObj.val[m]).text(optObj.txt[m]));
			}
			
			if(sNameArr.length && sNameArr.includes(selName)){
				v=rqData.searchData[selName];
				//上底色
				$(this).addClass('alert-info');
			}
			//设定select的显示值
			$(this).val(v);		
		});		
	});
}
//载入ent各个period的列表
function loadEntPeriodList(){
	let	//加载list的根节点
		obj=$('#entList');
	
	urlObj.module='index';
	urlObj.ctrl='List';
	urlObj.action='index';
	
	/* obj.html(loadStr).load(getRqUrl(),rqData,function(){
		let ent=rqData.ent,
			capSObj=$('#entList').find('caption strong'),
			periodChi=entProp[ent].period.detail[rqData.period].txt;
		
		//显示分页的第一页
		rqData.sortData.pageNum=1;
		
		consoleColor('loadEntPeriodList()','red');
		console.log(rqData);
		
		//给tbl标题加内容
		capSObj.text(periodChi);	
		sortEntListTbl();
		setTrBgColor();
		showSearchResult();	
	}); */
	
	return $.post(getRqUrl(),rqData,function(res){	
		obj.html(res);
	});
	
}
//组装向后端请求时的searchData，不带参数就是清空searchData
function setRqSearchDataBy(fm=''){
	let formData='';
	rqData.searchData={};
	
	if(typeof fm !='object' || fm[0].localName !=='form'){
		return rqData.searchData;
	}
	//用户搜索有关键值对从表单searchObj获取
	formData=new FormData(fm[0]);
	//使用Map对象的forEach方法组装hash数组
	formData.forEach(function(val,key){
		//非0非空的值才进行组装
		if(val!='' && val!=0){
			rqData.searchData[key]=val;
		}
	});		
	return rqData.searchData;
}
//组装向后端请求时的queryField，不带参数就是清空queryField
function setRqQueryFieldBy(fm=''){
	rqData.queryField={};
	
	if(typeof fm !='object' || fm[0].localName !=='form'){
		return rqData.queryField;
	}
	
	fm.find('[name]').each(function(){
		let n=$(this).attr('name'),
			v=$(this).val()==null?0:$(this).val(),
			lName=$(this)[0].localName;
		//表单输入项及其特征值
		rqData.queryField[n]={val:v,tagName:lName};
		//添加select的option特征值
		if(lName=='select'){
			let len=$(this).find('option').length,
				opt={num:len,val:[''],txt:['']};
			if(len){
				$(this).find('option').each(function(index,el){
					opt.val[index]=$(el).val();
					opt.txt[index]=$(el).text();
				});
			}
			rqData.queryField[n]['option']=opt;
		}
	});
	
	return rqData.queryField;
}
//完善entProp中的num属性值
function setEntProp(){
	for(let ent in entProp){
		for(let per in entProp[ent].period.detail){
			entProp[ent].period.detail[per].title.num=entNum[ent][per];
		}
	}
}
//构建title组件
function buildEntPeriodTitle(){
	let ent=(rqData.ent=='index')?'pat':rqData.ent,
		tProp=entProp[ent].period.detail,
		spObj=$('<span></span>').css({'cursor':'unset'}),
		spBdg=$('<span></span>').addClass('badge'),
		//生成title的根节点
		obj=$('#entPeriod').children('h4.title');
		// obj=$('[id="'+ent+'Title"]');
	obj.empty();
	for(let p in tProp){
		let e=tProp[p].title;
		if(p==rqData.period){
			spObj.addClass(e.btn).append(e.txt,spBdg.text(e.num));
			obj.empty().append(spObj);
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
		//有邻居都隐藏
		if($(this).siblings().length){
			$(this).siblings().hide();
		}
	});
	//清空查询数据
	setRqSearchDataBy();
}

//对ent List进行排序
//列表字段进行升序降序转换，改变该字段表头显示格式
function sortEntListTbl() {
	let glyAsc=$('<span></span>').addClass('small glyphicon glyphicon-sort-by-attributes'),
		glyDesc=$('<span></span>').addClass('small glyphicon glyphicon-sort-by-order-alt'),
		sortOrder=rqData.sortData.sortOrder,
		sortName=rqData.sortData.sortName,
		tblObj=$('[id="'+rqData.ent+'ListTbl"]'),
		aHSet=tblObj.find('thead a'),
		trSet=tblObj.find('tr'),
		column='',
		columnArr=['num'];
		
	aHSet.each(function(){
		let gly=(sortOrder == 'asc')?glyAsc:glyDesc,
			sort=(sortOrder == 'asc')?'desc':'asc',
			title=(sortOrder == 'asc')?'当前为升序。点击后降序':'当前为降序。点击后升序',
			column=$(this).data('sortName');
		columnArr.push(column);
		//每个添加属性
		$(this).removeClass('label label-primary').attr({'data-period':rqData.period,'title':'点击排序'}).css('cursor','pointer').closest('th').addClass('text-center');
		
		(sortName==column)?$(this).attr({'class':'label label-primary','data-sort-order':sort,'title':title}).tooltip({placement:'top'}).append('&nbsp;',gly):$(this).tooltip({placement:'bottom'});	
		
	});
	//每个单元格添加data-column=""，相同的data-column用于组合成一个column
	columnArr.forEach(function(el,index){
		trSet.each(function(){ 
			$(this).children().eq(index).attr('data-column',el);
		});	
	});
		
	//选中进行排序的列(除标题行外的)向左对齐，添加底色
	tblObj.find('tbody [data-column="'+sortName+'"]').addClass('text-left bg-info');
}

function setTrBgColor(showId='') {
	let id=showId?showId:rqData.sortData.showId,
		obj=$('#patListTbl tbody');
	//去掉所有行的底色
	obj.find('tr').removeClass('bg-warning');
	//给showId所在行上底色
	obj.find('[data-show-id="'+id+'"]').addClass('bg-warning');
}

function showSearchResult() {
	let bingoObj=$('#searchNum .bingo'),
		noneObj=$('#searchNum .none'),
		listObj=$('#entList'),
		sData=rqData.searchData,
		//计数器
		n=0;
		
	consoleColor('main.js showSearchResult(), rqData:');
	console.log(rqData);
	
	bingoObj.find('.badge').text(searchResultNum);
	for(let el in sData){
		n=(sData[el]=='' || sData[el]==0)?n:n+1;
	}
	
	//搜索结果显示
	bingoObj.hide();
	noneObj.hide();
	listObj.hide();
	if(n==0) listObj.show();
	if(n && searchResultNum){
		bingoObj.show();
		listObj.show();
	}
	if(n && searchResultNum==0) noneObj.show();
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
//使用$.post方法向服务器请求特定值。
function ajaxResByPost(opt={}) {
	let optDefault={getPageInitData:false,async:true,action:'index',data:''},
		resData='';
	opt=$.extend({},optDefault,opt);
	
	urlObj.action=opt.action;
	
	// consoleColor('ajaxResByPost()');
	// console.log(opt);
	// console.log(getRqUrl());
	
	$.ajax({	
		url:getRqUrl(),
		async:opt.async,
		type:'POST',
		data:opt,
		dataType:'json'	,
		success:function(data){
			resData=data;
			// return data;
		}				
	});
		
	return resData;
	
	
}