<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：EditState
 */

namespace isspatfsm\edit;

use isspatfsm\edit\EditContex;
use isspatfsm\audit\AuditContext;

class FillingState extends EditState{
  public function addNew($data){
    //无操作
    return '<br>无效addNew操作';
    //return json_encode($data);
  }
  
  public function delete($data){
    //delete操作代码
    //1.删除pat，模型destroy()方法
    Mdl::$patMdl->destroy($data['pat']['id']);
    
    //2.删除patRd，模型destroy()方法
    Mdl::$patRdMdl->destroy(['patinfo_id'=>$data['pat']['id']]);
    
    //3.删除iss，模型destroy()方法
    Mdl::$issMdl->destroy($data['iss']['id']);
    
    //4.删除issRd，模型destroy()方法
    Mdl::$issRdMdl->destroy(['issinfo_id'=>$data['iss']['id']]);
    
    //5.删除att，模型destroy()方法
    Mdl::$attMdl->destroy(['attmap_id'=>$data['iss']['id']]);
    
    return 'delete结果：';
  }

}

?>