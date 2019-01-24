
const glyPrex='glyphicon glyphicon-',
	//归纳bootStrap3中的颜色相关属性
	bs3Color={
		info:{lab:'label label-info',btn:'btn-info',txt:'text-info',bg:'bg-info',alt:'alert-info',pan:'panel panel-info'},
		success:{lab:'label label-success',btn:'btn-success',txt:'text-success',bg:'bg-success',alt:'alert-success',pan:'panel panel-success'},
		danger:{lab:'label label-danger',btn:'btn-danger',txt:'text-danger',bg:'bg-danger',alt:'alert-danger',pan:'panel panel-danger'},
		warning:{lab:'label label-warning',btn:'btn-warning',txt:'text-warning',bg:'bg-warning',alt:'alert-warning',pan:'panel panel-warning'},
		primary:{lab:'label label-primary',btn:'btn-primary',txt:'text-primary',bg:'bg-primary',pan:'panel panel-primary'},
		//default
		'default':{lab:'label label-default',btn:'btn-default'},
		muted:{text:'text-muted'},
		link:{btn:'btn-link'}
	},
	//topNav中li组件主要属性
	topNavProp={
		index:{'url':domain+'/index/index/index',period:'',gly:glyPrex+'home',chi:'首页'},
		pat:{'url':domain+'/patent/index/index',period:'total',gly:glyPrex+'wrench',chi:'专利'},
		ass:{'url':domain+'/asset/index/index',period:'_ASSS_USUAL',gly:glyPrex+'oil',chi:'固定资产'},
		pro:{'url':domain+'/project/index/index',period:'total',gly:glyPrex+'inbox',chi:'项目'},
		the:{'url':domain+'/thesis/index/index',period:'total',gly:glyPrex+'list',chi:'论文'}
	},
	noneSufx='模块无【查阅】权限，请与系统管理员联系。';

