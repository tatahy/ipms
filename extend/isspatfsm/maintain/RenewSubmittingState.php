<?php

/**
 * @author tatahy
 * @copyright 2018
 * �̳С�ʵ�ֳ����ࣺMaintainState
 */

namespace isspatfsm\maintain;

use isspatfsm\maintain\MaintainState;
use isspatfsm\maintain\MaintainContext;

class RenewSubmittingState extends MaintainState{
  
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
    $this->_updateStatus($data);
    //״̬�޸�
    $this->_context->setState(MaintainContext::$renewAuthorizedState);
    return '<br>authorize���';
  }
  
  public function reject($data){  
    return '<br>��Чreject����';
  }
  
  public function addRenew($data){  
    return '<br>��ЧaddRenew����';
  }
  
  public function close($data){  
    return '<br>��Чclose����';
  }
  
}

?>