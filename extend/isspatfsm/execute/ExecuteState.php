<?php

/**
 * @author tatahy
 * @copyright 2018
 * 定义一个isspat,_EXECUTE权限下的state抽象类“ExecuteState”
 */

namespace isspatfsm\execute;
//引入操作数据库的5个模型
use isspatfsm\OprtModel as Mdl;

use isspatfsm\execute\ExecuteContext;

abstract class ExecuteState{
  
  //定义一个环境属性，继承的子类才有，属性值是ExecuteContext对象实例。
  protected $_context;
  
  public function __construct(){
    //访问本类中定义的静态属性
    
  }
  
  //设定上下文环境
  public function setContext(ExecuteContext $context){
	$this->_context = $context;
 }
  
  //_EXECUTE的4种操作
  public abstract function accept($data);
  public abstract function refuse($data);
  public abstract function report($data);
  public abstract function finish($data);
  //使用Mdl中封装好的对数据库的操作方法。
  protected function _updateStatus($data){
    //patId!=0,issId!=0
    Mdl::issPatStatusUpdate($data);
    
  }
  
}

?>