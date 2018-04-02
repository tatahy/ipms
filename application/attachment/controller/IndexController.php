<?php
namespace app\attachment\controller;

use think\Request;
use think\Session;
use app\issue\model\Issinfo as IssinfoModel;
use app\attachment\model\Attinfo as AttinfoModel;


class IndexController extends \think\Controller
{
    public function index()
    {
        return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
    }
    
    //上传文件
    public function upload(Request $request)
    {
        
        // 获取表单上传文件
        //$file = request()->file('file');
        $file = $request->file('file');
        $num_id= $request->param('num_id');
        
        if(!empty($file)){
            // 移动到框架应用根目录/public/uploads/ 目录下,系统重新命名文件名
            $info = $file->validate(['size'=>10485760,'ext'=>'jpg,jpeg,pdf,doc,docx,xls,xlsx,ppt,pptx'])
                        ->move(ROOT_PATH.'uploads'.DS.$num_id);
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
            
            $path=array('attpath'=> '.'.DS.'uploads'. DS.$num_id.DS.$info->getSaveName());
            
            //POST数组中没有‘attpath’字段，将‘attpath’字段值与POST数组合并成一个新的数组
            $att_data=array_merge($request->param(),$path);
            
            //通过外部提交赋值给模型Attinfo类，由Attinfo类将数据写入数据表
            $att = new AttinfoModel($att_data); 
            //过滤post数组中的非数据表字段数据后写入数据表
            $att->allowField(true)->save(); 
            //获取自增ID值
            $att->id;
            
            $this->success('文件上传成功。');
            
        }else{
            // 上传失败获取错误信息
            //echo $file->getError();
            $this->error($file->getError());
        }
        //return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
    }
    
    //下载文件
    public function download(Request $request)
    {
        $att= AttinfoModel::get($request->param('id'));
        $attpath=$att->attpath;
        $name=$att->name;
        $file_name=substr($attpath,31);
                
        if(!file_exists($attpath)){
            $this->error("'".$name."'文件不存在。");  
        }else{
            header("Content-Type:application/octet-stream");
            header("Content-Disposition:attachment;filename=".$file_name);
            header('Content-Length:'.filesize($attpath));
            readfile($attpath);
            exit();
        }
        
    }
    
    //删除文件
    public function delete(Request $request)
    {
        $att= AttinfoModel::get($request->param('id'));
        $attpath=$att->attpath;
        $name=$att->name;
        //删除attachment表中的附件文件记录
        AttinfoModel::destroy($request->param('id'),true);
        //删除文件，成功后返回
        if(file_exists($attpath)){
          unlink($attpath);
          $this->success("删除文件'".$name."'成功。");	
        }else{
          $this->error("未删除文件'".$name."'");	
        }
        
        
        //return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
    }
    
}
