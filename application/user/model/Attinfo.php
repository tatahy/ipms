<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\user\model;

use think\Model;
use think\Request;
use think\File as FileObj; 

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
    public function attUpdate($data = [],$id)
    {
        $result = $this::get($id)->allowField(true)->save($data);
        //$att=$this::get($id);
//        $result = $att->allowField(true)->data($data, true)->save();
        if ($result) {
            return $id;
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
        //delete()方法返回的是受影响记录数
        $result = $this->where('id',$attId)->delete();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
    
    
    /**
     * 上传附件文件到本服务器temp目录
     * @param  $fileSet Object 文件对象
     * @return Object|string  成功：返回新建的att记录，未成功：返回未成功信息
     *
     */
    public function fileUploadTemp($data=[],$fileSet)
    {

      if(!empty($fileSet)){
            // 移动到框架根目录的uploads/temp/ 目录下,系统重新命名文件名
            $info = $fileSet->validate(['size'=>10485760,'ext'=>'jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,rar'])
                        ->move(ROOT_PATH.DS.'uploads'.DS.'temp');
        }else{
            $this->error('未选择文件，请选择需上传的文件。');
        }
        
        if($info){
            // 成功上传后 获取上传信息
            // 文件的后缀名
            $info->getExtension()."<br/>";
            // 文件存放的文件夹路径：类似20160820/42a79759f284b767dfcb2a0197904287.jpg
            $info->getSaveName()."<br/>";
            // 完整的文件名
            $info->getFilename(); 
            
            $path= '..'.DS.'uploads'. DS.'temp'.DS.$info->getSaveName();
            
            array_push($data,array('attpath'=>$path));
            
            $attId=$this->attCreate($data);
            
            $att = $this->get($attId); 
      
            return $att;
            
        }else{
            // 上传失败获取错误信息
            //echo $file->getError();
            return $fileSet->getError();
        }
      
    }
    
    /**
     * 上传附件文件到本服务器temp目录
     * @param  $fileSet Object 文件对象
     * @return Object|string  成功：返回新建的att记录，未成功：返回未成功信息
     *
     */
     public function fileMove($fileName,$targetDir,$id)
    {
      //得到文件对象
      $file = new FileObj($fileName); 
      
      //文件移动到$targetDir目录
      $fileMove=$file->move($targetDir);
    
       //引用attinfo模型中定义的方法向attinfo表更新信息
      $attId = $this->attUpdate($data=array('path'=>$targetDir),$id);
      
      if($attId && $fileMove){
        return true;
      }else{
        return false;
      }
      
    }

}

?>