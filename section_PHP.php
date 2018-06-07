<!--  HY 2018/6/7 -->

//数组赋值、定义
//array()===[]
<?php
$data = array(
        'iss' => array(
          'id' => 1,
          'info' => array('abstract' => '也issinfo.abstract'),
          'record' => array('act' => '由patrecord.act')),
        'pat' => array(
          'id' => 3,
          'info' => array('abstract' => '啊patinfo.abstract'),
          'record' => array('act' => '哈patrecord.act')),
        'att' => array(
          'info'=>array(),
          'arrId' => array(5,2),
          'arrFileName' => array('name'=>'0-1,2','kk'),
          'arrFileObjStr' => array('yy','99'))
        );

$data1 = [
        'iss' => [
          'id' => 1,
          'info' => ['abstract' => '也issinfo.abstract'],
          'record' => ['act' => '由patrecord.act']],
        'pat' => [
          'id' => 3,
          'info' => ['abstract' => '啊patinfo.abstract'],
          'record' => ['act' => '哈patrecord.act']],
        'att' => [
          'info'=>[],
          'arrId' => [5,2],
          'arrFileName' => ['name'=>'0-1,2','kk'],
          'arrFileObjStr' => ['yy','99']]
        ];

// $data==$data1;
?>



<!--//  HY 2018/6/7 -->

