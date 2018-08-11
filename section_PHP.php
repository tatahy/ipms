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

<!--  HY 2018/6/13 -->
//json函数
<?php
json_encode($data, JSON_UNESCAPED_UNICODE) ;//第2个参数表示保持$data的现有显示，不进行unicode转码显示。
json_last_error(); //显示最后的json错误码


?>


<!--//  HY 2018/6/11 -->


<!--  HY 2018/7/26 -->
//8个魔术常量，不区分大小写，常量值会随着它们在代码中的位置而改变。
1. __LINE__：返回文件中的当前行号。也可写成__line__。
2. __FILE__：返回当前文件的绝对路径（包含文件名）。
3. __DIR__：返回当前文件的绝对路径（不包含文件名），等价于 dirname(__FILE__)。
4. __FUNCTION__：返回当前函数（或方法）的名称。
5. __TRAIT__：返回当前的trait名称（包括该trait的作用区域或命名空间）。
6. __METHOD__：返回当前的方法名（包括类名）。
7. __NAMESPACE__：返回当前文件的命名空间的名称。
8. __CLASS__：返回当前的类名（包括该类的作用区域或命名空间）。


//预定义常量
内核预定义常量：是在PHP的内核中就定义好了的常量。区分大小写。
PHP_VERSION：返回PHP的版本。
PHP_OS：返回执行PHP解释器的操作系统名称。
PHP_EOL：系统换行符，Windows是（\r\n），Linux是（\n），MAC是（\r）。

标准预定义常量：PHP默认定义的常量。区分大小写。
M_PI：返回圆周率π的值。


DIRECTORY_SEPARATOR:返回系统分隔符（linux:"/",windows:"\"），无需定义与包含直接使用

//预定义变量
PHP中的许多预定义变量都是"超全局的"，这意味着它们在一个脚本的全部作用域中都可用。在函数或方法中无需执行global $variable， 就可以访问它们。

超全局变量是在全部作用域中始终可用的内置变量。
1. $GLOBALS：global全局变量，是一个包含了所有全局变量的组合数组，全局变量的名称就是该组合数组的键。
2. $_GET：HTTP GET 变量，通过 URL 参数传递给当前脚本的变量的数组。
3. $_POST：HTTP POST 变量，通过 HTTP POST 方式传递给当前脚本的变量的数组。
4. $_COOKIE：HTTP Cookies 变量，通过 HTTP Cookies 方式传递给当前脚本的变量的数组。
5. $_SESSION：session 变量，当前脚本可用的 SESSION 变量的数组。
6. $_REQUEST：HTTP Request 变量，默认情况下包含了 $_GET，$_POST 和 $_COOKIE 的数组。
7. $_FILES：HTTP 文件上传变量，通过 HTTP POST 方式上传到当前脚本的项目的数组。
8. $_SERVER：服务器信息变量，包含了诸如头信息(header)、路径(path)、以及脚本位置(script locations)等信息的数组。这个数组中的项目由 Web 服务器创建。
9. $_ENV：环境变量，通过环境方式传递给当前脚本的变量的数组。

以下预定义变量都是非全局的。
1. $php_errormsg：前一个错误信息，$php_errormsg 变量包含由 PHP生成的最新错误信息。这个变量只在错误发生的作用域内可用，并且要求 track_errors 配置项是开启的（默认是关闭的）。
2. $HTTP_RAW_POST_DATA：包含 POST 提交的原始数据。
3. $http_response_header：HTTP 响应头，$http_response_header 数组与 get_headers() 函数类似。当使用HTTP包装器时，$http_response_header 将会被 HTTP 响应头信息填充。
5. $argc：传递给脚本的参数数目，包含当运行于命令行下时传递给当前脚本的参数的数目。脚本的文件名总是作为参数传递给当前脚本，因此 $argc 的最小值为 1，这个变量仅在 register_argc_argv 打开时可用。
6. $argv：传递给脚本的参数数组，包含当运行于命令行下时传递给当前脚本的参数的数组。第一个参数总是当前脚本的文件名，因此 $argv[0] 就是脚本文件名，这个变量仅在 register_argc_argv 打开时可用。



//



<!--//  HY 2018/7/26 -->
