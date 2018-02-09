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
                          

// 输出模板文件，显示issue中与pat相关的数据集。
     public function issPatEdit(Request $request)
    {
      $this->_loginUser();
      
       // $returnType接收前端页面传来的returnType值，‘0’为模板文件，‘1’为数据
      if(!empty($request->param('returnType'))){
        $returnType=$request->param('returnType');
      }else{
        $returnType=0;
      }
      
      // $authority接收前端页面传来的authority值
     // if(!empty($request->param('authority'))){
//        $authority=$request->param('authority');
//      }else{
//        $authority='_EDIT';
//      }
//      
//      // 忽略前端页面传来的issType值，直接赋值为'_PATENT'
//      $issType='_PATENT';
      
      //$totalTableRows接收前端页面传来的分页时每页显示的记录数，默认为10
      if(!empty($request->param('issPatTableRows'))){
          $issPatTableRows=$request->param('issPatTableRows');
      }else{
          $issPatTableRows=10;
      }
      
       // 接收前端分页页数变量：“pageTotalNum”
      if(!empty($request->param('pageTotalNum'))){
          $pageTotalNum=$request->param('pageTotalNum');
      }else{
          $pageTotalNum=1;
      }
      
      // $sortName接收前端页面传来的排序字段名
      if(!empty($request->param('sortName'))){
          $sortName=$request->param('sortName');
      }else{
          $sortName='_TOPIC';
      }
        
      // $sort接收前端页面传来的排序顺序
      if(!empty($request->param('sort'))){
          $sort=$request->param('sort');
      }else{
          $sort='_ASC';
      }
      
      // 查询词1，'searchPatName'
      if(!empty($request->param('searchPatName'))){
          $searchPatName=$request->param('searchPatName');
      }else{
          $searchPatName='';
      } 
        
      // 查询词2，'searchDept'
      if(!empty($request->param('searchDept'))){
          $searchDept=$request->param('searchDept');
      }else{
          $searchDept=0;
      } 
        
      // 查询词3，'searchPatStatus'
      if(!empty($request->param('searchPatStatus'))){
          $searchPatStatus=$request->param('searchPatStatus');
      }else{
          $searchPatStatus=0;
      }
        
      // 查询词4，'searchPatType'
      if(!empty($request->param('searchPatType'))){
          $searchPatType=$request->param('searchPatType');
      }else{
          $searchPatType=0;
      } 
        
      // 查询词5，'searchWriter'
      if(!empty($request->param('searchWriter'))){
          $searchWriter=$request->param('searchWriter');
      }else{
          $searchWriter='';
      }
      
      // 选择排序字段
      switch($sortName){
      
        case '_PATNAME':
          $strOrder='abstract';
        break;
        
        case '_PATSTATUS':
          $strOrder='abstract';
        break;
            
        case '_ABSTRACT':
          $strOrder='abstract';
        break;
        
        case '_WRITER':
          $strOrder='writer';
        break;
        
        case '_EXECUTER':
          $strOrder='executer';
        break;
            
        case '_ADDNEWDATE':
          $strOrder='addnewdate';
        break;
        
        case '_STATUS':
          $strOrder='status';
        break;
            
        case '_DEPT':
          $strOrder='dept';
        break;
            
        case '_TOPIC':
          $strOrder='topic';  
          $sortName="_TOPIC";
        break;
        
         //默认按字段“status”
        default:
          $strOrder='status';  
          $sortName="_OPERATION";
        break;
        
      } 
      
      //  组合升序or降序查询
      if($sort=="_ASC"){
          $strOrder=$strOrder.' asc';
      }else{
          $strOrder=$strOrder.' desc';
          
      }
      
     //使用模型Issinfo
     $issSet = new IssinfoModel; 
     $mapEdit['status'] =['in',['填报','返回修改','修改完善']];
     $mapEdit['dept'] =$this->dept;
     $mapEdit['writer']=$this->username;
     
     // 记录总数
     $numTotal = $issSet->where($mapEdit)->count();
          
     // 查出所有的用户并分页，根据“strOrder”排序，前端页面显示的锚点（hash值）为$fragment，设定分页页数变量：“pageTotalNum”
     // 带上每页显示记录行数$totalTableRows，实现查询结果分页显示。
     $issPatTotal = $issSet->where($mapEdit)
                            ->order($strOrder)
                            ->paginate($issPatTableRows,false,['type'=>'bootstrap','var_page' => 'pageTotalNum',
                            'query'=>['issPatTableRows'=>$issPatTableRows]]);
     // 获取分页显示
     $pageTotal = $issPatTotal->render();
     //
//     switch($authority){            
//        case'_TODO':
//          if($this->auth['isspat']['edit']){
//            $mapEdit['status'] =['in',['填报','返回修改','修改完善']];
//            $mapEdit['dept'] =$this->dept;
//            $mapEdit['writer']=$this->username;
//            //$mapEdit['executer']=['notin',0];
//          }else{$mapEdit=$map;}
//          
//          if($this->auth['isspat']['audit']){
//            $mapAudit['status'] ='待审核';
//            $mapAudit['dept'] =$this->dept;
//            //$mapAudit['writer']=['notin',0];
////            $mapAudit['executer']=['notin',0];
//          }else{$mapEdit=$map;}
//          
//          if($this->auth['isspat']['approve']){
//            $mapApprove['status'] =['in',['审核未通过','审核通过','变更申请','拟续费']];
//            //$mapApprove['dept'] =['notin',0];
////            $mapApprove['writer']=['notin',0];
////            $mapApprove['executer']=['notin',0];
//          }else{$mapEdit=$map;}
//          
//          if($this->auth['isspat']['execute']){
//            $mapExecute['status'] =['in',['批准申报','申报执行','申报修改','准予变更','否决变更']];
//            $mapExecute['dept'] =$this->dept;
//           // $mapExecute['writer']=['notin',0];
//            $mapExecute['executer']=$this->username;
//          }else{$mapEdit=$map;}
//          
//          if($this->auth['isspat']['maintain']){
//            $mapMaintain['status'] =['in',['申报复核','申报提交','续费提交','准予续费',
//                                      '否决申报','专利授权','专利驳回','放弃续费','续费授权']];
////            $mapMaintain['dept'] =['notin',0];
////            $mapMaintain['writer']=['notin',0];
////            $mapMaintain['executer']=['notin',0];
//          }else{$mapMaintain=$map;}
//          
//          // 记录总数
//          $numTotal = $issSet
//                            ->where($mapEdit)
//                            ->whereOr(function ($query) use ($mapExecute){
//                              $query->where($mapExecute);
//                              })
//                            ->whereOr(function ($query) use ($mapAudit){
//                              $query->where($mapAudit);
//                              })
//                            ->whereOr($mapApprove)
//                            ->whereOr($mapMaintain)
//                            ->count();
//          
//          // 查出所有的用户并分页，根据“strOrder”排序，前端页面显示的锚点（hash值）为$fragment，设定分页页数变量：“pageTotalNum”
//          // 带上每页显示记录行数$totalTableRows，实现查询结果分页显示。
//          $issPatTotal = $issSet
//                            ->where($mapEdit)
//                            ->whereOr(function ($query) use ($mapExecute){
//                              $query->where($mapExecute);
//                              })
//                            ->whereOr(function ($query) use ($mapAudit){
//                              $query->where($mapAudit);
//                              })
//                            ->whereOr($mapApprove)
//                            ->whereOr($mapMaintain)
//                            ->order($strOrder)
//                            ->paginate($issPatTableRows,false,['type'=>'bootstrap','var_page' => 'pageTotalNum',
//                            'query'=>['issPatTableRows'=>$issPatTableRows]]);
//        break;
//        
//        case'_RESULT':
//          if($this->auth['isspat']['edit']){
//            $mapEdit['status'] =['notin',['填报','返回修改','修改完善','完结']];
//            $mapEdit['dept'] =$this->dept;
//            $mapEdit['writer']=$this->username;
//          }else{$mapEdit=$map;}
//          
//          if($this->auth['isspat']['audit']){
//            $mapAudit['status'] =['notin',['待审核','完结']];
//            $mapAudit['dept'] =$this->dept;
//          }else{$mapAudit=$map;}
//          
//          if($this->auth['isspat']['approve']){
//            $mapApprove['status'] =['notin',['审核未通过','审核通过','变更申请','拟续费','完结']];
//          }else{$mapApprove=$map;}
//          
//          if($this->auth['isspat']['execute']){
//            $mapExecute['status'] =['notin',['批准申报','申报执行','申报修改','准予变更','否决变更','完结']];
//            $mapExecute['dept'] =$this->dept;
//            $mapExecute['executer']=$this->username;
//          }else{$mapExecute=$map;}
//          
//          if($this->auth['isspat']['maintain']){
//            $mapMaintain['status'] =['notin',['申报复核','申报提交','续费提交','准予续费',
//                                      '否决申报','专利授权','专利驳回','放弃续费','续费授权','完结']];
//          }else{$mapMaintain=$map;}
//        break;
//         
//                 
//        case'_DONE':
//          $map['status'] ='完结';
//          
//              //根据权限对查询字段进行赋值
//          if($this->auth['isspat']['edit']){
//            $mapEdit['status']=1;
//            $mapEdit['dept'] =$this->dept;
//            $mapEdit['writer']=$this->username;
//            $mapEdit['executer']=1;
//          }else{
//            $mapEdit['status']=0;
//            $mapEdit['dept'] =0;
//            $mapEdit['writer']=0;
//            $mapEdit['executer']=0;
//          } 
//              
//          if($this->auth['isspat']['audit']){
//            $mapAudit['status']=1;
//            $mapAudit['dept'] =$this->dept;
//            $mapAudit['writer']=1;
//            $mapAudit['executer']=1;
//          }else{
//            $mapAudit['status']=0;
//            $mapAudit['dept'] =0;
//            $mapAudit['writer']=0;
//            $mapAudit['executer']=0;
//          } 
//                        
//          if($this->auth['isspat']['execute']){
//            $mapExecute['status']=1;
//            $mapExecute['dept'] =$this->dept;
//            $mapExecute['writer']=1;
//            $mapExecute['executer']=$this->username;
//          }else{
//            $mapExecute['status']=0;
//            $mapExecute['dept'] =0;
//            $mapExecute['writer']=0;
//            $mapExecute['executer']=0;
//          } 
//              
//          if($this->auth['isspat']['approve']){
//            $mapApprove['status']=1;
//            $mapApprove['dept'] =1;
//            $mapApprove['writer']=1;
//            $mapApprove['executer']=1;
//          }else{
//            $mapApprove['status']=0;
//            $mapApprove['dept'] =0;
//            $mapApprove['writer']=0;
//            $mapApprove['executer']=0;
//          } 
//                    
//          if($this->auth['isspat']['maintain']){
//            $mapMaintain['status']=1;
//            $mapMaintain['dept'] =1;
//            $mapMaintain['writer']=1;
//            $mapMaintain['executer']=1;
//          }else{
//            $mapMaintain['status']=0;
//            $mapMaintain['dept'] =0;
//            $mapMaintain['writer']=0;
//            $mapMaintain['executer']=0;
//          }
//                    
//          //根据权限情况，组合查询条件
//          if(($mapMaintain['dept'].$mapApprove['dept'])=='00' ){
//            $map['dept'] =$this->dept;
//          }else{
//            $map['dept'] =['notin',0];
//          }
//          
//          if(($mapMaintain['writer'].$mapApprove['writer'].$mapAudit['writer'].$mapExecute['writer'])=='0000'){
//            $map['writer'] =$this->username;
//          }else{
//            $map['writer'] =['notin',0];
//          }
//          
//          if(($mapMaintain['executer'].$mapApprove['executer'].$mapAudit['executer'].$mapEdit['executer'])=='0000'){
//            $map['executer'] =$this->username;
//          }else{
//            $map['executer'] =['notin',0];
//          }
//          
//           // 记录总数
//          $numTotal = $issSet->where($map)->count();
//          
//          // 查出所有的用户并分页，根据“strOrder”排序，前端页面显示的锚点（hash值）为$fragment，设定分页页数变量：“pageTotalNum”
//          // 带上每页显示记录行数$totalTableRows，实现查询结果分页显示。
//          $issPatTotal = $issSet->where($map)
//                            ->order($strOrder)
//                            ->paginate($issPatTableRows,false,['type'=>'bootstrap','var_page' => 'pageTotalNum',
//                            'query'=>['issPatTableRows'=>$issPatTableRows]]);
//          
//        break;
//     }      
      
      
     //返回数据还是模板文件,‘0’为模板文件，‘1’为数据
      if($returnType){
        //响应前端的请求，返回前端要求条件的issPat数量
        return ($numTotal);
      }else{
        $this->assign([
              'home'=>$request->domain(),
              //"destr".$this->auth['isspro']['edit'],
              
              // 分页显示所需参数
              'issPatTotal'=>$issPatTotal,
              'numTotal'=>$numTotal,
              'pageTotal'=>$pageTotal,
              'issPatTableRows'=>$issPatTableRows,
              'pageTotalNum'=>$pageTotalNum,
              
              // 表格搜索字段
              'searchPatName'=>$searchPatName,
              'searchDept'=>$searchDept,
              'searchPatStatus'=>$searchPatStatus,
              'searchPatType'=>$searchPatType,
              'searchWriter'=>$searchWriter,
        
              // 表格排序信息
              'sortName'=>$sortName,
              'sort'=>$sort,
              //'patIssTableRows'=>$patIssTableRows,
              
              // 所return的页面显示的iss流程$issStatus
              //'authority'=>$authority,
              
              //
              'mapEdit'=>json_encode($mapEdit),
              //'mapAudit'=>json_encode($mapAudit),
//              'mapApprove'=>json_encode($mapApprove),
//              'mapExecute'=>json_encode($mapExecute),
//              'mapMaintain'=>json_encode($mapMaintain),
              // 所return的页面，某个button的data-patIssId的值为patIssId
              //'patIssId'=>$patIssId,
              
              // 返回前端role值
              //'role'=>$role,
              
        ]);
        // $this->assign(['a'=>'a','b'=>'b']);
//      return $this->fetch();
      //return $this->fetch('issPat', ['a'=>'a','b'=>'b']);
      //return $this->display();
      //return view('issPat', ['a'=>$request->param('issType'),'b'=>$request->param('authority')]);
        
        return view();
      }
     
      
    }
    
    // 输出模板文件，显示issue中与pat相关的数据集。
     public function issPatAudit(Request $request)
    {
      $this->_loginUser();
      
       // $returnType接收前端页面传来的returnType值，‘0’为模板文件，‘1’为数据
      if(!empty($request->param('returnType'))){
        $returnType=$request->param('returnType');
      }else{
        $returnType=0;
      }

      //$totalTableRows接收前端页面传来的分页时每页显示的记录数，默认为10
      if(!empty($request->param('issPatTableRows'))){
          $issPatTableRows=$request->param('issPatTableRows');
      }else{
          $issPatTableRows=10;
      }
      
       // 接收前端分页页数变量：“pageTotalNum”
      if(!empty($request->param('pageTotalNum'))){
          $pageTotalNum=$request->param('pageTotalNum');
      }else{
          $pageTotalNum=1;
      }
      
      // $sortName接收前端页面传来的排序字段名
      if(!empty($request->param('sortName'))){
          $sortName=$request->param('sortName');
      }else{
          $sortName='_TOPIC';
      }
        
      // $sort接收前端页面传来的排序顺序
      if(!empty($request->param('sort'))){
          $sort=$request->param('sort');
      }else{
          $sort='_ASC';
      }
      
      // 查询词1，'searchPatName'
      if(!empty($request->param('searchPatName'))){
          $searchPatName=$request->param('searchPatName');
      }else{
          $searchPatName='';
      } 
        
      // 查询词2，'searchDept'
      if(!empty($request->param('searchDept'))){
          $searchDept=$request->param('searchDept');
      }else{
          $searchDept=0;
      } 
        
      // 查询词3，'searchPatStatus'
      if(!empty($request->param('searchPatStatus'))){
          $searchPatStatus=$request->param('searchPatStatus');
      }else{
          $searchPatStatus=0;
      }
        
      // 查询词4，'searchPatType'
      if(!empty($request->param('searchPatType'))){
          $searchPatType=$request->param('searchPatType');
      }else{
          $searchPatType=0;
      } 
        
      // 查询词5，'searchWriter'
      if(!empty($request->param('searchWriter'))){
          $searchWriter=$request->param('searchWriter');
      }else{
          $searchWriter='';
      }
      
      // 选择排序字段
      switch($sortName){
      
        case '_PATNAME':
          $strOrder='abstract';
        break;
        
        case '_PATSTATUS':
          $strOrder='abstract';
        break;
            
        case '_ABSTRACT':
          $strOrder='abstract';
        break;
        
        case '_WRITER':
          $strOrder='writer';
        break;
        
        case '_EXECUTER':
          $strOrder='executer';
        break;
            
        case '_ADDNEWDATE':
          $strOrder='addnewdate';
        break;
        
        case '_STATUS':
          $strOrder='status';
        break;
            
        case '_DEPT':
          $strOrder='dept';
        break;
            
        case '_TOPIC':
          $strOrder='topic';  
          $sortName="_TOPIC";
        break;
        
         //默认按字段“status”
        default:
          $strOrder='status';  
          $sortName="_OPERATION";
        break;
        
      } 
      
      //  组合升序or降序查询
      if($sort=="_ASC"){
          $strOrder=$strOrder.' asc';
      }else{
          $strOrder=$strOrder.' desc';
          
      }
      
     //使用模型Issinfo
     $issSet = new IssinfoModel; 
     $mapAudit['status'] ='待审核';
     $mapAudit['dept'] =$this->dept;
     
     // 记录总数
     $numTotal = $issSet->where($mapAudit)->count();
          
     // 查出所有的用户并分页，根据“strOrder”排序，前端页面显示的锚点（hash值）为$fragment，设定分页页数变量：“pageTotalNum”
     // 带上每页显示记录行数$totalTableRows，实现查询结果分页显示。
     $issPatTotal = $issSet->where($mapAudit)
                            ->order($strOrder)
                            ->paginate($issPatTableRows,false,['type'=>'bootstrap','var_page' => 'pageTotalNum',
                            'query'=>['issPatTableRows'=>$issPatTableRows]]);
     // 获取分页显示
     $pageTotal = $issPatTotal->render();
       
     //返回数据还是模板文件,‘0’为模板文件，‘1’为数据
      if($returnType){
        //响应前端的请求，返回前端要求条件的issPat数量
        return ($numTotal);
      }else{
        $this->assign([
              'home'=>$request->domain(),
              //"destr".$this->auth['isspro']['edit'],
              
              // 分页显示所需参数
              'issPatTotal'=>$issPatTotal,
              'numTotal'=>$numTotal,
              'pageTotal'=>$pageTotal,
              'issPatTableRows'=>$issPatTableRows,
              'pageTotalNum'=>$pageTotalNum,
              
              // 表格搜索字段
              'searchPatName'=>$searchPatName,
              'searchDept'=>$searchDept,
              'searchPatStatus'=>$searchPatStatus,
              'searchPatType'=>$searchPatType,
              'searchWriter'=>$searchWriter,
        
              // 表格排序信息
              'sortName'=>$sortName,
              'sort'=>$sort,             
              
        ]);
        
        return view();
      }
     
      
    }
    
    // 输出模板文件，显示issue中与pat相关的数据集。
     public function issPatApprove(Request $request)
    {
      $this->_loginUser();
      
       // $returnType接收前端页面传来的returnType值，‘0’为模板文件，‘1’为数据
      if(!empty($request->param('returnType'))){
        $returnType=$request->param('returnType');
      }else{
        $returnType=0;
      }

      //$totalTableRows接收前端页面传来的分页时每页显示的记录数，默认为10
      if(!empty($request->param('issPatTableRows'))){
          $issPatTableRows=$request->param('issPatTableRows');
      }else{
          $issPatTableRows=10;
      }
      
       // 接收前端分页页数变量：“pageTotalNum”
      if(!empty($request->param('pageTotalNum'))){
          $pageTotalNum=$request->param('pageTotalNum');
      }else{
          $pageTotalNum=1;
      }
      
      // $sortName接收前端页面传来的排序字段名
      if(!empty($request->param('sortName'))){
          $sortName=$request->param('sortName');
      }else{
          $sortName='_TOPIC';
      }
        
      // $sort接收前端页面传来的排序顺序
      if(!empty($request->param('sort'))){
          $sort=$request->param('sort');
      }else{
          $sort='_ASC';
      }
      
      // 查询词1，'searchPatName'
      if(!empty($request->param('searchPatName'))){
          $searchPatName=$request->param('searchPatName');
      }else{
          $searchPatName='';
      } 
        
      // 查询词2，'searchDept'
      if(!empty($request->param('searchDept'))){
          $searchDept=$request->param('searchDept');
      }else{
          $searchDept=0;
      } 
        
      // 查询词3，'searchPatStatus'
      if(!empty($request->param('searchPatStatus'))){
          $searchPatStatus=$request->param('searchPatStatus');
      }else{
          $searchPatStatus=0;
      }
        
      // 查询词4，'searchPatType'
      if(!empty($request->param('searchPatType'))){
          $searchPatType=$request->param('searchPatType');
      }else{
          $searchPatType=0;
      } 
        
      // 查询词5，'searchWriter'
      if(!empty($request->param('searchWriter'))){
          $searchWriter=$request->param('searchWriter');
      }else{
          $searchWriter='';
      }
      
      // 选择排序字段
      switch($sortName){
      
        case '_PATNAME':
          $strOrder='abstract';
        break;
        
        case '_PATSTATUS':
          $strOrder='abstract';
        break;
            
        case '_ABSTRACT':
          $strOrder='abstract';
        break;
        
        case '_WRITER':
          $strOrder='writer';
        break;
        
        case '_EXECUTER':
          $strOrder='executer';
        break;
            
        case '_ADDNEWDATE':
          $strOrder='addnewdate';
        break;
        
        case '_STATUS':
          $strOrder='status';
        break;
            
        case '_DEPT':
          $strOrder='dept';
        break;
            
        case '_TOPIC':
          $strOrder='topic';  
          $sortName="_TOPIC";
        break;
        
         //默认按字段“status”
        default:
          $strOrder='status';  
          $sortName="_OPERATION";
        break;
        
      } 
      
      //  组合升序or降序查询
      if($sort=="_ASC"){
          $strOrder=$strOrder.' asc';
      }else{
          $strOrder=$strOrder.' desc';
          
      }
      
     //使用模型Issinfo
     $issSet = new IssinfoModel; 
     $mapApprove['status'] =['in',['审核未通过','审核通过','变更申请','拟续费']];
     
     // 记录总数
     $numTotal = $issSet->where($mapApprove)->count();
          
     // 查出所有的用户并分页，根据“strOrder”排序，前端页面显示的锚点（hash值）为$fragment，设定分页页数变量：“pageTotalNum”
     // 带上每页显示记录行数$totalTableRows，实现查询结果分页显示。
     $issPatTotal = $issSet->where($mapApprove)
                            ->order($strOrder)
                            ->paginate($issPatTableRows,false,['type'=>'bootstrap','var_page' => 'pageTotalNum',
                            'query'=>['issPatTableRows'=>$issPatTableRows]]);
     // 获取分页显示
     $pageTotal = $issPatTotal->render();
       
     //返回数据还是模板文件,‘0’为模板文件，‘1’为数据
      if($returnType){
        //响应前端的请求，返回前端要求条件的issPat数量
        return ($numTotal);
      }else{
        $this->assign([
              'home'=>$request->domain(),
              //"destr".$this->auth['isspro']['edit'],
              
              // 分页显示所需参数
              'issPatTotal'=>$issPatTotal,
              'numTotal'=>$numTotal,
              'pageTotal'=>$pageTotal,
              'issPatTableRows'=>$issPatTableRows,
              'pageTotalNum'=>$pageTotalNum,
              
              // 表格搜索字段
              'searchPatName'=>$searchPatName,
              'searchDept'=>$searchDept,
              'searchPatStatus'=>$searchPatStatus,
              'searchPatType'=>$searchPatType,
              'searchWriter'=>$searchWriter,
        
              // 表格排序信息
              'sortName'=>$sortName,
              'sort'=>$sort,             
              
        ]);
        
        return view();
      }
     
      
    }
    
    // 输出模板文件，显示issue中与pat相关的数据集。
     public function issPatExecute(Request $request)
    {
      $this->_loginUser();
      
       // $returnType接收前端页面传来的returnType值，‘0’为模板文件，‘1’为数据
      if(!empty($request->param('returnType'))){
        $returnType=$request->param('returnType');
      }else{
        $returnType=0;
      }

      //$totalTableRows接收前端页面传来的分页时每页显示的记录数，默认为10
      if(!empty($request->param('issPatTableRows'))){
          $issPatTableRows=$request->param('issPatTableRows');
      }else{
          $issPatTableRows=10;
      }
      
       // 接收前端分页页数变量：“pageTotalNum”
      if(!empty($request->param('pageTotalNum'))){
          $pageTotalNum=$request->param('pageTotalNum');
      }else{
          $pageTotalNum=1;
      }
      
      // $sortName接收前端页面传来的排序字段名
      if(!empty($request->param('sortName'))){
          $sortName=$request->param('sortName');
      }else{
          $sortName='_TOPIC';
      }
        
      // $sort接收前端页面传来的排序顺序
      if(!empty($request->param('sort'))){
          $sort=$request->param('sort');
      }else{
          $sort='_ASC';
      }
      
      // 查询词1，'searchPatName'
      if(!empty($request->param('searchPatName'))){
          $searchPatName=$request->param('searchPatName');
      }else{
          $searchPatName='';
      } 
        
      // 查询词2，'searchDept'
      if(!empty($request->param('searchDept'))){
          $searchDept=$request->param('searchDept');
      }else{
          $searchDept=0;
      } 
        
      // 查询词3，'searchPatStatus'
      if(!empty($request->param('searchPatStatus'))){
          $searchPatStatus=$request->param('searchPatStatus');
      }else{
          $searchPatStatus=0;
      }
        
      // 查询词4，'searchPatType'
      if(!empty($request->param('searchPatType'))){
          $searchPatType=$request->param('searchPatType');
      }else{
          $searchPatType=0;
      } 
        
      // 查询词5，'searchWriter'
      if(!empty($request->param('searchWriter'))){
          $searchWriter=$request->param('searchWriter');
      }else{
          $searchWriter='';
      }
      
      // 选择排序字段
      switch($sortName){
      
        case '_PATNAME':
          $strOrder='abstract';
        break;
        
        case '_PATSTATUS':
          $strOrder='abstract';
        break;
            
        case '_ABSTRACT':
          $strOrder='abstract';
        break;
        
        case '_WRITER':
          $strOrder='writer';
        break;
        
        case '_EXECUTER':
          $strOrder='executer';
        break;
            
        case '_ADDNEWDATE':
          $strOrder='addnewdate';
        break;
        
        case '_STATUS':
          $strOrder='status';
        break;
            
        case '_DEPT':
          $strOrder='dept';
        break;
            
        case '_TOPIC':
          $strOrder='topic';  
          $sortName="_TOPIC";
        break;
        
         //默认按字段“status”
        default:
          $strOrder='status';  
          $sortName="_OPERATION";
        break;
        
      } 
      
      //  组合升序or降序查询
      if($sort=="_ASC"){
          $strOrder=$strOrder.' asc';
      }else{
          $strOrder=$strOrder.' desc';
          
      }
      
     //使用模型Issinfo
     $issSet = new IssinfoModel; 
     $mapExecute['status'] =['in',['批准申报','申报执行','申报修改','准予变更','否决变更']];
     $mapExecute['dept'] =$this->dept;
     $mapExecute['executer'] =$this->username;
     
     // 记录总数
     $numTotal = $issSet->where($mapExecute)->count();
          
     // 查出所有的用户并分页，根据“strOrder”排序，前端页面显示的锚点（hash值）为$fragment，设定分页页数变量：“pageTotalNum”
     // 带上每页显示记录行数$totalTableRows，实现查询结果分页显示。
     $issPatTotal = $issSet->where($mapExecute)
                            ->order($strOrder)
                            ->paginate($issPatTableRows,false,['type'=>'bootstrap','var_page' => 'pageTotalNum',
                            'query'=>['issPatTableRows'=>$issPatTableRows]]);
     // 获取分页显示
     $pageTotal = $issPatTotal->render();
       
     //返回数据还是模板文件,‘0’为模板文件，‘1’为数据
      if($returnType){
        //响应前端的请求，返回前端要求条件的issPat数量
        return ($numTotal);
      }else{
        $this->assign([
              'home'=>$request->domain(),
              //"destr".$this->auth['isspro']['edit'],
              
              // 分页显示所需参数
              'issPatTotal'=>$issPatTotal,
              'numTotal'=>$numTotal,
              'pageTotal'=>$pageTotal,
              'issPatTableRows'=>$issPatTableRows,
              'pageTotalNum'=>$pageTotalNum,
              
              // 表格搜索字段
              'searchPatName'=>$searchPatName,
              'searchDept'=>$searchDept,
              'searchPatStatus'=>$searchPatStatus,
              'searchPatType'=>$searchPatType,
              'searchWriter'=>$searchWriter,
        
              // 表格排序信息
              'sortName'=>$sortName,
              'sort'=>$sort,             
              
        ]);
        
        return view();
      }
    }
    
    // 输出模板文件，显示issue中与pat相关的数据集。
     public function issPatMaintain(Request $request)
    {
      $this->_loginUser();
      
       // $returnType接收前端页面传来的returnType值，‘0’为模板文件，‘1’为数据
      if(!empty($request->param('returnType'))){
        $returnType=$request->param('returnType');
      }else{
        $returnType=0;
      }

      //$totalTableRows接收前端页面传来的分页时每页显示的记录数，默认为10
      if(!empty($request->param('issPatTableRows'))){
          $issPatTableRows=$request->param('issPatTableRows');
      }else{
          $issPatTableRows=10;
      }
      
       // 接收前端分页页数变量：“pageTotalNum”
      if(!empty($request->param('pageTotalNum'))){
          $pageTotalNum=$request->param('pageTotalNum');
      }else{
          $pageTotalNum=1;
      }
      
      // $sortName接收前端页面传来的排序字段名
      if(!empty($request->param('sortName'))){
          $sortName=$request->param('sortName');
      }else{
          $sortName='_TOPIC';
      }
        
      // $sort接收前端页面传来的排序顺序
      if(!empty($request->param('sort'))){
          $sort=$request->param('sort');
      }else{
          $sort='_ASC';
      }
      
      // 查询词1，'searchPatName'
      if(!empty($request->param('searchPatName'))){
          $searchPatName=$request->param('searchPatName');
      }else{
          $searchPatName='';
      } 
        
      // 查询词2，'searchDept'
      if(!empty($request->param('searchDept'))){
          $searchDept=$request->param('searchDept');
      }else{
          $searchDept=0;
      } 
        
      // 查询词3，'searchPatStatus'
      if(!empty($request->param('searchPatStatus'))){
          $searchPatStatus=$request->param('searchPatStatus');
      }else{
          $searchPatStatus=0;
      }
        
      // 查询词4，'searchPatType'
      if(!empty($request->param('searchPatType'))){
          $searchPatType=$request->param('searchPatType');
      }else{
          $searchPatType=0;
      } 
        
      // 查询词5，'searchWriter'
      if(!empty($request->param('searchWriter'))){
          $searchWriter=$request->param('searchWriter');
      }else{
          $searchWriter='';
      }
      
      // 选择排序字段
      switch($sortName){
      
        case '_PATNAME':
          $strOrder='abstract';
        break;
        
        case '_PATSTATUS':
          $strOrder='abstract';
        break;
            
        case '_ABSTRACT':
          $strOrder='abstract';
        break;
        
        case '_WRITER':
          $strOrder='writer';
        break;
        
        case '_EXECUTER':
          $strOrder='executer';
        break;
            
        case '_ADDNEWDATE':
          $strOrder='addnewdate';
        break;
        
        case '_STATUS':
          $strOrder='status';
        break;
            
        case '_DEPT':
          $strOrder='dept';
        break;
            
        case '_TOPIC':
          $strOrder='topic';  
          $sortName="_TOPIC";
        break;
        
         //默认按字段“status”
        default:
          $strOrder='status';  
          $sortName="_OPERATION";
        break;
        
      } 
      
      //  组合升序or降序查询
      if($sort=="_ASC"){
          $strOrder=$strOrder.' asc';
      }else{
          $strOrder=$strOrder.' desc';
          
      }
      
     //使用模型Issinfo
     $issSet = new IssinfoModel; 
     $mapMaintain['status'] =['in',['申报复核','申报提交','续费提交','准予续费',
                                      '否决申报','专利授权','专利驳回','放弃续费','续费授权']];
     
     // 记录总数
     $numTotal = $issSet->where($mapMaintain)->count();
          
     // 查出所有的用户并分页，根据“strOrder”排序，前端页面显示的锚点（hash值）为$fragment，设定分页页数变量：“pageTotalNum”
     // 带上每页显示记录行数$totalTableRows，实现查询结果分页显示。
     $issPatTotal = $issSet->where($mapMaintain)
                            ->order($strOrder)
                            ->paginate($issPatTableRows,false,['type'=>'bootstrap','var_page' => 'pageTotalNum',
                            'query'=>['issPatTableRows'=>$issPatTableRows]]);
     // 获取分页显示
     $pageTotal = $issPatTotal->render();
       
     //返回数据还是模板文件,‘0’为模板文件，‘1’为数据
      if($returnType){
        //响应前端的请求，返回前端要求条件的issPat数量
        return ($numTotal);
      }else{
        $this->assign([
              'home'=>$request->domain(),
              //"destr".$this->auth['isspro']['edit'],
              
              // 分页显示所需参数
              'issPatTotal'=>$issPatTotal,
              'numTotal'=>$numTotal,
              'pageTotal'=>$pageTotal,
              'issPatTableRows'=>$issPatTableRows,
              'pageTotalNum'=>$pageTotalNum,
              
              // 表格搜索字段
              'searchPatName'=>$searchPatName,
              'searchDept'=>$searchDept,
              'searchPatStatus'=>$searchPatStatus,
              'searchPatType'=>$searchPatType,
              'searchWriter'=>$searchWriter,
        
              // 表格排序信息
              'sortName'=>$sortName,
              'sort'=>$sort,             
              
        ]);
        
        return view();
      }
    }
    
    // 输出模板文件，显示issue中与pat相关的数据集。
     public function issPatDone(Request $request)
    {
      $this->_loginUser();
      
       // $returnType接收前端页面传来的returnType值，‘0’为模板文件，‘1’为数据
      if(!empty($request->param('returnType'))){
        $returnType=$request->param('returnType');
      }else{
        $returnType=0;
      }

      //$totalTableRows接收前端页面传来的分页时每页显示的记录数，默认为10
      if(!empty($request->param('issPatTableRows'))){
          $issPatTableRows=$request->param('issPatTableRows');
      }else{
          $issPatTableRows=10;
      }
      
       // 接收前端分页页数变量：“pageTotalNum”
      if(!empty($request->param('pageTotalNum'))){
          $pageTotalNum=$request->param('pageTotalNum');
      }else{
          $pageTotalNum=1;
      }
      
      // $sortName接收前端页面传来的排序字段名
      if(!empty($request->param('sortName'))){
          $sortName=$request->param('sortName');
      }else{
          $sortName='_TOPIC';
      }
        
      // $sort接收前端页面传来的排序顺序
      if(!empty($request->param('sort'))){
          $sort=$request->param('sort');
      }else{
          $sort='_ASC';
      }
      
      // 查询词1，'searchPatName'
      if(!empty($request->param('searchPatName'))){
          $searchPatName=$request->param('searchPatName');
      }else{
          $searchPatName='';
      } 
        
      // 查询词2，'searchDept'
      if(!empty($request->param('searchDept'))){
          $searchDept=$request->param('searchDept');
      }else{
          $searchDept=0;
      } 
        
      // 查询词3，'searchPatStatus'
      if(!empty($request->param('searchPatStatus'))){
          $searchPatStatus=$request->param('searchPatStatus');
      }else{
          $searchPatStatus=0;
      }
        
      // 查询词4，'searchPatType'
      if(!empty($request->param('searchPatType'))){
          $searchPatType=$request->param('searchPatType');
      }else{
          $searchPatType=0;
      } 
        
      // 查询词5，'searchWriter'
      if(!empty($request->param('searchWriter'))){
          $searchWriter=$request->param('searchWriter');
      }else{
          $searchWriter='';
      }
      
      // 选择排序字段
      switch($sortName){
      
        case '_PATNAME':
          $strOrder='abstract';
        break;
        
        case '_PATSTATUS':
          $strOrder='abstract';
        break;
            
        case '_ABSTRACT':
          $strOrder='abstract';
        break;
        
        case '_WRITER':
          $strOrder='writer';
        break;
        
        case '_EXECUTER':
          $strOrder='executer';
        break;
            
        case '_ADDNEWDATE':
          $strOrder='addnewdate';
        break;
        
        case '_STATUS':
          $strOrder='status';
        break;
            
        case '_DEPT':
          $strOrder='dept';
        break;
            
        case '_TOPIC':
          $strOrder='topic';  
          $sortName="_TOPIC";
        break;
        
         //默认按字段“status”
        default:
          $strOrder='status';  
          $sortName="_OPERATION";
        break;
        
      } 
      
      //  组合升序or降序查询
      if($sort=="_ASC"){
          $strOrder=$strOrder.' asc';
      }else{
          $strOrder=$strOrder.' desc';
          
      }
      
     //使用模型Issinfo
     $issSet = new IssinfoModel; 
     $mapDone['status'] ='完结';
     
     // 记录总数
     $numTotal = $issSet->where($mapDone)->count();
          
     // 查出所有的用户并分页，根据“strOrder”排序，前端页面显示的锚点（hash值）为$fragment，设定分页页数变量：“pageTotalNum”
     // 带上每页显示记录行数$totalTableRows，实现查询结果分页显示。
     $issPatTotal = $issSet->where($mapDone)
                            ->order($strOrder)
                            ->paginate($issPatTableRows,false,['type'=>'bootstrap','var_page' => 'pageTotalNum',
                            'query'=>['issPatTableRows'=>$issPatTableRows]]);
     // 获取分页显示
     $pageTotal = $issPatTotal->render();
       
     //返回数据还是模板文件,‘0’为模板文件，‘1’为数据
      if($returnType){
        //响应前端的请求，返回前端要求条件的issPat数量
        return ($numTotal);
      }else{
        $this->assign([
              'home'=>$request->domain(),
              //"destr".$this->auth['isspro']['edit'],
              
              // 分页显示所需参数
              'issPatTotal'=>$issPatTotal,
              'numTotal'=>$numTotal,
              'pageTotal'=>$pageTotal,
              'issPatTableRows'=>$issPatTableRows,
              'pageTotalNum'=>$pageTotalNum,
              
              // 表格搜索字段
              'searchPatName'=>$searchPatName,
              'searchDept'=>$searchDept,
              'searchPatStatus'=>$searchPatStatus,
              'searchPatType'=>$searchPatType,
              'searchWriter'=>$searchWriter,
        
              // 表格排序信息
              'sortName'=>$sortName,
              'sort'=>$sort,             
              
        ]);
        
        return view();
      }
    }


?>