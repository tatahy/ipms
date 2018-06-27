<?php

/**
 * @author tatahy
 * @copyright 2018
 * 定义一个isspat,_EDIT权限下的state抽象类“EditState”，便于5个状态下4种操作的不同实现。
 */

namespace app\dashboard\model\isspatfsm\edit;
//引入操作数据库的5个模型
use app\dashboard\model\isspatfsm\IssPatModel;

use app\dashboard\model\isspatfsm\edit\EditContext;

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
    $data = $this->_oprtData;
    $issStatus=$data['iss']['info']['status'];
    //确保写入数据库的关键信息无误（前端无法准确给出??）
    $data['iss']['info']['status'] = '待审核';
    //$data['iss']['info']['oprt_user']['time']=time();
    //$data['iss']['record']['actdetail']='<p>提交《'.$data['iss']['info']['topic'].'》。待【审核】。</p><span class="text-info">提交说明：</span><pre>'.$data['iss']['info']['statusdescription'].'</pre>';
    $data['iss']['record']['actdetail']='0';
    //数据表“issrecord”的“actdetailhtml”字段存储结构化的信息（模型“issrecord”设置自动转换为json类型），便于前端组装HTML进行显示。
    $data['iss']['record']['actdetailhtml']=array(
                                                'p'=>array(
                                                        'prefix'=>'提交《'.$data['iss']['info']['topic'].'》',
                                                        'spanclass'=>'',
                                                        'spantext'=>'',
                                                        'nextstage'=>'待【审核】'
                                                        ),
                                                'span'=>array(
                                                        'class'=>'primary',
                                                        'text'=>'提交说明：',
                                                        ),
                                                'pre'=>array(
                                                        'class'=>'',
                                                        'text'=>$data['iss']['info']['statusdescription'],
                                                        )
                                            );
    
    $data['pat']['info']['status'] = '内审';
    $data['pat']['record']['actdetail']='《'.$data['pat']['info']['topic'].'》提交内部审查。';
    //已上传附件不可删除
    $data['att']['info']['deldisplay']=0;
    //$data=array_merge($data,array($data['iss']['info']['status']=>'待审核',$data['pat']['info']['status']=>'内审'));
    //调用IssPatModel的setMdlData()方法，设定所需进行处理的数据。
    $this->_mdl->setMdlData($data);

    //1.patinfo更新
    $this->_mdl->patUpdate();
    
    //
    if($issStatus=='填报'){
        //2.patrecord新增
        $this->_mdl->patRdCreate();
    }
    
    //3.issinfo更新，返回值是string类型，是更新的结果信息
    $msg=$this->_mdl->issUpdate();

    //4.issrecord新增
    $this->_mdl->issRdCreate();

    //5.attinfo更新
    $this->_mdl->attUpdate();
    
    //状态修改
    //$this->_context->setState(AuditContext::$checkingState);

    return $data['iss']['record']['actdetailhtml']['p']['nextstage'];
    //动作委托为submit
    //$this->_context->getState()->submit();
  }

  //abstract function update();
  public function update()
  {
    $data = $this->_oprtData;
    //有“_EDIT”权限用户已上传附件仍然可删除
    $data['att']['info']['deldisplay']=1;
    //$data['iss']['info']['oprt_user']['time']=time();
    
    //不更新['iss']['info']['statusdescription']，去掉该字段
    //unset($this->_oprtData['iss']['info']['statusdescription']);
    //调用IssPatModel的setMdlData()方法，设定所需进行处理的数据。
    $this->_mdl->setMdlData($data);
    //issinfo更新
    $msg=$this->_mdl->issUpdate();
    //issRd更新？？
    
    //patinfo更新
    $msg.=$this->_mdl->patUpdate();

    //patRd更新？？

    //5.attinfo更新
    $msg.=$this->_mdl->attUpdate();

    return '成功。'.$msg;
  }

}

?>