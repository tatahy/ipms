<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：ApproveState
 */

namespace isspatfsm\approve;

use isspatfsm\approve\ApproveState;
use isspatfsm\approve\ApproveContext;

class RenewPlanningState extends ApproveState{
  
  public function permit(){
    //写入数据库的信息
    $this->_oprtData['iss']['info']['status'] = '准予续费';
    $this->_oprtData['pat']['info']['status'] = '续费中';
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($this->_oprtData);
    return '<br>permit:'.$this->_mdl->test();
    
    //状态修改
    $this->_context->setState(ApproveContext::$renewApprovedState);
    
    return 'approve结果：';
  }
  public function veto(){
    //写入数据库的信息
    $this->_oprtData['iss']['info']['status'] = '放弃续费';
    $this->_oprtData['pat']['info']['status'] = '放弃续费';
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($this->_oprtData);
    return '<br>veto:'.$this->_mdl->test();
    
    //状态修改
    $this->_context->setState(ApproveContext::$renewVetoedState);
    
    return 'veto结果：';
  }
  public function complete(){
    
    return '<br>无complete操作';
  }
  
  
}

?>