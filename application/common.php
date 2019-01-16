<?php
/**
 * @author TATA
 * @copyright 2018
 * 应用公共文件
 * 所有的公共函数都以"fn"开头，再遵循下划线小写字母命名法，fn_xx_yy_zz。
 * 所有的公共常量都以"con"开头，再遵循驼峰命名法。
 */

//定义需要进行权限管理的模块/实体及其属性，方便使用及横向和纵向的扩展
const conAuthEntArr=[
        '_ISS-_PAT'=>[
          'chi'=>'专利事务',
          'rank'=>'common',
          'auth'=>[
            'edit'=>['chi'=>'申报','val'=>0],
            'audit'=>['chi'=>'审核','val'=>0],
            'review'=>['chi'=>'复核','val'=>0],
            'approve'=>['chi'=>'审批','val'=>0],
            'execute'=>['chi'=>'执行','val'=>0],
            'maintain'=>['chi'=>'维护','val'=>0]
          ],
          #所有权限都为0，所以'visible'=>false
          'visible'=>false
//          'status'=>[]
        ],
        '_ISS-_THE'=>[
          'chi'=>'论文事务',
          'rank'=>'common',
          'auth'=>[
            'edit'=>['chi'=>'申报','val'=>0],
            'audit'=>['chi'=>'审核','val'=>0],
            'review'=>['chi'=>'复核','val'=>0],
            'approve'=>['chi'=>'审批','val'=>0],
            'execute'=>['chi'=>'执行','val'=>0],
            'maintain'=>['chi'=>'维护','val'=>0]
          ],
          'visible'=>false
        ],
        '_ISS-_PRO'=>[
          'chi'=>'项目事务',
          'rank'=>'common',
          'auth'=>[
            'edit'=>['chi'=>'申报','val'=>0],
            'audit'=>['chi'=>'审核','val'=>0],
            'review'=>['chi'=>'复核','val'=>0],
            'approve'=>['chi'=>'审批','val'=>0],
            'execute'=>['chi'=>'执行','val'=>0],
            'maintain'=>['chi'=>'维护','val'=>0]
          ],
          'visible'=>false
        ],
        '_PAT'=>[
          'chi'=>'专利',
          'rank'=>'common',
          'auth'=>[
            'edit'=>['chi'=>'撰写','val'=>0],
            'audit'=>['chi'=>'审核','val'=>0],
            'approve'=>['chi'=>'审批','val'=>0],
            'execute'=>['chi'=>'执行','val'=>0],
            'maintain'=>['chi'=>'维护','val'=>0]
          ],
          'visible'=>false
        ],
        '_THE'=>[
          'chi'=>'论文',
          'rank'=>'common',
          'auth'=>[
            'edit'=>['chi'=>'撰写','val'=>0],
            'audit'=>['chi'=>'审核','val'=>0],
            'approve'=>['chi'=>'审批','val'=>0],
            'execute'=>['chi'=>'执行','val'=>0],
            'maintain'=>['chi'=>'维护','val'=>0]
          ],
          'visible'=>false
        ],
        '_PRO'=>[
          'chi'=>'项目',
          'rank'=>'important',
          'auth'=>[
            'edit'=>['chi'=>'撰写','val'=>0],
            'audit'=>['chi'=>'审核','val'=>0],
            'approve'=>['chi'=>'审批','val'=>0],
            'execute'=>['chi'=>'执行','val'=>0],
            'maintain'=>['chi'=>'维护','val'=>0]
          ],
          'visible'=>false
        ],
        '_ATT'=>[
          'chi'=>'附件',
          'rank'=>'common',
          'auth'=>[
            'upload'=>['chi'=>'上传','val'=>0],
            'download'=>['chi'=>'下载','val'=>0],
            'erase'=>['chi'=>'删除','val'=>0],
            'move'=>['chi'=>'移动','val'=>0],
            'copy'=>['chi'=>'复制','val'=>0]
          ],
          'visible'=>false
        ],
        '_ASS'=>[
          'chi'=>'固定资产',
          'rank'=>'important',
          'auth'=>[
            'read'=>['chi'=>'查阅','val'=>0],
            'edit'=>['chi'=>'申报','val'=>0],
            'audit'=>['chi'=>'审核','val'=>0],
            'approve'=>['chi'=>'审批','val'=>0],
            'maintain'=>['chi'=>'维护','val'=>0]
          ],
          'visible'=>false
        ],
        '_ADMIN'=>[
          'chi'=>'系统管理',
          'rank'=>'critical',
          'auth'=>['enable'=>['chi'=>'有','val'=>0]],
          'visible'=>false
        ]
];

//patent的类型数组
const conPatTypeArr=[
                    '_PATT1'=>'发明专利',
                    '_PATT2'=>'实用新型专利',
                    '_PATT3'=>'外观设计专利',
                    '_PATT4'=>'软件版权专利',
                    '_PATT5'=>'著作权专利',
                    '_PATT6'=>'集成电路专利',
                    '_PATT7'=>'其他',
                    ];
