// app/main.js
// import {glyPrex,domain,loadStr,bs3Color,topNavProp,entProp,rqData,urlObj,searchResultNum,year} from './conf.js';
// import * from './conf.js';

//匿名类，然后赋值给变量searchFormCollapse，（类表达式定义的类，不会进行提升）要先声明再使用，否则就会报‘ReferenceError’错
var searchFormCollapse= class {
	constructor(){
		let self=this;
		self.name=searchFormCollapse;
		self.divSet=$('[data-collapse-switch="div"]').parent();
		self.liSet=$('[data-collapse-switch="li"]').parent();
		self.bs3Cls={liShow:'active',divShow:'in',fn:{show:'show',hide:'hide'}};
		self.formId=self.initFormId();
		self.status=self.initStatus();		
	}
	initFormId(){
		let arr=[];
		this.divSet.each(function(index){
			arr[index]=$(this).attr('id');
		});
		return arr;
	}
	initStatus(){		
		/* 状态对象。
	 * multishow，bool，是否为多个组件可同时显示。
	 * type:'main'且multishow:true时，index为数组，记录要显示组件index。
	 * type:'main'且multishow:false时，index为数字，记录要显示组件index。
	 * type:'li'/'div'时，index为数字，值为触发状态改变的组件index。
	 * showArr，array，index为组件的index，value为组件是否显示true/false
	 */
		return {trigger:{type:'',index:'',multishow:false},showArr:this.zArr()};
    }
	zArr(){
		return Array(this.formId.length).fill(false);
	}
	
	//定义数组的reduce方法中使用的reducer	
	totalTrue(acc,cur){
		if(cur){
			acc++;
		}
		return acc;
	}
	getStatus(){
		let self=this,
			idx='';
		self.divSet.each(function(){
			idx=self.formId.indexOf($(this).attr('id'));
			//jQ中的hasClass()方法不起作用？
			// $(this).hasClass(self.bs3Cls.divShow)?status.showArr[idx]=true:status.showArr[idx]=false; 
			$(this).attr('class').indexOf(self.bs3Cls.divShow)!=-1?self.status.showArr[idx]=true:self.status.showArr[idx]=false;
		});
		return self.status;
	}
	reset() {
		let self=this;
		self.status= self.setCollapse({trigger:{type:'main',index:'0',multishow:''},showArr:self.zArr()});
		
		return self.status;
	}
	//设定status中的值
	setStatus(opt={}) {
		let self=this;
		let type='',
			idx='',
			showVal='',
			status=$.extend({},self.status,opt);
		
		type=status.trigger.type;
		idx=status.trigger.index;
		
		if(status.trigger.multishow==false){
			self.status=status;
			return self.setStatusSingle();
		}

		if(type=='main'){
			if(idx === ''){
				status.showArr=self.zArr();
				// status.trigger.index=0;
				status.trigger.index=[0];
				status.showArr[0]=true;
			}
			if(Array.isArray(idx)){
				//根据数组idx改变对应status.showArr的值
				status.showArr.forEach(function(v,i){
					if(idx.includes(i)){
						status.showArr[i]=!v;
					}
				});
			}
			
			return self.status=status;
		}
		
		//根据idx的值修改对应status.showArr值
		if(type=='li'){
			showVal=status.showArr[idx];
			status.showArr[idx]=!showVal;
		}
		if(type=='div'){
			status.showArr[idx]=false;
		}
		//根据status.showArr的值再修改status.trigger.index为数组
		if(status.showArr.reduce(self.totalTrue(),0)){
			idx=[];
			status.showArr.forEach(function(v,i){
				if(v){
					idx.push(i);
				}
			});
		}
		status.trigger.index=idx;
		return self.status=status;
	}
	//设定status中的值，只能显示一个searchForm及其对应组件
	setStatusSingle(){
		let self=this,
			status=self.status;
		let type=status.trigger.type,
			idx=status.trigger.index,
			showVal='';;
		
		//只有showIndex[idx]的值有改变，其他的都为false	
		if(type=='li'){
			showVal=status.showArr[idx];
			status.showArr=self.zArr();
			status.showArr[idx]=!showVal;
		}
		if(type=='main'){
			if(idx === ''){
				status.showArr=self.zArr();
				status.trigger.index=0;
				status.showArr[0]=true;
			}
			if((typeof idx) == 'number'){
				showVal=status.showArr[idx];
				status.showArr=self.zArr();
				status.showArr[idx]=!showVal;
			}
		}
		if(type=='div'){
			status.showArr[idx]=false;
		}
		return self.status=status;
	}
	//根据status对有关组件进行显示、隐藏
	setCollapse(opt={}) {
		let self=this;
		let	status=self.setStatus(opt),
			bs3Cls=self.bs3Cls;
		
		status.showArr.forEach(function(v,i){
			if(v){
				self.divSet.eq(i).collapse(bs3Cls.fn.show);
				self.liSet.eq(i).addClass(bs3Cls.liShow);
			}else{
				self.divSet.eq(i).collapse(bs3Cls.fn.hide);
				self.liSet.eq(i).removeClass(bs3Cls.liShow);
			}
		});
		return self.status=status;
	}
};

