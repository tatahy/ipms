<?php

//asset的状态数组共7类14个
const conAssStatusArr=['*'=>'*',
                    '_ASSS0'=>'新增_待验收',
                    '_ASSS1_1'=>'验收合格_待分配',
                    '_ASSS1_2'=>'维修验收_待确认',
                    '_ASSS2_1'=>'正常_折旧中',
                    '_ASSS2_2'=>'正常_折旧完',
                    '_ASSS3_1'=>'异常_待更新',
                    '_ASSS3_2'=>'异常_待维修',
                    '_ASSS3_3'=>'异常_遗失',
                    '_ASSS4_1'=>'停用_维修中',
                    '_ASSS4_2'=>'停用_封存',
                    '_ASSS4-3'=>'停用_待销账',
                    '_ASSS4-4'=>'遗失_待销账',
                    '_ASSS5'=>'销账',
                    '_ASSS6'=>'回收站'
                    ];

