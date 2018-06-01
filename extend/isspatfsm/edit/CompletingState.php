<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：EditState
 */

namespace isspatfsm\edit;

use isspatfsm\edit\EditContex;
use isspatfsm\edit\EditState;

class CompletingState extends EditState{
  
  public function addNew(){
    return '<br>无效addNew操作';
  }
  
  public function delete(){  
    return '<br>无效delete操作';
  }  
  
}

?>