<?php

/**
 * @author tatahy
 * @copyright 2018
 * 定义一个isspat,_MAINTAIN权限下的state抽象类“MaintainState”
 */

namespace isspatfsm\maintain;
//引入操作数据库的5个模型
use isspatfsm\IssPatModel;

use isspatfsm\maintain\MaintainContext;

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
    //写入数据库的信息
    $this->_oprtData['iss']['info']['status'] = '完结';
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($this->_oprtData);
    return '<br>close:'.$this->_mdl->test();
    //状态修改
    $this->_context->setState(MaintainContext::$closedState);
    return '<br>close结果：';
  }
  
}

?>