<?php

/**
 * @author tatahy
 * @copyright 2018
 * 继承、实现抽象类：EditState
 */

namespace isspatfsm\edit;

use isspatfsm\edit\EditContex;
use isspatfsm\edit\EditState;

class FillingState extends EditState
{
  public function addNew()
  {
    //return '<br>data:'. json_encode($this->_oprtData,JSON_UNESCAPED_UNICODE) . '<br>json_last_error:'. json_last_error();
    return '<br>无效addNew操作';

  }

  public function delete()
  {
    //调用IssPatModel的setMdlData()方法，设定所需进行处理的数据。
    $this->_mdl->setMdlData($this->_oprtData);
    //1.删除pat
    $n=$this->_mdl->patDelete();

    //2.删除patRd
    $n+=$this->_mdl->patRdDelete();

    //3.删除iss
    $n+=$this->_mdl->issDelete();

    //4.删除issRd
    $n+=$this->_mdl->issRdDelete();

    //5.删除att
    $n+=$this->_mdl->attDelete();
    
    $msg='<br>delete结果：删除'.$n.'条记录。';
    return $msg;
  }

}

?>