<?php

/**
 * @author tatahy
 * @copyright 2018
 * 定义一个isspat,_MAINTAIN权限下的state抽象类“MaintainState”
 */

namespace app\dashboard\model\isspatfsm\maintain;
//引入操作数据库的5个模型
use app\dashboard\model\isspatfsm\IssPatModel;

use app\dashboard\model\isspatfsm\maintain\MaintainContext;

abstract class MaintainState{
  
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
 
 //得到操作所需的数据
  public function getData($data){
    $this->_oprtData = $data;
  }
  
  //设定上下文环境
  public function setContext(MaintainContext $context){
	$this->_context = $context;
 }
  
  //_MAINTAIN的7种操作
  abstract function apply();
  abstract function review();
  abstract function improve();
  abstract function authorize();
  abstract function reject();
  abstract function addRenew();
  public function close(){
    $data=$this->_oprtData;
    //写入数据库的信息
    $data['iss']['info']['status'] = '完结';
    $data['iss']['info']['close_time'] = time();
    $data['iss']['info']['statusdescription'] = '专利事务完结。';
    //$data['iss']['record']['actdetail']='<p>《'.$data['iss']['info']['topic'].'》<span class="label label-default">完结</span></p>';
    $data['iss']['record']['actdetail']='0';
    //数据表“issrecord”的“actdetailhtml”字段存储结构化的信息（模型“issrecord”设置自动转换为json类型），便于前端组装HTML进行显示。
    $data['iss']['record']['actdetailhtml']=array(
                                                'p'=>array(
                                                        'prefix'=>'《'.$data['iss']['info']['topic'].'》',
                                                        'spanclass'=>'default',
                                                        'spantext'=>'完结',
                                                        'nextstage'=>''
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
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($data);
    
    //数据库模型操作方法
    //iss更新
    $this->_mdl->issUpdate();
    //iss新增
    $this->_mdl->issRdCreate();
     
    //状态修改
    //$this->_context->setState(MaintainContext::$closedState);
    return '完结关闭';
  }
  
  //数据库模型操作
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