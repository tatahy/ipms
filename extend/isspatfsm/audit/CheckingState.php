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
    $data['iss']['info']['statusdescription']='审核通过。';
    //$data['iss']['record']['actdetail']='<p>《'.$data['iss']['info']['topic'].'》【审核】结果：<span class="label label-info">审核通过</span> 待【审批】。</p>';
    $data['iss']['record']['actdetail']='0';
    //数据表“issrecord”的“actdetailhtml”字段存储结构化的信息（模型“issrecord”设置自动转换为json类型），便于前端组装HTML进行显示。
    $data['iss']['record']['actdetailhtml']=array(
                                                'p'=>array(
                                                        'prefix'=>'《'.$data['iss']['info']['topic'].'》【审核】结果：',
                                                        'spanclass'=>'info',
                                                        'spantext'=>'审核通过',
                                                        'nextstage'=>'待【审批】'
                                                        ),
                                                'span'=>array(
                                                        'class'=>'',
                                                        'text'=>'',
                                                        ),
                                                'pre'=>array(
                                                        'class'=>'',
                                                        'text'=>'',
                                                        )
                                            );
    $data['pat']['info']['status'] = '内审审核';
    //已上传附件不可删除
    $data['att']['info']['deldisplay']=0;
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($data);
   
    //父类数据库模型操作方法
    $this->_oprtMdl();
    //状态修改
    //$this->_context->setState(AuditContext::$auditPassedState);

    return $data['iss']['record']['actdetailhtml']['p']['nextstage'];
  }

  public function fail(){
    $data=$this->_oprtData;
    //写入数据库的信息
    $data['iss']['info']['status'] = '审核未通过';
    //$data['iss']['record']['actdetail']='<p>《'.$data['iss']['info']['topic'].'》【审核】结果：<span class="label label-warning">审核未通过</span>。待【审批】。</p><span class="text-warning">审核意见：</span><pre>'.$data['iss']['info']['statusdescription'].'</pre>';
    $data['iss']['record']['actdetail']=0;
    //数据表“issrecord”的“actdetailhtml”字段存储结构化的信息（模型“issrecord”设置自动转换为json类型），便于前端组装HTML进行显示。
    $data['iss']['record']['actdetailhtml']=array(
                                                'p'=>array(
                                                        'prefix'=>'《'.$data['iss']['info']['topic'].'》【审核】结果：',
                                                        'spanclass'=>'warning',
                                                        'spantext'=>'审核未通过',
                                                        'nextstage'=>'待【审批】'
                                                        ),
                                                'span'=>array(
                                                        'class'=>'warning',
                                                        'text'=>'审核意见：',
                                                        ),
                                                'pre'=>array(
                                                        'class'=>'',
                                                        'text'=>$data['iss']['info']['statusdescription'],
                                                        )
                                            );
    $data['pat']['info']['status'] = '内审审核';
    //已上传附件不可删除
    $data['att']['info']['deldisplay']=0;
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($data);
   
    //父类数据库模型操作方法
    $this->_oprtMdl();
    //状态修改
    //$this->_context->setState(AuditContext::$auditFailedState);

    return $data['iss']['record']['actdetailhtml']['p']['nextstage'];

  }

  public function modify(){
    $data=$this->_oprtData;
    //写入数据库的信息
    $data['iss']['info']['status'] = '返回修改';
    //$data['iss']['record']['actdetail']='<p>《'.$data['iss']['info']['topic'].'》【审核】结果：返回撰写人【'.$data['iss']['info']['writer'].'】修改。</p><span class="text-warning">修改意见：</span><pre>'.$data['iss']['info']['statusdescription'].'</pre>';
    $data['iss']['record']['actdetail']='0';
    //数据表“issrecord”的“actdetailhtml”字段存储结构化的信息（模型“issrecord”设置自动转换为json类型），便于前端组装HTML进行显示。
    $data['iss']['record']['actdetailhtml']=array(
                                                'p'=>array(
                                                        'prefix'=>'《'.$data['iss']['info']['topic'].'》【审核】结果：',
                                                        'spanclass'=>'',
                                                        'spantext'=>'返回修改。',
                                                        'nextstage'=>'返回撰写人【'.$data['iss']['info']['writer'].'】修改'
                                                        ),
                                                'span'=>array(
                                                        'class'=>'warning',
                                                        'text'=>'修改意见：',
                                                        ),
                                                'pre'=>array(
                                                        'class'=>'',
                                                        'text'=>$data['iss']['info']['statusdescription'],
                                                        )
                                            );
    $data['pat']['info']['status'] = '内审修改';
    //已上传附件不可删除
    $data['att']['info']['deldisplay']=0;
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($data);
    
    //父类数据库模型操作方法
    $this->_oprtMdl();

    //状态修改
    //$this->_context->setState(AuditContext::$modifyingState);

    return $data['iss']['record']['actdetailhtml']['p']['nextstage'];
  }

}

?>