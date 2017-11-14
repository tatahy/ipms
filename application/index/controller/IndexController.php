<?php
namespace app\index\controller;

use think\Request;
use think\Session;

use app\common\validate\Ipvalidate;
use app\user\model\User as UserModel;
use app\user\model\Rolety as RoletyModel;
use app\patent\model\Patinfo as PatinfoModel;

class IndexController extends \think\Controller
{
    public function index(Request $request)
    {
        //'username'和'pwd'的来源：session或初次登录时表单POST提交
        if(!empty($request->post('username'))){
            $username=$request->post('username');
        }else{
            $username=Session::get('username');
        }
        
        if(!empty($request->post('pwd')) ){
            $pwd=md5($request->post('pwd'));
        }else{
            $pwd=Session::get('pwd');
        }
        
        $log=Session::get('log');       
       
        //--在index.html页面输出自定义信息的HTML代码块
        $destr= "请求方法:".$request->method()."</br>".
                "username:".$username."</br>".
                //"pwd:".sizeof($pwd);
                "pwd:".$pwd."</br>".
                "log:".$log."</br>";
                //"session:".dump($request->session());
        //--!
        
        $data=[
            'name'=>$username,
            'pwd'=>$pwd,
        ];
        
        //使用自定义的validate类“Ipvalidate”进行浏览器端验证，类文件目录：application\common\validate
        //也可直接在页面利用jQuery进行验证；<input type="text" required>
        $result=$this->validate($data,'Ipvalidate.login');
        if(true!==$result){
            //验证失败输出提示错误信息
            $this->error($result);
            //返回登录界面
            $this->assign([
            'home'=>$request->domain(),
            ]);
            return view('index/login'); 
        }
        
        //通过浏览器端验证后再在数据库中查询是否有相应的用户存在,
        //连接数据库,利用模型对象查询有效的$username，$pwd在数据库中是否存在并得到其在系统中的所有role
        $user = UserModel::where('username',$username)
                            ->where('pwd',$pwd)
                            ->select();
                                   
        //不存在，同验证失败的处理
        if(empty($user)){
            $this->error('登录失败，用户名或密码错误。');
        }else{
        //存在，则显示index.html页面，$username,$pwd,$log=1,所有的role都放入session
         //通过rolied找出对应的role
        for($i=0;$i<sizeof($user);$i++){
            $roles[$i]=RoletyModel::where('id',$user[$i]['rolety_id'])->find();
            $role[$i]=$roles[$i]['name'];
            //$dept=$user[$i]['dept'];
        }
        Session::set('pwd',$pwd);
        Session::set('username',$username);
        Session::set('log',1);
        Session::set('role',$role);
        Session::set('dept',$user[0]['dept']);
        
        //patent数据,使用模型Patinfo
        $pats = new PatinfoModel;  
        
        //利用模型对象得到状态status"="新增"）的patent总数
        $numpatadd=$pats->where('status','新增')->count();
        
        //利用模型对象得到状态status"="申报"，'返回修改'）的patent总数
        $numpatapp=$pats->where('status',['=','申报'],['=','返回修改'],'or')->count();
        
        //利用模型对象得到状态status='授权'）的patent总数
        $numpataut=$pats->where('status','授权')->count();
        
        //利用模型对象得到状态status"='续费授权'，'续费中'）的patent总数
        $numpatren=$pats->where('status',['=','续费授权'],['=','续费中'],'or')->count();
        
        $this->assign([
            //在index.html页面通过'destr'输出自定义的信息
            'destr'=>$destr."</br>",
            //在index.html页面通过'array'输出自定义的数组内容
            'array'=>$role, 
            
            'roles'=>$roles,                           
            'home'=>$request->domain(),
            'username'=>$username, 
            
            //patent数据
            'numpatadd'=>$numpatadd,
            'numpatapp'=>$numpatapp,
            'numpataut'=>$numpataut,
            'numpatren'=>$numpatren,
            ]);
            return view();
            
        }
        
    }
    
    //修改application/config.php的设置将“默认操作”由“index”改为“login”
     public function login(Request $request)
    {
                        
        $this->assign([
        'home'=>$request->domain(),
       
        ]);
                
        return view();
    
    }
    
    
     public function logout(Request $request)
    {
        Session::clear();
        Session::destroy();
        $this->success('安全退出系统','index/login');
                                
    }
    

}
