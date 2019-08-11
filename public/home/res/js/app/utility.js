//app/utility.js

import {Event as eve} from './Event.js';
import {App} from './main.js';
import {Modal} from './Modal.js';

export {asyInitData,consoleColor,getRqUrl,initRqData,	buildTopNavbar,buildEntCharts,setRqData,setRqQueryFieldBy,setRqSearchDataBy};

//定义async函数asyInitData()进行全局变量赋值，默认返回的是一个promise对象。
async function asyInitData() {
	let d=App.data;	//$.post()方法返回的是jqXHR对象，这个jqXHR对象是对所发起的request的响应结果，无需解析
	// let resData = await $.post('/index/index/getInitData');
	//fetch()方法返回的是一个Promise对象，这个promise对象resolve成一个response对象，这个response对象是对所发起的request的响应结果。
	let opt={
			method:'POST',
			headers:{
				'Content-Type':'application/json'
			},
			//带上cookie
			credentials:'include'
		};
	let resObj = await fetch('/index/index/getInitData',opt);
	// 完成response对象内容解析
	let resData =await resObj.json();
	// let resData =await resObj.blob();
	
	// console.log(resObj);
	// console.log(resData);
	
	//全局变量赋值
	d.userName=resData.userName;
	d.urlObj=resData.urlObj;
	d.entNum=resData.entNum;
	d.rqData=initRqData();
	//完善entProp中的num属性值
	for(let ent in d.entProp){
		for(let per in d.entProp[ent].period.detail){
			d.entProp[ent].period.detail[per].title.num=d.entNum[ent][per];
		}
	}
	
	return d;
}

//
function setRqData() {	
	let rData=App.data.rqData;
	let fm=$('form.fmQuery'),
		entHArr=$('#entPeriod').find('.nav-pills li.active a').data(),
		sortName=$('#entList').find('[data-sort-name]').eq(0).data('sortName'),
		showId=$('#entList').find('[data-show-id]').eq(0).data('showId');
		
	rData.ent=entHArr.ent;
	rData.period=entHArr.period;
	
	if(rData.sortData.sortName==''){
		rData.sortData.sortName=sortName;	
	}
	
	fm.find('[name]').each(function(){
		let name=$(this).attr('name'),
			//null转为0
			v=$(this).val()==null?0:$(this).val(),
			lName=$(this)[0].localName;
			
		//赋值rqData.searchData，非0非空的值才进行组装
		if(v!=0){
			rData.searchData[name]=v;
		}
		
		//赋值rqData.queryField，表单输入项及其特征值
		rData.queryField[name]={val:v,tagName:lName};
		//添加select的option特征值
		if(lName=='select'){
			let len=$(this).find('option').length,
				opt={num:len,val:[''],txt:['']};
			/* //将option的value,text都带上
			if(len){
				$(this).find('option').each(function(index,el){
					opt.val[index]=$(el).val();
					opt.txt[index]=$(el).text();
				});
			} */
			rData.queryField[name]['option']=opt;
		}
	});
	
	return rData;
}


//让rqData回到初始值
function initRqData(){	
	App.data.rqData={
		ent:'index',
		period:'',
		sheet:{mode:'none',idArr:[],type:'',head:{}},
		sortData:{listRows:10,sortName:'',sortOrder:'asc',pageNum:1,showId:''},
		searchSource:'',
		searchData:{},
		queryField:{}
	};	
	return App.data.rqData;
}
//设定向后端请求的url
function getRqUrl(opt=''){
	let kArr=['domain','module','ctrl','action'],
	// let kArr=['module','ctrl','action'],
		arr=[],
		url='',
		urlObj=App.data.urlObj;
	urlObj=$.extend({},{module:'index',ctrl:'index',action:'index'},opt);
	
	kArr.forEach(function(e,i){
		arr[i]=(urlObj[e]);
	});
	App.data.urlObj=urlObj;
	return arr.join('/');
	// return url;
}

