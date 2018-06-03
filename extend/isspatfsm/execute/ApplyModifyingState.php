<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：ExecuteState
 */

namespace isspatfsm\execute;

use isspatfsm\execute\ExecuteState;
use isspatfsm\execute\ExecuteContext;

class ApplyModifyingState extends ExecuteState{
  
  public function accept(){  
    return '<br>无accept操作';
  }
  public function refuse(){
    return '<br>无refuse操作';
  }
  public function report(){
    return '<br>无report操作';
  }
  public function finish(){
     //写入数据库的信息
    $this->_oprtData['iss']['info']['status'] = '申报复核';
    $this->_oprtData['pat']['info']['status'] = '申报中';
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($this->_oprtData);
    return '<br>permit:'.$this->_mdl->test();
    
    //状态修改
    $this->_context->setState(ExecuteContext::$applyReviewingState);
    return '<br>finish结果';
  }
  
  
}

?>