//定义前端各个实体主要属性(用于jQ构建组件，路由生成等)
var entProp={
	pat:{
		module:'patent',
		ctl:['index'],
		method:['index','patList','patSearchForm','getSelComData'],
		txt:'专利概况',
		gly:glyPrex+'wrench',
		period:{
			summary:{
				newAdd:{txt:'拟申报专利数：',color:bs3Color['info']['btn']},
				apply:{txt:'申报专利数：',color:bs3Color['primary']['btn']},
				authorize:{txt:'有效专利数：',color:bs3Color['success']['btn']}
			},
			detail:{
				total:{txt:'专利概况',title:{btn:'btn btn-primary',txt:'专利总数&nbsp;',num:''}},
				audit:{txt:'内审',title:{lab:'label label-info',btn:'btn btn-info',txt:'内部审核拟申报专利&nbsp;',num:''}},
				newAdd:{txt:'拟申报',title:{btn:'btn btn-info',txt:'拟申报专利&nbsp;',num:''}},
				apply:{txt:'申报',title:{btn:'btn btn-primary',txt:'申报专利&nbsp;',num:''}},
				authorize:{txt:'授权(有效)',title:{btn:'btn btn-success',txt:'授权(有效期)专利&nbsp;',num:''}},
				invalid:{txt:'无授权(无效)',title:{btn:'btn btn-default',txt:'无效专利数&nbsp;',num:''}}
			}
		},
		noneTxt:'“专利”'+noneSufx
	},
	ass:{
		module:'asset',
		ctl:['index'],
		method:['index','assList','assSearchForm'],
		txt:'固定资产',
		gly:glyPrex+'oil',
		period:{
			summary:{
				_ASSS1:{txt:'待定数：',color:bs3Color['info']['btn']},
				_ASSS2:{txt:'正常数：',color:bs3Color['success']['btn']},
				_ASSS3:{txt:'异常数：',color:bs3Color['warning']['btn']},
				_ASSS4:{txt:'停用数：',color:bs3Color['default']['btn']}
			},
			detail:{
				_ASSS_USUAL:{txt:'固定资产概况',title:{txt:'固定资产总数&nbsp;',btn:'btn btn-primary',num:''}},
				_ASSS1:{txt:'待定',title:{txt:'状态待定数&nbsp;',btn:'btn btn-info',num:''}},
				_ASSS2:{txt:'正常',title:{txt:'状态正常数&nbsp;',btn:'btn btn-success',num:''}},
				_ASSS3:{txt:'异常',title:{txt:'状态异常数&nbsp;',btn:'btn btn-warning',num:''}},
				_ASSS4:{txt:'停用',title:{txt:'停止使用数&nbsp;',btn:'btn btn-default',num:''}},
				_ASSS5:{txt:'销账',title:{txt:'财务销账数&nbsp;',btn:'btn btn-danger',num:''}}
			}
		},
		noneTxt:'“固定资产”'+noneSufx
	},
	pro:{
		module:'project',
		ctl:['index','proList','proSearchForm'],
		method:['index'],
		txt:'项目概况&nbsp;<span class="bg-primary">&nbsp;开发中&nbsp;</span>',
		gly:glyPrex+'inbox',
		period:{
			summary:{
				plan:{txt:'拟申报项目数：',color:bs3Color['info']['btn']},
				apply:{txt:'申报项目数',color:bs3Color['primary']['btn']},
				approve:{txt:'立项项目数：',color:bs3Color['warning']['btn']},
				process:{txt:'在研项目数：',color:bs3Color['danger']['btn']},
				done:{txt:'结题项目数：',color:bs3Color['success']['btn']},
				terminate:{txt:'终止项目数：',color:bs3Color['default']['btn']}
			},
			detail:{
				total:{txt:'项目总体情况',title:{btn:'btn btn-primary',txt:'项目总数&nbsp;',num:''}},
				audit:{txt:'内审',title:{btn:'btn btn-info',txt:'内部审核中项目数&nbsp;',num:''}},
				plan:{txt:'拟申报',title:{btn:'btn btn-info',txt:'内审通过拟申报项目数&nbsp;',num:''}},
				apply:{txt:'申报',title:{btn:'btn btn-primary',txt:'申报过程中项目数&nbsp;',num:''}},
				approve:{txt:'立项',title:{btn:'btn btn-warning',txt:'立项批准项目数&nbsp;',num:''}},
				process:{txt:'在研',title:{btn:'btn btn-danger',txt:'执行中项目数&nbsp;',num:''}},
				inspect:{txt:'验收',title:{btn:'btn btn-primary',txt:'验收过程中项目数&nbsp;',num:''}},
				done:{txt:'结题',title:{btn:'btn btn-success',txt:'结束(基本达成项目预期)项目数&nbsp;',num:''}},
				terminate:{txt:'终止',title:{btn:'btn btn-default',txt:'终止(未达成项目预期)项目数&nbsp;',num:''}},
				reject:{txt:'申报未立项',title:{btn:'btn btn-default',txt:'申报未立项项目数&nbsp;',num:''}}
			}
		},
		noneTxt:'“项目”'+noneSufx
	},
	the:{
		module:'thesis',
		ctl:['index','theList','theSearchForm'],
		method:['index'],
		txt:'论文概况&nbsp;<span class="bg-primary">&nbsp;开发中&nbsp;</span>',
		gly:glyPrex+'list',
		period:{
			summary:{
				plan:{txt:'拟发表论文数：',color:bs3Color['info']['btn']},
				apply:{txt:'投稿论文数：',color:bs3Color['primary']['btn']},
				publish:{txt:'发表论文数：',color:bs3Color['success']['btn']}
			},
			detail:{
				total:{txt:'论文总体情况',title:{btn:'btn btn-primary',txt:'论文总数&nbsp;',num:''}},
				audit:{txt:'内审',title:{btn:'btn btn-info',txt:'内部审核中论文数&nbsp;',num:''}},
				plan:{txt:'拟发表',title:{btn:'btn btn-info',txt:'内审通过拟发表论文数&nbsp;',num:''}},
				apply:{txt:'投稿',title:{btn:'btn btn-primary',txt:'投稿过程中论文数&nbsp;',num:''}},
				accept:{txt:'收录',title:{btn:'btn btn-warning',txt:'已收录论文数&nbsp;',num:''}},
				publish:{txt:'发表',title:{btn:'btn btn-success',txt:'已公开发表论文数&nbsp;',num:''}},
				reject:{txt:'拒稿',title:{btn:'btn btn-default',txt:'被拒绝发表论文数&nbsp;',num:''}}
			}
		},
		noneTxt:'“论文”'+noneSufx
	}
};

