<?php
namespace isspatfsm;

use isspatfsm\edit\EditContext;
use isspatfsm\edit\FillingState;
use isspatfsm\audit\AuditContext;
use isspatfsm\approve\ApproveContext;
use isspatfsm\execute\ExecuteContext;
use isspatfsm\maintain\MaintainContext;

/**
 * isspat的FSM（Finite State Machine）
 */ 
class IssPatFSM
{
  
  const ISSPATSTATUS = array('applyCreating','filling','modifying','completing',
                            'checking','auditPassed','auditFailed',
                            'exeChanging','renewPlanning','applyApproved','applyVetoed','exeChApproved','exeChVetoed','renewApproved',
                            'executing','applyModifying','applyReviewing',
                            'applySubmitting','applyAuthorized','applyRejected','renewCreating','renewSubmitting','renewAuthoried','renewVetoed','closed'
                           );
  const ISSPATSTATUSCH=array('申报新增','填报','返回修改','修改完善',
                            '待审核','审核通过','审核未通过',
                            '变更申请','拟续费','批准申报','否决申报','准予变更','否决变更','准予续费',
                            '申报执行','申报修改','申报复核',
                            '申报提交','专利授权','专利驳回','续费新增','续费提交','续费授权','放弃续费','完结'
                            );  
  const ISSPATOPRT=array('_ADDNEW','_SUBMIT','_DELETE','_UPDATE',
                          '_PASS','_FAIL','_MODIFY',
                          '_PERMIT','_VETO','_COMPLETE',
                          '_ACCEPT','_REFUSE','_REPORT','_FINISH',
                          '_APPLY','_REVIEW','_IMPROVE','_AUTHORIZE','_REJECT','_ADDRENEW','_CLOSE'
                          );
  const ISSAUTH=array('_EDIT','_AUDIT','_APPROVE','_EXECUTE','_MAINTAIN');                                                 
  
  private $_context;
  private $_msg;
  private $_errFlag;
  
  private $_auth;
  private $_status;
  private $_oprt;
  private $_data;
  
  public function __construct($auth='',$status='',$oprt='') {
    
    //if(in_array($auth,self::ISSAUTH)?$auth:0){
//      $this->_auth=$auth;
//      $this->_errFlag=0;
//    }else{
//      $this->_msg.='Wrong auth."auth" should be among'.json_encode(self::ISSAUTH).'<br>';
//      $this->_errFlag=1;
//    }
//    
//    if(in_array($status,self::ISSPATSTATUS)?$status:0){
//      $this->_status=$status;
//      $this->_errFlag=0;
//    }else{
//      $this->_msg.='Wrong status."status" should be among'.json_encode(self::ISSPATSTATUS).'<br>';
//      $this->_errFlag=1;
//    }
//    
//    if(in_array($oprt,self::ISSPATOPRT)?$oprt:0){
//      $this->_oprt=$oprt;
//      $this->_errFlag=0;
//    }else{
//      $this->_msg.='Wrong operation."oprt" should be among'.json_encode(self::ISSPATOPRT).'<br>';
//      $this->_errFlag=1;
//    }
    }
    
  //选择FSM，根据$this->_auth确定xxContext()
  private function selectFSM(){
    //$this->_auth=$auth;
    if($this->_errFlag==0){
      switch($this->_auth){
        case '_EDIT':
          $this->_context = new EditContext();
        break;
        case '_AUDIT':
          $this->_context = new AuditContext();
        break;
        case '_APPROVE':
          $this->_context = new ApproveContext();
        break;
        case '_EXECUTE':
          $this->_context = new ExecuteContext();
        break;
        case '_MAINTAIN':
          $this->_context = new MaintainContext();
        break;
      }
      //返回本对象，便于进行链式操作。
      return $this;
    }
  }
  