//patent的状态数组
const conPatStatusArr=[
                    '_PATS1'=>'填报',
                    '_PATS2'=>'内审',
                    '_PATS2-1'=>'内审中',
                    '_PATS2-2'=>'内审-审核通过',
                    '_PATS2-3'=>'内审-修改',
                    '_PATS2-END1'=>'内审-批准申报',
                    '_PATS2-END2'=>'内审-否决',
                    '_PATS3'=>'申报',
                    '_PATS3-1'=>'申报中',
                    '_PATS3-2'=>'申报-修改',
                    '_PATS3-END1'=>'申报-授权',
                    '_PATS3-END2'=>'申报-驳回',
                    '_PATS4'=>'续费',
                    '_PATS4-1'=>'续费中',
                    '_PATS4-2'=>'续费-同意续费',
                    '_PATS4-END1'=>'续费-放弃',
                    '_PATS4-END2'=>'续费-授权',
                    '_PATS5'=>'超期无效',
                    ];
                    
//thesis的类型数组
const conTheTypeArr=[
                    '_THET1'=>'市级出版物',
                    '_THET2'=>'省级出版物',
                    '_THET3'=>'国家级出版物',
                    '_THET4'=>'境外出版物',
                    '_THET5'=>'会议文集',

                    ];
//thesis的状态数组
const conTheStatusArr=[
                    '_THES1'=>'拟发表',
                    '_THES2'=>'投稿',
                    '_THES3'=>'收录',
                    '_THES4'=>'发表',
                    '_THES5'=>'拒稿',
               
                    ];
//projec的类型数组
const conProTypeArr=[
                    '_PROT1'=>'市级',
                    '_PROT2'=>'省级',
                    '_PROT3'=>'国家级',
                    '_PROT4'=>'客户委托',
                    '_PROT5'=>'外包',
                    '_PROT6'=>'自研',

                    ];
//thesis的状态数组
const conProStatusArr=[
                    '_PROS1'=>'申报',
                    '_PROS1-1'=>'申报-准备',
                    '_PROS1-2'=>'申报-提交',
                    '_PROS1-END1'=>'申报-受理',
                    '_PROS1-END2'=>'申报-驳回',
                    '_PROS2'=>'立项',
                    '_PROS3'=>'在研',
                    '_PROS4'=>'验收',
                    '_PROS5'=>'结题',
                    '_PROS6'=>'终止',
               
                    ];
//attachment的类型数组
const conAttTypeArr=[
                    '_ATTT1'=>'申请',
                    '_ATTT2'=>'请示',
                    '_ATTT3'=>'报告',
                    '_ATTT3-1'=>'经费报告',
                    '_ATTT3-2'=>'技术报告',
                    '_ATTT4'=>'合同',
                    '_ATTT4-1'=>'技术合同',
                    '_ATTT4-2'=>'商务合同',
                    '_ATTT4-3'=>'项目合同',
                    '_ATTT5'=>'协议',
                    '_ATTT5-1'=>'技术协议',
                    '_ATTT5-2'=>'合作协议',
                    '_ATTT5'=>'说明',

                    ];
//attachment的依附对象
const conAttObjectArr=[
                    '_ATTO1'=>'_ISS',
                    '_ATTO2'=>'_PAT',
                    '_ATTO3'=>'_PRO',
                    '_ATTO4'=>'_THE',
                    
                    ];

//asset的状态数组大类7类
const conAssTypeArr=[
                    '_ASSS_USUAL'=>'除"回收站"以外的其它5类状态',
                    '_ASSS1'=>'待定',
                    '_ASSS2'=>'正常',
                    '_ASSS3'=>'异常',
                    '_ASSS4'=>'停用',
                    '_ASSS5'=>'销账',
                    '_ASSS6'=>'回收站'
                    ];
//asset的状态数组细分共7类17个
const conAssStatusArr=[//除‘回收站’以外的其它状态
                    '_ASSS0'=>'*',
                    //label-info，待定
                    '_ASSS1'=>'待定',
                    '_ASSS1_1'=>'填报中',
                    '_ASSS1_2'=>'新增_待验收',
                    '_ASSS1_3'=>'待分配_初次验收合格',
                    '_ASSS1_4'=>'待分配_维修验收合格',
                    //label-success，正常
                    '_ASSS2'=>'正常',
                    '_ASSS2_1'=>'正常_折旧中',
                    '_ASSS2_2'=>'正常_折旧完',
                    //label-warning，异常
                    '_ASSS3'=>'异常',
                    '_ASSS3_1'=>'异常_待审核',
                    '_ASSS3_2'=>'异常_待维修',
                    '_ASSS3_3'=>'异常_遗失',
                    //label-default，停用
                    '_ASSS4'=>'停用',
                    '_ASSS4_1'=>'停用_维修中',
                    '_ASSS4_2'=>'停用_封存',
                    '_ASSS4_3'=>'停用_遗失',
                    '_ASSS4_4'=>'待销账_报废',
                    '_ASSS4_5'=>'待销账_遗失',
                    //label-default，销账
                    '_ASSS5'=>'销账',
                    //label-danger
                    '_ASSS6'=>'回收站'
                    ];

