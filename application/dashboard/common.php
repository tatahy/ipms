<?php
//issue的状态与操作的对应关系，实体->现状->可对现状进行的操作->操作后的状态
const conIssStatusOprtArr=[
        '_PAT'=>[
          ['status'=>'_PATS1','statusChi'=>'送审','oprt'=>[['name'=>''],['nextStatus'=>[]]]
            ],
          //['status'=>'_PATS1-1','statusChi'=>'填报中','oprt'=>['_UPDATE','_SUBMIT','_DELETE'],'nextStatus'=>['_SUBMIT'=>['_PATS2-1'=>'新增-专利申请']]],
          ['status'=>'_PATS1-1','statusChi'=>'填报中','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                ['name'=>'_DELETE','nextStatus'=>['display'=>1]],
                                                                ['name'=>'_SUBMIT','nextStatus'=>['_PATS2-1'=>'新增-专利申请']]]
            ],
          ['status'=>'_PATS1-2','statusChi'=>'审核完-待修改','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                      ['name'=>'_SUBMIT','nextStatus'=>['_PATS2-2'=>'送审-已修改']]]
            ],                                                         
          ['status'=>'_PATS1-3','statusChi'=>'审批完-需完善','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                      ['name'=>'_SUBMIT','nextStatus'=>['_PATS2-3'=>'送审-已完善']]]
            ],
                    //label-success，正常
          ['status'=>'_PATS2','statusChi'=>'审核','oprt'=>[['name'=>''],['nextStatus'=>[]]]
            ],
          ['status'=>'_PATS2-1','statusChi'=>'新增-专利申请','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                      ['name'=>'_AUDIT','nextStatus'=>['_PATS1-2'=>'审核完-待修改','_PATS3-3'=>'审核-不予支持','_PATS3-1'=>'审核-通过']]]
            ],
          ['status'=>'_PATS2-2','statusChi'=>'送审-已修改','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                    ['name'=>'_AUDIT','nextStatus'=>['_PATS1-2'=>'审核完-待修改','_PATS3-3'=>'审核-不予支持','_PATS3-1'=>'审核-通过']]]
            ],
          ['status'=>'_PATS2-3','statusChi'=>'送审-已完善','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                    ['name'=>'_AUDIT','nextStatus'=>['_PATS1-2'=>'审核完-待修改','_PATS3-3'=>'审核-不予支持','_PATS3-1'=>'审核-通过']]]
            ],
                    //label-warning，异常
          ['status'=>'_PATS3','statusChi'=>'审批','oprt'=>[['name'=>''],['nextStatus'=>[]]]
            ],
          ['status'=>'_PATS3-1','statusChi'=>'审核-通过','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                  ['name'=>'_APPROVE','nextStatus'=>['_PATS4-1'=>'审批-批准','_PATS4-END4'=>'审批-否决','_PATS1-3'=>'审批完-需完善']]]
            ],
          ['status'=>'_PATS3-2','statusChi'=>'新增-续费申请','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                      ['name'=>'_APPROVE','nextStatus'=>['_PATS4-1'=>'审批-批准','_PATS4-END5'=>'续费-放弃']]]
            ],
          ['status'=>'_PATS3-3','statusChi'=>'审核-不予支持','oprt'=>[['name'=>'_APPROVE','nextStatus'=>['_PATS4-1'=>'审批-批准','_PATS4-END4'=>'审批-否决','_PATS1-3'=>'审批完-需完善']]]
            ],
                    //label-default，停用
          ['status'=>'_PATS4','statusChi'=>'执行','oprt'=>[['name'=>''],['nextStatus'=>[]]]
            ],
          ['status'=>'_PATS4-1','statusChi'=>'审批-批准','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                  ['name'=>'_EXECUTE','nextStatus'=>['_PATS4-2'=>'执行中','_PATS4-3'=>'申报-复核']]]
            ],
          ['status'=>'_PATS4-2','statusChi'=>'执行中','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                ['name'=>'_EXECUTE','nextStatus'=>['_PATS4-3'=>'申报-复核']]]
            ],
          ['status'=>'_PATS4-3','statusChi'=>'申报-复核','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                  ['name'=>'_MAINTAIN','nextStatus'=>['_PATS4-4'=>'申报-提交','_PATS4-5'=>'申报-修改']]]
            ],
          ['status'=>'_PATS4-4','statusChi'=>'申报-提交','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                  ['name'=>'_MAINTAIN','nextStatus'=>['_PATS4-END1'=>'申报-授权','_PATS4-END2'=>'申报-驳回','_PATS4-5'=>'申报-修改']]]
            ],
          ['status'=>'_PATS4-5','statusChi'=>'申报-修改','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                  ['name'=>'_EXECUTE','nextStatus'=>['_PATS4-3'=>'申报-复核']]]
            ],
          ['status'=>'_PATS4-6','statusChi'=>'续费-批准','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                  ['name'=>'_MAINTAIN','nextStatus'=>['_PATS4-7'=>'续费-提交']]]
            ],
          ['status'=>'_PATS4-7','statusChi'=>'续费-提交','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                  ['name'=>'_MAINTAIN','nextStatus'=>['_PATS4-END3'=>'续费-授权']]]
            ],
          ['status'=>'_PATS4-END1','statusChi'=>'申报-授权','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                      ['name'=>'_MAINTAIN','nextStatus'=>['_PATS_END'=>'完结']]]
            ],
          ['status'=>'_PATS4-END2','statusChi'=>'申报-驳回','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                      ['name'=>'_MAINTAIN','nextStatus'=>['_PATS_END'=>'完结']]]
            ],
          ['status'=>'_PATS4-END3','statusChi'=>'续费-授权','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                      ['name'=>'_MAINTAIN','nextStatus'=>['_PATS_END'=>'完结']]]
            ],
          ['status'=>'_PATS4-END4','statusChi'=>'审批-否决','oprt'=>[['name'=>'_MAINTAIN','nextStatus'=>['_PATS_END'=>'完结']]]
            ],
          ['status'=>'_PATS4-END5','statusChi'=>'续费-放弃','oprt'=>[['name'=>'_MAINTAIN','nextStatus'=>['_PATS_END'=>'完结']]]
            ],
                    //label-default，销账
          ['status'=>'_PATS_END','statusChi'=>'完结','oprt'=>[['name'=>'','nextStatus'=>[]]]
            ]
          //['status'=>'_PATS_END','statusChi'=>'完结','oprt'=>['',''],'nextStatus'=>[''=>[],''=>[]]],
        ],
        '_PRO'=>[
          ['status'=>'_PROS1','statusChi'=>'送审','oprt'=>[['name'=>''],['nextStatus'=>[]]]
            ],
          ['status'=>'_PROS1-1','statusChi'=>'','oprt'=>['',''],'nextStatus'=>[''=>[],''=>[]]],
                    //label-success，审核
          ['status'=>'_PROS2','statusChi'=>'审核','oprt'=>['',''],'nextStatus'=>[''=>[],''=>[]]],
          ['status'=>'_PROS2-1','statusChi'=>'','oprt'=>['',''],'nextStatus'=>[''=>[],''=>[]]],
          ['status'=>'_PROS2-2','statusChi'=>'送审-已修改','oprt'=>['',''],'nextStatus'=>[''=>[],''=>[]]],
          ['status'=>'_PROS2-3','statusChi'=>'送审-已完善','oprt'=>['',''],'nextStatus'=>[''=>[],''=>[]]],
          //['status'=>'_PROS2-END','statusChi'=>'审核-不予支持','oprt'=>['',''],'nextStatus'=>[''=>[],''=>[]]],
                    //label-warning，审批
          ['status'=>'_PROS3','statusChi'=>'审批','oprt'=>['',''],'nextStatus'=>[''=>[],''=>[]]],
          ['status'=>'_PROS3-1','statusChi'=>'审核-通过','oprt'=>['',''],'nextStatus'=>[''=>[],''=>[]]],
          ['status'=>'_PROS3-2','statusChi'=>'审核-不予支持','oprt'=>['',''],'nextStatus'=>[''=>[],''=>[]]],
          //['status'=>'_PROS3-END','statusChi'=>'审批-否决','oprt'=>['',''],'nextStatus'=>[''=>[],''=>[]]],
                    //label-primary，执行
          ['status'=>'_PROS4','statusChi'=>'执行','oprt'=>['',''],'nextStatus'=>[''=>[],''=>[]]],
          ['status'=>'_PROS4-1','statusChi'=>'审批-批准','oprt'=>['',''],'nextStatus'=>[''=>[],''=>[]]],
          ['status'=>'_PROS4-END1','statusChi'=>'审批-否决','oprt'=>['',''],'nextStatus'=>[''=>[],''=>[]]],
                    //label-default，完结
          ['status'=>'_PROS_END','statusChi'=>'完结','oprt'=>[['name'=>'','nextStatus'=>[]]]
            ]          
        ],
        '_THE'=>[
          ['status'=>'_THES1','statusChi'=>'送审','oprt'=>[['name'=>''],['nextStatus'=>[]]]
            ],
          ['status'=>'_THES1-1','statusChi'=>'填报中','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                ['name'=>'_SUBMIT','nextStatus'=>['_THES2-1'=>'新增-论文发表申请']],
                                                                ['name'=>'_DELETE','nextStatus'=>['显示'=>1]]]
            ],
          ['status'=>'_THES1-2','statusChi'=>'审核完-待修改','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                      ['name'=>'_SUBMIT','nextStatus'=>['_THES2-2'=>'送审-已修改']]]
            ],
          ['status'=>'_THES1-3','statusChi'=>'审批完-需完善','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                      ['name'=>'_SUBMIT','nextStatus'=>['_THES2-3'=>'送审-已完善']]]
            ],
                    //label-success，审核
          ['status'=>'_THES2','statusChi'=>'审核','oprt'=>[['name'=>''],['nextStatus'=>[]]]
            ],
          ['status'=>'_THES2-1','statusChi'=>'新增-论文发表申请','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                          ['name'=>'_AUDIT','nextStatus'=>['_THES3-1'=>'审核-通过','_THES3-2'=>'审核-不予支持','_THES1-2'=>'审核完-待修改']]]
            ],
          ['status'=>'_THES2-2','statusChi'=>'送审-已修改','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                    ['name'=>'_AUDIT','nextStatus'=>['_THES3-1'=>'审核-通过','_THES3-2'=>'审核-不予支持','_THES1-2'=>'审核完-待修改']]]
            ],
          ['status'=>'_THES2-3','statusChi'=>'送审-已完善','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                    ['name'=>'_AUDIT','nextStatus'=>['_THES3-1'=>'审核-通过','_THES3-2'=>'审核-不予支持','_THES1-2'=>'审核完-待修改']]]
            ],
          //['status'=>'_THES2-END','statusChi'=>'审核-不予支持','oprt'=>[['name'=>'_APPROVE','nextStatus'=>['_THES4-1'=>'审批-批准','_THES3-END'=>'审批-否决','_THES1-3'=>'审批完-需完善']]]
