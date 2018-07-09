
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

// TP5 同样可以传入true表示过滤非数据表字段 

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
?>

<html>
{switch name="变量" }
    {case value="值1" break="0或1"}输出内容1{/case}
    {case value="值2"}输出内容2{/case}
    {default /}默认情况
{/switch}

{switch name="User.level"}
    {case value="1"}value1{/case}
    {case value="2"}value2{/case}
    {default /}default
{/switch}

{switch name="Think.get.type"}
    {case value="gif|png|jpg"}图像格式{/case}
    {default /}其他格式
{/switch}

简洁的用法
{switch $User.userId}
    {case $adminId} admin{/case}
    {case $memberId} member{/case}
{/switch}
</html>

<?php
default:
                    
    $pros= null;
    $prosnum=0;
                    
    $thes= null;
    $thesnum=0;
                    
    $pats = PatinfoModel::where(function ($query) use ($topic, $enddate) {$query->where('topic', 'like', '%'.$topic.'%')->where('submitdate', '<= time', $enddate);})
            ->where(function ($query) use ($writer, $dept) {$query->where('author', 'like', '%'.$writer.'%')->where('dept', 'like', '%'.$dept.'%');})
            ->order('submitdate desc')
            ->select();
                                        
    $patsnum=count($pats);
                        
    //issue的查询语句
    $isses= IssinfoModel::where(function ($query) use ($topic, $enddate) {$query->where('topic', 'like', '%'.$topic.'%')->where('submitdate', '<= time', $enddate);})
            ->where(function ($query) use ($writer, $dept) {$query->where('writer', 'like', '%'.$writer.'%')->where('dept', 'like', '%'.$dept.'%');})
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
//闭包查询仅有一个参数$query,use引入闭包外部变量
$usersNum=count(User::all(function($query) use ($searchUsergroup,$searchDept,$searchUserName){
	$query->where('id',3)
    	->where([
                'usergroup_id'=>['like','%'.$searchUsergroup.'%'],
                'dept'=>$searchDept,
                'username'=>['like','%'.$searchUserName.'%']
            ])
        ->group('username');
    })
); 

//或者在实例化模型后调用查询方法

$user = new User();
// 查询数据集
$user->where('name', 'thinkphp')
    ->whereOr('id','>',10)
	->limit(10)
    ->order('id', 'desc')
    ->select();

//在模型内部，请不要使用$this->name的方式来获取数据(因为得到的是模型名称)，请使用$this->getAttr('name') 替代。

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
            processData: false,// 是否序列化data属性，默认true(注意：false时type必须是post)。当使用FormData()，必须设为false，否则jqurey默认将FormData()转换为查询字符串。
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

<!--/  HY 2018/1/16 -->

<!--/  HY 2018/1/21 -->
模型调用
模型支持实例化调用和静态调用（主要是查询，查询后会返回一个模型对象实例）。
<?php 
// 实例化User模型
$user = new \app\index\model\User();
// 直接静态查询
$user = \app\index\model\User::get(1);

?>

一般来说，我们会事先使用use引入User模型类，就不需要每次都使用完整命名空间方式来调用User模型类了。
<?php

namespace app\index\controller;

use app\index\model\User;

class Index
{
	public function index()
    {
    	$user = User::get(1);
    }
}
?>

//模型CURD
用法		模型（动态，模型实例化后）		模型（静态）
创建		save（返回值：影响的记录数）	create（返回值：模型对象实例）
更新		save（返回值：影响的记录数）	update（返回值：模型对象实例）
读取单个	find（返回值：模型对象实例）	get（返回值：模型对象实例）
读取多个	select（返回值：包含模型对象实例的数组或者数据集）				all（返回值：包含模型对象实例的数组或者数据集）
删除		delete（返回值：影响的记录数）	destroy（返回值：影响的记录数）

//静态CURD
模型类的静态CURD操作其实都是内部自动实例化而已，所以说白了提供的这些静态操作方法只是对动态CURD操作方法的静态封装罢了。查询时，模型方式返回的数据集包含符合查询条件的模型对象实例的数组。

至于静态方法的场景，主要是不想实例化或者不方便实例化的需求，而且支持变量的静态调用。
<?php
//create方法,创建。返回值是创建的User模型的对象实例
//update方法,更新。返回值是更新的User模型的对象实例
//destroy方法,删除。返回值是影响的记录数。
//get方法,读取单个。返回值单个符合查询条件的User模型的对象实例
//all方法,读取多个。返回值多个符合查询条件的User模型的对象实例

create方法
//静态调用
$user = User::create([
    'name'  => 'thinkphp',
    'email' => 'thinkphp@qq.com',
]);
echo $user->id;
//或者：
$model = '\app\index\model\User';
$user  = $model::create([
    'name'  => 'thinkphp',
    'email' => 'thinkphp@qq.com',
]);
echo $user->id;
//模型类
$user        = new User;
$user->name  = 'thinkphp';
$user->email = 'thinkphp@qq.com';
$user->save();
// 获取用户的主键数据
echo $user->id;

//create方法的返回值是User模型的对象实例，所以可以通过$user->id得到create后的id值。
//save方法的返回值不是自增主键的值（和Db的execute方法一样返回影响的记录数），要获取自增主键的值可以使用下面的方式：


update方法
//静态调用
User::update([
    'name'  => 'topthink',
    'email' => 'topthink@qq.com',
], ['id' => 1]);
//模型类
$user        = User::get(1);
$user->name  = 'topthink';
$user->email = 'topthink@qq.com';
$user->save();


all方法，destroy方法。
//1.根据主键值查询/删除
//静态调用
User::all([1, 2, 3]);
User::destroy([1, 2, 3]);
//模型类
$user = User::get(1);
$user->delete();

//2.闭包查询/删除。闭包方法中可以使用任何的查询类方法（但不需要在闭包里面调用查询）。闭包只有一个参数，就是查询对象。
//静态调用
$users = User::all(function ($query) {
    $query->where('name', 'like', '%think')
        ->where('id', 'between', [1, 5])
        ->order('id desc')
        ->limit(5);
});

User::destroy(function ($query) {
    $query->where('id', '>', 0)
        ->where('status', 0);
});

//3.传入数组查询/删除条件
//静态调用
$users = User::all([
    'name' => 'thinkphp',
    'id'   => ['>', 1],
]);

User::destroy([
    'status' => 0,
]);

?>

以name属性为例，获取模型数据的方式有下列三种：
场景								方法
外部获取模型数据					$model->name
内部获取模型数据					$this->getAttr('name')
内部获取（原始）模型数据			$this->getData('name')
getData和getAttr方法的区别前者是原始数据，后者是经过读取器处理的数据，如果没有定义数据读取器的话，两个方法的结果是相同的。

对应的设置模型数据的方式也有三种：
场景								方法
外部设置模型数据					$model->name='thinkphp'
内部设置模型数据（经过修改器）		$this->setAttr('name','thinkphp')
内部设置模型数据					$this->data('name','thinkphp')

data和setAttr方法的区别前者是赋值最终数据，后者赋值的数据还会经过修改器处理，如果没有定义修改器的话，两个方法的结果是相同的。

//进行条件查询时，
1.静态调用
<?php 
// 查询单个记录
User::where('name', 'thinkphp')->find();
// 调用动态查询方法
User::getByName('thinkphp');
// 查询数据集
User::where('id', '>', 0)->limit(10)->order('id desc')->select();
// 删除数据
User::where('status', 0)->delete();
?>

2.动态调用
如果查询代码在模型内部，可以直接支持动态方式调用查询构造器的方法，用法就是从静态调用改为动态调用。
<?php 
// 查询单个记录
$this->where('name', 'thinkphp')->find();
// 调用动态查询方法
$this->getByName('thinkphp');
// 查询数据集
$this->where('id', '>', 0)->limit(10)->order('id desc')->select();
// 删除数据
$this->where('status', 0)->delete();
?>
不过类似的查询场景应当极力避免（个别场景例如查询范围等可能会涉及到此类用法），查询操作应当是静态调用，更新和删除操作则是动态方法调用。如果是在模型方法中查询其它模型的数据，第一种静态调用方式仍然适用。尤其不建议使用table方法在同一个模型实例中切换数据表查询，在模型中动态设置table属性的方式更加不可取（经常发现这种奇葩的用法），模型和数据表以及相应的业务逻辑是应当在创建的时候就相对固定的，应当极力避免在一个模型对象实例中查询操作多次不同数据。

//模型查询的原则应当是每个模型对象实例操作一个唯一记录，对于数据集来说这个原则也不变，只是每个数据集对象实例则包含多个模型对象实例而已。

//get、all以及destroy方法的参数用法记住一个原则，如果是数字、字符串或者普通数组都表示一个或者多个主键，如果是索引数组则表示查询条件，闭包则支持查询条件以外的其它链式操作方法。对于get方法的参数最好做一次非null检查，否则查询的就会是第一个数据（V5.0.8+已经改进，不需要检查是否为null了）。

字段过滤
经常我们会直接使用表单提交的数据来作为模型数据写入，但并不是所有的数据都是数据表字段（直接写入会导致数据库异常），或者不希望某些数据被用户通过表单提交的方式更新（为了安全或者逻辑考虑），Request类自身提供了only方法来获取部分想要的数据，例如：
<?php 
// 只获取请求变量中的nickname和address变量
$data = request()->only(['nickname', 'address']);
// 获取当前用户对象
$user = User::get(request()->session('user_id'));
// 更新用户数据
$user->data($data, true)->save();
?>
模型类提供了allowField方法用于在数据写入操作的时候设置字段过滤，从而避免数据库因为字段不存在而报错，上面的写法可以简化为。
<?php 
// 获取当前用户对象
$user = User::get(request()->session('user_id'));
// 只允许更新用户的nickname和address数据
$user->allowField(['nickname', 'address'])
    ->data(requst()->param(), true)
    ->save();

如果仅仅是希望去除数据表之外的字段，可以使用