//asset的状态标志数组共7类15个，要发送给前端决定label标签类型
const conAssStatusLabelArr=[
                    '_ASSS0'=>'info',
                    //label-info
                    '_ASSS1'=>'info',
                    '_ASSS1_1'=>'info',
                    '_ASSS1_2'=>'info',
                    '_ASSS1_3'=>'info',
                    '_ASSS1_4'=>'info',
                    //label-success
                    '_ASSS2'=>'success',
                    '_ASSS2_1'=>'success',
                    '_ASSS2_2'=>'success',
                    //label-warning
                    '_ASSS3'=>'warning',
                    '_ASSS3_1'=>'warning',
                    '_ASSS3_2'=>'warning',
                    '_ASSS3_3'=>'warning',
                    //label-default
                    '_ASSS4'=>'default',
                    '_ASSS4_1'=>'default',
                    '_ASSS4_2'=>'default',
                    '_ASSS4_3'=>'default',
                    '_ASSS4_4'=>'default',
                    '_ASSS4_5'=>'default',
                    '_ASSS5'=>'default',
                    //label-danger
                    '_ASSS6'=>'danger'
                    ];                    

//asset的操作数组,4类（CURD）10个
const conAssOprtArr=['_CREATE'=>'新增',
                    '_SUBMIT'=>'送审',
                    '_AUDIT'=>'审核',
                    '_APPROVE'=>'审批',
                    '_MAINTAIN'=>'维护',
                    '_UPDATE'=>'更新',
                    '_TRASH'=>'回收',
                    '_RESTORE'=>'还原',
                    '_READ'=>'查阅',
                    '_DELETE'=>'删除',
                    ];
                    
const conIssAuthArr=[
        '_PAT'=>['edit'=>0,'audit'=>0,'review'=>0,'approve'=>0,'maintain'=>0
          //['name'=>'edit','value'=>0],
//          ['name'=>'audit','value'=>0],
//          ['name'=>'review','value'=>0],
//          ['name'=>'approve','value'=>0],
//          ['name'=>'maintain','value'=>0],
        ],
        '_PRO'=>['edit'=>0,'audit'=>0,'review'=>0,'approve'=>0,'maintain'=>0
          //['name'=>'edit','value'=>0],
//          ['name'=>'audit','value'=>0],
//          ['name'=>'review','value'=>0],
//          ['name'=>'approve','value'=>0],
//          ['name'=>'maintain','value'=>0],
        ],
        '_THE'=>['edit'=>0,'audit'=>0,'review'=>0,'approve'=>0,'maintain'=>0
          //['name'=>'edit','value'=>0],
//          ['name'=>'audit','value'=>0],
//          ['name'=>'review','value'=>0],
//          ['name'=>'approve','value'=>0],
//          ['name'=>'maintain','value'=>0],
        ],
];

//issue的操作数组,4类（CURD）10个
const conIssOprtArr=['_CREATE'=>'新增',
                    '_SUBMIT'=>'送审',
                    '_AUDIT'=>'审核',
                    '_REVIEW'=>'复核',
                    '_APPROVE'=>'审批',
                    '_MAINTAIN'=>'维护',
                    '_UPDATE'=>'更新',
                    '_EXECUTE'=>'执行',
                    '_READ'=>'查阅',
                    '_DELETE'=>'删除',
                    ];

//issue的权限与操作的对应关系
const conIssAuthOprtArr=[
        '_PAT'=>[
          'edit'=>['_READ','_UPDATE','_CREATE','_SUBMIT','_DELETE','_EXECUTE'],
          'audit'=>['_READ','_UPDATE','_AUDIT'],
          'review'=>['_READ','_UPDATE','_REVIEW'],
          'approve'=>['_READ','_UPDATE','_APPROVE'],
          'maintain'=>['_READ','_UPDATE','_MAINTAIN']
        ],
        '_THE'=>[
          'edit'=>['_READ','_UPDATE','_CREATE','_SUBMIT','_DELETE','_EXECUTE'],
          'audit'=>['_READ','_UPDATE','_AUDIT'],
          'review'=>['_READ','_UPDATE','_REVIEW'],
          'approve'=>['_READ','_UPDATE','_APPROVE'],
          'maintain'=>['_READ','_UPDATE','_MAINTAIN']
        ],
        '_PRO'=>[
          'edit'=>['_READ','_UPDATE','_CREATE','_SUBMIT','_DELETE','_EXECUTE'],
          'audit'=>['_READ','_UPDATE','_AUDIT'],
          'review'=>['_READ','_UPDATE','_REVIEW'],
          'approve'=>['_READ','_UPDATE','_APPROVE'],
          'maintain'=>['_READ','_UPDATE','_MAINTAIN']
        ],
];

