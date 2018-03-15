<?php

/**
 * @author Prince
 * @copyright 2018
 */

   //根据前端传来的操作类型，对数据库进行操作
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
      
     //如果要获取的数据为数组，要加上 /a 修饰符才能正确获取。
      if(!empty($request->param('attId/a'))){
        $arrAttId=$request->param('attId/a');
        $arrAttFileName=$request->param('attFileName/a');
      }else{
        $arrAttId=array(0);
        $arrAttFileName=array(0);
      }
      
      //变量赋初值
      $issData=array();
      $issRdData=array();
      $patData=array();
      $patRdData=array();
      $attData=array();
      
      $issMdlOprt='';
      $patMdlOprt='';
      $attMdlOprt='';
      
      $oprtCHNStr='';
      
      $msg="";
      $tplFile='dashboard2'.DS.'issPatAuthSingle'.DS;
      
      if($oprt=='_ADDNEW'){
        //patId=0,issId=0
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
                
//                'keyword'=>$request->param(''),
//                'summary'=>$request->param(''),
//                'applyplace'=>$request->param(''),
//                'pronum'=>$request->param(''),
//                'note'=>$request->param(''),
//                
          );
          //1. 新增pat,返回新增的patId                   
          $patId = $patMdl->patCreate($patData);
          if ($patId) {
            $msg.='专利【新增】成功。<br>';
            $patSet=$patMdl->where('id',$patId)->find();
            
            $patRdData=array(
                'patinfo_id'=>$patId,
                'num'=>$patSet->patnum,
                'act'=>'填报',
                'actdetail'=>'专利《'.$patSet->topic.'》新增填报',
                'acttime'=>$this->now,
                'username'=>$this->username,
                'rolename'=>$auth,
                
            );
            //2.由patId新增patRd
            $patRdId = $patRdMdl->patRdCreate($patRdData);
            
            $issData=array(
                'issmap_type'=>$request->param('issType'),
                'topic'=>$request->param('issPatTopic'),
                'abstract'=>$request->param('issPatAbstract'),
                
                'issmap_id'=>$patId,
                'addnewdate'=>$this->now(),
                'status'=>'填报',
                'writer'=>$this->username,
                'dept'=>$this->dept,
        
            );
            //2.由patId新增issPat,返回新增issId
            $issId = $issMdl->issCreate($issData);  
            
            $issRdData=array(
                
                'topic'=>$request->param('patTopic'),
                'pattype'=>$request->param('patType'),
                'status'=>'填报',
                
            );
            //3.由issId新增issRd
            $issRdId = $issRdMdl->issRdCreate($issRdData);
            
            //4.循环更新attMdl,将文件从现有的‘temp’目录移动到指定目录
            for($i=0;$i<count($arrAttId);$i++){
              $issSet = $issMdl->where('id',$issId)->find();            
              
              $fileName=$arrAttFileName[$i];
              $targetDir=ROOT_PATH.DS.'uploads'.DS.$issSet->issnum;
              //将文件从‘temp’目录移动到指定目录
              $fileMoveResult=$this->_moveAtt($fileName,$targetDir,$arrAttId[$i]);
              //移动是否成功：
              if($fileMoveResult){
                $attData=array(
                  'num_id'=>$issSet->issnum,
                  'attmap_id'=>$issSet->id,
                  'path'=>$targetDir
                );
                //更新att
                $attId = $attMdl->attUpdate($attData,$arrAttId[$i]);
                
              }else{
                $msg.="附件".$arrAttFileName[$i]."移动失败<br>"; 
              }
            }
          }else {
            $msg.='专利【新增】失败。<br>';
          }
          
      }else if($oprt=='_ADDRENEW'){
        //issId=0
        
      }else{
        
        
      }
      
            
      switch($oprt){
        //“_EDIT”权限拥有的操作
        case'_ADDNEW':
          //patId=0,issId=0
          $issData=array(
                'topic'=>$request->param('issPatTopic'),
                'type'=>$request->param('issType'),
                'abstract'=>$request->param('issPatAbstract')
          );
          $issMdlOprt='_CREAT';
          
          
          
          $attData=array(
                ''=>$request->param(''),
                ''=>$request->param('')
          );
          $attMdlOprt='_UPDATE';
          
         // ''=>$request->param(''),
          
          $tplFile.='editSingle';
          $oprtCN='新增';
          
        break;
        
        case'_SUBMIT':
          $issData=array(
                ''=>$request->param(''),
                
          );
          $issMdlOprt='_UPDATE';
          
          $patData=array(
                ''=>$request->param(''),
                
          );
          $patMdlOprt='_UPDATE';
          
          $attData=array(
                ''=>$request->param(''),
        
          );
          $attMdlOprt='_CREAT';
        
          $tplFile.='editSingle';
          $oprtCN='提交';
          
        break;
        
        case'_DELETE':
          $issData=array(
                ''=>$request->param(''),
                
          );
          $issMdlOprt='_DELETE';
          
          $patData=array(
                ''=>$request->param(''),
                
          );
          $patMdlOprt='_DELETE';
          
          $attData=array(
                ''=>$request->param(''),
        
          );
          $attMdlOprt='_DELETE';
        
          $tplFile.='editSingle';
          $oprtCN='删除';
        break;
        
        case'_UPDATE':
          $issData=array(
                ''=>$request->param(''),
                
          );
          $issMdlOprt='_UPDATE';
          
          $patData=array(
                ''=>$request->param(''),
                
          );
          $patMdlOprt='_UPDATE';
          
          $attData=array(
                ''=>$request->param(''),
        
          );
          $attMdlOprt='_CREAT';
        
          $tplFile.='editSingle';
          $oprtCN='更新';
        break;
        //“_AUDIT”权限拥有的操作
        case'_PASS':
          $issData=array(
                ''=>$request->param(''),
                
          );
          $issMdlOprt='_UPDATE';
          
          $patData=array(
                ''=>$request->param(''),
                
          );
          $patMdlOprt='_UPDATE';
          
          $attData=array(
                ''=>$request->param(''),
        
          );
          $attMdlOprt='_CREAT';
        
          $tplFile.='auditSingle';
          $oprtCN='审核通过';
        break;
        
        case'_FAIL':
          $issData=array(
                ''=>$request->param(''),
                
          );
          $issMdlOprt='_UPDATE';
          
          $patData=array(
                ''=>$request->param(''),
                
          );
          $patMdlOprt='_UPDATE';
          
          $attData=array(
                ''=>$request->param(''),
        
          );
          $attMdlOprt='_CREAT';
        
          $tplFile.='auditSingle';
          $oprtCN='审核未通过';
         
        break;
        
        case'_MODIFY':
          $issData=array(
                ''=>$request->param(''),
                
          );
          $issMdlOprt='_UPDATE';
          
          $patData=array(
                ''=>$request->param(''),
                
          );
          $patMdlOprt='_UPDATE';
          
          $attData=array(
                ''=>$request->param(''),
        
          );
          $attMdlOprt='_CREAT';
        
          $tplFile.='auditSingle';
          $oprtCN='返回修改';
         
        break;
        //“_APPROVE”权限拥有的操作
        case'_PERMIT':
          $issData=array(
                ''=>$request->param(''),
                
          );
          $issMdlOprt='_UPDATE';
          
          $patData=array(
                ''=>$request->param(''),
                
          );
          $patMdlOprt='_UPDATE';
          
          $attData=array(
                ''=>$request->param(''),
        
          );
          $attMdlOprt='_CREAT';
        
          $tplFile.='auditSingle';
          $oprtCN='批准';
          
        break;
        
        case'_VETO':
          $issData=array(
                ''=>$request->param(''),
                
          );
          $issMdlOprt='_UPDATE';
          
          $patData=array(
                ''=>$request->param(''),
                
          );
          $patMdlOprt='_UPDATE';
          
          $attData=array(
                ''=>$request->param(''),
        
          );
          $attMdlOprt='_CREAT';
        
          $tplFile.='auditSingle';
          $oprtCN='否决';
          
        break;
        
        case'_COMPLETE':
          $issData=array(
                ''=>$request->param(''),
                
          );
          $issMdlOprt='_UPDATE';
          
          $patData=array(
                ''=>$request->param(''),
                
          );
          $patMdlOprt='_UPDATE';
          
          $attData=array(
                ''=>$request->param(''),
        
          );
          $attMdlOprt='_CREAT';
        
          $tplFile.='auditSingle';
          $oprtCN='修改完善';
        break;
        //“_EXECUTE”权限拥有的操作
        case'_ACCEPT':
          $issData=array(
                ''=>$request->param(''),
                
          );
          $issMdlOprt='_UPDATE';
          
          $patData=array(
                ''=>$request->param(''),
                
          );
          $patMdlOprt='_UPDATE';
          
          $attData=array(
                ''=>$request->param(''),
        
          );
          $attMdlOprt='_CREAT';
        
          $tplFile.='auditSingle';
          $oprtCN='领受';
        break;
        
        case'_REFUSE':
          $issData=array(
                ''=>$request->param(''),
                
          );
          $issMdlOprt='_UPDATE';
          
          $patData=array(
                ''=>$request->param(''),
                
          );
          $patMdlOprt='_UPDATE';
          
          $attData=array(
                ''=>$request->param(''),
        
          );
          $attMdlOprt='_CREAT';
        
          $tplFile.='auditSingle';
          $oprtCN='申述';
          
        break;
        
        case'_REPORT':
          $issData=array(
                ''=>$request->param(''),
                
          );
          $issMdlOprt='_UPDATE';
          
          $patData=array(
                ''=>$request->param(''),
                
          );
          $patMdlOprt='_UPDATE';
          
          $attData=array(
                ''=>$request->param(''),
        
          );
          $attMdlOprt='_CREAT';
        
          $tplFile.='auditSingle';
          $oprtCN='执行报告';
          
        break;
        
        case'_FINISH':
          $issData=array(
                ''=>$request->param(''),
                
          );
          $issMdlOprt='_UPDATE';
          
          $patData=array(
                ''=>$request->param(''),
                
          );
          $patMdlOprt='_UPDATE';
          
          $attData=array(
                ''=>$request->param(''),
        
          );
          $attMdlOprt='_CREAT';
        
          $tplFile.='auditSingle';
          $oprtCN='执行完成';
          
        break;
        //“_MAINTAIN”权限拥有的操作
        case'_APPLY':
          $issData=array(
                ''=>$request->param(''),
                
          );
          $issMdlOprt='_UPDATE';
          
          $patData=array(
                ''=>$request->param(''),
                
          );
          $patMdlOprt='_UPDATE';
          
          $attData=array(
                ''=>$request->param(''),
        
          );
          $attMdlOprt='_CREAT';
        
          $tplFile.='auditSingle';
          $oprtCN='申报';
          
        break;
        
        case'_IMPROVE':
          $issData=array(
                ''=>$request->param(''),
                
          );
          $issMdlOprt='_UPDATE';
          
          $patData=array(
                ''=>$request->param(''),
                
          );
          $patMdlOprt='_UPDATE';
          
          $attData=array(
                ''=>$request->param(''),
        
          );
          $attMdlOprt='_CREAT';
        
          $tplFile.='auditSingle';
          $oprtCN='申报修改';
          
        break;
        
        case'_AUTHORIZE':
          $issData=array(
                ''=>$request->param(''),
                
          );
          $issMdlOprt='_UPDATE';
          
          $patData=array(
                ''=>$request->param(''),
                
          );
          $patMdlOprt='_UPDATE';
          
          $attData=array(
                ''=>$request->param(''),
        
          );
          $attMdlOprt='_CREAT';
        
          $tplFile.='auditSingle';
          $oprtCN='授权';
          
        break;
        
        case'_REJECT':
          $issData=array(
                ''=>$request->param(''),
                
          );
          $issMdlOprt='_UPDATE';
          
          $patData=array(
                ''=>$request->param(''),
                
          );
          $patMdlOprt='_UPDATE';
          
          $attData=array(
                ''=>$request->param(''),
        
          );
          $attMdlOprt='_CREAT';
        
          $tplFile.='auditSingle';
          $oprtCN='驳回';
          
        break;
        
        case'_CLOSE':
          $issData=array(
                ''=>$request->param(''),
                
          );
          $issMdlOprt='_UPDATE';
          
          $patData=array(
                ''=>$request->param(''),
                
          );
          $patMdlOprt='_UPDATE';
          
          $attData=array(
                ''=>$request->param(''),
        
          );
          $attMdlOprt='_CREAT';
        
          $tplFile.='auditSingle';
          $oprtCN='关闭';
          
        break;
        
        case'_ADDRENEW':
          
          if($request->param('returnType')=='_JSON'){
            return json(array_merge($patMdl->where('id',$request->param('patId'))->find()->toArray(),
                              array("today"=>date('Y-m-d'),"username"=>$this->username,"deptMaintainer"=>$this->dept)));
          }else{
            $msg='<div style="padding: 24px 48px;"><h1>:)</h1><p>'.$oprt.'模块开发中……<br/></p></div>';
            $issData=array(
                ''=>$request->param(''),
                
            );
            $issMdlOprt='_CREAT';
            
            $patData=array(
                  ''=>$request->param(''),
                  
            );
            $patMdlOprt='_UPDATE';
            
            $attData=array(
                  ''=>$request->param(''),
          
            );
            $attMdlOprt='_CREAT';
          
            $tplFile.='auditSingle';
            $oprtCN='新增';
          }
          
        break;
        
        //
        
      }
      
      //引用patinfo模型中定义的方法向patinfo表更新信息
      switch( $patMdlOprt){
        case '_CREAT':
          $patId = $patMdl->patCreate($patData);
          if ($patId) {
            $msg.='专利【新增】成功。<br>';
          }else {
            $msg.='专利【新增】失败。<br>';
          }
        break;
        
        case '_UPDATE':
          $patId = $patMdl->patUpdate($patData);
          if ($patId) {
            $msg.='专利【更新】完成。<br>';
          }else {
            $msg.='专利信息无变化，无需【更新】。<br>';
          }
        break;
        
        case '_DELETE':
          $patId = $patMdl->patDelete($patData);
          if ($patId) {
            $msg.='专利【删除】成功。<br>';
          }else {
            $msg.='专利【删除】失败。<br>';
          }
        break;
        
      }
      
      //引用Issinfo模型中定义的方法向issinfo表更新信息
      switch($issMdlOprt){
        case '_CREAT':
          $issId = $issMdl->issCreate($issData);
          if ($issId) {
            $msg.='专利事务【'.$oprtCN.'】成功。<br>';
          }else {
            $msg.='专利事务【'.$oprtCN.'】失败。<br>';
          }
        break;
        
        case '_UPDATE':
          $issId = $issMdl->issUpdate($issData);
          if ($issId) {
            $msg.='专利事务【'.$oprtCN.'】完成。<br>';
          }else {
            $msg.='专利事务【'.$oprtCN.'】无变化，无需更新。<br>';
          }
        break;
        
        case '_DELETE':
          $issId = $issMdl->issDelete($issData);
          if ($issId) {
            $msg.='专利事务【'.$oprtCN.'】成功。<br>';
          }else {
            $msg.='专利事务【'.$oprtCN.'】失败。<br>';
          }
        break;
        
      }
      
      //引用attinfo模型中定义的方法向attinfo表新增信息
      switch( $attMdlOprt){
        case '_CREAT':
          $attId = $attMdl->attCreate($attData);
          if ($attId) {
            $msg.='专利事务附件【上传】成功。<br>';
          }else {
            $msg.='专利事务附件【上传】失败。<br>';
          }
        break;
        
        //case '_UPDATE':
//          $attId = $attMdl->attUpdate($attData);
//          if ($attId) {
//            $msg.='专利事务附件'.$oprtCN.'完成。<br>';
//          }else {
//            $msg.='专利事务附件'.$oprtCN.'无变化，无需更新。<br>';
//          }
//        break;
        
        case '_DELETE':
          $attId = $attMdl->attDelete($attData);
          if ($attId) {
            $msg.='专利事务附件【删除】成功。<br>';
          }else {
            $msg.='专利事务附件【删除】失败。<br>';
          }
        break;
        
      }
      
      //return $msg;
      //return json(array('msg'=>$msg,'btnHtml'=>$btnHtml,'topic'=>$request->param('issPatTopic')));
      return json(array('msg'=>$msg,'topic'=>$request->param('issPatTopic'),'patId'=>$patId));
      //return $this->issPatAuth($request);//参数不够，不会产生分页。
    }


?>