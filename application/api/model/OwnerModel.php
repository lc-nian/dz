<?php


namespace app\api\model;


use think\Exception;
use think\Model;

class OwnerModel extends Model
{
    protected $name = 'owner';
    //开启时间戳记录
    protected $autoWriteTimestamp = true;
    protected $pk = 'y_id';

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
     * 业主信息列表
     * @param $where
     * @param $field
     * @param string $order
     * @return array
     */
    public function owner_list($where,$field,$order = 'o.y_id desc'){
        try{
            $res = $this->field($field)->alias('o')
                ->join('agent a','o.developers_id = a.id')
                ->where($where)
                ->order($order)->select()->toArray();

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
     * 查询信息
     * @param $where
     * @param string $field
     * @param string $order
     * @return array
     */
    public function getInfo($where,$field = '*',$order = ''){
        try{
            $res = $this->field($field)->where($where)->order($order)->find()->toArray();
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