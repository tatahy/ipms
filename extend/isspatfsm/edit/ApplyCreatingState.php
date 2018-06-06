<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：EditState
 */

namespace isspatfsm\edit;

use isspatfsm\edit\EditContex;
use isspatfsm\edit\EditState;

class ApplyCreatingState extends EditState
{

  public function addNew()
  {
    $data = $this->_oprtData;
        //1.patinfo新增，
        $data['pat']['info']['status']='内审';
        //调用IssPatModel的setMdlData()方法，设定所需进行处理的数据。
        $this->_mdl->setMdlData($data);
        //patCreate方法返回的是模型实例
        $pat=$this->_mdl->patCreate();
    
        //2.patrecord新增
        $data['pat']['record']['num']=$pat->patnum;
        $data['pat']['record']['patinfo_id']=$pat->id;
        //调用IssPatModel的setMdlData()方法，设定所需进行处理的数据。
        $this->_mdl->setMdlData($data);
        $this->_mdl->patRdCreate();
    
        //3.issinfo新增
        $data['iss']['info']['status']='填报';
        $data['iss']['info']['issmap_id']=$pat->id;
        $data['iss']['info']['addnewdate']=date('Y-M-d H:i:s');
        //调用IssPatModel的setMdlData()方法，设定所需进行处理的数据。
        $this->_mdl->setMdlData($data);
        //issCreate方法返回的是模型实例
        $iss=$this->_mdl->issCreate();
    
        //4.issrecord新增
        $data['iss']['record']['num']=$iss->issnum;
        $data['iss']['record']['issinfo_id']=$iss->id;
        //调用IssPatModel的setMdlData()方法，设定所需进行处理的数据。
        $this->_mdl->setMdlData($data);
        $this->_mdl->issRdCreate();
    
        //5.attinfo更新???
    
        //状态修改
        $this->_context->setState(EditContext::$FillingState);
    
        return '<br>addnew操作结果：《'.$iss->topic.'》新增成功';
    //return json_encode($data, JSON_UNESCAPED_UNICODE) . json_last_error();
  }

  public function delete()
  {
    return '<br>无效delete操作';
    //return false;
  }

  public function submit()
  {
    //oprt=="submit"要进行的操作
    return '<br>无效submit操作';
    //return false;

  }

  public function update()
  {
    return '<br>无效update操作';
    //return false;
  }

}

?>