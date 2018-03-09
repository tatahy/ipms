<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\user\model;

use think\Model;

class Attinfo extends Model
{   
    //protected $auto = ['patnum','pronum'];
    //protected $insert = ['issnum',];  
    //protected $update = [];  
    
    //只读字段，这个字段的值一旦写入，就无法更改。
    //protected $readonly = ['issnum'];
    
    //获取器，获取数据表attinfo中atttype字段值，转换为中文输出，待考虑是否采用？？
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
          $outPut='……';
        break;
        
      }
      return $outPut;
    }
    
    
    //获取attachment的多态模型,涉及attinfo表中的attmap_id和attmap_type两个字段内容
     public function attmap()
    {
        return $this->morphTo(null, [
            '_ATTO1' => 'Issinfo',
            '_ATTO2' => 'Patinfo',
            '_ATTO3' => 'Proinfo',
            '_ATTO4' => 'Theinfo',
        ]);
    }
    
     //获取器，获取数据表issrecord中rolename字段值，转换为中文输出
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
    
    /**
     * 新增一个att。
     * @param  array $data 新增att的各项信息
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
     * 新增一个attachment。
     * @param  array $data 新增att的各项信息
     * @return integer|bool  新增成功返回主键，新增失败返回false
     * 要求：传入的数组下标名与模型属性名（数据表字段名）一模一样。
     */
    public function attCreate($data = [])
    {
        $result = $this->allowField(true)->save($data);
        if ($result) {
            return $this->getData('id');
        } else {
            return false;
        }
    }
    
    /**
     * 更新attachment。
     * @param  array $data 更新attachment的各项信息
     * @param  integer $attId 删除attachment的id
     * @return integer|bool  更新成功返回主键，未更新返回false
     * 要求：传入的数组下标名与模型属性名（数据表字段名）一模一样。
     */
    public function attUpdate($data = [],$attId)
    {
        $result = $this->where('id',$attId)->allowField(true)->save($data);
        if ($result) {
            return $this->getData('id');
        } else {
            return false;
        }
    }
    
     /**
     * 删除attachment。
     * @param  integer $attId 删除attachment的id
     * @return integer|bool  删除成功返回主键，未成功返回false
     *
     */
    public function attDelete($attId)
    {
        $result = $this->where('id',$attId)->delete();
        if ($result) {
            return $this->getData('id');
        } else {
            return false;
        }
    }

}

?>