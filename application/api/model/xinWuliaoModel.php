<?php


namespace app\api\model;


use think\Exception;
use think\Model;

class xinWuliaoModel extends Model
{
    protected $name = 'xin_wuliao';
    //开启时间戳记录
    protected $autoWriteTimestamp = true;
    protected $pk = 'x_id';

    /**
     * 添加多条数据
     * @param $data
     * @return array
     * @throws \Exception
     */
    public function save_dataAll($data){
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
     * @param $where
     * @return array
     * @throws \Exception
     */
    public function del_data($where){
        try {
            $res = $this->delete($where);
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
     * 物料列表
     * @param $where
     * @param string $field
     * @param string $order
     * @return array
     */
    public function part_list($where,$field = '*',$order = ''){
        try {
            $res = $this->alias('xw')
                ->join('part p','p.m_id = xw.m_id')
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