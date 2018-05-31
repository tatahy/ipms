<?php

/**
 * @author tatahy
 * @copyright 2018
 * ����һ��IssPatModel��,��װisspat�ж�5��model������,��װisspat�ж����ݿ�Ĳ���
 */

namespace isspatfsm;

use isspatfsm\model\Issinfo as IssinfoModel;
use isspatfsm\model\Issrecord as IssrecordModel;
use isspatfsm\model\Patinfo as PatinfoModel;
use isspatfsm\model\Patrecord as PatrecordModel;
use isspatfsm\model\Attinfo as AttinfoModel;

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
    self::$issMdl->create($data['iss']['info'],true);
  }
  //��̬��������װ�����ݿ�Ĳ�����iss����
  static function issUpdate($data){ 
    //save����������Ӱ��Ķ�������
    $id=self::$issMdl->save($data['iss']['info'],['id' => $data['iss']['id']],true);
    if($id){
        $msg="updated.";
        $msg.=self::$issMdl->get($data['iss']['id'])->statussummary;
    }else{
        $msg="no update.";
    }
    return $msg;
    
    //update��������Ҫ�޸ĵ����Լ�ֵ������
   // $id=self::$issMdl->update($data['iss']['info'],['id' => $data['iss']['id']],true);
//    return json_encode($id);
  } 
  //��̬��������װ�����ݿ�Ĳ�����issɾ��
  static function issDelete($data){
    self::$issMdl->destroy($data['iss']['id']);
  }
  //��̬��������װ�����ݿ�Ĳ�����issRd����
  static function issRdCreate($data){ 
    self::$issRdRdMdl->create($data['iss']['record'],true);
  }
  
  //��̬��������װ�����ݿ�Ĳ�����issRd����
  static function issRdUpdate($data){ 
    //??
  }
  //��̬��������װ�����ݿ�Ĳ�����issRdɾ��
  static function issRdDelete($data){
    self::$issRdMdl->destroy(['issinfo_id'=>$data['iss']['id']]);
  }
  
   //��̬��������װ�����ݿ�Ĳ�����pat����
  static function patCreate($data){ 
    self::$patMdl->create($data['pat']['info'],true);
  } 
  //��̬��������װ�����ݿ�Ĳ�����pat����
  static function patUpdate($data){ 
    self::$patMdl->update($data['pat']['info'],['id' => $data['pat']['id']],true);
  }
  //��̬��������װ�����ݿ�Ĳ�����patɾ��
  static function patDelete($data){
    self::$patMdl->destroy($data['pat']['id']);
  }
   
  //��̬��������װ�����ݿ�Ĳ�����patRd����
  static function patRdCreate($data){ 
    self::$patRdMdl->create($data['pat']['record'],true);
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
    self::$attMdl->create($data['att']['info'],true);
  } 
  //��̬��������װ�����ݿ�Ĳ�����att����
  static function attUpdate($data){ 
    $issSet=self::$issMdl->get($data['iss']['id']);
    //ѭ������attMdl,���ļ������еġ�temp��Ŀ¼�ƶ���ָ��Ŀ¼
    for($i=0;$i<count($data['att']['arrId']);$i++){
        $fileStr=$data['att']['arrFileObjStr'][$i];
        $name=$data['att']['arrFileName'][$i];
        //�С�temp���ַ������ƶ���ָ��Ŀ¼
        if(substr_count($fileStr,'temp')){
            $newDir='..'.DS.'uploads'.DS.$issSet->issnum;
            //����AttinfoModel�ж����fileMove()����������true��Ÿ���Attinfo��
            if(self::$attMdl->fileMove($fileStr,$name,$newDir)){
              $attData=array('deldisplay'=>0,
                                'num_id'=>$issSet->issnum,
                                'attmap_id'=>$issSet->id,
                                'attpath'=>$newDir.DS.$name,
                                );
              //����att
              self::$attMdl->update($attData,['id'=>$data['iss']['id'][$i]],true);
                            
              $msg.="����".$name."���ϴ���</br>"; 
            }else{
              $msg.="����".$name."�ϴ�ʧ�ܡ�</br>"; 
            }
          }
    }
    
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
  
  //��̬���������ԡ�
  static function test($data){ 
    //$data['iss']['info']['status']='issVarT';
//    $data['pat']['info']['status']='patVarT';
//    $msg= '<br>'.json_encode($data).'<br>';
    $data=array('iss'=>array('id'=>4,'info'=>array('abstract'=>'����','num_id'=>01))
                );
    $msg=self::issUpdate($data);
    return $msg;
  }
  
}

?>