  //设定FSM的状态，根据$this->_status确定xxState()
  private function setFSMState(){
  
    //$this->_status=in_array($status,self::ISSPATSTATUS)?$status:0;
    
    if($this->_errFlag==0){
      //25个status
      switch($this->_status){
        //case 'applyCreating':
        case '申报新增':
          $this->_context->setState(new ApplyCreatingState());
        break;
        //case 'filling':
        case '填报':
          $this->_context->setState(new FillingState());
        break;
        //case 'modifying':
        case '返回修改':
          $this->_context->setState(new ModifyingState());
        break;
        //case 'completing':
        case '修改完善':
          $this->_context->setState(new CompletingState());
        break;
        //case 'checking':
        case '待审核':
          $this->_context->setState(new CheckingState());
        break;
        //case 'auditPassed':
        case '审核通过':
          $this->_context->setState(new AuditPassedState());
        break;
        //case 'auditFailed':
        case '审核未通过':
          $this->_context->setState(new AuditFailedState());
        break;
        //case 'exeChanging':
        case '变更申请':
          $this->_context->setState(new ExeChangingState());
        break;
        //case 'renewPlanning':
        case '拟续费':
          $this->_context->setState(new RenewPlanningState());
        break;
        //case 'applyApproved':
        case '批准申报':
          $this->_context->setState(new ApplyApprovedState());
        break;
        //case 'applyVetoed':
        case '否决申报':
          $this->_context->setState(new ApplyVetoedState());
        break;
        //case 'exeChApproved':
        case '准予变更':
          $this->_context->setState(new ExeChApprovedState());
        break;
        //case 'exeChVetoed':
        case '否决变更':
          $this->_context->setState(new ExeChVetoedState());
        break;
        //case 'renewApproved':
        case '准予续费':
          $this->_context->setState(new RenewApprovedState());
        break;
        //case 'executing':
        case '申报执行':
          $this->_context->setState(new ExecutingState());
        break;
        //case 'applyModifying':
        case '申报修改':
          $this->_context->setState(new ApplyModifyingState());
        break;
        //case 'applyReviewing':
        case '申报复核':
          $this->_context->setState(new ApplyReviewingState());
        break;
        //case 'applySubmitting':
        case '申报提交':
          $this->_context->setState(new ApplySubmittingState());
        break;
        //case 'applyAuthorized':
        case '专利授权':
          $this->_context->setState(new ApplyAuthorizedState());
        break;
        //case 'applyRejected':
        case '专利驳回':
          $this->_context->setState(new ApplyRejectedState());
        break;
        //case 'renewCreating':
        case '续费新增':
          $this->_context->setState(new RenewCreatingState());
        break;
        //case 'renewSubmitting':
        case '续费提交':
          $this->_context->setState(new RenewSubmittingState());
        break;
        //case 'renewAuthoried':
        case '续费授权':
          $this->_context->setState(new RenewAuthoriedState());
        break;
        //case 'renewVetoed':
        case '放弃续费':
          $this->_context->setState(new RenewVetoedState());
        break;
        //case 'closed':
        case '完结':
          $this->_context->setState(new ClosedState());
        break;        
        
      }
      //返回本对象，便于进行链式操作。
      return $this;
    }
   //else{
//      $this->_msg.='Wrong status."status" should be among'.json_encode(self::ISSPATSTATUS);
//      //停止操作。
//      return $this->result();
//    }
    
    
  }
  //FSM起作用，根据$this->_oprt确定$this->_context->oprt()处理$this->_data
  private function processingData(){
    if($this->_errFlag==0){
      //21个oprt
      switch($this->_oprt){
        case '_ADDNEW':
          $this->_msg=$this->_context->addNew($this->_data);
        break;
        case '_SUBMIT':
          $this->_msg=$this->_context->submit($this->_data);
        break;
        case '_DELETE':
          $this->_msg=$this->_context->delete($this->_data);
        break;
        case '_UPDATE':
          $this->_msg=$this->_context->update($this->_data);
        break;
        case '_PASS':
          $this->_msg=$this->_context->pass($this->_data);
        break;
        case '_FAIL':
          $this->_msg=$this->_context->fail($this->_data);
        break;
        case '_MODIFY':
          $this->_msg=$this->_context->modify($this->_data);
        break;
        case '_PERMIT':
          $this->_msg=$this->_context->permit($this->_data);
        break;
        case '_VETO':
          $this->_msg=$this->_context->veto($this->_data);
        break;
        case '_COMPLETE':
          $this->_msg=$this->_context->complete($this->_data);
        break;
        case '_ACCEPT':
          $this->_msg=$this->_context->accept($this->_data);
        break;
        case '_REFUSE':
          $this->_msg=$this->_context->refuse($this->_data);
        break;
        case '_REPORT':
          $this->_msg=$this->_context->report($this->_data);
        break;
        case '_FINISH':
          $this->_msg=$this->_context->finish($this->_data);
        break;
        case '_APPLY':
          $this->_msg=$this->_context->apply($this->_data);
        break;
        case '_REVIEW':
          $this->_msg=$this->_context->review($this->_data);
        break;
        case '_IMPROVE':
          $this->_msg=$this->_context->improve($this->_data);
        break;
        case '_AUTHORIZE':
          $this->_msg=$this->_context->authorize($this->_data);
        break;
        case '_REJECT':
          $this->_msg=$this->_context->reject($this->_data);
        break;
        case '_ADDRENEW':
          $this->_msg=$this->_context->addRenew($this->_data);
        break;
        case '_CLOSE':
          $this->_msg=$this->_context->close($this->_data);
        break;
      }
    }
    //else{
//      $this->_msg.='Wrong operation."oprt" should be among'.json_encode(self::ISSPATOPRT);
//      return $this->result();
//    }
    
  }
  
