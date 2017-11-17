<?php
namespace app\issue\controller;

use think\Request;
use think\Session;
use think\View;

use app\issue\model\Issinfo as IssinfoModel;
use app\issue\model\Issrecord as IssrecordModel;
use app\patent\model\Patinfo as PatinfoModel;
use app\attachment\model\Attinfo as AttinfoModel;
use app\user\model\User as UserModel;

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
            $this->redirect($request->domain());
        }else{
            //根据登录人选择的role，控制模板显示对应的内容
            //3个参数由user/view/index/index.html模板文件通过GET传来
            $roleparam=$request->param('role');
            $active=$request->param('active');
            $type=$request->param('type');
        }
                       
        //$active的值不能超4或小1(因为系统默认每个角色需处理3~4种状态的事务，分别为1,2,3,4)，否则修改为1
        if($active>4 or $active<1){
            $active=1;
        }
        
        //判断$request->param('role')传来的role值是否为Session中存储的role值，否则报错退回到登录页面
        for($i = 0; $i < count($this->roles); $i++) {
            if($roleparam==$this->roles[$i]){
                $n=0;
                $n=$n+1;
                break;   
            }else{
                $n=0;
            }
        }
        if($n>=1){
            switch($roleparam){
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
                $this->error('用户角色错误，请重新登录系统');   
            }
        }else{
            $this->error('用户角色不符，只能使用已注册角色');
        }
        
        
        
        //根据$type的值选择不同的模板显示内容
        switch($type){
                case 1:
                    $typename="项目";
                break;
                        
                case 2:
                    $typename="专利";
                break;
                        
                case 3:
                    $typename="论文";
                break;
                        
                default:
                    $type=2;
                    $typename="专利";
                break;   
            }
            
        //实例化IssinfoModel模型类
        $issues = new IssinfoModel;     
        
        //为index.html模板的writer第一类issue(待处理)模板变量赋值
        $writer1=$issues->where('num_type',$type)
                        ->where('writer',$this->username)
                        ->where('status',['=','填报'],['=','退回修改'],'or')
                        ->order('status desc')
                        ->select();
        $num_writer1=count($writer1);                    
        //为index.html模板的writer第二类issue（审批中）模板变量赋值
        $writer2=$issues->where('num_type',$type)
                        ->where('writer',$this->username)
                        ->where('status',['=','提交'],['=','审核通过'],'or')
                        ->order('addnewdate asc')
                        ->select();
        $num_writer2=count($writer2); 
        //为index.html模板的writer第三类issue（审批结果）模板变量赋值
        $writer3=$issues->where('num_type',$type)
                        ->where('writer',$this->username)
                        ->where('status',['=','批准'],['=','否决'],'or')
                        ->order('addnewdate asc')
                        ->select();
        $num_writer3=count($writer3); 
        //为index.html模板的writer第四类issue（执行情况）模板变量赋值
        $writer4=$issues->where('num_type',$type)
                        ->where('writer',$this->username)
                        ->where('status',['=','执行中'],['=','完成'],'or')
                        ->order('addnewdate asc')
                        ->select();
        $num_writer4=count($writer4); 
        
        //为index.html模板的reviewer第一类issue(待处理)模板变量赋值
        $reviewer1=$issues->where('num_type',$type)
                            ->where('dept',$this->dept)
                            ->where('status','提交')
                            ->order('submitdate asc')
                            ->select();
        $num_reviewer1=count($reviewer1); 
        //为index.html模板的reviewer第二类issue（批准中）模板变量赋值
        $reviewer2=$issues->where('num_type',$type)
                            ->where('dept',$this->dept)
                            ->where('status',['=','退回修改'],['=','审核通过'],'or')
                            ->order('auditrejectdate asc')
                            ->select();
        $num_reviewer2=count($reviewer2); 
        //为index.html模板的reviewer第三类issue（批准结果）模板变量赋值
        $reviewer3=$issues->where('num_type',$type)
                            ->where('dept',$this->dept)
                            ->where('status',['=','批准'],['=','否决'],'or')
                            ->order('auditrejectdate asc')
                            ->select();
        $num_reviewer3=count($reviewer3); 
        //为index.html模板的reviewer第四类issue（执行情况）模板变量赋值
        $reviewer4=$issues->where('num_type',$type)
                            ->where('dept',$this->dept)
                            ->where('status',['=','执行中'],['=','完成'],'or')
                            ->order('resultdate asc')
                            ->select();
        $num_reviewer4=count($reviewer4); 
        
        //为index.html模板的approver第一类issue(待处理)模板变量赋值
        $approver1=$issues->where('num_type',$type)
                            ->where('status','审核通过')
                            ->order('auditrejectdate asc')
                            ->select();
        $num_approver1=count($approver1); 
        //为index.html模板的approver第二类issue（审批结果）模板变量赋值
        $approver2=$issues->where('num_type',$type)
                            ->where('status',['=','退回修改'],['=','批准'],['=','否决'],'or')
                            ->order('resultdate asc')
                            ->select();
        $num_approver2=count($approver2);
        //为index.html模板的approver第三类issue（执行中）模板变量赋值
        $approver3=$issues->where('num_type',$type)
                            ->where('status','执行中')
                            ->order('resultdate asc')
                            ->select();
        $num_approver3=count($approver3);
        //为index.html模板的approver第四类issue（完成情况）模板变量赋值
        $approver4=$issues->where('num_type',$type)
                            ->where('status','完成')
                            ->order('resultdate asc')
                            ->select();
        $num_approver4=count($approver4);
                    
        //为index.html模板的operator第一类issue(待处理)模板变量赋值
        $operator1=$issues->where('num_type',$type)
                            ->where('executer',$this->username)
                            ->where('status','批准')
                            ->order('resultdate asc')
                            ->select();
        $num_operator1=count($operator1);
        //为index.html模板的operator第二类issue（执行中）模板变量赋值
       
       // $operator2=$issues->where('num_type',$type)
