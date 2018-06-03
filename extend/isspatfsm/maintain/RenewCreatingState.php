<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：MaintainState
 */

namespace isspatfsm\maintain;

use isspatfsm\maintain\MaintainState;
use isspatfsm\maintain\MaintainContext;

class RenewCreatingState extends MaintainState{
  
  public function apply(){  
    return '<br>无apply操作';
  }
  public function review(){
  
    return '<br>无review操作';
  }
  public function improve(){
    
    return '<br>无improve操作：';
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
    //写入数据库的信息
    $this->_oprtData['iss']['info']['status'] = '拟续费';
    $this->_oprtData['pat']['info']['status'] = '续费中';
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($this->_oprtData);
    return '<br>addRenew:'.$this->_mdl->test();
    
    //状态修改
    $this->_context->setState(MaintainContext::$renewPlanningState);
    return '<br>addRenew结果';
  }
  
}

?>