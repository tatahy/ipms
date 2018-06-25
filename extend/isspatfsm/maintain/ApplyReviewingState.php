<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：MaintainState
 */

namespace isspatfsm\maintain;

use isspatfsm\maintain\MaintainState;
use isspatfsm\maintain\MaintainContext;

class ApplyReviewingState extends MaintainState{
  
  public function apply(){  
    $data=$this->_oprtData;
    //写入数据库的信息
    $data['iss']['info']['status'] = '申报提交';
    //$data['iss']['record']['actdetail']='<p>《'.$data['iss']['info']['topic'].'》【申报复核】结果：<span class="label label-info">申报提交</span>。 待【申报结果】。</p><span class="text-primary">申报提交说明：</span><pre>'.$data['iss']['info']['statusdescription'].'</pre>';
    $data['iss']['record']['actdetail']='0';
    //数据表“issrecord”的“actdetailhtml”字段存储结构化的信息（模型“issrecord”设置自动转换为json类型），便于前端组装HTML进行显示。
    $data['iss']['record']['actdetailhtml']=array(
                                                'p'=>array(
                                                        'prefix'=>'《'.$data['iss']['info']['topic'].'》【申报复核】结果：',
                                                        'spanclass'=>'info',
                                                        'spantext'=>'申报提交',
                                                        'nextstage'=>'待【申报结果】'
                                                        ),
                                                'span'=>array(
                                                        'class'=>'primary',
                                                        'text'=>'申报提交说明：',
                                                        ),
                                                'pre'=>array(
                                                        'class'=>'',
                                                        'text'=>$data['iss']['info']['statusdescription'],
                                                        )
                                            );
    $data['pat']['info']['status'] = '申报提交';
    $data['pat']['record']['actdetail']='<p>《'.$data['pat']['info']['topic'].'》<span class="label label-info">申报提交</span></p>';
    //已上传附件不可删除
    $data['att']['info']['deldisplay']=0;
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($data);
   
    //父类数据库模型操作方法
    $this->_oprtMdl();
    
    return $data['iss']['record']['actdetailhtml']['p']['nextstage'];
    
    //状态修改
    //$this->_context->setState(MaintainContext::$applySubmittingState);
    
  }
  public function review(){
    //有“_MAINTAIN”权限用户已上传附件仍然可删除
    $this->_oprtData['att']['info']['deldisplay']=1;
    $this->_mdl->setMdlData($this->_oprtData);
    //issinfo更新
    $msg=$this->_mdl->issUpdate();
    //patinfo更新
    $msg.=$this->_mdl->patUpdate();
    //attinfo更新
    $msg.=$this->_mdl->attUpdate();

    return '成功。'.$msg;

     //状态修改
    //$this->_context->setState(MaintainContext::$applyModifyingState);
  }
  public function improve(){
    $data=$this->_oprtData;
    //写入数据库的信息
    $data['iss']['info']['status'] = '申报修改';
    //$data['iss']['record']['actdetail']='<p>《'.$data['iss']['info']['topic'].'》【申报复核】结果：返回执行人【'.$data['iss']['info']['executor'].'】修改</p><span class="text-warning">修改意见：</span><pre>'.$data['iss']['info']['statusdescription'].'</pre>';
    $data['iss']['record']['actdetail']='0';
    //数据表“issrecord”的“actdetailhtml”字段存储结构化的信息（模型“issrecord”设置自动转换为json类型），便于前端组装HTML进行显示。
    $data['iss']['record']['actdetailhtml']=array(
                                                'p'=>array(
                                                        'prefix'=>'《'.$data['iss']['info']['topic'].'》【申报复核】结果：',
                                                        'spanclass'=>'',
                                                        'spantext'=>'申报提交前修改。',
                                                        'nextstage'=>'返回执行人【'.$data['iss']['info']['executor'].'】修改。'
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
    //已上传附件不可删除
    $data['att']['info']['deldisplay']=0;
    //调用IssPatModel的setMdlData()方法，设定要进行处理的数据。
    $this->_mdl->setMdlData($data);
   
    //数据库模型操作方法
     //1.patinfo更新
    $this->_mdl->patUpdate();
    //申报正式提交前的修改，patrecord无需新增记录。
    
    //2.issinfo更新
    $this->_mdl->issUpdate();

    //3.issrecord新增
    $this->_mdl->issRdCreate();

    //4.attinfo更新
    $this->_mdl->attUpdate();
   
    return $data['iss']['record']['actdetailhtml']['p']['nextstage'];

     //状态修改
    //$this->_context->setState(MaintainContext::$applyModifyingState);
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
    return '无效操作';
  }
}

?>