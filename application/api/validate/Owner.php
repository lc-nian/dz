<?php


namespace app\api\validate;


use think\Validate;

class Owner extends Validate
{
    protected $rule = [
        'owner_name' => 'require',
        'owner_phone' => 'require',
        'earnest' => 'require|number',
        'zhuzhi' => 'require',
        'c_zhuzhi' => 'require',
        'farmers_number' => 'require',
        'y_name' => 'require',
        'sfz' => 'require',
        'sheet_number' => 'require',
        'y_id' => 'require',
    ];

    protected $message = [
        'owner_name.require' => '业主姓名不能为空',
        'owner_phone.require' => '业主联系电话不能为空',
        'earnest.require' => '定金不能为空',
        'earnest.number' => '定金限定为整数',
        'zhuzhi.require' => '项目地址不能为空',
        'c_zhuzhi.require' => '常驻地址不能为空',
        'farmers_number.require' => '农户信息编号不能为空',
        'y_name.require' => '业务员不能为空',
        'sfz.require' => '身份证号不能为空',
        'sheet_number.require' => '意向单编号不能为空',
        'd_type.require' => '电站类型不能为空',
        'y_type.require' => '业主类型不能为空',
        'y_id.require' => '业主编号不能为空',
    ];

    protected $scene = [
        'send' => ['y_id','owner_name','owner_phone','earnest','zhuzhi','c_zhuzhi','farmers_number'],
        'check' => ['y_id','sheet_number','d_type','y_name','y_type','owner_name','sfz','owner_phone','zhuzhi','c_zhuzhi'],
    ];
}