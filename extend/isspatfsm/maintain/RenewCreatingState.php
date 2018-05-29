<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：MaintainState
 */

namespace isspatfsm\maintain;

use isspatfsm\maintain\MaintainState;
use isspatfsm\maintain\MaintainContext;

class RenewCreatingState extends MaintainState{
  
  public function apply($data){  
    return '<br>无效apply操作';
  }
  public function review($data){
  
    return '<br>无效review操作';
  }
  public function improve($data){
    
    return '<br>无效improve操作：';
  }
  public function authorize($data){
    
    return '<br>无效authorize操作';
  }
  
  public function reject($data){  
    return '<br>无效reject操作';
  }
  
  public function close($data){  
    return '<br>无效close操作';
  }
  
  public function addRenew($data){  
    $this->_updateStatus($data);
    //状态修改
    $this->_context->setState(MaintainContext::$renewPlanningState);
    return '<br>addRenew结果';
  }
  
  
}

?>