#issue的实体定义
const conIssEntArr=[
  'process'=>[
    'submit'=>['chi'=>'送审','status'=>['_PATS1','_THES1','_PROS1']],
    'audit'=>['chi'=>'审核','status'=>['_PATS2','_THES2','_PROS2']],
    'approve'=>['chi'=>'审批','status'=>['_PATS3','_THES3','_PROS3']],
    'execute'=>['chi'=>'执行','status'=>['_PATS4','_THES4','_PROS4']],
    'end'=>['chi'=>'完结','status'=>['_PATS_END','_THES_END','_PROS_END']]
  ],
  'oprt'=>[
    '_CREATE'=>'新增',
    '_SUBMIT'=>'送审',
    '_AUDIT'=>'审核',
    '_REVIEW'=>'复核',
    '_APPROVE'=>'审批',
    '_MAINTAIN'=>'维护',
    '_UPDATE'=>'更新',
    '_EXECUTE'=>'执行',
    '_READ'=>'查阅',
    '_DELETE'=>'删除'
  ],
  'ent-name'=>['iss-pat','iss-the','iss-pro'],
  'iss-pat'=>[
    'auth-oprt'=>[
      'edit'=>['chi'=>'申报','oprt'=>['_READ','_UPDATE','_CREATE','_SUBMIT','_DELETE','_EXECUTE']],
      'audit'=>['chi'=>'审核','oprt'=>['_READ','_UPDATE','_AUDIT']],
      'review'=>['chi'=>'复审','oprt'=>['_READ','_UPDATE','_REVIEW']],
      'approve'=>['chi'=>'审批','oprt'=>['_READ','_UPDATE','_APPROVE']],
      'maintain'=>['chi'=>'维护','oprt'=>['_READ','_UPDATE','_MAINTAIN']],
      'execute'=>['chi'=>'执行','oprt'=>['_READ','_UPDATE','_MAINTAIN']]
    ],
    'status'=>[//除‘完结’以外的其它状态
      '_ISST_PATS'=>'*',
      //label-info，待定
      '_PATS1'=>['chi'=>'送审','oprt'=>[]],
      '_PATS1-1'=>[
        'chi'=>'填报中',
        'oprt'=>[
          '_UPDATE'=>['next-status'=>[]],
          '_DELETE'=>['next-status'=>['display'=>1]],
          '_SUBMIT'=>['next-status'=>['_PATS2-1']]
        ]
      ],
      '_PATS1-2'=>[
        'chi'=>'审核完-待修改',
        'oprt'=>[
          '_UPDATE'=>['next-status'=>[]],
          '_SUBMIT'=>['next-status'=>['_PATS2-2']]
        ]
      ],
      '_PATS1-3'=>[
        'chi'=>'审批完-需完善',
        'oprt'=>[
          '_UPDATE'=>['next-status'=>[]],
          '_SUBMIT'=>['next-status'=>['_PATS2-3']]
        ]
      ],
      //label-success，正常
      '_PATS2'=>['chi'=>'审核','oprt'=>[]],
      '_PATS2-1'=>[
        'chi'=>'新增-专利申请',
        'oprt'=>[
          '_UPDATE'=>['next-status'=>[]],
          '_AUDIT'=>['next-status'=>['_PATS1-2','_PATS3-3','_PATS3-1']]
        ]
      ],
      '_PATS2-2'=>[
        'chi'=>'送审-已修改',
        'oprt'=>[
          '_UPDATE'=>['next-status'=>[]],
          '_AUDIT'=>['next-status'=>['_PATS1-2','_PATS3-3','_PATS3-1']]
        ]
      ],
      '_PATS2-3'=>[
        'chi'=>'送审-已完善',
        'oprt'=>[
          '_UPDATE'=>['next-status'=>[]],
          '_AUDIT'=>['next-status'=>['_PATS1-2','_PATS3-3','_PATS3-1']]
        ]
      ],
      //label-warning，异常
      '_PATS3'=>['chi'=>'审批','oprt'=>[]],
      '_PATS3-1'=>[
        'chi'=>'审核-通过',
        'oprt'=>[
          '_UPDATE'=>['next-status'=>[]],
          '_APPROVE'=>['next-status'=>['_PATS4-1','_PATS4-END4','_PATS1-3']]
        ]
      ],
      '_PATS3-2'=>[
        'chi'=>'新增-续费申请',
        'oprt'=>[
          '_UPDATE'=>['next-status'=>[]],
          '_APPROVE'=>['next-status'=>['_PATS4-1','_PATS4-END5']]
        ]
      ],
      '_PATS3-3'=>[
        'chi'=>'审核-不予支持',
        'oprt'=>[
          '_APPROVE'=>['next-status'=>['_PATS4-1','_PATS4-END4','_PATS1-3']]
        ]
      ],
      //'_PATS3-END1'=>'审批-否决',
//      '_PATS3-END2'=>'续费-放弃',
      //label-default，停用
      '_PATS4'=>['chi'=>'执行','oprt'=>[]],
      '_PATS4-1'=>[
        'chi'=>'审批-批准',
        'oprt'=>[
          '_UPDATE'=>['next-status'=>[]],
          '_EXECUTE'=>['next-status'=>['_PATS4-2','_PATS4-3']]
        ]
      ],
      '_PATS4-2'=>[
        'chi'=>'执行中',
        'oprt'=>[
          '_UPDATE'=>['next-status'=>[]],
          '_EXECUTE'=>['next-status'=>['_PATS4-3']]
        ]
      ],
      '_PATS4-3'=>[
        'chi'=>'申报-复核',
        'oprt'=>[
          '_UPDATE'=>['next-status'=>[]],
          '_MAINTAIN'=>['next-status'=>['_PATS4-4','_PATS4-5']]
        ]
      ],
      '_PATS4-4'=>[
        'chi'=>'申报-提交',
        'oprt'=>[
          '_UPDATE'=>['next-status'=>[]],
          '_MAINTAIN'=>['next-status'=>['_PATS4-END1','_PATS4-END2','_PATS4-5']]
        ]
      ],
      '_PATS4-5'=>[
        'chi'=>'申报-修改',
        'oprt'=>[
          '_UPDATE'=>['next-status'=>[]],
          '_EXECUTE'=>['next-status'=>['_PATS4-3']]
        ]
      ],
      '_PATS4-6'=>[
        'chi'=>'续费-批准',
        'oprt'=>[
          '_UPDATE'=>['next-status'=>[]],
          '_MAINTAIN'=>['next-status'=>['_PATS4-7']]
        ]
      ],
      '_PATS4-7'=>[
        'chi'=>'续费-提交',
        'oprt'=>[
          '_UPDATE'=>['next-status'=>[]],
          '_MAINTAIN'=>['next-status'=>['_PATS4-END3']]
        ]
      ],
      '_PATS4-END1'=>[
        'chi'=>'申报-授权',
        'oprt'=>[
          '_UPDATE'=>['next-status'=>[]],
          '_MAINTAIN'=>['next-status'=>['_PATS_END']]
        ]
      ],
      '_PATS4-END2'=>[
        'chi'=>'申报-驳回',
        'oprt'=>[
          '_UPDATE'=>['next-status'=>[]],
          '_MAINTAIN'=>['next-status'=>['_PATS_END']]
        ]
      ],
      '_PATS4-END3'=>[
        'chi'=>'续费-授权',
        'oprt'=>[
          '_UPDATE'=>['next-status'=>[]],
          '_MAINTAIN'=>['next-status'=>['_PATS_END']]
        ]
      ],
      '_PATS4-END4'=>[
        'chi'=>'审批-否决',
        'oprt'=>[
          '_MAINTAIN'=>['next-status'=>['_PATS_END']]
        ]
      ],
      '_PATS4-END5'=>[
        'chi'=>'续费-放弃',
        'oprt'=>[
          '_MAINTAIN'=>['next-status'=>['_PATS_END']]
        ]
      ],
      //label-default，销账
      '_PATS_END'=>['chi'=>'完结','oprt'=>[]]
    ]
  ],
  'iss-the'=>[
    'auth-oprt'=>[
      'edit'=>['chi'=>'申报','oprt'=>['_READ','_UPDATE','_CREATE','_SUBMIT','_DELETE','_EXECUTE']],
      'audit'=>['chi'=>'审核','oprt'=>['_READ','_UPDATE','_AUDIT']],
      'review'=>['chi'=>'复审','oprt'=>['_READ','_UPDATE','_REVIEW']],
      'approve'=>['chi'=>'审批','oprt'=>['_READ','_UPDATE','_APPROVE']],
      'maintain'=>['chi'=>'维护','oprt'=>['_READ','_UPDATE','_MAINTAIN']],
      'execute'=>['chi'=>'执行','oprt'=>['_READ','_UPDATE','_MAINTAIN']]
    ],
    'status'=>[//除‘完结’以外的其它状态
      '_ISST_THES'=>'*',
      //label-info，送审
      '_THES1'=>'送审',
      '_THES1-1'=>'填报中',
      '_THES1-2'=>'审核完-待修改',
      '_THES1-3'=>'审批完-需完善',
      //label-success，审核
      '_THES2'=>'审核',
      '_THES2-1'=>'新增-论文发表申请',
      '_THES2-2'=>'送审-已修改',
      '_THES2-3'=>'送审-已完善',
      //'_THES2-END'=>'审核-拒绝',
      //label-warning，审批
      '_THES3'=>'审批',
      '_THES3-1'=>'审核-通过',
      '_THES3-2'=>'审核-不予支持',
      //'_THES3-END'=>'审批-否决',
      //label-primary，执行
      '_THES4'=>'执行',
      '_THES4-1'=>'审批-批准',
      '_THES4-2'=>'执行中',
      '_THES4-3'=>'复核-修改',
      '_THES4-4'=>'复核-通过',
      '_THES4-5'=>'投稿-提交',
      '_THES4-6'=>'投稿-修改',
      '_THES4-7'=>'投稿-接受',
      '_THES4-8'=>'投稿-复核',
      '_THES4-END1'=>'投稿-发表',
      '_THES4-END2'=>'投稿-被拒',
      '_THES4-END3'=>'审批-否决',
      //label-default，完结
      '_THES_END'=>'完结'
    ]
  ],
  'iss-pro'=>[
    'auth-oprt'=>[
      'edit'=>['chi'=>'申报','oprt'=>['_READ','_UPDATE','_CREATE','_SUBMIT','_DELETE','_EXECUTE']],
      'audit'=>['chi'=>'审核','oprt'=>['_READ','_UPDATE','_AUDIT']],
      'review'=>['chi'=>'复审','oprt'=>['_READ','_UPDATE','_REVIEW']],
      'approve'=>['chi'=>'审批','oprt'=>['_READ','_UPDATE','_APPROVE']],
      'maintain'=>['chi'=>'维护','oprt'=>['_READ','_UPDATE','_MAINTAIN']],
      'execute'=>['chi'=>'执行','oprt'=>['_READ','_UPDATE','_MAINTAIN']]
    ],
    'status'=>[//除‘完结’以外的其它状态
      '_ISST_PROS'=>'*',
      //label-info，送审
      '_PROS1'=>'送审',
      '_PROS1-1'=>'',
      //label-success，审核
      '_PROS2'=>'审核',
      '_PROS2-1'=>'',
      '_PROS2-2'=>'送审-已修改',
      '_PROS2-3'=>'送审-已完善',
      //'_PROS2-END'=>'审核-拒绝',
      //label-warning，审批
      '_PROS3'=>'审批',
      '_PROS3-1'=>'审核-通过',
      '_PROS3-2'=>'审核-不予支持',
      '_PROS3-3'=>'复核-通过',
      '_PROS3-4'=>'复核-不予支持',
      //'_PROS3-END'=>'审批-否决',
      //label-primary，执行
      '_PROS4'=>'执行',
      '_PROS4-1'=>'审批-批准',
      '_PROS4-END1'=>'审批-否决',
      //label-default，完结
      '_PROS_END'=>'完结'
    ]
  ]
];
//issue的类型大类：3类
const conIssEntNameArr=['_PAT','_THE','_PRO'];
//issue的状态数组大类：5类
const conIssTypeArr=['submit'=>['typeChi'=>'送审','typeValue'=>['_PATS1','_THES1','_PROS1']],
                      'audit'=>['typeChi'=>'审核','typeValue'=>['_PATS2','_THES2','_PROS2']],
                      'approve'=>['typeChi'=>'审批','typeValue'=>['_PATS3','_THES3','_PROS3']],
                      'execute'=>['typeChi'=>'执行','typeValue'=>['_PATS4','_THES4','_PROS4']],
                      'end'=>['typeChi'=>'完结','typeValue'=>['_PATS_END','_THES_END','_PROS_END']]
                    ];
