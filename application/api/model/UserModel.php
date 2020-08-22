<?php


namespace app\api\model;


use think\Exception;
use think\Model;

class UserModel extends Model
{
    protected $name = 'user';
    //开启时间戳记录
    protected $autoWriteTimestamp = true;
    protected $pk = 'id';

    /**
     * 查询信息
     * @param $where
     * @return array
     */
    public function getInfo($where,$field = '*'){
        return $this->where($where)->field($field)->find();
//        try {
//            $res = $this->where($where)->find();
//            if($res === false){
//                return ['code' => 'fail','msg' => $this->getError()];
//            }else{
//                return ['code' => 'ok','data' => $res];
//            }
//        }catch(Exception $e){
//            return ['code' => 'fail','msg' => $e->getMessage()];
//        }
    }

    /**
     * 修改数据
     * @param $data
     * @param $where
     * @return array
     */
    public function update_data($data,$where){
        try {
            $res = $this->allowField(true)->isUpdate(true)->save($data,$where);
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
    public function getList($where,$field = '*',$order = 'id'){
        try {
            $res = $this->where($where)->field($field)->order($order)->select();
            if($res === false){
                return ['code' => 'fail','msg' => $this->getError()];
            }else{
                return ['code' => 'ok','data' => $res];
            }
        }catch (Exception $e){
            return ['code' => 'fail','msg' => $this->getMessage()];
        }
    }

}