<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：ApproveState
 */

namespace app\dashboard\model\isspatfsm\approve;

use app\dashboard\model\isspatfsm\approve\ApproveState;
use app\dashboard\model\isspatfsm\approve\ApproveContext;

class ExeChangingState extends ApproveState{
  
  public function permit(){
    $data=$this->_oprtData;
    //写入数据库的信息
    $data['iss']['info']['status'] = '准予变更';
    //$data['iss']['record']['actdetail']='<p>《'.$data['iss']['info']['topic'].'》<span class="label label-success">准予变更</span>。待【执行】。</p>';
    $data['iss']['record']['actdetail']='0';
    //数据表“issrecord”的“actdetailhtml”字段存储结构化的信息（模型“issrecord”设置自动转换为json类型），便于前端组装HTML进行显示。
    $data['iss']['record']['actdetailhtml']=array(
                                                'p'=>array(
                                                        'prefix'=>'《'.$data['iss']['info']['topic'].'》变更申请【审批】结果：',
                                                        'spanclass'=>'success',
                                                        'spantext'=>'准予变更',
                                                        'nextstage'=>'待【执行】'
                                                        ),
                                                'span'=>array(
                                                        'class'=>'warning',
                                                        'text'=>'准予意见：',
                                                        ),
                                                'pre'=>array(
                                                        'class'=>'',
                                                        'text'=>$data['iss']['info']['statusdescription'],
                                                        )
                                            );
    //是否有变更意见
    if(empty($data['iss']['info']['statusdescription'])){
        $data['iss']['record']['actdetailhtml']=array(
                                                'span'=>array(
                                                        'class'=>'',
                                                        'text'=>'',
                                                        ),
                                            );
    }
    //是否有执行人的变更
    if($data['iss']['info']['executer']!=$data['iss']['info']['executerchangeto']){
        $data['iss']['info']['executer']=$data['iss']['info']['executerchangeto'];
        //$data['iss']['record']['actdetail'].='<p>执行人由【'.$data['iss']['info']['executer'].'】变更为<span class="text-primary">【'.$data['iss']['info']['executerchangeto'].'】</span></p>';
        $data['iss']['record']['actdetailhtml']=array(
                                                'span'=>array(
                                                        'class'=>'warning',
                                                        'text'=>'准予意见：',
                                                        ),
                                                'pre'=>array(
                                                        'class'=>'',
                                                        'text'=>'执行人由['.$data['iss']['info']['executer'].']变更为【'.$data['iss']['info']['executerchangeto'].'】\\n'.$data['iss']['info']['statusdescription'],
                                                        )
                                            );
    }
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
    
    return '“变更申请”。'.$data['iss']['record']['actdetailhtml']['p']['nextstage'];
        
    //状态修改
    //$this->_context->setState(ApproveContext::$exeChApprovedState);

  }
  public function veto(){
    $data=$this->_oprtData;
    //写入数据库的信息
    $data['iss']['info']['status'] = '否决变更';
    $data['iss']['info']['statusdescription'];
    //$data['iss']['record']['actdetail']='<p>《'.$data['iss']['info']['topic'].'》<span class="label label-danger">否决变更</span>。待【执行】。</p><span class="text-danger">否决意见：</span><pre>'.$data['iss']['info']['statusdescription'].'</pre>';
    $data['iss']['record']['actdetail']='0';
    //数据表“issrecord”的“actdetailhtml”字段存储结构化的信息（模型“issrecord”设置自动转换为json类型），便于前端组装HTML进行显示。
    $data['iss']['record']['actdetailhtml']=array(
                                                'p'=>array(
                                                        'prefix'=>'《'.$data['iss']['info']['topic'].'》变更申请【审批】结果：',
                                                        'spanclass'=>'danger',
                                                        'spantext'=>'否决变更',
                                                        'nextstage'=>'待【执行】'
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
    
    //是否有执行人的变更
    if($data['iss']['info']['executer']!=$data['iss']['info']['executerchangeto']){
        //$data['iss']['record']['actdetail'].='<p>执行人为【'.$data['iss']['info']['executer'].'】</p>';
        $data['iss']['record']['actdetail']='0';
        $data['iss']['record']['actdetailhtml']['pre']['text']='执行人由['.$data['iss']['info']['executer'].']变更为【'.$data['iss']['info']['executerchangeto'].'】\\n'.$data['iss']['info']['statusdescription'];
    }
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
    
    return '“变更申请”。'.$data['iss']['record']['actdetailhtml']['p']['nextstage'];
 
    //状态修改
    //$this->_context->setState(ApproveContext::$exeChVetoedState);
    
  }
  public function complete(){
    
    return '无效操作';
  }
  
  
}

?>