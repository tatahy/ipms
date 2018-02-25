<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\user\model;

use think\Model;

class Patinfo extends Model
{   
    //protected $auto = ['patnum','pronum'];
    protected $insert = ['patnum','issinfo_id'];  
    //protected $update = [];  
    
    //只读字段，这个字段的值一旦写入，就无法更改。
    protected $readonly = ['patnum','issinfo_id'];
    
    //修改器，设置patnum字段的值为pat+yyyy+0000的形式，即是在当年进行流水编号
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

    //修改器，将输入的数组Pronum转换为“,”分隔的字符串
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
    
    //获取器，将字符串Pronum转换为数组输出
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
        
        default:
          $outPut=$value;
        break;
        
      }
      return $outPut;
    }
    
    //获取器，获取数据表patinfo中status字段值，转换为中文输出，待考虑是否采用？？
   // protected function getStatusAttr($value)
//    {
//      $outPut='……';
//      switch($value){
//        case '_PATS1':
//          $outPut='内审';
//        break;
//        
//        case '_PATS2':
//          $outPut='内审修改';
//        break;
//          
//        case '_PATS3':
//          $outPut='内审否决';
//        break;
//        
//        case '_PATS4':
//          $outPut='拟申报(内审批准)';
//        break;
//        
//        case '_PATS5':
//          $outPut='申报';
//        break;
//        
//        case '_PATS6':
//          $outPut='申报修订';
//        break;
//        
//        case '_PATS7':
//          $outPut='授权';
//        break;
//        
//        case '_PATS8':
//          $outPut='续费授权';
//        break;
//        
//        case '_PATS9':
//          $outPut='放弃';
//        break;
//        
//        case '_PATS10':
//          $outPut='驳回';
//        break;
//        
//        default:
//          $outPut='……';
//        break;
//        
//      }
//      return $outPut;
//    }

    /**
     * 获取即将到期需授权续费的patent信息
     */
    public function patRenew()
    {
        $today=date('Y-m-d');
        $deadline=date('Y-m-d',strtotime("+6 month"));
        $map['status'] =['in',['授权','续费授权']];
        return $this->where($map)
                    ->where('renewdeadlinedate','between time',[$today,$deadline])
                    ->order('renewdeadlinedate asc')
                    ->paginate();
                   // ->select();
    }
    
    /**
     * 获取内容所属的issue信息
     */
    public function issinfo()
    {
        //return $this->belongsTo('app\issue\model\Issinfo');
        return $this->belongsTo('Issinfo');
    }
    
    /**
     * 获取专利的所有issues
     */
    public function issues()
    {
        return $this->morphMany('Issinfo','issmap',['_ISST_PAT1','_ISST_PAT2']);
    }
    
    ///**
//     * 获取专利的所有attachment
//     */
//    public function attachments()
//    {
//        return $this->morphMany('app\attachment\model\Attinfo', 'num', 2);
//    }

    /**
     * 获取patent的附件
     */
    public function attachments()
    {
      return $this->morphMany('Attinfo','attmap','_ATTO2');
    } 
    
    /**
     * 获取patent的过程记录
     */
    public function patrecords()
    {   
        return $this->hasMany('Patrecord');
    }     

    
}
    



?>