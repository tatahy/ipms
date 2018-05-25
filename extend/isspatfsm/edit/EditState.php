<?php

/**
 * @author tatahy
 * @copyright 2018
 * 定义一个state抽象类，定义每个state下可进行的操作。
 */

namespace isspatfsm\edit;

interface EditState{
  
  //_EDIT的各种操作
  public function addNew();
  public function submit();
  public function delete();
  public function update();
  
}

?>