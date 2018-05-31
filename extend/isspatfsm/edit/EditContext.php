<?php

/**
 * @author tatahy
 * @copyright 2018
 * 定义一个控制类，实现_EDIT权限下对issPat的5种状态的转换，4种操作的执行。
 */

namespace isspatfsm\edit;

use isspatfsm\edit\EditState;

//input State
use isspatfsm\edit\ApplyCreatingState;
use isspatfsm\edit\FillingState;
use isspatfsm\edit\ModifyingState;
use isspatfsm\edit\CompletingState;
//output state
use isspatfsm\audit\CheckingState;

class EditContext{
  //_EDIT的5种状态
  //static标识的类的静态属性只属于类，与对象实例和其他类无关。
  //类的静态属性类似于函数的全局变量，在类的外部使用类名直接访问类的静态属性“EditContext::fillingState”
  static $applyCreatingState = null;
  static $fillingState = null;
  static $modifyingState = null;
  static $completingState = null;
  static $checkingState = null; 
  //定义一个当前_EDIT状态，属性值是当前_EDIT状态对象实例。
  private $_currentState;
  
  public function __construct(){
    //访问本类中定义的静态属性
    self::$applyCreatingState = new ApplyCreatingState();
    self::$fillingState = new FillingState();
    self::$modifyingState = new ModifyingState();
    self::$completingState = new CompletingState();
    self::$checkingState = new CheckingState();
  }
  
  //获取状态
  public function getState(){
    return $this->_currentState;
  }
  //设置当前状态
  public function setState(EditState $editState){
    $this->_currentState=$editState;
    //把当前的环境通知到各个实现类中
    $this->_currentState->setContext($this);
  }
  
  //中转state中要处理的数据
  public function transferData($data){
    //state接收要处理的数据
    $this->_currentState->getData($data);
  }
  
  //_EDIT的4种操作
  public function addNew(){
   return $this->_currentState->addNew();
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