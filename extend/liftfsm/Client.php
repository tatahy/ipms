<?php
namespace liftfsm;

use liftfsm\Context;
use liftfsm\ClosingState;

/**
 * 模拟电梯的动作 
 */ 
class Client {
  private $result;
  
	public function __construct() {
    $this->result='lift<br>';
    $context = new Context();
		$context->setLiftState(new ClosingState());
    
		$this->result.=$context->open().'open<br>';
		$this->result.=$context->close().'close<br>';
		$this->result.=$context->run().'run<br>';
		$this->result.=$context->stop().'stop<br>';
	}
  
  public function display()
  {
      return $this->result;
  }
}

?>

