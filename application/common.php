<?php
/**
 * @author TATA
 * @copyright 2018
 * 应用公共文件
 * 所有的公共函数都以"_common"开头，再遵循驼峰命名法。
 * 所有的公共常量都以"con"开头，再遵循驼峰命名法。
 */
//asset的状态数组大类7类
const conAssTypeArr=[
                    '_ASSS_USUAL'=>'除"回收站"以外的其它5类状态',
                    '_ASSS1'=>'待定',
                    '_ASSS2'=>'正常',
                    '_ASSS3'=>'异常',
                    '_ASSS4'=>'停用',
                    '_ASSS5'=>'销账',
                    '_ASSS6'=>'回收站'
                    ];
//asset的状态数组细分共7类17个
const conAssStatusArr=[//除‘回收站’以外的其它状态
                    '_ASSS0'=>'*',
                    //label-info，待定
                    '_ASSS1'=>'待定',
                    '_ASSS1_1'=>'填报中',
                    '_ASSS1_2'=>'新增_待验收',
                    '_ASSS1_3'=>'待分配_初次验收合格',
                    '_ASSS1_4'=>'待分配_维修验收合格',
                    //label-success，正常
                    '_ASSS2'=>'正常',
                    '_ASSS2_1'=>'正常_折旧中',
                    '_ASSS2_2'=>'正常_折旧完',
                    //label-warning，异常
                    '_ASSS3'=>'异常',
                    '_ASSS3_1'=>'异常_待审核',
                    '_ASSS3_2'=>'异常_待维修',
                    '_ASSS3_3'=>'异常_遗失',
                    //label-default，停用
                    '_ASSS4'=>'停用',
                    '_ASSS4_1'=>'停用_维修中',
                    '_ASSS4_2'=>'停用_封存',
                    '_ASSS4_3'=>'停用_遗失',
                    '_ASSS4_4'=>'待销账_报废',
                    '_ASSS4_5'=>'待销账_遗失',
                    //label-default，销账
                    '_ASSS5'=>'销账',
                    //label-danger
                    '_ASSS6'=>'回收站'
                    ];

//asset的状态标志数组共7类15个，要发送给前端决定label标签类型
const conAssStatusLabelArr=[
                    '_ASSS0'=>'info',
                    //label-info
                    '_ASSS1'=>'info',
                    '_ASSS1_1'=>'info',
                    '_ASSS1_2'=>'info',
                    '_ASSS1_3'=>'info',
                    '_ASSS1_4'=>'info',
                    //label-success
                    '_ASSS2'=>'success',
                    '_ASSS2_1'=>'success',
                    '_ASSS2_2'=>'success',
                    //label-warning
                    '_ASSS3'=>'warning',
                    '_ASSS3_1'=>'warning',
                    '_ASSS3_2'=>'warning',
                    '_ASSS3_3'=>'warning',
                    //label-default
                    '_ASSS4'=>'default',
                    '_ASSS4_1'=>'default',
                    '_ASSS4_2'=>'default',
                    '_ASSS4_3'=>'default',
                    '_ASSS4_4'=>'default',
                    '_ASSS4_5'=>'default',
                    '_ASSS5'=>'default',
                    //label-danger
                    '_ASSS6'=>'danger'
                    ];                    

