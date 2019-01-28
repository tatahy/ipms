// app/main.js
//import {glyPrex,bs3Color,topNavProp,entProp,rqData,urlObj,searchResultNum,year} from 'conf.js';

//自调用匿名函数具有立即执行的特点,2种结构。
(function($,p){ 
	// console.log(p);
})(jQuery,'p1');

(function($,p){ 
	// console.log(p);
}(jQuery,'p2'));

// var navEntSet=$('nav .navbar-collapse ul').eq(0).find('a'),
	// navEntPeriodSet=$('#entPeriod ul nav').find('a'),
	// yearObj=$('footer .year'),
	// btnSetPeriod=$('#entSummary').find('.btnPeriod'),
	// btnTopnavToggle=$('nav .navbar-header').children('.navbar-toggle');

	// btnSearchFormToggle=;
var navEntPeriod=$('#entPeriod').children('ul.nav');
	
buildTopNavbarCom();
buildMainRowCom();
setEntNumProp();
// 激活并设置tooltip
$('body').tooltip({selector:'[title]',triger:'hover click',placement:'auto top',delay: {show: 200, hide: 100},html: true });

//jQuery中的HTML文件准备好的函数。
$(document).ready(function(){
//本函数中有效的变量
let navEntSet=$('nav .navbar-collapse ul').eq(0).find('a'),
	navEntPeriodSet=$('#entPeriod ul nav').find('a'),
	yearObj=$('footer .year'),
	btnSetPeriod=$('#entSummary').find('.btnPeriod'),
	btnTopnavToggle=$('nav .navbar-header').children('.navbar-toggle');
	
navEntSet.click(function(){
	let cls=$(this).closest('li').attr('class')?$(this).closest('li').attr('class'):'no class';
		
	for(let ent in topNavProp){
		let e=topNavProp[ent];
		if($(this).data('ent')==ent){
			url=e.url;
			rqData.ent=ent;
			rqData.period=e.period;
		}
	}
		
	if(cls.indexOf('disabled')==-1){
		loadEntIndexFile();
	}
});
	
btnSetPeriod.click(function(){
	rqData.ent=$(this).data('ent');
	rqData.period=$(this).data('period');
	loadEntIndexFile();
});
	
yearObj.html((year-1)+'-'+year);

btnTopnavToggle.click(function(){
	showTopNavbar();
});

});

//让urlObj的取值都在给定的范围内
function initUrlObj(ent=''){	
	let arr=Object.keys(entProp),
		module='index',
		ctl='index',
		method='index',
		obj='',
		cArr=[],
		mArr=[];
	
	if(arr.indexOf(ent)!=-1){
		obj=entProp[ent];
		cArr=obj.ctl;
		mArr=obj.method;
		module=obj.module;
	}
	
	if(typeof obj=='object'){
		ctl=(cArr.indexOf(urlObj.ctl)!=-1)?urlObj.ctl:ctl;
		method=(mArr.indexOf(urlObj.method)!=-1)?urlObj.method:method;
	}
	
	urlObj.module=module;
	urlObj.ctl=ctl;
	urlObj.method=method;
}
function initPage(){
	let ent=rqData.ent;
	initUrlObj(ent);
	//从上到下依次生成、载入页面中的各个组件
	//1 生成页面navBar
	buildMainPeriodNavCom();
	//2 period的title
	buildPeriodTitleCom();
	//3 载入ent的SearchForm
	urlObj.method=ent+'SearchForm';
	$('#searchForm').load(getRqUrl());
	
	//载入条码识别模板文件
	// if(ent=='ass'){
		// urlObj.method='loadSearchFormBarcode';
		// $('#divSearchFormBarcode').load(getRqUrl());
	// }
	
	//4 载入ent对应的List
	loadEntPeriodList();
	console.log(navEntPeriod.find('a').length);
}
//设定向后端请求的url
function getRqUrl(){
	let kArr=['module','ctl','method'],
		arr=[domain];
	urlObj=$.extend({},{module:'index',ctl:'index',method:'index'},urlObj);
	
	kArr.forEach(function(e){
		arr.push(urlObj[e]);
	});
	
	return arr.join('/');
}

