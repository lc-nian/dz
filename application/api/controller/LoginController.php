<?php


namespace app\api\controller;


use app\api\model\UserModel;
use app\api\validate\User;
use think\Request;
use think\facade\Config;

class LoginController extends BaseController
{
    protected $user;
    public function __construct(UserModel $user){
        $this->user = $user;
        parent::_initialize();
    }

    /**
     * 登录
     * @param Request $request
     */
    public function login(Request $request){
        if($request->isPost()) {
            $data = [
                'account' => $this->param['account'],
                'pwd' => $this->param['pwd'],
            ];
            $validate = new User();
            if (!$validate->check($data,'','send')) {
                return $this->_api_return('400', $validate->getError());
            }
            $info = $this->user->getInfo(['account' => $this->param['account']]);
            if ($info['pwd'] != md5($this->param['pwd'])) {
                return $this->_api_return('400', '账号或者密码不正确');
            }
            $this->response['token'] = $this->jwt($info['id']);
            return $this->_api_return('200', '登录成功');
        }
    }

    /**
     * 密码检测
     * @param Request $request
     */
    public function check_pwd(Request $request){
        if($request->isPost()){
            $info = $this->user->getInfo(['id' => $this->user_id]);
            if($info['pwd'] == md5('123456')){
                return $this->_api_return('400', '请修改密码');
            }
            return $this->_api_return('200', '不需要修改');
        }
    }
}