// app/Barcode.js

// 导入条形码识别插件??
// import Quagga from '../plugins/quaggaJs/quagga.min.js';
// import {module.exports as Quagga} from '../plugins/quaggaJs/quagga.js';

// export {root as Quagga} from '../plugins/quaggaJs/quagga.min.js';

// import '../plugins/quaggaJs/quagga.min.js';

//导入index.js中定义的变量、函数
// import {rqData,resetSearchForm,asyRefreshEntObj} from './index.js';

import {App} from './main.js';
import {resetSearchForm,asyRefreshEntObj} from './utility.js';

//定义导出对象Barcode。封装条形码进行识别所需参数的预处理，识别过程。
export var Barcode={
	init:function(fm) {
		let self=this;
		if(typeof fm !='object' || fm[0].localName !=='form'){
		return '条码识别模块初始化失败。';
		}
	
		self._node={
			fm:fm,
			img:fm.next(),
			file:fm.find('input[type=file]'),
			run:fm.find('.btnRun'),
			inSet:fm.find('[name]')
		};
		
		self.attachListenners();
	},
	code:'',
	_node:{
		fm:'',
		img:'',
		file:'',
		run:'',
		inSet:''
	},
	attachListenners: function() {
		let self=this,
			node=self._node;
		
		//文件输入框发生变化
		node.file.on('change',function(){
		// self._fileNod.change(function(){
			let fileObj=$(this)[0];
			node.img.hide();
			//去底色
			node.fm.find('.form-control').removeClass('alert-info');
			
			if(fileObj.files && fileObj.files.length){
				self.decode(URL.createObjectURL(fileObj.files[0]));
				//添加表单标记
				App.data.rqData.searchSource=node.fm.data('formType');
				//加底色
				$(this).addClass('alert-info');
				node.img.show().children('.alert').hide();
			}
		});
		//开始识别条码		
		node.run.on('click',function(){
			let input=node.file[0];
		
			if(input.files && input.files.length){	
				self.decode(URL.createObjectURL(input.files[0]));
				//添加表单标记
				App.data.rqData.searchSource=node.fm.data('formType');
			}else{
				$.alert('<p class="text-center" ><span class="label label-warning" style="font-size:16px;" >请选择条形码图片</span><p>');
			}
		});
		//表单输入项发生改变
		node.inSet.on('change',function(){
		// self._inSet.change(function(){
			let val=$(this).attr('type')==='checkbox'?$(this).	prop('checked'):$(this).val(),
				name=$(this).attr('name'),
				state=self._convertNameToState(name); 
			
			$(this).addClass('alert-info');	
        	self.setState(state, val);
		});
		//表单重置
		node.fm.find('button:reset').click(function(){
			node.img.prop('hidden',true).find('.viewport').empty();
			resetSearchForm();	
			//异步更新list
			asyRefreshEntObj('list');
		});
	},
	_accessByPath: function(obj, path, val) {
        var parts = path.split('.'),
            depth = parts.length,
            setter = (typeof val !== "undefined") ? true : false;	
        return parts.reduce(function(o, key, i) {
            if (setter && (i + 1) === depth) {
                o[key] = val;
            }
            return key in o ? o[key] : {};
        }, obj);
    },
	//将形如aaa_bb-cc-dd的字符串转换为aaa.bbCcDd
	_convertNameToState:function(name) {
		return name.replace('_','.').split('-').reduce(function(result,val){
			return result+val.charAt(0).toUpperCase()+val.substring(1);
		});
	},
	detachListenners: function() {
		let node = this._node;
		node.file.off('change');
		node.run.off('click');
		node.inSet.off('change');
		node.fm.find('button:reset').off('click');
	},
	//调用Qugga插件识别条码
	decode: function(src) {
        let self = this,
        	config = $.extend({}, self.state, {src: src});
		Quagga.decodeSingle(config, result=>{
			self.setCode(result);
			return self.processCode();
		});
    },
	setState: function(path, val) {
		let self=this;
		let typeStr = typeof self._accessByPath(self.inputMapper,path);
			
		if(typeStr==='function'){
			//通过self._accessByPath(self.inputMapper,path)函数转换val
			val=self._accessByPath(self.inputMapper,path)(val);
		}
	
		self._accessByPath(self.state,path,val);
		self.detachListenners();
		self.init(self._node.fm);
	},
	getArea: function(canvas,area) {
		return calculateRectFromArea(canvas,area);
	},
	setCode:function(result){
		this.code=dealDecodeResult(result);
	},
	processCode:function(){
		return queryByCode(this.code);	
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
}

function calculateRectFromArea(canvas,area){
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
//利用Quagga插件和上述Barcode对象，根据条码识别结果对条码图片进行标注
function dealDecodeResult(result){
	let drawingCtx=Quagga.canvas.ctx.overlay,
        drawingCanvas = Quagga.canvas.dom.overlay,
        area='';
	//条形码值赋值
	let code='';
			
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
			code=result.codeResult.code;
        }
		//若已定义Barcode.state.inputStream.area，则由calculateRectFromArea()函数计算方框4个点的像素坐标
        if (Barcode.state.inputStream.area) {
            area = Barcode.getArea(drawingCanvas, Barcode.state.inputStream.area);
           	 drawingCtx.strokeStyle = "#0F0";
            drawingCtx.strokeRect(area.x, area.y, area.width, area.height);
        }
    }
	return code;
}

//定义函数，显示识别结果和查询结果
function queryByCode(code='') {
	let rData=App.data.rqData;
	let fm=Barcode._node.fm;
	let img=Barcode._node.img;
	let nodSuccess=img.find('.alert-success').hide();
	let nodWarning=img.find('.alert-warning').hide();
	if(code){
		nodSuccess.show().find('span.alert-info').text(code);
		//设置查询数据
		rData.searchSource=fm.data('formType');
		rData.searchData={bar_code:code};
		
		//向后端发起异步查询并显示查询结果
		asyRefreshEntObj('list');
	}else{
		nodWarning.show();
	}	
}

//将导出变量App定义为本模块export的default变量
export {Barcode as default};