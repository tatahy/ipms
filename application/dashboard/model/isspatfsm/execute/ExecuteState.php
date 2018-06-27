<?php

/**
 * @author tatahy
 * @copyright 2018
 * 定义一个isspat,_EXECUTE权限下的state抽象类“ExecuteState”
 */

namespace app\dashboard\model\isspatfsm\execute;
//引入操作5个数据库的类
use app\dashboard\model\isspatfsm\IssPatModel;

use app\dashboard\model\isspatfsm\execute\ExecuteContext;

abstract class ExecuteState{
  
  //定义一个环境属性，继承的子类才有，属性值是EditContext对象实例。
  protected $_context;
  //
  protected $_mdl;
  //操作所需数据
  protected $_oprtData;
  
  public function __construct(){
    //实例化IssPatModel类，便于使用其封装的方法。
    $this->_mdl = new IssPatModel();
  }
  //设定上下文环境
  public function setContext(ExecuteContext $context){
	$this->_context = $context;
 }
 //得到操作所需的数据
  public function getData($data){
    $this->_oprtData = $data;
  }
  
  //_EXECUTE的4种操作
  abstract function refuse();
  
  public function accept(){   
    $data=$this->_oprtData;
    //写入数据库的信息
    $data['iss']['info']['status'] = '申报执行';
    $data['iss']['info']['statusdescription']='申报执行中。';
    //$data['iss']['record']['actdetail']='<p>《'.$data['iss']['info']['topic'].'》申报执行。待【完成】。</p>';
    $data['iss']['record']['actdetail']='0';
    //数据表“issrecord”的“actdetailhtml”字段存储结构化的信息（模型“issrecord”设置自动转换为json类型），便于前端组装HTML进行显示。
    $data['iss']['record']['actdetailhtml']=array(
                                                'p'=>array(
                                                        'prefix'=>'《'.$data['iss']['info']['topic'].'》申报执行。',
                                                        'spanclass'=>'',
                                                        'spantext'=>'',
                                                        'nextstage'=>'待【完成】'
                                                        ),
                                                'span'=>array(
                                                        'class'=>'',
                                                        'text'=>'',
                                                        ),
                                                'pre'=>array(
                                                        'class'=>'',
                                                        'text'=>'',
                                                        )
                                            );
    $data['pat']['info']['status'] = '申报中';
    $data['pat']['record']['actdetail']='<p>《'.$data['pat']['info']['topic'].'》<span class="label label-info">申报中</span></p>';
    //已上传附件不可删除
    $data['att']['info']['deldisplay']=0;
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($data);
    //数据库模型操作方法
    $this->_oprtMdl();
    return $data['iss']['record']['actdetailhtml']['p']['nextstage'];
  }
  public function report(){
    $data=$this->_oprtData;
    //写入数据库的信息
    $issStatus=$data['iss']['info']['status'];
    if($issStatus=='否决变更'){
        $data['iss']['info']['status'] = '申报执行';
        $data['iss']['record']['actdetail']='0';
        //数据表“issrecord”的“actdetailhtml”字段存储结构化的信息（模型“issrecord”设置自动转换为json类型），便于前端组装HTML进行显示。
        $data['iss']['record']['actdetailhtml']=array(
                                                'p'=>array(
                                                        'prefix'=>'《'.$data['iss']['info']['topic'].'》申报执行中。',
                                                        'spanclass'=>'',
                                                        'spantext'=>'',
                                                        'nextstage'=>'待【完成】'
                                                        ),
                                                'span'=>array(
                                                        'class'=>'',
                                                        'text'=>'',
                                                        ),
                                                'pre'=>array(
                                                        'class'=>'',
                                                        'text'=>'',
                                                        )
                                            );
    }

    //有“_EXECUTE”权限用户已上传附件仍然可删除
    $data['att']['info']['deldisplay']=1;
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($data);
    
    //issinfo更新
    $msg=$this->_mdl->issUpdate();
    if($issStatus=='否决变更'){
        $this->_mdl->issRdUpdate();
        
    }
    //issRd更新？？
    
    //patinfo更新
    $msg.=$this->_mdl->patUpdate();

    //patRd更新？？

    //5.attinfo更新
    $msg.=$this->_mdl->attUpdate();

    return '成功。'.$msg;

    //状态修改
    //$this->_context->setState(ExecuteContext::$executingState);

  }
  public function finish(){
    $data=$this->_oprtData;
    //写入数据库的信息
    $data['iss']['info']['status'] = '申报复核';
    //$data['iss']['record']['actdetail']='<p>《'.$data['iss']['info']['topic'].'》提交。待【申报复核】</p><span class="text-primary">提交说明：</span><pre>'.$data['iss']['info']['statusdescription'].'</pre>';
    $data['iss']['record']['actdetail']='0';
    //数据表“issrecord”的“actdetailhtml”字段存储结构化的信息（模型“issrecord”设置自动转换为json类型），便于前端组装HTML进行显示。
    $data['iss']['record']['actdetailhtml']=array(
                                                'p'=>array(
                                                        'prefix'=>'《'.$data['iss']['info']['topic'].'》申报执行中。',
                                                        'spanclass'=>'',
                                                        'spantext'=>'',
                                                        'nextstage'=>'待【申报复核】'
                                                        ),
                                                'span'=>array(
                                                        'class'=>'primary',
                                                        'text'=>'提交说明：',
                                                        ),
                                                'pre'=>array(
                                                        'class'=>'',
                                                        'text'=>$data['iss']['info']['statusdescription'],
                                                        )
                                            );
    //已上传附件不可删除
    $data['att']['info']['deldisplay']=0;
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($data);
    
    //issinfo更新
    $this->_mdl->issUpdate();
    //issrecord新增
    $this->_mdl->issRdCreate();
    //patinfo更新
    $this->_mdl->patUpdate();
    //attinfo更新
    $this->_mdl->attUpdate();
    
    //状态修改
    //$this->_context->setState(ExecuteContext::$applyReviewingState);
  
    return $data['iss']['record']['actdetailhtml']['p']['nextstage'];
  
  }
  
  //仅自己和继承类可调用方法，数据库模型操作
  protected function _oprtMdl(){
    //1.patinfo更新
    $this->_mdl->patUpdate();

    //2.patrecord新增
    $this->_mdl->patRdCreate();

    //3.issinfo更新
    $this->_mdl->issUpdate();

    //4.issrecord新增
    $this->_mdl->issRdCreate();

    //5.attinfo更新
    $this->_mdl->attUpdate();
  }
  
  
  
}

?>