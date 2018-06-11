//PHP5.6

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

<!--  HY 2018/6/11 -->
//时间函数、日期函数

//如何获取时间戳。
<?php  
echo "今天:".date("Y-m-d")."<br>";       
echo "昨天:".date("Y-m-d",strtotime("-1 day")), "<br>";       
echo "明天:".date("Y-m-d",strtotime("+1 day")). "<br>";    
echo "一周后:".date("Y-m-d",strtotime("+1 week")). "<br>";       
echo "一周零两天四小时两秒后:".date("Y-m-d G:H:s",strtotime("+1 week 2 days 4 hours 2 seconds")). "<br>";       
echo "下个星期四:".date("Y-m-d",strtotime("next Thursday")). "<br>";       
echo "上个周一:".date("Y-m-d",strtotime("last Monday"))."<br>";       
echo "一个月前:".date("Y-m-d",strtotime("last month"))."<br>";       
echo "一个月后:".date("Y-m-d",strtotime("+1 month"))."<br>";       
echo "十年后:".date("Y-m-d",strtotime("+10 year"))."<br>"; 

//得到此时此刻的Unix timestamp
strtotime("now");
time();
//得到"10 September 2000"的Unix timestamp
strtotime("10 September 2000");
//得到"+1 week 2 days 4 hours 2 seconds"的Unix timestamp
strtotime("+1 week 2 days 4 hours 2 seconds");

//得到明天的Unix timestamp
$tomorrow = mktime(0,0,0,date("m"),date("d")+1,date("Y"));
//得到明天此时此刻的Unix timestamp
$tomorrowNow = mktime(dat("H"),dat("i"),dat("s"),date("m"),date("d")+1,date("Y"));
//以yyyy-mm-dd形式显示明天的日期
date("Y-m-d",$tomorrow);
//以yyyy-mm-dd hh:mm:ss形式显示此时此刻的日期、时间
date("Y-m-d H:i:s");		     
?>



<!--//  HY 2018/6/11 -->