//                            ->where('executer',$this->username)
//                            ->where('status','执行中')
//                            ->order('operatestartdate asc')
//                            ->select();
                            
        //根据Patinfo模型和Issinfo模型的一对一关系，进行数据集查询
        //本文件的issinfo操作中也有应用
        //“issinfo.executer”的写法是为了指定查询的字段，因为还存在“Patinfo.executer”
        //是为了在本issue模块的模板文件：index.html中使用“Patinfo.status”字段的值
        $operator2= IssinfoModel::with('patinfo')
                            ->where('num_type',$type)
                            ->where('issinfo.executer',$this->username)
                            ->where('issinfo.status','执行中')
                            ->order('operatestartdate asc')
                            ->select();
        $num_operator2=count($operator2);
        //为index.html模板的operator第三类issue（完成情况）模板变量赋值
        $operator3=$issues->where('num_type',$type)
                            ->where('executer',$this->username)
                            ->where('status','完成')
                            ->order('finishdate asc')
                            ->select();
        $num_operator3=count($operator3);
        

        //--在index.html页面输出自定义信息的HTML代码块        
		$destr= "请求方法:".$request->method()."</br>".
            "username:".$this->username."</br>".
            //"pwd:".sizeof($pwd);
            "pwd:".$this->pwd."</br>".
            "log:".$this->log."</br>".
            "roleparam:".$request->param('role').";active:".$request->param('active')."</br>".
            "dept:".$this->dept;
        //--!       
        
        $this->assign([
        //在index.html页面通过'destr'输出自定义的信息
        'destr'=>$destr,
        //在index.html页面通过'array'输出自定义的数组内容
        'array'=>$this->roles, 
        
        'home'=>$request->domain(),
        'username'=>$this->username,
        'rolename'=>$rolename,
        'role'=>$roleparam,
        'active'=>$active,
        'typename'=>$typename,
        'type'=>$type,
        
        'writer1'=>$writer1,
        'num_writer1'=>$num_writer1,
        'writer2'=>$writer2,
        'num_writer2'=>$num_writer2,
        'writer3'=>$writer3,
        'num_writer3'=>$num_writer3,
        'writer4'=>$writer4,
        'num_writer4'=>$num_writer4,
        
        'reviewer1'=>$reviewer1,
        'num_reviewer1'=>$num_reviewer1,
        'reviewer2'=>$reviewer2,
        'num_reviewer2'=>$num_reviewer2,
        'reviewer3'=>$reviewer3,
        'num_reviewer3'=>$num_reviewer3,
        'reviewer4'=>$reviewer4,
        'num_reviewer4'=>$num_reviewer4,
        
        'approver1'=>$approver1,
        'num_approver1'=>$num_approver1,
        'approver2'=>$approver2,
        'num_approver2'=>$num_approver2,
        'approver3'=>$approver3,
        'num_approver3'=>$num_approver3,
        'approver4'=>$approver4,
        'num_approver4'=>$num_approver4,
        
        'operator1'=>$operator1,
        'num_operator1'=>$num_operator1,
        'operator2'=>$operator2,
        'num_operator2'=>$num_operator2,
        'operator3'=>$operator3,
        'num_operator3'=>$num_operator3,
        
        
        ]);
        return view();
    }
    
    //writer“新增”issue
     public function issnew(Request $request)
    {
        //通过$log判断是否是登录用户，非登录用户退回到登录页面
        if(1!==$this->log){
            $this->error('未登录用户，请先登录系统');
            //$this->redirect($request->domain());
        }else{
            $type=$request->param('type');
            $id=$request->param('id');
            $role=$request->param('role');
        }
        
        if(empty($role)){
             //判断登录用户的$roles值是否含有writer，不含writer就报错并退回到上一个页面
            $i=0;
            foreach ($this->roles as $value) {
                if($value=="writer")
                $i=1;
            }
            if(1==$i){
                $rolename="撰写人";
                $role="writer";
                //break;   
            }else{
                $this->error('未授权用户，请先登录系统');
            }
        }else{
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
                $this->error('用户角色错误，请重新登录系统');
           } 
        }
        
         //根据$type的值选择不同的模板显示内容
            switch($type){
                case 1:
                    $typename="项目";
                break;
                            
                case 2:
                    $typename="专利";
                break;
                            
                case 3:
                    $typename="论文";
                break;
                            
                default:
                    $type=1;
                    $typename="项目";
                break;   
            }
        
        if($id){
            //查出$id对应的issue记录
            $iss=IssinfoModel::get($id);
            
            //查出$iss->status对应的issrecord记录进行显示
            if($iss->status=="执行中"||$iss->status=="完成"){
    
                //查出符合2个条件的$iss->status对应的issrecord记录，选出ID最大的记录的“actdetail”字段值
                $maxid= $iss->issrecords()
                                ->where('issinfo_id',$iss->id)
                                ->where('act','批准')
                                ->max('id');
                                
                $issrecord= $iss->issrecords()
                                ->where('id',$maxid)
                                ->find();
                                
                $report=null;
                $reportdate=date("Y-m-d, H-i-s");
            }else{
                //查出符合2个条件的$iss->status对应的issrecord记录，选出ID最大的记录的“actdetail”字段值
                $maxid= $iss->issrecords()
                                ->where('issinfo_id',$iss->id)
                                ->where('act',$iss->status)
                                ->max('id');
                                
                $issrecord= $iss->issrecords()
                                ->where('id',$maxid)
                                ->find();
                $report=null;
                $reportdate=null;
            }
            
            ////查出符合2个条件的$iss->status对应的issrecord记录，选出ID最大的记录的“actdetail”字段值
//            $maxid= $iss->issrecords()
//                            ->where('issinfo_id',$iss->id)
//                            ->where('act',$iss->status)
//                            ->max('id');
//                            
//            $issrecord= $iss->issrecords()
//                            ->where('id',$maxid)
//                            ->find();
//            
            
            //查出$iss->issnum对应的attachment记录
            $atts=AttinfoModel::where('num_id',$iss->issnum)
                                ->order('uploaddate asc')
                                ->select(); 
            
            //为approver指定executer查出所有撰写人名单                    
            if($role=='approver'){
                $users=UserModel::where('rolety_id',"rolety1")
                                ->order('dept asc')
                                ->select();
            }else{
                $users=null;
            }
            
            $topic=$iss->topic;
            $type=$iss->num_type;
            $sub_num_type=$iss->sub_num_type;
            $username=$this->username;
            $writer=$iss->writer;
            $dept=$iss->dept;
            $abstract=$iss->abstract;
            $num_id=$iss->num_id;
            $status=$iss->status;
            $addnewdate=$iss->addnewdate;
            $submitdate=$iss->submitdate;
            $auditrejectdate=$iss->auditrejectdate;
            $resultdate=$iss->resultdate;
            $finishdate=$iss->finishdate;
            $operatestartdate=$iss->operatestartdate;
            $actdetail=$issrecord->actdetail;
            $issnum=$iss->issnum;
            
            $flag=1;
            
        }else{
            $id=null;
            $topic=null;
            $sub_num_type=null;
            $username=$this->username;
            $writer=$username;
            $dept=$this->dept;
            $abstract=null;
            $num_id=null;
            $status="填报";
            $addnewdate=date("Y-m-d, H-i-s");
            $submitdate=null;
            $auditrejectdate=null;
            $resultdate=null;
            $finishdate=null;
            $operatestartdate=null;
            $actdetail=null;
            $issnum=null;
            $atts=null;
            $users=null;
            
            $report=null;
            $reportdate=null;
            
            $flag=0;
            
        }
        
        //--在index.html页面输出自定义信息的HTML代码块
        $destr= "请求方法:".$request->method()."</br>".
                "username:".$this->username."</br>".
                //"pwd:".sizeof($pwd);
                "pwd:".$this->pwd."</br>".
                "status:".$status."</br>".
                "log:".$this->log."| type:".$type."</br>";
                //$request->param('pronum')."</br>";
                //"home:".$request->domain()."</br>";
        
        
        $this->assign([
        //在index.html页面通过'destr'输出自定义的信息
        'destr'=>$destr,
        //在index.html页面通过'array'输出自定义的数组内容
        'array'=>$this->roles, 
        
        'id'=>$id,
        'rolename'=>$rolename,
        'typename'=>$typename,
        'home'=>$request->domain(),
        'role'=>$role,
        
        'topic'=>$topic,
        'type'=>$type,
        'sub_num_type'=>$sub_num_type,
        'username'=>$username,
        'writer'=>$writer,
        'dept'=>$dept,
        'abstract'=>$abstract,
        'num_id'=>$num_id,
        'status'=>$status,
        'addnewdate'=>$addnewdate,
        'submitdate'=>$submitdate,
        'resultdate'=>$resultdate,
        'finishdate'=>$finishdate,
        'auditrejectdate'=>$auditrejectdate,
        'operatestartdate'=>$operatestartdate,
        'actdetail'=>$actdetail,
        'issnum'=>$issnum,
        
        'report'=>$report,
        'reportdate'=>$reportdate,
        'atts'=>$atts,
        'users'=>$users,
        
        'flag'=>$flag,
        
        'uploaddate'=>date("Y-m-d, H-i-s"),
        ]);
        return view();
        
        //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>IPMS V3<br/></p><span style="font-size:22px;"></span></div>';
        
    }
    
    //“保存”issue，记录issue写、审、批、执行过程。涉及Issinfo表、Issrecord表
     public function isssave(Request $request)
    {
        
        //
//        if($request->isPost()){
//            return '<div style="padding: 24px 48px;"><h1>:)</h1><p>IPMS V3 post<br/></p><span style="font-size:22px;"></span></div>';
//        }else{
//            return '<div style="padding: 24px 48px;"><h1>:)</h1><p>IPMS V3 no post<br/></p><span style="font-size:22px;"></span></div>';
//        } 
        
        $issnum=$request->param('issnum');
        $operate=$request->param('operate');
        $rolename=$request->param('rolename');
        $actdetail=$request->param('actdetail');
        $executer=$request->param('executer');
        //post来的“num_id”是数组，要加“a”才能获取到。TP5.0默认是“s”(字符串)
        $num_id = $request->param('num_id/a');
        //$num_type=post来的“sub_num_type”的第一个字符
        $num_type=substr($request->param('sub_num_type'), 0, 1);
        
        //保存新增的事务信息  
        if(empty($issnum)){
            //通过外部提交赋值给模型Issinfo类，由Issinfo类将数据写入数据表
            $isses = new IssinfoModel($_POST);
            $isses->num_type=$num_type;
            
            //过滤post数组中的非数据表字段数据后写入数据表
            $isses->allowField(true)->save(); 
            //获取自增ID值
            //$isses->id;
            
            //补充新的专利、项目、论文申报事务时num_id字段的值，此时的num_id值就为issnum值
            $iss= $isses->where('id', $isses->id)->find();
            if(empty($iss->num_id)){
                $iss->num_id=$iss->issnum;
                $iss->save();
                
                //由Issrecord类将数据写入数据表
                $data=array("num"=>$iss->issnum,"act"=>"填报","actdetail"=>"填报".'《'.$iss->topic.'》',"username"=>$this->username,"rolename"=>"撰写人","att_name"=>"","acttime"=>$iss->addnewdate,"issinfo_id"=>$iss->id);
                $issrecord = new IssrecordModel($data);
                //过滤数组中的非数据表字段数据后写入数据表
                $issrecord->allowField(true)->save();
            }
                   
            //上述代码将数据写入数据库后，带参数'id'跳转到<{$home}>/issue/index/issnew页面
            $this->redirect('index/issnew', ['id' =>$isses->id]);
        
        }
        //更新已存在的issue的各项信息
        else{
                             
            //其他情况
            switch($operate){
                //writer，submit
                case'submit':
                    $iss = IssinfoModel::where('issnum',$issnum)
                                        ->find();
                    //更新数据表中的字段值
                    $iss->submitdate=date("Y-m-d, H-i-s"); 
                    $iss->status="提交";
                    $iss->save();
                    
                    //获取修改的ID值
                    $iss->id;
                    
                    //通过外部提交赋值给模型Issrecord类，由Issrecord类将数据写入数据表
                    $data=array("num"=>$iss->issnum,"act"=>$iss->status,"actdetail"=>"提交".'《'.$iss->topic.'》',"username"=>$this->username,"rolename"=>$rolename,"att_name"=>"","acttime"=>$iss->submitdate,"issinfo_id"=>$iss->id);
                    
                    $issrecord = new IssrecordModel($data);
                    //过滤数组中的非数据表字段数据后写入数据表
                    $issrecord->allowField(true)->save();
                
                    //上述代码将数据写入数据库后，带参数'id'跳转到<{$home}>/issue/index/issnew页面
                    $this->redirect('index/issinfo', ['id' =>$iss->id]);
  
                break;
                
                //reviewer，审查audit
                case'audit':
                    $iss = IssinfoModel::where('issnum',$issnum)
                                        ->find();
                    //更新数据表中的字段值
                    $iss->auditrejectdate=date("Y-m-d, H-i-s"); 
                    $iss->status="审核通过";
                    $iss->save();
                    
                    //获取修改的ID值
                    $iss->id;
                    
                    //通过外部提交赋值给模型Issrecord类，由Issrecord类将数据写入数据表
                    $data=array("num"=>$iss->issnum,"act"=>$iss->status,"actdetail"=>$actdetail,"username"=>$this->username,"rolename"=>$rolename,"att_name"=>"","acttime"=>$iss->auditrejectdate,"issinfo_id"=>$iss->id);
                    
                    $issrecord = new IssrecordModel($data);
                    //过滤数组中的非数据表字段数据后写入数据表
                    $issrecord->allowField(true)->save();
                
                    //上述代码将数据写入数据库后，带参数'id'跳转到<{$home}>/issue/index/issinfo页面
                    $this->redirect('index/issinfo', ['id' =>$iss->id,'type' =>$iss->num_id,'role'=>'reviewer']);
                
                break;
                
                 //approver，同意approve
                case'approve':
                    $iss = IssinfoModel::where('issnum',$issnum)
                                        ->find();
                    //更新数据表中的字段值
                    $iss->resultdate=date("Y-m-d, H-i-s"); 
                    $iss->status="批准";
                    $iss->executer=$executer;
                    $iss->save();
                    
                    //获取修改的ID值
                    $iss->id;
                    
                    //通过外部提交赋值给模型Issrecord类，由Issrecord类将数据写入数据表
                    $data=array("num"=>$iss->issnum,"act"=>$iss->status,"actdetail"=>$actdetail,"username"=>$this->username,"rolename"=>$rolename,"att_name"=>"","acttime"=>$iss->resultdate,"issinfo_id"=>$iss->id);
                    
                    $issrecord = new IssrecordModel($data);
                    //过滤数组中的非数据表字段数据后写入数据表
                    $issrecord->allowField(true)->save();
                
                    //上述代码将数据写入数据库后，带参数'id'跳转到<{$home}>/issue/index/issinfo页面
                    $this->redirect('index/issinfo', ['id' =>$iss->id,'type' =>$iss->num_id,'role'=>'approver']);
                
                break;
                
                //reviewer，approver"退回修改"
                case'reject':
                    if($rolename=="审查人"){
                        $iss = IssinfoModel::where('issnum',$issnum)
                                        ->find();
                        //更新数据表中的字段值
                        $iss->auditrejectdate=date("Y-m-d, H-i-s"); 
                        $iss->status="退回修改";
                        $iss->save();
                        
                        //获取修改的ID值
                        $iss->id;
                        
                        //通过外部提交赋值给模型Issrecord类，由Issrecord类将数据写入数据表
                        $data=array("num"=>$iss->issnum,"act"=>$iss->status,"actdetail"=>$actdetail,"username"=>$this->username,"rolename"=>$rolename,"att_name"=>"","acttime"=>$iss->auditrejectdate,"issinfo_id"=>$iss->id);
                        
                        $issrecord = new IssrecordModel($data);
                        //过滤数组中的非数据表字段数据后写入数据表
                        $issrecord->allowField(true)->save();
                    
                        //上述代码将数据写入数据库后，带参数'id'跳转到<{$home}>/issue/index/issinfo页面
                        $this->redirect('index/issinfo', ['id' =>$iss->id,'type' =>$iss->num_id,'role'=>'reviewer']);
                    }else{
                        $iss = IssinfoModel::where('issnum',$issnum)
                                        ->find();
                        //更新数据表中的字段值
                        $iss->resultdate=date("Y-m-d, H-i-s"); 
                        $iss->status="退回修改";
                        $iss->save();
                        
                        //获取修改的ID值
                        $iss->id;
                        
                        //通过外部提交赋值给模型Issrecord类，由Issrecord类将数据写入数据表
                        $data=array("num"=>$iss->issnum,"act"=>$iss->status,"actdetail"=>$actdetail,"username"=>$this->username,"rolename"=>$rolename,"att_name"=>"","acttime"=>$iss->resultdate,"issinfo_id"=>$iss->id);
                        
                        $issrecord = new IssrecordModel($data);
                        //过滤数组中的非数据表字段数据后写入数据表
                        $issrecord->allowField(true)->save();
                    
                        //上述代码将数据写入数据库后，带参数'id'跳转到<{$home}>/issue/index/issinfo页面
                        $this->redirect('index/issinfo', ['id' =>$iss->id,'type' =>$iss->num_id,'role'=>'approver']);
                    }
                
                break;
                
                //reviewer，approver"否决"
                case'refuse':
                    if($rolename=="审查人"){
                        $iss = IssinfoModel::where('issnum',$issnum)
                                        ->find();
                        //更新数据表中的字段值
                        $iss->auditrejectdate=date("Y-m-d, H-i-s"); 
                        $iss->status="否决";
                        $iss->save();
                        
                        //获取修改的ID值
                        $iss->id;
                        
                        //通过外部提交赋值给模型Issrecord类，由Issrecord类将数据写入数据表
                        $data=array("num"=>$iss->issnum,"act"=>$iss->status,"actdetail"=>$actdetail,"username"=>$this->username,"rolename"=>$rolename,"att_name"=>"","acttime"=>$iss->auditrejectdate,"issinfo_id"=>$iss->id);
                        
                        $issrecord = new IssrecordModel($data);
                        //过滤数组中的非数据表字段数据后写入数据表
                        $issrecord->allowField(true)->save();
                    
                        //上述代码将数据写入数据库后，带参数'id'跳转到<{$home}>/issue/index/issinfo页面
                        $this->redirect('index/issinfo', ['id' =>$iss->id,'type' =>$iss->num_id,'role'=>'reviewer']);
                    }else{
                        $iss = IssinfoModel::where('issnum',$issnum)
                                        ->find();
                        //更新数据表中的字段值
                        $iss->resultdate=date("Y-m-d, H-i-s"); 
                        $iss->status="否决";
                        $iss->save();
                        
                        //获取修改的ID值
                        $iss->id;
                        
                        //通过外部提交赋值给模型Issrecord类，由Issrecord类将数据写入数据表
                        $data=array("num"=>$iss->issnum,"act"=>$iss->status,"actdetail"=>$actdetail,"username"=>$this->username,"rolename"=>$rolename,"att_name"=>"","acttime"=>$iss->resultdate,"issinfo_id"=>$iss->id);
                        
                        $issrecord = new IssrecordModel($data);
                        //过滤数组中的非数据表字段数据后写入数据表
                        $issrecord->allowField(true)->save();
                    
                        //上述代码将数据写入数据库后，带参数'id'跳转到<{$home}>/issue/index/issinfo页面
                        $this->redirect('index/issinfo', ['id' =>$iss->id,'type' =>$iss->num_id,'role'=>'approver']);
                    }
                
                break;
                
                //operator，"领受任务"
                case'accept':
                    $iss = IssinfoModel::where('issnum',$issnum)
                                        ->find();
                    //更新数据表中的字段值
                    $iss->operatestartdate=date("Y-m-d, H-i-s"); 
                    $iss->status="执行中";
                    $iss->save();
                    
                    //获取修改的ID值
                    $iss->id;
                    
                    //通过外部提交赋值给模型Issrecord类，由Issrecord类将数据写入数据表
                    $data=array("num"=>$iss->issnum,"act"=>$iss->status,"actdetail"=>"领受任务","username"=>$this->username,"rolename"=>$rolename,"att_name"=>"","acttime"=>$iss->operatestartdate,"issinfo_id"=>$iss->id);
                    
                    $issrecord = new IssrecordModel($data);
                    //过滤数组中的非数据表字段数据后写入数据表
                    $issrecord->allowField(true)->save();
                
                    //上述代码将数据写入数据库后，带参数'id'跳转到<{$home}>/issue/index/issinfo页面
                    //$this->redirect('index/issinfo', ['id' =>$iss->id,'role'=>'operator']);

                    //上述代码将数据写入数据库后，带参数'id'跳转到<{$home}>/patent/index/patnew页面
                    $this->redirect('Patent/index/patnew', ['id' =>$iss->id,'type' =>$iss->num_id,'role'=>'operator']);
                    //$this->redirect($request->domain().'/patent/index/patnew/issnum/'.$iss->issnum.'/role/operator');
                break;
                
                //operator，"报告任务"
                case'report':
                    $iss = IssinfoModel::where('issnum',$issnum)
                                        ->find();
  
                    //通过外部提交赋值给模型Issrecord类，由Issrecord类将数据写入数据表
                    $data=array("num"=>$iss->issnum,"act"=>$iss->status,"actdetail"=>$actdetail,"username"=>$this->username,"rolename"=>$rolename,"att_name"=>"","acttime"=>date("Y-m-d, H-i-s"),"issinfo_id"=>$iss->id);
                    
                    $issrecord = new IssrecordModel($data);
                    //过滤数组中的非数据表字段数据后写入数据表
                    $issrecord->allowField(true)->save();
                
                    //上述代码将数据写入数据库后，带参数'id'跳转到<{$home}>/issue/index/issinfo页面
                    $this->redirect('index/issinfo', ['id' =>$iss->id,'type' =>$iss->num_id,'role'=>'operator']);
                                
                
                break;
                
                //operator，"关闭任务"
                case'close':
                    $iss = IssinfoModel::where('issnum',$issnum)
                                        ->find();
                    //更新数据表中的字段值
                    $iss->finishdate=date("Y-m-d, H-i-s"); 
                    $iss->status="完成";
                    $iss->save();
                    
                    //获取修改的ID值
                    $iss->id;
                    
                    //通过外部提交赋值给模型Issrecord类，由Issrecord类将数据写入数据表
                    $data=array("num"=>$iss->issnum,"act"=>$iss->status,"actdetail"=>$actdetail,"username"=>$this->username,"rolename"=>$rolename,"att_name"=>"","acttime"=>$iss->finishdate,"issinfo_id"=>$iss->id);
                    
                    $issrecord = new IssrecordModel($data);
                    //过滤数组中的非数据表字段数据后写入数据表
                    $issrecord->allowField(true)->save();
                
                    //上述代码将数据写入数据库后，带参数'id'跳转到<{$home}>/issue/index/issinfo页面
                    $this->redirect('index/issinfo', ['id' =>$iss->id,'type' =>$iss->num_id,'role'=>'operator']);
                                
                
                break;
                
                
                default:
                    
                break;
                
            }
            
            $set=IssinfoModel::get(['issnum' => $issnum]);
            
            //writer更新数据表中的字段值,有2种情况，status=‘填报’或‘退回修改’。
            //if($rolename=="撰写人"){
            //if($request->param('status')=="退回修改"){
            if($set->status=="填报"){
                
                $set->topic=$request->param('topic');
                $set->abstract=$request->param('abstract'); 
                $set->addnewdate=date("Y-m-d, H-i-s");
                $set->save();
                
                //查出issreord表中$issnum对应的记录
                $issrecord= $set->issrecords()
                            ->where('num',$issnum)
                            ->where('act','填报')
                            ->find();
                
                //如果存在$issrecord->issnum，就修改acttime值;不存在就新增一条记录
                if(!empty($issrecord->num)){
                    $issrecord->acttime=$set->addnewdate;
                    $issrecord->save();
                    $this->redirect('index/issnew', ['id' =>$set->id]);
                    
                }else{
                     //通过外部提交赋值给模型Issrecord类，由Issrecord类将数据写入数据表
                    $data=array("num"=>$set->issnum,"act"=>$set->status,"actdetail"=>"填报".'《'.$set->topic.'》',"username"=>$this->username,"rolename"=>"撰写人","att_name"=>"","acttime"=>$set->addnewdate,"issinfo_id"=>$set->id);
                        
                    $issrecord = new IssrecordModel($data);
                    //过滤数组中的非数据表字段数据后写入数据表
                    $issrecord->allowField(true)->save();
                    //上述代码将数据写入数据库后，带参数'id'跳转到<{$home}>/issue/index/issnew页面
                    $this->redirect('index/issnew', ['id' =>$set->id]);
                        
                }
               
            }elseif($set->status=="退回修改"){
                     
                $set->topic=$request->param('topic');
                $set->abstract=$request->param('abstract'); 
                $set->addnewdate=date("Y-m-d, H-i-s");
                $set->save();
                
                //查出issreord表中$issnum对应的记录
                $issrecord= $set->issrecords()
                            ->where('num',$issnum)
                            ->where('act','退回后的修改')
                            ->find();
                
                //如果存在$issrecord->issnum，就修改acttime值;不存在就新增一条记录
                if(!empty($issrecord->num)){
                    $issrecord->acttime=$set->addnewdate;
                    $issrecord->save();
                    $this->redirect('index/issnew', ['id' =>$set->id]);
                    
                }else{
                     //通过外部提交赋值给模型Issrecord类，由Issrecord类将数据写入数据表
                    $data=array("num"=>$set->issnum,"act"=>"退回后的修改","actdetail"=>"退回".'《'.$set->topic.'》'."后的修改","username"=>$this->username,"rolename"=>"撰写人","att_name"=>"","acttime"=>$set->addnewdate,"issinfo_id"=>$set->id);
                        
                    $issrecord = new IssrecordModel($data);
                    //过滤数组中的非数据表字段数据后写入数据表
                    $issrecord->allowField(true)->save();
                    //上述代码将数据写入数据库后，带参数'id'跳转到<{$home}>/issue/index/issnew页面
                    $this->redirect('index/issnew', ['id' =>$set->id]);
                        
                }
            }
            
        }
        
        //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>IPMS V3<br/></p><span style="font-size:22px;"></span></div>';
    }    
    
    //writer“提交”issue
     public function isssubmit(Request $request)
    {
        $this->redirect('index/issnew', ['id' =>$request->param('id'),'type' =>$request->param('type')]);
        //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>IPMS V3<br/></p><span style="font-size:22px;"></span></div>';
    }
    
    //writer“修改”issue
     public function issmodify(Request $request)
    {
        
        $this->redirect('index/issnew', ['id' =>$request->param('id'),'type' =>$request->param('type')]);
        //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>IPMS V3<br/></p><span style="font-size:22px;"></span></div>';
    }
    
    //writer“删除”issue
     public function issdelete(Request $request)
    {
        
        return '<div style="padding: 24px 48px;"><h1>:)</h1><p>IPMS V3<br/></p><span style="font-size:22px;"></span></div>';
    }
    
    //reviewer“审核通过”issue
     public function issaudit(Request $request)
    {
        $this->redirect('index/issnew', ['id' =>$request->param('id'),'type' =>$request->param('type'),'role' =>"reviewer"]);
        //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>IPMS V3<br/></p><span style="font-size:22px;"></span></div>';
    }
    
    //reviewer、approver“退回修改”issue
     public function issreject(Request $request)
    {
        $this->redirect('index/issnew', ['id' =>$request->param('id'),'type' =>$request->param('type'),'role' =>$request->param('role')]);
        //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>IPMS V3<br/></p><span style="font-size:22px;"></span></div>';
    }
    
    //reviewer、approver“否决”issue
     public function issrefuse(Request $request)
    {
        $this->redirect('index/issnew', ['id' =>$request->param('id'),'type' =>$request->param('type'),'role' =>$request->param('role')]);
        //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>IPMS V3<br/></p><span style="font-size:22px;"></span></div>';
    }
    
    //approver“批准同意”issue
     public function issapprove(Request $request)
    {
        $this->redirect('index/issnew', ['id' =>$request->param('id'),'type' =>$request->param('type'),'role' =>"approver"]);
        //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>IPMS V3<br/></p><span style="font-size:22px;"></span></div>';
    }
    
    //operator“领受”issue
     public function issaccept(Request $request)
    {
       $this->redirect('index/issnew', ['id' =>$request->param('id'),'type' =>$request->param('type'),'role' =>"operator"]); 
       //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>IPMS V3<br/></p><span style="font-size:22px;"></span></div>';
    }
    
    //operator“填写报告”issue
     public function issreport(Request $request)
    {
        $this->redirect('index/issnew', ['id' =>$request->param('id'),'type' =>$request->param('type'),'role' =>"operator"]);
        //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>IPMS V3<br/></p><span style="font-size:22px;"></span></div>';
    }
    
    //operator“关闭事务”issue
     public function issclose(Request $request)
    {
        $this->redirect('index/issnew', ['id' =>$request->param('id'),'type' =>$request->param('type'),'role' =>"operator"]);
        //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>IPMS V3<br/></p><span style="font-size:22px;"></span></div>';
    }
    
    //显示issrecords
     public function issrecords(Request $request)
    {
        //通过$log判断是否是登录用户，非登录用户退回到登录页面
        if(1!==$this->log && !empty($request->param('id'))){
            $this->error('未登录用户，请先登录系统');
            //$this->redirect($request->domain());
        }else{
            $id=$request->param('id');
        }
        
        $iss = IssinfoModel::get($id);
        $topic=$iss->topic;
        $issrecords=$iss->issrecords;
         
        $this->assign([
        
        'home'=>$request->domain(),
        'username'=>$this->username,
        
        'topic'=>$topic,
       
        'issrecords'=>$issrecords,
        ]);
         
         return view();
         //return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
    }
    
    
    
    //issue信息显示
     public function issinfo(Request $request)
    {
        //通过$log判断是否是登录用户，非登录用户退回到登录页面
        if(1!==$this->log){
            $this->error('未登录用户，请先登录系统');
            //$this->redirect($request->domain());
        }else{
            $type=$request->param('type');
            $id=$request->param('id');
            $role=$request->param('role');
        }
        
        //若get的$role值为空以该用户的第一个系统角色进行赋值
        if(empty($role)){
            $role=$this->roles[0];
        }
        
        //根据$role值对$rolename进行赋值
            switch($role){
                case 'writer':
                    $rolename="撰写人";
                    $role='writer';
                break;
                            
                case 'reviewer':
                    $rolename="审查人";
                    $role='reviewer';
                break;
                            
                case 'approver':
                    $rolename="批准人";
                    $role='approver';
                break;
                
                case 'operator':
                    $rolename="执行人";
                    $role='operator';
                break;
                
                case 'maintainer':
                    $rolename="维护人";
                    $role='maintainer';
                break;
                            
                default:
                    $this->error('未授权用户，请先登录系统');
                break;   
            }
        
        if($id){
            //查出$id对应的issue记录，
            //预载入关联查询。根据Patinfo模型和Issinfo模型的一对一关系，
            //本文件中index操作里也有应用
            //$iss=IssinfoModel::get($id,'patinfo');
            
            $iss=IssinfoModel::get($id);
            
            //查出$iss->status对应的issrecord记录进行显示
            if($iss->status=="执行中"||$iss->status=="完成"){
                //查出符合2个条件的$iss->status对应的issrecord记录，选出ID最大的记录的“actdetail”字段值
                $maxid= $iss->issrecords()
                                ->where('issinfo_id',$iss->id)
                                ->where('act','批准')
                                ->max('id');
                                
                $issrecord= $iss->issrecords()
                                ->where('id',$maxid)
                                ->find();
                
                //operator还有“任务执行情况简报”要显示
                //查出符合2个条件的$iss->status对应的issrecord记录，选出ID最大的记录的“actdetail”字段值
                $maxid= $iss->issrecords()
                                ->where('issinfo_id',$iss->id)
                                ->where('act',$iss->status)
                                ->max('id');
                                
                $issrecord_operator= $iss->issrecords()
                                ->where('id',$maxid)
                                ->find();
                
                
            }else{
                //查出符合2个条件的$iss->status对应的issrecord记录，选出ID最大的记录的“actdetail”字段值
                $maxid= $iss->issrecords()
                                ->where('issinfo_id',$iss->id)
                                ->where('act',$iss->status)
                                ->max('id');
                                
                $issrecord= $iss->issrecords()
                                ->where('id',$maxid)
                                ->find();
            }
            
            
            //判断登录用户是否为该事务的“writer”或者为"审查人"、"批准人"、"执行人"、"维护人"
            if($iss->writer==$this->username or $rolename=="审查人" or $rolename=="批准人" or $rolename=="执行人" or $rolename=="维护人"){
                //查出$iss->issnum对应的attachment记录
                $atts=AttinfoModel::where('num_id',$iss->issnum)
                                    ->order('uploaddate asc')
                                    ->select();   
                
                $topic=$iss->topic;
                $reltopic=$iss->patinfo['topic'];
                $pat_id=$iss->patinfo['id'];
                $type=$iss->num_type;
                $sub_type=$iss->sub_num_type;
                $username=$this->username;
                $writer=$iss->writer;
                $dept=$iss->dept;
                $abstract=$iss->abstract;
                $num_id=$iss->num_id;
                $status=$iss->status;
                $addnewdate=$iss->addnewdate;
                $submitdate=$iss->submitdate;
                $auditrejectdate=$iss->auditrejectdate;
                $resultdate=$iss->resultdate;
                $finishdate=$iss->finishdate;
                $operatestartdate=$iss->operatestartdate;
                $issnum=$iss->issnum;
                
                 //根据$type的值选择不同的模板显示内容
                switch($type){
                    //项目类
                    case 1:
                        $typename="项目";
                        switch($sub_type){
                            case '1_1':
                                $typesubname="新增项目申请";
                    		break;
                    				
                    		case '1_2':
                    			$typesubname="项目一般申请";
                    		break;
                    				
                    		case '1_3':
                    			$typesubname="项目经费/预算申请";
                    		break;
                    				
                    		case '1_4':
                    			$typesubname="其他项目报告";
                    		break;
                        }
                        
                    break;
                    
                    //专利类            
                    case 2:
                        $typename="专利";
                        switch($sub_type){
                            case '2_1':
                                $typesubname="新增专利申请";
                    		break;
                    				
                    		case '2_2':
                    			$typesubname="专利一般申请";
                    		break;
                    				
                    		case '2_3':
                    			$typesubname="专利经费/预算申请";
                    		break;
                    				
                    		case '2_4':
                    			$typesubname="其他专利报告";
                    		break;
                        }
                        
                    break;
                    
                    //论文类            
                    case 3:
                        $typename="论文";
                        switch($sub_type){
                            case '3_1':
                                $typesubname="新增论文申请";
                    		break;
                    				
                    		case '3_2':
                    			$typesubname="论文一般申请";
                    		break;
                    				
                    		case '3_3':
                    			$typesubname="论文经费/预算申请";
                    		break;
                    				
                    		case '3_4':
                    			$typesubname="其他论文报告";
                    		break;
                        }
                    break;
                                
                    default:
                        $type=1;
                        $typename="项目";
                        $typesubname="新增项目申请";
                    break;   
                }
                
                if(empty($issrecord->actdetail)){
                    $actdetail=null;
                }else{
                    $actdetail=$issrecord->actdetail;
                }
                
                if(empty($issrecord_operator)){
                    $report=null;
                    $reportdate=null;
                }else{
                    $report=$issrecord_operator->actdetail;
                    $reportdate=$issrecord_operator->acttime;
                }
                
                
                
                $executer=$iss->executer;
                
                $flag=1; 
            }else{
                $this->error("【撰写人】仅能查看自己撰写的事务。");
           
            }
            
        }else{
            $topic=null;
            $reltopic=null;
            $pat_id=null;
            $username=$this->username;
            $writer=$username;
            $dept=$this->dept;
            $abstract=null;
            $num_id=null;
            $status="填报";
            $addnewdate=date("Y-m-d, H-i-s");
            $issnum=null;
            $atts=null;
            
            $report=null;
            $reportdate=null;
            $actdetail=null;
            $executer=null;
            
            $flag=0;
            
             //根据$type的值选择不同的模板显示内容
            switch($type){
                case 1:
                    $typename="项目";
                    $typesubname="新增项目申请";
                break;
                            
                case 2:
                    $typename="专利";
                    $typesubname="新增专利申请";
                break;
                            
                case 3:
                    $typename="论文";
                    $typesubname="新增论文申请";
                break;
                            
                default:
                    $type=1;
                    $typename="项目";
                    $typesubname="新增项目申请";
                break;   
            }
            
        }
        
        //--在index.html页面输出自定义信息的HTML代码块
        $destr= "请求方法:".$request->method()."</br>".
                "username:".$this->username."</br>".
                //"pwd:".sizeof($pwd);
                "pwd:".$this->pwd."</br>".
                "log:".$this->log."</br>".
                "topic:".$topic."</br>".
                "actdetail:".$actdetail."</br>";
                //"issrecord:".dump($issrecord)."</br>";
                //$request->param('pronum')."</br>";
                //"home:".$request->domain()."</br>";
        
        
        $this->assign([
        //在index.html页面通过'destr'输出自定义的信息
        'destr'=>$destr,
        //在index.html页面通过'array'输出自定义的数组内容
        'array'=>$this->roles, 
        
        'rolename'=>$rolename,
        'typename'=>$typename,
        'typesubname'=>$typesubname,
        'home'=>$request->domain(),
        'role'=>$role,
        
        'id'=>$id,
        'topic'=>$topic,
        'reltopic'=>$reltopic,
        'pat_id'=>$pat_id,
        'type'=>$type,
        'username'=>$username,
        'writer'=>$writer,
        'dept'=>$dept,
        'abstract'=>$abstract,
        'num_id'=>$num_id,
        'status'=>$status,
        'addnewdate'=>$addnewdate,
        'submitdate'=>$submitdate,
        'auditrejectdate'=>$auditrejectdate,
        'resultdate'=>$resultdate,
        'finishdate'=>$finishdate,
        'operatestartdate'=>$operatestartdate,
        'issnum'=>$issnum,
        'atts'=>$atts,
        
        'report'=>$report,
        'reportdate'=>$reportdate,
        'actdetail'=>$actdetail,
        'executer'=>$executer,
        
        'flag'=>$flag,
        
        'uploaddate'=>date("Y-m-d, H-i-s"),
        ]);
         return view();
         //return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
    }
    
}
