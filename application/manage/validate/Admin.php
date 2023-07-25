<?php

namespace app\manage\validate;

use app\common\validate\BaseValidate;

class Admin extends BaseValidate
{
    // 验证规则
    protected $rule =   [

        'username'      => 'require|unique:admin',
        'nickname'      => 'require',
        'password'      => 'require|length:6,20',
        'email'         => 'require'
    ];

    // 验证提示
    protected $message  =   [

        'username.require'      => '用户名不能为空',
        'username.unique'       => '用户名已存在',
        'nickname.require'      => '昵称不能为空',
        'password.require'      => '密码不能为空',
        'password.length'       => '密码长度为6-20字符',
        'email.require'         => '邮箱不能为空',
    ];
    // 应用场景
    protected $scene = [
        'add'       =>  ['username','nickname','password','email'],
        'edit'      =>  ['nickname','email']
    ];
}