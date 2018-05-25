<?php

/**
 * @author tatahy
 * @copyright 2018
 * 定义一个环境类，实现对_EDIT权限下对issPat状态的控制、切换
 */

namespace isspatfsm\edit;

class EditContext{
  //定义所有_EDIT的issPat状态
  //static标识的类的静态属性只属于类，与对象实例和其他类无关。
  //类的静态属性类似于函数的全局变量，在类的外部使用类名直接访问类的静态属性“EditContext::fillingState”
  static $applyCreatingState = null;
  static $fillingState = null;
  static $modifyingState = null;
  static $completingState = null;
  static $checkingState = null; 
  //定义一个当前_EDIT状态
  private $_currentState;
  
  public function __construct(){
    //访问本类中定义的静态属性
    self::$applyCreatingState = new ApplyCreatingState();
    self::$fillingState = new FillingState();
    self::$modifyingState = new ModifyingState();
    self::$completingState = new CompletingState();
    self::$checkingState = new CheckingState();
    $this->_currentState=self::$fillingState;
  }
  
  //获取状态
  public function getEditState(){
    return $this->_currentState;
  }
  //设置当前状态
  public function setEditState(EditState $editState){
    $this->_currentState=$editState;
    //把当前的环境通知到各个实现类中
    $this->_currentState->setContext($this);
  }
  
  //_EDIT对状态的操作
  public function addNew(){
    $this->_currentState->addNew();
  }
  
  public function submit(){
    $this->_currentState->submit();
  }
  
  public function delete(){
    $this->_currentState->delete();
  }
  
  public function update(){
    $this->_currentState->update();
  }
  
  
  
  
}

?>