// 只允许更新数据表字段数据
$user->allowField(true)
	->data(requst()->param(), true)
    ->save();
	
//data方法属于链式操作方法，用于设置数据

?>

为了不必每次都调用allowField方法，我们可以直接在模型类里面设置field属性，例如：

<?php

namespace app\index\model;

use think\Model;

class User extends Model
{
	protected $field = ['name', 'nickname', 'email', 'address'];
}
?>

当调用allowField方法的时候，当前模型实例中的该配置的值会被覆盖。

这一方式实现是默认表单提交数据的name与模型属性名称（数据表字段名称）一模一样

模型类提供了allowField方法用于在数据写入操作的时候设置字段过滤，从而避免数据库因为字段不存在而报错，上面的写法可以简化为。
<?php
// 获取当前用户对象
$user = User::get(request()->session('user_id'));
// 只允许更新用户的nickname和address数据
$user->allowField(['nickname', 'address'])
    ->data(requst()->param(), true)
    ->save();
	
//如果使用的是模型的静态方法（如create和update方法）进行数据写入的话，可以使用下面的方式进行字段过滤。

User::create(request()->param(), ['nickname', 'address']);
User::update(request()->param(), ['id' => 1], ['nickname', 'address']);

//同样可以传入true表示过滤非数据表字段

User::create(request()->param(), true);
User::update(request()->param(), ['id' => 1], true);
//save方法返回影响的记录数，而update方法返回的则是模型的对象实例。
?>

//模型的查询返回数据都是模型的对象实例，


//模型中添加业务逻辑
添加业务逻辑，模型的优势不是用来做基础的CURD操作的，实际的应用中，一般都需要根据业务需求来增加额外的业务逻辑方法。
以User模型为例，假设我们需要实现下列功能：

    ^用户注册；
    ^用户登陆；
    ^获取用户信息；
    ^获取用户的身份角色；
    ^...更多业务逻辑

那么可以在User模型添加下面的逻辑方法：
<?php
namespace app\index\model;

use think\Model;

class User extends Model
{

    /**
     * 注册一个新用户
     * @param  array $data 用户注册信息
     * @return integer|bool  注册成功返回主键，注册失败-返回false
     */
    public function register($data = [])
    {
        $result = $this->validate(true)->allowField(true)->save($data);
        if ($result) {
            return $this->getData('id');
        } else {
            return false;
        }
    }

    /**
     * 用户登录认证
     * @param  string  $username 用户名
     * @param  string  $password 用户密码
     * @return integer 登录成功-用户ID，登录失败-返回0或-1
     */
    public function login($username, $password)
    {
        $where['username'] = $username;
        $where['status']   = 1;
        /* 获取用户数据 */
        $user = $this->where($where)->find();
        if ($user) {
            if (md5($password) != $user->password) {
                $this->error = '密码错误';
                return 0;
            } else {
                return $user->id;
            }
        } else {
            $this->error = '用户不存在或被禁用';
            return -1;
        }
    }

    /**
     * 获取用户信息
     * @param  integer  $uid 用户主键
     * @return array|integer 成功返回数组，失败-返回-1
     */
    public function info($uid)
    {
        $user = $this->where('id', $uid)->field('id,username,email,mobile,status')->find();
        if ($user && 1 == $user->status) {
            // 返回用户数据
            return $user->hidden('status')->toArray();
        } else {
            $this->error = '用户不存在或被禁用';
            return -1;
        }
    }

    /**
     * 获取用户角色
     * @return integer 返回角色信息或者返回-1
     */
    public function role()
    {
        $uid = $this->getData('id');
        if ($uid) {
            $role = $this->getUserRole($uid);
            if ($role) {
                return $role;
            } else {
                $this->error = '用户未授权';
                return 0;
            }
        } else {
            $this->error = '请先登录';
            return -1;
        }
    }

    protected function getUserRole($uid)
    {
        return $this->table('role')->where('uid', $uid)->find();
    }
}
?>

同时给出在控制器中的调用参考。
<?php
namespace app\index\controller;

use app\index\model\User;
use think\Controller;
use think\Session;

class Index extends Controller
{
    public function login()
    {
        return $this->fetch();
    }

    public function doLogin(User $user, $username, $password)
    {
        $uid = $user->login($username, $password);
        if ($uid) {
            Session::set('user_id', $uid);
            $this->success('登录成功');
        } else {
            $this->error('登录失败');
        }
    }

    public function register()
    {
        return $this->fetch();
    }

    public function doRegister(User $user)
    {
        $data   = $this->request->param();
        $result = $user->register($data);
        if ($result) {
            $this->success('用户注册成功');
        } else {
            $this->error($user->getError());
        }
    }

    public function getUserInfo(User $user, $uid)
    {
        $info = $user->info($uid);
        if ($info) {
            $this->assign('user', $info);
            return $this->fetch();
        } else {
            return '用户不存在';
        }
    }

    protected function getUserRole()
    {
        $uid  = Session::get('user_id');
        $user = User::get($uid);
        return $user->role();
    }
}
?>

从上面的用法中我们可以注意几点：

    ^业务逻辑应当封装到具体模型中，并由控制器来调用；
    ^register和login方法获取用户主键的方法区别；
    ^可以设置模型的错误信息，并且用getError方法获取；

	
应用TP5中模型的多态1对多关联得到专利事务对应的专利名称$vo.issmap.topic，关键是要在issinfo模型中定义morphTo的方法名"issmap"，就可"." 出$vo.issmap.topic}

<td class="patInfo"><a href="{$home}/patent/index/patinfo/id/{$vo.issmap_id}" target="_blank" data-toggle="tooltip">{$vo.issmap.topic}</a></td>

<!--/  HY 2018/1/21 -->


<!--HY 2018/2/6 -->

<?php
// 查询JSON类型字段 （info字段为json类型,mysql V5.0.1）
Db::table('think_user')->where('info$.email','thinkphp@qq.com')->find();
?>


// json类型与array类型

// 模型model（user.php）：设定“authority”字段为json
<?php
namespace app\user\model;

use think\Model;

class User extends Model
{   
	protected $type = [
    	'authority'  =>  'json',
	];  
}
?>

// 控制器controller(controller.php)：
<?php
	use app\user\model\User as UserModel;
	
	$user=new UserModel;           
	$userlg=$user->where('username',$username)->where('pwd',$pwd)->find();
	
	$this->assign([
		//模型对象转换成数组赋值给前端模板变量，会出现"Array to string conversion"错误
        //'userlg'=>$userlg->toArray(),
		//模型对象赋值给前端模板变量
		'userlg'=>$userlg,
		//模型对象转换成json字符串赋值给前端模板变量，
		'userJson'=>$userlg->toJson(),
        
		// json引用("authority"字段已在模型中定义为"json"),此时对象方式引用$userlg->authority->att->delete不适用，会出现“Trying to get property of non-object”错误
        'delete'=>$userlg['authority']['att']['delete'],
		//数组方式引用后赋值给前端模板变量,此时对象方式引用$userlg->authority也适用
        'authArray'=>$userlg['authority'],
		// php的json_encode()函数将数组转换成json字符串
        'authJson'=>json_encode($userlg['authority']),        
		//json引用("authority"字段已在模型中定义为"json")后赋值给前端模板变量,此时对象方式引用$userlg->authority->att不适用，会出现“Trying to get property of non-object”错误
        'authAttArray'=>$userlg['authority']['att'],
		// php的json_encode()函数将数组转换成json字符串
        'authAttJson'=>json_encode($userlg['authority']['att']),
    ]);
	
?>
//前端页面
<html>
	<div>
	<P>前端模板变量$userlg={$userlg}<br>后端赋值语句'userlg'=>$userlg</P>
					
	<P>前端模板变量$userJson={$userJson}<br>后端赋值语句'userJson'=>$userlg->toJson()</P>
					
	<P>前端模板变量$userlg.authority.att.delete={$userlg.authority.att.delete}<br>后端赋值语句'userlg'=>$userlg</P>
					
	<P>前端模板变量$delete={$delete}<br>后端赋值语句'delete'=>$userlg['authority']['att']['delete']</P>
					
	<P>前端模板变量$authJson={$authJson}<br>后端赋值语句'authJson'=>json_encode($userlg['authority'])</P>
					
	<P>前端模板变量$authArray.att.delete={$authArray.att.delete}<br>后端赋值语句'authArray'=>$userlg['authority']</P>
					
	<P>前端模板变量$authAttJson={$authAttJson}<br>		后端赋值语句'authAttJson'=>json_encode($userlg['authority']['att'])</P>
					
	<P>前端模板变量$authAttArray.delete={$authAttArray.delete}<br>后端赋值语句'authAttArray'=>$userlg['authority']['att']</P>
					
	</div>
</html>
//前端页面运行后显示效果：
前端模板变量$userJson={"id":23,"usernum":"","username":"test","pwd":"c4ca4238a0b923820dcc509a6f75849b","dept":"dept2","enable":1,"rolety_id":2,"authority":{"att":{"delete":1,"upload":1,"download":1},"isspat":{"edit":1,"audit":1,"create":1,"approve":1,"execute":1,"maintain":1},"isspro":{"edit":0,"audit":0,"create":0,"approve":0,"execute":0,"maintain":0},"issthe":{"edit":0,"audit":0,"create":0,"approve":0,"execute":0,"maintain":0}}}
后端赋值语句'userJson'=>$userlg->toJson()

前端模板变量$userlg.authority.att.delete=1
后端赋值语句'userlg'=>$userlg

前端模板变量$delete=1
后端赋值语句'delete'=>$userlg['authority']['att']['delete']

前端模板变量$authJson={"att":{"delete":1,"upload":1,"download":1},"isspat":{"edit":1,"audit":1,"create":1,"approve":1,"execute":1,"maintain":1},"isspro":{"edit":0,"audit":0,"create":0,"approve":0,"execute":0,"maintain":0},"issthe":{"edit":0,"audit":0,"create":0,"approve":0,"execute":0,"maintain":0}}
后端赋值语句'authJson'=>json_encode($userlg['authority'])

