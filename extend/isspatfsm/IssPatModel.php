<?php

/**
 * @author tatahy
 * @copyright 2018
 * ����һ��IssPatModel��,��װisspat�ж�5��model������,��װisspat�ж����ݿ�Ĳ���
 */

namespace isspatfsm;

use app\dashboard\model\Issinfo as IssinfoModel;
use app\dashboard\model\Issrecord as IssrecordModel;
use app\dashboard\model\Patinfo as PatinfoModel;
use app\dashboard\model\Patrecord as PatrecordModel;
use app\dashboard\model\Attinfo as AttinfoModel;

class IssPatModel{
  //��̬���ԣ���װ���ݿ�ģ�͵Ķ���ʵ��
  static $issMdl = null;
  static $issRdMdl = null;
  static $patMdl = null;
  static $patRdMdl = null;
  static $attMdl = null; 
  
  public function __construct(){
    //���ʱ����ж���ľ�̬����
    self::$issMdl = new IssinfoModel();
    self::$issRdMdl = new IssrecordModel();
    self::$patMdl = new PatinfoModel();
    self::$patRdMdl = new PatrecordModel();
    self::$attMdl = new AttinfoModel();
  }
  
  //��̬��������װ�����ݿ�Ĳ�����iss����
  static function issCreate($data){ 
    self::$issMdl->create($data['iss']['data'],true);
  }
  //��̬��������װ�����ݿ�Ĳ�����iss����
  static function issUpdate($data){ 
    self::$issMdl->update($data['iss']['data'],['id' => $data['iss']['id']],true);
  } 
  //��̬��������װ�����ݿ�Ĳ�����issɾ��
  static function issDelete(){
    self::$issMdl->destroy($data['iss']['id']);
  }
  //��̬��������װ�����ݿ�Ĳ�����issRd����
  static function issRdCreate($data){ 
    self::$issRdRdMdl->create($data['iss']['rdData'],true);
  }
  
  //��̬��������װ�����ݿ�Ĳ�����issRd����
  static function issRdUpdate($data){ 
    //??
  }
  //��̬��������װ�����ݿ�Ĳ�����issRdɾ��
  static function issRdDelete(){
    self::$issRdMdl->destroy(['issinfo_id'=>$data['iss']['id']]);
  }
  
   //��̬��������װ�����ݿ�Ĳ�����pat����
  static function patCreate($data){ 
    self::$patMdl->create($data['pat']['data'],true);
  } 
  //��̬��������װ�����ݿ�Ĳ�����pat����
  static function patUpdate($data){ 
    self::$patMdl->update($data['pat']['data'],['id' => $data['pat']['id']],true);
  }
  //��̬��������װ�����ݿ�Ĳ�����patɾ��
  static function patDelete(){
    self::$patMdl->destroy($data['pat']['id']);
  }
   
  //��̬��������װ�����ݿ�Ĳ�����patRd����
  static function patRdCreate($data){ 
    self::$patRdMdl->create($data['pat']['rdData'],true);
  }
  //��̬��������װ�����ݿ�Ĳ�����patRd����
  static function patRdUpdate($data){ 
    //����
  }
  //��̬��������װ�����ݿ�Ĳ�����patRdɾ��
  static function patRdDelete($data){ 
    self::$patRdMdl->destroy(['patinfo_id'=>$data['pat']['id']]); 
  }
  
   //��̬��������װ�����ݿ�Ĳ�����att����
  static function attCreate($data){ 
    self::$attMdl->create($data['att']['data'],true);
  } 
  //��̬��������װ�����ݿ�Ĳ�����att����
  static function attUpdate($data){ 
    self::$attMdl->update($data['att']['data'],['id' => $data['att']['id']],true);
  }
  //��̬��������װ�����ݿ�Ĳ�����attɾ��
  static function attDelete($data){ 
    self::$attMdl->destroy(['attmap_id'=>$data['iss']['id']]);
  } 
  
  //��̬��������װ�����ݿ�Ĳ�����isspat״̬�仯
  static function issPatStatusChange($data){
    //patId!=0,issId!=0
    
    //1.patinfo����
    self::patUpdate($data);
          
    //2.patrecord����
    self::$patRdCreate($data);
          
    //3.issinfo����
    self::$issUpdate($data);
                          
    //4.issrecord����
    self::$issRdCreate($data);
          
    //5.attinfo����
    $attData=array('deldisplay'=>0);
    $issSet=self::$issMdl->get($issId);
        //ѭ������attMdl,���ļ������еġ�temp��Ŀ¼�ƶ���ָ��Ŀ¼
        for($i=0;$i<count($arrAttId);$i++){
          
          $fileStr=$arrAttFileObjStr[$i];
          $name=$arrAttFileName[$i];
          
          //�С�temp���ַ������ƶ���ָ��Ŀ¼
          if(substr_count($fileStr,'temp')){
            $newDir='..'.DS.'uploads'.DS.$issSet->issnum;
            
            //����AttinfoModel�ж����fileMove()����������true��Ÿ���Attinfo��
            if($attMdl->fileMove($fileStr,$name,$newDir)){
              $attDataPatch=array('num_id'=>$issSet->issnum,
                              'attmap_id'=>$issSet->id,
                              'attpath'=>$newDir.DS.$name,
                              );
              //����att
              self::$attMdl->update(array_merge($attData,$attDataPatch),['id'=>$arrAttId[$i]],true);
                            
              $msg.="����".$name."���ϴ���</br>"; 
            }else{
              $msg.="����".$name."�ϴ�ʧ�ܡ�</br>"; 
            }
          }
        }
    
  }
  
}

?>