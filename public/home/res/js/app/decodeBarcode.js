// decodeBarcode.js -->

//封装对条形码进行识别所需参数的预处理过程，识别过程。
var App={
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
//显示识别结果和查询结果
function queryByCode(code='') {
	let objSuccess=$('#divBarcodeImg div.alert-success').hide();
	let objWarning=$('#divBarcodeImg div.alert-warning').hide();
		
	if(code){
		objSuccess.show().find('span.alert-info').text(code);
		//设置查询数据
		rqData.searchData={bar_code:code};
		console.log(rqData.searchData);
		//向后端发起查询并显示查询结果
		loadEntPeriodList();
	}else{
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
//利用自调用匿名函数立即执行的特点， 页面初始化
(function($){
	$('#divBarcodeImg').hide();
	$('#interactive').prevAll().hide(); 
})(jQuery);

//$(document).ready(function(){});可简写为$(function(){});
$(function() {
	$('#fmRun input[type=file]').change(function(){
		var fileObj=$(this)[0];
		$('#fmRun .form-control').removeClass('alert-info');
		
		$('#divBarcodeImg').hide();
		onProcessedFlag=0;
		
		if(fileObj.files && fileObj.files.length){
			App.decode(URL.createObjectURL(fileObj.files[0]));
			$(this).addClass('alert-info');
			$('#divBarcodeImg').show();
			onProcessedFlag=1;
		}
		//console.table(fileObj.files[0]);
	});
	//开始识别条码		
	$('#btnRun').click(function(){
		var input=$('#fmRun input[type=file]')[0];
			
		if(input.files && input.files.length){
			let src=URL.createObjectURL(input.files[0])
			App.decode(src);
		}else{
			$.alert('<p class="text-center">请选择需识别的条形码图片<p>');
		}
	});
	//表单输入项发生改变
	$('#fmRun').find('[name]').on('change','input,select',function(){
		var val=$(this).attr('type')==='checkbox'?$(this).prop('checked'):$(this).val(),
			name=$(this).attr('name'),
			state=App.convertNameToState(name); 
		$(this).addClass('alert-info');	
		// console.log("Value of "+ state + " changed to " + val);
        App.setState(state, val);
	});
	//表单重置
	$('#fmRun button:reset').click(function(){
		$('#fmRun .form-control').removeClass('alert-info');
		$('#divBarcodeImg span').html('');
		$('#divBarcodeImg').hide();
		
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
			//若已定义App.state.inputStream.area，则由calculateRectFromArea()函数计算方框4个点的像素坐标
        	if (App.state.inputStream.area) {
            	area = calculateRectFromArea(drawingCanvas, App.state.inputStream.area);
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
});



