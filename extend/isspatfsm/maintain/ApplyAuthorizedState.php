<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：MaintainState
 */

namespace isspatfsm\maintain;

use isspatfsm\maintain\MaintainState;
use isspatfsm\maintain\MaintainContext;

class ApplyAuthorizedState extends MaintainState{
  public function apply(){  
    return '无效操作';
  }
  public function review(){
    return '无效操作';
  }
  public function improve(){
    return '无效操作';
  }
  public function authorize(){
    return '无效操作';
  }
  public function reject(){  
    return '无效操作';
  }
  public function addRenew(){  
    return '无效操作';
  }  
  
}

?>