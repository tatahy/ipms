<?php

namespace app\dashboard\model\isspatfsm;

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
  //静态属性，数据库模型对象的实例
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
   * 'info' => array('id'=>,...)
   * 'arrId' => [],
   * 'arrFileName' => [],
   * 'arrFileObjStr' => []
   * )
   * );
   */
  //数据库模型操作的数据
  private $_mdlData;

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

  //iss新增，封装对数据库的操作。返回类型是Obj，是创建的模型对象实例
  public function issCreate()
  {
    //模型的create方法，返回的是模型的对象实例
    return self::$issMdl->create($this->_mdlData['iss']['info'], true);
  }
  //iss更新，封装对数据库的操作。返回类型是string，是更新的结果信息。
  public function issUpdate()
  {
    //update方法返回模型对象实例
    //$iss = self::$issMdl->update($this->_mdlData['iss']['info'], ['id' =>$this->_mdlData['iss']['id']], true);
    
    //模型的save方法，返回的是受影响的记录数。
    $iss = self::$issMdl->get($this->_mdlData['iss']['id']);
    $n=$iss->allowField(true)
	   //->data($this->_mdlData['iss']['info'], true)
        ->save($this->_mdlData['iss']['info']);
    
    //if (count($iss)){
    if ($n){
      $msg = '<br>- 系统专利事务记录：已更新。';
    } else {
      $msg = '<br>- 系统专利事务记录：无需更新。';
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
  //issRd新增，封装对数据库的操作。返回类型是Obj，是创建的模型对象实例
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

  //pat新增，封装对数据库的操作。返回类型是Obj，是创建的模型对象实例
  public function patCreate()
  {
    //return count($this->_mdlData['pat']['info']);
    //return is_array($this->_mdlData['pat']['info']['pattype']);
    
    //模型的create方法，返回的是模型的对象实例
    return self::$patMdl->create($this->_mdlData['pat']['info'],true);
  }
  //pat更新，封装对数据库的操作。返回类型是string，是更新的结果信息
  public function patUpdate()
  {
    //模型的update方法，返回的是模型的对象实例
    //$pat= self::$patMdl->update($this->_mdlData['pat']['info'], ['id' => $this->_mdlData['pat']['id']], true);
    
    //模型的save方法，返回的是受影响的记录数。
    $pat = self::$patMdl->get($this->_mdlData['pat']['id']);
    $n=$pat->allowField(true)
	   //->data($this->_mdlData['pat']['info'], true)
        ->save($this->_mdlData['pat']['info']);
    
     //if (count($pat)){
    if ($n){
      $msg = '<br>- 系统专利记录：已更新。';
    } else {
      $msg = '<br>- 系统专利记录：无需更新。';
    }
    return $msg;
  }
  //静态方法，封装对数据库的操作。pat删除
 // static function patDelete()
//  {
//    //模型的destroy方法，返回的值是影响的记录数
//    return self::$patMdl->destroy($this->_mdlData['pat']['id']);
//  }

  //patRd新增，封装对数据库的操作。返回类型是Obj，是创建的模型对象实例
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

  //att新增，静态方法，封装对数据库的操作。返回类型是Obj，是创建的模型对象实例
  static function attCreate()
  {
    //模型的create方法，返回的是模型的对象实例
    return self::$attMdl->create($this->_mdlData['att']['info'], true);
  }
  //att更新，封装对数据库的操作，附件文件的移动。返回类型是string，更新结果信息。
  public function attUpdate()
  {
    $issSet = self::$issMdl->get($this->_mdlData['iss']['id']);
    $arrAttId=$this->_mdlData['att']['arrId'];
    $delDisplay=$this->_mdlData['att']['info']['deldisplay'];
    $msg = '<br>- 系统附件：';
    if(count($arrAttId)){
        //循环更新attMdl
        for ($i = 0; $i < count($arrAttId); $i++)
        {
          $fileStr = $this->_mdlData['att']['arrFileObjStr'][$i];
          $name = $this->_mdlData['att']['arrFileName'][$i];
          //有‘temp’字符串就移动到指定目录
          if (substr_count($fileStr, 'temp')){
            $newDir = '..' . DS . 'uploads' . DS . $issSet->issnum;
            //调用AttinfoModel中定义的fileMove()方法，返回true后才更新Attinfo表
            if (self::$attMdl->fileMove($fileStr, $name, $newDir)){
              $attData = array(
                'deldisplay' => $delDisplay,
                'num_id' => $issSet->issnum,
                'attmap_id' => $issSet->id,
                'attpath' => $newDir . DS . $name,
                );
              //更新att
              self::$attMdl->update($attData, ['id' => $arrAttId[$i]], true);
    
              $msg .= '<br>附件' . $name . '已处理。';
            } else{
              $msg .= '<br>附件' . $name . '处理失败。';
            }
          }else{
            $msg .= '<br>附件' . ($i+1) . '已处理过。';
            $delDisplay=0;
          }
          
          //更新att的“deldisplay”字段值
          $attData = array('deldisplay' => $delDisplay);
          self::$attMdl->update($attData, ['id' => $arrAttId[$i]], true);
          
        }
    }else{
        $msg .= '无附件上传。';
    }
    return $msg;
  }
  //封装对att附件删除和数据表attinfo记录的删除，私有。返回类型是string，删除结果信息。
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
    //att记录的删除，模型的destroy方法，返回的值是影响的记录数
    $delAttNum=self::$attMdl->destroy(['attmap_id' => $this->_mdlData['iss']['id']]);
    
    if($delAttNum*$delFileNum){
        $msg='<br>上传附件删除完成。系统删除['.$delAttNum.']条附件记录、['.$delFileNum.']个文件';
    }else{
        $msg='<br>无上传附件。';
    }
    return $msg;
  }
  
  //对数据库的5个表的删除操作。返回类型是string，删除结果信息。
  public function mdlDelete()
  {
    $issId=$this->_mdlData['iss']['id'];
    $patId=$this->_mdlData['pat']['id'];
    $issTopic=$this->_mdlData['iss']['info']['topic'];
    $patTopic=$this->_mdlData['pat']['info']['topic'];
    
    //$issTopic=self::$issMdl->get($issId)->topic;
//    $patTopic=self::$patMdl->get($patId)->topic;
    
  //  $msg='<br>《'.$issTopic.'》删除';
//    $msg.='<br>《'.$patTopic.'》删除';
//    return $msg;
    //issinfo删除，模型的destroy方法，返回的值是影响的记录数
    $issDelNum=self::$issMdl->destroy($issId);
    //issrecord删除，模型的destroy方法，返回的值是影响的记录数
    $issRdDelNum=self::$issRdMdl->destroy(['issinfo_id' => $issId]);
    $msg='<br>系统删除'.($issDelNum+$issRdDelNum).'条事务记录。';
    
    //patinfo删除，模型的destroy方法，返回的值是影响的记录数
    $patDelNum=self::$patMdl->destroy($patId);
    //patrecord删除，模型的destroy方法，返回的值是影响的记录数
    $patRdDelNum=self::$patRdMdl->destroy(['patinfo_id' => $patId]);
    $msg.='<br>系统删除'.($patDelNum+$patRdDelNum).'条专利记录。';
    
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
    //$msg=json_encode($this->_mdlData,JSON_UNESCAPED_UNICODE);
    //$msg.=json_last_error();
    return '<br>'.'需【撰写人】修改后再“提交”。';
  }

}

?>