<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：AuditState
 */

namespace isspatfsm\audit;

use isspatfsm\audit\AuditContext;
use isspatfsm\audit\AuditState;

class CheckingState extends AuditState{

  public function pass(){
    $data=$this->_oprtData;
    //写入数据库的信息
    $data['iss']['info']['status'] = '审核通过';
    $data['iss']['record']['actdetail']='<p>《'.$data['iss']['info']['topic'].'》<span class="label label-success">审核通过</span></p><span class="text-warning">审核意见：</span><pre>'.$data['iss']['info']['statusdescription'].'</pre>';
    $data['pat']['info']['status'] = '内审审核';
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($data);
   
    //issinfo更新
    $this->_mdl->issUpdate();
    //issrecord新增
    $this->_mdl->issRdCreate();
    //patinfo更新
    $this->_mdl->patUpdate();
    //attinfo更新
    $this->_mdl->attUpdate();
    //状态修改
    //$this->_context->setState(AuditContext::$auditPassedState);

    return '<br>【pass】操作结果：<span class="label label-success">审核通过</span>';
  }

  public function fail(){
    $data=$this->_oprtData;
    //写入数据库的信息
    $data['iss']['info']['status'] = '审核未通过';
    $data['iss']['record']['actdetail']='<p>《'.$data['iss']['info']['topic'].'》<span class="label label-warning">审核未通过</span></p><span class="text-warning">审核意见：</span><pre>'.$data['iss']['info']['statusdescription'].'</pre>';
    $data['pat']['info']['status'] = '内审审核';
    //已上传附件不可删除
    $data['att']['info']['deldisplay']=0;
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($data);
   
    //issinfo更新
    $this->_mdl->issUpdate();
    //issrecord新增
    $this->_mdl->issRdCreate();
    //patinfo更新
    $this->_mdl->patUpdate();
    //attinfo更新
    $this->_mdl->attUpdate();
    //状态修改
    //$this->_context->setState(AuditContext::$auditFailedState);

    return '<br>【fail】操作结果：<span class="label label-warning">审核未通过</span>';

  }

  public function modify(){
    $data=$this->_oprtData;
    //写入数据库的信息
    $data['iss']['info']['status'] = '返回修改';
    $data['iss']['record']['actdetail']='《'.$data['iss']['info']['topic'].'》返回撰写人【'.$data['iss']['info']['writer'].'】修改<br><span class="text-warning">修改意见：</span><br><pre>'.$data['iss']['info']['statusdescription'].'</pre>';
    $data['pat']['info']['status'] = '内审修改';
    //已上传附件不可删除
    $data['att']['info']['deldisplay']=0;
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($data);
    
    //issinfo更新
    $this->_mdl->issUpdate();
    //issrecord新增
    $this->_mdl->issRdCreate();
    //patinfo更新
    $this->_mdl->patUpdate();
    //attinfo更新
    $this->_mdl->attUpdate();

    //状态修改
    //$this->_context->setState(AuditContext::$modifyingState);

    return 'modify结果：<br>返回撰写人【'.$data['iss']['info']['writer'].'】修改。<br><span class="text-warning">修改意见：</span><br><pre>'.$data['iss']['info']['statusdescription'].'</pre>';
  }

}

?>