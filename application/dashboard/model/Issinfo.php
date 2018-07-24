<?php

/**
 * @author TATA
 * @copyright 2017
 */

namespace app\dashboard\model;

use think\Model;
use think\Collection;

use app\dashboard\model\User as UserModel;
use app\dashboard\model\Patinfo as PatinfoModel;

class Issinfo extends Model
{
    //新增、更新时需要自动完成的字段列表
    //protected $auto = ['issmap_type','issnum'];
    //新增时需要自动完成的字段列表
    protected $insert = ['issnum'];
    //更新时需要自动完成的字段列表
    //protected $update = ['topic','abstract','addnewdate'];

    //只读字段，这个字段的值一旦写入，就无法更改。
    protected $readonly = ['issnum'];

    // 开启时间字段自动写入
    protected $autoWriteTimestamp = true;

    //指定字段类型
    protected $type = ['auth_time' => 'json', 'oprt_user' => 'json', ];

    //修改器，设置issnum字段的值为iss+yyyy+0000的形式，即是在当年进行流水编号
    protected function setIssnumAttr()
    {
        //$idmax = Issinfo::max('id');
        //        $value = Issinfo::where('id',$idmax)->value('issnum');
        $idmax = $this->max('id');
        $value = $this->where('id', $idmax)->value('issnum');

        $year = substr($value, 3, 4);
        $num = substr($value, 3) + 1;

        if ($year == date('Y')) {
            $result = "iss".$num;
        } else {
            $result = "iss".date('Y')."0001";
        }
        return ($result);
    }

    //获取器，获取数据表issinfo中issmap_type字段值，转换为中文输出
    protected function getIssmapTypeAttr($key)
    {
        $value = $key;
        $issMapType = array(
            '_ISST_PAT1' => '专利授权申报',
            '_ISST_PAT2' => '专利授权到期续费',
            '_ISST_THE1' => '论文审查',
            '_ISST_THE2' => '论文发表',
            '_ISST_PRO1' => '项目申报',
            '_ISST_PRO2' => '项目立项',
            '_ISST_PRO3' => '项目执行',
            '_ISST_PRO4' => '项目验收',
            '0' => '……');
        if (array_key_exists($key, $issMapType)) {
            $value = $issMapType[$key];
        }
        return $value;
    }

    //修改器，设定数据表issinfo中issmap_type字段值，转换为类型编码写入数据库
    protected function setIssmapTypeAttr($key)
    {
        $value = $key;
        $issMapType = array(
            '专利授权申报' => '_ISST_PAT1',
            '专利授权到期续费' => '_ISST_PAT2',
            '论文审查' => '_ISST_THE1',
            '论文发表' => '_ISST_THE2',
            '项目申报' => '_ISST_PRO1',
            '项目立项' => '_ISST_PRO2',
            '项目执行' => '_ISST_PRO3',
            '项目验收' => '_ISST_PRO4',
            '……' => '0');
        if (array_key_exists($key, $issMapType)) {
            $value = $issMapType[$key];
        }
        return $value;
    }
    
    // iss基础查询
    protected function scopeIssmapType($query, $issType = '')
    {
        //$issType='_ISST_PAT'确保查询的是isspat
        $query->where('issmap_type', 'like', '%'.$issType.'%');
    }

