//app/index.js

//conf.js中采用默认输出，c为本文件中使用的hash对象，其键值为'./conf.js'各个导出键值对
import c from './conf.js';
const glyPrex=c.glyPrex;
const bs3Color=c.bs3Color;
const year=c.year;

var userName=c.userName;
var entNum=c.entNum;
var loadStr=c.loadStr;
var topNavProp=c.topNavProp;
var entProp=c.entProp;
var rqData=c.rqData;
var urlObj=c.urlObj;
var searchResultNum=c.searchResultNum;

asyInitData()
.then(function(uName){
	let str='数据初始化失败。页面无法正常显示。';
	
	if(uName){
		str='用户【'+uName+'】登录成功。';
		pageInit();
		pageReady();
	}
	return $.alert(str);
})
.catch((err)=>{
	console.log(err);
})
.finally(()=>{
	
});

//定义async函数asyInitData()进行全局变量赋值，默认返回的是一个promise对象。
async function asyInitData() {
	//$.post()方法返回的是jqXHR对象，这个jqXHR对象是对所发起的request的响应结果
	// let resData = await $.post('/index/index/getInitData');
	//fetch()方法返回的是一个Promise对象，这个promise对象resolve成一个response对象，这个response对象是对所发起的request的响应结果。
	let resObj = await fetch('/index/index/getInitData');
	//完成response对象内容解析
	let resData =await resObj.json();
	// let resData =await resObj.blob();
	
	//全局变量赋值
	userName=resData.userName;
	urlObj=resData.urlObj;
	entNum=resData.entNum;
	rqData=initRqData();
	//完善entProp中的num属性值
	for(let ent in entProp){
		for(let per in entProp[ent].period.detail){
			entProp[ent].period.detail[per].title.num=entNum[ent][per];
		}
	}
	return userName;
}

//页面生命周期函数-init，页面初始化
function pageInit(){
	//生成组件
	buildTopNavbar();
	buildEntSummary();
	
	// 激活并设置tooltip
	$('body').tooltip({selector:'[title]',triger:'hover click',placement:'auto top',delay: {show: 200, hide: 100},html: true });
	$('footer .year').html('2017-'+year);
}
//page-init成功后处理各种事件
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
		
		if(cls.indexOf('disabled')!=-1 ){
			return true;			
		}
		
		$(this).tab('show');
		sumNod.hide();
		perNod.hide();
			
		ent=='index'?sumNod.show():perNod.show();
		
		if(rqData.ent!=ent){
			initRqData();
			rqData.ent=ent;
			rqData.period=topNavProp[ent].period;
			if(ent!='index'){
				// asyEntLoad().catch((e)=>console.log(e));
				// return asyEntLoad();
				// asyEntLoad().then(()=>{return entReady()});
				return entGetReady();
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
		
		return entGetReady();
	});
}
//使用Promise对象实现异步的顺序处理？？
function entGetReady() {
	// Promise.resolve(asyEntLoad())
	asyEntLoad()
	.then(result=>{
		// console.log(result); 
		entReady();
		entEvent();
		// return entReady();
	})
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

//ent-ready
function entReady(){	
	
	//生成ent的period的title
	buildEntPeriodTitle();
	//生成ent的nav-pills
	buildEntPeriodNavPills();
	//设置表格
	setEntPeriodList();
	//设置rqData
	setRqData();
	
	return true;
}

function entEvent(){
	let //在asyEntLoad中已生成
		entPeriodASet=$('[data-period]'),
		clpsSwithcSet=$('[data-collapse-switch]'),
		btnRefresh=$('.btnPageRefresh');

	//周期导航菜单click事件
	entPeriodASet.click(function(){
		let sData=$(this).data();
		$(this).tab('show');
		if(rqData.period!=sData.period){
			rqData.ent=sData.ent;
			rqData.period=sData.period;

			buildEntPeriodTitle();
			asyRefreshEntObj(true);	
		}
	});
	
	// console.log(clpsSwithcSet.length);
	if(clpsSwithcSet.length){
		//动态加载类
		import('./SearchFormCollapse.class.js')
		.then(cls=>{
			let sFCObj=new cls.default;
			let fmId=sFCObj.formId,
				trgObj=sFCObj.status.trigger;

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
	
		})
		.catch(err=>{
			console.log(err);
		});					
	}

	// 页面刷新 
	btnRefresh.click(function(){
		resetSearchForm();
		// sFCObj.reset();
		
		return entGetReady();
	});	
	
	//对BarcodeForm的操作
	entOprtBarcodeForm();
	//对QueryForm的操作
	entOprtQueryForm();
	//对list的操作
	entOprtList();
}

//async 定义了一个promise对象
//异步加载ent对应的searchForm和list
async function asyLoadEntObj(type){
	let conf={
			list:{node:$('#entList'),url:'/index/list/index'},
			form:{node:$('#entSearchForm'),url:'/index/searchForm/index'}
		};
	let opt={
			method:'POST',
			body:JSON.stringify(rqData),
			headers:{
				'Content-Type':'application/json'
				// 'Content-Type':''
			}
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
	let content=await $.post(conf[type].url,rqData);
	result=(content)?true:false;
	
	loadNod.html(content);
	// console.log(resObj.body);
	return result;
}

async function asyRefreshEntObj(len=0) {
	/* if(!len){
		len=Object.keys(rqData.searchData).length;
	}
	
	if(len){
		//异步刷新queryform表单内容。
		asySetEntQueryForm()
		.catch((e)=>console.log(e))
		.finally(()=>{
			return console.log(true);
		});
	}
		
	// 异步重载list
	asyLoadEntObj('list')
	.then(result=>{
		// consoleColor("asyLoadEntObj('list'):");
		// console.log(result);
	})
	.catch((e)=>console.log(e))
	.finally(()=>{
		setRqData(); 
		setEntPeriodList();
		return entOprtList();
	});	 */
	let totalTrue=(acc,cur)=>{
		if(cur){
			acc++;
		}
		return acc;
	};
	let r1= true;
	let r2= asyLoadEntObj('list');
	let result=false;	
	let valArr='';
	
	if(!len){
		len=Object.keys(rqData.searchData).length;
	}
	
	if(len){
		//异步刷新queryform表单内容。
		r1=asySetEntQueryForm();
	}
	
	//await关键字，表示开启异步过程并等待结果
	valArr=await Promise.all([r1,r2]);
	
	if(valArr.reduce(totalTrue,0)){
		result=true;
		setRqData(); 
		setEntPeriodList();		
	}
	
	return entOprtList();
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
		
		asyRefreshEntObj(true);
	});
	//表单重置时附加的操作
	fmQ.find('[type="reset"]').click(function(evt){
		let len=Object.keys(rqData.searchData).length;
		resetSearchForm();
		
		asyRefreshEntObj(len);
	});
}