//const conIssTypeArr=['submit'=>['typeChi'=>'送审','typeValue'=>['_PATS1','_THES1']],
//                      'audit'=>['typeChi'=>'审核','typeValue'=>['_PATS2','_THES2']],
//                      'approve'=>['typeChi'=>'审批','typeValue'=>['_PATS3','_THES3']],
//                      'execute'=>['typeChi'=>'执行','typeValue'=>['_PATS4','_THES4']],
//                      'end'=>['typeChi'=>'完结','typeValue'=>['_PATS_END','_THES_END']]
//                    ];
//issue的pat状态数组细分共5类23个，大类间用下划线“_”分隔，大类里的小项用连字符“-”分隔
const conIssPatStatusArr=[//除‘完结’以外的其它状态
                    '_ISST_PATS'=>'*',
                    //label-info，待定
                    '_PATS1'=>'送审',
                    '_PATS1-1'=>'填报中',
                    '_PATS1-2'=>'审核完-待修改',
                    '_PATS1-3'=>'审批完-需完善',
                    //label-success，正常
                    '_PATS2'=>'审核',
                    '_PATS2-1'=>'新增-专利申请',
                    '_PATS2-2'=>'送审-已修改',
                    '_PATS2-3'=>'送审-已完善',
                    
                    //label-warning，异常
                    '_PATS3'=>'审批',
                    '_PATS3-1'=>'审核-通过',
                    '_PATS3-2'=>'新增-续费申请',
                    '_PATS3-3'=>'审核-不予支持',
                    //'_PATS3-END1'=>'审批-否决',
//                    '_PATS3-END2'=>'续费-放弃',
                    //label-default，停用
                    '_PATS4'=>'执行',
                    '_PATS4-1'=>'审批-批准',
                    '_PATS4-2'=>'执行中',
                    '_PATS4-3'=>'申报-复核',
                    '_PATS4-4'=>'申报-提交',
                    '_PATS4-5'=>'申报-修改',
                    '_PATS4-6'=>'续费-批准',
                    '_PATS4-7'=>'续费-提交',
                    '_PATS4-END1'=>'申报-授权',
                    '_PATS4-END2'=>'申报-驳回',
                    '_PATS4-END3'=>'续费-授权',
                    '_PATS4-END4'=>'审批-否决',
                    '_PATS4-END5'=>'续费-放弃',
                    //label-default，销账
                    '_PATS_END'=>'完结'
                    ];
