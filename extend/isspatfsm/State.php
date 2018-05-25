<?php

/**
 * @author tatahy
 * @copyright 2018
 * 定义一个……抽象类
 */

namespace hyphplib\isspatstate;

abstract class State{
  //定义一个环境角色，也就是封装状态的变换引起的功能变化
  protected $_context;
  
  public function setContext(Context $context){
    $this->_context=$context;    
  }
  
  //_EDIT的各种操作
  public abstract function addNew();
  public abstract function submit();
  public abstract function delete();
  public abstract function update();
  
  //_AUDIT的各种操作
  public abstract function pass();
  public abstract function fail();
  public abstract function modify();
  
  //_APPROVE的各种操作
  public abstract function permit();
  public abstract function veto();
  public abstract function complete();
  
  //_EXECUTE的各种操作
  public abstract function accept();
  public abstract function refuse();
  public abstract function report();
  public abstract function finish();
  
  //_MAINTAIN的各种操作
  public abstract function apply();
  public abstract function review();
  public abstract function improve();
  public abstract function authorize();
  public abstract function reject();
  public abstract function close();
  public abstract function addRenew(); 
  
}

?>