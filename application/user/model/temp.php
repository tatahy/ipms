<?php

/**
 * @author tatahy
 * @copyright 2018
 */

   //根据前端传来的操作类型，对5个数据表进行操作
    function issPatOprt(Request $request,IssinfoModel $issMdl,IssrecordModel $issRdMdl,
                                PatinfoModel $patMdl,PatrecordModel $patRdMdl,
                                AttinfoModel $attMdl)
    {
      $this->_loginUser();
      
      // $oprt接收前端页面传来的oprt值
      if(!empty($request->param('oprt'))){
        $oprt=$request->param('oprt');
      }else{
        $oprt='_NONE';
      }
      
      // $auth接收前端页面传来的auth值
      if(!empty($request->param('auth'))){
        $auth=$request->param('auth');
      }else{
        $auth='_NONE';
      }
      
      // $patId接收前端页面传来的patId值
      if(!empty($request->param('patId'))){
        $patId=$request->param('patId');
      }else{
        $patId=0;
      }
      
      // $issId接收前端页面传来的issId值
      if(!empty($request->param('issId'))){
        $issId=$request->param('issId');
      }else{
        $issId=0;
      }
      
     //如果要通过$request->param()获取的数据为数组，要加上 /a 修饰符才能正确获取。
      if(!empty($request->param('attId/a'))){
        $arrAttId=$request->param('attId/a');
        $arrAttFileName=$request->param('attFileName/a');
      }else{
        $arrAttId=array(0);
        $arrAttFileName=array(0);
      }
      
      //变量赋初值
      $issData=array(0);
      $issDataPatch=array(0);
      $issRdData=array(0);
      $issRdDataPatch=array(0);
      $issId_return=0;
      
      $patData=array(0);
      $patDataPatch=array(0);
      $patRdData=array(0);
      $patRdDataPatch=array(0);
      $patId_return=0;
      
      $attData=array(0);
      $attDataPatch=array(0);
      $attId_return=0;
      
     // $issMdlOprt='';
//      $patMdlOprt='';
//      $attMdlOprt='';
      
      $oprtCHNStr='';
      
      $msg="";
      //$tplFile='dashboard2'.DS.'issPatAuthSingle'.DS;
      
      switch($oprt){
        //“_EDIT”权限拥有的操作
        case'_ADDNEW':
          //patId=0,issId=0
          $oprtCHNStr='新增';
          
          $patDataPatch=array('z'=>0);
          $patRdDataPatch=array('z'=>0);
          $issDataPatch=array('z'=>0);
          $issRdDataPatch=array('z'=>0);
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        
        case'_SUBMIT':
          //patId!=0,issId!=0
          $oprtCHNStr='提交';
          
          $patDataPatch=array('status'=>'内审',
                              'submitdate'=>$this->now,
                              );
          $patRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利《'.$request->param('patTopic').'》提交。待内部审查<br>',
                                );
          $issDataPatch=array('status'=>'待审核',
                              'submitdate'=>$this->now,
                              );
          $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》提交审核。',
                                );
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        
        case'_DELETE':
          //patId!=0,issId!=0
          $oprtCHNStr='删除';
        break;
        
        case'_UPDATE':
          //patId!=0,issId!=0
          $oprtCHNStr='更新';
          
          $patDataPatch=array('z'=>0);
          $patRdDataPatch=array('z'=>0);
          $issDataPatch=array('z'=>0);
          $issRdDataPatch=array('z'=>0);
          $attDataPatch=array('z'=>0);
          
        break;
        //“_AUDIT”权限拥有的操作
        case'_PASS':
          //patId!=0,issId!=0
          $oprtCHNStr='审核';
          
          $patDataPatch=array('z'=>0);
          $patRdDataPatch=array('z'=>0);
          $issDataPatch=array('status'=>'审核通过',
                              'auditrejectdate'=>$this->now,
                              );
          $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》审核结果：</br>
                                              <span class="label label-success">审核通过</span></br>'
                                );
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        
        case'_FAIL':
          //patId!=0,issId!=0
          $oprtCHNStr='审核';
          
          $patDataPatch=array('z'=>0);
          $patRdDataPatch=array('z'=>0);
          $issDataPatch=array('status'=>'审核未通过',
                              'auditrejectdate'=>$this->now,
                              );
          $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》审核结果：</br>
                                              <span class="label label-warning">审核未通过</span></br>
                                              审核意见：<span class="label label-primary">'.$request->param('auditMsg').'</span></br>',
                                );
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        
        case'_MODIFY':
          //patId!=0,issId!=0
          $oprtCHNStr='审核';
          
          $patDataPatch=array('status'=>'内审修改');
          $patRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利《'.$request->param('patTopic').'》审核结果：</br>
                                              <span class="label label-primary">内审修改</span></br>
                                              审核意见：<span class="label label-primary">'.$request->param('auditMsg').'</span></br>',
                                );
          $issDataPatch=array('status'=>'返回修改',
                              'auditrejectdate'=>$this->now,
                              );
          $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》审核结果：</br>
                                              <span class="label label-primary">返回修改</span></br>
                                              审核意见：<span class="label label-primary">'.$request->param('auditMsg').'</span></br>',
                                );
          $attDataPatch=array('deldisplay'=>0);
         
        break;
        //“_APPROVE”权限拥有的操作
        case'_PERMIT':
          //patId!=0,issId!=0
          $oprtCHNStr='审批';
          //根据iss.status的值进行赋值
          $issSet=$issMdl->where('id',$issId)->find();
          
          if($issSet->status=='审核通过' || $issSet->status=='审核未通过' ){
            $patDataPatch=array('status'=>'拟申报(内审批准)',
                                'auditrejectdate'=>$this->now,
                                );
            $patRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利《'.$request->param('patTopic').'》审批结果：</br>
                                              <span class="label label-success">拟申报(内审批准)</span></br>'
                                  );
            $issDataPatch=array('status'=>'批准申报');
            $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》审批结果：</br>
                                              <span class="label label-success">批准申报</span></br>'
                                  );
            
          }else if($issSet->status=='变更申请'){
            $patDataPatch=array('z'=>0);
            $patRdDataPatch=array('z'=>0);
            $issDataPatch=array('status'=>'准予变更',
                                'auditrejectdate'=>$this->now,
                                );
            $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》审批结果：</br>
                                              <span class="label label-success">准予变更</span></br>'
                                  );
          }else{
            //$issSet->status=='拟续费'
            $patDataPatch=array('z'=>0);
            $patRdDataPatch=array('z'=>0);
            $issDataPatch=array('status'=>'准予续费',
                                'auditrejectdate'=>$this->now,
                                );
            $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》审批结果：</br>
                                              <span class="label label-success">准予续费</span></br>'
                                  );
          }
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        
        case'_VETO':
          //patId!=0,issId!=0
          $oprtCHNStr='审批';
          
           //根据iss.status的值进行赋值
          $issSet=$issMdl->where('id',$issId)->find();
          
          if($issSet->status=='审核通过' || $issSet->status=='审核未通过' ){
            $patDataPatch=array('status'=>'内审否决');
            $patRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利《'.$request->param('patTopic').'》审批结果：</br>
                                              <span class="label label-danger">内审否决</span></br>
                                              审批意见：<span class="label label-primary">'.$request->param('approveMsg').'</span></br>'
                                  );
            $issDataPatch=array('status'=>'否决申报',
                                'auditrejectdate'=>$this->now,
                                );
            $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》审批结果：</br>
                                              <span class="label label-danger">否决申报</span></br>
                                              审批意见：<span class="label label-primary">'.$request->param('approveMsg').'</span></br>'
                                  );
            
          }else if($issSet->status=='变更申请'){
            $patDataPatch=array('z'=>0);
            $patRdDataPatch=array('z'=>0);
            $issDataPatch=array('status'=>'否决变更',
                                'auditrejectdate'=>$this->now,
                                );
            $issRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》审批结果：</br>
                                              <span class="label label-danger">否决变更</span></br>
                                              审批意见：<span class="label label-primary">'.$request->param('approveMsg').'</span></br>'
                                  );
          }else{
            //$issSet->status=='拟续费'
            $patDataPatch=array('status'=>'放弃续费');
            $patRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利《'.$request->param('patTopic').'》续费审批结果：</br>
                                              <span class="label label-default">放弃续费</span></br>
                                              审批意见：<span class="label label-default">'.$request->param('approveMsg').'</span></br>'
                                  );
            $issDataPatch=array('status'=>'放弃续费',
                                'auditrejectdate'=>$this->now,
                                );
            $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》审批结果：</br>
                                              <span class="label label-default">放弃续费</span></br>
                                              审批意见：<span class="label label-default">'.$request->param('approveMsg').'</span></br>'
                                  );
          }
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        
        case'_COMPLETE':
          //patId!=0,issId!=0
         $oprtCHNStr='审批';
          
          //根据iss.status的值进行赋值
          //$issSet=$issMdl->where('id',$issId)->find();
          
          //$issSet->status=='审核通过' || $issSet->status=='审核未通过' ){
          $patDataPatch=array('status'=>'内审修改');
          $patRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利《'.$request->param('patTopic').'》审批结果：</br>
                                              <span class="label label-warning">内审修改</span></br>
                                              审批意见：<span class="label label-primary">'.$request->param('approveMsg').'</span></br>'
                                  );
          $issDataPatch=array('status'=>'修改完善',
                                'auditrejectdate'=>$this->now,
                                );
          $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》审批结果：</br>
                                              <span class="label label-warning">修改完善</span></br>
                                              审批意见：<span class="label label-primary">'.$request->param('approveMsg').'</span></br>'
                                  );
            
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        //“_EXECUTE”权限拥有的操作
        case'_ACCEPT':
          //patId!=0,issId!=0
          $oprtCHNStr='领受';
          
          $patDataPatch=array('z'=>0);
          $patRdDataPatch=array('z'=>0);
          $issDataPatch=array('status'=>'申报执行',
                              'operatestartdate'=>$this->now,
                              );
          $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》申报开始</br>'
                                );
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        
        case'_REFUSE':
          //patId!=0,issId!=0
          $oprtCHNStr='变更申述';
          
          $patDataPatch=array('z'=>0);
          $patRdDataPatch=array('z'=>0);
          $issDataPatch=array('status'=>'变更申请',
                              'executerchangeto'=>$this->username,
                              );
          $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》申报变更申请</br>
                                变更申请意见：<span class="label label-warning">'.$request->param('executeMsg').'</span></br>'
                                );
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        
        case'_REPORT':
          //patId!=0,issId!=0
          $oprtCHNStr='申报执行报告';
          
          $patDataPatch=array('z'=>0);
          $patRdDataPatch=array('z'=>0);
          $issDataPatch=array('z'=>0);
          $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》申报执行报告</br>
                                报告简述：<span class="label label-primary">'.$request->param('executeMsg').'</span></br>'
                                );
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        
        case'_FINISH':
          //patId!=0,issId!=0
          $oprtCHNStr='申报提交复核';
          
          $patDataPatch=array('z'=>0);
          $patRdDataPatch=array('z'=>0);
          $issDataPatch=array('status'=>'申报复核');
          $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》申报提交复核</br>
                                提交内容简述：<span class="label label-primary">'.$request->param('executeMsg').'</span></br>'
                                );
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        //“_MAINTAIN”权限拥有的操作
        case'_APPLY':
          //patId!=0,issId!=0
          $oprtCHNStr='<span class="label label-primary">申报正式提交</span>';
          
           //根据iss.status的值进行赋值
          $issSet=$issMdl->where('id',$issId)->find();
          
          if($issSet->status=='申报复核'){
            $patDataPatch=array('status'=>'申报',
                                'applydate'=>$this->now,
                                );
            $patRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利《'.$request->param('patTopic').'》申报提交</br>
                                                申报提交简述：<span class="label label-primary">'.$request->param('maintainMsg').'</span></br>'
                                  );
            $issDataPatch=array('status'=>'申报提交',
                                'applydate'=>$this->now,
                                );
            $issRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》申报提交</br>
                                                申报提交简述：<span class="label label-primary">'.$request->param('maintainMsg').'</span></br>'
                                  );
            
          }else{
            //$issSet->status=='准予续费'
            $patDataPatch=array('status'=>'续费中',
                                'renewapplydate'=>$this->now,
                                );
            $patRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利《'.$request->param('patTopic').'》续费申报提交</br>
                                              申报提交简述：<span class="label label-primary">'.$request->param('maintainMsg').'</span></br>'
                                  );
            $issDataPatch=array('status'=>'续费提交',
                                'applydate'=>$this->now,
                                );
            $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》续费申报提交</br>
                                              申报提交简述：<span class="label label-primary">'.$request->param('maintainMsg').'</span></br>'
                                  );
          }
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        
        case'_IMPROVE':
          //patId!=0,issId!=0
      
           //根据iss.status的值进行赋值
          $issSet=$issMdl->where('id',$issId)->find();
          
          if($issSet->status=='申报复核'){
            $oprtCHNStr='申报修改';
            
            $patDataPatch=array('z'=>0);
            $patRdDataPatch=array('z'=>0);
            $issDataPatch=array('status'=>'申报修改');
            $issRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》申报复核结果：</br>
                                                申报修改</br>
                                                申报修改原因：<span class="label label-primary">'.$request->param('maintainMsg').'</span></br>'
                                  );
            
          }else{
            //$issSet->status=='申报提交'
            $oprtCHNStr='<span class="label label-warning">申报修改</span>';
            
            $patDataPatch=array('status'=>'申报修改',
                                'modifydate'=>$this->now,
                                );
            $patRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利《'.$request->param('patTopic').'》申报提交结果：</br>'.$oprtCHNStr.'</br> 
                                              申报修改原因：<span class="label label-warning">'.$request->param('maintainMsg').'</span></br>'
                                  );
            $issDataPatch=array('status'=>'申报修改',
                                'resultdate'=>$this->now,
                                );
            $issRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》</br>'.$oprtCHNStr.'</br> 
                                              申报修改原因：<span class="label label-warning">'.$request->param('maintainMsg').'</span></br>'
                                  );
          }
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        
        case'_AUTHORIZE':
          //patId!=0,issId!=0
          $oprtCHNStr='<span class="label label-success">授权</span>';
          //根据iss.status的值进行赋值
          $issSet=$issMdl->where('id',$issId)->find();
          
          if($issSet->status=='申报提交'){            
            $patDataPatch=array('status'=>'授权',
                                'authrejectdate'=>$this->now,
                                );
            $patRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利《'.$request->param('patTopic').'》申报提交结果：</br>'.$oprtCHNStr.'</br>'
                                  );
            $issDataPatch=array('status'=>'专利授权',
                                'resultdate'=>$this->now,
                                );
            $issRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》申报提交结果：</br>'.$oprtCHNStr.'</br>'
                                  );
            
          }else{
            //$issSet->status=='续费提交'
            $patDataPatch=array('status'=>'续费授权',
                                //'authrejectdate'=>$request->param('xxDate'),
                                //'nextrenewdate'=>$request->param('xxDate'),
                                //'renewdeadlindate'=>$request->param('xxDate'),
                                );
            $patRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利《'.$request->param('patTopic').'》续费申报提交结果：</br>'.$oprtCHNStr.'</br>'
                                  );
            $issDataPatch=array('status'=>'专利授权',
                                'resultdate'=>$this->now,
                                );
            $issRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》续费申报提交结果：</br>'.$oprtCHNStr.'</br>'
                                  );
          }
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        
        case'_REJECT':
          //patId!=0,issId!=0
          $oprtCHNStr='<span class="label label-danger">驳回</span>';
          //根据iss.status的值进行赋值
          $issSet=$issMdl->where('id',$issId)->find();
          
          if($issSet->status=='申报提交'){            
            $patDataPatch=array('status'=>'驳回',
                                'authrejectdate'=>$this->now,
                                );
            $patRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利《'.$request->param('patTopic').'》申报提交结果：</br>'.$oprtCHNStr.'</br>
                                  驳回原因：<span class="label label-danger">'.$request->param('maintainMsg').'</span></br>'
                                  );
            $issDataPatch=array('status'=>'专利驳回',
                                'resultdate'=>$this->now,
                                );
            $issRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》申报提交结果：</br>'.$oprtCHNStr.'</br>
                                  驳回原因：<span class="label label-danger">'.$request->param('maintainMsg').'</span></br>'
                                  );
          }else{
            //$issSet->status=='续费提交'
            $patDataPatch=array('status'=>'驳回续费',
                                'renewrejectdate'=>$request->param('xxDate'),
                                //'nextrenewdate'=>$request->param('xxDate'),
                                //'renewdeadlindate'=>$request->param('xxDate'),
                                );
            $patRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利《'.$request->param('patTopic').'》续费申报提交结果：</br>'.$oprtCHNStr.'</br>
                                  驳回原因：<span class="label label-danger">'.$request->param('maintainMsg').'</span></br>'
                                  );
            $issDataPatch=array('status'=>'专利驳回',
                                'resultdate'=>$this->now,
                                );
            $issRdDataPatch=array('act'=>$oprtCHNStr,
                                  'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》续费申报提交结果：</br>'.$oprtCHNStr.'</br>
                                  驳回原因：<span class="label label-danger">'.$request->param('maintainMsg').'</span></br>'
                                  );
          }
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        
        case'_CLOSE':
          //patId!=0,issId!=0
          $oprtCHNStr='<span class="label label-default">完结</span>';
          //根据iss.status的值进行赋值
          $issSet=$issMdl->where('id',$issId)->find();
                    
          if($issSet->status=='放弃续费' ){            
            $patDataPatch=array('status'=>'超期无效',
                                'renewabandondate'=>$this->now,
                                );
            $patRdDataPatch=array('act'=>'<span class="label label-default">超期无效</span>',
                                    'actdetail'=>'专利《'.$request->param('patTopic').'》</br><span class="label label-default">超期无效</span></br>'
                                  );
            
          }else{
            //$issSet->status=='放弃续费' || $issSet->status=='续费授权' 
            $patDataPatch=array('z'=>0);
            $patRdDataPatch=array('z'=>0);
           
          }
          $issDataPatch=array('status'=>'完结',
                              'finishdate'=>$this->now,
                              );
          $issRdDataPatch=array('act'=>$oprtCHNStr,
                                'actdetail'=>'专利事务《'.$request->param('issPatTopic').'》'.$oprtCHNStr.'</br>'
                                );
          $attDataPatch=array('deldisplay'=>0);
          
        break;
        
        case'_ADDRENEW':
          //patId!=0,issId=0
          if($request->param('returnType')=='_JSON'){
            return json(array_merge($patMdl->where('id',$request->param('patId'))->find()->toArray(),
                              array("today"=>date('Y-m-d'),"username"=>$this->username,"deptMaintainer"=>$this->dept)));
          }else{
            $oprtCHNStr='续费报告';
            
            $patDataPatch=array('z'=>0);
            $patRdDataPatch=array('z'=>0);
            $issDataPatch=array('z'=>0);
            $issRdDataPatch=array('z'=>0);
            $attDataPatch=array('deldisplay'=>0);
          }
        break;
        
        //
        
      }
      
      //对5个数据表分4类情况进行操作
      if($oprt=='_ADDNEW'){
        //patId=0,issId=0
        //1.patinfo表新增
        $patData=array(
                'topic'=>$request->param('patTopic'),
                'pattype'=>$request->param('patType'),
                'patowner'=>$request->param('patOwner'),
                'inventor'=>$request->param('patInventor'),
                'otherinventor'=>$request->param('patOtherInventor'),
                'author'=>$request->param('patAuthor'),
                'dept'=>$request->param('dept'),
                
                'status'=>'填报',
                'addnewdate'=>$this->now(),
                
          );
        //添加到$patData的内容
        array_push($patData,$patDataPatch);
        //新增                  
        $patId_return = $patMdl->patCreate($patData);
        
        //2.patrecord表新增
        if ($patId_return) {
            $msg.='专利【新增】成功。<br>';
            $patSet=$patMdl->where('id',$patId_return)->find();
            
            $patRdData=array(
                'patinfo_id'=>$patId_return,
                'num'=>$patSet->patnum,
                'act'=>'填报',
                'actdetail'=>'专利《'.$patSet->topic.'》新增填报',
                'acttime'=>$this->now,
                'username'=>$this->username,
                'rolename'=>$auth,
                
            );
            //添加到$patRdData的内容
            arrary_push($patRdData,$patRdDataPatch);
            //新增patRd
            $patRdId = $patRdMdl->patRdCreate($patRdData);
        
        //3.issinfo表新增
            $issData=array(
                    'issmap_type'=>$request->param('issType'),
                    'topic'=>$request->param('issPatTopic'),
                    'abstract'=>$request->param('issPatAbstract'),
                    
                    'issmap_id'=>$patId_return,
                    'addnewdate'=>$this->now(),
                    'status'=>'填报',
                    'writer'=>$this->username,
                    'dept'=>$this->dept,
            
            );
            //添加到$issData的内容
            arrary_push($issData,$issDataPatch);
            //新增issPat
            $issId_return = $issMdl->issCreate($issData); 
        
        //4.issrecord表新增
            //取出新增的isspat内容
            $issSet = $issMdl->where('id',$issId_return)->find();
            $msg.='专利事务【新增】成功。<br>';  
            
            $issRdData=array(
                'issinfo_id'=>$issId_return,
                'num'=>$issSet->issnum,
                'act'=>'填报',
                'actdetail'=>'专利事务《'.$issSet->topic.'》新增填报',
                'acttime'=>$this->now,
                'username'=>$this->username,
                'rolename'=>$auth,               
            );
            //添加到$issRdData的内容
            arrary_push($issRdData,$issRdDataPatch);
            //新增issRd
            $issRdId = $issRdMdl->issRdCreate($issRdData);

          }else {
            $msg.='专利【新增】失败。<br>';
          }
          
      }else if($oprt=='_ADDRENEW'){
        //patId!=0,issId=0
        
        //1.patinfo表无需更新记录

        //2.patrecord表无需新增记录
        
        //3.issinfo表新增记录
        $issData=array(
                'issmap_type'=>$request->param('issType'),
                'topic'=>$request->param('issPatTopic'),
                'abstract'=>$request->param('issPatAbstract'),
                
                'issmap_id'=>$patId_return,
                'addnewdate'=>$this->now(),
                'status'=>'拟续费',
                'writer'=>$this->username,
                'dept'=>$this->dept,
        
        );
        //添加到$issData的内容
        arrary_push($issData,$issDataPatch);   
        //新增
        $issId_return = $issMdl->issCreate($issData);  
        
        //4.issrecord表新增
        //取出新增的isspat内容
        $issSet = $issMdl->where('id',$issId_return)->find();
        $msg.='专利事务【新增续费】成功。<br>';  
            
        $issRdData=array(
                'issinfo_id'=>$issId_return,
                'num'=>$issSet->issnum,
                'act'=>'拟续费',
                'actdetail'=>'专利事务《'.$issSet->topic.'》新增填报',
                'acttime'=>$this->now,
                'username'=>$this->username,
                'rolename'=>$auth,               
        );
        //添加到$issRdData的内容
        arrary_push($issRdData,$issRdDataPatch);   
        //新增
        $issRdId = $issRdMdl->issRdCreate($issRdData);
        
      }else if($oprt=='_DELETE'){
        //$patId!=0,$issId!=0
        //1.删除pat
        $patId_return=$patMdl->patDelete($patId);
        
        //2.删除patRd
        $patRdId_return=$patRdMdl->where('patinfo_id',$patId)->delete();
        
        //3.删除iss
        $issId_return=$issMdl->issDelete($issId);
        
        //4.删除issRd
        $issRdId_return=$issRdMdl->where('issinfo_id',$issId)->delete();
        
        //5.删除att
        $attId_return=$attMdl->where('attmap_id',$issId)->delete();
        
        return json(array('msg'=>$msg,'topic'=>$request->param('issPatTopic'),'patId'=>$patId));
      
      }else{
        //patId!=0,issId!=0
        //1.patinfo表更新
        $patData=array(                
                'status'=>'续费中',
                'renew_createdate'=>$this->now(),
        );
        //添加到$patData的内容
        arrary_push($patData,$patDataPatch);          
        //更新
        $patId_return = $patMdl->patUpdate($patData,$patId);
        
        //2.patrecord表新增
        $patRdData=array(
                'patinfo_id'=>$patId_return,
                'num'=>$patSet->patnum,
                'act'=>'续费',
                'actdetail'=>'专利《'.$patSet->topic.'》续费',
                'acttime'=>$this->now,
                'username'=>$this->username,
                'rolename'=>$auth,
                
        );
        //添加到$patRdData的内容
        arrary_push($patRdData,$patRdDataPatch);  
            
        //新增
        $patRdId = $patRdMdl->patRdCreate($patRdData);
        
        //3.issinfo表更新
        $issData=array(
                'issmap_type'=>$request->param('issType'),
                'topic'=>$request->param('issPatTopic'),
                'abstract'=>$request->param('issPatAbstract'),
                
                'issmap_id'=>$patId_return,
                'addnewdate'=>$this->now(),
                'status'=>'拟续费',
                'writer'=>$this->username,
                'dept'=>$this->dept,
        
        );
        //添加到$issData的内容
        arrary_push($issData,$issDataPatch);
        
        //更新
        $issId_return = $issMdl->issUpdate($issData,$issId); 
        
        //4.issrecord表新增
        //取出新增的isspat内容
        $issSet = $issMdl->where('id',$issId_return)->find();
        $msg.='专利事务【新增续费】成功。<br>';  
            
        $issRdData=array(
                'issinfo_id'=>$issId_return,
                'num'=>$issSet->issnum,
                'act'=>'拟续费',
                'actdetail'=>'专利事务《'.$issSet->topic.'》新增填报',
                'acttime'=>$this->now,
                'username'=>$this->username,
                'rolename'=>$auth,               
        );
        //添加到$issRdData的内容
        arrary_push($issRdData,$ssRdDataPatch);
        
        //新增
        $issRdId = $issRdMdl->issRdCreate($issRdData);
        
      }
      
      //5.attinfo表更新
      //循环更新attMdl,将文件从现有的‘temp’目录移动到指定目录
      for($i=0;$i<count($arrAttId);$i++){
             
        $fileName=$arrAttFileName[$i];
        $targetDir=ROOT_PATH.DS.'uploads'.DS.$issSet->issnum;
                
        //有‘temp’字符串才移动到指定目录
        while(substr_count($fileName,'temp')){
                  
          //引用attinfo模型中定义的fileMove()方法，将文件从‘temp’目录移动到指定目录
          $fileMoveResult=$attMdl->fileMove($fileName,$targetDir,$arrAttId[$i]);
                    
          //移动是否成功：
          if($fileMoveResult){
            $attData=array(
            'num_id'=>$issSet->issnum,
            'attmap_id'=>$issSet->id,
            'path'=>$targetDir,
            //'deldisplay'=>0
            );
            
            //添加到$attData的内容
            arrary_push($attData,$attDataPatch);
            
            //更新att
            $attId = $attMdl->attUpdate($attData,$arrAttId[$i]);
                        
            $msg.="附件".$arrAttFileName[$i]."移动成功<br>"; 
          }else{
            $msg.="附件".$arrAttFileName[$i]."移动失败<br>"; 
          }
        } 
      }
      
//  <----------------------------------------------------------------------------------------->
       
      //return $msg;
      //return json(array('msg'=>$msg,'btnHtml'=>$btnHtml,'topic'=>$request->param('issPatTopic')));
      return json(array('msg'=>$msg,'topic'=>$request->param('issPatTopic'),'patId'=>$patId));
      //return $this->issPatAuth($request);//参数不够，不会产生分页。
    }


?>