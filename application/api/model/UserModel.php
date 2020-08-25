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
     * @param $page
     * @param $limit
     * @param $where
     * @param string $field
     * @param string $order
     * @return array
     */
    public function getList($page,$limit,$where,$field = '*',$order = 'id'){
        try {
            $res = $this->where($where)->field($field)->order($order)->paginate($limit,false,['page' => $page]);
            if($res === false){
                return ['code' => 'fail','msg' => $this->getError()];
            }else{
                $data = [
                    'count' => $res->total(),//总记录数
                    'page' => $res->currentPage(),//当前页码
                    'limit' => $res->listRows(),//每页记录数
                    'list' => $res->items(),//分页数据
                ];
                return ['code' => 'ok','data' => $data];
            }
        }catch (Exception $e){
            return ['code' => 'fail','msg' => $e->getMessage()];
        }
    }

}