//issue的the状态数组细分共6类21个，大类间用下划线“_”分隔，大类里的小项用连字符“-”分隔
const conIssTheStatusArr=[//除‘完结’以外的其它状态
                    '_ISST_THES'=>'*',
                    //label-info，送审
                    '_THES1'=>'送审',
                    '_THES1-1'=>'填报中',
                    '_THES1-2'=>'审核完-待修改',
                    '_THES1-3'=>'审批完-需完善',
                    //label-success，审核
                    '_THES2'=>'审核',
                    '_THES2-1'=>'新增-论文发表申请',
                    '_THES2-2'=>'送审-已修改',
                    '_THES2-3'=>'送审-已完善',
                    //'_THES2-END'=>'审核-拒绝',
                    //label-warning，审批
                    '_THES3'=>'审批',
                    '_THES3-1'=>'审核-通过',
                    '_THES3-2'=>'审核-不予支持',
                    //'_THES3-END'=>'审批-否决',
                    //label-primary，执行
                    '_THES4'=>'执行',
                    '_THES4-1'=>'审批-批准',
                    '_THES4-2'=>'执行中',
                    '_THES4-3'=>'复核-修改',
                    '_THES4-4'=>'复核-通过',
                    '_THES4-5'=>'投稿-提交',
                    '_THES4-6'=>'投稿-修改',
                    '_THES4-7'=>'投稿-接受',
                    '_THES4-8'=>'投稿-复核',
                    '_THES4-END1'=>'投稿-发表',
                    '_THES4-END2'=>'投稿-被拒',
                    '_THES4-END3'=>'审批-否决',
                    //label-default，完结
                    '_THES_END'=>'完结'
                    ];
