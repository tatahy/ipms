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
    #用户名
    private $username = null;
    #用户密码
    private $pwd = null;
    #用户登录状态
    private $log = null;
    #用户角色
    private $roles=array();
    #用户所在部门
    private $dept = null;
    #请求对象域名
    private $home = '';
    #排序数组
    private $sortData=array();
    #patent的period与status的对应关系，本应用common.php中定义
    const PATPERIODSTATUS=conPatPeriodVsStatus;
    // 初始化
    protected function _initialize()
    {
        $this->username=Session::get('username');
        $this->pwd=Session::get('pwd');
        $this->log=Session::get('log');
        $this->roles=Session::get('role');
        $this->dept=Session::get('dept');
        #继承了控制器基类Controller后，直接可使用其request属性来使用Request类的实例。
        $this->home=$this->request->domain();
        #排序初始值
        $this->sortData=array('listRows'=>10,'sortName'=>'topic','sortOrder'=>'asc','pageNum'=>1,
                          'showId'=>0);
    }
    private function priLogin()
    {
        //通过$log判断是否是登录用户，非登录用户退回到登录页面
        if(1!==$this->log){
            $this->error('未登录用户，请先登录系统');
            //$this->redirect($request->domain());
        }
    }
    
    public function index(Request $request,PatinfoModel $patMdl)
    {
      $this->priLogin();
      $numArr=$patMdl::getPeriodNum(); 
      $periodArr=self::PATPERIODSTATUS;
      foreach($periodArr as $key=>$val){
        $periodArr[$key]['num']=$numArr[$key];
        unset($periodArr[$key]['status']);
        unset($periodArr[$key]['chi']);
      }
         
      $this->assign([
        'home'=>$this->home,
        #各个period的数量及名称
        'periodProp'=>json_encode($periodArr,JSON_UNESCAPED_UNICODE),
        #排序数组
        'sortData'=>json_encode($this->sortData,JSON_UNESCAPED_UNICODE),
        'username'=>$this->username,
        'year'=>date('Y')
      ]);
      return view();
    }
       
    #patent列表    
    public function patList(Request $request,PatinfoModel $patMdl)
    {
      $this->priLogin();    
      #
      $period=!empty($request->param('period'))?$request->param('period'):'total';
      #接收前端的排序参数数组
      $sortData=!empty($request->param('sortData/a'))?$request->param('sortData/a'):$this->sortData;
      $sortData=array_merge($this->sortData,$sortData);
      #接收前端的搜索参数数组，由前端保证传来的搜索参数值非0，非空。
      $searchData=!empty($request->param('searchData/a'))?$request->param('searchData/a'):array();
      $searchData=array_merge([],$searchData);
      #查询、排序结果总数
      $searchResultNum=0;
      #返回前端进行显示的内容
      $patList=array();
      #进行搜索的条件数组
      $whereArr=[];
      
      #前端输入的关键字搜索,like关键字，2个
      $whereArr['topic']=!empty($searchData['topic'])?['like','%'.$searchData['topic'].'%']:'';
      $whereArr['author']=!empty($searchData['author'])?['like','%'.$searchData['author'].'%']:'';
      
      #前端select值搜索，=select值(兼容select标签的multiple属性设置)，3个
      $whereArr['dept']=!empty($searchData['dept'])?['in',$searchData['dept']]:'';
      $whereArr['status']=!empty($searchData['status'])?['in',$searchData['status']]:'';
      $whereArr['type']=!empty($searchData['type'])?['in',$searchData['type']]:'';
      
      #将空白元素删除
      foreach($whereArr as $key=>$val){
        if(empty($val)){
          unset($whereArr[$key]);
        }
      }
      
      #pat模型对象，查询、排序用
      $queryBase=$patMdl->getPeriodSql($period)
                        ->where($whereArr)
                        ->order($sortData['sortName'],$sortData['sortOrder']);
      
      #查询、排序结果数据集：
      $baseSet=$queryBase->select();
      $baseSet=is_array($baseSet)?collection($baseSet):$baseSet;
      
      #查询、排序结果总数
      $searchResultNum=count($baseSet);
      
      if($searchResultNum){
        #本页要显示的记录
        $patList=$baseSet->slice(($sortData['pageNum']-1)*$sortData['listRows'],$sortData['listRows']);
      }
      
      #pat模型对象，排序、查询后分页用
      $pageQuery=$patMdl->where('id','in',$baseSet->column('id'))
                      ->order($sortData['sortName'],$sortData['sortOrder']);
      #分页对象，符合查询条件的所有iss记录分页,每页$listRows条记录
      $pageSet=$pageQuery->paginate($sortData['listRows'],false,['type'=>'bootstrap','var_page' =>'pageNum','page'=>$sortData['pageNum'],
                        'query'=>['listRows'=>$sortData['listRows']]]);
      
      $this->assign([
        'home'=>$this->home,
        'searchResultNum'=>$searchResultNum,
        #当前页显示内容
        'patList'=>$patList,
        #分页对象
        'pageSet'=>$pageSet,
        #排序数组
        'sortData'=>$sortData,
        #搜索数组。JSON_UNESCAPED_UNICODE，保持编码格式。若前端文件采用utf-8编码，汉字就可直接解析显示。
        'searchData'=>json_encode($searchData,JSON_UNESCAPED_UNICODE),
      ]);
      return view();
    }
    #parSearchForm
    public function patSearchForm(Request $request)
    {
      $this->priLogin();
      
      #接收前端的搜索参数数组，由前端保证传来的搜索参数值非0，非空。
      $searchData=!empty($request->param('searchData/a'))?$request->param('searchData/a'):array();
      $searchData=array_merge(['topic'=>'','author'=>'','type'=>0,'dept'=>0,'status'=>0],$searchData);    
      
      $this->assign([
        'numTotal'=>1,
        'searchData'=>json_encode($searchData,JSON_UNESCAPED_UNICODE),
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
    
    #输出total模板
    public function total(Request $request,PatinfoModel $patMdl)
    {
      
      #通过$log判断是否是登录用户，非登录用户退回到登录页面
      if(1!==$this->log){
        return $this->error('未登录用户，请先登录系统');
      //$this->redirect($request->domain());
      }
      
      $searchDefaults=array();
      $sortDefaults=array('listRows'=>10,'sortName'=>'topic','sortOrder'=>'asc','pageNum'=>1,
                          'showId'=>0,'period'=>'#total');
      
      #接收前端的排序参数数组
      $sortData=!empty($request->param('sortData/a'))?$request->param('sortData/a'):$sortDefaults;
      $sortData=array_merge($sortDefaults,$sortData);
      #接收前端的搜索参数数组，由前端保证传来的搜索参数值非0，非空。
      $searchData=!empty($request->param('searchData/a'))?$request->param('searchData/a'):$searchDefaults;
      $searchData=array_merge($searchDefaults,$searchData);
      
      #返回前端进行显示的内容
      $patList=array();
      #进行搜索的条件数组
      $whereArr=[];
      $map=[];
      
      #前端输入的关键字搜索,like关键字，2个
      $whereArr['topic']=!empty($searchData['topic'])?['like','%'.$searchData['topic'].'%']:'';
      $whereArr['author']=!empty($searchData['author'])?['like','%'.$searchData['author'].'%']:'';
      
      #前端select值搜索，=select值(兼容select标签的multiple属性设置)，3个
      $whereArr['dept']=!empty($searchData['dept'])?['in',$searchData['dept']]:'';
      $whereArr['status']=!empty($searchData['status'])?['in',$searchData['status']]:'';
      $whereArr['type']=!empty($searchData['type'])?['in',$searchData['type']]:'';
      
      # 组合状态查询条件，
      switch($sortData['period']){
        case '#total':
          $map='';
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
      
      #将空白元素删除
      foreach($whereArr as $key=>$val){
        if(empty($val)){
          unset($whereArr[$key]);
        }
      }
      
      #将空白元素删除
      if(!empty($map)){
        foreach($map as $key=>$val){
          if(empty($val)){
            unset($map[$key]);
          }
        } 
      }
       
         
      #pat模型对象，查询、排序用
      $queryBase=$patMdl->where('id','>',0)
                        ->where($map)
                        ->where($whereArr)
                        ->order($sortData['sortName'],$sortData['sortOrder']);
      
      #查询、排序结果数据集：
      $baseSet=$queryBase->select();
      $baseSet=is_array($baseSet)?collection($baseSet):$baseSet;
      
      #查询、排序结果总数
      $searchResultNum=count($baseSet);
      
      if($searchResultNum){
        #本页要显示的记录
        $patList=$baseSet->slice(($sortData['pageNum']-1)*$sortData['listRows'],$sortData['listRows']);
      }
      
      #pat模型对象，排序、查询后分页用
      $pageQuery=$patMdl->where('id','in',$baseSet->column('id'))
                      ->order($sortData['sortName'],$sortData['sortOrder']);
      #分页对象，符合查询条件的所有iss记录分页,每页$listRows条记录
      $pageSet=$pageQuery->paginate($sortData['listRows'],false,['type'=>'bootstrap','var_page' =>'pageNum','page'=>$sortData['pageNum'],
                        'query'=>['listRows'=>$sortData['listRows']]]);   
      
      $this->assign([
        'home'=>$request->domain(),
        'numTotal'=>$searchResultNum,
        'patList'=>$patList,
        'pageSet'=>$pageSet,
        #排序数组
        'sortData'=>$sortData,
        #将数组转换为json字符串，编码为Unicode字符（\uxxxx）。
        #前端就可以使用json对象的访问方法或是关联数组的访问方法进行使用，若前端文件采用utf-8编码，汉字也可直接解析显示。
        'searchData'=>json_encode($searchData,JSON_UNESCAPED_UNICODE),
              
      ]);
      return view();
    }
    #准备前端select组件所需的内容
    public function getSelComData(PatinfoModel $patMdl)
    {
      //通过$log判断是否是登录用户，非登录用户退回到登录页面
        if(1!==$this->log){
           return $this->error('未登录用户，请先登录系统');
            //$this->redirect($request->domain());
        }
        $request=$this->request;
        #定义返回前端的数据结构
        $resData=[
          'dept'=>[['txt'=>'','val'=>'']],
          'type'=>[['txt'=>'','val'=>'']],
          'status'=>[['txt'=>'','val'=>'']]
        ];
        #接收前端的参数
        $selNameArr=$request->param('nameArr/a');
        $period=!empty($request->param('period'))?$request->param('period'):'total';
        
        foreach($resData as $key=>$val){
          $resData[$key]=$patMdl::getPeriodSelData($key,$val[0]);
        }
        
        return $resData;
    }

}
