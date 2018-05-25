<?php

/**
 * @author tatahy
 * @copyright 2018
 * 定义一个……抽象类
 */

namespace hyphplib\isspatstate\edit;

abstract class EditState{
  //定义一个环境角色，也就是封装状态的变换引起的功能变化
  protected $_context;
  
  public function setContext(Context $context){
    $this->_context=$context;    
  }
  
  //_EDIT的各种操作
  public function addNew(){
    //
    $this->_context->setEditState(Context::$fillingState);
  }
  public abstract function submit();
  public abstract function delete();
  public abstract function update();
  
}

?>