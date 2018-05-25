<?php
namespace liftfsm;

use liftfsm\Context;
use liftfsm\LiftState;
/**
 * 电梯在运行状态下能做哪些动作 
 */ 
class RunningState extends LiftState {

	//电梯门关闭？这是肯定了
	public function close() {
		//do nothing
	}

	//运行的时候开电梯门？你疯了！电梯不会给你开的
	public function open() {
		//do nothing
	}

	//这是在运行状态下要实现的方法
	public function run() {
		return 'lift run...<br/>';
	}

	//这个事绝对是合理的，光运行不停止还有谁敢做这个电梯？！估计只有上帝了
	public function stop() {
		$this->_context->setLiftState(Context::$stoppingState); //环境设置为停止状态；
		$this->_context->getLiftState()->stop();
	}

}


?>

