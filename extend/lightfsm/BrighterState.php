<?php
/**
 * @author tatahy
 * @copyright 2018
 */
namespace lightfsm;
 
use lightfsm\IState;
use lightfsm\Light;
 
class BrighterState extends IState
{
    private $light;
    public function __construct(Light $light)
    {
        $this->light = $light;
    }
    public function turnLightOn()
    {
        //echo "不合法的操作!<br />";
        $this->fwdata(" LightON。不合法的操作!<br>");
    }
    public function turnLightOff()
    {
        //echo "不合法的操作!<br />";
        $this->fwdata(" LightOFF。不合法的操作!<br>");
    }
    public function turnBrighter()
    {
        //echo "不合法的操作!<br />";
        $this->fwdata(" Brighter。不合法的操作!<br>");
    }
    public function turnBrightest()
    {
        //echo "灯最亮了, 看帅哥chenqionghe已经帅到无敌!<br />";
        $this->fwdata(" Brightest。灯最亮了!<br>");
        $this->light->setState($this->light->getBrightestState());
    }
}

?>