<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\index\model;

//定义抽象类
interface EntityinfoInterface {
   
    #得到在period里的query对象
    static function getPeriodSql();
    
    #得到在period里的所有pat
    static function getPeriodSet();
    #得到在period里的所有pat的num
    static function getPeriodNum();
    #得到在period的指定field字段的groupby内容
    static public function getFieldGroupByArr($field);

   
}

?>