$.when(
	//请求页面初始数据
	$.post('index/index/index',{getPageInitData:true})
).then(function(resData){
		//页面初始化
		pageInit(resData);
		pageReady();
	},function(){
		$.alert('页面初始化失败。');
	}
);

//页面生命周期函数-init
function pageInit(resData){
	//全局变量赋值
	urlObj=resData.urlObj;
	entNum=resData.entNum;
	rqData=initRqData();
	//完善entProp中的num属性值
	for(let ent in entProp){
		for(let per in entProp[ent].period.detail){
			entProp[ent].period.detail[per].title.num=entNum[ent][per];
		}
	}
	
	//生成组件
	buildTopNavbar();
	buildEntSummary();
	
	// 激活并设置tooltip
	$('body').tooltip({selector:'[title]',triger:'hover click',placement:'auto top',delay: {show: 200, hide: 100},html: true });
	$('footer .year').html('2017-'+year);
}
//page-ready后处理各种事件
function pageReady(){
	let sumNod=$('#entSummary').show(),
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
			
		$(this).tab('show');
		sumNod.hide();
		perNod.hide();
			
		ent=='index'?sumNod.show():perNod.show();
		
		if(rqData.ent!=ent){
			initRqData();
			rqData.ent=ent;
			rqData.period=topNavProp[ent].period;
			if(cls.indexOf('disabled')==-1 && ent!='index'){
				entLoad();
			}
		}
	});
	
	btnEntPeriod.click(function(){
		let ent=$(this).data('ent');
		sumNod.hide();
		perNod.show();
		
		//ent对应的navTop的li添加'active'
		entASet.closest('li').removeClass('active').find('[data-ent="'+ent+'"]').tab('show');
		
		rqData.ent=ent;
		rqData.period=$(this).data('period');
		
		entLoad();
	});
}
//ent-load
function entLoad(){	
	//选择操作节点
	let loadFmNod=$('#entSearchForm'),
		loadListNod=$('#entList');
	// let url=urlObj.domain+'/index/SearchForm/index';
	//生成ent的nav-pills
	buildEntPeriodNavPills();
	//生成ent的period的title
	buildEntPeriodTitle();
	
	$.when(
		//异步请求entSearchForm模板
		$.post('/index/SearchForm/index',rqData),
		//异步请求entPeriodList模板
		$.post('/index/List/index',rqData),
	).then(function(p1,p2){
		//载入获得的模板
		loadFmNod.html(p1[0]);
		//载入获得的模板
		loadListNod.html(p2[0]);
		
		return entReady();
	},function(){
		return  $.alert('请求数据失败。');
	});
}
//ent-ready
function entReady(){
	let //在entLoad中已生成
		entPeriodASet=$('#entPeriod').children('.nav-pills').find('a'),
		clpsSwithcSet=$('[data-collapse-switch]'),
		btnRefresh=$('.btnPageRefresh');
	//collapse类的定义
	let	sFCObj=new searchFormCollapse(),
		fmId=sFCObj.formId,
		trgObj=sFCObj.status.trigger;
	
	setRqData();
	setEntQueryForm();
	setEntPeriodList();
	
	//周期导航菜单click事件
	entPeriodASet.click(function(){
		let sData=$(this).data();
		$(this).tab('show');
		if(rqData.period!=sData.period){
			rqData.ent=sData.ent;
			rqData.period=sData.period;

			return entLoad();	
		}
	});
	//3类共5个collapse-switch组件的click事件，
	//任意时刻只有一个组件能click。记录触发click的组件特征值，再由特征值决定组件的显示
	clpsSwithcSet.click(function(){
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
	// 页面刷新 
	btnRefresh.click(function(){
		resetSearchForm();
		// sFCObj.reset();
		return entLoad();
	});	
	// consoleColor('entReady() rqData','red');
	// console.log(rqData);
	
	//对QueryForm的操作
	entOprtQueryForm();
	
	//对BarcodeForm的操作
	entOprtBarcodeForm();
	
	//对list的操作
	entOprtList();
}

function entOprtQueryForm() {
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
		return entLoad();
	});
	//表单重置时附加的操作
	fmQ.find('[type="reset"]').click(function(evt){
		resetSearchForm();
		//载入list
		return loadEntPeriodList();
	});
	
}