//根据定义的topNavProp生成"navLi"组件
function buildTopNavbarCom(){ 
	let r=$('nav .navbar-collapse ul').eq(0),
		entity=rqData.ent;
	r.empty();
	for(let ent in topNavProp){
		let e=topNavProp[ent],
			n=num[ent],
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
function buildMainRowCom(){
	let //计数器
		n=0,
		divSet=[];
	$('#entSummary').empty();
	//挨个生成ent组件
	for (let ent in num){
		let numObj=(typeof num[ent]=='object')?num[ent]:0;
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
function buildMainPeriodNavCom(){
	let ent=rqData.ent,
		//nav加载点
		obj=$('[id="'+rqData.ent+'PeriodNav"]').css('border-bottom','1px solid #ddd'),
		perObj=entProp[ent].period.detail;
	for(let per in perObj){
		let e=perObj[per],
			a=$('<a></a>').css({'padding':'3px 10px','border-bottom-right-radius':'0px','border-bottom-left-radius':'0px','cursor':'pointer'}).attr({'data-ent':ent,'data-period':per}).html(e.txt);
		
		obj.append($('<li></li>').append(a));
	}
	<!--为第一个li标签添加.active -->
	obj.find('li').eq(0).addClass('active');
}

//load模块对应的Index文件
function loadEntIndexFile(){
	let row=$('#entSummary'),
		//index文件加载点
		obj=$('#entPeriod'),
		ent=rqData.ent,
		url=topNavProp[ent]['url'];
		// url=getRqUrl();
	
	row.hide();
	obj.empty().hide();
	
	//ul里的li添加active
	$('nav .navbar-collapse ul').eq(0).find('[data-ent="'+ent+'"]').tab('show'); 
	
	if(ent=='index'){
		row.show();
	}else{
		obj.show().html(loadStr).load(url,rqData);
	}
}
//载入实体各个阶段下的列表
function loadEntPeriodList(){
	let	//list加载点
		obj=$('[id="'+rqData.ent+'List"]');
	
	urlObj.method=rqData.ent+'List';
	//id="xxPeriodNav"里的li添加active
	$('[data-period="'+rqData.period+'"]').tab('show'); 
	obj.html(loadStr).load(getRqUrl(),rqData,function(){
		initEntList();	
	});
}
//设置向后端请求时的查询数据
function setRqSearchData(searchObj=''){
	let formData='';
	//清空查询数据
	rqData.searchData={};
	
	if(typeof searchObj=='object'){
		//用户搜索有关键值对从表单searchObj获取
		formData=new FormData(searchObj.get(0));
		//使用Map对象的forEach方法组装hash数组
		formData.forEach(function(val,key){
			//非0非空的值才进行组装
			if(val!='' && val!=0){
				rqData.searchData[key]=val;
			}
		});		
	}
	
}
//得到设置entProp中的num属性值
function setEntNumProp(){
	for(let ent in entProp){
		for(per in entProp[ent].period.detail){
			entProp[ent].period.detail[per].title.num=num[ent][per];
		}
	}
}
//构建title组件
function  buildPeriodTitleCom(){
	let ent=rqData.ent,
		tProp=entProp[ent].period.detail,
		spObj=$('<span></span>').css({'cursor':'unset'}),
		spBdg=$('<span></span>').addClass('badge'),
		//title组件加载点
		obj=$('[id="'+ent+'Title"]');
	
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
	let fm=$('.searchForm');
	
	$('#searchNum').children().hide();
	$('#searchList').show();
	
	fm.each(function(){
		$(this)[0].reset();
		$(this).find('.form-control').removeClass('alert-info');
	});
	//清空查询数据
	setRqSearchData();
	loadEntPeriodList();
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
		listObj=$('#searchList'),
		sData=rqData.searchData,
		//计数器
		n=0;
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
//初始化ent list
function initEntList(){
	let ent=rqData.ent,
		capSObj=$('[id="'+ent+'List"]').find('caption strong'),
		periodChi=entProp[ent].period.detail[rqData.period].txt;
	initUrlObj(ent);
	//给tbl标题加内容
	capSObj.text(periodChi);	
	sortEntListTbl();
	setTrBgColor();
	showSearchResult();
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