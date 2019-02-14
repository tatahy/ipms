// app/conf.js
//向后端请求时发送的数据
var	rqData={
		ent:'index',
		period:'',
		sortData:{listRows:10,sortName:'',sortOrder:'asc',pageNum:1},
		searchData:{}
	};
var urlObj={domain:'',module:'index',method:'index',ctrl:'index'};
var searchResultNum='';
//定义前端系统各个实体数量对象,由后端给定??
var entNum='';

var year=new Date().getFullYear();

const glyPrex='glyphicon glyphicon-';

//归纳bootStrap3中的颜色相关属性
const bs3Color={
		info:{lab:'label label-info',btn:'btn-info',txt:'text-info',bg:'bg-info',alt:'alert-info',pan:'panel panel-info'},
		success:{lab:'label label-success',btn:'btn-success',txt:'text-success',bg:'bg-success',alt:'alert-success',pan:'panel panel-success'},
		danger:{lab:'label label-danger',btn:'btn-danger',txt:'text-danger',bg:'bg-danger',alt:'alert-danger',pan:'panel panel-danger'},
		warning:{lab:'label label-warning',btn:'btn-warning',txt:'text-warning',bg:'bg-warning',alt:'alert-warning',pan:'panel panel-warning'},
		primary:{lab:'label label-primary',btn:'btn-primary',txt:'text-primary',bg:'bg-primary',pan:'panel panel-primary'},
		//default
		'default':{lab:'label label-default',btn:'btn-default'},
		muted:{text:'text-muted'},
		link:{btn:'btn-link'}
};
	//topNav中li组件主要属性
const topNavProp={
		index:{'url':domain+'/index/index/index',period:'',gly:glyPrex+'home',chi:'首页'},
		pat:{'url':domain+'/patent/index/index',period:'total',gly:glyPrex+'wrench',chi:'专利'},
		ass:{'url':domain+'/asset/index/index',period:'usual',gly:glyPrex+'oil',chi:'固定资产'},
		pro:{'url':domain+'/project/index/index',period:'total',gly:glyPrex+'inbox',chi:'项目'},
		the:{'url':domain+'/thesis/index/index',period:'total',gly:glyPrex+'list',chi:'论文'}
};

const noneSufx='模块无【查阅】权限，请与系统管理员联系。';

var loadStr='<p class="text-center" style="font-size:20px;padding-top:20px;">加载中……</p>';

//定义前端各个实体主要属性(用于jQ构建组件，路由生成等)
var entProp={
	pat:{
		module:'patent',
		ctrl:['index'],
		method:['index','patList','patSearchForm','getSelComData'],
		chi:'专利',
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
		ctrl:['index'],
		method:['index','assList','assSearchForm'],
		chi:'固定资产',
		gly:glyPrex+'oil',
		period:{
			summary:{
				undetermined:{txt:'待定数：',color:bs3Color['info']['btn']},
				normal:{txt:'正常数：',color:bs3Color['success']['btn']},
				abnormal:{txt:'异常数：',color:bs3Color['warning']['btn']},
				suspended:{txt:'停用数：',color:bs3Color['default']['btn']}
			},
			detail:{
				usual:{txt:'固定资产概况',title:{txt:'固定资产总数&nbsp;',btn:'btn btn-primary',num:''}},
				undetermined:{txt:'待定',title:{txt:'状态待定数&nbsp;',btn:'btn btn-info',num:''}},
				normal:{txt:'正常',title:{txt:'状态正常数&nbsp;',btn:'btn btn-success',num:''}},
				abnormal:{txt:'异常',title:{txt:'状态异常数&nbsp;',btn:'btn btn-warning',num:''}},
				suspended:{txt:'停用',title:{txt:'停止使用数&nbsp;',btn:'btn btn-default',num:''}},
				removed:{txt:'销账',title:{txt:'财务销账数&nbsp;',btn:'btn btn-danger',num:''}}
			}
		},
		noneTxt:'“固定资产”'+noneSufx
	},
	pro:{
		module:'project',
		ctrl:['index','proList','proSearchForm'],
		method:['index'],
		chi:'项目概况&nbsp;<span class="bg-primary">&nbsp;开发中&nbsp;</span>',
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
		ctrl:['index','theList','theSearchForm'],
		method:['index'],
		chi:'论文概况&nbsp;<span class="bg-primary">&nbsp;开发中&nbsp;</span>',
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
}

// export {glyPrex,entNum,domain,loadStr,bs3Color,topNavProp,entProp,rqData,urlObj,searchResultNum,year};