//根据定义的topNavProp生成"navLi"组件
function buildTopNavbar(){ 
	let d=App.data;
	let entity=d.rqData.ent;
	let r=$('nav .navbar-collapse ul').eq(0);
	r.empty();
	for(let ent in d.topNavProp){
		let e=d.topNavProp[ent],
			n=d.entNum[ent],
			a=$('<a></a>').attr('data-ent',ent).append($('<span></span>').addClass(e.gly),'&nbsp;',e.chi),
			rC=$('<li></li>');
			
		//有数据的ent才生成"navLi"组件
		if(ent=='index' || typeof n=='object'){
			a.css('cursor','pointer');
			rC.append(a);
			r.append(rC);
		}	
		
		//无数据的的ent也生成"navLi"组件
		// if(ent!='index' && n==0){
			// rC.addClass('disabled');
		// }	
		// rC.append(a);
		// r.append(rC);
	}
	//为li标签添加.active
	r.find('[data-ent="'+entity+'"]').tab('show');
}
//根据定义的entProp生成"canvas"组件
function buildEntCharts() {
	let d=App.data;
	let //计数器
		n=0,
		divSet=[];
		
	$('#entChart').empty();
	
	//挨个生成ent组件
	for (let ent in d.entNum){
		let numObj=(typeof d.entNum[ent]=='object')?d.entNum[ent]:0,
			entObj=d.entProp[ent],
			perObj=entObj.period.summary,
			p=$('<p/>',{'class':'text-center'}).text(entObj.noneTxt),
			// aNod=$('<a></a>').attr({'data-toggle':'collapse','href':`#col-${ent}`,'title':'隐藏/显示概况'}).append($('<span></span>').addClass(entObj.gly),'&nbsp;',$('<strong></strong>').html(entObj.chi)),
			aNod=$('<a/>',{'data-toggle':'collapse','href':`#col-${ent}`,'title':'隐藏/显示概况'}).append($('<span/>',{'class':entObj.gly}),'&nbsp;',$('<strong/>').html(entObj.chi)),
			panH=$('<div/>',{'class':'panel-heading'}).append(aNod),
			panB=$('<div/>',{'class':'panel-body text-center'}),
			panCol=$('<div/>',{'id':`col-${ent}`,'class':'panel-collapse collapse'}),
			panType=$('<div/>',{'class':'panel panel-default'}),
			div=$('<div/>',{'class':'col-sm-6'});
		let idStr=`cvs-${ent}`;
		
		//显示pat项或ass项
		if(ent=='pat' || ent=='ass'){
			panCol.addClass('in');
		}
		//有数据的ent才生成组件
		if(typeof numObj=='object' && Object.values(numObj).length){
			let cvsNod=$('<canvas/>',{
							'id':idStr,
							'height':'200'
						}).text('canvas coming');
			let chartData={
					data:[],
					backgroundColor:[],
					label:'xxx'
				},
				chartType=(ent=='ass')?'doughnut':'pie',
				chartObj='';
			let perName=[],perNum=[],labArr=[];
			let other={
				num:Object.values(numObj)[0],
				rgb:'#f5f5f5',
				txt:'其他'
			};
			let btnSet=[];
			//计数器
			let m=0;
			
			//生成btnSet
			for(let per in perObj){
				let num=numObj[per],
					el=perObj[per];
				let	btn=$('<button/>',{
							'class':'btn btn-xs btnPeriod',
							'title':'查看详情',
							'style':'margin:2px;font-size:14px;'
						}),
					spBdg=$('<span/>',{'class':'badge'});
				
				if(num){
					spBdg.text(num);	
					btn.attr({'data-ent':ent,'data-period':per}).addClass(el.color).append(el.txt+'&nbsp;',spBdg);
					// btn.attr({'data-ent':ent,'data-period':per}).addClass(el.color).append(el.txt);
				
					if(el.color=='btn-default'){
						btn.css('backgroundColor','#ccc');
					}
					
					btnSet[m]=btn;
					
					//chart中的数组赋值
					chartData.data[m]=num;
					chartData.backgroundColor[m]=el.rgb;
					// perName[m]=el.txt+':'+num;
					perName[m]=el.txt;
					labArr[m]=el.txt+':'+num;
					//计算其他项数量
					other.num-=num;
					
					m++;
				}
			}
			// chart中添加'其他'项
			if(other.num){				
				chartData.data.push(other.num);
				chartData.backgroundColor.push(other.rgb);
				perName.push(other.txt);
				labArr.push(other.txt+':'+other.num);
			}
			
			//生成chart
			chartObj=new Chart(cvsNod,{
				// type:'doughnut',
				type:chartType,
				data:{
					labels:perName,
					datasets:[chartData],
					
				},
				options:{
					responsive:true,
					legend:{
						display:true,
						position:'right',
						labels: {
                			// fontColor: 'rgb(255, 99, 132)',
                			fontSize: 14,
							// generateLabels:function(){
								// return {
									// text:labArr,
								// };
							// }
            			}
					},
					title:{
						display:true,
						text:'数量分布概况',
						fontSize: 14,
					},
					animation:{
						animateScale:true,
						animateRotate:true
					}
				}
			});
			//组装panB
			panB.append(cvsNod,'<br />',btnSet);
			panCol.append(panB);
			
			panType.append(panH,panCol);
		
			//生成一个ent组件
			divSet[n]=div.append(panType);
			n++;
			
			console.log(perName);
			console.log(labArr);
		}
		
		/*
		//无数据的ent也生成组件
		 if(numObj==0){
			panCol.append(p)
		}
		panType.append(panH,panCol);
		
		divSet[n]=div.append(panType);
		n++; 
		*/
		
	}
	
	//每个row放置2个ent组件
	for(let i=0;i<Math.ceil(n/2);i++){		
		let r=$('<div></div>').addClass('row');
		r.append(divSet[2*i],divSet[(2*i+1)]);
		$('#entChart').append(r);
	}
	
	
}

