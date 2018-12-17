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
     * 比较$dbAuth数组和$conAuth，补足$dbAuth中缺失的部分。
     * @param  Array $dbAuth，目标多维关联数组（json对象转换来的数组），地址引用。
     * @param  Array $conAuth，基准多维关联数组，地址引用。已在app/common.php中定义好。
     * @return Array $dbAuth, 补足空缺后的$dbAuth数组。
     * 递归，
     */
function fn_merge_auth(&$dbAuth,&$conAuth)
{
  if(empty($conAuth))return;
  $lKey='';
  foreach($conAuth as $k=>&$v){
    #$dbAuth与$conAuth的键名有一个转换关系类似$dbAuth['iss']['pat']与$conAuth['_ISS']['_PAT']
    if(strpos($k,'_')===0){
      $lKey=strtolower(ltrim($k,"_"));
    }else{
      $lKey=$k;
    }
    
    if(is_array($v) && isset($dbAuth[$lKey])){
      fn_merge_auth($dbAuth[$lKey],$v);
    }else {
      #因上述的转换关系就需要对$dbAuth与$conAuth进行逐层比对，复制。
      #如果无上述的转换关系，2个数组键名一样就只要一条语句：$dbAuth[$lKey]=$v;
      if(isset($dbAuth[$lKey]) && $dbAuth[$lKey]){
        $dbAuth[$lKey]=1;
      }else if(is_array($v)){
        $dbAuth[$lKey]=[];
        fn_merge_auth($dbAuth[$lKey],$v);
      }else{
        $dbAuth[$lKey]=$v;
      }
    }
  }
  return $dbAuth;
}
