<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：ExecuteState
 */

namespace isspatfsm\execute;

use isspatfsm\execute\ExecuteState;
use isspatfsm\execute\ExecuteContext;

class ApplyApprovedState extends ExecuteState{
  
  public function refuse(){
    $data=$this->_oprtData;
    //写入数据库的信息
    $data['iss']['info']['status'] = '变更申请';
    $data['iss']['info']['executerchangemsg'] = $data['iss']['info']['statusdescription'] ;
    //$data['iss']['record']['actdetail']='<p>《'.$data['iss']['info']['topic'].'》提交变更申请。待【审批】</p>';
    $data['iss']['record']['actdetail']=0;
    //数据表“issrecord”的“actdetailhtml”字段存储结构化的信息（模型“issrecord”设置自动转换为json类型），便于前端组装HTML进行显示。
    $data['iss']['record']['actdetailhtml']=array(
                                                'p'=>array(
                                                        'prefix'=>'《'.$data['iss']['info']['topic'].'》提交变更申请。',
                                                        'spanclass'=>'',
                                                        'spantext'=>'',
                                                        'nextstage'=>'待【审批】'
                                                        ),
                                                'span'=>array(
                                                        'class'=>'primary',
                                                        'text'=>'变更申请说明：',
                                                        ),
                                                'pre'=>array(
                                                        'class'=>'',
                                                        'text'=>$data['iss']['info']['statusdescription'],
                                                        )
                                            );
    //是否有执行人的变更
    if($data['iss']['info']['executer']!=$data['iss']['info']['executerchangeto']){
        $data['iss']['record']['actdetailhtml']['pre']['text']='执行人申请由['.$data['iss']['info']['executer'].']变更为【'.$data['iss']['info']['executerchangeto'].'】\n'.$data['iss']['info']['statusdescription'];
    }
    
    //已上传附件不可删除
    $data['att']['info']['deldisplay']=0;
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($data);
    
    //issinfo更新
    $this->_mdl->issUpdate();
    //issrecord新增
    $this->_mdl->issRdCreate();
    
    //状态修改
    //$this->_context->setState(ExecuteContext::$exeChangingState);
  
    return $data['iss']['record']['actdetailhtml']['p']['nextstage'];
  }
  public function report(){
    return '无效操作';
  }
  public function finish(){
    return '无效操作';
  }
  
}

?>