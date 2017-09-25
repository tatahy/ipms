<?php
namespace app\search\controller;

use think\Request;
use think\Session;

use app\user\model\User as UserModel;
use app\user\model\Rolety as RoletyModel;
use app\issue\model\Issinfo as IssinfoModel;
use app\patent\model\Patinfo as PatinfoModel;

class IndexController extends \think\Controller
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
        }
        
        $pats= null;
        $patsnum = 0;
        
        $pros= null;
        $prosnum= 0;
                        
        $thes= null;
        $thesnum= 0;
                        
        $isses= null;
        $issesnum= 0;
        
        //--在index.html页面输出自定义信息的HTML代码块
        $destr= "请求方法:".$request->method()."</br>".
                "username:".$this->username."</br>";     
        
        $this->assign([
            //在index.html页面通过'destr'输出自定义的信息
            'destr'=>$destr,
            //在index.html页面通过'array'输出自定义的数组内容
            'array'=>$this->roles, 
            
            'home'=>$request->domain(),
            'username'=>$this->username,
            
            'today'=>date("Y-m-d"),
            //上个月的今天
            'lastday'=>date("Y-m-d",mktime(0,0,0,date("n")-1,date("j"),date("Y"))),
            
            'pats'=>$pats,
            'patsnum'=>$patsnum,
            
            'pros'=>$pros,
            'prosnum'=>$prosnum,
            
            'thes'=>$thes,
            'thesnum'=>$thesnum,
            
            'isses'=>$isses,
            'issesnum'=>$issesnum,
        
            ]);
        
        return View();
        //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';
    }
    
    public function searchresult(Request $request)
    {
        
        //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';
        //通过$log判断是否是登录用户，非登录用户退回到登录页面
        if(1!==$this->log){
            $this->error('未登录用户，请先登录系统');
        }else{
            $type=$request->param('type');
            $topic=$request->param('topic');
            $writer=$request->param('writer');
            $dept=$request->param('dept');
            
        }
        
        //根据$type的值得到不同的查询记录集
        switch($type){
            case 'project':
            
            break;
            
            case 'patent':
                $pattype=$request->param('pattype');
                $patstatus=$request->param('patstatus');
                $patdate=$request->param('patdate');
                $patdaterange=$request->param('patdaterange');
                $patstartdate=$request->param('patstartdate');
                $patenddate=$request->param('patenddate');
                
                switch($patdaterange){
                    
                    //$patstartdate=<$patdate
                    case 'geq':
                        //patent的查询语句
                        
                       
                       
                    break;
                    
                    //$patstartdate=<$patdate=<$patenddate
                    case 'between':
                        //patent的查询语句
                        
                      
                    break;
                    
                    //$patdate=<$patenddate
                    default:
                        //patent的查询语句
                        
                                                
                    break;
                 }
            
            break;
            
            case 'thesis':
            
            break;
            
            case 'issue':
            
            break;
            
            case 'all':
                $date=$request->param('date');//$date='submitdate'
                $daterange=$request->param('daterange');
                $startdate=$request->param('startdate');
                $enddate=$request->param('enddate');
                
                switch($daterange){
                    
                    //$startdate=<$date
                    case 'geq':
                        //project的查询语句
                        //$projects=
                        
                        //patent的查询语句
                        //$pats = PatinfoModel::where('topic', 'like', '%'.$topic.'%')
//                                    ->order('id desc')
//                                    ->select();
                                    
                        $pats = PatinfoModel::where(function ($query) use ($topic, $startdate) {
                                                $query->where('topic', 'like', '%'.$topic.'%')->where('submitdate', '>= time', $startdate);
                                            })
                                            ->where(function ($query) use ($writer, $dept) {
                                                $query->where('author', 'like', '%'.$writer.'%')->where('dept', 'like', '%'.$dept.'%');
                                            })
                                            ->order('submitdate desc')
                                            ->select();
                                    
                        $patsnum=count($pats);
                        
                        //分页代码？？
                        
                        
                        
                        //thesis的查询语句
                        
                        
                        //issue的查询语句
                        
                        
                        $pros= null;
                        $prosnum=0;
                        
                        $thes= null;
                        $thesnum=0;
                        
                        $isses= null;
                        $issesnum=0;
                    
                    break;
                    
                    //$startdate=<$date=<$enddate
                    case 'btw':
                        //project的查询语句
                        
                        
                        //patent的查询语句
                        $pats = PatinfoModel::where(function ($query) use ($topic, $startdate,$enddate,$date) {
                                                $query->where('topic', 'like', '%'.$topic.'%')->where($date, 'between time', [$startdate,$enddate]);
                                            })
                                            ->where(function ($query) use ($writer, $dept) {
                                                $query->where('author', 'like', '%'.$writer.'%')->where('dept', 'like', '%'.$dept.'%');
                                            })
                                            ->order("'".$date." desc'" )
                                            ->select();
                                    
                        $patsnum=count($pats);
                        
                        //分页代码？？
                        
                        //thesis的查询语句
                        
                        
                        //issue的查询语句
                        
                        
                        $pros= null;
                        $prosnum=0;
                        
                        $thes= null;
                        $thesnum=0;
                        
                        $isses= null;
                        $issesnum=0;
                    
                    break;
                    
                    //$date<=$enddate
                    default:
                        //project的查询语句
                        
                        
                        //patent的查询语句
                        $pats = PatinfoModel::where(function ($query) use ($topic, $enddate,$date) {
                                                $query->where('topic', 'like', '%'.$topic.'%')->where($date, '<= time', $enddate);
                                            })
                                            ->where(function ($query) use ($writer, $dept) {
                                                $query->whereOr('author', 'like', '%'.$writer.'%')->where('dept', 'like', '%'.$dept.'%');
                                            })
                                            ->order('submitdate desc')
                                            ->select();
                                    
                        $patsnum=count($pats);
                        
                        //thesis的查询语句
                        
                        
                        //issue的查询语句
                        
                        
                        $pros= null;
                        $prosnum=0;
                        
                        $thes= null;
                        $thesnum=0;
                        
                        $isses= null;
                        $issesnum=0;
                    
                    break;
                      
                }
            break;
            
            
            default:
                $pats= null;
                $patsnum=0;
                
                $pros= null;
                $prosnum=0;
                
                $thes= null;
                $thesnum=0;
                
                $isses= null;
                $issesnum=0;
            
            break;
        }
        
        
        
        
        //--在index.html页面输出自定义信息的HTML代码块
        $destr= "请求方法:".$request->method()."</br>".
                "username:".$this->username."</br>";     
        
        $this->assign([
            //在index.html页面通过'destr'输出自定义的信息
            'destr'=>$destr,
            //在index.html页面通过'array'输出自定义的数组内容
            'array'=>$this->roles, 
            
            'home'=>$request->domain(),
            'username'=>$this->username,
            
            'pats'=>$pats,
            'patsnum'=>$patsnum,
            
            'pros'=>$pros,
            'prosnum'=>$prosnum,
            
            'thes'=>$thes,
            'thesnum'=>$thesnum,
            
            'isses'=>$isses,
            'issesnum'=>$issesnum,
            
            'today'=>date("Y-m-d"),
            //上个月的今天
            'lastday'=>date("Y-m-d",mktime(0,0,0,date("n")-1,date("j"),date("Y"))),
            ]);
        
        return View('index/showresult');
        //return View('index/index');
        
    }
    
    public function showresult(Request $request)
    {
        //通过$log判断是否是登录用户，非登录用户退回到登录页面
        if(1!==$this->log){
            $this->error('未登录用户，请先登录系统');
        }
        
        //--在index.html页面输出自定义信息的HTML代码块
        $destr= "请求方法:".$request->method()."</br>".
                "username:".$this->username."</br>";     
        
        $this->assign([
            //在index.html页面通过'destr'输出自定义的信息
            'destr'=>$destr,
            //在index.html页面通过'array'输出自定义的数组内容
            'array'=>$this->roles, 
            
            'home'=>$request->domain(),
            'username'=>$this->username,
            
            'today'=>date("Y-m-d"),
            //上个月的今天
            'lastday'=>date("Y-m-d",mktime(0,0,0,date("n")-1,date("j"),date("Y"))),
            
            'pats'=>$pats,
            'patsnum'=>$patsnum,
        
            ]);
        
        return View();
        //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';
    }
}
