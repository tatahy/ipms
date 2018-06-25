<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：ExecuteState
 */

namespace isspatfsm\execute;

use isspatfsm\execute\ExecuteState;
use isspatfsm\execute\ExecuteContext;

class ApplyModifyingState extends ExecuteState{
  
  public function accept(){  
    return '无效操作';
  }
  public function refuse(){
    return '无效操作';
  }
  
}

?>