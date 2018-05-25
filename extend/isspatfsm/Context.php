<?php

/**
 * @author tatahy
 * @copyright 2018
 * 定义一个环境类，实现对状态的控制、切换
 */

namespace hyphplib\isspatstate\edit;

class Context{
  //定义所有_EDIT的issPat状态
  static $fillingState = null;
  static $modifyingState = null;
  static $completingState = null;
  static $checkingState = null; 
  //定义一个当前_EDIT状态
  private $currentState;
  
  public function __construct(){
    self::$fillingState = new FillingState();
    self::$modifyingState = new ModifyingState();
    self::$completingState = new CompletingState();
    self::$checkingState = new CheckingState();
    $this->currentState=self::$fillingState;
  }
  
 
  //获取状态
  public function getEditState(){
    return $this->currentState;
  }
  //设置当前状态
  public function setEditState(EditState $editState){
    $this->currentState=$editState;
    //把当前的环境通知到各个实现类中
    $this->currentState->setContext($this);
  }
  
  //_EDIT对状态的操作
  public function addNew(){
    $this->_editState->addNew();
  }
  
  public function submit(){
    $this->_editState->submit();
  }
  
  public function delete(){
    $this->_editState->delete();
  }
  
  public function update(){
    $this->_editState->update();
  }
  
  
  
  
}

?>