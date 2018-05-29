<?php

/**
 * @author tatahy
 * @copyright 2018
 * 定义一个IssPatModel类,封装isspat中对5个model的引用,封装isspat中对数据库的操作
 */

namespace isspatfsm;

use app\dashboard\model\Issinfo as IssinfoModel;
use app\dashboard\model\Issrecord as IssrecordModel;
use app\dashboard\model\Patinfo as PatinfoModel;
use app\dashboard\model\Patrecord as PatrecordModel;
use app\dashboard\model\Attinfo as AttinfoModel;

class IssPatModel{
  //静态属性，封装数据库模型的对象实例
  static $issMdl = null;
  static $issRdMdl = null;
  static $patMdl = null;
  static $patRdMdl = null;
  static $attMdl = null; 
  
  public function __construct(){
    //访问本类中定义的静态属性
    self::$issMdl = new IssinfoModel();
    self::$issRdMdl = new IssrecordModel();
    self::$patMdl = new PatinfoModel();
    self::$patRdMdl = new PatrecordModel();
    self::$attMdl = new AttinfoModel();
  }
  
  //静态方法，封装对数据库的操作。iss新增
  static function issCreate($data){ 
    self::$issMdl->create($data['iss']['data'],true);
  }
  //静态方法，封装对数据库的操作。iss更新
  static function issUpdate($data){ 
    self::$issMdl->update($data['iss']['data'],['id' => $data['iss']['id']],true);
  } 
  //静态方法，封装对数据库的操作。iss删除
  static function issDelete(){
    self::$issMdl->destroy($data['iss']['id']);
  }
  //静态方法，封装对数据库的操作。issRd新增
  static function issRdCreate($data){ 
    self::$issRdRdMdl->create($data['iss']['rdData'],true);
  }
  
  //静态方法，封装对数据库的操作。issRd更新
  static function issRdUpdate($data){ 
    //??
  }
  //静态方法，封装对数据库的操作。issRd删除
  static function issRdDelete(){
    self::$issRdMdl->destroy(['issinfo_id'=>$data['iss']['id']]);
  }
  
   //静态方法，封装对数据库的操作。pat新增
  static function patCreate($data){ 
    self::$patMdl->create($data['pat']['data'],true);
  } 
  //静态方法，封装对数据库的操作。pat更新
  static function patUpdate($data){ 
    self::$patMdl->update($data['pat']['data'],['id' => $data['pat']['id']],true);
  }
  //静态方法，封装对数据库的操作。pat删除
  static function patDelete(){
    self::$patMdl->destroy($data['pat']['id']);
  }
   
  //静态方法，封装对数据库的操作。patRd新增
  static function patRdCreate($data){ 
    self::$patRdMdl->create($data['pat']['rdData'],true);
  }
  //静态方法，封装对数据库的操作。patRd更新
  static function patRdUpdate($data){ 
    //？？
  }
  //静态方法，封装对数据库的操作。patRd删除
  static function patRdDelete($data){ 
    self::$patRdMdl->destroy(['patinfo_id'=>$data['pat']['id']]); 
  }
  
   //静态方法，封装对数据库的操作。att新增
  static function attCreate($data){ 
    self::$attMdl->create($data['att']['data'],true);
  } 
  //静态方法，封装对数据库的操作。att更新
  static function attUpdate($data){ 
    self::$attMdl->update($data['att']['data'],['id' => $data['att']['id']],true);
  }
  //静态方法，封装对数据库的操作。att删除
  static function attDelete($data){ 
    self::$attMdl->destroy(['attmap_id'=>$data['iss']['id']]);
  } 
  
  //静态方法，封装对数据库的操作。isspat状态变化
  static function issPatStatusChange($data){
    //patId!=0,issId!=0
    
    //1.patinfo更新
    self::patUpdate($data);
          
    //2.patrecord新增
    self::$patRdCreate($data);
          
    //3.issinfo更新
    self::$issUpdate($data);
                          
    //4.issrecord新增
    self::$issRdCreate($data);
          
    //5.attinfo更新
    $attData=array('deldisplay'=>0);
    $issSet=self::$issMdl->get($issId);
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
              self::$attMdl->update(array_merge($attData,$attDataPatch),['id'=>$arrAttId[$i]],true);
                            
              $msg.="附件".$name."已上传。</br>"; 
            }else{
              $msg.="附件".$name."上传失败。</br>"; 
            }
          }
        }
    
  }
  
}

?>