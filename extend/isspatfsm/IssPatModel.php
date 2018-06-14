<?php

namespace isspatfsm;

//use isspatfsm\model\Issinfo as IssinfoModel;
//use isspatfsm\model\Issrecord as IssrecordModel;
//use isspatfsm\model\Patinfo as PatinfoModel;
//use isspatfsm\model\Patrecord as PatrecordModel;
//use isspatfsm\model\Attinfo as AttinfoModel;

//引入5个数据表对应的模型
use app\dashboard\model\Issinfo as IssinfoModel;
use app\dashboard\model\Issrecord as IssrecordModel;
use app\dashboard\model\Patinfo as PatinfoModel;
use app\dashboard\model\Patrecord as PatrecordModel;
use app\dashboard\model\Attinfo as AttinfoModel;

/**
 * @author tatahy
 * @copyright 2018
 * IssPatModel类,封装isspat中对5个model的引用,封装isspat中对数据库的操作
 */

class IssPatModel
{
  //静态属性，封装数据库模型对象的实例
  static $issMdl = null;
  static $issRdMdl = null;
  static $patMdl = null;
  static $patRdMdl = null;
  static $attMdl = null;
  //类外部代码不可访问，保护要进行操作的数据
  /**
   * 该数据的结构为,分别与数据库中的5个表对应：patinfo,patrecord,issinfo,issrecord,attinfo
   * $data = array(
   * 'pat' => array(
   * 'id' => 3,
   * 'info' => array('abstract' => '啊patinfo.abstract',...),
   * 'record' => array('act' => '哈patrecord.act',...)
   * ),
   * 'iss' => array(
   * 'id' => 1,
   * 'info' => array('abstract' => '也issinfo.abstract',...),
   * 'record' => array('act' => '由patrecord.act',...)
   * ),
   * 'att' => array(
   * 'arrId' => [],
   * 'arrFileName' => [],
   * 'arrFileObjStr' => []
   * )
   * );
   */
  //数据库模型操作的数据
  protected $_mdlData;

  public function __construct()
  {
    //赋值本类中定义的静态属性为数据库模型对象的实例
    self::$issMdl = new IssinfoModel();
    self::$issRdMdl = new IssrecordModel();
    self::$patMdl = new PatinfoModel();
    self::$patRdMdl = new PatrecordModel();
    self::$attMdl = new AttinfoModel();
  }

  public function setMdlData($data)
  {
    $this->_mdlData = $data;

  }

  //封装对数据库的操作。iss新增
  public function issCreate()
  {
    //模型的create方法，返回的是模型的对象实例
    return self::$issMdl->create($this->_mdlData['iss']['info'], true);
  }
  //静态方法，封装对数据库的操作。iss更新
  static function issUpdate()
  {
    //save方法返回受影响的对象数。
    $n = self::$issMdl->save($this->_mdlData['iss']['info'], ['id' => $this->_mdlData['iss']['id']], true);
    if ($n)
    {
      $msg = '<br>《'.self::$issMdl->get($this->_mdlData['iss']['id'])->topic.'》updated.';
    } else
    {
      $msg = "<br>no update.";
    }
    return $msg;

    //update方法返回要修改的属性键值对数组
    // $id=self::$issMdl->update($data['iss']['info'],['id' => $data['iss']['id']],true);
    //    return json_encode($id);
  }
  //静态方法，封装对数据库的操作。iss删除
 // static function issDelete()
//  {
//    //模型的destroy方法，返回的值是影响的记录数
//    return self::$issMdl->destroy($this->_mdlData['iss']['id']);
//  }
  //封装对数据库的操作。issRd新增
  public function issRdCreate()
  {
    //模型的create方法，返回的是模型的对象实例
    return self::$issRdMdl->create($this->_mdlData['iss']['record'], true);
  }

  //静态方法，封装对数据库的操作。issRd更新
  static function issRdUpdate()
  {
    //??
  }
  //静态方法，封装对数据库的操作。issRd删除
 // static function issRdDelete()
//  {
//    //模型的destroy方法，返回的值是影响的记录数
//    return self::$issRdMdl->destroy(['issinfo_id' => $this->_mdlData['iss']['id']]);
//  }

  //封装对数据库的操作。pat新增
  public function patCreate()
  {
    //return count($this->_mdlData['pat']['info']);
    //return is_array($this->_mdlData['pat']['info']['pattype']);
    
    //模型的create方法，返回的是模型的对象实例
    return self::$patMdl->create($this->_mdlData['pat']['info'],true);
  }
  //静态方法，封装对数据库的操作。pat更新
  static function patUpdate()
  {
    self::$patMdl->update($this->_mdlData['pat']['info'], ['id' => $this->_mdlData['pat']['id']], true);
  }
  //静态方法，封装对数据库的操作。pat删除
 // static function patDelete()
//  {
//    //模型的destroy方法，返回的值是影响的记录数
//    return self::$patMdl->destroy($this->_mdlData['pat']['id']);
//  }

