<?php
/**
 * @author TATA
 * @copyright 2018
 * 应用公共文件
 */

 //各个模块权限设置初始值
  function _commonModuleAuth($module='')
  {
    $nameArr=array('_ISS','_PAT','_PRO','_THE','_ATT','_ADMIN');
    if(empty($module)){
      $module='_ALL';
    }else{
      //判断$module的取值是否在规定的数组范围内
      if (in_array($module,$nameArr)== FALSE) {
        $auth='wrong parameter for function.';
      }
    }
    
//    $authIss=array('编辑'=>0,'审核'=>0,'审批'=>0,'执行'=>0,'维护'=>0);
//    $authPat=array('编辑'=>0,'审核'=>0,'审批'=>0,'执行'=>0,'维护'=>0);
//    $authPro=array('编辑'=>0,'审核'=>0,'审批'=>0,'执行'=>0,'维护'=>0);
//    $authThe=array('编辑'=>0,'审核'=>0,'审批'=>0,'执行'=>0,'维护'=>0);
//    $authAtt=array('上传'=>0,'下载'=>0,'删除'=>0,'移动'=>0,'复制'=>0); 
        
    $authIss=array('edit'=>0,'audit'=>0,'approve'=>0,'execute'=>0,'maintain'=>0);
    $authPat=array('edit'=>0,'audit'=>0,'approve'=>0,'execute'=>0,'maintain'=>0);
    $authPro=array('edit'=>0,'audit'=>0,'approve'=>0,'execute'=>0,'maintain'=>0);
    $authThe=array('edit'=>0,'audit'=>0,'approve'=>0,'execute'=>0,'maintain'=>0);
    $authAtt=array('upload'=>0,'download'=>0,'erase'=>0,'move'=>0,'copy'=>0);

    $authAdmin=array('enable'=>0);

    switch($module){
      case'_ISS':
        $auth=$authIss;
      break;
      case'_PAT':
        $auth=$authPat;
      break;
      case'_PRO':
        $auth=$authPro;
      break;
      case'_THE':
        $auth=$authThe;
      break;
      case'_ATT':
        $auth=$authAtt;
      break;
      case'_ADMIN':
        $auth=$authAdmin;
      break;
      //all
      case'_ALL':
      //default:
        $auth=array('iss'=>$authIss,'pat'=>$authPat,'att'=>$authAtt,'pro'=>$authPro,'the'=>$authThe,'admin'=>$authAdmin);
      break;
    }
    return $auth;
  }