function entOprtBarcodeForm() {
//封装对象。含条形码进行识别所需参数的预处理，识别过程。
var barcode={
	//将形如aaa_bb-cc-dd的字符串转换为aaa.bbCcDd
	convertNameToState:function(name) {
		return name.replace('_','.').split('-').reduce(function(result,val){
			return result+val.charAt(0).toUpperCase()+val.substring(1);
		});
	},
    _accessByPath: function(obj, path, val) {
        var parts = path.split('.'),
            depth = parts.length,
            setter = (typeof val !== "undefined") ? true : false;	
		// console.log('_accessByPath | parts:'+parts+' depth:'+depth+' setter:'+setter+' val:'+val);
		// console.log('_accessByPath |'+typeof obj);
        return parts.reduce(function(o, key, i) {
            if (setter && (i + 1) === depth) {
                o[key] = val;
            }
            return key in o ? o[key] : {};
        }, obj);
    },
	decode: function(src) {
        var self = this,
        config = $.extend({}, self.state, {src: src});
		Quagga.decodeSingle(config, function(result){});
    },
	setState: function(path, val) {
		var self=this;
		var typeStr = typeof self._accessByPath(self.inputMapper,path);
		
		// console.log('func setState() recieved: '+path+':'+val); 
		// console.log('**1 self.inputMapper: '+JSON.stringify(self.inputMapper)); 		
		// console.log('**2 self._accessByPath: '+typeStr); 
		// console.log(self._accessByPath(self.inputMapper,path)); 
		
		if(typeStr==='function'){
			//通过self._accessByPath(self.inputMapper,path)函数转换val
			val=self._accessByPath(self.inputMapper,path)(val);
		}
	
		self._accessByPath(self.state,path,val);
		//console.log('**3 '+JSON.stringify(self.state)); 
	},
	inputMapper: {
        inputStream: {
            size: function(v){
                return parseInt(v);
            }
        },
        numOfWorkers: function(v) {
            return parseInt(v);
        },
        decoder: {
            readers: function(v) {
                if (v === 'ean_extended') {
                    return [{
                        format: 'ean_reader',
                        config: {
                            supplements: [
                                'ean_5_reader', 'ean_2_reader'
                            ]
                        }
                    }];
                }
                return [{
                    format: v + '_reader',
                    config: {}
                }];
            }
        }
    },
	state: {
		inputStream:{
			size:800,
			singleChannel:false
		},
		locator:{
			patchSize:'medium',
			halfSample:true	
		},
		decoder:{
			readers:[{
				format:'code_93_reader',
				config:{}
			}]
		},
		locate:true,
		src:null
	}
};

//$(document).ready(function(){});可简写为$(function(){});
// $(function() {
	$('#fmRun input[type=file]').change(function(){
		var fileObj=$(this)[0];
		$('#fmRun .form-control').removeClass('alert-info');
		
		$('#divBarcodeImg').hide();
		// onProcessedFlag=0;
		
		if(fileObj.files && fileObj.files.length){
			barcode.decode(URL.createObjectURL(fileObj.files[0]));
			$(this).addClass('alert-info');
			$('#divBarcodeImg').show().children('.alert').hide();
			// onProcessedFlag=1;
		}
		console.table(fileObj.files[0]);
	});
	//开始识别条码		
	$('#btnRun').click(function(){
		let input=$('#fmRun input[type=file]')[0],
			src='';
		
		if(input.files && input.files.length){
			src=URL.createObjectURL(input.files[0]);
			barcode.decode(src);
		}else{
			$.alert('<p class="text-center">请选择需识别的条形码图片<p>');
		}
	});
	//表单输入项发生改变
	$('#fmRun').find('[name]').on('change','input,select',function(){
		let val=$(this).attr('type')==='checkbox'?$(this).prop('checked'):$(this).val(),
			name=$(this).attr('name'),
			state=barcode.convertNameToState(name); 
		$(this).addClass('alert-info');	
		// console.log("Value of "+ state + " changed to " + val);
        barcode.setState(state, val);
	});
	//表单重置
	$('#fmRun button:reset').click(function(){
		$('#divBarcodeImg').prop('hidden',true).find('.viewport').empty();
		resetSearchForm();	
		//载入list
		return loadEntPeriodList();
	});
	
	//识别处理过程		
	Quagga.onProcessed(function(result){
		// console.table(result);
		var drawingCtx=Quagga.canvas.ctx.overlay,
        	drawingCanvas = Quagga.canvas.dom.overlay,
        	area;
		//有识别结果数据
		if (result) {
        	//绿色方框标识出条形码区域
			if (result.boxes) {
            	drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.getAttribute("width")), parseInt(drawingCanvas.getAttribute("height")));
            	result.boxes.filter(function (box) {
                	return box !== result.box;
            	}).forEach(function (box) {
                	Quagga.ImageDebug.drawPath(box, {x: 0, y: 1}, drawingCtx, {color: "green", lineWidth: 2});
            	});
        	}
			//识别出条形码值，条形码区域方框为蓝色
        	if (result.box) {
            	Quagga.ImageDebug.drawPath(result.box, {x: 0, y: 1}, drawingCtx, {color: "#00F", lineWidth: 2});
        	}
			//识别出条形码值，条形码上添加红色横线
        	if (result.codeResult && result.codeResult.code) {
            	Quagga.ImageDebug.drawPath(result.line, {x: 'x', y: 'y'}, drawingCtx, {color: 'red', lineWidth: 3});
        	}
			//若已定义barcode.state.inputStream.area，则由calculateRectFromArea()函数计算方框4个点的像素坐标
        	if (barcode.state.inputStream.area) {
            	area = calculateRectFromArea(drawingCanvas, barcode.state.inputStream.area);
           	 	drawingCtx.strokeStyle = "#0F0";
            	drawingCtx.strokeRect(area.x, area.y, area.width, area.height);
        	}
			//没有识别出条形码值
			if(!(result.hasOwnProperty('codeResult'))){
				queryByCode();				
			}
    	}else{
			//没有识别结果数据
			queryByCode();	
		}
	});
	
	//成功识别条形码值后的处理
	Quagga.onDetected(function(result){
		queryByCode(result.codeResult.code);
	});
