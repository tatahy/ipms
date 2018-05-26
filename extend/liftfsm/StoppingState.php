<?php
namespace liftfsm;

use liftfsm\Context;
use liftfsm\LiftState;
/**
 * 在停止状态下能做什么事情 
 */ 
class StoppingState extends LiftState {

	//停止状态关门？电梯门本来就是关着的！
	public function close() {
		//do nothing;
    $this->fwdata(" _STOPSTATE. lift closed...<br>");
	}

	//停止状态，开门，那是要的！
	public function open() {
		$this->fwdata(" _STOPSTATE. lift openning...<br>");
    $this->_context->setLiftState(Context::$openningState);
		$this->_context->getLiftState()->open();
	}
	//停止状态再跑起来，正常的很
	public function run() {
		$this->fwdata(" _STOPSTATE. lift running...<br>");
    $this->_context->setLiftState(Context::$runningState);
		$this->_context->getLiftState()->run();
	}
	//停止状态是怎么发生的呢？当然是停止方法执行了
	public function stop() {
		//return 'lift stop...<br/>';
    $this->fwdata(" _STOPSTATE. lift stopped...<br>");
	}

}

?>

