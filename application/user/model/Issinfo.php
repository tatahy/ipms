<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\user\model;

use think\Model;

class Issinfo extends Model
{   
    //protected $auto = ['patnum','pronum'];
    protected $insert = ['issnum'];  
    //protected $update = ['topic','abstract','addnewdate'];  
   
    //只读字段，这个字段的值一旦写入，就无法更改。
    protected $readonly = ['issnum'];
    
    //修改器，设置issnum字段的值为iss+yyyy+0000的形式，即是在当年进行流水编号
    protected function setIssnumAttr()
    {
        
        $idmax = Issinfo::max('id');
        $value = Issinfo::where('id',$idmax)->value('issnum');
        
        $year=substr($value,3,4);
        $num=substr($value,3)+1;
        
        if($year==date('Y')){
            $result ="iss".$num;
        }else{
            $result ="iss".date('Y')."0001";
        }
        
        return ($result);
    }
    
    //获取器，获取数据表issinfo中issmap_type字段值，转换为中文输出
    protected function getIssmapTypeAttr($value)
    {
      $outPut='……';
      switch($value){
        case '_ISST_PAT1':
          $outPut='专利授权申报';
        break;
        
        case '_ISST_PAT2':
          $outPut='专利授权到期续费';
        break;
        
        case '_ISST_THE1':
          $outPut='论文审查';
        break;
        
        case '_ISST_THE2':
          $outPut='论文发表';
        break;
        
        case '_ISST_PRO1':
          $outPut='项目申报';
        break;
        
        case '_ISST_PRO2':
          $outPut='项目立项';
        break;
        
        case '_ISST_PRO3':
          $outPut='项目执行';
        break;
        
        case '_ISST_PRO4':
          $outPut='项目验收';
        break;
        
        default:
          $outPut=$value;
        break;
        
      }
      return $outPut;
    }
    
    /**
     * 获取对应patent的内容
     */    
    public function patinfo()
    {
        return $this->hasOne('Patinfo');
    }    
    
    /**
     * 获取issue的过程记录
     */
    public function issrecords()
    {   
        return $this->hasMany('Issrecord');
    }
    
    //获取issinfo的多态模型,涉及issinfo表中的num_id和num_type两个字段内容
  //   public function num()
//    {
//        return $this->morphTo(null, [
//            '0' => 'app\issue\model\Issinfo',
//            '1' => 'app\project\model\Proinfo',
//            '2' => 'app\patent\model\Patinfo',
//        ]);
//    }
    
    //获取issinfo的多态模型,涉及issinfo表中的issmap_id和issmap_type两个字段内容。
     public function issmap()
    {
        $this->getData('issmap_type');
        //本模型内已定义获取器getIssmapTypeAttr,本属性读取的是获取器输出的issmap_type字段值（中文），所以要以获取器的输出值来对应模型
        $data=['专利授权申报' => 'Patinfo','专利授权到期续费' => 'Patinfo','项目申报' => 'Proinfo','论文审查' => 'Theinfo'];
        return $this->morphTo(null, $data);
        
        //return $this->morphTo(null, [
//            //已定义获取器getIssmapTypeAttr,本属性读取的是获取器输出的issmap_type字段值，所以要以获取器的输出值来对应模型
//            '专利授权申报' => 'Patinfo',
//            '专利授权到期续费' => 'Patinfo',
//            '项目申报' => 'Proinfo',
//            '论文审查' => 'Theinfo',
//        ]);
    }
    
    /**
     * 获取issue的附件
     */
    public function attachments()
    {
      return $this->morphMany('Attinfo','attmap','_ATTO1');
    }
    
    /**
     * 新增一个issue。
     * @param  array $data 新增issue的各项信息
     * @return integer|bool  新增成功返回主键，新增失败返回false
     * 要求：传入的数组下标名与模型属性名（数据表字段名）一模一样。
     */
    public function myCreate($data = [])
    {
        $result = $this->allowField(true)->save($data);
        if ($result) {
            return $this->getData('id');
        } else {
            return false;
        }
    }
    
    /**
     * 新增一个issue。
     * @param  array $data 新增issue的各项信息
     * @return integer|bool  新增成功返回主键，新增失败返回false
     * 要求：传入的数组下标名与模型属性名（数据表字段名）一模一样。
     */
    public function issCreate($data = [])
    {
        $result = $this->allowField(true)->save($data);
        if ($result) {
            return $this->getData('id');
        } else {
            return false;
        }
    }
    
}

?>