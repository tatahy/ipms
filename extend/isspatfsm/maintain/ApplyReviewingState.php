<?php

/**
 * @author tatahy
 * @copyright 2018
 * �̳С�ʵ�ֳ����ࣺMaintainState
 */

namespace isspatfsm\maintain;

use isspatfsm\maintain\MaintainState;
use isspatfsm\maintain\MaintainContext;

class ApplyReviewingState extends MaintainState{
  
  public function apply($data){  
    $this->_updateStatus($data);
    //״̬�޸�
    $this->_context->setState(MaintainContext::$applySubmittingState);
    return '<br>apply�����';
    
  }
  public function review($data){
  
    return '<br>��Чreview����';
  }
  public function improve($data){
    $this->_updateStatus($data);
    //״̬�޸�
    $this->_context->setState(MaintainContext::$applyModifyingState);
    return '<br>improve�����';
  }
  public function authorize($data){
    
    return '<br>��Чauthorize����';
  }
  
  public function reject($data){  
    return '<br>��Чreject����';
  }
  
  public function close($data){  
    return '<br>��Чclose����';
  }
  
  public function addRenew($data){  
    return '<br>��ЧaddRenew����';
  }
  
  
}

?>