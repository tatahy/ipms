<?php

/**
 * @author tatahy
 * @copyright 2018
 * 定义一个isspat,_MAINTAIN权限下的state抽象类“MaintainState”
 */

namespace isspatfsm\execute;
//引入操作数据库的5个模型
use isspatfsm\IssPatModel as Mdl;

use isspatfsm\maintain\MaintainContext;

abstract class MaintainState{
  
  //定义一个环境属性，继承的子类才有，属性值是ExecuteContext对象实例。
  protected $_context;
  
  public function __construct(){
    //访问本类中定义的静态属性
    
  }
  
  //设定上下文环境
  public function setContext(MaintainContext $context){
	$this->_context = $context;
 }
  
  //_MAINTAIN的7种操作
  abstract function apply($data);
  abstract function review($data);
  abstract function improve($data);
  abstract function authorize($data);
  abstract function reject($data);
  abstract function addRenew($data);
  public function close($data){
    $this->_updateStatus($data);
    //状态修改
    $this->_context->setState(MaintainContext::$closedState);
    return '<br>close结果：';
  }
  //使用Mdl中封装好的对数据库的操作方法。
  protected function _updateStatus($data){
    //patId!=0,issId!=0
    Mdl::issUpdate($data);
    Mdl::issRdCreate($data);
    Mdl::patUpdate($data);
    Mdl::patRdCreate($data);
    Mdl::attUpdate($data);
  }
  
}

?>