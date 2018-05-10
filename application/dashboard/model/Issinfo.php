<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\dashboard\model;

use think\Model;

use app\dashboard\model\User as UserModel;
use app\dashboard\model\Patinfo as PatinfoModel;

class Issinfo extends Model
{   
    //protected $auto = ['patnum','pronum'];
    protected $insert = ['issnum'];  
    //protected $update = ['topic','abstract','addnewdate'];  
   
    //只读字段，这个字段的值一旦写入，就无法更改。
    protected $readonly = ['issnum'];
    
    //修改器，设置issnum字段的值为iss+yyyy+0000的形式，即是在当年进行流水编号
    protected function setIssnumAttr()
    {
        
        $idmax = Issinfo::max('id');
        $value = Issinfo::where('id',$idmax)->value('issnum');
        
        $year=substr($value,3,4);
        $num=substr($value,3)+1;
        
        if($year==date('Y')){
            $result ="iss".$num;
        }else{
            $result ="iss".date('Y')."0001";
        }
        
        return ($result);
    }
    
    //获取器，获取数据表issinfo中issmap_type字段值，转换为中文输出
    protected function getIssmapTypeAttr($value)
    {
      $outPut='……';
      switch($value){
        case '_ISST_PAT1':
          $outPut='专利授权申报';
        break;
        
        case '_ISST_PAT2':
          $outPut='专利授权到期续费';
        break;
        
        case '_ISST_THE1':
          $outPut='论文审查';
        break;
        
        case '_ISST_THE2':
          $outPut='论文发表';
        break;
        
        case '_ISST_PRO1':
          $outPut='项目申报';
        break;
        
        case '_ISST_PRO2':
          $outPut='项目立项';
        break;
        
        case '_ISST_PRO3':
          $outPut='项目执行';
        break;
        
        case '_ISST_PRO4':
          $outPut='项目验收';
        break;
        
        default:
          $outPut=$value;
        break;
        
      }
      return $outPut;
    }
    
   /**
     * 获取登录用户所有权限下isspat的各类总数
     * 参数$issType，类型：字符串。值：不为空。说明：需要得到的iss类型。默认为‘_PAT’。
     * 参数$userId，类型：数值。值：不为空。说明：登录用户的id。默认为空。
     * 参数$auth，类型：字符串。值：可为空。说明：登录用户的iss权限。默认为空。
     */
    public function issPatNum($userId='',$auth='')
    {   
        if($userId==''){
          return false;
        }else{
          $user=UserModel::get($userId);
        }
        //存放iss各个权限名称及其对应iss记录数的数组
        $numIssPatArr=array();
        
        //登录用户的iss权限
        $authIssArr=$user['authority']['iss'];
    
        foreach($authIssArr as $key=>$value){
          //分情况写查询条件
          $map['issmap_type']=['like','%_ISST_PAT%'];
          if($value!=0){
            
            switch($key){
              case'maintain':
                 $map['status'] =['in',['申报复核','申报提交','续费提交','准予续费',
                                        '否决申报','专利授权','专利驳回','放弃续费','续费授权']];
              break;   
              
              case'edit':
                $map['status'] =['in',['填报','返回修改','修改完善']];
                $map['dept'] =$user->dept;
                $map['writer']=$user->username;
              break;
              
              case'audit':
                $map['status'] ='待审核';
                $map['dept'] =$user->dept;
              break;
              
              case'approve':
                $map['status'] =['in',['审核未通过','审核通过','变更申请','拟续费']];
              break;
              
              case'execute':
                $map['status'] =['in',['批准申报','申报执行','申报修改','准予变更','否决变更']];
                $map['executer'] =$user->username;
              break;
                       
            }
            $numIssPatArr[$key]=$this->where($map)->count();
           
          }else{
            $numIssPatArr[$key]=0;
          }
          
          $iss=$this->where($map)->select();
        }
        //得到满足续费条件的专利数
        $deadline=date('Y-m-d',strtotime("+6 month"));
        $mapRenew['status'] =['in',['授权','续费授权']];          
        // 查出满足条件的patent
        //$numIssPatArr['maintain']+=PatinfoModel::where($mapRenew)->where('renewdeadlinedate','between time',[date('Y-m-d'),$deadline])->count();
        $numIssPatArr['renew']=PatinfoModel::where($mapRenew)->where('renewdeadlinedate','between time',[date('Y-m-d'),$deadline])->count();
        //done
        $map['status'] ='完结';
        $numIssPatArr['done']=$this->where($map)->count();
        //total
        $numTotal=0;
        foreach($numIssPatArr as $key=>$value){
          if($key!='done')$numTotal+=$value;
        }
        $numIssPatArr['total']=$numTotal;
        $numIssPatArr['iss']=$iss;
        //根据$auth的值返回值不同
        if($auth==''){
          //$num=$numIssPatArr['total'];
          $num=json_encode($numIssPatArr);
        }else{
          $num=$numIssPatArr[$auth];
        }
        
        return $num;
    }
    
