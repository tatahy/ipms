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
   * 'arrId' => [0],
   * 'arrFileName' => [0],
   * 'arrFileObjStr' => [0]
   * )
   * );
   */

  protected $_oprtData;

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
    $this->_oprtData = $data;

  }

  //静态方法，封装对数据库的操作。iss新增
  static function issCreate()
  {
    self::$issMdl->create($this->_oprtData['iss']['info'], true);
  }
  //静态方法，封装对数据库的操作。iss更新
  static function issUpdate()
  {
    //save方法返回受影响的对象数。
    $id = self::$issMdl->save($this->_oprtData['iss']['info'], ['id' => $this->
      _oprtData['iss']['id']], true);
    if ($id)
    {
      $msg = "updated.";
      $msg .= self::$issMdl->get($this->_oprtData['iss']['id'])->topic;
    } else
    {
      $msg = "no update.";
    }
    return $msg;

    //update方法返回要修改的属性键值对数组
    // $id=self::$issMdl->update($data['iss']['info'],['id' => $data['iss']['id']],true);
    //    return json_encode($id);
  }
  //静态方法，封装对数据库的操作。iss删除
  static function issDelete()
  {
    self::$issMdl->destroy($this->_oprtData['iss']['id']);
  }
  //静态方法，封装对数据库的操作。issRd新增
  static function issRdCreate()
  {
    self::$issRdRdMdl->create($this->_oprtData['iss']['record'], true);
  }

  //静态方法，封装对数据库的操作。issRd更新
  static function issRdUpdate()
  {
    //??
  }
  //静态方法，封装对数据库的操作。issRd删除
  static function issRdDelete()
  {
    self::$issRdMdl->destroy(['issinfo_id' => $this->_oprtData['iss']['id']]);
  }

  //静态方法，封装对数据库的操作。pat新增
  static function patCreate()
  {
    self::$patMdl->create($this->_oprtData['pat']['info'], true);
  }
  //静态方法，封装对数据库的操作。pat更新
  static function patUpdate()
  {
    self::$patMdl->update($this->_oprtData['pat']['info'], ['id' => $this->
      _oprtData['pat']['id']], true);
  }
  //静态方法，封装对数据库的操作。pat删除
  static function patDelete()
  {
    self::$patMdl->destroy($this->_oprtData['pat']['id']);
  }

  //静态方法，封装对数据库的操作。patRd新增
  static function patRdCreate()
  {
    self::$patRdMdl->create($this->_oprtData['pat']['record'], true);
  }
  //静态方法，封装对数据库的操作。patRd更新
  static function patRdUpdate()
  {
    //？？
  }
  //静态方法，封装对数据库的操作。patRd删除
  static function patRdDelete()
  {
    self::$patRdMdl->destroy(['patinfo_id' => $this->_oprtData['pat']['id']]);
  }

  //静态方法，封装对数据库的操作。att新增
  static function attCreate()
  {
    self::$attMdl->create($this->_oprtData['att']['info'], true);
  }
  //静态方法，封装对数据库的操作。att更新
  static function attUpdate()
  {
    $issSet = self::$issMdl->get($this->_oprtData['iss']['id']);
    //循环更新attMdl,将文件从现有的‘temp’目录移动到指定目录
    for ($i = 0; $i < count($this->_oprtData['att']['arrId']); $i++)
    {
      $fileStr = $this->_oprtData['att']['arrFileObjStr'][$i];
      $name = $this->_oprtData['att']['arrFileName'][$i];
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
          self::$attMdl->update($attData, ['id' => $this->_oprtData['iss']['id'][$i]], true);

          $msg .= "附件" . $name . "已上传。</br>";
        } else
        {
          $msg .= "附件" . $name . "上传失败。</br>";
        }
      }
    }

  }
  //静态方法，封装对数据库的操作。att删除
  static function attDelete()
  {
    self::$attMdl->destroy(['attmap_id' => $this->_oprtData['iss']['id']]);
  }


  //静态方法，测试。
  public function test($data)
  {
    //$data['iss']['info']['status']='issVarT';
    //    $data['pat']['info']['status']='patVarT';
    //    $msg= '<br>'.json_encode($data).'<br>';
    //\u6797\u5219\u5f90：林则徐

    $data = array('iss' => array('id' => 4, 'info' => array('abstract' => '林则徐',
            'num_id' => 2)));
    $msg = self::issUpdate($data);
    //$msg=json_encode($data,JSON_UNESCAPED_UNICODE);
    //$msg.=json_last_error();
    return $msg;
  }

}

?>