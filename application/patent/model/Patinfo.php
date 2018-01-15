<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\patent\model;

use think\Model;

class Patinfo extends Model
{   
    //protected $auto = ['patnum','pronum'];
    protected $insert = ['patnum','issinfo_id'];  
    //protected $update = [];  
    
    //只读字段，这个字段的值一旦写入，就无法更改。
    protected $readonly = ['patnum','issinfo_id'];
    
    //设置patnum字段的值为pat+yyyy+0000的形式，即是在当年进行流水编号
    protected function setPatnumAttr()
    {
        
        $idmax=Patinfo::max('id');
        $value = Patinfo::where('id',$idmax)->value('patnum');
        
        $year=substr($value,3,4);
        $num=substr($value,3)+1;
        
        if($year==date('Y')){
            $result ="pat".$num;
        }else{
            $result ="pat".date('Y')."0001";
        }
        
        return ($result);
    }

    //将输入的数组Pronum转换为“,”分隔的字符串
    protected function setPronumAttr($value)
    {
        $result=$value;
        if($result[0]=='0'){
            $result[0]='无';
            return implode(",",$result);
        }else{
            return implode(",",$result);
        }
        
    }
    
    //将字符串Pronum转换为数组输出
    protected function getPronumAttr($value)
    {
        return explode(",",$value);
    }
    
    //获取器，获取数据表patinfo中pattype字段值，转换为中文输出
    protected function getPattypeAttr($value)
    {
      $outPut='……';
      switch($value){
        case '_PATT1':
          $outPut='发明专利';
        break;
        
        case '_PATT2':
          $outPut='实用新型专利';
        break;
          
        case '_PATT3':
          $outPut='外观设计专利';
        break;
        
        case '_PATT4':
          $outPut='软件版权';
        break;
        
        case '_PATT5':
          $outPut='著作权';
        break;
        
        case '_PATT6':
          $outPut='集成电路图';
        break;
        //$value值不做转换直接输出
        default:
          $outPut=$value;
        break;
        
      }
      return $outPut;
    }
    
    /**
     * 获取内容所属的issue信息
     */
    public function issinfo()
    {
        return $this->belongsTo('app\issue\model\Issinfo');
    }
    
    /**
     * 获取专利的所有issues
     */
    public function issues()
    {
        return $this->morphMany('app\issue\model\Issinfo', 'num', 2);
    }
    
    /**
     * 获取专利的所有attachment
     */
    public function attachments()
    {
        return $this->morphMany('app\attachment\model\Attinfo', 'num', 2);
    } 
    
    /**
     * 获取patent的过程记录
     */
    public function patrecords()
    {   
        return $this->hasMany('Patrecord')->order('acttime desc');
    }     

    
}
    



?>