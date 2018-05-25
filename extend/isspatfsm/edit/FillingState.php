<?php

/**
 * @author tatahy
 * @copyright 2018
 * 定义一个环境类
 */

namespace isspatfsm\edit;

class FillingState implements EditState{
  private $_editContext;
  
  public function __construct(EditContext $editContext){
    $this->_editContext = $editContext;
  }  
  
  
  public function addNew(){
    
  }
  
  public function submit(){
    //状态修改
    $this->_editContext->setEditState(EditContext::$checkingState);
    //动作委托为submit
    $this->_editContext->getEditState()->submit();
  }
  
  public function delete(){
    $this->_editContext->getEditState()->delete();
  }
  
  public function update(){
    $this->_editContext->setEditState(EditContext::$fillingState);
    $this->_editContext->getEditState()->update();
  }
  
}

?>