    /**
     * 获取登录用户各类isspat的总数和数据集
     * @param  Array $logUser 登录用户信息数组
     * @param  String $type 决定返回数据('_NUM','_TODO',_INPROCESS','_PATRENEW','_DONE') 
     * @param  Array $field 进行分组的字段名 
     * @return Array $result 返回的数组
     */
    public function issPatProcess($logUser= [], $type = '', $field=[])
    {
        //存放各类isspat的procss类别名称及其对应记录数的数组$num 
        $num = array('todo'=>0,'inprocess'=>0,'done'=>0,'patrenew'=>0);
        $listToDo= array();
        $listInProcess= array();
        $listPatRenew= array();
        $listDone= array();
        
        $mapToDo= array();
        $mapInProcess= array();
        
        $arrToDo= array();
        $arrInProcess= array();
        
        //基础查询，在isspat中进行查询
        $iss=$this::scope('issmap_type','_PAT');
        
        if (count($field)){
            $visibleField=$field;
        }else{
            $visibleField=['id','topic','status','statusdescription','create_time','update_time','dept','writer','executer','issmap_id','issmap_type',
                            'issmap' => ['id','patnum','topic','pattype','status']]; 
        }
        
        foreach ($logUser['auth']['iss'] as $key => $value) {
            //清空查询条件数组
            $mapToDo = array();
            $mapInProcess = array();
            
            $mapToDo['status'] = ['in', _commonIssAuthStatus('_PAT', $key)];
            $mapInProcess['status']=$mapToDo['status'];
            switch ($key) {
                case 'maintain':
                    //$map['status'] =['in',['申报复核','申报提交','续费提交','准予续费','否决申报','专利授权','专利驳回','放弃续费','续费授权']];
                    break;

                case 'edit':
                    //$map['status'] =['in',['填报','返回修改','修改完善']];
                    $mapToDo['dept'] = $logUser['dept'];
                    $mapToDo['writer'] = $logUser['username'];
                    break;

                case 'audit':
                    //$map['status'] ='待审核';
                    $mapToDo['dept'] = $logUser['dept'];
                    break;

                case 'approve':
                    //$map['status'] =['in',['审核未通过','审核通过','变更申请','拟续费']];
                    break;

                case 'execute':
                    //$map['status'] =['in',['批准申报','申报执行','申报修改','准予变更','否决变更']];
                    $mapToDo['executer'] = $logUser['username'];
                    break;

            }
                         
            $arrToDo= $iss->where($mapToDo)->select();
            if(count($arrToDo)){
                $arrToDo = collection($arrToDo)->load('issmap')->visible($visibleField)->toArray();
            }else{
                $arrToDo = array();
            } 
            
            $arrInProcess= $iss->where($mapInProcess)->select();
            if(count($arrInProcess)){
                $arrInProcess = collection($arrInProcess)->load('issmap')->visible($visibleField)->toArray(); 
            }else{
                $arrInProcess = array();
            } 
            
            if($value){
                $listToDo=array_merge($listToDo,$arrToDo);
                $num[$key]=count($arrToDo);
                $num['todo']+=$num[$key];
            }else{                
                $listInProcess=array_merge($listInProcess,$arrInProcess);
                $num[$key]=count($arrInProcess);
                $num['inprocess']+=$num[$key];
            }
            
        }
        
        $num['done'] = $iss->where('status','完结')->count();        
        
        //得到满足续费条件的专利数
        $deadline = date('Y-m-d', strtotime("+6 month"));
        $mapRenew['status'] = ['in', ['授权', '续费授权']];
        // 查出满足条件的patent
        $num['patrenew']  = PatinfoModel::where($mapRenew)->where('renewdeadlinedate', 'between time', [date('Y-m-d'), $deadline])->count();

        switch ($type) {
            case '_NUM':
                $result=$num;
                break;

            case '_TODO':
                $result=$listToDo;
                break;

            case '_INPROCESS':
                $result=$listInProcess;
                break;

            case '_PATRENEW':
                $result=array_merge($listPatRenew,PatinfoModel::where($mapRenew)->where('renewdeadlinedate', 'between time', [date('Y-m-d'), $deadline])
                                    ->order('renewdeadlinedate','asc')
                                    ->select());
                break;

            case '_DONE':
                $listDone=$iss->where('status','完结')->select();
                //$listDone= collection($listDone)->append(['issmap'])->visible(['issmap' => ['id', 'patnum','topic','pattype','status']])->toArray();
                $listDone = collection($listDone)->load('issmap')->visible($visibleField)->toArray();  
                $result=$listDone;
                
                break;

        }

        return $result;
    }
     
     /**
     * 将查询结果数据集(数组)根据排序参数转换为排序后的数组
     * @param  Array $issArr 待排序的数组
     * @param  String $field 排序字段 
     * @param  String $order “asc”/"desc"
     * @param  Array $idSearchArr 搜索id值索引数组
     * @return Array $result 返回值，已排好序的索引数组
     */
    public function issPatSort($issArr=[],$field = '',$order='_ASC',$idSearchArr=[]) 
    {
        //含排序字段值的索引数组   
        $arr=array();
        //排序id值索引数组，由$issArr的"id"字段值得到   
        $idSortArr=array();
        //需返回的已排好序的索引数组
        $result=array();
        //排序字段名称
        $field=('oprt'==$field)?'status':$field;
        
        //取排序数组的id索引数组
        $idSortArr=collection($issArr)->column('id');
                
        if(count($idSearchArr)){
            //id索引数组交集
            $idArr=array_intersect($idSortArr,$idSearchArr);
            if(count($idArr)){
                //交集不为空，重组$idArr下标，保证$idArr的下标是连续，使用模型的all方法根据id进行查询时要求
                $idArr=array_values($idArr);
            }else{
                $idArr=[];
            }
        }else{
            $idArr=$idSortArr;
        }
  
        if(count($idArr)){
            $issArr=$this->all($idArr);
            $visibleField=['id','topic','status','statusdescription','create_time','update_time','dept','writer','executer','issmap_id','issmap_type',
                            'issmap' => ['id','patnum','topic','pattype','status']]; 
            $issArr = collection($issArr)->load('issmap')->visible($visibleField)->toArray();
            //根据交集得到数据集
            //$issArr=$this->all($idArr,'issmap'); 
        }else{
            $issArr=$this->all(0);
        }
        
        //对数据集进行排序
        //1.生成一个仅含排序字段值的索引数组  
        //方法一：使用for循环    
        for($i=0;$i<count($issArr);$i++){
            if(is_array($field)){
                $arr[$i]=$issArr[$i][$field[0]][$field[1]];
            }else{
                $arr[$i]=$issArr[$i][$field];
            }
        }
        //2.按照$order的值对上述新生成的索引数组进行自然排序
        if($order=='_ASC'){
            asort($arr,SORT_NATURAL);
        }else{
            arsort($arr,SORT_NATURAL);
        }        
        //3.得到排序好的id值数组        
        //方法：按$arr的顺序将$issArr的‘id’字段值拷贝到新数组$result        
        $i=0;
        foreach($arr as $key=>$value){
            $result[$i]=$issArr[$key];
            $i++;
        }
        
        return $result;
    }
    
