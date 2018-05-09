<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\attachment\model;

use think\Model;

class Attinfo extends Model
{   
    //protected $auto = ['patnum','pronum'];
    //protected $insert = ['issnum',];  
    //protected $update = [];  
    
    //只读字段，这个字段的值一旦写入，就无法更改。
    //protected $readonly = ['issnum'];
    
    //设置issnum字段的值为iss+yyyy+0000的形式，即是在当年进行流水编号
   // protected function setIssnumAttr()
//    {
//        
//        $idmax=Issinfo::max('id');
//        $value = Issinfo::where('id',$idmax)->value('issnum');
//        
//        $year=substr($value,3,4);
//        $num=substr($value,3)+1;
//        
//        if($year==date('Y')){
//            $result ="iss".$num;
//        }else{
//            $result ="iss".date('Y')."0001";
//        }
//        
//        return ($result);
//    }

 //获取器，获取数据表attinfo中rolename字段值，转换为中文输出
    protected function getRolenameAttr($value)
    {
      $outPut='……';
      switch($value){
        case 'writer':
          $outPut='撰写人';
        break;
        
        case 'reviewer':
          $outPut='审核人';
        break;
        
        case 'approver':
          $outPut='批准人';
        break;
        
        case 'operator':
          $outPut='执行人';
        break;
        
        case 'maintainer':
          $outPut='维护人';
        break;
        
        case '_EDIT':
          $outPut='撰写人';
        break;
        
        case '_AUDIT':
          $outPut='审核人';
        break;
        
        case '_APPROVE':
          $outPut='批准人';
        break;
        
        case '_EXECUTE':
          $outPut='执行人';
        break;
        
        case '_MAINTAIN':
          $outPut='维护人';
        break;
        
        default:
          $outPut=$value;
        break;
        
      }
      return $outPut;
    }
    
    //获取器，获取数据表attinfo中atttype字段值，转换为中文输出
    protected function getAtttypeAttr($value)
    {
      $outPut='……';
      switch($value){    
        case '_ATTT1':
          $outPut='申请';
        break;
        
        case '_ATTT2':
          $outPut='请示';
        break;
        
        case '_ATTT3':
          $outPut='报告';
        break;
        
        case '_ATTT4':
          $outPut='预算报告';
        break;
        
        case '_ATTT5':
          $outPut='说明';
        break;
        
        default:
          $outPut=$value;
        break;
        
      }
      return $outPut;
    }
    
    //获取issinfo的多态模型,涉及issinfo表中的num_id和num_type两个字段内容
     public function num()
    {
        return $this->morphTo(null, [
            '0' => 'app\issue\model\Issinfo',
            '1' => 'app\project\model\Proinfo',
            '2' => 'app\patent\model\Patinfo',
            
        ]);
    }


    
    
}
    



?>