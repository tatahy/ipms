<?php

//asset的状态数组
const assStatusArr=['*','新增(待分配)',
                '正常(折旧中)','正常(折旧完)',
                '异常(待更新)','异常(待维修)','异常(遗失)',
                '停用(维修中)','停用(封存)','停用(待销账)',
                '销账'];
                
    function assStatusArr()
    {
        return assStatusArr;
    }

