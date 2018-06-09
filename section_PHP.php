
<!--  HY 2018/5/31 -->
json_encode函数的第二参数“JSON_UNESCAPED_UNICODE”，保持数组的中文原样。默认是转为UTF-8编码(\u开头的4位16进制字符串)。
<?php
$data=array('iss'=>array('id'=>4,'info'=>array('abstract'=>'哈哈','num_id'=>2)));
json_encode($data,JSON_UNESCAPED_UNICODE);

json_last_error();//显示错误代码。
?>

<!--//  HY 2018/5/31 -->

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

