<?php
/**
 * @author tathy
 * @copyright 2018
 */
namespace lightfsm;

abstract class IState{
     
    public abstract function turnLightOn();
    public abstract function turnLightOff();
    public abstract function turnBrighter();
    public abstract function turnBrightest();
    
    protected function fwdata($str ){
        $fileName='../extend/lightfsm/data.txt';
        $handle=fopen($fileName,'a+');
        fwrite($handle,date("Y-m-d H:i:s").$str);
        fclose($handle);
        
    }
}

?>