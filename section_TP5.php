
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

模型类的静态CURD操作其实都是内部自动实例化而已，所以说白了提供的这些静态操作方法只是对动态CURD操作方法的静态封装罢了。
至于静态方法的场景，主要是不想实例化或者不方便实例化的需求，而且支持变量的静态调用，例如：
<?php

//save方法的返回值不是自增主键的值（和Db的execute方法一样返回影响的记录数），要获取自增主键的值可以使用下面的方式：
$user        = new User;
$user->name  = 'thinkphp';
$user->email = 'thinkphp@qq.com';
$user->save();
// 获取用户的主键数据
echo $user->id;

$model = '\app\index\model\User';
$user  = $model::create([
    'name'  => 'thinkphp',
    'email' => 'thinkphp@qq.com',
]);
echo $user->id;
//create方法的返回值是User模型的对象实例，所以可以通过$user->id得到create后的id值。
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

进行条件查询时，
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

?>

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
// 查询JSON类型字段 （info字段为json类型）（mysql V5.0.1）
Db::table('think_user')->where('info$.email','thinkphp@qq.com')->find();
?>


// json与array

// 模型model（user.php）：设定“authority”字段为
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
//请求参数输出
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

//前端提交request的方式（我在jQuery常用）：
<html>

<script>
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

