<?php
namespace liftfsm;

use liftfsm\Context;
use liftfsm\LiftState;
/**
 * 在电梯门开启的状态下能做什么事情 
 */ 
class OpenningState extends LiftState {

	/**
	 * 开启当然可以关闭了，我就想测试一下电梯门开关功能
	 *
	 */
	public function close() {
		//状态修改
		$this->_context->setLiftState(Context::$closeingState);
		//动作委托为CloseState来执行
		$this->_context->getLiftState()->close();
	}

	//打开电梯门
	public function open() {
		return 'lift open...<br/>';
	}
	//门开着电梯就想跑，这电梯，吓死你！
	public function run() {
		//do nothing;
	}

	//开门还不停止？
	public function stop() {
		//do nothing;
	}

}

?>