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
        
        if($request->param('type')){
            $type=$request->param('type');
            $topic=$request->param('topic');
            $writer=$request->param('writer');
            $dept=$request->param('dept');
            $daterange=$request->param('daterange');
            $startdate=$request->param('startdate');
            $enddate=$request->param('enddate');
            
            $pattype=$request->param('pattype');
            $patstatus=$request->param('patstatus');
            $patdate=$request->param('patdate');
            $patdaterange=$request->param('patdaterange');
            $patstartdate=$request->param('patstartdate');
            $patenddate=$request->param('patenddate');
        }else{
            $type='all';
            $topic=null;
            $writer=$this->username;
            $dept=$this->dept;
            $daterange='leq';
            //上个月的今天
            $startdate=date("Y-m-d",mktime(0,0,0,date("n")-1,date("j"),date("Y")));
            //今天
            $enddate=date("Y-m-d");
            
            $pattype='不限';
            $patstatus='不限';
            $patdate='submitdate';
            $patdaterange=$daterange;
            $patstartdate=$startdate;
            $patenddate=$enddate;
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
            
            
            'type'=>$type,
            'topic'=>$topic,
            'writer'=>$writer,
            'dept'=>$dept,
            'daterange'=>$daterange,
            'startdate'=>$startdate,
            'enddate'=>$enddate,
            
            'pats'=>$pats,
            'patsnum'=>$patsnum,
            'pattype'=>$pattype,
            'patstatus'=>$patstatus,
            'patdate'=>$patdate,
            'patdaterange'=>$patdaterange,
            'patstartdate'=>$patstartdate,
            'patenddate'=>$patenddate,
            
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
            //页面../index/index/index.html,header处post来的‘searchword’、‘htype’
            $searchword=$request->param('searchword');
            $htype=$request->param('htype');
            
            //页面../search/index/index.html,post来的数据
            $type=$request->param('type');
            $topic=$request->param('topic');
            $writer=$request->param('writer');
            $dept=$request->param('dept');
            $daterange=$request->param('daterange');
            $startdate=$request->param('startdate');
            $enddate=$request->param('enddate');
            $date=$request->param('date');//$date='submitdate'
            
            $pattype=$request->param('pattype');
            $patstatus=$request->param('patstatus');
            $patdate=$request->param('patdate');
            $patdaterange=$request->param('patdaterange');
            $patstartdate=$request->param('patstartdate');
            $patenddate=$request->param('patenddate');
            
        }
        
        //处理页面../index/index/index.html,header处post来的‘searchword’、‘htype’
        if(!empty($searchword)){           
            $type=$htype;
            $topic=$searchword;
            $writer=$searchword;
            $dept=$searchword;
            $daterange='leq';
            $startdate=date("Y-m-d",mktime(0,0,0,date("n")-1,date("j"),date("Y")));
            $enddate=date("Y-m-d");
            
            $pattype='不限';
            $patstatus='不限';
            $patdate='submitdate';
            $patdaterange=$daterange;
            $patstartdate=$startdate;
            $patenddate=$enddate;
                       
            
            //project的查询语句
            $pros= null;
            $prosnum=0;
                        
            //issue的查询语句
            $isses= IssinfoModel::where(function ($query) use ($topic, $enddate) {
                                    $query->where('topic', 'like', '%'.$topic.'%')->where('submitdate', '<= time', $enddate);
                                  })
                                  ->whereOr(function ($query) use ($writer, $dept) {
                                    $query->where('writer', 'like', '%'.$writer.'%')->whereOr('dept', 'like', '%'.$dept.'%');
                                  })
                                  ->order('submitdate desc')
                                  ->select();
            $issesnum=count($isses);
                        
                        
            //patent的查询语句
            $pats = PatinfoModel::where(function ($query) use ($topic, $enddate) {
                                    $query->where('topic', 'like', '%'.$topic.'%')->where('submitdate', '<= time', $enddate);
                                 })
                                 ->whereOr(function ($query) use ($writer, $dept) {
                                    $query->where('author', 'like', '%'.$writer.'%')->whereOr('dept', 'like', '%'.$dept.'%');
                                 })
                                 //->order("'".$date." desc'" )
                                 ->order('submitdate desc' )
                                 ->select();
            $patsnum=count($pats);
                        
            //thesis的查询语句
            $thes= null;
            $thesnum=0;
            
        }else{
        //处理页面../search/index/index.html,post来的数据
        //根据$type的值得到不同的查询记录集
            
            //处理页面../index/index/index.html,header处post来的‘htype’
            if($htype){
                $type=$htype;
                $writer=$this->username;
                $daterange='leq';
                $startdate=date("Y-m-d",mktime(0,0,0,date("n")-1,date("j"),date("Y")));
                $enddate=date("Y-m-d");
                
                $pattype='不限';
                $patstatus='不限';
                $patdate='submitdate';
                $patdaterange=$daterange;
                $patstartdate=$startdate;
                $patenddate=$enddate;
                
            }
            
            switch($type){
                case 'project':
                    $pros= null;
                    $prosnum=0;
                    
                    $isses= null;
                    $issesnum=0;
                    
                    $thes= null;
                    $thesnum=0;
                    
                    $pats= null;
                    $patsnum=0;
                break;
                
                case 'patent':
                    
                    $pros= null;
                    $prosnum=0;
                    
                    $isses= null;
                    $issesnum=0;
                    
                    $thes= null;
                    $thesnum=0;
                    
                    switch($patdaterange){
                        
                        //$patstartdate=<$patdate
                        case 'geq':
                            //patent的查询语句
                            if($pattype=='不限'){
                                $pats = PatinfoModel::where(function ($query) use ($topic, $patdate, $patstartdate) {
                                                        $query->where('topic', 'like', '%'.$topic.'%')->where($patdate, '>= time', $patstartdate);
                                                    })
                                                    ->where(function ($query) use ($writer, $dept) {
                                                        $query->where('author', 'like', '%'.$writer.'%')->where('dept', 'like', '%'.$dept.'%');
                                                    })
                                                    ->where(function ($query) use ($patstatus) {
                                                        $query->where('status', $patstatus);
                                                    })
                                                    ->order($patdate." desc")
                                                    ->select();
                            }else{
                                $pats = PatinfoModel::where(function ($query) use ($topic, $patdate, $patstartdate) {
                                                        $query->where('topic', 'like', '%'.$topic.'%')->where($patdate, '>= time', $patstartdate);
                                                    })
                                                    ->where(function ($query) use ($writer, $dept) {
                                                        $query->where('author', 'like', '%'.$writer.'%')->where('dept', 'like', '%'.$dept.'%');
                                                    })
                                                    ->where(function ($query) use ($pattype, $patstatus) {
                                                        $query->where('pattype', $pattype)->where('status', $patstatus);
                                                    })
                                                    ->order($patdate." desc")
                                                    ->select();
                            }
                                        
                            $patsnum=count($pats);
                           
                        break;
                        
                        //$patstartdate=<$patdate=<$patenddate
                        case 'btw':
                            //patent的查询语句
                            //patent的查询语句
                            if($pattype=='不限'){
                                $pats = PatinfoModel::where(function ($query) use ($topic, $patdate, $patstartdate, $patenddate) {
                                                        $query->where('topic', 'like', '%'.$topic.'%')->where($patdate, 'between time', [$patstartdate,$patenddate]);
                                                    })
                                                    ->where(function ($query) use ($writer, $dept) {
                                                        $query->where('author', 'like', '%'.$writer.'%')->where('dept', 'like', '%'.$dept.'%');
                                                    })
                                                    ->where(function ($query) use ($patstatus) {
                                                        $query->where('status', $patstatus);
                                                    })
                                                    ->order($patdate." desc")
                                                    ->select();
                            }else{
                                $pats = PatinfoModel::where(function ($query) use ($topic, $patdate, $patstartdate, $patenddate) {
                                                        $query->where('topic', 'like', '%'.$topic.'%')->where($patdate, 'between time', [$patstartdate,$patenddate]);
                                                    })
                                                    ->where(function ($query) use ($writer, $dept) {
                                                        $query->where('author', 'like', '%'.$writer.'%')->where('dept', 'like', '%'.$dept.'%');
                                                    })
                                                    ->where(function ($query) use ($pattype, $patstatus) {
                                                        $query->where('pattype', $pattype)->where('status', $patstatus);
                                                    })
                                                    ->order($patdate." desc")
                                                    ->select();
                            }         
                            $patsnum=count($pats);
                          
                        break;
                        
                        //$patdate=<$patenddate
                        default:
                            //patent的查询语句
                            if($pattype=='不限'){
                                if($patstatus=='不限'){
                                    $pats = PatinfoModel::where(function ($query) use ($topic, $patdate, $patenddate) {
                                                        $query->where('topic', 'like', '%'.$topic.'%')->where($patdate, '<= time', $patenddate);
                                                    })
                                                    ->where(function ($query) use ($writer, $dept) {
                                                        $query->where('author', 'like', '%'.$writer.'%')->where('dept', 'like', '%'.$dept.'%');
                                                    })
                                                    ->order($patdate." desc")
                                                    ->select();
                                    
                                }
                                else{
                                    $pats = PatinfoModel::where(function ($query) use ($topic, $patdate, $patenddate) {
                                                        $query->where('topic', 'like', '%'.$topic.'%')->where($patdate, '<= time', $patenddate);
                                                    })
                                                    ->where(function ($query) use ($writer, $dept) {
                                                        $query->where('author', 'like', '%'.$writer.'%')->where('dept', 'like', '%'.$dept.'%');
                                                    })
                                                    ->where(function ($query) use ($patstatus) {
                                                        $query->where('status', $patstatus);
                                                    })
                                                    ->order($patdate." desc")
                                                    ->select();
                                    
                                }
                                
                                
                            }else{
                                 if($patstatus=='不限'){
                                    $pats = PatinfoModel::where(function ($query) use ($topic, $patdate, $patenddate) {
                                                        $query->where('topic', 'like', '%'.$topic.'%')->where($patdate, '<= time', $patenddate);
                                                    })
                                                    ->where(function ($query) use ($writer, $dept) {
                                                        $query->where('author', 'like', '%'.$writer.'%')->where('dept', 'like', '%'.$dept.'%');
                                                    })
                                                    ->where(function ($query) use ($pattype) {
                                                        $query->where('pattype', $pattype);
                                                    })
                                                    ->order($patdate." desc")
                                                    ->select();
                                 }else{
                                    $pats = PatinfoModel::where(function ($query) use ($topic, $patdate, $patenddate) {
                                                        $query->where('topic', 'like', '%'.$topic.'%')->where($patdate, '<= time', $patenddate);
                                                    })
                                                    ->where(function ($query) use ($writer, $dept) {
                                                        $query->where('author', 'like', '%'.$writer.'%')->where('dept', 'like', '%'.$dept.'%');
                                                    })
                                                    ->where(function ($query) use ($pattype, $patstatus) {
                                                        $query->where('pattype', $pattype)->where('status', $patstatus);
                                                    })
                                                    ->order($patdate." desc")
                                                    ->select();
                                 }
                                 
                            }
                            $patsnum=count($pats);
                                                    
                        break;
                     }
                
                break;
                
                case 'thesis':
                    $pros= null;
                    $prosnum=0;
                    
                    $isses= null;
                    $issesnum=0;
                    
                    $thes= null;
                    $thesnum=0;
                    
                    $pats= null;
                    $patsnum=0;
                break;
                
                case 'issue':
                    $pros= null;
                    $prosnum=0;
                    
                    $isses= IssinfoModel::where(function ($query) use ($topic, $enddate) {
                                            $query->where('topic', 'like', '%'.$topic.'%')->where('submitdate', '<= time', $enddate);
                                        })
                                        ->where(function ($query) use ($writer, $dept) {
                                            $query->where('writer', 'like', '%'.$writer.'%')->where('dept', 'like', '%'.$dept.'%');
                                        })
                                        ->order('submitdate desc')
                                        ->select();
                                        
                    $issesnum=count($isses);
                    
                    $thes= null;
                    $thesnum=0;
                    
                    $pats= null;
                    $patsnum=0;
                break;
                
                // 'all'
                default:
                               
                    switch($daterange){
                        
                      
                        //$startdate=<$date
                        case 'geq':
                            //project的查询语句
                            //$projects=
                            
                            //issue的查询语句
                            $isses= IssinfoModel::where(function ($query) use ($topic, $startdate) {
                                                    $query->where('topic', 'like', '%'.$topic.'%')->where('submitdate', '>= time', $startdate);
                                                })
                                                ->where(function ($query) use ($writer, $dept) {
                                                    $query->where('writer', 'like', '%'.$writer.'%')->where('dept', 'like', '%'.$dept.'%');
                                                })
                                                ->order('submitdate desc')
                                                ->select();
                            $issesnum=count($isses);
                            
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
                            
                            
                            
                            
                            $pros= null;
                            $prosnum=0;
                            
                            $thes= null;
                            $thesnum=0;
                        
                        break;
                        
                        //$date=<$enddate
                        case 'leq':
                            //project的查询语句
                            
                            //patent的查询语句
                            $pats = PatinfoModel::where(function ($query) use ($topic, $enddate) {
                                                    $query->where('topic', 'like', '%'.$topic.'%')->where('submitdate', '<= time', $enddate);
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
                            
                            //分页代码？？
                            
                            //thesis的查询语句
                            
                        
                            
                            $pros= null;
                            $prosnum=0;
                            
                            $thes= null;
                            $thesnum=0;
                        
                        break;
                        
                        //$startdate=<$date<=$enddate
                        default:
                            //project的查询语句
                            $pros= null;
                            $prosnum=0;
                            
                            //issue的查询语句
                            $isses= IssinfoModel::where(function ($query) use ($topic, $startdate,$enddate) {
                                                    $query->where('topic', 'like', '%'.$topic.'%')->where('submitdate', 'between time', [$startdate,$enddate]);
                                                })
                                                ->where(function ($query) use ($writer, $dept) {
                                                    $query->where('writer', 'like', '%'.$writer.'%')->where('dept', 'like', '%'.$dept.'%');
                                                })
                                                ->order('submitdate desc')
                                                ->select();
                            $issesnum=count($isses);
                            
                            
                            //patent的查询语句
                            $pats = PatinfoModel::where(function ($query) use ($topic, $startdate,$enddate) {
                                                    $query->where('topic', 'like', '%'.$topic.'%')->where('submitdate', 'between time', [$startdate,$enddate]);
                                                })
                                                ->where(function ($query) use ($writer, $dept) {
                                                    $query->where('author', 'like', '%'.$writer.'%')->where('dept', 'like', '%'.$dept.'%');
                                                })
                                                //->order("'".$date." desc'" )
                                                ->order('submitdate desc' )
                                                ->select();
                                        
                            $patsnum=count($pats);
                            
                            //thesis的查询语句
                            $thes= null;
                            $thesnum=0;
                            
                            
                        break;
                          
                    }
                  
                break;
                
                
                
            }
        }
        
        
        
        //--在index.html页面输出自定义信息的HTML代码块
        $destr= "请求方法:".$request->method()."</br>".
                "type:".$type."</br>".
                "searchword:".$searchword."</br>".
                "username:".$this->username."</br>";     
        
        $this->assign([
            //在index.html页面通过'destr'输出自定义的信息
            'destr'=>$destr,
            //在index.html页面通过'array'输出自定义的数组内容
            'array'=>$this->roles, 
            
            'home'=>$request->domain(),
            'username'=>$this->username,
            
            'type'=>$type,
            'topic'=>$topic,
            'writer'=>$writer,
            'dept'=>$dept,
            'daterange'=>$daterange,
            'startdate'=>$startdate,
            'enddate'=>$enddate,
            
            'pats'=>$pats,
            'patsnum'=>$patsnum,
            'pattype'=>$pattype,
            'patstatus'=>$patstatus,
            'patdate'=>$patdate,
            'patdaterange'=>$patdaterange,
            'patstartdate'=>$patstartdate,
            'patenddate'=>$patenddate,
            
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
        
        //return View('index/showresult');
        return View('index/index');
        
        
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
