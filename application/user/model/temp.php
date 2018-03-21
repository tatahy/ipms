<?php

//5.attinfo表更新
      //循环更新attMdl,将文件从现有的‘temp’目录移动到指定目录
      for($i=0;$i<count($arrAttId);$i++){
             
        $fileObj=$arrAttFileName[$i];
        $targetDir=ROOT_PATH.DS.'uploads'.DS.$issSet->issnum;
                
        //有‘temp’字符串才移动到指定目录
        while(substr_count($fileObj,'temp')){
                  
          //引用attinfo模型中定义的fileMove()方法，将文件从‘temp’目录移动到指定目录
          $fileMoveResult=$attMdl->fileMove($fileObj,$targetDir);
                    
          //移动是否成功：
          if($fileMoveResult){
            $attData=array(
            'num_id'=>$issSet->issnum,
            'attmap_id'=>$issSet->id,
            'attpath'=>$targetDir.$fileMoveResult,
            //'deldisplay'=>0
            );
            
            //添加到$attData的内容,更新att
            $attId = $attMdl->attUpdate(array_push($attData,$attDataPatch),$arrAttId[$i]);
                        
            $msg.="附件".$arrAttFileName[$i]."移动成功<br>"; 
          }else{
            $msg.="附件".$arrAttFileName[$i]."移动失败<br>"; 
          }
        } 
      }


?>