<?php


namespace app\api\model;


use think\Exception;
use think\Model;

class KanchaModel extends Model
{
    protected $name = 'kancha';
    //开启时间戳记录
    protected $autoWriteTimestamp = true;
    protected $pk = 'k_id';

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

    /**
     * 修改数据
     * @param $data
     * @return array
     */
    public function update_data($data){
        try {
            $res = $this->allowField(true)->isUpdate(true)->save($data);
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
     * 勘查信息列表
     * @param $where
     * @param string $field
     * @param string $order
     * @return array
     */
    public function kancha_list($where,$field = '*',$order = ''){
        try {
            $res = $this->alias('k')
                ->join('owner o','o.y_id = k.o_id')
                ->join('agent a','a.id = o.developers_id')
                ->where($where)->field($field)->order($order)
                ->select()->toArray();
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