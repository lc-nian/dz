<?php


namespace app\api\model;


use think\Exception;
use think\Model;

class BracketModel extends Model
{
    protected $name = 'bracket';
    //开启时间戳记录
    protected $autoWriteTimestamp = true;
    protected $pk = 'zz_id';

    /**
     * 添加多条数据
     * @param $data
     * @return array
     * @throws \Exception
     */
    public function saveall_data($data){
        try {
            $res = $this->allowField(true)->saveAll($data);
            if($res === false){
                return ['code' => 'fail','msg' => $this->getError()];
            }else{
                return ['code' => 'ok'];
            }
        }catch (Exception $e){
            return ['code' => 'fail','msg' => $e->getMessage()];
        }
    }

    /**
     * 查询列表
     * @param $where
     * @param string $field
     * @param string $order
     * @return array
     */
    public function getList($where,$field = '*',$order = ''){
        try {
            $res = $this->where($where)->field($field)->order($order)->select();
            if($res === false){
                return ['code' => 'fail','msg' => $this->getError()];
            }else{
                return ['code' => 'ok','data' => $res];
            }
        }catch (Exception $e){
            return ['code' => 'fail','msg' => $e->getMessage()];
        }
    }
}