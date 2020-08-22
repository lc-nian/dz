<?php


namespace app\api\model;


use think\Exception;
use think\Model;

class AgentModel extends Model
{
    protected $name = 'agent';
    //开启时间戳记录
    protected $autoWriteTimestamp = true;
    protected $pk = 'id';

    /**
     * 查询信息
     * @param $where
     * @return array
     */
    public function getInfo($where){
        try {
            $res = $this->where($where)->find();
            if($res === false){
                return ['code' => 'fail','msg' => $this->getError()];
            }else{
                return ['code' => 'ok','data' => $res];
            }
        }catch(Exception $e){
            return ['code' => 'fail','msg' => $e->getMessage()];
        }
    }
}