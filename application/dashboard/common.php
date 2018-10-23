<?php
//asset的状态数组共7类14个已放入app的common.php中

//asset的状态与操作的对应关系（conAssStatusOprtArr），现状->可对现状进行的操作->操作后的状态
const conAssStatusOprtArr=[
                      ['status'=>'_ASSS0','statusChi'=>'*','oprt'=>['_UPDATE','_SUBMIT','_DELETE'],'nextStatus'=>['_SUBMIT'=>['新增_待验收','异常_待更新']]],
                      ['status'=>'_ASSS1_1','statusChi'=>'新增_待验收','oprt'=>['_UPDATE','_SUBMIT'],'nextStatus'=>['_SUBMIT'=>['待分配_初次验收合格','异常_待维修','异常_遗失']]],
                      ['status'=>'_ASSS1_2','statusChi'=>'待分配_初次验收合格','oprt'=>['_UPDATE','_AUDIT'],'nextStatus'=>['_AUDIT'=>['正常_折旧中','异常_待维修','异常_遗失']]],
                      ['status'=>'_ASSS1_3','statusChi'=>'待分配_维修验收合格','oprt'=>['_UPDATE','_AUDIT'],'nextStatus'=>['_AUDIT'=>['正常_折旧中','正常_折旧完','异常_待维修','异常_遗失']]],
                      ['status'=>'_ASSS2_1','statusChi'=>'正常_折旧中','oprt'=>['_UPDATE','_SUBMIT'],'nextStatus'=>['_SUBMIT'=>['异常_待维修','异常_遗失','异常_待更新']]],
                      ['status'=>'_ASSS2_2','statusChi'=>'正常_折旧完','oprt'=>['_UPDATE','_SUBMIT'],'nextStatus'=>['_SUBMIT'=>['异常_待维修','异常_遗失','异常_待更新']]],
                      ['status'=>'_ASSS3_1','statusChi'=>'异常_待更新','oprt'=>['_UPDATE','_AUDIT','_TRASH'],'nextStatus'=>['_AUDIT'=>['异常_待维修','异常_遗失','停用_维修中'],'_TRASH'=>['回收站']]],
                      ['status'=>'_ASSS3_2','statusChi'=>'异常_待维修','oprt'=>['_UPDATE','_AUDIT',],'nextStatus'=>['_AUDIT'=>['异常_遗失','停用_维修中','待分配_维修验收合格']]],
                      ['status'=>'_ASSS3_3','statusChi'=>'异常_遗失','oprt'=>['_UPDATE','_AUDIT',],'nextStatus'=>['_AUDIT'=>['异常_待维修','停用_维修中','停用_封存']]],
                      ['status'=>'_ASSS4_1','statusChi'=>'停用_维修中','oprt'=>['_UPDATE','_APPROVE','_TRASH'],'nextStatus'=>['_APPROVE'=>['待分配_维修验收合格','待销账_报废','待销账_遗失'],'_TRASH'=>['回收站']]],
                      ['status'=>'_ASSS4_2','statusChi'=>'停用_封存','oprt'=>['_UPDATE','_APPROVE','_TRASH'],'nextStatus'=>['_APPROVE'=>['待销账_报废','待销账_遗失','正常_折旧完'],'_TRASH'=>['回收站']]],
                      ['status'=>'_ASSS4_3','statusChi'=>'待销账_报废','oprt'=>['_UPDATE','_MAINTAIN'],'nextStatus'=>['_MAINTAIN'=>['销账']]],
                      ['status'=>'_ASSS4_4','statusChi'=>'待销账_遗失','oprt'=>['_UPDATE','_MAINTAIN'],'nextStatus'=>['_MAINTAIN'=>['销账']]],
                      ['status'=>'_ASSS5','statusChi'=>'销账','oprt'=>[],'nextStatus'=>[]],
                      ['status'=>'_ASSS6','statusChi'=>'回收站','oprt'=>['_RESTORE'],'nextStatus'=>['_RESTORE'=>['正常_折旧完','正常_折旧中','异常_待维修','异常_遗失','停用_维修中','停用_封存']]]
                    ];

//asset的权限与操作的对应关系（conAssAuthOprtArr）
const conAssAuthOprtArr=[
                      //部门资产管理员
                      ['auth'=>'edit','oprt'=>['_CREATE','_SUBMIT','_READ','_DELETE']],
                      //院资产管理员
                      ['auth'=>'audit','oprt'=>['_AUDIT','_READ','_TRASH','_RESTORE']],
                      //院资产管理负责人
                      ['auth'=>'approve','oprt'=>['_APPROVE','_READ']],
                      //院资产管理员
                      ['auth'=>'maintain','oprt'=>['_MAINTAIN','_READ','_TRASH','_RESTORE']]
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
                    '_READ'=>'查阅',
                    '_DELETE'=>'删除',
                    ];
                    
//asset的操作与操作后状态的对应关系（conAssStatusOprtArr）
const conAssOprtChangeStatusArr=[
                        ['oprt'=>'_CREATE','oprtChi'=>'新增','statusNow'=>['_ASSS0'=>'*'],'statusChangeTo'=>['新增_待验收']],
                        ['oprt'=>'_SUBMIT','oprtChi'=>'提交','statusNow'=>['_ASSS1_1'=>'新增_待验收','_ASSS2_1'=>'正常_折旧中','_ASSS2_2'=>'正常_折旧完'],'statusChangeTo'=>['待分配_初次验收合格','异常_待维修','异常_遗失']],
                        ['oprt'=>'_AUDIT','oprtChi'=>'审核','statusNow'=>['_ASSS1_2'=>'待分配_初次验收合格','_ASSS1_3'=>'待分配_维修验收合格'],'statusChangeTo'=>['正常_折旧中','正常_折旧完','停用_封存','停用_维修中']],
                        ['oprt'=>'_APPROVE','oprtChi'=>'审批','statusNow'=>[''],'statusChangeTo'=>[]],
                        ['oprt'=>'_MAINTAIN','oprtChi'=>'维护','statusNow'=>['_ASSS4_3'=>'待销账_报废','_ASSS4_4'=>'待销账_遗失'],'statusChangeTo'=>['销账']],
                        ['oprt'=>'_UPDATE','oprtChi'=>'更新','statusNow'=>[''],'statusChangeTo'=>[]],
                        ['oprt'=>'_TRASH','oprtChi'=>'回收','statusNow'=>['_ASSS3_1'=>'异常_待更新',],'statusChangeTo'=>['回收站']],
                        ['oprt'=>'_RESTORE','oprtChi'=>'还原','statusNow'=>['_ASSS6'=>'回收站'],'statusChangeTo'=>['正常_折旧完','正常_折旧中','异常_待维修','异常_遗失','停用_维修中','停用_封存']],
                        ['oprt'=>'_READ','oprtChi'=>'查阅','statusNow'=>[''],'statusChangeTo'=>[]],
                        ['oprt'=>'_DELETE','oprtChi'=>'删除','statusNow'=>['_ASSS0'=>'*','_ASSS1_1'=>'新增_待验收'],'statusChangeTo'=>[]]
                        ];
                        
const conA=[
                    '_ASSS3_2'=>'异常_待维修',
                    '_ASSS3_3'=>'异常_遗失',
                    //label-default，停用
                    '_ASSS4_1'=>'停用_维修中',
                    '_ASSS4_2'=>'停用_封存',
                   
                    //label-default，销账
                    '_ASSS5'=>'销账',
                    //label-danger
                    
                    ];


