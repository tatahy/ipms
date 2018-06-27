<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：ApproveState
 */

namespace app\dashboard\model\isspatfsm\approve;

use app\dashboard\model\isspatfsm\approve\ApproveState;
use app\dashboard\model\isspatfsm\approve\ApproveContext;

class RenewPlanningState extends ApproveState{
  
  public function permit(){
    $data=$this->_oprtData;
    //写入数据库的信息
    $data['iss']['info']['status'] = '准予续费';
    //$data['iss']['record']['actdetail']='<p>《'.$data['iss']['info']['topic'].'》<span class="label label-success">准予续费</span>。待【执行】。</p>'; 
    $data['iss']['record']['actdetail']='0';
    //数据表“issrecord”的“actdetailhtml”字段存储结构化的信息（模型“issrecord”设置自动转换为json类型），便于前端组装HTML进行显示。
    $data['iss']['record']['actdetailhtml']=array(
                                                'p'=>array(
                                                        'prefix'=>'《'.$data['iss']['info']['topic'].'》【审批】结果：',
                                                        'spanclass'=>'success',
                                                        'spantext'=>'准予续费',
                                                        'nextstage'=>'待【续费提交】'
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
    $data['pat']['info']['status'] = '续费中';
    $data['pat']['record']['actdetail']='<p>《'.$data['pat']['info']['topic'].'》<span class="label label-success">续费中</span></p>';
    //已上传附件不可删除
    $data['att']['info']['deldisplay']=0;
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($data);
    
    //父类数据库模型操作方法
    $this->_oprtMdl();
    
    return $data['iss']['record']['actdetailhtml']['p']['nextstage'];
    
    //状态修改
    //$this->_context->setState(ApproveContext::$renewApprovedState);
  }
  public function veto(){
    //写入数据库的信息
    $data['iss']['info']['status'] = '放弃续费';
    //$data['iss']['record']['actdetail']='<p>《'.$data['iss']['info']['topic'].'》<span class="label label-default">放弃续费</span>。待【完结】。</p>'; 
    $data['iss']['record']['actdetail']='0';
    //数据表“issrecord”的“actdetailhtml”字段存储结构化的信息（模型“issrecord”设置自动转换为json类型），便于前端组装HTML进行显示。
    $data['iss']['record']['actdetailhtml']=array(
                                                'p'=>array(
                                                        'prefix'=>'《'.$data['iss']['info']['topic'].'》【审批】结果：',
                                                        'spanclass'=>'danger',
                                                        'spantext'=>'放弃续费',
                                                        'nextstage'=>'待【完结】'
                                                        ),
                                                'span'=>array(
                                                        'class'=>'danger',
                                                        'text'=>'放弃意见：',
                                                        ),
                                                'pre'=>array(
                                                        'class'=>'',
                                                        'text'=>$data['iss']['info']['statusdescription'],
                                                        )
                                            );
    $data['pat']['info']['status'] = '放弃续费';
    $data['pat']['record']['actdetail']='<p>《'.$data['pat']['info']['topic'].'》<span class="label label-default">放弃续费</span></p>';
    //已上传附件不可删除
    $data['att']['info']['deldisplay']=0;
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($data);
    
    //父类数据库模型操作方法
    $this->_oprtMdl();
    
    return $data['iss']['record']['actdetailhtml']['p']['nextstage'];
    
    //状态修改
    //$this->_context->setState(ApproveContext::$renewVetoedState);
    
  }
  public function complete(){
    
    return '无效操作';
  }
  
  
}

?>