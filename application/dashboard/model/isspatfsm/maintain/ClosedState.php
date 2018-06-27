<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：MaintainState
 */

namespace app\dashboard\model\isspatfsm\maintain;

use app\dashboard\model\isspatfsm\maintain\MaintainState;
use app\dashboard\model\isspatfsm\maintain\MaintainContext;

class ClosedState extends MaintainState{
  
  public function apply(){  
    return '<br>无效apply操作';
  }
  public function review(){
  
    return '<br>无效review操作';
  }
  public function improve(){
    return '<br>无效improve操作';
  }
  public function authorize(){
    
    return '<br>无效authorize操作';
  }
  
  public function reject(){  
    return '<br>无效reject操作';
  }
  
  public function addRenew(){  
    return '<br>无效addRenew操作';
  }

}

?>