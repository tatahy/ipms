<?php

/**
 * @author tatahy
 * @copyright 2018
 * 定义一个环境类，实现对_xx权限下对issPat状态的控制、切换
 */

namespace isspatfsm\xx;
//input state 
use isspatfsm\xx\xxState;

//output state
use isspatfsm\xx\xxState;

class xxContext{
  //定义所有_xx的issPat状态
  //static标识的类的静态属性只属于类，与对象实例和其他类无关。
  //类的静态属性类似于函数的全局变量，在类的外部使用类名直接访问类的静态属性“xxContext::fillingState”
  static $checkingState = null;
  
  //定义一个当前_xx状态，属性值是当前_EDIT状态对象实例。
  private $_currentState;
  
  public function __construct(){
    //访问本类中定义的静态属性
    self::$xxState = new xxState();
    
    
  }
  
  //获取状态
  public function getState(){
    return $this->_currentState;
  }
  //设置当前状态
  public function setState(xxState $xxState){
    $this->_currentState=$xxState;
    //把当前的环境通知到各个实现类中
    $this->_currentState->setContext($this);
  }
  
  //_AUDIT对状态的操作
  public function pass($data){
   return $this->_currentState->pass($data);
  }
  
  
   
}

?>