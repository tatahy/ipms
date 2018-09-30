<?php


//asset的状态数组共7类12个
const conAssStatusArr=['*'=>'*',
                    '_ASSS0'=>'新增_待验收',
                    '_ASSS1'=>'验收合格_待分配',
                    '_ASSS2_1'=>'正常_折旧中',
                    '_ASSS2_2'=>'正常_折旧完',
                    '_ASSS3_1'=>'异常_待更新',
                    '_ASSS3_2'=>'异常_待维修',
                    '_ASSS3_3'=>'异常_遗失',
                    '_ASSS4_1'=>'停用_维修中',
                    '_ASSS4_2'=>'停用_封存',
                    '_ASSS4-3'=>'停用_待销账',
                    '_ASSS5'=>'销账'
                    ];

//asset的状态与操作的对应关系（conAssStatusOprtArr）
const conAssStatusOprtArr=[
                      ['status'=>'*','statusChi'=>'*','oprt'=>['_SAVE','_SUBMIT','_TRASH','_RESTORE','_DELETE']],
                      ['status'=>'_ASSS0','statusChi'=>'新增_待验收','oprt'=>['_SAVE','_SUBMIT']],
                      ['status'=>'_ASSS1','statusChi'=>'验收合格_待分配','oprt'=>['_SAVE','_AUDIT']],
                      ['status'=>'_ASSS2_1','statusChi'=>'正常_折旧中','oprt'=>['_SAVE','_SUBMIT']],
                      ['status'=>'_ASSS2_2','statusChi'=>'正常_折旧完','oprt'=>['_SAVE','_SUBMIT']],
                      ['status'=>'_ASSS3_1','statusChi'=>'异常_待更新','oprt'=>['_SAVE','_AUDIT']],
                      ['status'=>'_ASSS3_2','statusChi'=>'异常_待维修','oprt'=>['_SAVE','_SUBMIT']],
                      ['status'=>'_ASSS3_3','statusChi'=>'异常_遗失','oprt'=>['_SAVE','_APPROVE']],
                      ['status'=>'_ASSS4_1','statusChi'=>'停用_维修中','oprt'=>['_SAVE','_SUBMIT','_TRASH','_RESTORE']],
                      ['status'=>'_ASSS4_2','statusChi'=>'停用_封存','oprt'=>['_SAVE','_APPROVE','_TRASH','_RESTORE']],
                      ['status'=>'_ASSS4_3','statusChi'=>'停用_待销账','oprt'=>['_SAVE','_APPROVE','_TRASH','_RESTORE']],
                      ['status'=>'_ASSS5','statusChi'=>'销账','oprt'=>[]]
                    ];

//asset的操作数组,4类（CURD）10个
const conAssOprtArr=['_CREATE'=>'新增',
                    '_SUBMIT'=>'提交',
                    '_AUDIT'=>'审核',
                    '_APPROVE'=>'审批',
                    '_MAINTAIN'=>'维护',
                    '_SAVE'=>'保存',
                    '_TRASH'=>'回收站',
                    '_RESTORE'=>'还原',
                    '_READ'=>'查询',
                    '_DELETE'=>'永久删除',
                    ];
                    
//asset的权限与操作的对应关系（conAssAuthOprtArr）
const conAssAuthOprtArr=[
                      ['auth'=>'edit','oprt'=>['_CREATE','_SUBMIT','_SAVE','_READ','_TRASH','_RESTORE']],
                      ['auth'=>'audit','oprt'=>['_AUDIT','_SAVE','_READ','_TRASH','_RESTORE']],
                      ['auth'=>'approve','oprt'=>['_APPROVE','_SAVE','_READ','_DELETE']],
                      ['auth'=>'maintain','oprt'=>['_MAIINTAIN','_SAVE','_READ','_TRASH','_RESTORE']]
                    ];
                    


