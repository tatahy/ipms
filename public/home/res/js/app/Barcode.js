// app/Barcode.js

// 导入条形码识别插件??
// import Quagga from '../plugins/quaggaJs/quagga.min.js';
// import {default:Quagga} from '../plugins/quaggaJs/quagga.js';
// export {default:Quagga} from '../plugins/quaggaJs/quagga.js';

//定义对象。封装条形码进行识别所需参数的预处理，识别过程。
export var Barcode={
	init:function(fm) {
		let self=this;
		if(typeof fm !='object' || fm[0].localName !=='form'){
		return '条码识别模块初始化失败。';
		}
		self._fm=fm;
		self._imgNod=fm.next();
		self._fileNod=fm.find('input[type=file]');
		self._runBtn=fm.find('button[type=button]');
		self._inSet=fm.find('[name]');
		Barcode.attachListenners();
	},
	_fm:'',
	_fileNod:'',
	_runBtn:'',
	_inSet:'',
	_imgNod:'',
	attachListenners: function() {
		let self=this;
		
		//文件输入框发生变化
		self._fileNod.on('change',function(){
		// self._fileNod.change(function(){
			let fileObj=$(this)[0];
			
			self._fm.find('.form-control').removeClass('alert-info');
			self._imgNod.hide();
			if(fileObj.files && fileObj.files.length){
				Barcode.decode(URL.createObjectURL(fileObj.files[0]));
				$(this).addClass('alert-info');
				self._imgNod.show().children('.alert').hide();
			}
		});
		//开始识别条码		
		self._runBtn.on('click',function(){
			let input=self._fileNod[0];
		
			if(input.files && input.files.length){
				Barcode.decode(URL.createObjectURL(input.files[0]));
			}else{
				$.alert('<p class="text-center">请选择需识别的条形码图片<p>');
			}
		});
		//表单输入项发生改变
		self._inSet.on('change',function(){
		// self._inSet.change(function(){
			let val=$(this).attr('type')==='checkbox'?$(this).	prop('checked'):$(this).val(),
				name=$(this).attr('name'),
				state=self._convertNameToState(name); 
			
			$(this).addClass('alert-info');	
        	self.setState(state, val);
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
		let self = this;
		self._fileNod.off('change');
		self._runBtn.off('click');
		self._inSet.off('change');
	},
	decode: function(src) {
        let self = this,
        	config = $.extend({}, self.state, {src: src});
		Quagga.decodeSingle(config, function(result){});
    },
	setState: function(path, val) {
		let self=this;
		let typeStr = typeof self._accessByPath(self.inputMapper,path);
			
		if(typeStr==='function'){
			//通过self._accessByPath(self.inputMapper,path)函数转换val
			val=self._accessByPath(self.inputMapper,path)(val);
		}
	
		self._accessByPath(self.state,path,val);
		
		// console.log(JSON.stringify(self.state));
		Barcode.detachListenners();
		Barcode.init(self._fm);
	},
	getArea: function(canvas,area) {
		return calculateRectFromArea(canvas,area);
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


// export {Barcode,Quagga};