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

//asset的状态与操作的对应关系（conAssStatusOprtArr）
const conAssStatusOprtArr=[
                      ['status'=>'*','statusChi'=>'*','oprt'=>['_UPDATE','_SUBMIT','_DELETE'],'nextStatus'=>[]],
                      ['status'=>'_ASSS0','statusChi'=>'新增_待验收','oprt'=>['_UPDATE','_SUBMIT'],'nextStatus'=>[]],
                      ['status'=>'_ASSS1_1','statusChi'=>'验收合格_待分配','oprt'=>['_UPDATE','_AUDIT'],'nextStatus'=>[]],
                      ['status'=>'_ASSS1_2','statusChi'=>'维修验收_待确认','oprt'=>['_UPDATE','_AUDIT'],'nextStatus'=>[]],
                      ['status'=>'_ASSS2_1','statusChi'=>'正常_折旧中','oprt'=>['_UPDATE','_SUBMIT'],'nextStatus'=>[]],
                      ['status'=>'_ASSS2_2','statusChi'=>'正常_折旧完','oprt'=>['_UPDATE','_SUBMIT'],'nextStatus'=>[]],
                      ['status'=>'_ASSS3_1','statusChi'=>'异常_待更新','oprt'=>['_UPDATE','_AUDIT','_TRASH'],'nextStatus'=>[]],
                      ['status'=>'_ASSS3_2','statusChi'=>'异常_待维修','oprt'=>['_UPDATE','_SUBMIT'],'nextStatus'=>[]],
                      ['status'=>'_ASSS3_3','statusChi'=>'异常_遗失','oprt'=>['_UPDATE','_APPROVE'],'nextStatus'=>[]],
                      ['status'=>'_ASSS4_1','statusChi'=>'停用_维修中','oprt'=>['_UPDATE','_SUBMIT','_TRASH'],'nextStatus'=>[]],
                      ['status'=>'_ASSS4_2','statusChi'=>'停用_封存','oprt'=>['_UPDATE','_APPROVE','_TRASH'],'nextStatus'=>[]],
                      ['status'=>'_ASSS4_3','statusChi'=>'停用_待销账','oprt'=>['_UPDATE','_APPROVE','_TRASH'],'nextStatus'=>[]],
                      ['status'=>'_ASSS4_4','statusChi'=>'遗失_待销账','oprt'=>['_UPDATE','_APPROVE','_TRASH'],'nextStatus'=>[]],
                      ['status'=>'_ASSS5','statusChi'=>'销账','oprt'=>[],'nextStatus'=>[]],
                      ['status'=>'_ASSS6','statusChi'=>'回收站','oprt'=>['_RESTORE'],'nextStatus'=>['正常_折旧完','正常_折旧中','异常_待更新','停用_封存',]]
                    ];

//asset的操作数组,4类（CURD）10个
const conAssOprtArr=['_CREATE'=>'新增',
                    '_SUBMIT'=>'提交',
                    '_AUDIT'=>'审核',
                    '_APPROVE'=>'审批',
                    '_MAINTAIN'=>'维护',
                    '_UPDATE'=>'更新',
                    '_TRASH'=>'回收',
                    '_RESTORE'=>'还原',
                    '_READ'=>'查询',
                    '_DELETE'=>'删除',
                    ];
                    
//asset的操作与操作后状态的对应关系（conAssStatusOprtArr）
const conAssOprtChangeStatusArr=[
                        ['oprt'=>'_CREATE','oprtChi'=>'新增','statusChangeTo'=>['新增_待验收']],
                        ['oprt'=>'_SUBMIT','oprtChi'=>'提交','statusChangeTo'=>['验收合格_待分配','维修验收_待确认','异常_待更新','异常_待维修','异常_遗失']],
                        ['oprt'=>'_AUDIT','oprtChi'=>'审核','statusChangeTo'=>['正常_折旧中','正常_折旧完','停用_封存','停用_维修中']],
                        ['oprt'=>'_APPROVE','oprtChi'=>'审批','statusChangeTo'=>['停用_待销账','遗失_待销账']],
                        ['oprt'=>'_MAINTAIN','oprtChi'=>'维护','statusChangeTo'=>['销账']],
                        ['oprt'=>'_UPDATE','oprtChi'=>'更新','statusChangeTo'=>[]],
                        ['oprt'=>'_TRASH','oprtChi'=>'回收','statusChangeTo'=>['回收站']],
                        ['oprt'=>'_RESTORE','oprtChi'=>'还原','statusChangeTo'=>['异常_待更新','异常_待维修','异常_遗失','正常_折旧中','正常_折旧完',]],
                        ['oprt'=>'_READ','oprtChi'=>'查询','statusChangeTo'=>[]],
                        ['oprt'=>'_DELETE','oprtChi'=>'删除','statusChangeTo'=>[]]
                        ];
                        
//asset的权限与操作的对应关系（conAssAuthOprtArr）
const conAssAuthOprtArr=[
                      //一般员工
                      ['auth'=>'edit','oprt'=>['_CREATE','_SUBMIT','_UPDATE','_READ','_TRASH','_RESTORE']],
                      //部门资产管理员
                      ['auth'=>'audit','oprt'=>['_AUDIT','_UPDATE','_READ','_TRASH','_RESTORE']],
                      //院资产管理部门负责人
                      ['auth'=>'approve','oprt'=>['_APPROVE','_UPDATE','_READ','_DELETE']],
                      //院资产管理员
                      ['auth'=>'maintain','oprt'=>['_MAIINTAIN','_UPDATE','_READ','_TRASH','_RESTORE']]
                    ];
                    


