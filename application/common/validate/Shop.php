<?php


namespace app\common\validate;


class Shop extends BaseValidate
{

    protected $rule = [
        'id'               => 'require',
        'name'         => 'require',
        'type'           => 'require|integer'
    ];

    protected $message = [
        'id.require'       => 'ID参数不能为空',
        'name.require'  =>  '商铺名称不能为空',
        'type.require'  =>  '商铺名称类型不能为空',
        'type.integer'  =>  '商铺名称类型错误',
    ];

    protected $scene = [
        'add' => ['name', 'type'] ,
        'edit' => ['id','name', 'type'] ,
    ];
}