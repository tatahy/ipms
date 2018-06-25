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
    return '无效操作';
  }
  public function review(){
    return '无效操作';
  }
  public function improve(){
    return '无效操作';
  }
  public function authorize(){
    return '无效操作';
  }
  public function reject(){  
    return '无效操作';
  }
  public function close(){  
    return '无效操作';
  }
  public function addRenew(){  
    $data=$this->_oprtData;
    //写入数据库的信息
    $data['iss']['info']['status'] = '拟续费';
    //$data['iss']['record']['actdetail']='<p>《'.$data['iss']['info']['topic'].'》拟续费。待【审批】。';
    $data['iss']['record']['actdetail']='0';
    //数据表“issrecord”的“actdetailhtml”字段存储结构化的信息（模型“issrecord”设置自动转换为json类型），便于前端组装HTML进行显示。
    $data['iss']['record']['actdetailhtml']=array(
                                                'p'=>array(
                                                        'prefix'=>'《'.$data['iss']['info']['topic'].'》续费申请。',
                                                        'spanclass'=>'',
                                                        'spantext'=>'',
                                                        'nextstage'=>'。待【审批】。'
                                                        ),
                                                'span'=>array(
                                                        'class'=>'',
                                                        'text'=>''
                                                        ),
                                                'pre'=>array(
                                                        'class'=>'',
                                                        'text'=>''
                                                        )
                                            );
    $data['pat']['info']['status'] = '续费中';
    $data['pat']['record']['actdetail']='<p>《'.$data['pat']['info']['topic'].'》<span class="label label-success">续费中</span>';
    //已上传附件不可删除
    $data['att']['info']['deldisplay']=0;
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($data);
   
    //父类数据库模型操作方法
    $this->_oprtMdl();
   
    return $data['iss']['record']['actdetailhtml']['p']['nextstage'];
    
    //状态修改
    //$this->_context->setState(MaintainContext::$renewPlanningState);
    
  }
  
}

?>