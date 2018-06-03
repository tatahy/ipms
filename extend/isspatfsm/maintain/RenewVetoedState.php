<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：MaintainState
 */

namespace isspatfsm\maintain;

use isspatfsm\maintain\MaintainState;
use isspatfsm\maintain\MaintainContext;

class RenewVetoedState extends MaintainState{
  
  public function apply(){  
    return '<br>无apply操作';
  }
  public function review(){
  
    return '<br>无review操作';
  }
  public function improve(){
    return '<br>无improve操作';
  }
  public function authorize(){
    
    return '<br>无authorize操作';
  }
  
  public function reject(){  
    return '<br>无reject操作';
  }
  
  public function addRenew(){  
    return '<br>无addRenew操作';
  }
  
  
}

?>