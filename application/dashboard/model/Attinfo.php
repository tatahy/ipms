<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\dashboard\model;

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
    
    // 开启时间字段自动写入
	protected $autoWriteTimestamp = true; 
    
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
        //…………………………………………
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
        //…………………………………………
        case 'edit':
          $outPut='撰写人';
        break;
        
        case 'audit':
          $outPut='审核人';
        break;
        
        case 'approve':
          $outPut='批准人';
        break;
        
        case 'execute':
          $outPut='执行人';
        break;
        
        case 'maintain':
          $outPut='维护人';
        break;
        
        default:
          $outPut=$value;
        break;
        
      }
      return $outPut;
    }
    
    /**
     * 新增一个attachment
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
     * @param  integer $attId 删除att的id
     * @return integer|bool  删除成功返回true，未成功返回false
     *  考虑应用TP5的软删除进行改进，？？？2018/3/23
     */
    public function attDelete($attMapId)
    {
        $att=$this->where('attmap_id',$attMapId)->select();
        
        if(count($att)){
            //默认查询出的所有记录对应的附件文件都在同一个目录下
            $fileDir=dirname($att[0]->attpath);
            //引用本模型中定义的singleDelete方法删除记录和附件文件
            for($i=0;$i<count($att);$i++){
              $this->singleDelete($att[$i]->id);
            }
            
            //删除目录，若旧目录为空目录scandir()函数的值为“2”
            if(count(scandir($fileDir))==2){
                rmdir($fileDir);
                $result = true;
                $msg='<br>附件删除完成。';
            }else {
                $result = false;
                $msg='<br>附件未删除干净，有残余附件。';
            }
        }else{
            $result = true;
            $msg='<br>无附件。';
        }
        
        return array('result'=>$result,'msg'=>$msg);
    }
    
    
    /**
     * 上传附件文件到本服务器temp目录
     * @param  $data array 数组，写入attinfo表的内容
     * @param  $fileObj Object TP5的文件对象
     * @return array(true|false,string,Object)  
     */
    public function fileUploadTemp($data=[],$fileObj)
    {
        $result=false;
        $msg='';
        $obj=array('id'=>0);
        $info=0;
        
        if(!empty($fileObj)){
            // 移动到框架根目录的uploads/temp/ 目录下,并且使用md5规则重新命名文件。
            //新命名文件所在目录及文件名类似temp/72/ef580909368d824e899f77c7c98388.jpg，若之前已上传过，会覆盖。 
            $info = $fileObj->rule('md5')
                            ->validate(['size'=>10485760,'ext'=>'jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,rar'])
                            ->move(ROOT_PATH.'uploads'.DS.'temp');
            
            if($info){
                // 成功上传后 获取上传信息
                // 文件的后缀名
                $info->getExtension()."<br/>";
                // 文件存放的文件夹路径：类似temp/42/a79759f284b767dfcb2a0197904287.jpg
                $info->getSaveName()."<br/>";
                // 完整的文件名
                $fileName=$info->getFilename(); 
                        
                $path= '..'.DS.'uploads'. DS.'temp'.DS.$info->getSaveName();
                
                //查找是否有重名的记录：
                $att=$this->getByAttfilename($fileName);
                
                if(count($att)){
                    $msg='<strong>附件已存在。</strong>由【'.$att->uploader.'】('.$att->rolename.')使用文件名："'.$att['name'].'"于'.$att->create_time.'上传。';
                    $obj=$att;
                    //释放对象，否则后续的unlink无法进行，会报错
                    $info='';
                    //查出的发生重名的记录里文件路径信息匹配'temp'?判断上传的文件与查出的重名文件是否都在'temp'文件夹下
                    if(!stripos($att->attpath,'temp')){
                        $fileDir=dirname($path);   
                        //删除本次已上传的文件及其所在目录
                        if(file_exists($path)){
                          unlink($path);
                        }                                             
                        //若目录为空目录,删除目录
                        if(count(scandir($fileDir))==2){
                            rmdir($fileDir);
                        }     
                    }
                }else{
                    //上传文件信息写入数据库
                    $attId=$this->attCreate(array_merge($data,array('attpath'=>$path,'attfilename'=>$info->getFileName())));
                    $att = $this->get($attId); 
                    $result=true;
                    $msg='附件"'.$att['name'].'"上传成功。';
                    $obj=$att;
                }   
            }else{
                // 上传失败获取错误信息
                //echo $file->getError();
                $msg = '<br>附件上传错误。<br>错误信息：'.$info->getError();
            }
            
        }else{
            
            $msg ='<br>无附件上传。';
        }
        
        return array('result'=>$result,'msg'=>$msg,'obj'=>$obj);

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
          if(count(scandir($fileDir))==2){
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
    public function singleDelete($attId)
    {
        $att= $this->where('id',$attId)->find();
        $attpath=$att->attpath;
        $name=$att->getAttr('name');//$att->name得到的是模型名称："Attinfo"
        $fileDir=dirname($attpath);
        //return $name;
        //删除attinfo表中的附件文件记录
        $att->delete();
        
        //删除文件，成功后返回
        if(file_exists($attpath)){
          unlink($attpath);
          $msg='文件"'.$name.'"删除成功。';
          $result=true;	
        }else{
          $msg='未删除文件"'.$name.'"';
          $result=false;
     	    
        }
        
        //若目录为空目录,删除目录
        if(count(scandir($fileDir))==2){
            rmdir($fileDir);
        }   
        return array('result'=>$result,'msg'=>$msg);

    }
    
    //下载单个文件
    public function singleDownload($attId)
    {
        $att= $this->where('id',$attId)->find();
        $attpath=$att['attpath'];
        
        if(file_exists($attpath)){
            header("Content-Type:application/octet-stream");
            header("Content-Disposition:attachment;filename=".$att['attfilename']);
            header('Content-Length:'.filesize($attpath));
            readfile($attpath);
            exit();
        }else{
            return array('result'=>false,'msg'=>'"'.$att['name'].'"文件不存在。');
        }
    }

}

?>