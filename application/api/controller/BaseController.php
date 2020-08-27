<?php


namespace app\api\controller;


use Firebase\JWT\JWT;
use app\api\model\UserModel;

//跨域问题
header("Access-Control-Allow-Origin:*");
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with, content-type');
class BaseController extends PublicController
{
    public $param = [];
    public $model = null;
    public $controller = null;
    public $action = null;
    public $mvc = null;
    public $user_id = null;
    public $m = null;

    /**
     * 验证签名
     */
    public function _initialize()
    {
        //保存参数
//        halt(1);
        $this->setAction();
        //不需要 Token 验证的方法
        $ext = [
            'api/Login/login', //登录
        ];

        if (!in_array($this->mvc, $ext)) {
            //token验证
            $check = $this->checkToken();

            if ($check === false) {
                return $this->_api_return(400, "您还没有登录！");
            }
            $user = new UserModel();
            $info = $user->getInfo(['id' => $this->user_id],'status');
            if(empty($info)){
                return $this->_api_return(400, "用户不存在！");
            }
            if ($info['status'] == 0) {
                return $this->_api_return(400,'账号已被禁用，请联系平台管理员!');
            }
        }

    }

    /**
     * 接收数据
     */
    private function setAction()
    {
        $this->model = request()->module();
        $this->controller = request()->controller();
        $this->action = request()->action();
        $this->mvc = $this->model . "/" . $this->controller . "/" . $this->action;
        $this->param = request()->isPost() ? input('post.') : input('get.');
    }

    /**
     * Token 校验
     * @return bool
     */
    private function checkToken()
    {
        //获取 JWT 密钥
        $key = config('app.jwt.key');
        //判断 Token 是否存在
        if (!isset($this->param['token']) || $this->param['token'] == null) {
            return false;
        }
        //解密 JWT 字符串
        $param_decode = JWT::decode($this->param['token'], $key, array('HS256'));
//        var_dump($param_decode);exit;
        $param_decode = (array)$param_decode;
//        var_dump($param_decode);exit;
        //判断 user_id 是否存在
        if (!isset($param_decode['user_id']) || $param_decode['user_id'] == null) {
            return false;
        }
        //给 user_id 赋值
        $this->user_id = $param_decode['user_id'];
        return true;
    }

}