// });

//显示识别结果和查询结果
function queryByCode(code='') {
	// let objSuccess=$('#divBarcodeImg div.alert-success').prop('hidden',true);
	// let objWarning=$('#divBarcodeImg div.alert-warning').prop('hidden',true);
	
	let objSuccess=$('#divBarcodeImg div.alert-success').hide();
	let objWarning=$('#divBarcodeImg div.alert-warning').hide();

	consoleColor('fn:queryByCode, code:','green');
	console.log(code);	
	
	if(code){
		objSuccess.show().find('span.alert-info').text(code);
		//设置查询数据
		rqData.searchSource=$('#fmRun').data('formType');
		rqData.searchData={bar_code:code};
		
		//向后端发起查询并显示查询结果
		loadEntPeriodList();
		// return entLoad();	
	}else{
		/* objWarning.show(); */
		console.log('else');	
		objWarning.show();
	}	
}

function calculateRectFromArea(canvas,area){
	var canvasWidth=canvas.width,
		canvasHeight=canvas.height,
		top=parseInt(area.top)/100,
		right=parseInt(area.right)/100,
		bottom=parseInt(area.bottom)/100,
		left=parseInt(area.left)/100;
	
	top *= canvasHeight;
    right = canvasWidth - canvasWidth*right;
    bottom = canvasHeight - canvasHeight*bottom;
    left *= canvasWidth;

	return {
		x:left,	
		y:top,
		width:right-left,
		height:bottom-top
	};
}
}

