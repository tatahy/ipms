<?php

/**
 * @author tatahy
 * @copyright 2018
 * �̳С�ʵ�ֳ����ࣺMaintainState
 */

namespace isspatfsm\maintain;

use isspatfsm\maintain\MaintainState;
use isspatfsm\maintain\MaintainContext;

class ApplyAuthorizedState extends MaintainState{
  
  public function apply($data){  
    return '<br>��Чapply����';
  }
  public function review($data){
  
    return '<br>��Чreview����';
  }
  public function improve($data){
    return '<br>��Чimprove����';
  }
  public function authorize($data){
    
    return '<br>��Чauthorize����';
  }
  
  public function reject($data){  
    return '<br>��Чreject����';
  }
  
  public function addRenew($data){  
    return '<br>��ЧaddRenew����';
  }
  
  
}

?>