前端模板变量$authArray.att.delete=1
后端赋值语句'authArray'=>$userlg['authority']

前端模板变量$authAttJson={"delete":1,"upload":1,"download":1}
后端赋值语句'authAttJson'=>json_encode($userlg['authority']['att'])

前端模板变量$authAttArray.delete=1
后端赋值语句'authAttArray'=>$userlg['authority']['att']

<!--/  HY 2018/2/6 -->


<!--  HY 2018/2/7 -->
// 模板
<?php
//在控制器中我们给模板变量（字符串型）赋值：
$view = new View();
$view->name = 'thinkphp';
//对数组变量赋值
$data['name'] = 'ThinkPHP';
$data['email'] = 'thinkphp@qq.com';
$view->assign('data',$data);
//输出变量到模板
return $view->fetch('模板名');

//或是直接用助手函数输出变量到模板：
return view('模板名',['name'=>'thinkphp','data'=>$data]);

?>

<html>
//变量输出。模板标签的变量输出根据变量类型有所区别
//数组变量输出方式1
<p>Name：{$data.name}</p>
<p>Email：{$data.email}</p>
//数组变量输出方式2
<p>Name：{$data['name']}</p>
<p>Email：{$data['email']}</p>
//输出多维数组的时候，往往要采用输出方式2。

//data变量是一个对象（并且包含有name和email两个属性）
//输出方式1
<p>Name：{$data:name}</p>
<p>Email：{$data:email}</p>

//输出方式2
<p>Name：{$data->name}</p>
<p>Email：{$data->email}</p>

</html>

<html>
//系统变量输出
//可以直接在模板中输出，系统变量的输出通常以{$Think** 打头，例如：
<p>{$Think.server.script_name}</p> // 输出$_SERVER['SCRIPT_NAME']变量
<p>{$Think.session.user_id}</p> // 输出$_SESSION['user_id']变量
<p>{$Think.get.pageNumber}</p> // 输出$_GET['pageNumber']变量
<p>{$Think.cookie.name}</p>  // 输出$_COOKIE['name']变量

//支持输出 $_SERVER、$_ENV、 $_POST、 $_GET、 $_REQUEST、$_SESSION和 $_COOKIE变量。
</html>

<html>
//请求参数输出,
//模板支持直接输出Request请求对象的方法参数，用法如下：$Request.方法名.参数,例如：

<p>{$Request.get.id}</p>
<p>{$Request.param.name}</p>

//支持Request类的大部分方法，但只支持方法的第一个参数。例如：
// 调用Request对象的get方法 传入参数为id
<p>{$Request.get.id}</p>
// 调用Request对象的param方法 传入参数为name
<p>{$Request.param.name}</p>
// 调用Request对象的param方法 传入参数为user.nickname
<p>{$Request.param.user.nickname}</p>
// 调用Request对象的root方法
<p>{$Request.root}</p>
// 调用Request对象的root方法，并且传入参数true
<p>{$Request.root.true}</p>
// 调用Request对象的path方法
<p>{$Request.path}</p>
// 调用Request对象的module方法
<p>{$Request.module}</p>
// 调用Request对象的controller方法
<p>{$Request.controller}</p>
// 调用Request对象的action方法
<p>{$Request.action}</p>
// 调用Request对象的ext方法
<p>{$Request.ext}</p>
// 调用Request对象的host方法
<p>{$Request.host}</p>
// 调用Request对象的ip方法
<p>{$Request.ip}</p>
// 调用Request对象的header方法
<p>{$Request.header.accept-encoding}</p>

</html>

<html>
//使用函数
//对模板输出变量使用函数，可以使用：
<p>{$data.name|md5}</p>

