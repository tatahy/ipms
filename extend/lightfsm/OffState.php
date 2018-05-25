<?php
/**
 * @author tatahy
 * @copyright 2018
 */
namespace lightfsm;
 
use lightfsm\IState;
use lightfsm\Light;
 
class OffState extends IState
{
    private $light;
    public function __construct(Light $light)
    {
        $this->light = $light;
    }
    public function turnLightOn()
    {
        echo "灯打开!可以看见帅哥chenqionghe了!<br />";
        $this->fwdata(" LightON。灯打开!<br>");
        //return对象继续操作
        $this->light->setState($this->light->getOnState());
       
    }
    public function turnLightOff()
    {
        echo "不合法的操作!<br/>";
        $this->fwdata(" LightOff。不合法的操作!<br>");
    }
    public function turnBrighter()
    {
        echo "不合法的操作!<br />";
        $this->fwdata(" Brighter。不合法的操作!<br>");
    }
    public function turnBrightest()
    {
        echo "不合法的操作!<br />";
        $this->fwdata(" Brightest。不合法的操作!<br>");
    }
    
}

?>