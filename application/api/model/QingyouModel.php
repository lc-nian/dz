<?php


namespace app\api\model;


use think\Exception;
use think\Model;

class QingyouModel extends Model
{
    protected $name = 'qingyou';
    //开启时间戳记录
    protected $autoWriteTimestamp = true;
    protected $pk = 'y_id';

    /**
     * 查询列表
     * @param $where
     * @param string $field
     * @param string $order
     * @return array
     */
    public function getList($where,$field = '*',$order = ''){
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

    /**
     * 添加数据
     * @param $data
     * @return array
     */
    public function save_data($data){
        try {
            $res = $this->allowField(true)->save($data);
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
                return ['code' => 'ok','data' => $res];
            }
        }catch (Exception $e){
            return ['code' => 'fail','msg' => $e->getMessage()];
        }
    }

    /**
     * 删除数据
     * @param $where
     * @return array
     */
    public function del_data($where){
        try {
            $res = $this->where($where)->delete();
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