function entOprtBarcodeForm() {
	//显示识别结果和查询结果
	let queryByCode=(code='') =>{
		let objSuccess=$('#divBarcodeImg div.alert-success').hide();
		let objWarning=$('#divBarcodeImg div.alert-warning').hide();

		if(code){
			objSuccess.show().find('span.alert-info').text(code);
			//设置查询数据
			rqData.searchSource=$('#fmRun').data('formType');
			rqData.searchData={bar_code:code};
		
			//向后端发起异步查询并显示查询结果
			asyLoadEntObj('list')
			.then(()=>{
				setEntPeriodList();
			})
			.catch((e)=>console.log(e));
		}else{
			objWarning.show();
		}	
	};

	//动态加载对象
	import('./Barcode.js')
	.then(module=>{
		let Barcode= module.Barcode;
		Barcode.init($('#fmRun'));
		
		Quagga.onProcessed(function(result){
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
			//若已定义Barcode.state.inputStream.area，则由calculateRectFromArea()函数计算方框4个点的像素坐标
        	if (Barcode.state.inputStream.area) {
            	area = Barcode.getArea(drawingCanvas, Barcode.state.inputStream.area);
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
		
		Quagga.onDetected(function(result){
			queryByCode(result.codeResult.code);
		});
		
		//表单重置
		$('#fmRun button:reset').click(function(){
			$('#divBarcodeImg').prop('hidden',true).find('.viewport').empty();
			resetSearchForm();	
			// 载入list
			asyRefreshEntObj();
		});
	})
	.catch(err=>{
		console.log(err);
	});		
}

function entOprtList() {
	let tblNod=$('#entList table'),
		listRowNod=$('#listRows'),
		aHeadSet=tblNod.find('thead a'),
		aBodySet=tblNod.find('tbody a'),
		aPageSet=$('#divListRows').find('a');
	
	//表格每页显示记录行数；表格按选定行数显示
	listRowNod.val(rqData.sortData.listRows).change(function(){
		//排序有关的值向sortData汇集
		rqData.sortData.listRows=$(this).val()*1;
		//分页从第一页开始
		rqData.sortData.pageNum=1;
		
		// 载入list
		asyRefreshEntObj();
		
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
	
		// 载入list
		asyRefreshEntObj();
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
			rqData.sortData.pageNum=pageNum;
		}
		
		// 载入list
		asyRefreshEntObj();
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
		if(v!=0){
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
async function asySetEntQueryForm(){
	let fm=$('form.fmQuery'),
		selSet=fm.find('select'),
		inSet=fm.find('input'),
		//查询字段名数组
		sNameArr='';
	let resObj='';
	let opt={
			method:'POST',
			body:'',
			headers:{
				'Content-Type':'application/json'
			}
		};
	let optData='';
	let result=false;
	
	rqData.searchSource=fm.data('formType');
	setRqQueryFieldBy(fm);
	
	opt.body= JSON.stringify(rqData);
	
	resObj=await fetch('/index/searchForm/getSelOptData',opt);
	optData=await resObj.json();
	result=resObj.ok;

	sNameArr=Object.keys(rqData.searchData);

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
	result=true;
	}
	
	if(sNameArr.length){
		//显示整个form
		fm.closest('.collapse').collapse('show');
		inSet.each(function(){
			let n=$(this).attr('name');
			if(sNameArr.includes(n)){
				//赋值
				$(this).val(rqData.searchData[n]);
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
		nod=$('#entPeriod').children('h4.title');
		// obj=$('[id="'+ent+'Title"]');
	nod.empty();
	for(let p in tProp){
		let e=tProp[p].title;
		if(p==rqData.period){
			spObj.addClass(e.btn).append(e.txt,spBdg.text(e.num));
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
		aHSet=tblNod.find('[data-sort-name]'),
		trSet=tblNod.find('tr'),
		column='',
		columnArr=['num'];
	
	if(sortName==''){
		sortName=aHSet.eq(0).data('sortName');
		rqData.sortData.sortName=sortName;
	}
	
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
	searchResultNum=parseInt($('#searchResultNum').text());
	
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


//各个事件处理函数
