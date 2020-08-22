<?php


namespace app\api\validate;


use think\Validate;

class Qinyou extends Validate
{
    protected $rule = [
        'o_id' => 'require',
        'y_id' => 'require',
        'name' => 'require',
        'guanxi' => 'require',
        'phone' => 'require',
    ];

    protected $message = [
        'o_id.require' => '业主编号不能为空',
        'y_id.require' => '联系人id不能为空',
        'name.require' => '姓名不能为空',
        'guanxi.require' => '关系不能为空',
        'phone.require' => '联系电话不能为空',
    ];

    protected $scene = [
        'send' => ['o_id','name','guanxi','phone'],
        'check' => ['y_id','name','guanxi','phone'],
    ];
}