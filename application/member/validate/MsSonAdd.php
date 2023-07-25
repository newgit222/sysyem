<?php


namespace app\member\validate;


use think\Validate;


/**
 * 二维码验证规则
 * Class EwmPayCode
 * @package app\ms\validate
 */
class MsSonAdd extends Validate
{

    protected $rule = [
        'ms_name'          =>  'require|max:30|chsAlphaNum', //chsAlphaNum 只能是汉字、字母和数字  token
        'ms_password'        =>  'require|min:6|max:20|alphaDash', // alphaDash 是否为字母和数字，下划线_及破折号-
        '__token__' => 'require|token'
    ];

    protected $message = [
        'ms_name.require' => '服务商名称必填',
        'ms_name.max' => '服务商名称最大30位',
        'ms_password.require' => '密码必填',
        'ms_password.min' => '密码最小6位',
        'ms_password.max' => '密码最大20位',
        'ms_name.chsAlphaNum'  => '用户名只能是汉字、字母和数字',
        'ms_password.alphaDash'  => '密码只能是字母和数字，下划线_及破折号-',
    ];

    protected $scene = [
        'addMs'      =>  ['ms_name', 'ms_password','__token__'],
    ];

}


