<?php


namespace app\api\validate;


use think\Validate;

class User extends Validate
{
    protected $rule = [
        'account' => 'require',
        'name' => 'require',
        'pwd' => 'require',
        'phone' => 'require|number|length:11',
        'oldpwd' => 'require',
        'newpwd' => 'require',
        'newpwds' => 'require',
        'node' => 'require',
        'email' => 'email',
    ];

    protected $message = [
        'account.require' => '账号不能为空',
        'name.require' => '姓名不能为空',
        'pwd.require' => '密码不能为空',
        'phone.require' => '手机号不能为空',
        'phone.number' => '手机号必须是数字',
        'phone.length' => '手机号必须是11位',
        'oldpwd.require' => '旧密码不能为空',
        'newpwd.require' => '新密码不能为空',
        'newpwds.require' => '重复密码不能为空',
        'node.require' => '角色不能为空',
        'email.email' => '邮箱格式不正确',
    ];

    protected $scene = [
        'send' => ['account','pwd'],
        'check' => 'phone',
        'pwds' => ['oldpwd','newpwd','newpwds'],
        'edit' => ['account','name','phone','node','email']
    ];
}