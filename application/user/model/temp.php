<?php

/**
 * @author Prince
 * @copyright 2018
 */

   //case 'authorize':
                 $msg.=$issSet->topic.'提交结果：'.$request->request('title').'<br>
                 结果说明：<span class="text-info">'.$request->request('resultMsg').'</span><br>';
              
                  //根据2类issStatus情况向patinfo,patrecord表写入不同的数据
                  switch($issSet->status){
                    case '申报提交':
                      //issinfo表要写入的数据  
                      $issData=array('status'=>'专利授权','resultdate'=>$today);
                      
                      //patinfo表要写入的数据，$request->request()为前端的formdata对象发送来的表单数据（键/值对）                      
                      $patData= array_merge($request->request(),array('status'=>'授权','note'=>$today.":".$msg));

                      //issrecord表、patrecord表要写入的数据
                      $act='专利授权';
                      $actdetail=$msg;
                    break;
                    
                    case '续费提交':
                      //issinfo表要写入的数据  
                      $issData=array('status'=>'续费授权','resultdate'=>$today);
                      
                      //patinfo表要写入的数据，$request->request()为前端的formdata对象发送来的表单数据（键/值对）
                      $patData=array_merge($request->request(),array('status'=>'续费授权','note'=>($today.":".$msg)));
                      
                      //issrecord表、patrecord表要写入的数据
                      $act='续费授权';
                      $actdetail=$msg;
                    break;
                    
                  }
                  // 使用对象方法，向issinfo表更新数据表字段信息,有更新就返回受影响的行数。
                  $issUpdated=$issSet->allowField(true)->data($issData,true)->save();
              
                  //$issUpdated不为0表示对issinfo表进行了更新，才在issrecord表新建一条记录。
                  if($issUpdated){
                    // 使用静态方法，向issrecord表新增信息。
                    $issRecordSet=IssrecordModel::create([
                      'num'=>$numId,
                      'act'=>$act,
                      'actdetail'=>$actdetail,
                      'acttime'=>$today,
                      'username'=>$request->request('username'),
                      'rolename'=>$role,
                      'issinfo_id'=>$issId,
                    ]);
                    //静态方法创建新对象后，返回对象id
                    $issRecordId= $issRecordSet->id;
                  }
                  
                  // 使用对象方法，向patinfo表更新信息,有更新就返回受影响的行数。
                  $patUpdated=$patSet->allowField(true)->data($patData,true)->save();
                  
                  //$patUpdated不为0表示对patinfo表进行了更新，才在patrecord表新建一条记录。
                  if($patUpdated){
                    // 使用静态方法，向patrecord表新增信息。
                    $patRecordSet=PatrecordModel::create([
                      'num'=>$patNumId,
                      'act'=>$act,
                      'actdetail'=>$actdetail,
                      'acttime'=>$today,
                      'username'=>$request->param('username'),
                      'rolename'=>$role,
                      'patinfo_id'=>$patId,
                      'note'=>$today.":".$actdetail
                    ]);
                    //静态方法创建新对象后，返回对象id
                    $patRecordId= $patRecordSet->id;
                  }
                  
                  //返回前端的信息
                  $result='success';
             //break;
        UserModel::update([
            'authority'=>('{"isspat":{"create":0,"edit":0,"audit":1,"approve":1,"execute":1,"maintain":1},
            "isspro":{"create":0,"edit":0,"audit":0,"approve":0,"execute":0,"maintain":0},
            "issthe":{"create":0,"edit":0,"audit":0,"approve":0,"execute":0,"maintain":0},
            "att":{"upload":1,"download":1,"delete":1}}'),
         ], ['id' => $userlg->id]);
         
         $authority=array('isspat'=>array('create'=>0,'edit'=>0,'audit'=>1,'approve'=>1,'execute'=>1,'maintain'=>1),
                          'isspro'=>array('create'=>0,'edit'=>0,'audit'=>0,'approve'=>0,'execute'=>0,'maintain'=>0),
                          'issthe'=>array('create'=>0,'edit'=>0,'audit'=>0,'approve'=>0,'execute'=>0,'maintain'=>0),
                          'att'=>array('upload'=>1,'download'=>1,'delete'=>1),
                          );
                          

          switch($oprt){
            case 'edit':
              $map['status'] =['in',['填报','返回修改','修改完善']];
              $map['dept'] =$this->dept;
            break;
            
            case 'audit':
              $map['status'] ='待审核';
              $map['dept'] =$this->dept;
            break;
            
            case 'approve':
              $map['status'] =['in',['审核未通过','审核通过','变更申请','拟续费']];
            break;
            
            case 'execute':
              $map['status'] =['in',['批准申报','申报执行','申报修改','准予变更','否决变更']];
              $map['dept'] =$this->dept;
            break;
            
            case 'maintain':
              $map['status'] =['in',['申报复核','申报提交','续费提交','准予续费',
                                      '否决申报','专利授权','专利驳回','放弃续费','续费授权']];
            break;
          }
         
         $mapAudit['status'] ='待审核';
         $mapAudit['dept'] =$this->dept;
         
         $mapApprove['status'] =['in',['审核未通过','审核通过','变更申请','拟续费']];
         
         $mapExecute['status'] =['in',['批准申报','申报执行','申报修改','准予变更','否决变更']];
         $mapExecute['dept'] =$this->dept;
         
         $mapMaintain['status'] =['in',['申报复核','申报提交','续费提交','准予续费',
                                      '否决申报','专利授权','专利驳回','放弃续费','续费授权']];
         
         $map['status'] ='完结';
?>