  //封装对数据库的操作。patRd新增
  public function patRdCreate()
  {
    //模型的create方法，返回的是模型的对象实例
    return self::$patRdMdl->create($this->_mdlData['pat']['record'], true);
  }
  //静态方法，封装对数据库的操作。patRd更新
  static function patRdUpdate()
  {
    //？？
  }
  //静态方法，封装对数据库的操作。patRd删除
  //static function patRdDelete()
//  {
//    //模型的destroy方法，返回的值是影响的记录数
//    return self::$patRdMdl->destroy(['patinfo_id' => $this->_mdlData['pat']['id']]);
//  }

  //静态方法，封装对数据库的操作。att新增
  static function attCreate()
  {
    //模型的create方法，返回的是模型的对象实例
    return self::$attMdl->create($this->_mdlData['att']['info'], true);
  }
  //封装对数据库的操作。att更新
  public function attUpdate()
  {
    $issSet = self::$issMdl->get($this->_mdlData['iss']['id']);
    $arrAttId=$this->_mdlData['att']['arrId'];
    $msg = '附件上传结果：<br>';
    if(count($arrAttId)){
        //循环更新attMdl,将文件从现有的‘temp’目录移动到指定目录
        for ($i = 0; $i < count($arrAttId); $i++)
        {
          $fileStr = $this->_mdlData['att']['arrFileObjStr'][$i];
          $name = $this->_mdlData['att']['arrFileName'][$i];
          //有‘temp’字符串才移动到指定目录
          if (substr_count($fileStr, 'temp'))
          {
            $newDir = '..' . DS . 'uploads' . DS . $issSet->issnum;
            //调用AttinfoModel中定义的fileMove()方法，返回true后才更新Attinfo表
            if (self::$attMdl->fileMove($fileStr, $name, $newDir))
            {
              $attData = array(
                'deldisplay' => 0,
                'num_id' => $issSet->issnum,
                'attmap_id' => $issSet->id,
                'attpath' => $newDir . DS . $name,
                );
              //更新att
              self::$attMdl->update($attData, ['id' => $arrAttId[$i]], true);
    
              $msg .= '附件' . $name . '已上传。</br>';
            } else
            {
              $msg .= '附件' . $name . '上传失败。</br>';
            }
          }
        }
    }else{
        $msg .= '无附件上传。<br>';
    }
    return $msg;
  }
  //封装对att附件删除和数据表attinfo记录的删除，自己使用。
  private function attDelete()
  {
    $att=self::$attMdl->all(['attmap_id' => $this->_mdlData['iss']['id']]);
    $delFileNum=0;
    //att文件的删除
    if(count($att)){
        for($i=0;$i<count($att);$i++){
            $attpath=$att[$i]->attpath;
            $fileDir=dirname($attpath);

            //删除文件，成功后返回
            if(file_exists($attpath)){
              unlink($attpath);
              //$msg='文件"'.$name.'"删除成功。';
//              $result=true;
                $delFileNum++;	
            }
            //若目录为空目录,删除目录
            if(count(scandir($fileDir))==2){
                rmdir($fileDir);
            }   
        }
    }
    ////att记录的删除，模型的destroy方法，返回的值是影响的记录数
    $delAttNum=self::$attMdl->destroy(['attmap_id' => $this->_mdlData['iss']['id']]);
    
    if($delAttNum*$delFileNum){
        $msg='<br>上传附件删除完成。删除['.$delAttNum.']条记录、['.$delFileNum.']个文件';
    }else{
        $msg='<br>无上传附件。';
    }
    return $msg;
  }
  
  //对数据库的5个表的删除操作
  public function mdlDelete()
  {
    $issId=$this->_mdlData['iss']['id'];
    $patId=$this->_mdlData['pat']['id'];
    $issTopic=self::$issMdl->get($issId)['topic'];
    $patTopic=self::$patMdl->get($patId)['topic'];
    return $issTopic;
    //issinfo删除，模型的destroy方法，返回的值是影响的记录数
    $issDelNum=self::$issMdl->destroy($issId);
    //issrecord删除，模型的destroy方法，返回的值是影响的记录数
    $issRdDelNum=self::$issRdMdl->destroy(['issinfo_id' => $issId]);
    $msg='<br>《'.$issTopic.'》删除'.$issDelNum+$issRdDelNum.'条记录。';
    
    //patinfo删除，模型的destroy方法，返回的值是影响的记录数
    $patDelNum=self::$patMdl->destroy($patId);
    //patrecord删除，模型的destroy方法，返回的值是影响的记录数
    $patRdDelNum=self::$patRdMdl->destroy(['patinfo_id' => $patId]);
    $msg.='<br>《'.$patTopic.'》删除'.$patDelNum+$patRdDelNum.'条记录。';
    
    //att删除,调用私有$attDelete()，已封装对att附件删除和数据表attinfo记录的删除。返回的是删除结果
    $msg.=$this->attDelete();
    
    return $msg;
    
  }

    
  //静态方法，测试。
  public function test()
  {
    //$data['iss']['info']['status']='issVarT';
    //    $data['pat']['info']['status']='patVarT';
    //    $msg= '<br>'.json_encode($data).'<br>';
    //\u6797\u5219\u5f90：林则徐

    //$data = array('iss' => array('id' => 4, 'info' => array('abstract' => '林则徐',
//            'num_id' => 2)));
//    $msg = self::issUpdate($data);
    $msg=json_encode($this->_mdlData,JSON_UNESCAPED_UNICODE);
    //$msg.=json_last_error();
    return '<br>'.$msg;
  }

}

?>