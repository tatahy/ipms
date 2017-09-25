<?php

/**
 * @author TATA
 * @copyright 2017
 * 定义验证器类Ipvalidate
 */
namespace app\common\iclass\validate;

use think\Validate;

class Ipval extends Validate
{
    protected $rule=[
        'name'=>'require|max:25',
        'pwd'=>'require',
    ];
    
    protected $message=[
        'username.require'=>'用户名必须',
        'pwd.require'=>'密码必须',
    ];
    
    protected $scene=[
        'login'=>['username','pwd'],
        
    ];
}


?>