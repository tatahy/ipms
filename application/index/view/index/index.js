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
	urlIndex=domain+'/index/index/index',
	noneSufx='模块无【查阅】权限，请与系统管理员联系。';

//定义前端系统各个实体主要属性(用于jQ构建组件)
const entProp={
	pat:{
		module:'patent',
		txt:'专利概况',
		gly:glyPrex+'wrench',
		period:{
			summary:[
				{name:'newAdd',txt:'拟申报专利数：',color:bs3Color['info']['btn']},
				{name:'apply',txt:'申报专利数：',color:bs3Color['primary']['btn']},
				{name:'authorize',txt:'有效专利数：',color:bs3Color['success']['btn']}
			],
			detail:[
				{name:'total',txt:'专利概况'},
				{name:'audit',txt:'内审'},
				{name:'newAdd',txt:'拟申报'},
				{name:'apply',txt:'申报'},
				{name:'authorize',txt:'授权(有效)'},
				{name:'invalid',txt:'无授权(无效)'}
			]
		},
		noneTxt:'“专利”'+noneSufx
	},
	ass:{
		module:'asset',
		txt:'固定资产',
		gly:glyPrex+'oil',
		period:{
			summary:[
				{name:'_ASSS1',txt:'待定数：',color:bs3Color['info']['btn']},
				{name:'_ASSS2',txt:'正常数：',color:bs3Color['success']['btn']},
				{name:'_ASSS3',txt:'异常数：',color:bs3Color['warning']['btn']},
				{name:'_ASSS4',txt:'停用数：',color:bs3Color['default']['btn']}
			],
			detail:[
				{name:'_ASSS_USUAL',txt:'固定资产概况'},
				{name:'_ASSS1',txt:'待定'},
				{name:'_ASSS2',txt:'正常'},
				{name:'_ASSS3',txt:'异常'},
				{name:'_ASSS4',txt:'停用'},
				{name:'_ASSS5',txt:'销账'}
			]
		},
		noneTxt:'“固定资产”'+noneSufx
	},
	pro:{
		module:'project',
		txt:'项目概况&nbsp;<span class="bg-primary">&nbsp;开发中&nbsp;</span>',
		gly:glyPrex+'inbox',
		period:{
			summary:[
				{name:'plan',txt:'拟申报项目数：',color:bs3Color['info']['btn']},
				{name:'apply',txt:'申报项目数',color:bs3Color['primary']['btn']},
				{name:'approve',txt:'立项项目数：',color:bs3Color['warning']['btn']},
				{name:'process',txt:'在研项目数：',color:bs3Color['primary']['btn']},
				{name:'done',txt:'结题项目数：',color:bs3Color['success']['btn']},
				{name:'terminate',txt:'终止项目数：',color:bs3Color['default']['btn']}
			],
			detail:[
				{name:'total',txt:'项目总体情况'},
				{name:'audit',txt:'内审'},
				{name:'plan',txt:'拟申报'},
				{name:'apply',txt:'申报'},
				{name:'approve',txt:'立项'},
				{name:'process',txt:'在研'},
				{name:'inspect',txt:'验收'},
				{name:'done',txt:'结题'},
				{name:'terminate',txt:'终止'},
				{name:'reject',txt:'申报未立项'}
			]
		},
		noneTxt:'“项目”'+noneSufx
	},
	the:{
		module:'thesis',
		txt:'论文概况&nbsp;<span class="bg-primary">&nbsp;开发中&nbsp;</span>',
		gly:glyPrex+'list',
		period:{
			summary:[
				{name:'plan',txt:'拟发表论文数：',color:bs3Color['info']['btn']},
				{name:'apply',txt:'投稿论文数：',color:bs3Color['primary']['btn']},
				{name:'publish',txt:'发表论文数：',color:bs3Color['success']['btn']}
			],
			detail:[
				{name:'total',txt:'论文总体情况'},
				{name:'audit',txt:'内审'},
				{name:'plan',txt:'拟发表'},
				{name:'apply',txt:'投稿'},
				{name:'accept',txt:'收录'},
				{name:'publish',txt:'发表'},
				{name:'reject',txt:'拒稿'}
			]
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

buildTopNavbarCom();
buildMainRowCom();
// 激活并设置tooltip
$('body').tooltip({selector:'[title]',triger:'hover click',placement: 'auto bottom',delay: {show: 200, hide: 100},html: true });	

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
		console.log(rqData);
		if(cls.indexOf('disabled')==-1){
			loadIndex();
		}
	});
	
	btnSet.click(function(){
		rqData.ent=$(this).data('ent');
		rqData.period=$(this).data('period');
		loadIndex();
	});
});
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
			p=$('<p></p>').addClass('text-center').text(entObj.noneTxt),
			panH=$('<div></div>').addClass('panel-heading').append($('<span></span>').addClass(entObj.gly),'&nbsp;',$('<strong></strong>').html(entObj.txt)),
			panB=$('<div></div>').addClass('panel-body'),
			pan=$('<div></div>').addClass('panel-group'),
			panType=$('<div></div>').addClass('panel panel-default'),
			div=$('<div></div>').addClass('col-sm-6');
		
		if(typeof numObj=='object'){
			entObj.period.summary.forEach(function(el){
				let num=numObj[el.name],
					href=entObj.url+'#'+el.name,
					btn=$('<button></button>').attr({'class':'btn btn-sm btnPeriod','title':'查看详情'}).css({'margin':'2px','font-size':'14px'}),
					spBdg=$('<span></span>').addClass('badge');
								
				spBdg.text(num);
				btn.attr({'data-url':entObj.url,'data-ent':ent,'data-period':el.name}).addClass(el.color).append(el.txt,spBdg);
				panB.append(btn);
			});
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
		pArr=entProp[ent].period.detail;
	pArr.forEach(function(e){
		let a=$('<a></a>').css({'padding':'3px 10px','border-bottom-right-radius':'0px','border-bottom-left-radius':'0px','cursor':'pointer'}).attr({'data-ent':ent,'data-period':e.name}).html(e.txt);
		
		obj.append($('<li></li>').append(a));
		
	});
	<!--为第一个li标签添加.active -->
	obj.find('li').eq(0).addClass('active');
}

//load模块对应的Index文件
function loadIndex(){
	let row=$('#mainRow'),
		//index文件加载点
		obj=$('#mainPeriod'),
		ent=rqData.ent,
		loadStr='<p class="text-center" style="font-size:20px;padding-top:20px;">加载中……</p>',
		url=topNavProp[ent]['url'];
	
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
//得到title的属性
function getTitleProp(tDefault,tProp=''){
	if(tProp){
		for(let p in tProp){
			tProp[p]=$.extend({},tDefault[p],tProp[p]);
		}
	}else{
		tProp=tDefault;
	}
	return tProp;
}
//设定Title
function setTitle(tProp){
	let spObj=$('<span></span>').css({'cursor':'unset'}),
		spBdg=$('<span></span>').addClass('badge'),
		//title加载点
		obj=$('[id="'+rqData.ent+'Title"]');
	
	for(let period in tProp){
		let e=tProp[period];
		if(period==rqData.period){
			spObj.addClass(e.btn).append(e.txt,spBdg.text(e.num));
			obj.empty().append(spObj);
			break;
		}
	}
}
//所有查询表单重置
function fmSearchReset(){
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
};



