<?php

/**
 * @author tatahy
 * @copyright 2018
 * 定义一个isspat,_EDIT权限下的state抽象类“EditState”
 */

namespace isspatfsm\edit;
//引入操作数据库的5个模型
use isspatfsm\IssPatModel;

use isspatfsm\edit\EditContext;

abstract class EditState
{

  //定义一个环境属性，继承的子类才有，属性值是EditContext对象实例。
  protected $_context;
  //
  protected $_mdl;
  //操作所需数据
  protected $_oprtData;

  public function __construct()
  {
    //实例化IssPatModel类，便于使用其封装的方法。
    $this->_mdl = new IssPatModel();
  }

  //设定上下文环境
  public function setContext(EditContext $context)
  {
    $this->_context = $context;
  }

  //得到操作所需的数据
  public function getData($data)
  {
    $this->_oprtData = $data;
  }

  //_EDIT的4种操作
  abstract function addNew();
  abstract function delete();

  //abstract function submit();
  public function submit()
  {
    return '<br>delete结果：';
    $data = $this->_oprtData;
    //确保写入数据库的关键信息无误（前端无法准确给出??）
    $data['iss']['info']['status'] = '待审核';
    $data['pat']['info']['status'] = '内审';
    //$data=array_merge($data,array($data['iss']['info']['status']=>'待审核',$data['pat']['info']['status']=>'内审'));

    //1.patinfo更新
    $this->_mdl->patUpdate($data['pat']['info']);

    //2.patrecord新增
    $this->_mdl->patRdCreate($data['pat']['record']);

    //3.issinfo更新
    $this->_mdl->issUpdate($data['iss']['info']);

    //4.issrecord新增
    $this->_mdl->issRdCreate($data['iss']['record']);

    //5.attinfo更新
    $this->_mdl->attUpdate($data['att']);
    //状态修改
    $this->_context->setState(AuditContext::$checkingState);

    return 'submit结果：';
    //动作委托为submit
    //$this->_context->getState()->submit();
  }

  //abstract function update();
  public function update()
  {
    //patId!=0,issId!=0
    $data = $this->_oprtData;
    //1.patinfo更新
    $this->_mdl->patUpdate($data['pat']['info']);

    //2.patRd更新？？

    //3.issinfo更新
    $this->_mdl->issUpdate($data['iss']['info']);

    //4.issRd更新？？

    //5.attinfo更新
    $this->_mdl->attUpdate($data['att']);

    return 'update结果：';
  }

}

?>