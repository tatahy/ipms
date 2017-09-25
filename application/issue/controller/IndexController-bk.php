<?php
namespace app\issue\controller;

use think\Request;
use think\Session;
use think\View;
use app\issue\model\Issinfo as IssinfoModel;

class IndexController extends \think\Controller
{
        
    public function index(Request $request)
    {
        
        $username=Session::get('username');
        $pwd=Session::get('pwd');
        $log=Session::get('log');
        $roles=Session::get('role');
        $dept=Session::get('dept');
        
        //通过$log判断是否是登录用户，非登录用户退回到登录页面
        if(1!==$log){
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
                        ->where('writer',$username)
                        ->where('status',['=','填报'],['=','退回修改'],'or')
                        ->select();
        $num_writer1=count($writer1);                    
        //为index.html模板的writer第二类issue（审批中）模板变量赋值
        $writer2=$issues->where('num_type',$type)
                        ->where('writer',$username)
                        ->where('status',['=','提交'],['=','审核通过'],'or')
                        ->select();
        $num_writer2=count($writer2); 
        //为index.html模板的writer第三类issue（审批结果）模板变量赋值
        $writer3=$issues->where('num_type',$type)
                        ->where('writer',$username)
                        ->where('status',['=','批准'],['=','否决'],'or')
                        ->select();
        $num_writer3=count($writer3); 
        //为index.html模板的writer第四类issue（执行情况）模板变量赋值
        $writer4=$issues->where('num_type',$type)
                        ->where('writer',$username)
                        ->where('status',['=','执行中'],['=','完成'],'or')
                        ->select();
        $num_writer4=count($writer4); 
        
        //为index.html模板的reviewer第一类issue(待处理)模板变量赋值
        $reviewer1=$issues->where('num_type',$type)
                            ->where('dept',$dept)
                            ->where('status','提交')
                            ->select();
        $num_reviewer1=count($reviewer1); 
        //为index.html模板的reviewer第二类issue（批准中）模板变量赋值
        $reviewer2=$issues->where('num_type',$type)
                            ->where('dept',$dept)
                            ->where('status',['=','退回修改'],['=','审核通过'],'or')
                            ->select();
        $num_reviewer2=count($reviewer2); 
        //为index.html模板的reviewer第三类issue（批准结果）模板变量赋值
        $reviewer3=$issues->where('num_type',$type)
                            ->where('dept',$dept)
                            ->where('status',['=','批准'],['=','否决'],'or')
                            ->select();
        $num_reviewer3=count($reviewer3); 
        //为index.html模板的reviewer第四类issue（执行情况）模板变量赋值
        $reviewer4=$issues->where('num_type',$type)
                            ->where('dept',$dept)
                            ->where('status',['=','执行中'],['=','完成'],'or')
                            ->select();
        $num_reviewer4=count($reviewer4); 
        
        //为index.html模板的approver第一类issue(待处理)模板变量赋值
        $approver1=$issues->where('num_type',$type)
                            ->where('status','审核通过')
                            ->select();
        $num_approver1=count($approver1); 
        //为index.html模板的approver第二类issue（审批结果）模板变量赋值
        $approver2=$issues->where('num_type',$type)
                            ->where('status',['=','退回修改'],['=','批准'],['=','否决'],'or')
                            ->select();
        $num_approver2=count($approver2);
        //为index.html模板的approver第三类issue（执行中）模板变量赋值
        $approver3=$issues->where('num_type',$type)
                            ->where('status','执行中')
                            ->select();
        $num_approver3=count($approver3);
        //为index.html模板的approver第四类issue（完成情况）模板变量赋值
        $approver4=$issues->where('num_type',$type)
                            ->where('status','完成')
                            ->select();
        $num_approver4=count($approver4);
                    
        //为index.html模板的operator第一类issue(待处理)模板变量赋值
        $operator1=$issues->where('num_type',$type)
                            ->where('executer',$username)
                            ->where('status','批准')
                            ->select();
        $num_operator1=count($operator1);
        //为index.html模板的operator第二类issue（执行中）模板变量赋值
        $operator2=$issues->where('num_type',$type)
                            ->where('executer',$username)
                            ->where('status','执行中')
                            ->select();
        $num_operator2=count($operator2);
        //为index.html模板的operator第三类issue（完成情况）模板变量赋值
        $operator3=$issues->where('num_type',$type)
                            ->where('executer',$username)
                            ->where('status','完成')
                            ->select();
        $num_operator3=count($operator3);
        

        //--在index.html页面输出自定义信息的HTML代码块        
		$destr= "请求方法:".$request->method()."</br>".
            "username:".$username."</br>".
            //"pwd:".sizeof($pwd);
            "pwd:".$pwd."</br>".
            "log:".$log."</br>".
            "roleparam:".$request->param('role').";active:".$request->param('active')."</br>".
            "dept:".$dept;
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
    
     public function issnew(Request $request)
    {
        $log=Session::get('log');
        
        //通过$log判断是否是登录用户，非登录用户退回到登录页面
        if(1!==$log){
            $this->error('未登录用户，请先登录系统');
            //$this->redirect($request->domain());
        }else{
            $username=Session::get('username');
            $pwd=Session::get('pwd');
            $roles=Session::get('role');
            $type=$request->param('type');
        }
        
        //判断登录用户的$roles值是否含有writer，不含writer就报错并退回到上一个页面
        $i=0;
        foreach ($roles as $value) {
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
        
        //HY，需进行修改。利用模型Issinfo类，由Issinfo类将数据写入数据表
       // if(!empty($request->param())){
//            //通过外部提交赋值给模型Patinfo类，由Patinfo类将数据写入数据表
//            $pats = new PatinfoModel($_POST); 
//            //过滤post数组中的非数据表字段数据后写入数据表
//            $pats->allowField(true)->save(); 
//            //获取自增ID值
//            $pats->id;
//            //post来的“Pronum”是数组，要加“a”才能获取到。TP5.0默认是“s”(字符串)
//            $pronum = ($request->param('pronum/a'));
//            
//            //上述代码将数据写入数据库后，带参数'id'跳转到<{$home}>/patent/index/patmod操作页面
//            $this->redirect('index/patmod', ['id' =>$pats->id]);
//            
//            
//            //$this->success('填报成功，进入修改页面', 'index/patmod');
//            break;
//        }
        
        
        //--在index.html页面输出自定义信息的HTML代码块
        $destr= "请求方法:".$request->method()."</br>".
                "username:".$username."</br>".
                //"pwd:".sizeof($pwd);
                "pwd:".$pwd."</br>".
                "log:".$log."</br>";
                //$request->param('pronum')."</br>";
                //"home:".$request->domain()."</br>";
        
        
        $this->assign([
        //在index.html页面通过'destr'输出自定义的信息
        'destr'=>$destr,
        //在index.html页面通过'array'输出自定义的数组内容
        'array'=>$roles, 
        
        'home'=>$request->domain(),
        'username'=>$username,
        'rolename'=>$rolename,
        'role'=>$role,
        'typename'=>$typename,
        'type'=>$type,
        'today'=>date("Y-m-d, H-i-s"),
//        'active'=>$active,
        ]);
        return view();
        
        //return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
    }
    
    //writer“提交”issue
     public function isssubmit(Request $request)
    {
        
         return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
    }
    
    //writer“修改”issue
     public function issmodify(Request $request)
    {
        
         return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
    }
    
    //writer“删除”issue
     public function issdelete(Request $request)
    {
        
         return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
    }
    
    //reviewer“审核通过”issue
     public function issaudit(Request $request)
    {
        
         return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
    }
    
    //reviewer、approver“退回修改”issue
     public function issreject(Request $request)
    {
        
         return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
    }
    
    //reviewer、approver“否决”issue
     public function issrefuse(Request $request)
    {
        
         return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
    }
    
    //approver“批准同意”issue
     public function issapprove(Request $request)
    {
        
         return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
    }
    
    //operator“领受”issue
     public function issaccess(Request $request)
    {
        
         return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
    }
    
    //operator“填写报告”issue
     public function issreport(Request $request)
    {
        
         return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
    }
    
    //operator“关闭事务”issue
     public function issclose(Request $request)
    {
        
         return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
    }
    
}
