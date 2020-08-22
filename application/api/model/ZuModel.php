<?php


namespace app\api\model;


use think\Exception;
use think\Model;

class ZuModel extends Model
{
    protected $name = 'zu';
    //开启时间戳记录
    protected $autoWriteTimestamp = true;
    protected $pk = 'z_id';

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
}