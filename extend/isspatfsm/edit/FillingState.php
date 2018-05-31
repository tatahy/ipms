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
    //$data['iss']['info']['status']='issVar';
//    $data['pat']['info']['status']='patVar';
//    //$dataPatch=array('pat'=>array('data'=>array('status'=>'issArray')),
////                'iss'=>array('data'=>array('status'=>'patArray'))
////                );
////    $data=array_merge($data,$dataPatch);
//    $msg= '<br>无效addNew操作<br>'.json_encode($data);
    $msg=$this->_mdl->test($data);
    return '<br>无效addNew<br>'.$msg;
    //return json_encode($data);
  }
  
  public function delete($data){
    //delete操作代码
    $this->_mdl->patDelete($data);
    
    //2.删除patRd，模型destroy()方法
    $this->_mdl->patRdDelete($data);
    
    //3.删除iss，模型destroy()方法
    $this->_mdl->issDelete($data);
    
    //4.删除issRd，模型destroy()方法
    $this->_mdl->issRdDelete($data);
    
    //5.删除att，模型destroy()方法
    $this->_mdl->attDelete($data);
    
    return 'delete结果：';
  }

}

?>