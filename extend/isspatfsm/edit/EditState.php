<?php

/**
 * @author tatahy
 * @copyright 2018
 * 定义一个isspat,_EDIT权限下的state抽象类“EditState”
 */

namespace isspatfsm\edit;
//引入操作数据库的5个模型
use isspatfsm\OprtModel as Mdl;

use isspatfsm\edit\EditContext;

abstract class EditState{
  
  //定义一个环境属性，继承的子类才有，属性值是EditContext对象实例。
  protected $_context;
  
  public function __construct(){
    //访问本类中定义的静态属性
    
  }
  
  //设定上下文环境
  public function setContext(EditContext $context){
	$this->_context = $context;
 }
  
  //_EDIT的4种操作
  public abstract function addNew($data);
  public abstract function delete($data);
  
  public function submit($data){
    //patId!=0,issId!=0
    
    //1.patinfo更新
    //patData
    //更新，模型update()方法
    Mdl::$patMdl->update($data['pat']['data'],['id' => $data['pat']['id']],true);
          
    //2.patrecord新增
    //新增，模型create()方法
    Mdl::$patRdMdl->create($data['pat']['rdData'],true);
          
    //3.issinfo更新
    //更新，模型update()方法
    Mdl::$issMdl->update($data['iss']['data'],['id' => $data['iss']['id']],true);
                          
    //4.issrecord新增
    //新增，模型create()方法
    Mdl::$issRdMdl->create($data['iss']['rdData'],true);
          
    //5.attinfo更新
    $attData=array('deldisplay'=>0);
    $issSet=Mdl::$issMdl->get($issId);
        //循环更新attMdl,将文件从现有的‘temp’目录移动到指定目录
        for($i=0;$i<count($arrAttId);$i++){
          
          $fileStr=$arrAttFileObjStr[$i];
          $name=$arrAttFileName[$i];
          
          //有‘temp’字符串才移动到指定目录
          if(substr_count($fileStr,'temp')){
            $newDir='..'.DS.'uploads'.DS.$issSet->issnum;
            
            //调用AttinfoModel中定义的fileMove()方法，返回true后才更新Attinfo表
            if($attMdl->fileMove($fileStr,$name,$newDir)){
              $attDataPatch=array('num_id'=>$issSet->issnum,
                              'attmap_id'=>$issSet->id,
                              'attpath'=>$newDir.DS.$name,
                              );
              //更新att
              Mdl::$attMdl->update(array_merge($attData,$attDataPatch),['id'=>$arrAttId[$i]],true);
                            
              $msg.="附件".$name."已上传。</br>"; 
            }else{
              $msg.="附件".$name."上传失败。</br>"; 
            }
          }
        }
    
    //状态修改
    $this->_context->setState(AuditContext::$checkingState);
    
    return 'submit结果：';
    //动作委托为submit
    //$this->_context->getState()->submit();
  }
  
  public function update($data){
    //patId!=0,issId!=0
    
    //1.patinfo更新
    //更新，模型update()方法
    Mdl::$patMdl->update($data['pat']['data'],['id'=>$data['pat']['id']],true);
    
    //2.patRd更新？？
          
    //3.issinfo更新
    //更新，模型update()方法
    Mdl::$issMdl->update($data['iss']['data'],['id'=>$data['iss']['id']],true);
    
    //4.issRd更新？？
                          
    //5.attinfo更新    
    return 'update结果：';
  }
  
}

?>