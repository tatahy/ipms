<?php
namespace app\user\controller;

use think\Request;
use think\Session;

use app\user\model\User as UserModel;
use app\user\model\Rolety as RoletyModel;
use app\issue\model\Issinfo as IssinfoModel;
use app\patent\model\Patinfo as PatinfoModel;

class ContentController extends \think\Controller
{
    //用户名
    private $username = null;
    //用户密码
    private $pwd = null;
    //用户登录状态
    private $log = null;
    //用户角色
    private $roles=array();
    //用户所在部门
    private $dept = null;
    
    // 初始化
    protected function _initialize()
    {
        $this->username=Session::get('username');
        $this->pwd=Session::get('pwd');
        $this->log=Session::get('log');
        $this->roles=Session::get('role');
        $this->dept=Session::get('dept');
    }
    
    public function index(Request $request)
    {
        //通过$log判断是否是登录用户，非登录用户退回到登录页面
        if(1!==$this->log){
            $this->error('未登录用户，请先登录系统');
        }else{
            $role=$request->param('role');
            $type=$request->param('type');
            $active=$request->param('active');
            
            //$listrows接收页面传来的分页时每页显示的记录数，初始值为10
            if(!empty($request->param('listrows'))){
                $listrows=$request->param('listrows');
            }else{
                $listrows=10;
            }
        }
        
        //确认登录人的角色是已有的。
        $n=0;
        foreach($this->roles as $value){
            if($value==$role)$n=1;
        }
        
        if($n){
            
            switch($type){
                case 1:
                    $typename="项目";
                    
                break;
                            
                case 2:
                    $typename="专利";
                    //实例化PatinfoModel模型类
                    $pats = new PatinfoModel;
                    
                    switch($role){
                        case 'maintainer':
                            
                            //"待处理"
                            $num_maintainer1=$pats->where('status','新增')
                                                    ->count();
                            //分页,每页$listrows条记录
                    		$maintainer1 = $pats->where('status','新增')
                                                ->order('submitdate', 'desc')
                                                ->paginate($listrows,false,['type'=>'bootstrap','var_page'=>'pagemaintainer1',]);             
                            // 获取分页显示
                            $pagemaintainer1 = $maintainer1->render();
                            //利用用模型关联，查出$iss_topic 
                            //$iss_topic=$pats->issinfo->topic;                     
                            
                            //"审批中"
                            $num_maintainer2=$pats->where('status',['=','申报'],['=','续费中'],'or')
                                                    ->count();
                            //分页,每页$listrows条记录
                            $maintainer2=$pats->where('status',['=','申报'],['=','续费中'],'or')
                                            ->order('status asc')
                                            ->order('pattype asc')
                                            ->paginate($listrows,false,['type'=>'bootstrap','var_page'=>'pagemaintainer2',]); 
                            // 获取分页显示
                            $pagemaintainer2 = $maintainer2->render();   
                            
                            //"审批结果"
                            $num_maintainer3=$pats->where('status',['=','授权'],['=','驳回'],['=','放弃'],['=','续费授权'],['=','返回修改'],'or')
                                                    ->count();
                            //分页,每页$listrows条记录
                            $maintainer3=$pats->where('status',['=','授权'],['=','驳回'],['=','放弃'],['=','续费授权'],['=','返回修改'],'or')
                                            ->order('status asc')
                                            ->order('authrejectdate desc')
                                            //->order('pattype desc')
                                            ->paginate($listrows,false,['type'=>'bootstrap','var_page'=>'pagemaintainer3',]); 
                            // 获取分页显示
                            $pagemaintainer3 = $maintainer3->render(); 
                            
                        break;
                        
                        case 'operator':
                            //"待处理"
                            $num_maintainer1=$pats->where('status',['=','填报'],['=','返回修改'],'or')
                                                    ->where('executer',$this->username)
                                                    ->count();
                            //分页,每页$listrows条记录
                    		$maintainer1 = $pats->where('status',['=','填报'],['=','返回修改'],'or')
                                                ->where('executer',$this->username)
                                                ->order('addnewdate', 'desc')
                                                ->paginate($listrows,false,['type'=>'bootstrap','var_page'=>'pagemaintainer1',]);             
                            // 获取分页显示
                            $pagemaintainer1 = $maintainer1->render();
                            //利用用模型关联，查出$iss_topic 
                            //$iss_topic=$pats->issinfo->topic;                     
                            
                            //"审批中"
                            $num_maintainer2=$pats->where('status',['=','新增'],['=','申报'],'or')
                                                    ->where('executer',$this->username)
                                                    ->count();
                            //分页,每页$listrows条记录
                            $maintainer2=$pats->where('status',['=','新增'],['=','申报'],'or')
                                            ->where('executer',$this->username)
                                            ->order('applydate asc')
                                            ->order('pattype asc')
                                            ->paginate($listrows,false,['type'=>'bootstrap','var_page'=>'pagemaintainer2',]); 
                            // 获取分页显示
                            $pagemaintainer2 = $maintainer2->render();   
                            
                            //"审批结果"
                            $num_maintainer3=$pats->where('status',['=','授权'],['=','驳回'],['=','放弃'],['=','续费'],'or')
                                                    ->where('executer',$this->username)
                                                    ->count();
                            //分页,每页$listrows条记录
                            $maintainer3=$pats->where('status',['=','授权'],['=','驳回'],['=','放弃'],['=','续费'],'or')
                                            ->where('executer',$this->username)
                                            ->order('status asc')
                                            ->order('authrejectdate desc')
                                            //->order('pattype desc')
                                            ->paginate($listrows,false,['type'=>'bootstrap','var_page'=>'pagemaintainer3',]); 
                            // 获取分页显示
                            $pagemaintainer3 = $maintainer3->render(); 
                            
                        break;
                        
                        default:
                            
                            
                        break;
                        
                    }
                    
                    
                    //利用正则表达式匹配出分页所在选项卡<li>,<div>标签的id所含关键字存入$pagex
                    //分页页码存入$pageXnum
                    //上述2类变量值需传入模板文件，用jQuery实现在选中分页所在选项卡内显示内容和连续编号值     
                    $str1=$request->url();
                    preg_match("/page\w{3,}/i",$str1,$matches);
                    if(!empty($matches[0])){
                        $pagex=$matches[0];
                        
                        switch($pagex){
                            case "pagemaintainer1":
                                $pagemaintainer1num=$request->param($pagex);
                                $pagemaintainer2num=1;
                                $pagemaintainer3num=1;
                                $active=1;
                                
                            break;
                            case "pagemaintainer2":
                                $pagemaintainer1num=1;
                                $pagemaintainer2num=$request->param($pagex);
                                $pagemaintainer3num=1;
                                $active=2;
                			break;
                            case "pagemaintainer3":
                                $pagemaintainer1num=1;
                                $pagemaintainer2num=1;
                                $pagemaintainer3num=$request->param($pagex);
                                $active=3;
                            break;

                            default:
                            break;
            	       }
                    }else{
                        $pagex='pagemaintainer'.$active;
                        $pagemaintainer1num=1;
                        $pagemaintainer2num=1;
                        $pagemaintainer3num=1;
                    }                         
                 
                    
                break;
                            
                case 3:
                    $typename="论文";
                break;
                            
                default:
                    $num_maintainer1=0;
                    $num_maintainer2=0;
                    $num_maintainer3=0;
                break;   
        }
            
             
            
            switch($role){
                case"writer":
                    $rolename="撰写人";
                break;
                        
                case"reviewer":
                    $rolename="审查人";
                break;
                        
                case"formchecker":
                    $rolename="形式审查人";
                break;
                        
                case"financialchecker":
                    $rolename="财务审查人";
                break;
                        
                case"approver":
                    $rolename="批准人";
                break;
                        
                case"maintainer":
                    $rolename="维护人";
                break;
                        
                case"operator":
                   $rolename="执行人";
                break;
                        
                default:
                break;
                   
            }
        }else{
            $this->error('未授权用户，请先登录系统');
        }
        
        $destr= "请求方法:".$request->method()."<br/>".
                "username:". $this->username."<br/>".
                "pwd:".$this->pwd."<br/>".
                "log:".$this->log; 
        
        $this->assign([
        //在usercenter.html页面输出自定义的信息
        //在index.html页面通过destr输出自定义的信息
        'destr'=>$destr."</br>",
        //在index.html页面通过array输出自定义的数组内容
        'array'=>$this->roles, 
        
        'home'=>$request->domain(),
        'username'=>$this->username,
        'role'=>$role,
        'rolename'=>$rolename,
        'type'=>$type,
        'typename'=>$typename,
        'active'=>$active,
        
        'maintainer1'=>$maintainer1,
        'maintainer2'=>$maintainer2,
        'maintainer3'=>$maintainer3,
        
        'num_maintainer1'=>$num_maintainer1,
        'num_maintainer2'=>$num_maintainer2,
        'num_maintainer3'=>$num_maintainer3,
        
        'pagex'=>$pagex,
        'listrows'=>$listrows,
        
        'pagemaintainer1'=>$pagemaintainer1,
        'pagemaintainer2'=>$pagemaintainer2,
        'pagemaintainer3'=>$pagemaintainer3,
        
        'pagemaintainer1num'=>$pagemaintainer1num,
        'pagemaintainer2num'=>$pagemaintainer2num,
        'pagemaintainer3num'=>$pagemaintainer3num,
        
        ]);
        return view();
       
    }
    
    //patent
    public function patent(Request $request)
    {
        //通过$log判断是否是登录用户，非登录用户退回到登录页面
        if(1!==$this->log){
            $this->error('未登录用户，请先登录系统');
        }
        
        
        
        return '<div style="padding: 24px 48px;"><h1>:)</h1><p>IPMS V3<br/></p><span style="font-size:22px;"></span></div>';
    }
    
}
