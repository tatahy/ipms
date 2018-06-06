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

  //静态方法，封装对数据库的操作。iss新增
  static function issCreate()
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
  static function issDelete()
  {
    //模型的destroy方法，返回的值是影响的记录数
    return self::$issMdl->destroy($this->_mdlData['iss']['id']);
  }
  //静态方法，封装对数据库的操作。issRd新增
  static function issRdCreate()
  {
    //模型的create方法，返回的是模型的对象实例
    return self::$issRdRdMdl->create($this->_mdlData['iss']['record'], true);
  }

  //静态方法，封装对数据库的操作。issRd更新
  static function issRdUpdate()
  {
    //??
  }
  //静态方法，封装对数据库的操作。issRd删除
  static function issRdDelete()
  {
    //模型的destroy方法，返回的值是影响的记录数
    return self::$issRdMdl->destroy(['issinfo_id' => $this->_mdlData['iss']['id']]);
  }

  //静态方法，封装对数据库的操作。pat新增
  static function patCreate()
  {
    //模型的create方法，返回的是模型的对象实例
    return self::$patMdl->create($this->_mdlData['pat']['info'], true);
  }
  //静态方法，封装对数据库的操作。pat更新
  static function patUpdate()
  {
    self::$patMdl->update($this->_mdlData['pat']['info'], ['id' => $this->_mdlData['pat']['id']], true);
  }
  //静态方法，封装对数据库的操作。pat删除
  static function patDelete()
  {
    //模型的destroy方法，返回的值是影响的记录数
    return self::$patMdl->destroy($this->_mdlData['pat']['id']);
  }

  //静态方法，封装对数据库的操作。patRd新增
  static function patRdCreate()
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
  static function patRdDelete()
  {
    //模型的destroy方法，返回的值是影响的记录数
    return self::$patRdMdl->destroy(['patinfo_id' => $this->_mdlData['pat']['id']]);
  }

  //静态方法，封装对数据库的操作。att新增
  static function attCreate()
  {
    //模型的create方法，返回的是模型的对象实例
    return self::$attMdl->create($this->_mdlData['att']['info'], true);
  }
  //静态方法，封装对数据库的操作。att更新
  static function attUpdate()
  {
    $issSet = self::$issMdl->get($this->_mdlData['iss']['id']);
    //循环更新attMdl,将文件从现有的‘temp’目录移动到指定目录
    for ($i = 0; $i < count($this->_mdlData['att']['arrId']); $i++)
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
          self::$attMdl->update($attData, ['id' => $this->_mdlData['iss']['id'][$i]], true);

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
    //模型的destroy方法，返回的值是影响的记录数
    return self::$attMdl->destroy(['attmap_id' => $this->_mdlData['iss']['id']]);
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