<?php


//调用User模型层定义的refreshUserAuth()方法，刷新登录用户的各个模块权限
        $authority=UserModel::refreshUserAuth($username,$pwd);
        

?>