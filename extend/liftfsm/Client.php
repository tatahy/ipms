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
    //$this->result='lift<br>';
    $context = new Context();
		$context->setLiftState(new closingState());
    
		$context->open();
    $context->close();
		$context->run();
		$context->stop();
	}
  
  public function display()
  {
      //读出文件内容
      $this->result=file_get_contents('../extend/liftfsm/data.txt');
      //清除文件内容，赋予文件初值“light<br>”
      $fileName='../extend/liftfsm/data.txt';
      $handle=fopen($fileName,'w+');
      fwrite($handle,'lift<br>');
      fclose($handle);
      return $this->result;
      //return json_encode($this);
  }
}

?>

