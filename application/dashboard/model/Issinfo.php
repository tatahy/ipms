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
     * @return Array $result 返回的数组
     */
    public function issPatProcess($logUser= [], $type = '')
    {
        //存放各类isspat的procss类别名称及其对应记录数的数组$num 
        $num = array('todo'=>0,'inprocess'=>0,'done'=>0,'patrenew'=>0);
        $listToDo= array();
        $listInProcess= array();
        $listPatRenew= array();
        $listDone= array();
        
        //基础查询，在isspat中进行查询
        $iss=$this->scope('issmap_type','_PAT');
        
        foreach ($logUser['auth']['iss'] as $key => $value) {
            $map['status'] = ['in', _commonIssAuthStatus('_PAT', $key)];
            switch ($key) {
                case 'maintain':
                    //$map['status'] =['in',['申报复核','申报提交','续费提交','准予续费','否决申报','专利授权','专利驳回','放弃续费','续费授权']];
                    break;

                case 'edit':
                    //$map['status'] =['in',['填报','返回修改','修改完善']];
                    $map['dept'] = $logUser['dept'];
                    $map['writer'] = $logUser['username'];
                    break;

                case 'audit':
                    //$map['status'] ='待审核';
                    $map['dept'] = $logUser['dept'];
                    break;

                case 'approve':
                    //$map['status'] =['in',['审核未通过','审核通过','变更申请','拟续费']];
                    break;

                case 'execute':
                    //$map['status'] =['in',['批准申报','申报执行','申报修改','准予变更','否决变更']];
                    $map['executer'] = $logUser['username'];
                    break;

            }
            //$num[$key] = $iss->where($map)->count();
            
            if($value){
                $listToDo=array_merge($listToDo,$iss->where($map)->select());
                $num['todo']+=$iss->where($map)->count();
            }else{
                $listInProcess=array_merge($listInProcess,$iss->where($map)->select());
                $num['inprocess']+=$iss->where($map)->count();
            }
            //清空查询条件数组
            $map = array();
        }
        //
        $num['done'] = $iss->where('status','完结')->count();
        //$listDone=$iss->where('status','完结')->select();
        
        //得到满足续费条件的专利数
        $deadline = date('Y-m-d', strtotime("+6 month"));
        $mapRenew['status'] = ['in', ['授权', '续费授权']];
        // 查出满足条件的patent
        $num['patrenew']  = PatinfoModel::where($mapRenew)->where('renewdeadlinedate', 'between time', [date('Y-m-d'), $deadline])->count();
//        $listPatRenew=PatinfoModel::where($mapRenew)->where('renewdeadlinedate', 'between time', [date('Y-m-d'), $deadline])
//                                    ->order('renewdeadlinedate','asc')
//                                    ->select();
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
                $result=array_merge($listDone,$iss->where('status','完结')->select());
                break;

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
        $this->getData('issmap_type');
        //本模型内已定义获取器getIssmapTypeAttr,本属性读取的是获取器输出的issmap_type字段值（中文），所以要以获取器的输出值来对应模型
        $data = ['专利授权申报' => 'Patinfo', '专利授权到期续费' => 'Patinfo', '项目申报' => 'Proinfo', '论文审查' => 'Theinfo'];
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