//组装向后端请求时的searchData，不带参数就是清空searchData
function setRqSearchDataBy(fm=''){
	let rData=App.data.rqData;
	let formData='';
	rData.searchData={};
	
	if(typeof fm !='object' || fm[0].localName !=='form'){
		return rData.searchData;
	}
	rData.searchSource=fm.data('formType');
	
	//用户搜索有关键值对从表单searchObj获取
	formData=new FormData(fm[0]);
	//使用Map对象的forEach方法组装hash数组
	formData.forEach(function(val,key){
		//非0非空的值才进行组装
		if(val!='' && val!=0){
			rData.searchData[key]=val;
		}
	});		
	return rData.searchData;
}
//组装向后端请求时的queryField，不带参数就是清空queryField
function setRqQueryFieldBy(fm=''){
	let rData=App.data.rqData;
	rData.queryField={};
	
	if(typeof fm !='object' || fm[0].localName !=='form'){
		return rData.queryField;
	}
	
	fm.find('[name]').each(function(){
		let n=$(this).attr('name'),
			v=$(this).val()==null?0:$(this).val(),
			lName=$(this)[0].localName;
		//表单输入项及其特征值
		rData.queryField[n]={val:v,tagName:lName};
		//添加select的option特征值
		if(lName=='select'){
			let len=$(this).find('option').length,
				opt={num:len,val:[''],txt:['']};
			/* //将option的value,text都带上
			if(len){
				$(this).find('option').each(function(index,el){
					opt.val[index]=$(el).val();
					opt.txt[index]=$(el).text();
				});
			} */
			rData.queryField[n]['option']=opt;
		}
	});
	
	return rData.queryField;
}

//给console.log的内容加上颜色
function consoleColor(str='无内容',color='blue'){
	let arr={red:'red',blue:'blue',yellow:'#f0ad4e',green:'#5cb85c'};
	
	color=Object.keys(arr).includes(color)?arr[color]:'red';
	//字符串中插入变量color，字符串的首尾要以'`'来代替引号，变量名要放入${}的花括号中。
	return console.log('%c%s',`font-size:16px;color:${color};`,str);
}

	