//            ],
                    //label-warning，审批
          ['status'=>'_THES3','statusChi'=>'审批','oprt'=>[['name'=>''],['nextStatus'=>[]]]
            ],
          ['status'=>'_THES3-1','statusChi'=>'审核-通过','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                  ['name'=>'_APPROVE','nextStatus'=>['_THES4-1'=>'审批-批准','_THES4-END3'=>'审批-否决','_THES1-3'=>'审批完-需完善']]]
            ],
          ['status'=>'_THES3-2','statusChi'=>'审核-不予支持','oprt'=>[['name'=>'_APPROVE','nextStatus'=>['_THES4-1'=>'审批-批准','_THES4-END3'=>'审批-否决','_THES1-3'=>'审批完-需完善']]]
            ],
          //['status'=>'_THES3-END','statusChi'=>'审批-否决','oprt'=>[['name'=>'_MAINTAIN','nextStatus'=>['_THES_END'=>'完结']]]
           // ],
                    //label-primary，执行
          ['status'=>'_THES4','statusChi'=>'执行','oprt'=>[['name'=>''],['nextStatus'=>[]]]
            ],
          ['status'=>'_THES4-1','statusChi'=>'审批-批准','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                  ['name'=>'_EXECUTE','nextStatus'=>['_THES4-2'=>'执行中','_THES4-8'=>'投稿-复核']]]
            ],
          ['status'=>'_THES4-2','statusChi'=>'执行中','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                ['name'=>'_EXECUTE','nextStatus'=>['_THES4-8'=>'投稿-复核']]]
            ],
          ['status'=>'_THES4-3','statusChi'=>'复核-修改','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                  ['name'=>'_EXECUTE','nextStatus'=>['_THES4-8'=>'投稿-复核']]]
            ],
          ['status'=>'_THES4-4','statusChi'=>'复核-通过','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                  ['name'=>'_EXECUTE','nextStatus'=>['_THES4-5'=>'投稿-提交']]]
            ],
          ['status'=>'_THES4-5','statusChi'=>'投稿-提交','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                  ['name'=>'_EXECUTE','nextStatus'=>['_THES4-6'=>'投稿-修改','_THES4-7'=>'投稿-接受','_THES4-END1'=>'投稿-发表','_THES4-END2'=>'投稿-被拒']]]
            ],
          ['status'=>'_THES4-6','statusChi'=>'投稿-修改','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                  ['name'=>'_EXECUTE','nextStatus'=>['_THES4-7'=>'投稿-接受','_THES4-END1'=>'投稿-发表','_THES4-END2'=>'投稿-被拒']]]
            ],
          ['status'=>'_THES4-7','statusChi'=>'投稿-接受','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                  ['name'=>'_EXECUTE','nextStatus'=>['_THES4-END1'=>'投稿-发表','_THES4-END2'=>'投稿-被拒']]]
            ],
          ['status'=>'_THES4-8','statusChi'=>'投稿-复核','oprt'=>[['name'=>'_UPDATE','nextStatus'=>[]],
                                                                  ['name'=>'_MAINTAIN','nextStatus'=>['_THES4-3'=>'复核-修改','_THES4-4'=>'复核-通过']]]
            ],
          ['status'=>'_THES4-END1','statusChi'=>'投稿-发表','oprt'=>[['name'=>'_MAINTAIN','nextStatus'=>['_THES_END'=>'完结']]]
            ],
          ['status'=>'_THES4-END2','statusChi'=>'投稿-被拒','oprt'=>[['name'=>'_MAINTAIN','nextStatus'=>['_THES_END'=>'完结']]]
            ],
          ['status'=>'_THES4-END3','statusChi'=>'审批-否决','oprt'=>[['name'=>'_MAINTAIN','nextStatus'=>['_THES_END'=>'完结']]]
            ],
                    //label-default，完结
          ['status'=>'_THES_END','statusChi'=>'完结','oprt'=>[['name'=>''],['nextStatus'=>[]]]
            ]
        ],
];

