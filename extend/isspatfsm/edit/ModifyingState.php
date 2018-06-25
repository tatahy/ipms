<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：EditState
 */

namespace isspatfsm\edit;

use isspatfsm\edit\EditContex;
use isspatfsm\edit\EditState;

class ModifyingState extends EditState{
  
  public function addNew(){
    return '无效操作';
  }  
  
  public function delete(){  
    return '无效操作';
  }  
}

?>