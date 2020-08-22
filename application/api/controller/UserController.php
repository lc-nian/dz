<?php


namespace app\api\controller;


use app\api\model\UserModel;
use app\api\validate\User;
use think\Request;

class UserController extends BaseController
{
    protected $user;
    public function __construct(UserModel $user){
        $this->user = $user;
        parent::_initialize();
    }


    /**
     * 查询个人信息
     * @param Request $request
     */
    public function user_info(Request $request){
        if($request->isPost()){
            $info = $this->user->getInfo(['id' => $this->user_id],'headimg,account,name,phone,email');
            if($info){
                $this->response['info'] = $info;
                return $this->_api_return(200,'查询成功');
            }else{
                return $this->_api_return(400,'失败');
            }
        }
    }

    /**
     * 修改个人信息
     * @param Request $request
     */
    public function user_edit(Request $request){
        if($request->isPost()){
            $validate = new User();//验证信息
            if (!$validate->check($this->param,'','check')) {
                return $this->_api_return('400', $validate->getError());
            }

            $res = $this->user->update_data($this->param,['id' => $this->user_id]);
            if($res['code'] != 'ok'){
                return $this->_api_return('400','保存失败');
            }else{
                return $this->_api_return('200','保存成功');
            }
        }
    }

    /**
     * 头像上传
     */
    public function headimgUp(Request $request){
        if($request->isPost()){
            if($request->file('headimg')){
                $file = $request->file('headimg');
                $info = $file->validate(['ext'=>'jpg,png'])->move('../uploads/headimg');
                if($info){
                    $name = "/uploads/headimg/".$info->getSaveName();
                    $imgPath = str_replace('\\','/',$name);

                    $this->response['headimg'] = $imgPath;
                    return $this->_api_return(200,'上传成功');
                }else{
                    return $this->_api_return('400',$file->getError());
                }
            }else{
                return $this->_api_return('400','请上传图片');
            }
        }
    }

    /**
     * 修改密码
     * @param Request $request
     */
    public function pwd_edit(Request $request){
        if($request->isPost()){
            $validate = new User();//验证信息
            if (!$validate->check($this->param,'','pwds')) {
                return $this->_api_return(400, $validate->getError());
            }
            if($this->param['oldpwd'] == $this->param['newpwd']){
                return $this->_api_return(400,'新密码不能和旧密码一样');
            }
            if($this->param['newpwd'] != $this->param['newpwds']){
                return $this->_api_return(400,'新密码和重复密码不相同');
            }
            $info = $this->user->getInfo(['id' => $this->user_id]);
            if($info['pwd'] != md5($this->param['oldpwd'])){
                return $this->_api_return(400,'旧密码错误');
            }
            $res = $this->user->update_data(['pwd' => md5($this->param['newpwd'])],['id' => $this->user_id]);
            if($res['code'] != 'ok'){
                return $this->_api_return(400,'修改失败');
            }
            return $this->_api_return(200,'密码修改成功');
        }
    }

    /**
     * 用户列表
     * @param Request $request
     */
    public function userList(Request $request){
        if($request->isPost()){
            $info = $this->user->getInfo(['id' => $this->user_id]);
            if(empty($info)){
                return $this->_api_return(400,'信息错误');
            }

            $list = $this->user->getList(['a_id' => $info['a_id']],'id,a_id,account,name,status,type');
            if($list['code'] != 'ok'){
                return $this->_api_return(400,'列表信息错误');
            }
            if($list['data']){
                foreach ($list['data'] as &$v){
                    $v['abbreviation'] = db('agent')->where('id',$v['a_id'])->value('abbreviation');//机构简称
                }
            }
            $this->response['list'] = $list['data'];
            return $this->_api_return(200,'成功');
        }
    }

    /**
     * 修改用户状态
     * @param Request $request
     */
    public function status_edit(Request $request){
        if($request->isPost()){
            if(empty($this->param['id'])){
                return $this->_api_return(400,'参数错误');
            }
            $info = $this->user->getInfo(['id' => $this->param['id']]);
            if($info['type'] == 1){
                return $this->_api_return(400,'管理员不可修改');
            }
            if($info['status'] == 0){//当前停用
                $res = $this->user->update_data(['status' => 1],['id' => $this->param['id']]);
            }else{
                $res = $this->user->update_data(['status' => 0],['id' => $this->param['id']]);
            }

            if($res['code'] != 'ok'){
                return $this->_api_return(400,'操作失败');
            }
            return $this->_api_return(200,'成功');
        }
    }

    /**
     * 重置密码
     * @param Request $request
     */
    public function reset_pwd(Request $request){
        if($request->isPost()){
            if(empty($this->param['id'])){
                return $this->_api_return(400,'参数错误');
            }
            $info = $this->user->getInfo(['id' => $this->param['id']]);
            if($info['type'] == 1){
                return $this->_api_return(400,'管理员不可修改');
            }
            $res = $this->user->update_data(['pwd' => md5('123456')],['id' => $this->param['id']]);
            if($res['code'] != 'ok'){
                return $this->_api_return(400,'重置失败');
            }
            return $this->_api_return(200,'密码成功重置为123456');
        }
    }

    /**
     * 用户信息
     * @param Request $request
     */
    public function user_data(Request $request){
        if($request->isPost()){
            if(empty($this->param['id'])){
                return $this->_api_return(400,'参数错误');
            }
            $info = $this->user->getInfo(['id' => $this->param['id']],'id,account,name,phone,email,sex,node,type');
            if($info['type'] == 1){
                return $this->_api_return(400,'管理员不可修改');
            }
            $this->response['info'] = $info;//用户信息
            $this->response['node'] = explode(',',$info['node']);//权限角色信息
            return $this->_api_return(200,'成功');
        }
    }

    /**
     * 编辑用户
     * @param Request $request
     */
    public function user_update(Request $request){
        if($request->isPost()){
            if(empty($this->param['id'])) {
                return $this->_api_return(400, '参数错误');
            }
            $info = $this->user->getInfo(['id' => $this->param['id']],'id,account,name,phone,email,sex,node,type');
            if($info['type'] == 1){
                return $this->_api_return(400,'管理员不可修改');
            }

            $validate = new User();//验证信息
            if (!$validate->check($this->param,'','edit')) {
                return $this->_api_return(400, $validate->getError());
            }
            halt(1);
            $res = $this->user->update_data($this->param,['id' => $this->param['id']]);
            if($res['code'] != 'ok'){
                return $this->_api_return(400,'修改失败');
            }
            return $this->_api_return(200,'修改成功');
        }
    }
}