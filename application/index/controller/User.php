<?php


namespace app\index\controller;

use http\Env\Request;
use think\Controller;
use think\Db;

class User extends Controller
{
    public function index()
    {
        $res = db('user')->where('del',0)->select();
//        halt($res);
        $number = db('user')->where('del',0)->count();
        $this->assign('res',$res);
        $this->assign('number',$number);
        return $this->fetch();
    }

    public function user_add()
    {
        if (Request()->isGet()){
            return $this->fetch();
        }else{
            $data = input('post.');
            $data['time'] = time();
//            halt($data);
            $res = db('user')->insert($data);
            if ($res){
//                $this->success('ok','index');
                return ret('1','ok',$res,200);
            }else{
                return ret('0','NO',[],404);
            }
        }
    }

    public function user_del($id)
    {
//        halt($id);
//        $data = input('post.');
//        halt($data);
        $res = db('user')->where('uid',$id)->update(['del'=>1]);
        if ($res){
//                $this->success('ok','index');
            return ret('1','OK',$res,200);
        }else{
            return ret('0','NO',[],404);
        }
    }
    public function user_dodel($id)
    {
        $res = db('user')->where('uid',$id)->update(['del'=>0]);
        if ($res){
            return ret('1','OK',$res,200);
        }else{
            return ret('0','NO',[],404);
        }
    }

    public function user_pidel($data)
    {
        $res = db('user')->delete($data);
        if ($res){
            return ret('1','OK',$res,200);
        }else{
            return ret('0','NO',[],404);
        }
    }

    public function pidel($data)
    {
        foreach ($data as $k=>$v){
            $res = db('user')->where('uid',$v)->update(['del'=>1]);
        }
//        $res = db('user')->delete($data);
        if ($res){
            return ret('1','OK',$res,200);
        }else{
            return ret('0','NO',[],404);
        }
    }

    public function user_list()
    {
        $res = db('user')->where('del',1)->select();
        $number = db('user')->where('del',1)->count();
        $this->assign('res',$res);
        $this->assign('number',$number);
        return $this->fetch();
    }

}