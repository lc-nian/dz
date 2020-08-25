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
     * 勘察信息列表
     * @param $page
     * @param $limit
     * @param $where
     * @param string $field
     * @param string $order
     * @return array
     */
    public function kancha_list($page,$limit,$where,$field = '*',$order = ''){
        try {
            $res = $this->alias('k')
                ->join('owner o','o.y_id = k.o_id')
                ->join('agent a','a.id = o.developers_id')
                ->where($where)->field($field)->order($order)
                ->paginate($limit,false,['page' => $page]);
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


    public function check($where){
        try {
            $info = $this->where($where)->find();
            if($info === false){
                return ['code' => 'fail','msg' => $this->getError()];
            }else{
                if(!$info){
                    return ['code' => 'fail','msg' => '查询错误'];
                }
                if(empty($info['wuding'])){
                    return ['code' => 'fail','msg' => '有必填数据未填写,无法提交'];
                }
                return ['code' => 'ok','data' => $info];
            }
        }catch (Exception $e){
            return ['code' => 'fail','msg' => $e->getMessage()];
        }
    }
}