function entOprtList() {
	let tblNod=$('#entList table'),
		listRowNod=$('#listRows'),
		aHeadSet=tblNod.find('thead a'),
		aBodySet=tblNod.find('tbody a'),
		aPageSet=$('#divListRows a');
	
	//表格每页显示记录行数；表格按选定行数显示
	listRowNod.val(rqData.sortData.listRows).change(function(){
		//排序有关的值向sortData汇集
		rqData.sortData.listRows=$(this).val()*1;
		//分页从第一页开始
		rqData.sortData.pageNum=1;
		
		return entLoad();
	});	
	//表格按选定字段排序
	aHeadSet.click(function(){
		let sortName=$(this).data('sortName');

		rqData.sortData.sortOrder=(rqData.sortData.sortOrder=='asc')?'desc':'asc';
	
		//排序有关的值向sortData汇集
		if(rqData.sortData.sortName!=sortName){
			rqData.sortData.sortName=sortName;
			rqData.sortData.sortOrder='asc';	
		}
		rqData.sortData.pageNum=1;
	
		return entLoad();
	});	
	//表格中点击a后，标签所在行上底色
	aBodySet.click(function(){
		rqData.sortData.showId=$(this).closest('tr').data('showId');
		
		return setTrBgColor();
	});
	//表格显示分页内容
	aPageSet.click(function(evt){
		// a所在分页页数
		let pageStr=$(this).text(),
			pageNum=0
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
			rqData.sortData.pageNum=pageNum;
		}
		
		return entLoad();
	});
}
//setRqData
function setRqData() {	
	let fm=$('form.fmQuery'),
		entHArr=$('#entPeriod').find('.nav-pills li.active a').data(),
		sortName=$('#entList').find('[data-sort-name]').eq(0).data('sortName'),
		showId=$('#entList').find('[data-show-id]').eq(0).data('showId');
		
	rqData.ent=entHArr.ent;
	rqData.period=entHArr.period;
	
	if(rqData.sortData.sortName==''){
		rqData.sortData.sortName=sortName;	
	}
	
	fm.find('[name]').each(function(){
		let n=$(this).attr('name'),
			//null转为0
			v=$(this).val()==null?0:$(this).val(),
			lName=$(this)[0].localName;
			
		//赋值rqData.searchData，非0非空的值才进行组装
		if(v){
			rqData.searchData[n]=v;
		}
		
		//赋值rqData.queryField，表单输入项及其特征值
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
	
	return rqData;
}
//设置list
function setEntPeriodList(){	
	let ent=rqData.ent,
		period=rqData.period,
		capSNod=$('#entList').find('caption strong'),
		periodChi=entProp[ent].period.detail[period].txt;

	//显示分页的第一页
	rqData.sortData.pageNum=1;
	
	//tbl标题加内容
	capSNod.text(periodChi);
	//tbl排序	
	sortEntListTbl();
	//指定行加底色	
	setTrBgColor();
	//显示搜索结果数
	showSearchResult();	
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
	rqData={
		ent:'index',
		period:'',
		sortData:{listRows:10,sortName:'',sortOrder:'asc',pageNum:1,showId:''},
		searchSource:'',
		searchData:{},
		queryField:{}
	};	
	return rqData;
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

//设定$('form.fmQuery')的各个表单项
function setEntQueryForm(){
	let fm=$('form.fmQuery'),
		selSet=fm.find('select'),
		inSet=fm.find('input'),
		searchSrc=rqData.searchSource,
		//查询字段名数组
		sNameArr=Object.keys(rqData.searchData);
	
	urlObj.ctrl='searchForm';
	urlObj.action='getSelOptData';
	
	// if(searchSrc!=fm.data('formType')){
		
		// return $('[data-form-type="'+searchSrc+'"]').closest('.collapse').collapse();
		
	// }
	
	if(sNameArr.length){
		//显示整个form
		fm.closest('.collapse').collapse();
			//设定input的显示值和底色，并显示
		inSet.each(function(){
			let n=$(this).attr('name');
			if(sNameArr.includes(n)){
				//赋值
				$(this).val(rqData.searchData[n]);
				//上底色
				$(this).addClass('alert-info');
				//显示
				$(this).closest('.collapse').collapse();
			}
		});
	}
	
	$.when(
		$.post(getRqUrl(),rqData)
	).then(function(optData){
		
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
			
			if(sNameArr.length && sNameArr.includes(selName)){
				v=rqData.searchData[selName];
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
	},function(){
		$.alert('与服务器连接失败。');
	}
	);
	
}

//组装向后端请求时的searchData，不带参数就是清空searchData
function setRqSearchDataBy(fm=''){
	let formData='';
	rqData.searchData={};
	
	if(typeof fm !='object' || fm[0].localName !=='form'){
		return rqData.searchData;
	}
	rqData.searchSource=fm.data('formType');
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
		if($(this).siblings().length){
			$(this).siblings().hide();
		}
	});
	//清空查询数据
	return setRqSearchDataBy();
	
}

//对ent List进行排序
//列表字段进行升序降序转换，改变该字段表头显示格式
function sortEntListTbl() {
	let glyAsc=$('<span></span>').addClass('small glyphicon glyphicon-sort-by-attributes'),
		glyDesc=$('<span></span>').addClass('small glyphicon glyphicon-sort-by-order-alt'),
		sortOrder=rqData.sortData.sortOrder,
		sortName=rqData.sortData.sortName,
		tblNod=$('#entList table'),
		aHSet=tblNod.find('thead a'),
		trSet=tblNod.find('tr'),
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
	tblNod.find('tbody [data-column="'+sortName+'"]').addClass('text-left bg-info');
}

function setTrBgColor() {
	let id=rqData.sortData.showId,
		nod=$('#entList table');
	//去掉所有行的底色
	nod.find('tr').removeClass('bg-warning');
	
	//给showId所在行上底色
	nod.find('[data-show-id="'+id+'"]').addClass('bg-warning');
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
		n=(sData[el])?n+1:n;
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
//
function loadEntPeriodList() {
	let rData=rqData.searchData;
		listNod=$('#entList');
	
	urlObj.module='index';
	urlObj.ctrl='list';
	urlObj.action='index';
	consoleColor('loadEntPeriodList() rqData','green');
	console.log(rqData);
	
	listNod.html(loadStr).load(getRqUrl(),rqData,function(){
		setEntPeriodList();
	});
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