//向后端请求时发送的数据
var	rqData={
		ent:'index',
		period:'',
		sortData:{},
		searchData:{}
	};
var urlObj={module:'index',method:'index',ctl:'index'};
var searchResultNum='';

buildTopNavbarCom();
buildMainRowCom();
setEntNumProp();
// 激活并设置tooltip
$('body').tooltip({selector:'[title]',triger:'hover click',placement: 'auto bottom',delay: {show: 200, hide: 100},html: true });

//自调用匿名函数具有立即执行的特点。
(function($){ })(jQuery);	

$(document).ready(function(){
	let navASet=$('#topNavbar .navbar-nav').eq(0).find('a'),
		btnSet=$('#mainRow').find('.btnPeriod');
	navASet.click(function(){
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
	
	btnSet.click(function(){
		rqData.ent=$(this).data('ent');
		rqData.period=$(this).data('period');
		loadEntIndexFile();
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
	$('[id="'+ent+'SearchForm"]').load(getRqUrl());
	//4 载入ent对应的List
	loadEntPeriodList();
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
	let r=$('#topNavbar .navbar-nav').eq(0),
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
	<!--为li标签添加.active -->
	$('[data-ent="'+entity+'"]').tab('show');
}

//根据定义的entProp生成"row"组件
function buildMainRowCom(){
	let //计数器
		n=0,
		divSet=[];
	$('#mainRow').empty();
	//挨个生成ent组件
	for (let ent in num){
		let numObj=(typeof num[ent]=='object')?num[ent]:0;
			entObj=entProp[ent],
			perObj=entObj.period.summary,
			p=$('<p></p>').addClass('text-center').text(entObj.noneTxt),
			panH=$('<div></div>').addClass('panel-heading').append($('<span></span>').addClass(entObj.gly),'&nbsp;',$('<strong></strong>').html(entObj.txt)),
			panB=$('<div></div>').addClass('panel-body'),
			pan=$('<div></div>').addClass('panel-group'),
			panType=$('<div></div>').addClass('panel panel-default'),
			div=$('<div></div>').addClass('col-sm-6');
		
		if(typeof numObj=='object'){
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
		$('#mainRow').append(r);
	}
	
	//返回btnSet
	<!-- return console.log($('#mainRow').find('.btnPeriod').length); -->
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
	let row=$('#mainRow'),
		//index文件加载点
		obj=$('#mainPeriod'),
		ent=rqData.ent,
		loadStr='<p class="text-center" style="font-size:20px;padding-top:20px;">加载中……</p>',
		url=topNavProp[ent]['url'];
		// url=getRqUrl();
	
	row.hide();
	obj.empty().hide();
	
	//id="topNavbar"里的li添加active
	$('[data-ent="'+ent+'"]').tab('show'); 
	
	if(ent=='index'){
		row.show();
	}else{
		obj.show().html(loadStr).load(url,rqData);
	}
}
//载入实体各个阶段下的列表
function loadEntPeriodList(){
	let loadStr='<p class="text-center" style="font-size:28px;">加载中……</p>',
		//list加载点
		obj=$('[id="'+rqData.ent+'List"]');
	
	urlObj.method=rqData.ent+'List';
	//id="xxPeriodNav"里的li添加active
	$('[data-period="'+rqData.period+'"]').tab('show'); 
	obj.html(loadStr).load(getRqUrl(),rqData);
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
		<!-- tblObj=$('#patListTbl'), -->
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
};

function showSearchResult() {
	let bingoObj=$('#searchNum .bingo'),
		noneObj=$('#searchNum .none'),
		listObj=$('#searchList'),
		//计数器
		n=0;
	bingoObj.find('.badge').text(searchResultNum);
	for(let el in sData){
		n=(sData[el]=='' || sData[el]==0)?n:n+1;
	}
	//是否显示搜索表单
	<!-- n?fmObj.addClass('in'):fmObj.removeClass('in'); -->
	
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
	
};

//初始化ent list
function initEntList(){
	sortEntListTbl();
	setTrBgColor();
	showSearchResult();
}

