<?php
/**
 * @author tatahy
 * @copyright 2018
 */
namespace lightfsm;
 
use lightfsm\IState;
use lightfsm\OffState;
use lightfsm\OnState;
use lightfsm\BrighterState;
use lightfsm\BrightestState;
 
class Light
{
    private $offState;  //关闭状态
    private $onState;   //开启状态
    private $brighterState; //更亮状态
    private $brightestState;//最亮状态
    private $currentState;  //当前状态
    public function __construct()
    {
        $this->offState = new OffState($this);
        $this->onState = new OnState($this);
        $this->brighterState = new BrighterState($this);
        $this->brightestState = new BrightestState($this);
        //开始状态为关闭状态Off
        $this->currentState = $this->offState;
    }
    //调用状态方法触发器
    public function turnLightOn()
    {
        $this->currentState->turnLightOn();
    }
    public function turnLightOff()
    {
        $this->currentState->turnLightOff();
    }
    public function turnLightBrighter()
    {
        $this->currentState->turnBrighter();
    }
    public function turnLigthBrightest()
    {
        $this->currentState->turnBrightest();
    }
    //设置当前状态
    public function setState(IState $state)
    {
        $this->currentState = $state;
    }
    //获取状态
    public function getOnState()
    {
        return $this->onState;
    }
    public function getOffState()
    {
        return $this->offState;
    }
    public function getBrighterState()
    {
        return $this->brighterState;
    }
    public function getBrightestState()
    {
        return $this->brightestState;
    }
}

?>