    /**
     * 根据搜索条件查询结果数据集(数组)
     * @param  Array $queryParam 搜索条件数组
     * @param  Array $field 查询结果数据集所包含的字段名数组，默认只是‘id’字段
     * @return Array $result 返回的索引数组
     */
    public function issPatSearch($queryParam=[],$field=['id']) 
    {
        //需返回的索引数组
        $result=array();
        //时间查询参数数组
        $time=array();
        //查询条件数组
        $map=array();
        $strRange='';
        
        /**
 *      $queryParam = array(
 *             'topic' => '',
 *             'status' => '',
 *             'dept' => '',
 *             'writer' => '',
 *             'executer' => '',
 *             'timeName' => 'create_time'/'update_time',
 *             'timeRange' => '_LE/_GE/_BT',
 *             'startTime' => '2018-7-2',
 *             'endTime' => '2018-7-12',
 *             );
 */
        //搜索过程，$queryParam有内容就开始从Issinfo表中查询
        if(count($queryParam)){
            //1. 根据$queryParam内容组织查询数组
            foreach($queryParam as $key=>$val){
                //提取时间有关搜索数据,
                //strpos对大小写敏感，返回值可能包含0，所以要用严格比较符“!==”
                //stristr()对大小写不敏感，返回值为找到的字符串或false。性能应该比不上strpos
                if(strpos($key, 'ime')!== false){
                    //时间字段名
                    if(strpos($key, 'Name')!== false){
                        $time['name']=$val;
                    }
                    //时间范围
                    if(strpos($key, 'Range')!== false){
                        switch($val){
                            case'_BT':
                                $strRange='between time';
                                break;
                            case'_LE':
                                $strRange='<= time';
                                break;
                            case'_GE':
                                $strRange='>= time';
                                break;
                        }
                        $time['range']=array('type'=>$val,'value'=>$strRange);
                    }
                    
                    //时间值
                    if(strpos($key, 'start')!== false){
                        $time['start']=$val;
                    }
                    
                    if(strpos($key, 'end')!== false){
                        $time['end']=$val;
                    }
                }else{
                     //其他搜索字段名和条件
                     $map[$key]=['like','%'.$val.'%'];
                     //$map[$key]=['like',$val];
                }
            }
            
            //时间搜索字段名和条件
            if(count($time)) {
                switch($time['range']['type']){
                    case'_BT':
                        $map[$time['name']]=[$time['range']['value'],[$time['start'],$time['end']]];
                        break;
                    case'_LE':
                        $map[$time['name']]=[$time['range']['value'],$time['end']];
                        break;
                    case'_GE':
                        $map[$time['name']]=[$time['range']['value'],$time['start']];
                        break;
                }
            }   
            //搜索结果数据集
            $result=$this->where($map)->field($field)->select();
        }
        return $result;
    }
    
    /**
     * 获取对应patent的内容
     */
    public function patinfo()
    {
        return $this->hasOne('Patinfo');
    }

    /**
     * 获取issue的过程记录，与Issrecord模型关联，是一对多的关联关系
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

    //定义issinfo的多态模型,涉及issinfo表中的issmap_id和issmap_type两个字段内容。可得到对应关联模型的所有字段内容。
    public function issmap()
    {
        //本模型内已定义获取器getIssmapTypeAttr,本属性读取的是获取器输出的issmap_type字段值（中文），所以要以获取器的输出值来对应模型
        $data = ['专利授权申报' => 'Patinfo', '专利授权到期续费' => 'Patinfo', '项目申报' => 'Proinfo', '论文审查' => 'Theinfo',
                    '_ISST_PAT1'=> 'Patinfo','_ISST_PAT2'=> 'Patinfo'
                ];
        return $this->morphTo('issmap', $data);
    }

    /**
     * 获取issue的附件，与Attinfo模型关联
     */
    public function attachments()
    {
        return $this->morphMany('Attinfo', 'attmap', '_ATTO1');
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
    public function issUpdate($data = [], $id)
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
        $result = $this->where('id', $issId)->delete();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

}

?>