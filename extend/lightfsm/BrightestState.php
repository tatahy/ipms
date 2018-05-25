<?php
/**
 * @author tatahy
 * @copyright 2018
 */
namespace lightfsm;
 
use lightfsm\IState;
use lightfsm\Light;
 
class BrightestState extends IState
{
    private $light;
    public function __construct(Light $light)
    {
        $this->light = $light;
    }
    public function turnLightOn()
    {
        //echo "灯已经打开了->不做操作<br />";
        $this->fwdata(" LightOn。灯已经打开了->不做操作!<br>");
    }
    public function turnLightOff()
    {
        //echo "灯关闭!看不见帅哥chenqionghe了!<br />";
        $this->fwdata(" LightOFF。灯关闭!<br>");
        $this->light->setState($this->light->getOffState());
    }
    public function turnBrighter()
    {
        //echo "不合法的操作!<br />";
        $this->fwdata(" Brighter。不合法的操作!<br>");
    }
    public function turnBrightest()
    {
        //echo "不合法的操作!<br />";
        $this->fwdata(" Brightest。不合法的操作!<br>");
    }
}

?>