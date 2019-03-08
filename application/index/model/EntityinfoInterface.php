<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\index\model;

//定义类接口
interface EntityinfoInterface {
   
    #得到在period里的query对象
    public function getPeriodSql();
    #得到在period里的所有pat
    public function getPeriodSet();
    #得到在period里的所有pat的num
    public function getPeriodNum();
    #得到在period的指定field字段的groupby内容
    public function getFieldGroupByArr($field);

   
}

?>