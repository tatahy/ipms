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
    $data = $this->_oprtData;
    //delete操作代码
    //1.删除pat，模型destroy()方法
    $this->_mdl->patDelete($data);

    //2.删除patRd，模型destroy()方法
    $this->_mdl->patRdDelete($data);

    //3.删除iss，模型destroy()方法
    $this->_mdl->issDelete($data);

    //4.删除issRd，模型destroy()方法
    $this->_mdl->issRdDelete($data);

    //5.删除att，模型destroy()方法
    $this->_mdl->attDelete($data);

    //return 'delete结果：';
  }

}

?>