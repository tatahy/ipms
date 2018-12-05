<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\dashboard\model;

use think\Model;
use think\Collection;

class Dept extends Model
{   

    //得到部门简称与全称的关联数组，简称为下标
    public function getNameAbbrVSFull(){
      $baseSet=$this::all();
      $baseSet=is_array($baseSet)?collection($baseSet):$baseSet;
      $keyArr=$baseSet->column('abbr');
      $valueArr=$baseSet->column('name');

      return array_combine($keyArr,$valueArr);
    }
    
    
}


?>