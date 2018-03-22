<?php

//20个oprt分3大类情况对5个数据表分别进行操作
      if($patId==0 && $issId==0){
        // $oprt=='_ADDNEW'
        //1. pat
       
        
        //2. patRd
        
        
        //3. iss
        
        
        //4. issRd
        
      }else if($patId!=0 && $issId==0){
        // $oprt=='_ADDRENEW'
        switch($oprt){
          case'':
          
          break;
          
          default:
          
          break;
          
        }
        
        //1. pat
       
        
        //2. patRd
        
        
        //3. iss
        
        
        //4. issRd
      
        
      }else if($patId!=0 && $issId!=0){
        if($oprt=='_DELETE'){
          
          
        }elseif($oprt=='_UPDATE'){
          
        }elseif($oprt=='_ACCEPT' | $oprt=='_REFUSE' | $oprt=='_REPORT'| $oprt=='_FINISH' | $oprt=='_CLOSE'){
          
        }else{
          //$oprt=='_SUBMIT' | 
          //$oprt=='_PASS' | $oprt=='_FAIL'| $oprt=='_MODIFY' | 
          //$oprt=='_PERMIT' | $oprt=='_VETO' | $oprt=='_COMPLETE'
          //$oprt=='_APPLY' | $oprt=='_IMKPROVE' | $oprt=='_REJECT' | $oprt=='_AUTHORIZE' 
          
        }
        //1. pat
       
        
        //2. patRd
        
        
        //3. iss
        
        
        //4. issRd
      }


?>