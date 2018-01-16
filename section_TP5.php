
<!-- //ThinkPHP V5的渲染页面	 -->
<?php 
return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';		

<!-- //简单渲染页面，IPMS V3	 -->
return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';
?>

    //分页,每页$listrows条记录
<?php
	$patstotal = $pats->where('status','neq','填报')
                      ->order('submitdate', 'desc')
                      ->paginate($listrows,false,['type'=>'bootstrap','var_page'=>'pagetotal',]);             
                    // 获取分页显示
    $pagetotal = $patstotal->render(); 
?>
<!-- TP5 使用模型只允许更新数据表字段数据 -->
// 获取当前用户对象
<?php
$user = User::get(request()->session('user_id'));
$user->allowField(true)
	->data(requst()->param(), true)
    ->save();
?>	
<!-- TP5 如果使用的是模型的静态方法（如create和update方法）进行数据写入的话，可以使用下面的方式进行字段过滤。 -->
<?php 
User::create(request()->param(), ['nickname', 'address']);
User::update(request()->param(), ['id' => 1], ['nickname', 'address']);

<!-- TP5 同样可以传入true表示过滤非数据表字段 -->
User::create(request()->param(), true);
User::update(request()->param(), ['id' => 1], true);

switch(){
                    case '':
                    
                    break;
                    
                    case '':
                    
                    break;
                    
                    default:
                    
                    break;
                    
                    
                }

<{switch name="变量" }>
    <{case value="值1" break="0或1"}>输出内容1<{/case}>
    <{case value="值2"}>输出内容2<{/case}>
    <{default /}>默认情况
<{/switch}>

简洁的用法
<{switch $User.userId}>
    <{case $adminId}> admin<{/case}>
    <{case $memberId}> member<{/case}>
<{/switch}>


default:
                    
                    $pros= null;
                    $prosnum=0;
                    
                    $thes= null;
                    $thesnum=0;
                    
                    $pats = PatinfoModel::where(function ($query) use ($topic, $enddate) {$query->where('topic', 'like', '%'.$topic.'%')->where('submitdate', '<= time', $enddate);
                                                })
                                                ->where(function ($query) use ($writer, $dept) {
                                                    $query->where('author', 'like', '%'.$writer.'%')->where('dept', 'like', '%'.$dept.'%');
                                                })
                                                ->order('submitdate desc')
                                                ->select();
                                        
                    $patsnum=count($pats);
                        
                    //issue的查询语句
                    $isses= IssinfoModel::where(function ($query) use ($topic, $enddate) {
                                                    $query->where('topic', 'like', '%'.$topic.'%')->where('submitdate', '<= time', $enddate);
                                                })
                                                ->where(function ($query) use ($writer, $dept) {
                                                    $query->where('writer', 'like', '%'.$writer.'%')->where('dept', 'like', '%'.$dept.'%');
                                                })
                                                ->order('submitdate desc')
                                                ->select();
                    $issesnum=count($isses);                    
                
                break;
?>


<!--  HY 2017/12/22 -->
//通过模型获得多个数据：
<?php
// 使用闭包查询
$list = User::all(function($query){
  $query->where('status',1)->whereOr('id','>',10)->limit(3)->order('id', 'asc');
});


//或者在实例化模型后调用查询方法

$user = new User();
// 查询数据集
$user->where('name', 'thinkphp')
    ->whereOr('id','>',10)
	->limit(10)
    ->order('id', 'desc')
    ->select();

//在模型内部，请不要使用$this->name的方式来获取数据，请使用$this->getAttr('name') 替代。

//模型的all方法或者select方法返回的是一个包含模型对象的二维数组或者数据集对象。所以可以用‘$user->name’来得到user对象的name属性值。

//条件删除
//闭包删除
User::destroy(function($query){
    $query->where('id','>',10);
});

//通过数据库类的查询条件删除
User::where('id','>',10)->delete();

<!--/  HY 2017/12/22 -->				

<!--  HY 2017/12/25 -->
//控制器中定义删除目录及其文件的操作

//删除$dirName目录及其文件
// 应用php5里的dir,is_dir,unlink,rmdir
    private function _deleteDirs($dirName)
    {
        //循环删除目录和文件，成功后返回 "success"
        $d=dir($dirName);
        $result=0;
        while(false!==($child=$d->read())){
          // 清除目录里所有的文件
          if($child!="."&&$child!=".."){
            if(is_dir($dirName.DS.$child)){
              // 递归调用自己
              $this->_deleteDirs($dirName.DS.$child);
            }else{
              unlink($dirName.DS.$child);
              
            }
          }
        }
        $d->close();
        
        //清除目录
        rmdir($dirName);
        
        if(is_dir($dirName)){
          $result=$dirName;
        }else{
          $result="success";
        }
        return $result;
    }
?>

<!--/  HY 2017/12/25 -->
				
<!--  HY 2017/12/29 -->
<?php 
//问题,ipms系统中user模块的DashboardController.php， 操作‘role’(public function role())里
//return $this->fetch()与return view()，有区别？？
return $this->fetch('dashboard'.DS.'pat'.DS.$role);
return view('dashboard'.DS.'pat'.DS.$role);
//view()能正常显示模板文件内容，$this->fetch()显示的是转义字符填充后的模板文件内容。
?>    



<!--/  HY 2017/12/29 -->


