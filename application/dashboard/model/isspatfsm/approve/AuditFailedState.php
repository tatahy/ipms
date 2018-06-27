<?php
/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：ApproveState
 */

namespace app\dashboard\model\isspatfsm\approve;

use app\dashboard\model\isspatfsm\approve\ApproveState;
use app\dashboard\model\isspatfsm\approve\ApproveContext;

class AuditFailedState extends ApproveState{
  
  public function permit(){
    $data=$this->_oprtData;
    //写入数据库的信息
    $data['iss']['info']['status'] = '批准申报';
    //$data['iss']['record']['actdetail']='<p>《'.$data['iss']['info']['topic'].'》<span class="label label-success">批准申报</span>。待【执行】。</p>';
    $data['iss']['record']['actdetail']='0';
    //数据表“issrecord”的“actdetailhtml”字段存储结构化的信息（模型“issrecord”设置自动转换为json类型），便于前端组装HTML进行显示。
    $data['iss']['record']['actdetailhtml']=array(
                                                'p'=>array(
                                                        'prefix'=>'《'.$data['iss']['info']['topic'].'》【审批】结果：',
                                                        'spanclass'=>'success',
                                                        'spantext'=>'批准申报',
                                                        'nextstage'=>'待【执行】'
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
    $data['pat']['info']['status'] = '内审批准';
    $data['pat']['record']['actdetail']='<p>《'.$data['pat']['info']['topic'].'》<span class="label label-success">内审批准</span></p>';
    //已上传附件不可删除
    $data['att']['info']['deldisplay']=0;
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($data);
    
    //父类数据库模型操作方法
    $this->_oprtMdl();
    
    return $data['iss']['record']['actdetailhtml']['p']['nextstage'];
  }
  public function veto(){
    $data=$this->_oprtData;
    //写入数据库的信息
    $data['iss']['info']['status'] = '否决申报';
    //$data['iss']['record']['actdetail']='<p>《'.$data['iss']['info']['topic'].'》<span class="label label-danger">否决申报</span>。待【完结】。</p><span class="text-danger">否决意见：</span><pre>'.$data['iss']['info']['statusdescription'].'</pre>';
    $data['iss']['record']['actdetail']='0';
    //数据表“issrecord”的“actdetailhtml”字段存储结构化的信息（模型“issrecord”设置自动转换为json类型），便于前端组装HTML进行显示。
    $data['iss']['record']['actdetailhtml']=array(
                                                'p'=>array(
                                                        'prefix'=>'《'.$data['iss']['info']['topic'].'》【审批】结果：',
                                                        'spanclass'=>'danger',
                                                        'spantext'=>'否决申报',
                                                        'nextstage'=>'待【完结】'
                                                        ),
                                                'span'=>array(
                                                        'class'=>'danger',
                                                        'text'=>'否决意见：',
                                                        ),
                                                'pre'=>array(
                                                        'class'=>'',
                                                        'text'=>$data['iss']['info']['statusdescription'],
                                                        )
                                            );
    $data['pat']['info']['status'] = '内审否决';
    $data['pat']['record']['actdetail']='<p>《'.$data['pat']['info']['topic'].'》<span class="label label-danger">内审否决</span></p><span class="text-danger">否决意见：</span><pre>'.$data['iss']['info']['statusdescription'].'</pre>';
    //已上传附件不可删除
    $data['att']['info']['deldisplay']=0;
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($data);
    
    //父类数据库模型操作方法
    $this->_oprtMdl();
    
    //状态修改
    //$this->_context->setState(ApproveContext::$applyVetoedState);
    
    return $data['iss']['record']['actdetailhtml']['p']['nextstage'];

  }
  public function complete(){
    $data=$this->_oprtData;
    //写入数据库的信息
    $data['iss']['info']['status'] = '修改完善';
    //$data['iss']['record']['actdetail']='<p>《'.$data['iss']['info']['topic'].'》<span class="label label-warning">修改完善</span></p><span class="text-waring">修改意见：</span><pre>'.$data['iss']['info']['statusdescription'].'</pre>';
    $data['iss']['record']['actdetail']='0';
    //数据表“issrecord”的“actdetailhtml”字段存储结构化的信息（模型“issrecord”设置自动转换为json类型），便于前端组装HTML进行显示。
    $data['iss']['record']['actdetailhtml']=array(
                                                'p'=>array(
                                                        'prefix'=>'《'.$data['iss']['info']['topic'].'》【审批】结果：',
                                                        'spanclass'=>'warning',
                                                        'spantext'=>'修改完善',
                                                        'nextstage'=>'返回撰写人【'.$data['iss']['info']['writer'].'】修改完善。'
                                                        ),
                                                'span'=>array(
                                                        'class'=>'warning',
                                                        'text'=>'否决意见：',
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
    
    //issinfo更新
    $this->_mdl->issUpdate();
    //issrecord新增
    $this->_mdl->issRdCreate();
    //patinfo更新
    $this->_mdl->patUpdate();
    //attinfo更新
    $this->_mdl->attUpdate();
    
    //状态修改
    //$this->_context->setState(ApproveContext::$applyVetoedState);
    return $data['iss']['record']['actdetailhtml']['p']['nextstage'];
    
  }
  
  
  
}

?>