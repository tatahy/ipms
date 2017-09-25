<?php

/**
 * @author TATA
 * @copyright 2017
 * 定义验证器类Ipvalidate
 */
namespace app\common\validate;

use think\Validate;

class Ipvalidate extends Validate
{
    protected $rule=[
        //login.html
        'name'=>'require|max:25',
        'pwd'=>'require',
        
        //patmod.html
        'topic'=>'require',
        'inventor'=>'require',
        'otherinventor'=>'require',
        'keyword'=>'require',
        'summary'=>'require',
        'pronum'=>'require',
        
        //patmaintain.html
        'patapplynum'=>'require',
        'patagency'=>'require',
        'patadmin'=>'require',

    ];
    
    protected $message=[
        //login.html
        'name.require'=>'用户名必须',
        'pwd.require'=>'密码必须',
        
        //patmod.html
        'topic'=>'题目必须',
        'inventor'=>'发明人必须',
        'otherinventor'=>'其他发明人必须',
        'keyword'=>'关键词必须',
        'summary'=>'简介必须',
        'pronum'=>'关联项目必须',
        
        //patmaintain.html
        'patapplynum'=>'申请编号必须',
        'patagency'=>'代理机构必须',
        'patadmin'=>'授权机构必须',
    ];
    
    protected $scene=[
        'login'=>['name','pwd'],
        
        'patmod'=>['topic','inventor','otherinventor','keyword','summary','pronum'],
        
        'patmaintain'=>['patapplynum','patagency','patadmin'],
    ];
}


?>