<!--  HY 2018/1/16 -->
//定义模型获取器后前端取值方式
<script>				
	// jQuery语句select显示为后端传来的内容,后端patinfo模型中对pattpye字段有定义获取器，所以$pat.pattype的值不是数据库中的值，$pat->getData('pattype')才是。同理$iss->getData('issmap_type')
	$('#patType').val('{$pat->getData('pattype')}').attr('selected');
	$('#issType').val('{$iss->getData('issmap_type')}').attr('selected');	

	//显示经获取器后的值：
	//$('#patType').text('{$pat.pattype}').attr('selected');
	//$('#issType').text('{$iss.issmap_type}').attr('selected');	
	
</script>

// 前端以formdata形式上传表单数据
<script>				
<!-- function -->
	//**函数名：formDataAjax
 	//* 作用：应用jQuery的$.ajax向后端提交formData数据
	//* 参数1：oprt，类型：字符。值：不为空。说明：表示触发本函数的‘操作’
	//* 参数2：fmObj，类型：对象。值：可为空，默认为空。说明：表示需要一起提交的表单。
	//* 参数3：title，类型：字符。值：不为空。说明：本函数显示的提示框$.alert的标题内容。
	function formDataAjax(oprt,fmObj,title){
	// formdata形式上传表单数据,自动搜索表单信息(表单内没有name属性的input不会被搜索到)，IE<=9不支持FormData
		if(fmObj){
			var formData = new FormData(fmObj[0]);
		}else{
			var formData = new FormData();
		}
		
		<!-- var formData = new FormData($('#fmIssPat')[0]); -->
		// 添加表单内没有的内容
        formData.append('returnType', 0);
		formData.append('role','writer');
		formData.append('username','{$writer}');
		formData.append('dept','{$dept}');
		formData.append('patId','{$pat.id}');
		formData.append('issId','{$iss.id}');
		formData.append('oprt',oprt);
			
        $.ajax({
            type: 'post',
            url: 'patIssOprt',
            data: formData,
            contentType: false,// 当有文件要上传时，此项是必须的，否则后台无法识别文件流的起始位置
            processData: false,// 是否序列化data属性，默认true(注意：false时type必须是post)
			// 进度条显示上传进度
			xhr: xhrOnProgress(function(e){
                var percent=(e.loaded / e.total)*100;//计算百分比
                $('#divUpLoadProcess').css("width",percent + "%").text(percent + "%");
            }),
            success: function(data) {
                if(data.result=='success'){
					
					// 选中的单选按钮的value值
					var v=$('input:radio[name="attUpload"][value="1"]').prop('checked');
					// v==true,'上传'按钮
					if(v){
						// show出modal，以便看见文件上传进度条
						$('#modalPatIssTpl').modal('show');
						// 重新载入文件
						$("#modalPatIssTpl").on('shown.bs.modal',function(){
							$('#patIss').load('{$home}/user/dashboard/patIss',{'role':'writer','returnType':0,'issType':'_PATENT','issStatus':'_TODO','patIssId':'{$iss.id}'});
						});
					
						$("#modalPatIssTpl").modal("hide");
					}else{
						// 重新载入文件
						$('#patIss').load('{$home}/user/dashboard/patIss',{'role':'writer','returnType':0,'issType':'_PATENT','issStatus':'_TODO','patIssId':'{$iss.id}'});
					}
					// 操作成功，提示框显示信息
					$.alert(title+'成功<span class="glyphicon glyphicon-ok text-success"></span><hr>'+data.msg);
				}else{
					$('#modalPatIssTpl').modal();
					<!-- $.alert(data.msg); -->
				}
            }
        });
	}
	
<!-- function -->		
</script>				

//如何获取时间戳。
<?php  
echo "今天:".date("Y-m-d")."<br>";       
echo "昨天:".date("Y-m-d",strtotime("-1 day")), "<br>";       
echo "明天:".date("Y-m-d",strtotime("+1 day")). "<br>";    
echo "一周后:".date("Y-m-d",strtotime("+1 week")). "<br>";       
echo "一周零两天四小时两秒后:".date("Y-m-d G:H:s",strtotime("+1 week 2 days 4 hours 2 seconds")). "<br>";       
echo "下个星期四:".date("Y-m-d",strtotime("next Thursday")). "<br>";       
echo "上个周一:".date("Y-m-d",strtotime("last Monday"))."<br>";       
echo "一个月前:".date("Y-m-d",strtotime("last month"))."<br>";       
echo "一个月后:".date("Y-m-d",strtotime("+1 month"))."<br>";       
echo "十年后:".date("Y-m-d",strtotime("+10 year"))."<br>"; 

//得到此时此刻的Unix timestamp
strtotime("now");
//得到"10 September 2000"的Unix timestamp
strtotime("10 September 2000");
//得到"+1 week 2 days 4 hours 2 seconds"的Unix timestamp
strtotime("+1 week 2 days 4 hours 2 seconds");

//得到明天的Unix timestamp
$tomorrow = mktime(0,0,0,date("m"),date("d")+1,date("Y"));
//得到明天此时此刻的Unix timestamp
$tomorrowNow = mktime(dat("H"),dat("i"),dat("s"),date("m"),date("d")+1,date("Y"));
//以yyyy-mm-dd形式显示明天的日期
$date("Y-m-d",$tomorrow);
//以yyyy-mm-dd hh:mm:ss形式显示此时此刻的日期、时间
$date("Y-m-d H:i:s");		     
?>


<!--/  HY 2018/1/16 -->



