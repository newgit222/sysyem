<?php
/**
 *  +----------------------------------------------------------------------
 *  | 狂神系统系统 [ WE CAN DO IT JUST THINK ]
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2018 http://www.iredcap.cn All rights reserved.
 *  +----------------------------------------------------------------------
 *  | Licensed ( https://www.apache.org/licenses/LICENSE-2.0 )
 *  +----------------------------------------------------------------------
 *  | Author: Brian Waring <BrianWaring98@gmail.com>
 *  +----------------------------------------------------------------------
 */
namespace app\admin\validate;

/**
 * 登录验证器
 *
 * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
 *
 */
class Login extends BaseAdmin
{

    /**
     * 验证规则
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @var array
     */
    protected $rule =   [

        'username'  => 'require|max:25',
        'password'  => 'require|max:25',
        'vercode'   => 'require|captcha',
    ];

    /**
     * 验证消息
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @var array
     */
    protected $message  =   [

        'username.require'    => '用户名不能为空',
        'username.max'    => '用户名超出最大长度',
        'password.require'    => '密码不能为空',
        'password.max'    => '密码超出最大长度',
        'vercode.require'     => '验证码不能为空',
        'vercode.captcha'     => '验证码不正确',
    ];
}
