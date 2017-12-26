
<!-- //ThinkPHP V5的渲染页面	 -->
return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';		
		
<!-- //简单渲染页面，IPMS V3	 -->
return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';


    //分页,每页$listrows条记录
            		$patstotal = $pats->where('status','neq','填报')
                                        ->order('submitdate', 'desc')
                                        ->paginate($listrows,false,['type'=>'bootstrap','var_page'=>'pagetotal',]);             
                    // 获取分页显示
                    $pagetotal = $patstotal->render(); 

<!-- TP5 使用模型只允许更新数据表字段数据 -->
// 获取当前用户对象
$user = User::get(request()->session('user_id'));
$user->allowField(true)
	->data(requst()->param(), true)
    ->save();
	
<!-- TP5 如果使用的是模型的静态方法（如create和update方法）进行数据写入的话，可以使用下面的方式进行字段过滤。 -->
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

<!--  HY 2017/12/22 -->
//通过模型获得多个数据：
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


<!--/  HY 2017/12/25 -->
				
				