//asset的操作数组,4类（CURD）10个
const conAssOprtArr=['_CREATE'=>'新增',
                    '_SUBMIT'=>'送审',
                    '_AUDIT'=>'审核',
                    '_APPROVE'=>'审批',
                    '_MAINTAIN'=>'维护',
                    '_UPDATE'=>'更新',
                    '_TRASH'=>'回收',
                    '_RESTORE'=>'还原',
                    '_READ'=>'查阅',
                    '_DELETE'=>'删除',
                    ];
  /**
     * 各个模块权限设置初始值
     * 参数$module，类型：字符串。值：可为空。说明：模块名称。默认值：'_ALL'
     */
  function _commonModuleAuth($module='')
  {
    //各个模块的名称
    $nameArr=array('_ISS','_PAT','_PRO','_THE','_ATT','_ADMIN','_ASS','');
    //if(empty($module)){
//      $module='_ALL';
//    }else{
//      //判断$module的取值是否在规定的数组范围内
//      if (in_array($module,$nameArr)== FALSE) {
//        $auth='wrong parameter for function.';
//      }
//    }
    
    //判断$module的取值是否在规定的数组范围内
    if(in_array($module,$nameArr)){
        if(empty($module))$module='_ALL';
    }else{
        $auth='wrong parameter for function.';
    }
    
//    $authIss=array('编辑'=>0,'审核'=>0,'审批'=>0,'执行'=>0,'维护'=>0);
//    $authPat=array('编辑'=>0,'审核'=>0,'审批'=>0,'执行'=>0,'维护'=>0);
//    $authPro=array('编辑'=>0,'审核'=>0,'审批'=>0,'执行'=>0,'维护'=>0);
//    $authThe=array('编辑'=>0,'审核'=>0,'审批'=>0,'执行'=>0,'维护'=>0);
//    $authAtt=array('上传'=>0,'下载'=>0,'删除'=>0,'移动'=>0,'复制'=>0); 
    //'read'权限仅为用户登录后前台的“查阅”权限，后台“用户中心”无需该权限
    $authIss=array('edit'=>0,'audit'=>0,'approve'=>0,'execute'=>0,'maintain'=>0);
    $authPat=array('edit'=>0,'audit'=>0,'approve'=>0,'execute'=>0,'maintain'=>0);
    $authPro=array('edit'=>0,'audit'=>0,'approve'=>0,'execute'=>0,'maintain'=>0);
    $authThe=array('edit'=>0,'audit'=>0,'approve'=>0,'execute'=>0,'maintain'=>0);
    $authAtt=array('upload'=>0,'download'=>0,'erase'=>0,'move'=>0,'copy'=>0);
    $authAss=array('read'=>0,'edit'=>0,'audit'=>0,'approve'=>0,'maintain'=>0);

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
      case'_ASS':
        $auth=$authAss;
      break;
      //all
      case'_ALL':
      //default:
        $auth=array('iss'=>$authIss,'pat'=>$authPat,'att'=>$authAtt,'pro'=>$authPro,
                    'the'=>$authThe,'admin'=>$authAdmin,'ass'=>$authAss);
      break;
    }
    return $auth;
  }
  
  
  /**
     * issue的权限与issue状态的对应关系
     * 参数$issType，类型：字符串。值：不为空。说明：需要得到的iss类型。
     * 参数$authName，类型：字符串。值：不为空。说明：issue权限名称。
     */
  function _commonIssAuthStatus($issType='',$authName='')
  {
    $issTypeArr=array('_PAT','_PRO','_THE');
    //判断$issType的取值是否在规定的数组范围内
    if(in_array($issType,$issTypeArr)){
        $issType;
    }else{
        return 'The parameter should be a string in:'.json_encode($issTypeArr);
    }
    
    $status=array();
    
    switch($issType){
      case'_PAT':
        $authNameArr=array_keys(_commonModuleAuth('_ISS'));
        foreach($authNameArr as $value){
            switch($value){
              case'maintain':
                  $status[$value]=array('申报复核','申报提交','续费提交','准予续费','否决申报',
                                '专利授权','专利驳回','放弃续费','续费授权','续费新增');
              break;   
              
              case'edit':
                  $status[$value]=array('申报新增','填报','返回修改','修改完善');
              break;
              
              case'audit':
                  $status[$value]=array('待审核');
              break;
              
              case'approve':
                  $status[$value]=array('审核未通过','审核通过','变更申请','拟续费');
              break;
              
              case'execute':
                  $status[$value]=array('批准申报','申报执行','申报修改','准予变更','否决变更');
              break;
                       
            }
            
        }
      break;
      
      case'_PRO':
        $authNameArr=array_keys(_commonModuleAuth('_ISS'));
        
      break;
      
      case'_THE':
        $authNameArr=array_keys(_commonModuleAuth('_ISS'));
        
      break;
    }
    
    return $status[$authName];
  
  }
  
   /**
     * 将iss权限数组的$key转为中文
     * 参数$authArr，类型：数组。值：不为空。说明：需要进行数组键名转换的数组。
     */
  function _commonAuthArrKeyToCHN($authArr=array())
  {
      $issTemp=array();
      foreach($authArr as $k=>$v){
              switch($k){
                case 'edit':
                  $issTemp['编辑']=$v;
                break;
                
                case 'audit':
                  $issTemp['审核']=$v;
                break;
                
                case 'approve':
                  $issTemp['审批']=$v;
                break;
                
                case 'execute':
                  $issTemp['执行']=$v;
                break;
                
                case 'maintain':
                  $issTemp['维护']=$v;
                break;
                //...................附件.....................
                case 'upload':
                  $issTemp['上传']=$v;
                break;
                
                case 'download':
                  $issTemp['下载']=$v;
                break;
                
                case 'erase':
                  $issTemp['删除']=$v;
                break;
                
                case 'move':
                  $issTemp['移动']=$v;
                break;
                
                case 'copy':
                  $issTemp['复制']=$v;
                break;
                //...................!附件.....................
                
               // case 'enable':
//                  $issTemp['启用']=$v;
//                break;
              }
          }
      return $issTemp;
    
  }

