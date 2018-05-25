<?php
/**
 * @author tatahy
 * @copyright 2018
 */
namespace lightfsm;
 
use lightfsm\IState;
use lightfsm\Light;
 
class OnState extends IState
{
    private $light;
    public function __construct(Light $light)
    {
        $this->light = $light;
    }
    public function turnLightOn()
    {
       // echo "不合法的操作!<br />";
       $this->fwdata(" LightON。不合法的操作!<br>");
    }
    public function turnLightOff()
    {
       // echo "灯关闭!看不见帅哥chenqionghe了!<br />";
        $this->fwdata(" LightOff。灯关闭!<br/>");
        $this->light->setState($this->light->getOffState());
    }
    public function turnBrighter()
    {
       // echo "灯更亮了, 看帅哥chenqionghe看得更真切了!<br />";
        $this->fwdata(" Brighter。灯更亮了!<br/>");
        $this->light->setState($this->light->getBrighterState());
    }
    public function turnBrightest()
    {
       // echo "不合法的操作!<br />";
       $this->fwdata(" Brightest。不合法的操作!<br/>");
    }
}

?>