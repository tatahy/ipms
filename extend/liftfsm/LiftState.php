<?php
namespace liftfsm;

use liftfsm\Context;

/**
 * 
 * 定义一个电梯的抽象类 
 */ 
abstract class LiftState{
	//定义一个环境属性，继承的子类才有。状态的变换引起的功能变化
	protected $_context;
  
  //设定上下文环境
	public function setContext(Context $context){
		$this->_context = $context;
	}

	public abstract function open();
  public abstract function close();
  public abstract function run();
  public abstract function stop();
  
  //继承的子类可以使用的方法
  protected function fwdata($str ){
    $fileName='../extend/liftfsm/data.txt';
    $handle=fopen($fileName,'a+');
    fwrite($handle,date("Y-m-d H:i:s").$str);
    fclose($handle);
  }

}

?>

