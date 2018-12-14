<?php

/**
     * 实现从数组的根节点开始，得到该数组从根节点开始到最后一层节点的所有路径的数组。
     * @param  Array $tarArr, 多维关联数组（json对象转换来的数组）。
     * @param  Array $nameArr，定义$tarArr的根节点名（string，默认为空,可指定任意名称），要忽略的节点名（string数组）
     * @return Array $pathArr, 路径数组。
     * #闭包+递归
     * 对数组有结构要求:要求最后一层节点下保存的是关联数组
     * 使用2个地址引用变量：&$pathArr存储每次递归结果，&$pathFind实现闭包的递归调用
     */
function fn_get_json_path_array($tarArr,$name='')
{
  $pathArr=[];
  #$nameArr=['',['']];
  $nameArr[0]=is_string($name)?$name:'';
  $nameArr[1]=is_array($name)?$name:[''];
  $rootName=$nameArr[0];
  $igNodeNameArr=$nameArr[1];

  #闭包的定义
  $pathFind=function($arr,$nodeName='',$igNodeNameArr=[''])use(&$pathArr,&$pathFind){
      $subKeys=[];
      $subVals=[];
      $path='';
      #递归的基准情况    
      if(is_array($arr)){
          $subKeys=array_keys($arr);
          $subVals=array_values($arr);
      }else{
          #递归调用终止
          return false;
      }
      
  	#判断当前节点名是否为最后一层的节点名。不是最后一层的节点名，返回本层节点名与下一层节点名组合后的数组，递归操作主体
      if(is_string($subKeys[0]) && is_array($subVals[0])){
          #删除当前节点名。
          array_splice($pathArr,array_search($nodeName,$pathArr),1);         
          foreach($subKeys as $v){
              #是否为不希望遍历的节点名
              if(!in_array($v,$igNodeNameArr,true)){
                $path=empty($nodeName)?$v:$nodeName.'-'.$v; 
                array_push($pathArr,$path);
                #递归调用本闭包
                $pathFind($arr[$v],$path,$igNodeNameArr);
              }
          }
      }
  };
  //调用闭包函数，设定$pathArr的值。
  $pathFind($tarArr,$rootName,$igNodeNameArr);
  
 	return $pathArr;
}

/**
     * 比较$tarArr数组和$refArr，补足$tarArr中缺失的部分。
     * @param  Array $tarArr，目标多维关联数组（json对象转换来的数组）。
     * @param  Array $refArr，基准多维关联数组
     * @return Array $arr, 补足空缺后的$tarArr数组。
     * #闭包+递归，
     */
function fn_get_intact_json_array($tarArr,$refArr)
{
  
//  #闭包的定义
//  $pathFind=function($arr,$nodeName='')use(&$tarArr,&$refArr){
//      $subKeys=[];
//      $subVals=[];
//      $path='';
//      #递归的基准情况    
//      if(is_array($arr)){
//          $subKeys=array_keys($arr);
//          $subVals=array_values($arr);
//      }else{
//          #递归调用终止
//          return false;
//      }
//      
//  	#判断当前节点名是否为最后一层的节点名。不是最后一层的节点名，返回本层节点名与下一层节点名组合后的数组，递归操作主体
//      if(is_string($subKeys[0]) && is_array($subVals[0])){
//          #删除当前节点名。
//          array_splice($pathArr,array_search($nodeName,$pathArr),1);         
//          foreach($subKeys as $v){
//              #是否为不希望遍历的节点名
//              if(!in_array($v,$igNodeNameArr,true)){
//                $path=empty($nodeName)?$v:$nodeName.'-'.$v; 
//                array_push($pathArr,$path);
//                #递归调用本闭包
//                $pathFind($arr[$v],$path,$igNodeNameArr);
//              }
//          }
//      }
//  };
  $pathArr=fn_get_json_path_array($tarArr);
  
  #定义闭包函数，用闭包定义递归完成补全$dbArr的当前$val项所在数组的所有值，基于数组的合并函数。
  #$tarArr是$refArr的子集，$path是节点访问路径
  $mergeArr=function($path)use(&$mergeArr,&$tarArr,&$refArr){
    $k='';
    $arr=[];
    #递归的基准情况     
  	if(strpos($path,'-')===false){
			${$tarArr[$path]}=array_merge(${$refArr['_'.strtoupper($path)]},${$tarArr[$path]});
      #$tarArr[$path]=array_merge($refArr['_'.strtoupper($path)],$tarArr[$path]);
      #递归调用终止
      return false;
		}else{
		  $arr=explode('-',$path);
      
		}
    
    if(count($arr)){
		  $k=array_shift($arr);
		  $path=implode('-',$arr);
		  $mergeArr($path);
		}

  };
  
  foreach($pathArr as $v){
    $mergeArr($v);
    //if(strpos($v,'-')===false){
//			$tarArr[$v]=array_merge($refArr['_'.strtoupper($v)],$tarArr[$v]);
//		}else{
//		  $arr=explode('-',$v);
//      #$tarArr[$arr[0]][$arr[1]]=array_merge($refArr['_'.strtoupper($arr[0])]['_'.strtoupper($arr[1])],$tarArr[$arr[0]][$arr[1]]);
//		}
  
  }
  
  return $tarArr;
        
}  
