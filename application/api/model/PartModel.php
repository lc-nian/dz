<?php


namespace app\api\model;


use think\Exception;
use think\Model;

class PartModel extends Model
{
    protected $name = 'part';
    //开启时间戳记录
    protected $autoWriteTimestamp = true;
    protected $pk = 'm_id';

    /**
     * 查询列表
     * @param $where
     * @param string $field
     * @param string $order
     * @return array
     */
    public function getList($where,$field = '',$order = ''){
        try {
            $res = $this->where($where)->field($field)->order($order)->select()->toArray();
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