//issue有关的配置参数
const conIssConf=['_PAT'=>[//issStatus数组
                              'status'=>conIssPatStatusArr,
                              //iss实体中文名
                              'entNameChi'=>'专利',
                              //issue模型中关联属性/方法
                              'relMethod'=>'pat_list',
                              //关联对象Status数组
                              'relStatus'=>conPatStatusArr,
                              //关联对象type数组
                              'relType'=>conPatTypeArr,
                              //关联对象模型名称
                              'relEntModelName'=>'Patinfo',
                              //状态与操作的关系
                              'statusOprt'=>conIssStatusOprtArr['_PAT'],
                              //权限与操作的关系
                              'authOprt'=>conIssAuthOprtArr['_PAT'],
                           ],   
                    '_THE'=>['status'=>conIssTheStatusArr,
                              'entNameChi'=>'论文',
                              'relMethod'=>'the_list',
                              'relStatus'=>conTheStatusArr,
                              'relType'=>conTheTypeArr,
                              'relEntModelName'=>'Theinfo',
                              'statusOprt'=>conIssStatusOprtArr['_THE'],
                              'authOprt'=>conIssAuthOprtArr['_THE']
                            ],
                    '_PRO'=>['status'=>conIssProStatusArr,
                              'entNameChi'=>'项目',
                              'relMethod'=>'pro_list',
                              'relStatus'=>conProStatusArr,
                              'relType'=>conProTypeArr,
                              'relEntModelName'=>'Proinfo',
                              'statusOprt'=>conIssStatusOprtArr['_PRO'],
                              'authOprt'=>conIssAuthOprtArr['_PRO']
                            ],
                    //关联对象共有的字段名称
                    'relEntTblCommonFields'=>['id','topic','type','status'],
                    //关联对象共有的字段名称与前端变量名的对应关系
                    'relTblFieldsFEName'=>['issEntName'=>'topic','issEntType'=>'type','issEntStatus'=>'status']
                    ];

