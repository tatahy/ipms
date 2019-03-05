// app/Barcode.js

// 导入条形码识别插件??
// import Quagga from '../plugins/quaggaJs/quagga.min.js';
// import {default:Quagga} from '../plugins/quaggaJs/quagga.js';
// export {default:Quagga} from '../plugins/quaggaJs/quagga.js';

//定义对象。封装条形码进行识别所需参数的预处理，识别过程。
export var Barcode={
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

// export {Barcode,Quagga};