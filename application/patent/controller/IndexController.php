<?php
namespace app\patent\controller;

use think\Request;
use think\Session;
use think\View;

use app\patent\model\Patinfo as PatinfoModel;
use app\patent\model\Patrecord as PatrecordModel;
use app\issue\model\Issinfo as IssinfoModel;
use app\attachment\model\Attinfo as AttinfoModel;
use app\admin\model\Dept as DeptModel;

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
            $roleparam=$request->param('role');
            $active=$request->param('active');
        }
                       
        //$active的值不能超3或小1(因为系统默认每个角色需处理3种状态的事务，分别为1,2,3)，否则修改为1
        if($active>3 or $active<1){
            $active=1;
        }
        
        //判断$request->param('role')传来的role值是否为Session中存储的role值，否则报错退回到登录页面
        for($i = 0; $i < count($roles); $i++) {
            if($roleparam==$roles[$i]){
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

        //--在index.html页面输出自定义信息的HTML代码块        
		$destr= "请求方法:".$request->method()."</br>".
            "username:".$username."</br>".
            //"pwd:".sizeof($pwd);
            "pwd:".$pwd."</br>".
            "log:".$log."</br>".
            "roleparam:".$request->param('role').";active:".$request->param('active')."</br>";
        //--!       
        
        $this->assign([
          //在index.html页面通过'destr'输出自定义的信息
          'destr'=>$destr,
          //在index.html页面通过'array'输出自定义的数组内容
          'array'=>$roles, 
          
          'home'=>$request->domain(),
          'username'=>$username,
          'rolename'=>$rolename,
          'role'=>$roleparam,
          'active'=>$active,
          'year'=>date('Y')
        ]);
        return view();
    }
    
     //patent列表    
	public function patlist(Request $request)
    {
        $log=Session::get('log');
            
        //通过$log判断是否是登录用户，非登录用户退回到登录页面
        if(1!==$this->log){
            $this->error('未登录用户，请先登录系统');
            //$this->redirect($request->domain());
        }else{
            $username=Session::get('username');
            $pwd=Session::get('pwd');
            $roles=Session::get('role');
            //$listrows接收页面传来的分页时每页显示的记录数，初始值为10
            $listrows=10;
            if(!empty($request->param('listrows'))){
                $listrows=$request->param('listrows');
            }
        }
      
        //使用模型Patinfo
        $pats = new PatinfoModel;  
        
        //利用模型对象得到非“填报”状态的patent总数
        $numtotal=$pats->where('status','neq','填报')->count();
        //分页,每页$listrows条记录
		    $patstotal = $pats->where('status','neq','填报')
                            ->order('submitdate', 'desc')
                            ->paginate($listrows,false,['type'=>'bootstrap','var_page'=>'pagetotal',]);             
        // 获取分页显示
        $pagetotal = $patstotal->render(); 
        
        //利用模型对象得到状态status"="拟申报"）的patent总数
        $numnew=$pats->where('status','拟申报')->count();
        //分页,每页$listrows条记录
        $patsnew = $pats->where('status','拟申报')
                            ->order('submitdate', 'desc')
                            ->paginate($listrows,false,['type'=>'bootstrap','var_page'=>'pagenew',]);
        // 获取分页显示
        $pagenew = $patsnew->render(); 
        
        //利用模型对象得到状态status"="申报"）的patent总数
        $numapp = $pats->where('status',['=','申报'],['=','申报修改'],'or')->count();
        //分页,每页$listrows条记录
        $patsapp = $pats->where('status',['=','申报'],['=','申报修改'],'or')
                            ->order('applydate', 'desc')
                            ->paginate($listrows,false,['type'=>'bootstrap','var_page'=>'pageapp',]);
        // 获取分页显示
        $pageapp = $patsapp->render(); 
        
        //利用模型对象得到有效状态的patent总数
        $numaut = $pats->where('status',['=','授权'],['=','续费授权'],['=','续费中'],['=','放弃续费'],'or')->count();
        //分页,每页$listrows条记录
        $patsaut = $pats->where('status',['=','授权'],['=','续费授权'],['=','续费中'],['=','放弃续费'],'or')
                            ->order('authrejectdate', 'desc')
                           ->paginate($listrows,false,['type'=>'bootstrap','var_page' => 'pageaut',]);
        // 获取分页显示
        $pageaut = $patsaut->render();
        
        //利用模型对象得到状态status"="放弃"）的patent总数
        $numaba = $pats->where('status','放弃')->count();
        //分页,每页$listrows条记录
		    $patsaba = $pats->where('status','放弃')
                            ->order('nextrenewdate', 'desc')
                            ->paginate($listrows,false,['type'=>'bootstrap','var_page'=>'pagetoaba',]);
        // 获取分页显示
        $pageaba = $patsaba->render();
        
        //利用模型对象得到状态status"="驳回"）的patent总数
        $numrej = $pats->where('status','驳回')->count();
        //分页,每页$listrows条记录
        $patsrej = $pats->where('status','驳回')
                            ->order('nextrenewdate', 'desc')
                            ->paginate($listrows,false,['type'=>'bootstrap','var_page'=>'pagerej',]);
        // 获取分页显示
        $pagerej = $patsrej->render();
        
        //利用模型对象得到状态status"="续费"）的patent总数
        $numren = $pats->where('status',['=','续费授权'],['=','续费中'],'or')->count();
        //分页,每页$listrows条记录
        $patsren = $pats->where('status',['=','续费授权'],['=','续费中'],'or')
                            ->order('renewabandondate', 'desc')
                            ->paginate($listrows,false,['type'=>'bootstrap','var_page'=>'pageren',]);
        // 获取分页显示
        $pageren = $patsren->render();
        
        //利用正则表达式匹配出分页所在选项卡<li>,<div>标签的id所含关键字存入$pagex
        //分页页码存入$pageXnum
        //上述2类变量值需传入模板文件，用jQuery实现在选中分页所在选项卡内显示内容和连续编号值     
        $str1=$request->url();
        preg_match("/page\w{3,}/i",$str1,$matches);
        if(!empty($matches[0])){
            $pagex=$matches[0];
            
            switch($pagex){
                case "pagetotal":
                    $pagetotalnum=$request->param($pagex);
                    $pagenewnum=1;
                    $pageappnum=1;
                    $pageautnum=1;
                    $pagerennum=1;
                    $pageabanum=1;
                    $pagerejnum=1;
                break;
                case "pagenew":
                    $pagetotalnum=1;
                    $pagenewnum=$request->param($pagex);
                    $pageappnum=1;
                    $pageautnum=1;
                    $pagerennum=1;
                    $pageabanum=1;
                    $pagerejnum=1;
    			break;
                case "pageapp":
                    $pagetotalnum=1;
                    $pagenewnum=1;
                    $pageappnum=$request->param($pagex);
                    $pageautnum=1;
                    $pagerennum=1;
                    $pageabanum=1;
                    $pagerejnum=1;
                break;
                case "pageaut":
                    $pagetotalnum=1;
                    $pagenewnum=1;
                    $pageappnum=1;
                    $pageautnum=$request->param($pagex);
                    $pagerennum=1;
                    $pageabanum=1;
                    $pagerejnum=1;
                break;
                case "pageaba":
                    $pagetotalnum=1;
                    $pagenewnum=1;
                    $pageappnum=1;
                    $pageautnum=1;
                    $pagerennum=1;
                    $pageabanum=$request->param($pagex);
                    $pagerejnum=1;
                break;
                case "pagerej":
                    $pagetotalnum=1;
                    $pagenewnum=1;
                    $pageappnum=1;
                    $pageautnum=1;
                    $pagerennum=1;
                    $pageabanum=1;
                    $pagerejnum=$request->param($pagex);
                break;
                case "pageren":
                    $pagetotalnum=1;
                    $pagenewnum=1;
                    $pageappnum=1;
                    $pageautnum=1;
                    $pageabanum=1;
                    $pagerejnum=1;
                    $pagerennum=$request->param($pagex);
                break;
                default:
                break;
	       }
        }else{
            $pagex='pagetotal';
            $pagetotalnum=1;
            $pagenewnum=1;
            $pageappnum=1;
            $pageautnum=1;
            $pagerennum=1;
            $pageabanum=1;
            $pagerejnum=1;
        }
        
        //preg_match("/php/i", "PHP is the web scripting language of choice.", $matches);

        //--在index.html页面输出自定义信息的HTML代码块
		$destr= "请求方法:".$request->method()."</br>".
                "username:".$username."</br>".
                //"pwd:".sizeof($pwd);
                "pwd:".$pwd."</br>".
                "log:".$log."</br>";
//                "pathinfo(request->url()):".$request->url()."</br>".
//                "request->param():".implode(" ", $request->param())."</br>".
//                $pagex."</br>".
//                "listrows:".$listrows."||模板jQuery post:".$request->param('listrows')."</br>";
        //--!    
        
        $this->assign([
          //在index.html页面通过'destr'输出自定义的信息
          'destr'=>$destr,
          //在index.html页面通过'array'输出自定义的数组内容
          'array'=>$roles,  
                            
          'numtotal'=>$numtotal,
          'listtotal'=>$patstotal, 
          'pagetotal'=>$pagetotal,
          'pagetotalnum'=>$pagetotalnum,
          
          'numnew'=>$numnew,
          'listnew'=>$patsnew,
          'pagenew'=>$pagenew,
          'pagenewnum'=>$pagenewnum,
          
          'numapp'=>$numapp,
          'listapp'=>$patsapp,
          'pageapp'=>$pageapp,
          'pageappnum'=>$pageappnum,
          
          'numaut'=>$numaut,
          'listaut'=>$patsaut,
          'pageaut'=>$pageaut,
          'pageautnum'=>$pageautnum,
          
          'numaba'=>$numaba,
          'listaba'=>$patsaba,
          'pageaba'=>$pageaba,
          'pageabanum'=>$pageabanum,
          
          'numrej'=>$numrej,
          'listrej'=>$patsrej,
          'pagerej'=>$pagerej,
          'pagerejnum'=>$pagerejnum,
          
          'numren'=>$numren,
          'listren'=>$patsren,
          'pageren'=>$pageren,
          'pagerennum'=>$pagerennum,
          
          'pagex'=>$pagex,
          'listrows'=>$listrows,
          
          'home'=>$request->domain(),
          'username'=>$username,
          
          'year'=>date('Y')

        ]); 
        
        return view();
    }
    
    //role为writer才能增加新patent
    public function patnew(Request $request)
    {
        //通过$log判断是否是登录用户，非登录用户退回到登录页面
        if(1!==$this->log){
            $this->error('未登录用户，请先登录系统');
            //$this->redirect($request->domain());
        }else{
            //$this->success('成功领受任务。跳转到新专利填报页面。');
            $issinfo_id=$request->param('id');
            $role=$request->param('role');
            $roles=$this->roles;
            $patnum=$request->param('patnum');
        }
        
        //判断$roles值是否含有operator，不含就报错并退回到登录页面
        $i=0;
        foreach ($roles as $value) {
            if($value=="operator")
            $i=1;
        }
        if(1==$i){
            $rolename="执行人";
            $role="operator";
            //break;   
        }else{
            $this->error('未授权用户，请先登录系统');
        }
        
        //$issinfo_id在patinfo表中不存在，在issinfo表中已有的才是有效的值
        $pat = PatinfoModel::where('issinfo_id',$issinfo_id)->find();
        $iss = IssinfoModel::get($issinfo_id);
    
        if(!empty($iss) && empty($pat)){
            //return '<div style="padding: 24px 48px;"><h1>:) IF</h1><p>IPMS V3<br/></p><span style="font-size:22px;"></span></div>';
            //通过外部提交赋值给模型Patinfo类，由Patinfo类将数据写入数据库
            //$pat = new PatinfoModel($_POST); 
            //过滤post数组中的非数据表字段数据
            
            $pat = new PatinfoModel; 
            $pat->issinfo_id=$issinfo_id;
            $pat->issinfo_topic=$iss->topic;
            $pat->executer=$iss->executer;
            $pat->status='填报';
            $pat->addnewdate=date("Y-m-d, H:i:s");
            
            //过滤post数组中的非数据表字段数据
            $pat->allowField(true)->save(); 
            //获取自增ID值
            //$pat->id;
            
            
            
            //post来的“Pronum”是数组，要加“a”才能获取到。TP5.0默认是“s”(字符串)
            //$pronum = ($request->param('pronum/a'));
            
            //上述代码将数据写入数据库后，带参数'id'跳转到<{$home}>/patent/index/patmod操作页面
            //$this->redirect('index/patmod', ['id' =>$pat->id]);
            
            //$this->success('填报成功，进入修改页面', 'index/patmod')
            $this->success('新增专利成功。进入完善专利信息页面',url('Index/patmod', ['issinfo_id' => $pat->issinfo_id,'role'=>'operator']));
            //$this->redirect('index/patmod', ['id' =>$pat->issinfo_id]);
        }else{
            if(!empty($pat)){
                $patnum=$pat->patnum;
                
                //过滤post数组中的非数据表字段数据
                $iss->allowField(true)->save(); 
                
                $this->success('进入填报专利信息页面',url('Index/patmod', ['issinfo_id' => $pat->issinfo_id,'role'=>'operator']));
                //$this->redirect('index/patnew', ['id' =>$pat->issinfo_id]);
            }
            
        }
        
        //--在index.html页面输出自定义信息的HTML代码块
        $destr= "请求方法:".$request->method()."</br>".
                "username:".$this->username."</br>".
                //"pwd:".sizeof($pwd);
                "pwd:".$this->pwd."</br>".
                "log:".$this->log."</br>";
                //$request->param('pronum')."</br>";
                //"home:".$request->domain()."</br>";
        
        
        $this->assign([
        //在index.html页面通过'destr'输出自定义的信息
        'destr'=>$destr,
        //在index.html页面通过'array'输出自定义的数组内容
        'array'=>$roles, 
        
        'home'=>$request->domain(),
        'username'=>$this->username,
        'rolename'=>$rolename,
        'role'=>$role,
        'patnum'=>$patnum,
        'today'=>date("Y-m-d, H:i:s"),
//        'active'=>$active,
        ]);
        return view();
    }
    
    //role为operator才能修改状态为“填报”、“新增”、“返回修改”的patent
    public function patmod(Request $request)
    {
        
        //通过$log判断是否是登录用户，非登录用户退回到登录页面
        if(1!==$this->log){
            $this->error('未登录用户，请先登录系统',url($request->domain()));
            //$this->redirect($request->domain());
        }else{
            $username=$this->username;
            $role=$request->param('role');
            //Issue/index/index.html或本控制器的patnew操作发送来'issinfo_id'
            $issinfo_id=$request->param('issinfo_id');
            $module=$request->param('module');
           
        }
        
        //判断$roles值是否含有operator，不含operator就报错并退回到登录页面
        $i=0;
        foreach ($this->roles as $value) {
            if($value==$role)
            $i=1;
        }
        if(1==$i){
            $rolename="执行人";
            $role="operator";
            //break;   
        }else{
            $this->error('未授权用户，请先登录系统');
        }
        
        $pat = PatinfoModel::where('issinfo_id',$issinfo_id)->find();
        $status=$pat->status;
        //利用用模型关联，查出$issnum、$iss_topic 
        $issnum=$pat->issinfo->issnum;
        $iss_topic=$pat->issinfo->topic;
        $iss_id=$pat->issinfo->id;
        
        
        //利用模型直接查出$issnum、$iss_topic 
        //$iss = IssinfoModel::get($pat->issinfo_id);
        //$issnum=$iss->issnum;
        //$iss_topic=$iss->topic;
        
        //查出$pat->patnum对应的attachment记录
        $atts=AttinfoModel::where('num_id',$pat->patnum)
                                    ->order('uploaddate asc')
                                    ->select();   

        if(count($pat->pronum)){
            $flag=count($pat->pronum);
        }else{
            $flag=0;
        }
        
        //$pat必须存在，并且登录用户名$this->username必须与$pat->executer一致
        if(!empty($pat) && ($this->username==$pat->executer)){
            //--在index.html页面输出自定义信息的HTML代码块
            $destr= "请求方法:".$request->method()."</br>".
                    "username:".$username."</br>";
                    //"pat:".$pat['id']."</br>"
                    //$request->param('pronum')."</br>";
                    //"home:".$request->domain()."</br>";
            
            
            $this->assign([
            //在index.html页面通过'destr'输出自定义的信息
            'destr'=>$destr,
            //在index.html页面通过'array'输出自定义的数组内容
            'array'=>$pat, 
            
            'home'=>$request->domain(),
            'username'=>$username,
            'rolename'=>$rolename,
            'role'=>$role,
            
            'id'=>$pat->id,
            'topic'=>$pat->topic,
            'type'=>$pat->type,
            'inventor'=>$pat->inventor,
            'otherinventor'=>$pat->otherinventor,
            'issnum'=>$issnum,
            'iss_topic'=>$iss_topic,
            'iss_id'=>$iss_id,
            'keyword'=>$pat->keyword,
            'summary'=>$pat->summary,
            'status'=>$status,
            'patnum'=>$pat->patnum,
            'flag'=>$flag,
            'pronum'=>$pat->pronum,
            'today'=>date("Y-m-d, H:i:s"),
            'addnewdate'=>$pat->addnewdate,
            'submitdate'=>$pat->submitdate,
            'applydate'=>$pat->applydate,
            'modsuggestion'=>$pat->note,
            'module'=>$module,
            'dept'=>$pat->dept,
            
            //modal用
            'uploaddate'=>date("Y-m-d, H:i:s"),
            'type'=>2,
            
            'atts'=>$atts,
            
    //        'active'=>$active,
            ]);
            
            
            return view();
        }else{
            $this->error('非法操作。');
        }
        
        
    }
    
    public function patsave(Request $request)
    {
        if(1!==$this->log){
            $this->error('未登录用户，请先登录系统',url($request->domain()));
            //$this->redirect($request->domain());
        }else{
            $role=$request->param('role');
            
            switch($role){
                //operator保存专利信息时的验证
                case 'operator':
                    $rolename='执行人';
                    $data=[
                        'topic'=>$request->param('topic'),
                        'inventor'=>$request->param('inventor'),
                        'otherinventor'=>$request->param('otherinventor'),
                        'keyword'=>$request->param('keyword'),
                        'summary'=>$request->param('summary'),
                        'pronum'=>$request->param('patnum'),
                    ];
                
                    //使用自定义的validate类“Ipvalidate”进行浏览器端验证，类文件目录：application\common\validate
                    //也可直接在页面利用jQuery进行验证；<input type="text" required>
                    $result=$this->validate($data,'Ipvalidate.patmod');
                break;
                
                //maintainer保存专利信息时的验证
                case 'maintainer':            
                    $rolename='维护人';
                    $data=[
                        'patapplynum'=>$request->param('patapplynum'),
                        'patagency'=>$request->param('patagency'),
                        'patadmin'=>$request->param('patadmin'),
                    ];
                
                    //使用自定义的validate类“Ipvalidate”进行浏览器端验证，类文件目录：application\common\validate
                    //也可直接在页面利用jQuery进行验证；<input type="text" required>
                    //$result=$this->validate($data,'Ipvalidate.patmaintain');
                    $result=1;
                break;
                
                
            }
            
            if(true!=$result){
                //验证失败输出提示错误信息
                $this->error($result);
                //返回登录界面
            }else{
                //直接更新数据
                $pat = PatinfoModel::where('patnum',$request->param('patnum'))->find();         
                $num=$pat->patnum;
                $status_org=$pat->status;
                $topic=$pat->topic;
                $patinfo_id=$pat->id;
                //过滤post数组中的非数据表字段数据
                //$pat->allowField(true)->save($_POST,['patnum' => $request->param('patnum')]);
                $pat->allowField(true)->data($_POST,true)->save();
                
                //向patrecord表中写入本次状态改变的关键信息           
                $record = new PatrecordModel;
                 
                //通过外部提交赋值给模型Patrecord类，由Patrecord将数据写入数据表
                $data_record=array("num"=>$num,
                                    "topic"=>$topic,
                                    "act"=>$request->param('status'),
                                    "actdetail"=>"专利状态改变，由".$status_org."->".$request->param('status'),
                                    "username"=>$this->username,
                                    "rolename"=>$rolename,
                                    "att_name"=>"无",
                                    "actime"=>date("Y-m-d, H:i:s"),
                                    "note"=>$request->param('note'),
                                    "patinfo_id"=>$patinfo_id);
                
                //过滤数组中的非数据表字段数据后写入数据表
                $record->allowField(true)->data($data_record,true)->save();
                
                
                switch($role){
                    //operator保存专利信息时的验证
                    case 'operator':
                        //operator新增专利后修改Issinfo表中对应issue的num_id和xinfo_topic字段值
                        $iss = IssinfoModel::get($pat->issinfo_id); 
                        $iss->num_id=$pat->patnum;
                        $iss->xinfo_topic=$pat->topic;
                        $iss->allowField(true)->save();   
                        $this->success('专利信息已保存',url('Index/patmod', ['issinfo_id' => $pat->issinfo_id,'role'=>$role]));
                    break;
                    
                    //maintainer保存专利信息时的验证
                    case 'maintainer':  
                        $this->success('专利信息已保存');
                    break;
                }
                     
                
            }
    
        }    
        
        
    }
    
    
    public function patinfo(Request $request)
    {
        //通过$log判断是否是登录用户，非登录用户退回到登录页面
        if(1!==$this->log){
            $this->error('未登录用户，请先登录系统');
            //$this->redirect($request->domain());
        }else{
            //
            if(!empty($request->param('id'))){
                $id=$request->param('id');
            }else{
                $patnum=$request->param('num');
                $pat = PatinfoModel::where('patnum',$patnum)->find();
                $id=$pat->id;
            }
        }
        
        $pat = PatinfoModel::get($id); 
        //利用用模型关联，查出$issnum、$iss_topic
        if(!empty($pat->issinfo_id)){
            $issnum=$pat->issinfo->issnum;
            $iss_topic=$pat->issinfo_topic;
            $iss_id=$pat->issinfo_id;
            
        } else{
            $issnum=0;
            $iss_topic='无';
            $iss_id=0;
            
        }
        
        
        //--在index.html页面输出自定义信息的HTML代码块
        $destr= "请求方法:".$request->method()."</br>".
                "username:".$this->username."</br>";
        
        if($pat){
        
        //查出$pat->patnum对应的attachment记录
        $atts=AttinfoModel::where('num_id',$pat->patnum)
                                    ->order('uploaddate asc')
                                    ->select();   
        $this->assign([
            //在index.html页面通过'destr'输出自定义的信息
            'destr'=>$destr,
            //在index.html页面通过'array'输出自定义的数组内容
            'array'=>$this->roles, 
            
            'home'=>$request->domain(),
            'username'=>$this->username,
            
            //专利信息
            'id'=>$id,
            'topic'=>$pat->topic, 
            'type'=>$pat->type, 
            'author'=>$pat->author, 
            'dept'=>$pat->dept, 
            'keyword'=>$pat->keyword, 
            'summary'=>$pat->summary, 
            'pronum'=>$pat->pronum, 
            'patnum'=>$pat->patnum, 
            'status'=>$pat->status, 
            'patowner'=>$pat->patowner, 
            'inventer'=>$pat->inventor, 
            'otherinventer'=>$pat->otherinventor, 
            'issnum'=>$issnum,
            'iss_topic'=>$iss_topic,
            'iss_id'=>$iss_id,
            
            'addnewdate'=>$pat->addnewdate, 
            'update_time'=>$pat->update_time, 
            'submitdate'=>$pat->submitdate,
            'applydate'=>$pat->applydate, 
            'nextrenewdate'=>$pat->nextrenewdate, 
            'authrejectdate'=>$pat->authrejectdate,
            'renewabandondate'=>$pat->renewabandondate, 
            
            'patapplynum'=>$pat->patapplynum,
            'patauthnum'=>$pat->patauthnum,
            'patagency'=>$pat->patagency,
            'patadmin'=>$pat->patadmin,
            'abandonrejectreason'=>$pat->abandonrejectreason,
            
            //专利附件信息
            'atts'=>$atts,
            
            'year'=>date('Y')
        
            ]);
            return view();
        }else{
            $this->error('系统错误：patID不存在。');
            
        }
        //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>IPMS V3<br/></p><span style="font-size:22px;"></span></div>';

    }
   
    //operator【执行人】提交专利  
    public function patsubmit(Request $request)
    {
          if(1!==$this->log){
            $this->error('未登录用户，请先登录系统',url($request->domain()));
            //$this->redirect($request->domain());
        }else{
            $id=$request->param('id');
        
        }
        
        //判断$roles值是否含有operator，不含就报错并退回到登录页面
        $i=0;
        foreach ($this->roles as $value) {
            if($value==$role)
            $i=1;
        }
        if(1==$i){
            $rolename="执行人";
            $role="operator";
            //break;   
        }else{
            $this->error('未授权用户，请先登录系统');
        }
        
        $pat=PatinfoModel::get($id);
        $status_org=$pat->status;
        
        switch($pat->status){
            
            case '填报':
                $pat->status='新增';
                $pat->submitdate=date("Y-m-d, H:i:s");
            break;
            
            case '返回修改':
                $pat->status='申报';
               
            break;
            
            default:
            
            break;
            
        }
        //过滤post数组中的非数据表字段数据后写入数据表
        $pat->allowField(true)->save(); 
                    
        //向patrecord表中写入本次状态改变的关键信息           
        $record = new PatrecordModel;
 
        //通过外部提交赋值给模型Patrecord类，由Patrecord将数据写入数据表
        $data=array("num"=>$pat->patnum,
                    "topic"=>$pat->topic,
                    "act"=>$pat->status,
                    "actdetail"=>"专利状态改变，由".$status_org."->".$pat->status,
                    "username"=>$this->username,
                    "rolename"=>$rolename,
                    "att_name"=>"无",
                    "actime"=>date("Y-m-d, H:i:s"),
                    "note"=>$pat->note);

        //过滤数组中的非数据表字段数据后写入数据表
        $record->allowField(true)->data($data,true)->save();

        $this->success('专利已提交，由【维护人】进行下一步处理。',url('Index/patmod', ['issinfo_id' => $pat->issinfo_id,'role'=>'operator']));
        
    }
    
    //maintainer【维护人】维护专利  
    public function patmaintain(Request $request)
    {
        if(1!==$this->log){
            $this->error('未登录用户，请先登录系统',url($request->domain()));
            //$this->redirect($request->domain());
        }else{
            $username=$this->username;
            $role=$request->param('role');
            $id=$request->param('id');
            
        }
        
        //判断$roles值是否含有maintainer，不含就报错并退回到登录页面
        $i=0;
        foreach ($this->roles as $value) {
            if($value==$role)
            $i=1;
        }
        if(1==$i){
            $rolename="维护人";
            $role="maintainer";
            //break;
               
        }else{
            $this->error('未授权用户，请先登录系统');
        }
        
        $pat = PatinfoModel::get($id);
        $status=$pat->status;
        //利用用模型关联，查出$issnum、$iss_topic
        if(!empty($pat->issinfo_id)){
            $issnum=$pat->issinfo->issnum;
            $iss_topic=$pat->issinfo_topic;
            $iss_id=$pat->issinfo_id;
            
        } else{
            $issnum=0;
            $iss_topic='无';
            $iss_id=0;
            
        }
            
        //查出$pat->patnum对应的attachment记录
        $atts=AttinfoModel::where('num_id',$pat->patnum)
                                        ->order('uploaddate asc')
                                        ->select();  
        
        if(count($pat->pronum)){
            $flag=count($pat->pronum);
        }else{
            $flag=0;
        }
        
        if(!empty($pat)){
            //--在index.html页面输出自定义信息的HTML代码块
            $destr= "请求方法:".$request->method()."</br>".
                    "username:".$username."</br>";
                    //"pat:".$pat['id']."</br>"
                    //$request->param('pronum')."</br>";
                    //"home:".$request->domain()."</br>";
            
            
            $this->assign([
            //在index.html页面通过'destr'输出自定义的信息
            'destr'=>$destr,
            //在index.html页面通过'array'输出自定义的数组内容
            'array'=>$pat, 
            
            'home'=>$request->domain(),
            'username'=>$username,
            'rolename'=>$rolename,
            'role'=>$role,
            
            'id'=>$pat->id,
            'topic'=>$pat->topic,
            'author'=>$pat->author,
            'dept'=>$pat->dept,
            'patowner'=>$pat->patowner,
            'type'=>$pat->type,
            'inventor'=>$pat->inventor,
            'otherinventor'=>$pat->otherinventor,
            'issnum'=>$issnum,
            'iss_topic'=>$iss_topic,
            'iss_id'=>$iss_id,
            'keyword'=>$pat->keyword,
            'summary'=>$pat->summary,
            'status'=>$status,
            'patnum'=>$pat->patnum,
            'flag'=>$flag,
            'pronum'=>$pat->pronum,
            'today'=>date("Y-m-d, H:i:s"),
            'addnewdate'=>$pat->addnewdate,
            'submitdate'=>$pat->submitdate,
            'applydate'=>$pat->applydate,
            'modifydate'=>$pat->modifydate,
            'authrejectdate'=>$pat->authrejectdate,
            'nextrenewdate'=>$pat->nextrenewdate,
            'renewdeadlinedate'=>$pat->renewdeadlinedate,
            'renewabandondate'=>$pat->renewabandondate,
            'patapplynum'=>$pat->patapplynum,
            'patauthnum'=>$pat->patauthnum,
            'patagency'=>$pat->patagency,
            'patadmin'=>$pat->patadmin,
            'abandonrejectreason'=>$pat->abandonrejectreason,
            'modsuggestion'=>$pat->note,
            
            //'module'=>$module,
            
            //modal用
            'uploaddate'=>date("Y-m-d, H:i:s"),
            'type'=>2,
            
            'atts'=>$atts,
            
    //        'active'=>$active,
            ]);
          
       
            return view();
        }else{
            $this->error('非法操作。');
        }
    
    }
    
    //maintainer【维护人】专利已向外申请 
    public function patapply(Request $request)
    {
        //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';
        
        if(1!==$this->log){
            $this->success('未登录用户，请先登录系统',url($request->domain()));
            //$this->redirect($request->domain());
        }else{
            $id=$request->param('id');
        }
        
        $pat=PatinfoModel::get($id);
        
        $pat->status='申报';
        $pat->applydate=date("Y-m-d, H:i:s");
            
        //过滤post数组中的非数据表字段数据
        $pat->allowField(true)->save(); 
        
        $this->success('专利已申报，由【维护人】进行下一步处理。',url('Index/patinfo', ['id' => $pat->id,'role'=>'maintainer']));
        
    }
    
     //显示patrecords
     public function patrecords(Request $request)
    {
        //return '<div style="padding: 24px 48px;"><h1>:)</h1><p>模块开发中……<br/></p></div>';
        
        //通过$log判断是否是登录用户，非登录用户退回到登录页面
        if(1!==$this->log && !empty($request->param('id'))){
            $this->error('未登录用户，请先登录系统');
            //$this->redirect($request->domain());
        }else{
            $id=$request->param('id');
        }
        
        $pat = PatinfoModel::get($id);
        $topic=$pat->topic;
        $patrecords=$pat->patrecords;
         
        $this->assign([
        
        'home'=>$request->domain(),
        'username'=>$this->username,
        
        'topic'=>$topic,
       
        'patrecords'=>$patrecords,
        ]);
         
         return view();

    }
    
    // 输出total模板
    public function total(Request $request)
    {
      
      //通过$log判断是否是登录用户，非登录用户退回到登录页面
      if(1!==$this->log){
        $this->error('未登录用户，请先登录系统');
      //$this->redirect($request->domain());
      }else{
        //$totalTableRows接收前端页面传来的分页时每页显示的记录数，默认为10
        if(!empty($request->param('totalTableRows'))){
          $totalTableRows=$request->param('totalTableRows');
        }else{
          $totalTableRows=10;
        }
        
         // 接收前端分页页数变量：“pageUserNum”
        if(!empty($request->param('pageTotalNum'))){
          $pageTotalNum=$request->param('pageTotalNum');
        }else{
          $pageTotalNum=1;
        }
        
        // $sortName接收前端页面传来的排序字段名
        if(!empty($request->param('sortName'))){
          $sortName=$request->param('sortName');
        }else{
          $sortName='_PATNAME';
        }
        
        // $sort接收前端页面传来的排序顺序
        if(!empty($request->param('sort'))){
          $sort=$request->param('sort');
        }else{
          $sort='_ASC';
        }
        
        // $patStatus接收前端页面传来的专利状态值
        if(!empty($request->param('patStatus'))){
          $patStatus=$request->param('patStatus');
        }else{
          $patStatus=0;
        }
        
         // 查询词1，'searchPatName'
        if(!empty($request->param('searchPatName'))){
          $searchPatName=$request->param('searchPatName');
        }else{
          $searchPatName='';
        } 
        
        // 查询词2，'searchDept'
        if(!empty($request->param('searchDept'))){
          $searchDept=$request->param('searchDept');
        }else{
          $searchDept=0;
        } 
        
        // 查询词3，'searchPatStatus'
        if(!empty($request->param('searchPatStatus'))){
          $searchPatStatus=$request->param('searchPatStatus');
        }else{
          $searchPatStatus=0;
        }
        
        // 查询词4，'searchPatType'
        if(!empty($request->param('searchPatType'))){
          $searchPatType=$request->param('searchPatType');
        }else{
          $searchPatType=0;
        } 
        
        // 查询词5，'searchWriter'
        if(!empty($request->param('searchWriter'))){
          $searchWriter=$request->param('searchWriter');
        }else{
          $searchWriter='';
        }  
        
      }
      
      // 选择排序字段
      switch($sortName){
        case '_PATNAME':
          $strOrder='topic';
        break;
            
        case '_PATTYPE':
          $strOrder='type';
        break;
        
        case '_AUTHOR':
          $strOrder='author';
        break;
        
        case '_INVENTOR':
          $strOrder='inventor';
        break;
            
        case '_PATOWNER':
          $strOrder='patowner';
        break;
            
        case '_SUBMITDATE':
          $strOrder='submitdate';
        break;
            
        case '_DEPT':
          $strOrder='dept';
        break;
            
        case '_PROJECT':
          $strOrder='pronum';
        break;
        
        case '_PATSTATUS':
          $strOrder='status';
        break;
            
        //默认按字段“topic”
        default:
          $strOrder='topic';  
          $sortName="_PATNAME";
        break;
      } 
      
      //  组合升序or降序查询
      if($sort=="_ASC"){
          $strOrder=$strOrder.' asc';
      }else{
          $strOrder=$strOrder.' desc';
          
      }
      
      // 组合状态查询条件，
      switch($patStatus){
        case '#total':
          $map='';
          
          // 5个查询词
          if($searchDept){
            $map['dept']=$searchDept;
          }
          
          if($searchPatName){
            $map['topic']=['like','%'.$searchPatName.'%'];
          }
          
          if($searchPatStatus){
            $map['status']=$searchPatStatus;
          }
          
          if($searchPatType){
            $map['type']=$searchPatType;
          }
          
          if($searchWriter){
            $map['author']=['like','%'.$searchWriter.'%'];
          }
          
        break;
        // '内审'   
        case '#audit':
          $map['status'] =['in',['内审','内审否决','内审修改']];
        break;
        // '拟申报'   
        case '#newAdd':
          $map['status'] ='拟申报';
        break;
        // ''    
        case '#apply':
          $map['status'] =['in',['申报修改','申报']];
        break;
        // ''    
        case '#authorize':
          $map['status'] =['in',['授权','续费授权','续费中','放弃续费']];
        break;
        // ''    
       // case '#render':
//          $map['status'] =['in',['续费授权','续费']];
//        break;
        // ''    
        case '#abandon':
          $map['status'] ='放弃';
        break;
        // ''
        case '#reject':
          $map['status'] ='驳回';
        break;
        
        case '#invalid':
          $map['status'] =['in',['超期无效','驳回']];
        break;
            
        //默认所有状态专利'#total':
        default:
          $map='';
        break;
      }  
         
      //使用模型Patinfo
      $pats = new PatinfoModel;
      
      // 查出所有的用户并分页，根据“strOrder”排序，前端页面显示的锚点（hash值）为$fragment，设定分页页数变量：“pageTotalNum”
      // 带上每页显示记录行数$totalTableRows，实现查询结果分页显示。
      $patTotal = $pats->where('id','>',0)
                        ->where($map)
                        ->order($strOrder)
                        ->paginate($totalTableRows,false,['type'=>'bootstrap','fragment'=>$patStatus,'var_page' => 'pageTotalNum',
                        'query'=>['totalTableRows'=>$totalTableRows]]);
                        
      // 获取分页显示
      $pageTotal = $patTotal->render();
      // 记录总数
      $numTotal = $pats->where('id','>',0)->where($map)->count();
      
      $this->assign([
              'home'=>$request->domain(),
              
              // 分页显示所需参数
              'patTotal'=>$patTotal,
              'numTotal'=>$numTotal,
              'pageTotal'=>$pageTotal,
              'totalTableRows'=>$totalTableRows,
              'pageTotalNum'=>$pageTotalNum,
              
              // 表格搜索字段
              'searchPatName'=>$searchPatName,
              'searchDept'=>$searchDept,
              'searchPatStatus'=>$searchPatStatus,
              'searchPatType'=>$searchPatType,
              'searchWriter'=>$searchWriter,
              
    
              
              // 表格排序信息
              'sortName'=>$sortName,
              'sort'=>$sort,
              'totalTableRows'=>$totalTableRows,
              
              // 所return的页面显示的pat状态值$patStatus
              'patStatus'=>$patStatus,
              
        ]);
      return view();
    }
    
    // 获取所有部门信息,不能写成“_dept”，因为前端的HTML文件中的url里不能含有“_”开头的名称，否则就无法访问到，会报错
    public function dept()
    {
      //通过$log判断是否是登录用户，非登录用户退回到登录页面
        if(1!==$this->log){
            $this->error('未登录用户，请先登录系统');
            //$this->redirect($request->domain());
        }else{
            $dept=DeptModel::all();
            // 将数组转化为json
            return json($dept);
        }
    }
    
    // 获取所有专利状态信息,不能写成“_status”，因为前端的HTML文件中的url里不能含有“_”开头的名称，否则就无法访问到，会报错
    public function patStatus()
    {
      //通过$log判断是否是登录用户，非登录用户退回到登录页面
        if(1!==$this->log){
            $this->error('未登录用户，请先登录系统');
            //$this->redirect($request->domain());
        }else{
            $status=PatinfoModel::field('status')->group('status')->select();
            return $status;
        }
    }
    
    // 获取所有专利类型信息,不能写成“_type”，因为前端的HTML文件中的url里不能含有“_”开头的名称，否则就无法访问到，会报错
    public function patType()
    {
      //通过$log判断是否是登录用户，非登录用户退回到登录页面
        if(1!==$this->log){
            $this->error('未登录用户，请先登录系统');
            //$this->redirect($request->domain());
        }else{
            $patType=PatinfoModel::field('type')->group('type')->select();
            return $patType;
        }
    }

}
