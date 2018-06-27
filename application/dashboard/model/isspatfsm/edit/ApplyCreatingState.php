<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：EditState
 */

namespace app\dashboard\model\isspatfsm\edit;

use app\dashboard\model\isspatfsm\edit\EditContex;
use app\dashboard\model\isspatfsm\edit\EditState;

class ApplyCreatingState extends EditState
{

  public function addNew()
  {
    $data = $this->_oprtData;
        //1.patinfo新增，
        $data['pat']['info']['status']='填报';
        //调用IssPatModel的setMdlData()方法，设定所需进行处理的数据。
        $this->_mdl->setMdlData($data);
        //patCreate方法返回的是模型实例
        $pat=$this->_mdl->patCreate();
        //return '<br>addnew操作结果：《'.json_encode($pat,JSON_UNESCAPED_UNICODE).'》新增成功';
    
        //2.patrecord新增
        $data['pat']['record']['num']=$pat->patnum;
        $data['pat']['record']['patinfo_id']=$pat->id;
        $data['pat']['record']['actdetail']='新增《'.$pat->topic.'》';
        $data['pat']['record']['note']='新增《'.$pat->topic.'》';
        //调用IssPatModel的setMdlData()方法，设定所需进行处理的数据。
        $this->_mdl->setMdlData($data);
        $this->_mdl->patRdCreate();
    
        //3.issinfo新增
        $data['iss']['info']['status']='填报';
        $data['iss']['info']['statusdescription']='填报专利事务。';
        $data['iss']['info']['issmap_id']=$pat->id;
        $data['iss']['info']['addnewdate']=date('Y-M-d H:i:s');
        //$data['iss']['info']['oprt_user']['time']=time();
        //调用IssPatModel的setMdlData()方法，设定所需进行处理的数据。
        $this->_mdl->setMdlData($data);
        //issCreate方法返回的是模型实例
        $iss=$this->_mdl->issCreate();
    
        //4.issrecord新增
        $data['iss']['record']['num']=$iss->issnum;
        $data['iss']['record']['issinfo_id']=$iss->id;
        //$data['iss']['record']['actdetail']='新增《'.$iss->topic.'》';
        $data['iss']['record']['actdetail']='0';
        //数据表“issrecord”的“actdetailhtml”字段存储结构化的信息（模型“issrecord”设置自动转换为json类型），便于前端组装HTML进行显示。
        $data['iss']['record']['actdetailhtml']=array(
                                                    'p'=>array(
                                                            'prefix'=>'新增《'.$iss->topic.'》',
                                                            'spanclass'=>'',
                                                            'spantext'=>'',
                                                            'nextstage'=>'待【提交】'
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
    //调用IssPatModel的setMdlData()方法，设定所需进行处理的数据。
    $this->_mdl->setMdlData($data);
    $this->_mdl->issRdCreate();
    
    //5.attinfo更新
    //确保使用的是create的issId
    $data['iss']['id']=$iss->id;
    //附件可删除
    $data['att']['info']['deldisplay']=1;
        
        
    //调用IssPatModel的setMdlData()方法，设定所需进行处理的数据。
    $this->_mdl->setMdlData($data);
    $this->_mdl->attUpdate();
    
    //状态修改
    //$this->_context->setState(EditContext::$fillingState);
    
    return '成功！'.$data['iss']['record']['actdetailhtml']['p']['nextstage'];
        //return array('msg'=>$msg,'topic'=>$iss->topic,'patId'=>$pat->id,'issId'=>$iss->id));
  }

  public function delete()
  {
     return '无效操作';
  }

  public function submit()
  {
    //oprt=="submit"要进行的操作
    return '无效操作';
    //return false;

  }

  public function update()
  {
    return '无效操作';
  }

}

?>