<?php

/**
 * @author tatahy
 * @copyright 2018
 * 定义一个isspat,_APPROVE权限下的state抽象类“ApproveState”
 */

namespace isspatfsm\approve;
//引入操作数据库的5个模型
use isspatfsm\OprtModel as Mdl;

use isspatfsm\approve\ApproveContext;

abstract class ApproveState{
  
  //定义一个环境属性，继承的子类才有，属性值是ApproveContext对象实例。
  protected $_context;
  
  public function __construct(){
    //访问本类中定义的静态属性
    
  }
  
  //设定上下文环境
  public function setContext(ApproveContext $context){
	$this->_context = $context;
 }
  
  //_APPROVE的3种操作
  public abstract function approve($data);
  public abstract function veto($data);
  public abstract function complete($data);
  //使用Mdl中封装好的对数据库的操作方法。
  protected function _updateStatus($data){
    //patId!=0,issId!=0
    Mdl::issPatStatusUpdate($data);
    
  }
  
}

?>