//asset的状态数组共7类14个已放入app的common.php中

//asset的状态与操作的对应关系（conAssStatusOprtArr），现状->可对现状进行的操作->操作后的状态
const conAssStatusOprtArr=[
                      ['status'=>'_ASSS0','statusChi'=>'*','oprt'=>['_CREATE'],'nextStatus'=>['_CREATE'=>['填报中','新增_待验收']]],
                      //['status'=>'_ASSS0','statusChi'=>'*','oprt'=>['_UPDATE','_SUBMIT','_DELETE'],'nextStatus'=>['_SUBMIT'=>['新增_待验收']]],
                      ['status'=>'_ASSS1_1','statusChi'=>'填报中','oprt'=>['_UPDATE','_SUBMIT','_DELETE'],'nextStatus'=>['_SUBMIT'=>['新增_待验收'],'_DELETE'=>[]]],
                      ['status'=>'_ASSS1_2','statusChi'=>'新增_待验收','oprt'=>['_UPDATE','_SUBMIT'],'nextStatus'=>['_SUBMIT'=>['待分配_初次验收合格','异常_待维修','异常_遗失']]],
                      ['status'=>'_ASSS1_3','statusChi'=>'待分配_初次验收合格','oprt'=>['_UPDATE','_AUDIT'],'nextStatus'=>['_AUDIT'=>['正常_折旧中','异常_待维修','异常_遗失','停用_维修中','停用_封存']]],
                      ['status'=>'_ASSS1_4','statusChi'=>'待分配_维修验收合格','oprt'=>['_UPDATE','_AUDIT'],'nextStatus'=>['_AUDIT'=>['正常_折旧中','正常_折旧完','异常_待维修','异常_遗失','停用_维修中','停用_封存']]],
                      ['status'=>'_ASSS2_1','statusChi'=>'正常_折旧中','oprt'=>['_UPDATE','_SUBMIT'],'nextStatus'=>['_SUBMIT'=>['异常_待维修','异常_遗失','异常_待审核']]],
                      ['status'=>'_ASSS2_2','statusChi'=>'正常_折旧完','oprt'=>['_UPDATE','_SUBMIT'],'nextStatus'=>['_SUBMIT'=>['异常_待维修','异常_遗失','异常_待审核']]],
                      ['status'=>'_ASSS3_1','statusChi'=>'异常_待审核','oprt'=>['_UPDATE','_AUDIT','_TRASH'],'nextStatus'=>['_AUDIT'=>['异常_待维修','异常_遗失','停用_维修中','停用_遗失','停用_封存','正常_折旧完','正常_折旧中'],'_TRASH'=>['回收站']]],
                      ['status'=>'_ASSS3_2','statusChi'=>'异常_待维修','oprt'=>['_UPDATE','_AUDIT',],'nextStatus'=>['_AUDIT'=>['异常_遗失','停用_维修中','待分配_维修验收合格']]],
                      ['status'=>'_ASSS3_3','statusChi'=>'异常_遗失','oprt'=>['_UPDATE','_AUDIT',],'nextStatus'=>['_AUDIT'=>['异常_待维修','停用_维修中','停用_遗失']]],
                      ['status'=>'_ASSS4_1','statusChi'=>'停用_维修中','oprt'=>['_UPDATE','_APPROVE','_TRASH'],'nextStatus'=>['_APPROVE'=>['待分配_维修验收合格','待销账_报废','待销账_遗失'],'_TRASH'=>['回收站']]],
                      ['status'=>'_ASSS4_2','statusChi'=>'停用_封存','oprt'=>['_UPDATE','_APPROVE','_TRASH'],'nextStatus'=>['_APPROVE'=>['待销账_报废','待销账_遗失','正常_折旧完'],'_TRASH'=>['回收站']]],
                      ['status'=>'_ASSS4_3','statusChi'=>'停用_遗失','oprt'=>['_UPDATE','_APPROVE'],'nextStatus'=>['_APPROVE'=>['待销账_遗失']]],
                      ['status'=>'_ASSS4_4','statusChi'=>'待销账_报废','oprt'=>['_UPDATE','_MAINTAIN'],'nextStatus'=>['_MAINTAIN'=>['销账']]],
                      ['status'=>'_ASSS4_5','statusChi'=>'待销账_遗失','oprt'=>['_UPDATE','_MAINTAIN'],'nextStatus'=>['_MAINTAIN'=>['销账']]],
                      ['status'=>'_ASSS5','statusChi'=>'销账','oprt'=>[],'nextStatus'=>[]],
                      ['status'=>'_ASSS6','statusChi'=>'回收站','oprt'=>['_RESTORE'],'nextStatus'=>['_RESTORE'=>['正常_折旧完','正常_折旧中','异常_待维修','异常_遗失','停用_维修中','停用_封存','停用_遗失']]]
                    ];

