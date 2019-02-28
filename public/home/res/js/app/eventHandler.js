//index.js
// app/eventHandler.js
//各个事件处理函数

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
				return entLoad();
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
		
		return entLoad();
	});
}

let entPeriodASet=$('#entPeriod').children('.nav-pills').find('a'),
	clpsSwithcSet=$('[data-collapse-switch]'),
	btnRefresh=$('.btnPageRefresh');
	//collapse类的定义
let	sFCObj=new sfcCls(),
	fmId=sFCObj.formId,
	trgObj=sFCObj.status.trigger;
	
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

//$('form.fmQuery')表单
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

//barcode表单
	//显示识别结果和查询结果
	let queryByCode=(code='') =>{
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

	let calculateRectFromArea=(canvas,area)=>{
		let canvasWidth=canvas.width,
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
	$('#fmRun input[type=file]').change(function(){
		var fileObj=$(this)[0];
		$('#fmRun .form-control').removeClass('alert-info');
		
		$('#divBarcodeImg').hide();
		// onProcessedFlag=0;
		
		if(fileObj.files && fileObj.files.length){
			Barcode.decode(URL.createObjectURL(fileObj.files[0]));
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
			Barcode.decode(src);
		}else{
			$.alert('<p class="text-center">请选择需识别的条形码图片<p>');
		}
	});
	//表单输入项发生改变
	$('#fmRun').find('[name]').on('change','input,select',function(){
		let val=$(this).attr('type')==='checkbox'?$(this).prop('checked'):$(this).val(),
			name=$(this).attr('name'),
			state=Barcode.convertNameToState(name); 
		$(this).addClass('alert-info');	
		// console.log("Value of "+ state + " changed to " + val);
        Barcode.setState(state, val);
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
			//若已定义Barcode.state.inputStream.area，则由calculateRectFromArea()函数计算方框4个点的像素坐标
        	if (Barcode.state.inputStream.area) {
            	area = calculateRectFromArea(drawingCanvas, Barcode.state.inputStream.area);
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
}

//list 

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



