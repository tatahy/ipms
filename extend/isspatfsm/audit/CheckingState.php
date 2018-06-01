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
    //调用IssPatModel的setMdlData()方法，设定所需进行处理的数据。
    $this->_mdl->setMdlData($this->_oprtData);
    //自有数据库模型操作方法
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
    //调用IssPatModel的setMdlData()方法，设定所需进行处理的数据。
    $this->_mdl->setMdlData($this->_oprtData);
    //自有数据库模型操作方法
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
    //调用IssPatModel的setMdlData()方法，设定所需进行处理的数据。
    $this->_mdl->setMdlData($this->_oprtData);
    //自有数据库模型操作方法
    $this->_oprtMdl();
    //状态修改
    $this->_context->setState(AuditContext::$modifyingState);

    return 'modify结果：';
  }
  //数据库模型操作，自己使用
  private function oprtMdl()
  {
    //1.patinfo更新
    $this->_mdl->patUpdate();

    //2.patrecord新增
    $this->_mdl->patRdCreate();

    //3.issinfo更新
    $this->_mdl->issUpdate();

    //4.issrecord新增
    $this->_mdl->issRdCreate();

    //5.attinfo更新
    $this->_mdl->attUpdate();
  }

}

?>