<?php
namespace app\asset\controller;

use think\Request;
use think\Session;
use think\View;

use app\asset\model\Assinfo as AssinfoModel;

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
    
    //
    private function priLogin(){
        //通过$log判断是否是登录用户，非登录用户退回到登录页面
        if(1!==$this->log){
            $this->error('未登录用户，请先登录系统');
            //$this->redirect($request->domain());
        }
    }
    
    public function index(Request $request,AssinfoModel $assMdl)
    {
        $this->priLogin();
        
        $numTotal=count($assMdl::all());
        
        $this->assign([
                    
          'home'=>$request->domain(),
          'username'=>$this->username,
          'year'=>date('Y'),
          
          'numTotal'=>$numTotal,
        ]);
        return view();
        //return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
    }
    
    public function assList(Request $request,AssinfoModel $assMdl)
    {
        $this->priLogin();
        
        $listRows=!empty($request->param('listRows'))?$request->param('listRows'):10;
        $pageTotalNum=!empty($request->param('pageTotalNum'))?$request->param('pageTotalNum'):1;
        
                
        //利用模型对象得到非“填报”状态的patent总数
        $numTotal=count($assMdl::all());
        
        //分页,每页$listRows条记录
        $assSet=$assMdl::where('id','>',0)
                        ->order('place_now', 'asc')
                        ->paginate($listRows,false,['type'=>'bootstrap','var_page' => 'pageTotalNum',
                        'query'=>['listRows'=>$listRows]]);
        // 获取分页显示
        $assList=$assSet->render(); 
        
        $this->assign([
                    
          //'home'=>$request->domain(),
//          'username'=>$this->username,
//          'year'=>date('Y'),
          
          'numTotal'=>$numTotal,
          'pageTotalNum'=>$pageTotalNum,
          
          'assSet'=>$assSet,
          'assList'=>$assList,
          'listRows'=>$listRows,
        ]);
        return view();
        
    }
}
