<?php


namespace app\api\controller;

use Firebase\JWT\JWT;
use think\Controller;
use think\exception\HttpResponseException;
use think\Response;
use think\facade\Config;

//跨域问题
header("Access-Control-Allow-Origin:*");
header('Access-Control-Allow-Methods:POST');
header('Access-Control-Allow-Headers:x-requested-with, content-type');
class PublicController extends Controller
{
    protected $user_id;
    protected $response = array();

    /**
     * @param $user_id
     * @return string
     */
    public function jwt($user_id)
    {
        $key = config('app.jwt.key');
        $data = [
            "iss" => "kangye",
            "aud" => "dianzhan",
            'user_id' => $user_id,
        ];
        return JWT::encode($data, $key);
    }

    /**
     * @param int $code
     * @param string $msg
     */
    public function _api_return($code = 0, $msg = "")
    {
        return $this->_return(['code' => $code,'msg' => $msg,'data' => $this->response]);
    }

    /**
     * @param array $response
     * @param array $header
     */
    public function _return($response = [], $header = [])
    {
        $response = Response::create($response,'json')->header($header);
        throw new HttpResponseException($response);
    }
}