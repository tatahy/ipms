<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：EditState
 */

namespace app\dashboard\model\isspatfsm\edit;

use app\dashboard\model\isspatfsm\edit\EditContex;
use app\dashboard\model\isspatfsm\edit\EditState;

class FillingState extends EditState
{
  public function addNew()
  {
    //return '<br>data:'. json_encode($this->_oprtData,JSON_UNESCAPED_UNICODE) . '<br>json_last_error:'. json_last_error();
    return '无效操作';

  }

  public function delete()
  {
    //调用IssPatModel的setMdlData()方法，设定所需进行处理的数据。
     $this->_mdl->setMdlData($this->_oprtData);
     //return '<br>delete操作结果：<br>'.json_encode($this->_oprtData, JSON_UNESCAPED_UNICODE) . json_last_error();
     //调用IssPatModel的mdlDelete()方法，进行删除操作，得到操作结果信息。
     $msg=$this->_mdl->mdlDelete();
     return '成功！'. $msg;
  }

}

?>