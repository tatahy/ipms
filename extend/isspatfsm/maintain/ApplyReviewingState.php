<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：MaintainState
 */

namespace isspatfsm\maintain;

use isspatfsm\maintain\MaintainState;
use isspatfsm\maintain\MaintainContext;

class ApplyReviewingState extends MaintainState{
  
  public function apply(){  
    //写入数据库的信息
    $this->_oprtData['iss']['info']['status'] = '申报提交';
    $this->_oprtData['pat']['info']['status'] = '申报中';
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($this->_oprtData);
    return '<br>apply:'.$this->_mdl->test();
    
    //状态修改
    $this->_context->setState(MaintainContext::$applySubmittingState);
    return '<br>apply结果：';
    
  }
  public function review(){
     //写入数据库的信息
    $this->_oprtData['iss']['info']['status'] = '申报修改';
    $this->_oprtData['pat']['info']['status'] = '申报修改';
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($this->_oprtData);
    return '<br>review:'.$this->_mdl->test();
    
     //状态修改
    $this->_context->setState(MaintainContext::$applyModifyingState);
    return '<br>review操作';
  }
  public function improve(){
    return '<br>无improve操作';
  }
  public function authorize(){
    
    return '<br>无authorize操作';
  }
  
  public function reject(){  
    return '<br>无reject操作';
  }
  
  public function close(){  
    return '<br>无close操作';
  }
  
  public function addRenew(){  
    return '<br>无addRenew操作';
  }
}

?>