<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：EditState
 */

namespace app\dashboard\model\isspatfsm\edit;

use app\dashboard\model\isspatfsm\edit\EditContex;
use app\dashboard\model\isspatfsm\edit\EditState;

class CompletingState extends EditState{
  
  public function addNew(){
    return '无效操作';
  }
  
  public function delete(){  
    return '无效操作';
  }  
  
}

?>