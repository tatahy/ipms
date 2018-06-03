<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：AuditState
 */

namespace isspatfsm\audit;

use isspatfsm\audit\AuditContext;
use isspatfsm\audit\AuditState;

class CheckingState extends AuditState
{

  public function pass()
  {
    //写入数据库的信息
    $this->_oprtData['iss']['info']['status'] = '审核通过';
    $this->_oprtData['pat']['info']['status'] = '内审审核';
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($this->_oprtData);
    return $this->_mdl->test();
    
    //父类数据库模型操作方法
    $this->_oprtMdl();
    //状态修改
    $this->_context->setState(AuditContext::$auditPassedState);

    return 'pass结果：';
  }

  public function fail()
  {
    //写入数据库的信息
    $this->_oprtData['iss']['info']['status'] = '审核未通过';
    $this->_oprtData['pat']['info']['status'] = '内审审核';
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($this->_oprtData);
    return $this->_mdl->test();
    
    //父类数据库模型操作方法
    $this->_oprtMdl();
    //状态修改
    $this->_context->setState(AuditContext::$auditFailedState);

    return 'fail结果：';

  }

  public function modify()
  {
    //写入数据库的信息
    $this->_oprtData['iss']['info']['status'] = '返回修改';
    $this->_oprtData['pat']['info']['status'] = '内审修改';
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($this->_oprtData);
    return $this->_mdl->test();
    
    //父类数据库模型操作方法
    $this->_oprtMdl();
    //状态修改
    $this->_context->setState(AuditContext::$modifyingState);

    return 'modify结果：';
  }

}

?>