//asset的权限与操作的对应关系（conAssAuthOprtArr）
const conAssAuthOprtArr=[
                      //普通员工
                      ['auth'=>'read','oprt'=>['_READ']],
                      //部门资产管理员
                      ['auth'=>'edit','oprt'=>['_CREATE','_SUBMIT','_READ','_DELETE']],
                      //院资产管理员
                      ['auth'=>'audit','oprt'=>['_AUDIT','_READ']],
                      //院资产管理负责人
                      ['auth'=>'approve','oprt'=>['_APPROVE','_READ']],
                      //院资产管理员
                      ['auth'=>'maintain','oprt'=>['_MAINTAIN','_READ','_TRASH','_RESTORE']]
                    ];
                 
//asset的操作与操作后状态的对应关系（conAssStatusOprtArr）
const conAssOprtChangeStatusArr=[
                        ['oprt'=>'_CREATE','oprtChi'=>'新增','statusNow'=>['_ASSS0'=>'*'],'statusChangeTo'=>['新增_待验收']],
                        ['oprt'=>'_SUBMIT','oprtChi'=>'送审','statusNow'=>['_ASSS1_1'=>'新增_待验收','_ASSS2_1'=>'正常_折旧中','_ASSS2_2'=>'正常_折旧完'],'statusChangeTo'=>['待分配_初次验收合格','异常_待维修','异常_遗失']],
                        ['oprt'=>'_AUDIT','oprtChi'=>'审核','statusNow'=>['_ASSS1_2'=>'待分配_初次验收合格','_ASSS1_3'=>'待分配_维修验收合格'],'statusChangeTo'=>['正常_折旧中','正常_折旧完','停用_封存','停用_维修中']],
                        ['oprt'=>'_APPROVE','oprtChi'=>'审批','statusNow'=>[''],'statusChangeTo'=>['待销账_报废','待销账_遗失']],
                        ['oprt'=>'_MAINTAIN','oprtChi'=>'维护','statusNow'=>['_ASSS4_3'=>'待销账_报废','_ASSS4_4'=>'待销账_遗失'],'statusChangeTo'=>['销账']],
                        ['oprt'=>'_UPDATE','oprtChi'=>'更新','statusNow'=>[''],'statusChangeTo'=>[]],
                        ['oprt'=>'_TRASH','oprtChi'=>'回收','statusNow'=>['_ASSS3_1'=>'异常_待审核',],'statusChangeTo'=>['回收站']],
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

/**
     * 得到$parentArr的子集
     * @param  Array $parentArr 一维关联数组，无重复值。
     * @param  Array $clueArr, 一维索引数组，可能有重复值。
     * @param  String $type, 'KEY'| 'VALUE'，指定$clueArr的类型，默认为'KEY'
     * @return Array $childArr 返回的一维关联数组，是$parentArr的子集，包含无重复值的$clueArr。
     */
function get_child_array($parentArr,$clueArr=[],$type='KEY')
{
  $childArr=[];
  $childArrKeys=[];
  $childArrValues=[];
  $cArr=[];
  if(count($clueArr)==0 ){
    return 'Error: clueArr is empty!';
  }
        
  if($type=='KEY'){
    //地址引用
    $childArrKeys=&$clueArr;
    $childArrValues=&$cArr;    
  }else{
    //交换键值
    $parentArr=array_flip($parentArr);
    //地址引用
    $childArrKeys=&$cArr;
    $childArrValues=&$clueArr;    
  }
        
  //去重
  $clueArr=array_unique($clueArr);

  //由$clueArr作为key数组在$parentArr中寻找其对应的value数组,去掉在$parentArr中不存在的项        
  foreach($clueArr as $key=>$val){
    foreach($parentArr as $k=>$v){
      if($val==$k){
        $cArr[$key]=$v;
      }
    }
    //$key是数字索引，$val是$parentArr中的键名
    //if(array_key_exists($val,$parentArr)){
//      $cArr[$key]=$parentArr[$val];
//    }else{
//      $cArr[$key]='';
//      unset($cArr[$key]);
//      unset($clueArr[$key]);
//    }
  }
        
  $childArr=array_combine($childArrKeys,$childArrValues);
  //以键名进行升序排序，保持键值关系
  ksort($childArr);
  return $childArr;
}
