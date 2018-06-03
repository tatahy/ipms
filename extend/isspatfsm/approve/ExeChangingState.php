<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：ApproveState
 */

namespace isspatfsm\approve;

use isspatfsm\approve\ApproveState;
use isspatfsm\approve\ApproveContext;

class ExeChangingState extends ApproveState{
  
  public function permit(){
    //写入数据库的信息
    $this->_oprtData['iss']['info']['status'] = '准予变更';
    $this->_oprtData['pat']['info']['status'] = '内审批准';
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($this->_oprtData);
    return '<br>permit:'.$this->_mdl->test();
    
    //状态修改
    $this->_context->setState(ApproveContext::$exeChApprovedState);
    
    return 'approve结果：';
  }
  public function veto(){
    //写入数据库的信息
    $this->_oprtData['iss']['info']['status'] = '否决变更';
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($this->_oprtData);
    return '<br>veto:'.$this->_mdl->test();
    
    //状态修改
    $this->_context->setState(ApproveContext::$exeChVetoedState);
    
    return 'veto结果：';
  }
  public function complete(){
    
    return '<br>无complete操作';
  }
  
  
}

?>