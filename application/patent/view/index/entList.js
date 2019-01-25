// app/entList.js

initUrlObj(rqData.ent);
initEntList();

//本文件有效的变量
let tbl=$('[id="'+rqData.ent+'ListTbl"]'),
	listRow=$('#listRows'),
	aHeadSet=tbl.find('thead a'),
	aBodySet=tbl.find('body a'),
	aPageSet=$('#divListRows a');
	
//表格每页显示记录行数；表格按选定行数显示
listRow.val(rqData.sortData.listRows).change(function(){
	let listRows=$(this).val()*1;
	//排序有关的值向sortData汇集
	rqData.sortData.listRows=$(this).val();
	//分页从第一页开始
	rqData.sortData.pageNum=1;
	//载入entList
	loadEntPeriodList();
});	
//表格按选定字段排序
aHeadSet.click(function(){
	let sortName=$(this).data('sortName'),
		sortOrder=$(this).data('sortOrder');
	//排序有关的值向sortData汇集
	rqData.sortData.sortName=sortName;
	rqData.sortData.sortOrder=sortOrder;
	rqData.sortData.pageNum=1;
	//载入entList
	loadEntPeriodList();
});	
//表格中点击a后，标签所在行上底色
aBodySet.click(function(){
	rqData.sortData.showId=$(this).closest('tr').data('showId');
	setTrBgColor();
});
//表格显示分页内容
aPageSet.click(function(evt){
	// a所在分页页数
	let pageStr=$(this).text(),pageNum,
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
	//载入entList
	loadEntPeriodList();
});
