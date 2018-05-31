<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：EditState
 */

namespace isspatfsm\edit;

use isspatfsm\edit\EditContex;
use isspatfsm\audit\AuditContext;

class ModifyingState extends EditState{
    
  public function delete(){  
    return '无此delete操作';
  }  
}

?>