    /**
     * 获取对应patent的内容
     */    
    public function patinfo()
    {
        return $this->hasOne('Patinfo');
    }    
    
    /**
     * 获取issue的过程记录
     */
    public function issrecords()
    {   
        return $this->hasMany('Issrecord');
    }
    
    //获取issinfo的多态模型,涉及issinfo表中的num_id和num_type两个字段内容
  //   public function num()
//    {
//        return $this->morphTo(null, [
//            '0' => 'app\issue\model\Issinfo',
//            '1' => 'app\project\model\Proinfo',
//            '2' => 'app\patent\model\Patinfo',
//        ]);
//    }
    
    //获取issinfo的多态模型,涉及issinfo表中的issmap_id和issmap_type两个字段内容。
     public function issmap()
    {
        $this->getData('issmap_type');
        //本模型内已定义获取器getIssmapTypeAttr,本属性读取的是获取器输出的issmap_type字段值（中文），所以要以获取器的输出值来对应模型
        $data=['专利授权申报' => 'Patinfo','专利授权到期续费' => 'Patinfo','项目申报' => 'Proinfo','论文审查' => 'Theinfo'];
        return $this->morphTo(null, $data);
        
        //return $this->morphTo(null, [
//            //已定义获取器getIssmapTypeAttr,本属性读取的是获取器输出的issmap_type字段值，所以要以获取器的输出值来对应模型
//            '专利授权申报' => 'Patinfo',
//            '专利授权到期续费' => 'Patinfo',
//            '项目申报' => 'Proinfo',
//            '论文审查' => 'Theinfo',
//        ]);
    }
    
    /**
     * 获取issue的附件
     */
    public function attachments()
    {
      return $this->morphMany('Attinfo','attmap','_ATTO1');
    }
    
    /**
     * 新增一个issue。
     * @param  array $data 新增issue的各项信息
     * @return integer|bool  新增成功返回主键，新增失败返回false
     * 要求：传入的数组下标名与模型属性名（数据表字段名）一模一样。
     */
    public function myCreate($data = [])
    {
        $result = $this->allowField(true)->save($data);
        if ($result) {
            return $this->getData('id');
        } else {
            return false;
        }
    }
    
    /**
     * 新增一个issue。
     * @param  array $data 新增issue的各项信息
     * @return integer|bool  新增成功返回主键，新增失败返回false
     * 要求：传入的数组下标名与模型属性名（数据表字段名）一模一样。
     */
    public function issCreate($data = [])
    {
        $result = $this->allowField(true)->save($data);
        if ($result) {
            return $this->getData('id');
        } else {
            return false;
        }
    }
    
    /**
     * 更新issue。
     * @param  array $data 更新issue的各项信息
     * @return integer|bool  更新成功返回主键，未更新返回false
     * 要求：传入的数组下标名与模型属性名（数据表字段名）一模一样。
     */
    public function issUpdate($data = [],$id)
    {
        $result = $this::get($id)->allowField(true)->save($data);
        //$iss=$this::get($id);
//        $result = $iss->allowField(true)->data($data, true)->save();
        if ($result) {
            return $id;
        } else {
            return false;
        }
    }
    
     /**
     * 删除issue。
     * @param  integer $issId 删除issue的id
     * @return integer|bool  删除成功返回主键，未成功返回false
     *  考虑应用TP5的软删除进行改进，？？？2018/3/23
     */
    public function issDelete($issId)
    {
        //delete()方法返回的是受影响记录数
        $result = $this->where('id',$issId)->delete();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
    
}

?>