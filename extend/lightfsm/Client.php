<?php
/**
 * @author tatahy
 * @copyright 2018
 */
namespace lightfsm;
 
use lightfsm\Light;
 
class Client
{
    private $light;
    private $result;
    
    public function __construct()
    {
        $this->result='light<br>';
        $this->light = new Light();

        $this->light->turnLightOn().'On<br>';
        $this->light->turnLightBrighter().'Brighter<br>';
        $this->light->turnLigthBrightest().'Brightest<br>';
        $this->light->turnLightOff().'Off<br>';
        $this->light->turnLigthBrightest().'Brightest<br>';
    }
    
    public function display()
    {
        //读出文件内容
        $this->result=file_get_contents('../extend/lightfsm/data.txt');
        //清除文件内容，赋予文件初值“light<br>”
        $fileName='../extend/lightfsm/data.txt';
        $handle=fopen($fileName,'w+');
        fwrite($handle,'light<br>');
        fclose($handle);
        return $this->result;
    }
}

?>