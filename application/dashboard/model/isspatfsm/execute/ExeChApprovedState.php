<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：ExecuteState
 */

namespace app\dashboard\model\isspatfsm\execute;

use app\dashboard\model\isspatfsm\execute\ExecuteState;
use app\dashboard\model\isspatfsm\execute\ExecuteContext;

class ExeChApprovedState extends ExecuteState{
  public function refuse(){
    return '无效操作';
  }
  public function finish(){
    return '无效操作';
  }
  
}

?>