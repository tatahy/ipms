<?php

/**
 * @author tatahy
 * @copyright 2018
 * �̳С�ʵ�ֳ����ࣺEditState
 */

namespace isspatfsm\edit;

use isspatfsm\edit\EditContex;
use isspatfsm\audit\AuditContext;

class ModifyingState extends EditState{
    
  public function delete(){  
    return '�޴�delete����';
  }  
}

?>