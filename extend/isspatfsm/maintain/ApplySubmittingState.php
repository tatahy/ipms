<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：MaintainState
 */

namespace isspatfsm\maintain;

use isspatfsm\maintain\MaintainState;
use isspatfsm\maintain\MaintainContext;

class ApplySubmittingState extends MaintainState{
  
  public function apply(){  
    return '<br>无apply操作';
  }
  public function review(){
  
    return '<br>无review操作';
  }
  public function improve(){
     //写入数据库的信息
    $this->_oprtData['iss']['info']['status'] = '申报修改';
    $this->_oprtData['pat']['info']['status'] = '申报修改';
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($this->_oprtData);
    return '<br>review:'.$this->_mdl->test();
    
    //状态修改
    $this->_context->setState(MaintainContext::$applyModifyingState);
    return '<br>improve结果';
  }
  public function authorize(){
     //写入数据库的信息
    $this->_oprtData['iss']['info']['status'] = '专利授权';
    $this->_oprtData['pat']['info']['status'] = '授权';
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($this->_oprtData);
    return '<br>authorize:'.$this->_mdl->test();
    
    //状态修改
    $this->_context->setState(MaintainContext::$applyAuthorizedState);
    return '<br>authorize结果';
  }
  
  public function reject(){  
     //写入数据库的信息
    $this->_oprtData['iss']['info']['status'] = '专利驳回';
    $this->_oprtData['pat']['info']['status'] = '驳回';
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($this->_oprtData);
    return '<br>reject:'.$this->_mdl->test();
    
    //状态修改
    $this->_context->setState(MaintainContext::$applyRejectedState);
    return '<br>reject结果';
  }
  
  public function addRenew(){  
    return '<br>无addRenew操作';
  }
  
  public function close(){  
    return '<br>无close操作';
  }
  
}

?>