//issue的Pro状态数组细分共6类?个，大类间用下划线“_”分隔，大类里的小项用连字符“-”分隔
const conIssProStatusArr=[//除‘完结’以外的其它状态
                    '_ISST_PROS'=>'*',
                    //label-info，送审
                    '_PROS1'=>'送审',
                    '_PROS1-1'=>'',
                    //label-success，审核
                    '_PROS2'=>'审核',
                    '_PROS2-1'=>'',
                    '_PROS2-2'=>'送审-已修改',
                    '_PROS2-3'=>'送审-已完善',
                    //'_PROS2-END'=>'审核-拒绝',
                    //label-warning，审批
                    '_PROS3'=>'审批',
                    '_PROS3-1'=>'审核-通过',
                    '_PROS3-2'=>'审核-不予支持',
                    '_PROS3-3'=>'复核-通过',
                    '_PROS3-4'=>'复核-不予支持',
                    //'_PROS3-END'=>'审批-否决',
                    //label-primary，执行
                    '_PROS4'=>'执行',
                    '_PROS4-1'=>'审批-批准',
                    '_PROS4-END1'=>'审批-否决',
                    //label-default，完结
                    '_PROS_END'=>'完结'
                    ];


  /**
     * 各个模块权限设置初始值
     * 参数$module，类型：字符串。值：可为空。说明：模块名称。默认值：'_ALL'
     */
  function _commonModuleAuth($module='')
  {
    //各个模块的名称
    $nameArr=array('_ISS','_PAT','_PRO','_THE','_ATT','_ADMIN','_ASS','');
    //if(empty($module)){
//      $module='_ALL';
//    }else{
//      //判断$module的取值是否在规定的数组范围内
//      if (in_array($module,$nameArr)== FALSE) {
//        $auth='wrong parameter for function.';
//      }
//    }
    
    //判断$module的取值是否在规定的数组范围内
    if(in_array($module,$nameArr)){
        if(empty($module))$module='_ALL';
    }else{
        $auth='wrong parameter for function.';
    }
    
//    $authIss=array('编辑'=>0,'审核'=>0,'审批'=>0,'执行'=>0,'维护'=>0);
//    $authPat=array('编辑'=>0,'审核'=>0,'审批'=>0,'执行'=>0,'维护'=>0);
//    $authPro=array('编辑'=>0,'审核'=>0,'审批'=>0,'执行'=>0,'维护'=>0);
//    $authThe=array('编辑'=>0,'审核'=>0,'审批'=>0,'执行'=>0,'维护'=>0);
//    $authAtt=array('上传'=>0,'下载'=>0,'删除'=>0,'移动'=>0,'复制'=>0); 
    //'read'权限仅为用户登录后前台的“查阅”权限，后台“用户中心”无需该权限
    $authIss1=['_PAT'=>conIssAuthArr['_PAT'],'_PRO'=>conIssAuthArr['_PRO'],'_THE'=>conIssAuthArr['_THE']];
    
    $authIss=array('edit'=>0,'audit'=>0,'approve'=>0,'execute'=>0,'maintain'=>0);
    $authPat=array('edit'=>0,'audit'=>0,'approve'=>0,'execute'=>0,'maintain'=>0);
    $authPro=array('edit'=>0,'audit'=>0,'approve'=>0,'execute'=>0,'maintain'=>0);
    $authThe=array('edit'=>0,'audit'=>0,'approve'=>0,'execute'=>0,'maintain'=>0);
    $authAtt=array('upload'=>0,'download'=>0,'erase'=>0,'move'=>0,'copy'=>0);
    $authAss=array('read'=>0,'edit'=>0,'audit'=>0,'approve'=>0,'maintain'=>0);

    $authAdmin=array('enable'=>0);

    switch($module){
      case'_ISS':
        $auth=$authIss;
      break;
      case'_PAT':
        $auth=$authPat;
      break;
      case'_PRO':
        $auth=$authPro;
      break;
      case'_THE':
        $auth=$authThe;
      break;
      case'_ATT':
        $auth=$authAtt;
      break;
      case'_ADMIN':
        $auth=$authAdmin;
      break;
      case'_ASS':
        $auth=$authAss;
      break;
      //all
      case'_ALL':
      //default:
        $auth=array('iss'=>$authIss,'pat'=>$authPat,'att'=>$authAtt,'pro'=>$authPro,
                    'the'=>$authThe,'admin'=>$authAdmin,'ass'=>$authAss);
      break;
    }
    return $auth;
  }
  
  
  /**
     * issue的权限与issue状态的对应关系
     * 参数$issType，类型：字符串。值：不为空。说明：需要得到的iss类型。
     * 参数$authName，类型：字符串。值：不为空。说明：issue权限名称。
     */
  function _commonIssAuthStatus($issType='',$authName='')
  {
    $issTypeArr=array('_PAT','_PRO','_THE');
    //判断$issType的取值是否在规定的数组范围内
    if(in_array($issType,$issTypeArr)){
        $issType;
    }else{
        return 'The parameter should be a string in:'.json_encode($issTypeArr);
    }
    
    $status=array();
    
    switch($issType){
      case'_PAT':
        $authNameArr=array_keys(_commonModuleAuth('_ISS'));
        foreach($authNameArr as $value){
            switch($value){
              case'maintain':
                  $status[$value]=array('申报复核','申报提交','续费提交','准予续费','否决申报',
                                '专利授权','专利驳回','放弃续费','续费授权','续费新增');
              break;   
              
              case'edit':
                  $status[$value]=array('申报新增','填报','返回修改','修改完善');
              break;
              
              case'audit':
                  $status[$value]=array('待审核');
              break;
              
              case'approve':
                  $status[$value]=array('审核未通过','审核通过','变更申请','拟续费');
              break;
              
              case'execute':
                  $status[$value]=array('批准申报','申报执行','申报修改','准予变更','否决变更');
              break;
                       
            }
            
        }
      break;
      
      case'_PRO':
        $authNameArr=array_keys(_commonModuleAuth('_ISS'));
        
      break;
      
      case'_THE':
        $authNameArr=array_keys(_commonModuleAuth('_ISS'));
        
      break;
    }
    
    return $status[$authName];
  
  }
  
   /**
     * 将iss权限数组的$key转为中文
     * 参数$authArr，类型：数组。值：不为空。说明：需要进行数组键名转换的数组。
     */
  function _commonAuthArrKeyToCHN($authArr=array())
  {
      $issTemp=array();
      foreach($authArr as $k=>$v){
              switch($k){
                case 'edit':
                  $issTemp['编辑']=$v;
                break;
                
                case 'audit':
                  $issTemp['审核']=$v;
                break;
                
                case 'approve':
                  $issTemp['审批']=$v;
                break;
                
                case 'execute':
                  $issTemp['执行']=$v;
                break;
                
                case 'maintain':
                  $issTemp['维护']=$v;
                break;
                //...................附件.....................
                case 'upload':
                  $issTemp['上传']=$v;
                break;
                
                case 'download':
                  $issTemp['下载']=$v;
                break;
                
                case 'erase':
                  $issTemp['删除']=$v;
                break;
                
                case 'move':
                  $issTemp['移动']=$v;
                break;
                
                case 'copy':
                  $issTemp['复制']=$v;
                break;
                //...................!附件.....................
                
               // case 'enable':
//                  $issTemp['启用']=$v;
//                break;
              }
          }
      return $issTemp;
    
  }

