<?php


namespace app\api\model;


use think\Exception;
use think\Model;

class ShejituModel extends Model
{
    protected $name = 'shejitu';
    //开启时间戳记录
    protected $autoWriteTimestamp = true;
    protected $pk = 's_id';

    /**
     * 查询信息
     * @param $where
     * @param string $field
     * @param string $order
     * @return array
     */
    public function getInfo($where,$field = '*',$order = ''){
        try {
            $res = $this->where($where)->field($field)->order($order)->find();
            if($res === false){
                return ['code' => 'fail','msg' => $this->getError()];
            }else{
                return ['code' => 'ok','data' => $res];
            }
        }catch (Exception $e){
            return ['code' => 'fail','msg' => $e->getMessage()];
        }
    }

    /**
     * 新增数据
     * @param $data
     * @return array
     */
    public function save_data($data){
        try {
            $res = $this->allowField(true)->save($data);
            if($res === false){
                return ['code' => 'fail','msg' => $this->getError()];
            }else{
                return ['code' => 'ok'];
            }
        }catch (Exception $e){
            return ['code' => 'fail','msg' => $e->getMessage()];
        }
    }
}