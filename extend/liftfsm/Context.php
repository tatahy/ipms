<?php
namespace liftfsm;

use liftfsm\LiftState;
use liftfsm\OpenningState;
use liftfsm\ClosingState;
use liftfsm\RunningState;
use liftfsm\StoppingState;
/**
 * 环境类:定义客户感兴趣的接口。维护一个ConcreteState子类的实例，这个实例定义当前状态。
 */ 
class Context {
	//定义出所有的电梯状态
	static  $openningState = null;
	static  $closeingState = null;
	static  $runningState  = null;
	static  $stoppingState = null;

    public function __construct() {
		self::$openningState = new OpenningState();
		self::$closeingState = new ClosingState();
		self::$runningState =  new RunningState();
		self::$stoppingState = new StoppingState();

	}

	//定一个当前电梯状态
	private  $_liftState;

	public function getLiftState() {
		return $this->_liftState;
	}

	public function setLiftState($liftState) {
		$this->_liftState = $liftState;
		//把当前的环境通知到各个实现类中
		$this->_liftState->setContext($this);
	}


	public function open(){
		$this->_liftState->open();
	}

	public function close(){
		$this->_liftState->close();
	}

	public function run(){
		$this->_liftState->run();
	}

	public function stop(){
		$this->_liftState->stop();
	}
}

?>