  public function setFSM($param=''){
    if(is_array($param)){
      if(in_array($param['auth'],self::ISSAUTH)?$param['auth']:0){
        $this->_auth=$param['auth'];
        $flag1=0;
      }else{
        $this->_msg.='<br>Wrong auth."auth" should be among'.json_encode(self::ISSAUTH);
        $flag1=1;
      }
      
      if(in_array($param['status'],self::ISSPATSTATUSCH)?$param['status']:0){
        $this->_status=$param['status'];
        $flag2=0;
      }else{
        $this->_msg.='<br>Wrong status."status" should be among'.json_encode(self::ISSPATSTATUSCH);
        $flag2=1;
      }
      
      if(in_array($param['oprt'],self::ISSPATOPRT)?$param['oprt']:0){
        $this->_oprt=$param['oprt'];
        $flag3=0;
      }else{
        $this->_msg.='<br>Wrong operation."oprt" should be among'.json_encode(self::ISSPATOPRT);
        $flag3=1;
      }
      
      $this->_errFlag=$flag1+$flag2+$flag3;
      
    }else{
      $this->_msg.='<br>Wrong param."param" should be an array like: '."array('auth'=>'_EDIT','status'=>'filling','oprt'=>'_ADDNEW')".'<br>';
      $this->_errFlag=1;
    }
    
    return $this;

  }
  
  public function result($initData){
    $data=array('pat'=>array('id'=>0,'data'=>0,'rdData'=>0,'rdDataPatch'=>0),
                'iss'=>array('id'=>0,'data'=>0,'rdData'=>0,'rdDataPatch'=>0),
                'att'=>array('arrId'=>0,'arrFileName'=>0,'arrFileObjStr'=>0)
                );
    
    if(!is_array($initData)){
        $this->_msg.='<br>Wrong Data."data" should be an array like: <br>'.json_encode($data).'<br>';
        $this->_errFlag=1;
    }
    
    $this->_data=array_merge($data,$initData);
    
    if($this->_errFlag){
      return $this->_msg;
    }else{
      $this->selectFSM()->setFSMState()->processingData();
      return $this->_msg;
    }
  }
  
}

?>

