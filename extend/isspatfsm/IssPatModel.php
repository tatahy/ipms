<?php
namespace isspatfsm;

use isspatfsm\model\Issinfo as IssinfoModel;
use isspatfsm\model\Issrecord as IssrecordModel;
use isspatfsm\model\Patinfo as PatinfoModel;
use isspatfsm\model\Patrecord as PatrecordModel;
use isspatfsm\model\Attinfo as AttinfoModel;

/**
 * @author tatahy
 * @copyright 2018
 * 定义一个IssPatModel类,封装isspat中对5个model的引用,封装isspat中对数据库的操作
 */
 

class IssPatModel {
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
    self::$issMdl->create($data['iss']['info'],true);
  }
  //静态方法，封装对数据库的操作。iss更新
  static function issUpdate($data){ 
    //save方法返回受影响的对象数。
    $id=self::$issMdl->save($data['iss']['info'],['id' => $data['iss']['id']],true);
    if($id){
        $msg="updated.";
        $msg.=self::$issMdl->get($data['iss']['id'])->topic;
    }else{
        $msg="no update.";
    }
    return $msg;
    
    //update方法返回要修改的属性键值对数组
   // $id=self::$issMdl->update($data['iss']['info'],['id' => $data['iss']['id']],true);
//    return json_encode($id);
  } 
  //静态方法，封装对数据库的操作。iss删除
  static function issDelete($data){
    self::$issMdl->destroy($data['iss']['id']);
  }
  //静态方法，封装对数据库的操作。issRd新增
  static function issRdCreate($data){ 
    self::$issRdRdMdl->create($data['iss']['record'],true);
  }
  
  //静态方法，封装对数据库的操作。issRd更新
  static function issRdUpdate($data){ 
    //??
  }
  //静态方法，封装对数据库的操作。issRd删除
  static function issRdDelete($data){
    self::$issRdMdl->destroy(['issinfo_id'=>$data['iss']['id']]);
  }
  
   //静态方法，封装对数据库的操作。pat新增
  static function patCreate($data){ 
    self::$patMdl->create($data['pat']['info'],true);
  } 
  //静态方法，封装对数据库的操作。pat更新
  static function patUpdate($data){ 
    self::$patMdl->update($data['pat']['info'],['id' => $data['pat']['id']],true);
  }
  //静态方法，封装对数据库的操作。pat删除
  static function patDelete($data){
    self::$patMdl->destroy($data['pat']['id']);
  }
   
  //静态方法，封装对数据库的操作。patRd新增
  static function patRdCreate($data){ 
    self::$patRdMdl->create($data['pat']['record'],true);
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
    self::$attMdl->create($data['att']['info'],true);
  } 
  //静态方法，封装对数据库的操作。att更新
  static function attUpdate($data){ 
    $issSet=self::$issMdl->get($data['iss']['id']);
    //循环更新attMdl,将文件从现有的‘temp’目录移动到指定目录
    for($i=0;$i<count($data['att']['arrId']);$i++){
        $fileStr=$data['att']['arrFileObjStr'][$i];
        $name=$data['att']['arrFileName'][$i];
        //有‘temp’字符串才移动到指定目录
        if(substr_count($fileStr,'temp')){
            $newDir='..'.DS.'uploads'.DS.$issSet->issnum;
            //调用AttinfoModel中定义的fileMove()方法，返回true后才更新Attinfo表
            if(self::$attMdl->fileMove($fileStr,$name,$newDir)){
              $attData=array('deldisplay'=>0,
                                'num_id'=>$issSet->issnum,
                                'attmap_id'=>$issSet->id,
                                'attpath'=>$newDir.DS.$name,
                                );
              //更新att
              self::$attMdl->update($attData,['id'=>$data['iss']['id'][$i]],true);
                            
              $msg.="附件".$name."已上传。</br>"; 
            }else{
              $msg.="附件".$name."上传失败。</br>"; 
            }
          }
    }
    
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
  
  //静态方法，测试。
  public function test($data){ 
    //$data['iss']['info']['status']='issVarT';
//    $data['pat']['info']['status']='patVarT';
//    $msg= '<br>'.json_encode($data).'<br>';
//\u6797\u5219\u5f90：林则徐
    
    $data=array('iss'=>array('id'=>4,'info'=>array('abstract'=>'林则徐','num_id'=>2))
                );
    $msg=self::issUpdate($data);
    //$msg=json_encode($data,JSON_UNESCAPED_UNICODE);
    //$msg.=json_last_error();
    return $msg;
  }
  
}

?>