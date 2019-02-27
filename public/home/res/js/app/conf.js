// app/conf.js
//采用默认导出

export default {
	userName:'',
	//向后端请求时发送的数据
	rqData:{
		ent:'index',
		period:'',
		sortData:{listRows:10,sortName:'',sortOrder:'asc',pageNum:1,showId:''},
		searchSource:'',
		searchData:{},
		queryField:{}
	},
	urlObj:{domain:'',module:'index',ctrl:'index',action:'index'},
	searchResultNum:'',
//定义前端系统各个实体数量对象,由后端给定
	entNum:'',
	year:new Date().getFullYear(),
	glyPrex:'glyphicon glyphicon-',
//归纳bootStrap3中的颜色相关属性
	bs3Color:{
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
	topNavProp:{
	index:{period:'',gly:'glyphicon glyphicon-home',chi:'首页'},
	pat:{period:'total',gly:'glyphicon glyphicon-wrench',chi:'专利'},
	ass:{period:'usual',gly:'glyphicon glyphicon-oil',chi:'固定资产'},
	pro:{period:'total',gly:'glyphicon glyphicon-inbox',chi:'项目'},
	the:{period:'total',gly:'glyphicon glyphicon-list',chi:'论文'}
	},
	noneSufx:'模块无【查阅】权限，请与系统管理员联系。',
	loadStr:'<p class="text-center" style="font-size:20px;padding-top:20px;">加载中……</p>',
//定义前端各个实体主要属性(用于jQ构建组件，路由生成等)
	entProp:{
	pat:{
		module:'patent',
		ctrl:['index'],
		action:['index','patList','patSearchForm','getSelComData'],
		chi:'专利',
		gly:'glyphicon glyphicon-wrench',
		period:{
			summary:{
				newAdd:{txt:'拟申报专利数：',color:'btn-info'},
				apply:{txt:'申报专利数：',color:'btn-primary'},
				authorize:{txt:'有效专利数：',color:'btn-success'}
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
		noneTxt:'"专利"模块无【查阅】权限，请与系统管理员联系。'
	},
	ass:{
		module:'asset',
		ctrl:['index'],
		action:['index','assList','assSearchForm'],
		chi:'固定资产',
		gly:'glyphicon glyphicon-oil',
		period:{
			summary:{
				undetermined:{txt:'待定数：',color:'btn-info'},
				normal:{txt:'正常数：',color:'btn-success'},
				abnormal:{txt:'异常数：',color:'btn-warning'},
				suspended:{txt:'停用数：',color:'btn-default'}
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
		noneTxt:'"固定资产"模块无【查阅】权限，请与系统管理员联系。'
	},
	pro:{
		module:'project',
		ctrl:['index','proList','proSearchForm'],
		action:['index'],
		chi:'项目&nbsp;<span class="bg-primary">&nbsp;开发中&nbsp;</span>',
		gly:'glyphicon glyphicon-inbox',
		period:{
			summary:{
				plan:{txt:'拟申报项目数：',color:'btn-info'},
				apply:{txt:'申报项目数',color:'btn-primary'},
				approve:{txt:'立项项目数：',color:'btn-warning'},
				process:{txt:'在研项目数：',color:'btn-danger'},
				done:{txt:'结题项目数：',color:'btn-success'},
				terminate:{txt:'终止项目数：',color:'btn-default'}
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
		noneTxt:'"项目"模块无【查阅】权限，请与系统管理员联系。'
	},
	the:{
		module:'thesis',
		ctrl:['index','theList','theSearchForm'],
		action:['index'],
		chi:'论文&nbsp;<span class="bg-primary">&nbsp;开发中&nbsp;</span>',
		gly:'glyphicon glyphicon-list',
		period:{
			summary:{
				plan:{txt:'拟发表论文数：',color:'btn-info'},
				apply:{txt:'投稿论文数：',color:'btn-primary'},
				publish:{txt:'发表论文数：',color:'btn-success'}
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
		noneTxt:'"论文"模块无【查阅】权限，请与系统管理员联系。'
	}
	}
}

// export {glyPrex,entNum,domain,loadStr,bs3Color,topNavProp,entProp,rqData,urlObj,searchResultNum,year};