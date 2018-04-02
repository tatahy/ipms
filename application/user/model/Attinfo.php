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
     * 删除attachment记录及其对应的附件文件和目录
     * @param  integer $attId 删除attachment的id
     * @return integer|bool  删除成功返回true，未成功返回false
     *  考虑应用TP5的软删除进行改进，？？？2018/3/23
     */
    public function attDelete($attMapId)
    {
        $att=$this->where('attmap_id',$attMapId)->select();
        //默认查询出的所有记录对应的附件文件都在同一个目录下
        $fileDir=dirname($att[0]->attpath);
        //引用本模型中定义的fileDelete方法删除记录和附件文件
        for($i=0;$i<count($att);$i++){
          $this->fileDelete($att[i]->id);
        }
        
        //删除目录，若旧目录为空目录
        if(count(scandir($fileDir)==2)){
            rmdir($fileDir);
            return true;
        }else {
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
            // 移动到框架根目录的uploads/temp/ 目录下,并且使用md5规则重新命名文件。
            //新命名文件所在目录类似temp/72/ef580909368d824e899f77c7c98388.jpg 
            $info = $fileSet->rule('md5')
                            ->validate(['size'=>10485760,'ext'=>'jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,rar'])
                            ->move(ROOT_PATH.'uploads'.DS.'temp');
            
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
            
            $attId=$this->attCreate(array_merge($data,array('attpath'=>$path,'attfilename'=>$info->getFileName())));
            
            $att = $this->get($attId); 
      
            return $att;
            
        }else{
            // 上传失败获取错误信息
            //echo $file->getError();
            return $fileSet->getError();
        }
      
    }
    
    /**
     * 移动附件文件到目标目录
     * @param  string $fileStr    指向一个文件的全路径的字符串
     * @param  string $name       文件名
     * @param  string $targetDir  目标文件夹
     * @return Object|string      成功：返回true，未成功：返回false
     *
     */
     public function fileMove($fileStr,$name,$targetDir)
    {
          $fileDir=dirname($fileStr);
          //新目录是否存在
          if(is_dir($targetDir)){
            rename($fileStr,$targetDir.DS.$name);
          }else{
            mkdir($targetDir,0777);
            rename($fileStr,$targetDir.DS.$name);
          }
          
          //删除旧目录，若旧目录为空目录
          if(count(scandir($fileDir)==2)){
            rmdir($fileDir);
          }else{
            //旧目录不为空，返回true
            return true;
          }
          
          //文件存在返回true
          if(file_exists($targetDir.DS.$name)){
            return true;
          }else{
            return false;
          }
    }
    
     //删除单一文件及其记录
    public function fileDelete($attId)
    {
        $att= $this->where('id',$attId)->find();
        $attpath=$att->attpath;
        $name=$att->name;
        //删除attachment表中的附件文件记录
        $this->where('id',$attId)->delete();
        //删除文件，成功后返回
        if(file_exists($attpath)){
          unlink($attpath);
          $this->success("删除文件'".$name."'成功。");	
        }else{
          $this->error("未删除文件'".$name."'");	
        }
    }
      
    

}

?>