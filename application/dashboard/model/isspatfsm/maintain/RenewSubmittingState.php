<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：MaintainState
 */

namespace app\dashboard\model\isspatfsm\maintain;

use app\dashboard\model\isspatfsm\maintain\MaintainState;
use app\dashboard\model\isspatfsm\maintain\MaintainContext;

class RenewSubmittingState extends MaintainState{
  public function apply(){  
    return '无效操作';
  }
  public function review(){
    return '无效操作';
  }
  public function improve(){
    return '无效操作';
  }
  public function authorize(){
    $data=$this->_oprtData;
    //写入数据库的信息
    $data['iss']['info']['status'] = '续费授权';
    //$data['iss']['record']['actdetail']='<p>《'.$data['iss']['info']['topic'].'》<span class="label label-success">续费授权</span>。待【完结】。</p>';
    $data['iss']['record']['actdetail']='0';
    //数据表“issrecord”的“actdetailhtml”字段存储结构化的信息（模型“issrecord”设置自动转换为json类型），便于前端组装HTML进行显示。
    $data['iss']['record']['actdetailhtml']=array(
                                                'p'=>array(
                                                        'prefix'=>'《'.$data['iss']['info']['topic'].'》【续费结果】：',
                                                        'spanclass'=>'success',
                                                        'spantext'=>'续费授权',
                                                        'nextstage'=>'。待【完结】。'
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
    $data['pat']['info']['status'] = '续费授权';
    $data['pat']['record']['actdetail']='<p>《'.$data['pat']['info']['topic'].'》<span class="label label-success">续费授权</span></p>';
    //已上传附件不可删除
    $data['att']['info']['deldisplay']=0;
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($data);
   
    //父类数据库模型操作方法
    $this->_oprtMdl();
   
    return $data['iss']['record']['actdetailhtml']['p']['nextstage'];
    
    //状态修改
    //$this->_context->setState(MaintainContext::$renewAuthorizedState);
   
  }
  
  public function reject(){  
    $data=$this->_oprtData;
    //写入数据库的信息
    $data['iss']['info']['status'] = '专利驳回';
    //$data['iss']['record']['actdetail']='<p>《'.$data['iss']['info']['topic'].'》<span class="label label-danger">专利驳回</span>。待【完结】。</p><span class="text-danger">驳回原因：</span><pre>'.$data['iss']['info']['statusdescription'].'</pre>';
    $data['iss']['record']['actdetail']='0';
    //数据表“issrecord”的“actdetailhtml”字段存储结构化的信息（模型“issrecord”设置自动转换为json类型），便于前端组装HTML进行显示。
    $data['iss']['record']['actdetailhtml']=array(
                                                'p'=>array(
                                                        'prefix'=>'《'.$data['iss']['info']['topic'].'》【续费结果】：',
                                                        'spanclass'=>'danger',
                                                        'spantext'=>'驳回',
                                                        'nextstage'=>'。待【完结】。'
                                                        ),
                                                'span'=>array(
                                                        'class'=>'danger',
                                                        'text'=>'驳回原因：'
                                                        ),
                                                'pre'=>array(
                                                        'class'=>'',
                                                        'text'=>$data['iss']['info']['statusdescription']
                                                        )
                                            );
    $data['pat']['info']['status'] = '驳回';
    $data['pat']['record']['actdetail']='<p>《'.$data['pat']['info']['topic'].'》<span class="label label-danger">驳回</span></p><span class="text-danger">驳回原因：</span><pre>'.$data['iss']['info']['statusdescription'].'</pre>';
    //已上传附件不可删除
    $data['att']['info']['deldisplay']=0;
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($data);
   
    //父类数据库模型操作方法
    $this->_oprtMdl();
   
    return $data['iss']['record']['actdetailhtml']['p']['nextstage'];
    
    //状态修改
    //$this->_context->setState(MaintainContext::$applyRejectedState);
  }
  public function addRenew(){  
    return '无效操作';
  }
  public function close(){  
    return '无效操作';
  }
  
}

?>