//如果函数有多个参数需要调用，则使用：
<p>{$create_time|date="y-m-d",###}</p>
//表示date函数传入两个参数，每个参数用逗号分割，这里第一个参数是y-m-d，第二个参数是前面要输出的create_time变量，因为该变量是第二个参数，因此需要用###标识变量位置

//如果前面输出的变量在后面定义的函数的第一个参数，
<p>{$data.name|substr=###,0,3}</p>
//则可以直接简化为：
<p>{$data.name|substr=0,3}</p>

//还可以支持多个函数过滤，多个函数之间用“|”分割即可，函数会按照从左到右的顺序依次调用。例如：
<p>{$name|md5|strtoupper|substr=0,3}</p>

</html>

<html>
//使用默认值
//给变量输出提供默认值，例如：
<p>{$user.nickname|default="这家伙很懒，什么也没留下"}</p>

//对系统变量依然可以支持默认值输出，例如：
<p>{$Think.get.name|default="名称为空"}</p>

//默认值和函数可以同时使用，例如：
<p>{$Think.get.name|getName|default="名称为空"}</p>


</html>

<html>
//运算符
// “+”“-” “*” “/”和“%” “++” “--”的支持
<p>{$a+$b}、{$a-$b}、{$a*$b}、{$a/$b}、{$a%$b}、{$a++}、{++$a}、{$a--}、{--$a}、{$a+$b*10+$c}</p>

//在使用运算符的时候，不再支持常规函数用法，
<p>{$user['score']|myFun*10}</p> //错误的
<p>{$user['score']+myFun($user['level'])}</p>//正确的

//支持三元运算符，例如：
<p>{$status? '正常' : '错误'}</p>
<p>{$info['status']? $info['msg'] : $info['error']}</p>
<p>{$info.status? $info.msg : $info.error }</p>

//前面的表达式为真输出yes,否则输出no， 条件可以是“==”、“===”、“!=”、“!==”、“>=”、“<=”
<p>{$a==$b ? 'yes' : 'no'}</p>

</html>

<html>
//原样输出
//所有可能和内置模板引擎的解析规则冲突的地方都可以使用literal标签处理。
{literal}
    Hello,{$name}！
{/literal}

//上面的{$name}标签被literal标签包含，因此并不会被模板引擎解析，而是保持原样输出。
</html>

<?php
//查询语言
//查询条件相关的用法我们称之为查询语言，查询语言可以用于数据库的URD操作，要掌握查询语言的核心，谨记：2个方法，3个用法，8个要诀。

//2个方法
//where和whereOr方法可以在一次查询操作中多次调用

//3个用法
//表达式、数组和闭包用法，并且可以混合使用
//表达式用法:
//where('字段名','表达式','查询条件');
//whereOr('字段名','表达式','查询条件');
//数组用法,数组用法其实是多字段的表达式用法，在一个方法完成所有的查询条件:    
where([
'字段名1'=>['表达式','查询条件'],
'字段名2'=>['表达式','查询条件'],
'字段名2'=> '条件（等于）',
...
])
//数组用法不够灵活，有时候需要和其它用法配合使用，出于安全考虑也并不推荐。

//闭包用法,指的是直接在where或者whereOr方法中传入闭包，和前面两种用法配合可以完成复杂的查询条件，闭包方法只有一个查询对象参数，如果需要在闭包中使用外部的变量，可以使用闭包的use语法
$id     = 1;
$name   = 'think';
$result = Db::table('data')
    ->where(function ($query) use ($id) {
        $query->where('id', $id);
    })
    ->whereOr(function ($query) use ($id, $name) {
        $query->where('name', 'like', '%' . $name . '%')->where('id', '<>', $id);
    })
    ->select();

//8个要诀
    //1查询条件的调用次序就是生成SQL的条件顺序；
    //2查询字段用&分割表示对多个字段使用AND查询；
    //3查询字段用|分割表示对多个字段使用OR查询；
    //4对同一个查询字段多次调用非等查询条件会合并查询；
    //5闭包查询和EXP查询会在生成的查询语句两边加上括号；
    //6用闭包查询替代3.2版本的组合查询；
    //7除了EXP查询外，其它查询都会自动使用参数绑定；
	//8如果查询条件来自用户输入，尽量使用表达式和闭包查询，数组条件查询务必使用官方推荐的方法获取变量；
?>


<!--/  HY 2018/2/7 -->

<!--HY 2018/2/11 -->
模板注释，在生成编译缓存文件后会自动删除，这一点和Html的注释不同。
<html>
//单行注释,注意{和注释标记之间不能有空格。
{// 这是模板注释内容 }

//支持多行注释，例如：
{/* 这是模板
注释内容*/ }

</html>

<!--/  HY 2018/2/11 -->

<!--HY 2018/2/13 -->
后端如何响应前端的request。后端与前端的数据交换可以通过request动态进行。
request里前端可以明确：
1.前端需要后端返回什么？
	例：前端需要JSON数组
	"$request->param('returnType')=='_JSON'"
2.前端提供了什么？
	例：patId
	"$request->param('patId')==58"

后端响应request
1.从数据库查到所需数据(已定义模型对象$patObj):
	"$patObj->where('id',$request->param('patId'))->find()"
2.组合返回前端的数组：
	"array_merge($patObj->where('id',$request->param('patId'))->find()->toArray(),array("today"=>date('Y-m-d'),"username"=>$this->username,"deptMaintainer"=>$this->dept))"
3.返回前端JSON数组：
	"return json(array_merge($patObj->where('id',$request->param('patId'))->find()->toArray(),array("today"=>date('Y-m-d'),"username"=>$this->username,"deptMaintainer"=>$this->dept)));"


<html>

<script>
//前端提交request的方式（我在jQuery常用）：
	//1."load()":Loads data from a server and puts the returned data into the selected elementhtml中的某一个元素
	$(selector).load(URL,[data],[function(responseTxt, statusTxt, xhr){}]); 
	/* The optional 3rd parameter specifies a callback function to run when the load() method is completed. The callback function can have different parameters: */
    /* responseTxt - contains the resulting content if the call succeeds */
    /* statusTxt - contains the status of the call */
    /* xhr - contains the XMLHttpRequest object */

	
	//2."$.post()":Loads data from a server using an AJAX HTTP POST request.
	$.post('url',sendData,function(data){});
	$(selector).post(URL,[data],[function(data,status,xhr){}],[dataType])

	//3."({name:value, name:value, ... })":Performs an async AJAX request.The parameters specifies one or more name/value pairs for the AJAX request.
	$.ajax({
		//type可选：'post','get'
		type: 'post',
        url: 'issPatOprt',
        data: formData,
		// 当有文件要上传时，此项是必须的，否则后台无法识别文件流的起始位置
        contentType: false,
		// 是否序列化data属性，默认true(注意：false时type必须是post)
        processData: false,
		success(result,status,xhr): function(data) {
        	
        },
		error(xhr,status,error)	: function(data) {
        	
        }	
	});

</script>

</html>
	
//后端代码：
<?php 
	if($request->param('returnType')=='_JSON'){   
    	return json(array_merge($patObj->where('id',$request->param('patId'))->find()->toArray(),array("today"=>date('Y-m-d'),"username"=>$this->username,"deptMaintainer"=>$this->dept)));
    }
?>

<!--/  HY 2018/2/13 -->

<!-- HY 2018/2/21 -->
数组
//遍历数组，输出键值对方式(PHP 4, PHP 5, PHP 7)
<?php
$arr = array("one", "two", "three");
reset($arr);
while (list($key, $value) = each($arr)) {
    echo "Key: $key; Value: $value<br />\n";
}

foreach ($arr as $key => $value) {
    echo "Key: $key; Value: $value<br />\n";
}
?>

//遍历数组，输出值方式(PHP 4, PHP 5, PHP 7)
<?php
$arr = array("one", "two", "three");
reset($arr);
while (list(, $value) = each($arr)) {
    echo "Value: $value<br>\n";
}

foreach ($arr as $value) {
    echo "Value: $value<br />\n";
}
?>

用 list() 给嵌套的数组解包(PHP 5 >= 5.5.0, PHP 7)
<?php
$array = [
    [1, 2],
    [3, 4],
];

foreach ($array as list($a, $b)) {
    // $a contains the first element of the nested array,
    // and $b contains the second element.
    echo "A: $a; B: $b\n";
}
?>
以上例程会输出：
A: 1; B: 2
A: 3; B: 4

<!--/  HY 2018/2/21 -->


<!-- HY 2018/2/22 -->
数组和字符串互相转换实现方法

$array=explode(separator,$string); 
$string=implode(glue,$array);

<?php
$s1='Mon-Tue-Wed-Thu-Fri';
$days_array=explode('-',$s1);
//$days_array 变量现在是一个有5个元素的数组，其元素 Mon 的索引为0，Tue 的索引为1，等等。
$s2=implode(',',$days_array);
//$s2变量现在是一个用逗号分隔的一个星期中各天的列表：Mon,Tue,Wed,Thu,Fri


?>
//字段指定,字段排除,field方法
<?php
//字段指定
Db::table('think_user')->field('id,title,content')->select();

//字段设置别名
Db::table('think_user')->field('id,nickname as name')->select();

//field方法中直接使用函数
Db::table('think_user')->field('id,SUM(score)')->select();

//field方法的参数可以支持数组
Db::table('think_user')->field(['id','title','content'])->select();
Db::table('think_user')->field(['id','nickname'=>'name'])->select();
Db::table('think_user')->field(['id','concat(name,"-",id)'=>'truename','LEFT(title,7)'=>'sub_title'])->select();

//显式的调用所有字段，获取数据表的所有字段列表，哪怕数据表有100个字段。
Db::table('think_user')->field(true)->select();

//字段合法性检测。field方法结合数据库的写入方法使用就可以完成表单提交的字段合法性检测
Db::table('think_user')->field('title,email,content')->insert($data);
//即表示表单中的合法字段只有title,email和content字段，无论用户通过什么手段更改或者添加了浏览器的提交字段，都会直接屏蔽。因为，其他字段我们都不希望由用户提交来决定，可以通过自动完成功能定义额外的字段写入。

//V5.0.17+版本开始，系统还提供了fieldRaw方法，用于更安全的指定字段，尤其是使用了SQL函数的情况。
Db::table('think_user')->fieldRaw('title,email,content,max(score) as max_score')->insert($data);

//在开启数据表字段严格检查的情况下，提交了非法字段会抛出异常，可以在数据库设置文件中设置：
// 关闭严格字段检查
'fields_strict'	=>	false,

//字段排除
Db::table('think_user')->field('user_id,content',true)->select();
//或者用
Db::table('think_user')->field(['user_id','content'],true)->select();

?>

//注意的是，字段排除功能不支持跨表和join操作。


<!--/  HY 2018/2/22 -->

<!--  HY 2018/3/15 -->

request数组
如果你要获取的数据为数组，请一定注意要加上 /a 修饰符才能正确获取到。

<html>
<form id="fmAtt" action="x" method="post">
	<input name="attId[]" value="1" type="text" >
	<input name="attId[]" value="2" type="text" >
	<input name="attId[]" value="3" type="text" >
	
	<input type="submit" >

</form>

</html>

<?php
	$arr=$request->param('attId/a');

?>


<!--/  HY 2018/3/15 -->

<!--  HY 2018/3/21 -->
移动文件，上传到服务器的文件要改变存放目录
<?php
//通过命名空间引入TP5内置的File对象重新命名为FileObj
use think\File as FileObj; 

//方法一： 应用php原生的raname()函数，应确保newDir存在，否则应先创建newDir
	rename('oldDir\fileOldName','newDir\fileNewName');
	
	//注意：在应用rename()移动文件时，要确保该文件没有被打开或该文件对象已被释放，如下语句在tp5中会报错[code 32]
	$fileStr='oldDir\fileOldName';
	$file = new FileObj($fileStr); 
	rename('oldDir\fileOldName','newDir\fileNewName');
	
	//上述代码需修改为
	$fileStr='oldDir\fileOldName';
	$file = new FileObj($fileStr);
	//释放文件对象
	unset($file);	
	rename('oldDir\fileOldName','newDir\fileNewName');
	
//方法二： 应用php原生的copy()函数+unlink()函数，应确保newDir存在，否则应先创建newDir。同上述方法一，应用时要确保该文件没有被打开或该文件对象已被释放，否则原文件删除时报错，无法删除
	//复制新文件
	copy('oldDir\fileOldName','newDir\fileNewName');
	//删除原文件 
	unlink($fileStr);
	
	$fileObj= new FileObj($fileStr);
    //得到文件的md5散列值,32位16进制数
	$fileMd5=$fileObj->hash('md5');
	//得到文件的sha1散列值,40位16进制数
    $fileSha1=$fileObj->hash('sha1');
	
	
//说明：
//1.对于文件，rename可以在不同盘符之间移动
//2.对于空文件夹，rename也可在不同盘符之间移动，但目标文件夹的父目录必须存在。
//3.对于非空文件夹，只能在同一盘符下移动。
//4.对于几十M的文件，rename比copy+unlink快上百倍。

//PHP原生dirname(),给出一个包含有指向一个文件的全路径的字符串，本函数返回去掉文件名后的目录名。 
dirname($fileStr);

//PHP原生basename(),给出一个包含有指向一个文件的全路径的字符串，本函数返回基本的文件名。 
basename($fileStr);

//PHP原生scandir(dir),返回一个 array，包含有 dir中的文件和目录。
$dir    = '/tmp';
$files1 = scandir($dir);
$files2 = scandir($dir, 1);

print_r($files1);
print_r($files2);

//$files1输出：按字母升序
/* Array
(
   [0] => .
   [1] => ..
   [2] => bar.php
   [3] => foo.txt
   [4] => somedir
) 

//$files2输出：按字母降序
Array
(
    [0] => somedir
    [1] => foo.txt
    [2] => bar.php
    [3] => ..
    [4] => .
)


*/

?>

<!--/  HY 2018/3/21 -->


<!--  HY 2018/3/22 -->
//文件上传
TP5内置的上传(\library\think\File.php)只是上传到本地服务器，上传到远程或者第三方平台的话需要自己扩展。

上传成功后返回的仍然是一个File对象，除了File对象自身的方法外，并且可以使用PHP原生SplFileObject的属性和方法，便于进行后续的文件处理。

上传文件的唯一性
系统默认提供了几种上传命名规则，包括：
规则	描述
date	根据日期和微秒数生成
md5	    对文件使用md5_file散列生成
sha1	对文件使用sha1_file散列生成
uniqid   应用的php原生uniqid()函数（以微秒计的当前时间，生成一个唯一的13位ID）得到的文件名
<?php
//通过getInfo()方法得到上传文件对象的信息
$request->file('attFile')->getInfo();

// 获取表单上传文件 例如上传了001.jpg
$fileSet = request()->file('image');

// 移动到框架根目录的uploads/temp/ 目录下,系统默认使用date规则，在上传目录下面生成以当前日期为子目录，以微秒时间的md5编码为文件名的文件，例如，下面语句执行后生成的文件名可能是：“../uploads/temp/20160510/42a79759f284b767dfcb2a0197904287.jpg”

$info = $fileSet->validate(['size'=>10485760,'ext'=>'jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,rar'])->move(ROOT_PATH.'uploads'.DS.'temp');

//应用uniqid规则，生成的文件名类似于：“../uploads/temp/573d3b6d7abe2.jpg”，是应用的php原生uniqid()函数（以微秒计的当前时间，生成一个唯一的13位ID）得到的文件名
$info = $fileSet->rule('uniqid')->validate(['size'=>10485760,'ext'=>'jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,rar'])->move(ROOT_PATH.'uploads'.DS.'temp');

//应用md5规则，生成的文件名类似于：“../uploads/temp/72/ef580909368d824e899f77c7c98388.jpg”，md5和sha1规则会自动以散列值的前两个字符作为子目录，后面的散列值作为文件名。
$info = $fileSet->rule('md5')->validate(['size'=>10485760,'ext'=>'jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,rar'])->move(ROOT_PATH.'uploads'.DS.'temp');

// 移动到服务器的上传目录,并且使用原文件名
$file->move('/home/www/upload/','');

// 移动到服务器的上传目录,并且设置不覆盖
$file->move('/home/www/upload/',true,false);

//获取文件hash散列值

// 移动到服务器的上传目录 并且使用原文件名
$upload = $file->move('/home/www/upload/');
// 获取上传文件(不是文件的名称，是整个文件内容)的hash散列值
echo $upload->hash('sha1');
echo $upload->hash('md5');

//PHP原生hash_file()/md5_file()函数获取文件的散列值，得到md5散列值(32位十六进制数字)
hash_file('md5', 'example.txt');
md5_file('example.txt');

//PHP原生sha1_file()函数获取文件的散列值，得到sha1散列值(40位十六进制数字)。
sha1_file('example.txt');

?>



<!--/  HY 2018/3/22 -->

<!--  HY 2018/4/20 -->
//common.php
整个应用的公共函数放在\application\common.php中，
某个模块的公共函数放在\application\模块名\common.php中，
框架自动加载上述common.php文件，定义好的公共函数可随时引用。

<!--  HY 2018/4/20 -->


<!--  HY 2018/5/24 -->
扩展类可以部署到任意目录
1、类名首字母大写。
2、类的文件名和类名一致。(类的文件名：类名.php)
3、类的命名空间（引用的时候use到类的文件名;类自身声明的时候到自己所在文件夹名即可，如果把类直接放到extend目录，类本身和引用可以不用use, 直接 new \类名即可, 如果放在其它任意目录或者extend子目录，在类本身的头部需要namespace, 引用可以use或者直接new '\'+命名空间）。

//自动注册
只需要把自己的类库包目录放入EXTEND_PATH目录（默认为extend，可配置,可以在入口文件中随意修改extend目录的名称，例如：define('EXTEND_PATH', '../extension/');），就可以自动注册对应的命名空间，例如：
我们在extend目录下面新增一个my目录，然后定义一个\my\Test类（ 类文件位于extend/my/Test.php）如下：
<?php
namespace my;

class Test 
{
    public function sayHello()
    {
        echo 'hello';
    }
}

?>

我们就可以直接实例化和调用：
<?php
$Test = new \my\Test();
$Test->sayHello();
?>
或者
<?php
use my\test;
$Test = new Test();
$Test->sayHello();
?>

ThinkPHP5建议所有的扩展类库都使用命名空间定义，如果你的类库没有使用命名空间，则不支持自动加载，必须使用Loader::import方法先导入文件后才能使用。
<?php
Loader::import('first.second.Foo');
$foo = new \Foo();
?>

<!-- // HY 2018/5/24 -->

<!--  HY 2018/6/6 -->
// 自动时间字段（模型中的create_time字段,update_time字段）

框架做了一些强化支持，无需定义获取器和修改器就能完成时间日期类型字段的自动处理。

默认情况下自动写入时间戳字段功能是关闭的，可以在模型里面定义
<?php

namespace app\index\model;

use think\Model;

class User extends Model
{
	// 开启时间字段自动写入，默认字段类型为整形
	protected $autoWriteTimestamp = true; 
	
	// 开启时间字段自动写入，并设置字段类型为datetime
	protected $autoWriteTimestamp = 'datetime'; 
}
?>

开启时间字段（这里的时间字段支持整型、时间戳和日期类型）自动写入后，会默认自动写入两个时间字段：create_time（创建时间，新增数据的时候自动写入）和update_time（更新时间，新增和更新的时候都会自动写入），并且以整型类型写入数据库。

autoWriteTimestamp属性支持设置的时间字段类型包括：整型（设置为true的时候使用该类型）、时间（datetime）和时间戳（timestamp）。

模型中设置好后，自动时间字段的写入如下：
<?php
// 新增用户数据
$user       = new User;
$user->name = 'thinkphp';
// 会自动写入create_time和update_time字段
$user->save();
echo $user->create_time;
echo $user->update_time;

// 更新用户数据
$user->name = 'topthink';
// 会自动更新update_time字段
$user->save();
echo $user->create_time;
echo $user->update_time;
?>

create_time 和update_time字段的值不需要进行设置，系统会自动写入。如果你手动进行设置的话，则不会触发自动写入机制（也就是说不会进行时间字段的格式转换），你需要按照实际的字段类型设置。

如果时间字段类型为整型，自动写入的时间字段会在获取的时候自动转换为dateFormat属性设置的时间格式，所以不需要再次对时间字段进行格式化输出，以免出错。如果不希望自动格式化，可以设置数据库配置参数datetime_format 的值为false。（时间类型字段则无需更改设置）

改变时间字段的输出格式示例：
<?php

namespace app\index\model;

use think\Model;

class User extends Model
{
	// 开启时间字段自动写入 并设置字段类型为datetime
	protected $autoWriteTimestamp = 'datetime'; 
    protected $dateFormat = 'Y/m/d H:i:s';
}

?>

观察输出的值是否有变化
<?php
// 新增用户数据
$user       = new User;
$user->name = 'thinkphp';
// 会自动写入create_time和update_time字段
$user->save();
echo $user->create_time;
echo $user->update_time;

// 更新用户数据
$user->name = 'topthink';
// 会自动更新update_time字段
$user->save();
echo $user->create_time;
echo $user->update_time;

?>

上面的设置都是针对单个模型的，如果需要设置全局使用，可以在数据库配置文件中设置下面的参数：
<?php
// 开启自动写入时间字段 支持设置字段类型（同前）
'auto_timestamp' => true,
// 时间字段取出后的时间格式
'datetime_format' => 'Y-m-d H:i:s',
?>

如果全局设置开启时间字段自动写入后，部分模型可以单独关闭，例如：

<?php

namespace app\index\model;

use think\Model;

class Data extends Model
{
	// 关闭时间字段自动写入
	protected $autoWriteTimestamp = false; 
}
?>

甚至说部分模型的时间字段名和类型可以单独设置

<?php

namespace app\index\model;

use think\Model;

class Data extends Model
{
	// 设置本模型时间字段的类型为“datetime”
	protected $autoWriteTimestamp = 'datetime'; 
    // 定义时间字段名
    protected $createTime = 'create_at';
    protected $updateTime = 'update_at';      
}
?>
在系统自动时间字段之外的其它时间字段，如果需要自动格式输出，可以设置类型转换。

//自动时间字段数据类型转换
自动时间字段写入只支持创建时间和更新时间的自动写入和格式化读取，如果模型有其它时间字段的话，则可以通过设置类型转换来完成。

例如User模型的birthday字段也使用了时间类型。可以通过定义修改器和读取器的方式来处理birthday字段，更简单的办法则是设置类型转换，免去定义修改器和读取器的麻烦。
<?php
namespace app\index\model;

use think\Model;

class User extends Model
{
    protected $type = [
        'birthday'  =>  'datetime:Y/m/d',
    ];   
}
?>
type属性用于定义类型转换（支持的类型:integer,float,boolean,array,object,serialize,json,timestamp,datetime），'datetime:Y/m/d'表示使用datetime类型，输出格式为Y/m/d，下面是一段代码示例。
<?php
$user = User::get(1);
// 输出 2009/02/14
echo $user->birthday;

$user->birthday = '2017-1-1';
$user->save();
// 输出 2017/1/1
echo $user->birthday;
?>

类型转换支持的类型设置包括：
"integer":设置为integer（整型）后，该字段写入和输出的时候都会自动转换为整型。

"float":该字段的值写入和输出的时候自动转换为浮点型。

"boolean":该字段的值写入和输出的时候自动转换为布尔型。

"array":如果设置为强制转换为array类型，系统会自动把数组编码为json格式字符串写入数据库，取出来的时候会自动解码。

"object":该字段的值在写入的时候会自动编码为json字符串，输出的时候会自动转换为stdclass对象。

"serialize":指定为序列化类型的话，数据会自动序列化写入，并且在读取的时候自动反序列化。

"json":指定为json类型的话，数据会自动json_encode写入，并且在读取的时候自动json_decode处理。

"timestamp":指定为时间戳字段类型（注意并不是数据库的timestamp类型，事实上是int类型）的话，该字段的值在写入时候会自动使用strtotime生成对应的时间戳，输出的时候会自动转换为dateFormat属性定义的时间字符串格式，默认的格式为"Y-m-d H:i:s"。

"datetime":和timestamp类似，区别在于写入和读取数据的时候都会自动处理成时间字符串Y-m-d H:i:s的格式。

PHP5.6版本以下，数据库查询的字段返回数据类型都是字符串的，在做API开发的时候最好是使用类型转换强制处理下，PHP5.6版本开始，PDO查询的返回数据的字段类型都是实际的字段类型格式。


// 软删除（模型中的delete_time字段）
对数据频繁使用删除操作会导致性能问题，因此不推荐直接物理删除数据，而是用逻辑删除替代，也就是下面要讲的软删除。软删除的作用就是把数据加上删除标记，而不是真正的删除，同时也便于需要的时候进行数据的恢复。

要使用软删除功能，需要引入“SoftDelete trait”，例如User模型按照下面的定义就可以使用软删除功能：
<?php
namespace app\index\model;

use think\Model;
use traits\model\SoftDelete;

class User extends Model
{
    use SoftDelete;
	protected $autoWriteTimestamp = 'datetime';
}

?>

为了配合软删除功能，还需要在数据表中添加delete_time字段，ThinkPHP5的软删除功能使用时间戳类型（数据表默认值为Null），用于记录数据的删除时间。

可以用类型转换指定软删除字段的类型，建议数据表的所有时间字段统一使用autoWriteTimestamp属性规范时间类型（支持datetime、date、timestamp以及integer）

定义好模型后，我们就可以使用：
<?php
// 软删除
User::destroy(1);
// 真实删除
User::destroy(1,true);

$user = User::get(1);
// 软删除
$user->delete();
// 真实删除
$user->delete(true);
?>

默认情况下查询的数据不包含软删除数据，如果需要包含软删除的数据，可以使用下面的方式查询：
<?php
User::withTrashed()->find();
User::withTrashed()->select();
?>

如果仅仅需要查询软删除的数据，可以使用：
<?php
User::onlyTrashed()->find();
User::onlyTrashed()->select();
?>

如果你的查询条件比较复杂，尤其是某些特殊情况下使用OR查询条件会把软删除数据也查询出来，可以使用闭包查询的方式解决，如下：
<?php
User::where(function($query) {
	$query->where('id', '>', 10)
    	->whereOr('name', 'like', 'think');
})->select();
?>

//获取模型数据
<?php
// 模型外部获取
$model->name

// 模型内部获取,避免数据表的字段名和模型的内部属性重名后发生冲突，
$this->getData('name');
$this->getAttr('email');

//例如下面的代码得到的是模型的name属性值，而不是数据表name字段的值
$this->name;
?>
getData和getAttr方法的区别前者是原始数据，后者是经过读取器处理的数据，如果没有定义数据读取器的话，两个方法的结果是相同的。


// 设置模型数据
<?php
// 模型外部设置
$model->name='thinkphp'

// 模型内部设置
$this->data('name','thinkphp');
$this->setAttr('email','thinkphp@qq.com');
?>
data和setAttr方法的区别前者是赋值最终数据，后者赋值的数据还会经过修改器处理，如果没有定义修改器的话，两个方法的结果是相同的。


<!--//  HY 2018/6/6 -->



<!--  HY 2018/6/13 -->
//事务处理

//使用条件：
1.需要数据库引擎支持事务处理。比如MySQL的 MyISAM不支持事务处理，需要使用InnoDB引擎。
2.确保数据库连接是相同的。

//数据库事务,使用 transaction 方法操作数据库事务，当发生异常会自动回滚，例如：
<?php
//自动控制事务处理
Db::transaction(function(){
    Db::table('think_user')->find(1);
    Db::table('think_user')->delete(1);
});


// 手动控制事务，

// 启动事务
Db::startTrans();
try{
    Db::table('think_user')->find(1);
    Db::table('think_user')->delete(1);
    // 提交事务
    Db::commit();    
} catch (\Exception $e) {
    // 回滚事务
    Db::rollback();
}
?>

//模型事务,在模型中使用事务和数据库中使用事务一样。
<?php
//自动控制事务处理
$this->transaction(function(){
	// 添加实现代码
});

// 手动控制事务，
$this->startTrans();
try{
	// 添加实现代码
    // ...
    // 提交事务
    $this->commit();    
} catch (\Exception $e) {
    // 回滚事务
    $this->rollback();
}
?>

<!--//  HY 2018/6/13 -->


<!--  HY 2018/6/21 -->
//模型数据集

数据集的优势：
    - 数据更对象化；
    - 关联操作更方便；
    - 数据集本身可以单独定义独立的业务方法；

<?php
//前提条件：应用的设置文件“database.php”中已设置模型的数据集返回类型为数组

//查询要求：得到所有未禁用的用户的指定字段信息数据集
//方式1：模型类静态调用链式查询使用field方法设置可见字段。
$user=UserModel::where('enable','1')->field('username,dept,usergroup_id')->order('dept', 'asc')->select();

//方式2：模型闭包查询使用field方法设置可见字段。
$user=UserModel::all(function($query){
                       $query->where('enable','1')->field('username,dept,usergroup_id')->order('dept', 'asc');
                        }); 
						
//方式3：模型闭包查询后转换为数据集对象，数据集对象的visual方法设置可见字段
$user=UserModel::all(function($query){
                        $query->where('enable','1')->order('dept', 'asc');
                    });
//应用tp5助手函数collection将$user转换为数据集对象后设置可见字段。
$user = collection($user)->visible(['username','dept','usergroup_id']);

?>

模型的单个数据查询返回的都是模型对象实例，但查询多个数据的时候默认返回的是一个包含模型对象实例的数组。框架提供了一个Collection数据集对象来进行统一的模型的对象化操作，替代默认的数组数据集更好的封装自己的数据处理和业务逻辑。

设置数据集对象后，查询多个数据的方法（包括Db类的select和模型类的all方法）返回的结果类型就会变成think\model\Collection对象实例。

有两种方式可以设置
<?php

//方式1：是全局设置数据库的配置参数（默认设置为array）。该设置会影响所有的查询（包括Db类和模型类）。
'resultset_type'  => 'collection',

//方式2：是在模型类中添加属性设置。该设置仅仅影响设置的模型中的查询结果，
protected $resultSetType = 'collection';

?>
如果需要多个模型或者全部模型支持，可以使用继承或者使用第一种数据库配置方式。

//数据集对象和普通的二维数组

数据集对象和普通的二维数组在使用上的一个最大的区别就是数据是否为空的判断，
<?php
//二维数组的数据集，直接使用empty()函数
$resultSet = User::all();
if (empty($resultSet)) {
    echo '数据集为空';
}

//数据集对象，使用isEmpty()函数
$resultSet = User::all();
if ($resultSet->isEmpty()) {
    echo '数据集为空';
}

//通用的判断数据是否为空的方式，用count()函数
$resultSet = User::all();
if (0 == count($resultSet)) {
    echo '数据集为空';
}
?>

//数据集对象的方法和数组函数的对应关系

作用				数据集方法			数组函数
合并数据			merge				array_merge
比较数据差集		diff				array_diff
交换数组中的键和值	flip				array_flip
比较数组交集		intersect			array_intersect
返回键名			keys				array_keys
最后元素出栈		pop					array_pop
数组迭代简化		reduce				array_reduce
数据反序			reverse				array_reverse
首个元素出栈		shift				array_shift
开头插入元素		unshift				array_unshift
元素回调			each				---
过滤元素			filter				array_filter
返回指定列			column				array_column
元素排序			sort				array_sort
打乱元素			shuffle				shuffle
截取部分元素		slice				array_slice
元素分割			chunk				array_chunk
转换数组			toArray				---


//自定义的数据集对象：

一般自定义的数据集对象建议继承think\model\Collection，然后在模型中设置resultSetType属性值为自定义查询类的类名。
<? php
protected $resultSetType = 'app\common\Collection';
?>

//获取器

获取器的作用是在获取数据的字段值后自动进行处理，

<?php
//用处1：对状态值进行转换。利用了索引数组（key-value/键值对）引用数组索引输出数组值的特性。
class User extends Model 
{
    public function getStatusAttr($value)
    {
        $status = [-1=>'删除',0=>'禁用',1=>'正常',2=>'待审核'];
        return $status[$value];
    }
}

?>

<!--//  HY 2018/6/21 -->


<!--//  HY 2018/6/21 -->
//模型关联

//关联关系
关联关系通常有一个参照模型，即是主模型（或者当前模型），关联关系对应的模型就是关联模型，关联关系是指定义在主模型中的关联，有些关联关系还会涉及到一个中间表的概念，但中间表不一定需要存在具体的模型。

主模型和关联模型之间通常是通过某个外键进行关联，而这个外键的命名系统会有一个约定规则，通常是主模型名称+_id，尽量遵循这个约定会给关联定义带来很大简化。

从面向对象的角度来看关联的话，模型的关联其实应该是模型的某个属性。框架选择了方法定义而不是属性定义的方式，每个关联属性其实是对应了一个模型的关联方法，这个关联属性和模型的数据一样是动态的，并非模型类的实际属性。

例如
<?php
namespace app\index\model;

use think\Model;

class User extends Model
{
	public function profile()
    {
    	return $this->hasOne('Profile');
    }
}
?>

上述代码中的关联属性就是在User模型类中定义了一个profile方法。

关联的使用：
<?php
// 用户的档案
$user->profile;
// 用户的档案属性中的手机资料
$user->profile->mobile;
?>
$user本身是一个`User`模型的对象实例，而`$user->profile`则是一个`Profile`模型的对象实例，所以具备模型的所有特性而不是一个数组，包括进行`Profile`模型的CURD操作和业务逻辑执行，`$user->profile->mobile`则表示获取Profile模型对象实例的mobile数据，包括下面的操作也是有效的。
<?php
// 对查询出来的关联模型进行数据更新
$user->profile->email = 'thinkphp@qq.com'
$user->profile->save();
?>
这种关联关系使用Db类是无法完成的，所以这个使命是由模型来完成的，模型的关联用法很好的解决了关联的对象化，支持大部分的关联场景和需求。

当我们访问`User`模型对象实例的`profile`属性的时候，其实就是调用了`profile`方法来完成关联查询。当获取一个模型的属性的时候会触发模型的获取器，而当获取器在没有检测到模型有对应属性的时候就会检查是否存在关联方法定义（对于关联方法的判断很简单，关联方法返回的是一个`think\model\Relation`对象），如果存在则调用对应关联类的`getRelation`方法。


//定义关联
关联方法的定义最关键是要搞清楚具体应该使用何种关联关系，其次是掌握不同的关联关系的定义方法和参数。
5.0版本支持的关联关系包括下面七种，
模型方法		关联类型
hasOne			一对一HAS ONE
belongsTo		一对一BELONGS TO
hasMany			一对多 HAS MANY
hasManyThrough	远程一对多 HAS MANY THROUTH
belongsToMany	多对多 BELONGS TO MANY
morphMany		多态一对多 MORPH MANY
morphTo			多态 MORPH TO
关联方法的第一个参数就是要关联的模型名称，也就是说当前模型的关联模型必须也是已经定义的一个模型。

//hasOne关联
用法：hasOne('关联模型','外键','主键');除了关联模型外，其它参数都是可选。
	- 关联模型（必须）：模型名或者模型类名
	- 外键：在关联模型中的字段名，默认的外键规则是当前模型名（不含命名空间，下同）+_id ，例如user_id
    - 主键：当前模型主键，一般会自动获取也可以指定传入
	
//belongsTo关联
用法：belongsTo('关联模型','外键','关联表主键');除了关联模型外，其它参数都是可选。
    - 关联模型（必须）：模型名或者模型类名
    - 外键：当前模型外键，默认的外键名规则是关联模型名+_id
    - 关联主键：关联模型主键，一般会自动获取也可以指定传入
	
//hasMany关联
用法：hasMany('关联模型','外键','主键');除了关联模型外，其它参数都是可选。
    - 关联模型（必须）：模型名或者模型类名
    - 外键：关联模型外键，默认的外键名规则是当前模型名+_id
    - 主键：当前模型主键，一般会自动获取也可以指定传入
	
//hasManyThrough
用法：hasManyThrough('关联模型','中间模型','外键','中间表关联键','主键');
    - 关联模型（必须）：模型名或者模型类名
    - 中间模型（必须）：模型名或者模型类名
    - 外键：默认的外键名规则是当前模型名+_id
    - 中间表关联键：默认的中间表关联键名的规则是中间模型名+`_id`
    - 主键：当前模型主键，一般会自动获取也可以指定传入

//belongsToMany关联
用法：belongsToMany('关联模型','中间表','外键','关联键');
    - 关联模型（必须）：模型名或者模型类名
    - 中间表：默认规则是当前模型名+_+关联模型名 （注意，在V5.0.8版本之前需要添加表前缀）
    - 外键：中间表的当前模型外键，默认的外键名规则是关联模型名+`_id`
    - 关联键：中间表的当前模型关联键名，默认规则是当前模型名+`_id`

//morphMany关联
用法：morphMany('关联模型','多态字段','多态类型');
    - 关联模型（必须）：模型名或者模型类名
    - 多态字段：多态字段信息定义包含两种方式，字符串的话表示多态字段的前缀，数组则表示实际的多态字段
    - 多态类型：默认是当前模型名
数据表的多态字段一般包含两个字段：多态类型和多态主键。
如果多态字段使用字符串例如morph，那么多态类型和多态主键字段分别对应morph_type和morph_id，如果用数组方式定义的话，就改为['morph_type','morph_id']即可。

//morphTo关联
用法：morphTo('多态字段','多态类型别名（数组）');
    - 多态字段：定义和`morphMany`一致
    - 多态类型别名：用于设置特殊的多态类型（比如用数字标识的多态类型）


//相对关联关系	
两个模型之间因为参照模型的不同就会产生相对的但不一定相同的关联关系，并且相对的关联关系只有在需要调用的时候才需要定义，下面是每个关联类型的相对关联关系对照：
类型		关联关系		相对的关联关系
一对一		hasOne			belongsTo
一对多		hasMany			belongsTo
多对多		belongsToMany	belongsToMany
远程一对多	hasManyThrough	不支持
多态一对多	morphMany		morphTo

除此之外，关联定义的几个要点必须了解：

    * 关联方法必须使用驼峰法命名；
    * 关联方法一般无需定义任何参数；
    * 关联调用的时候驼峰法和小写+下划线都支持(推荐使用后者)；
    * 关联字段设计尽可能按照规范可以简化关联定义；
    * 关联方法定义可以添加额外查询条件；
	

<?php
//定义关联，一般不需要使用命名空间，会自动使用当前模型的命名空间，如果不同请使用完整命名空间定义，例如：
namespace app\index\model;

use think\Model;

class User extends Model
{
	public function profile()
    {
    	// Profile模型和当前模型的命名空间不一致
    	return $this->hasOne('app\model\Profile');
    }
}


?>

//关联操作，关联方法
关联操作经常会涉及到几个重要的方法，也是关联操作的基础，掌握了这几个方法对于掌握关联（尤其是关联查询）有很大的帮助，包括：
方法名			作用							所属类/对象
`relation`		关联查询						Query类(链式方法，在查询方法之前调用)
`with`			关联预载入						Query类(链式方法，在查询方法之前调用)
`withCount`		关联统计（`V5.0.5+`）			Query类(链式方法，在查询方法之前调用)
`load`			关联延迟预载入（`V5.0.5+`）		数据集对象
`together`		关联自动写入（`V5.0.5+`）		模型类

`relation`和`with`方法的主要区别:`relation`是单纯的关联查询。
比如：查询一个用户列表，然后需要关联查询用户的档案数据。
1. 使用`relation`方法：先查询用户列表数据，每个用户再单纯查询档案数据。如果用户列表数据有10个，那么就会产生11次查询。

2. 使用`with`方法:最终查询出来的关联数据是一样的，但由于with查询使用的是预载入查询，因此实际只会产生2次查询。

3.使用`load`方法则更先进，先查询出用户列表，然后在需要关联数据的时候使用load方法获取关联数据，尤其适合动态关联的情况，最终也是两次查询，因此称为延迟预载入。

由于模型关联的对象化封装机制的优势，其实`relation`方法基本上很少被用到，而是使用关联惰性查询及关联方法的自定义查询来替代。
最常用的莫过于`with`方法，因为最常用因此被内置到模型类的`get`和`all`方法的第二个参数了，我们后面对with方法的用法说明也均适用于get和all方法的第二个参数。

`withCount`用于在不获取关联数据的情况下提供关联数据的统计，在查询一对多或者多对多关联的时候才需要使用。

`load`方法则适用于在数据集的延迟预载入关联查询（对于默认的数据集查询类型，系统提供了一个`load_relation`助手函数，作用是等效的）。

`together`方法用于一对一的关联自动写入操作（包括新增、更新和删除），提供了更简单的关联写入机制。

虽然作用不尽相同，但这几个方法的使用方法都是类似的，这四个方法都只有一个参数，参数类型包括字符串和数组，并且数组方式还支持索引数组以方便完成关联的自定义查询。


//关联方法，
例如：
<?php
// 查询用户的Profile关联数据
$users = $user->relation('profile')->select();
// 查询用户的Book关联数据
$users = $user->relation('books')->select();
?>
关联查询的方法返回的依然是包含User对象实例的数据集，`relation`方法设定的关联查询结果只是数据集中的User模型对象实例的某个关联属性。

`relation`方法传入的字符串就是关联定义的方法名而不是关联模型的名称，由于模型方法名使用的都是驼峰法规范，假设定义了一个名为`userBooks`的关联方法的话，`relation`方法可以使用两种方式的关联查询：
<?php
//方式1: 驼峰法的关联方法定义
$users = $user->relation('userBooks')->select();

//方式2: 下划线的关联方法定义
$users = $user->relation('user_books')->select();

?>
第一种传入的是实际的驼峰法关联方法名`userBooks`，第二种是传入小写和下划线的转化名称`user_books`，两种关联查询用法都会实际定位到关联方法名称userBooks，所以**关联方法定义必须使用驼峰法**。

在获取关联查询数据的时候，同样可以支持两种方式：
<?php
//方式1
foreach ($users as $user) {
    dump($user->userBooks);
}

//方式2
foreach ($users as $user) {
	dump($user->user_books);
}
?>

//关联查询条件，定义关联查询条件
默认情况下，关联方法获取的是满足关联条件的所有数据，如果需要自定义关联查询条件的话，可以使用
<?php
// 使用自定义关联查询
$user->relation(['books' => function ($query) {
    $query->where('title', 'like', '%thinkphp%');
}])->select();
?>

表示查询该用户写的标题中包含`thinkphp`的书籍，闭包中不仅仅可以使用查询条件，还可以支持其它的链式方法，比如对关联数据进行排序和指定字段：
<?php
// 使用自定义关联查询
$user->relation(['books' => function ($query) {
    $query
        ->field('id,name,title,pub_time,user_id')
        ->order('pub_time desc')
        ->whereTime('pub_time', 'year');
}])->select();
?>

如果使用field方法指定查询字段，务必包含你的当前模型的主键以及关联模型的关键键，否则会导致关联查询失败。

关联方法可以同时指定多个关联，即使是不同的关联类型，使用：
<?php
//方式1：查询用户的Profile和Book关联数据
$users = $user->relation('profile,books')->select();

//方式2：数组方式查询用户的Profile和Book关联数据
$users = $user->relation(['profile','books'])->select();
?>
一般使用数组的话，主要需要使用闭包进行自定义关联查询的情况，否则用逗号分割的字符串就可以了。

//关联查询

有两种方式进行关联的数据获取：
1.关联预查询：`relation`方法
<?php
// 指定User模型的profile关联
$user = User::relation('profile')->find(1);
// profile关联属性也是一个模型对象实例
dump($user->profile);
?>
`relation`方法中传入关联（方法）名称即可（多个可以使用逗号分割的字符串或者数组）。这种方式，无论是否最终获取`profile`属性，都会事先进行关联查询，因此称为关联预查询。

如果关联数据不存在，一对一关联返回的是null，一对多关联的话返回的是空数组或空数据集对象。

2.关联延迟查询
出于性能考虑，通常我们选择关联延迟查询的方式。
<?php
// 不需要指定关联
$user = User::get(1);
// 获取profile属性的时候自动进行关联查询
dump($user->profile);

?>
这种方式下的关联查询是惰性的，只有在获取关联属性的时候才会实际进行关联查询，因此称之为关联延迟查询。

关联属性的名称一般就是关联（定义）方法的名称，但同时也支持驼峰关联方法的小写+下划线转化名称。


//定义关联查询条件
模型的关联方法除了会自动在关联获取的时候自动调用外，仍然可以作为查询构造器的链式操作来对待，以完成额外的附加条件或者其它自定义查询（一对多的关联关系时候比较多见类似场景），例如`User`模型定义了一个`articles`的`hasMany`关联：
<?php
namespace app\index\model;

use think\Model;

class User extends Model
{
	public function articles()
    {
    	return $this->hasMany('Article');
    }
}
?>

普通的关联查询，获取的是全部的关联数据：
<?php
$user = User::get(1);
$articles = $user->articles;
?>

对关联数据进行筛选，例如需要查询用户发表的标题里面包含`think`的文章，并且按照`create_time`倒序排序：
<?php
$user     = User::get(1);
$articles = $user->articles()
    ->where('title', 'like', '%think%')
    ->order('create_time desc')
    ->select();

?>
调用`articles()`关联方法的动作有下面几个：
1. 相当于切换当前模型到关联模型对象（Article）；
2. 并且会自动传入关联条件（user_id = 1）；

如果是一对多或者多对多关联，并且希望自主条件查询关联数据的话请参考该方式。

可以直接在定义关联的时候添加额外条件，而不是在外部查询的时候指定：
<?php
namespace app\index\model;

use think\Model;

class User extends Model
{
	public function articles()
    {
    	return $this->hasMany('Article')
          ->where('title', 'like', '%think%')
          ->order('create_time desc');
    }
}
?>

如果还需要在外部调用的时候追加额外条件，
<?php
//包含了关联方法里面定义的和额外追加条件的关联查询
$user     = User::get(1);
$articles = $user->articles()
    ->where('name', 'thinkphp')
    ->field('id,name,title')
    ->select();

?>

可以单独定义多个关联关系，各自独立使用互不影响。
<?php
namespace app\index\model;

use think\Model;

class User extends Model
{
	public function articles()
    {
    	return $this->hasMany('Article');
    }
    
	public function articlesLike($title)
    {
        return $this->hasMany('Article')
                    ->where('title', 'like', '%' . $title . '%')
                    ->field('id,name,title')
                    ->order('create_time desc');
    }    
}
?>
`articlesLike`方法就作为自定义关联查询专用，并且需要传入`title`参数，用法如下：
<?php
//正确用法
$user = User::get(1);
$articles = $user->articlesLike('think')
    ->select();

//错误用法	
$user = User::get(1);
$articles = $user->articlesLike;	
?>
带有参数的关联定义方法不能直接用于关联属性获取，只能用于链式关联自定义查询。

//关联约束
对于`hasMany`关联关系，系统提供了根据关联数据条件来查询当前模型数据的关联约束方法，包括`has`和`hasWhere`两个方法。

`has`方法主要用于查询关联数据的记录数来作为当前模型的查询依据，默认是存在一条数据即可。
<?php
// 查询有评论数据的文章
$list = Article::has('comments')->select();

// 查询评论超过3个的文章
$list = Article::has('comments', '>', 3)->select();
?>
//`has`方法的第二个参数支持`>`、`=`、`<=`、`<`、`>=`、第三个参数是一个整数。


`hasWhere`方法，用于需要复杂的关联查询约束条件：
<?php
// 查询评论状态正常的文章
$list = Article::hasWhere('comments', ['status' => 1])->select();

// 查询最近一周包含think字符的评论的文章(使用闭包查询，在闭包里面使用链式方法查询：)
$list = Article::hasWhere('comments', function ($query) {
    $query
        ->whereTime('create_time', 'week')
        ->where('content', 'like', '%think%');
})->select();

// 查询最近一周包含think字符的评论的文章,查询的关联模型字段同时存在当前模型和关联模型，需要加上关联模型的名称作为别名。
$list = Article::hasWhere('comments', function ($query) {
    $query
        ->whereTime('Comment.create_time', 'week')
        ->where('content', 'like', '%think%');
})->select();
?>
V5.0.5+版本开始，has也支持hasWhere的所有用法。


//关联预载入
关联查询的预查询载入功能，主要解决了N+1次查询的问题
<?php
//查询如果有3个记录，会执行4次查询
$list = User::all([1, 2, 3]);
foreach ($list as $user) {
    // 获取用户关联的profile模型数据
    dump($user->profile);
}

//使用with方法指定需要预载入的关联（方法），用法和relation方法类似。
$list = User::with('profile')->select([1, 2, 3]);
foreach ($list as $user) {
    // 获取用户关联的profile模型数据
    dump($user->profile);
}

//模型的get和all方法的第二个参数可以直接传入预载入参数
$list = User::all([1, 2, 3], 'profile');
foreach ($list as $user) {
    // 获取用户关联的profile模型数据
    dump($user->profile);
}
?>
使用关联预查询功能，对于一对一关联来说，默认只有一次查询，对于一对多关联的话，就变成2次查询，有效提高性能。关联的预载入查询不是惰性的，是连同数据查询一起完成的，但由于封装的合并查询，性能方面远远优于普通的关联惰性查询，所以整体的查询性能是非常乐观的。


//嵌套预载入
嵌套预载入指的是如果关联模型本身还需要进行关联预载入的话，可以在当前模型预载入查询的时候直接指定，理论上嵌套是可以任意级别的（但实际上估计不会有这么复杂的关联设计）。
<?php
//Profile模型还关联了一个名片模型（cards关联方法）
$list = User::all([1, 2, 3], 'profile.cards');
foreach ($list as $user) {
    // 获取用户关联数据
    dump($user->profile->cards);
}
?>
一对一关联的JOIN方式不支持嵌套预载入

//预载入条件限制
在预载入的时候通过闭包指定额外的条件限制，但不要在闭包里面执行任何的查询。
<?php
$list = User::with(['articles' => function ($query) {
    $query->where('title', 'like', '%think%')
        ->field('id,name,title')
        ->order('create_time desc');
}])->select([1, 2, 3]);

foreach ($list as $user) {
    // 获取用户关联的profile模型数据
    dump($user->profile);
}
?>
如果是一对一预载入查询的条件限制，注意`field`方法要改为`withField`方法，否则会产生字段混淆。

//延迟预载入
延迟预载入仅针对多个数据的查询，因为单个数据的查询用延迟预载入和关联惰性查询没有任何区别，所以不需要使用延迟预载入。
如果数据集查询返回的是数据集对象，可以调用数据集对象的`load`实现延迟预载入。

需要根据查询出来的数据来决定是否需要使用关联预载入，当然关联查询本身就能解决这个问题，因为关联查询是惰性的，不过用预载入的理由也很明显，性能具有优势。

<?php
// 查询数据集
$list = User::all([1, 2, 3]);

// 数据集对象的`load`方法实现延迟预载入
$list->load('cards');	

// 数据集查询返回的是数组，系统提供了一个load_relation助手函数实现延迟预载入
$list = load_relation($list, 'cards');

foreach ($list as $user) {
    // 获取用户关联的card模型数据
    dump($user->cards);
}

?>

//关联统计

使用`withCount`方法进行指定关联的统计。不需要获取关联数据，而只是希望获取关联数据的统计（关联统计仅针对一对多或者多对多的关联关系）。
<?php
$list = User::withCount('cards')->select([1, 2, 3]);
foreach ($list as $user) {
    // 获取用户关联的card关联统计
    echo $user->cards_count;
}

//关联统计进行条件过滤，可以使用闭包
$list = User::withCount(['cards' => function ($query) {
    $query->where('status', 1);
}])->select([1, 2, 3]);
foreach ($list as $user) {
    // 获取用户关联的card关联统计
    echo $user->cards_count;
}
?>

关联统计功能会在模型的对象属性中自动添加一个以“关联方法名+`_count`”为名称的动态属性来保存相关的关联统计数据。

一对一关联关系使用关联统计是无效的，一般可以用exists查询来判断是否存在关联数据。

//关联输出
关联属性的输出和模型的输出转换一样，使用模型的toArray方法可以同时输出关联属性（对象），
<?php
$user = User::get(1,'profile');
$data = $user->toArray();
dump($data);
$data = $user->toJson();
dump($data);
?>
对于使用了关联预载入查询和手动获取了关联属性（延迟关联查询）的情况，toArray和toJson方法都会包含关联数据。

还可以调用`visible`和`hidden`方法对当前模型以及关联模型的属性进行输出控制，
<?php
$user = User::get(1, 'profile');
$data = $user->hidden(['name', 'profile.email'])->toArray();
?>
上面的代码返回的`data`数据中不会包含用户模型的`name`属性以及关联`profile`模型的`email`属性。

<?php
$user = User::get(1, 'profile');
//隐藏多个关联属性：
$data = $user->hidden(['name', 'profile' => ['email', 'address']])->toArray();
?>
模型的`visible`方法（用于设置需要输出的属性）的使用和`hidden`一致。同时调用`visible`和`hidden`方法，`visible`优先。

<?php
$user = User::get(1, 'profile');
//同时调用`visible`和`hidden`方法，`visible`优先
$data = $user->visible(['profile' => ['email', 'sex']])->hidden(['name', 'profile' => ['email', 'address']])->toArray();
?>
上述代码中`profile`关联属性输出会包含`email`和`sex`。

可以在输出的时候追加关联属性，即使之前没有进行任何的关联查询。
<?php
$user = User::get(1);
$user->append(['profile'])->toArray();
?>
该例子在调用`toArray`方法的时候才会进行`profile`关联数据获取并转换输出。

对于数据集查询，如果返回类型是数据集对象仍然支持调用`visible`、`hidden`和`append`方法，如果不是数据集对象的话可以先用`collection`助手函数转换为数据集对象。
<?php
$users = User::all();
//数据集对象调用`hidden`方法
$data  = $users->hidden(['name', 'profile' => ['email', 'address']])
    ->toArray();

?>


<?php
//关联查询

?>


<!--//  HY 2018/6/21 -->


<!--  HY 2018/7/5 -->
//查询范围scope，模型高级用法
对于一些常用的查询条件，我们可以事先定义好，以便快速调用，这个事先定义的查询条件方法有一个统一的前缀scope，我们称之为查询范围。
模型的scope方法之后只能使用数据库查询方法而不能使用模型的方法。

<?php
//查询范围
namespace app\index\model;

use think\Model;

class User extends Model
{

    // email查询
    protected function scopeEmail($query)
    {
        $query->where('email', 'thinkphp@qq.com');
    }

    // status查询
    protected function scopeStatus($query)
    {
        $query->where('status', 1);
    }
}

//直接使用

$users = User::scope('email,status')->select();

//或者使用

$users = User::scope('email')->scope('status')->select();

?>

查询范围方法支持额外的参数。查询范围的方法的第一个参数必须是查询对象，并且支持多个额外参数。
<? php
//例如scopeEmail方法改为：

    // email查询
    protected function scopeEmail($query, $email = '')
    {
        $query->where('email', $email);
    }
?>

//全局查询范围base，
    

<!--//  HY 2018/7/5 -->