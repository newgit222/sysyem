<?php
return [
    'admin_api_config' =>[
//        [
//            'name' => 'zhuankayemian',
//            'type' => 30,
//            'title' => '转卡页面',
//            'value' => 1
//        ],
        [
            'name' => 'order_invalid_time',
            'type' => 30,
            'title' => '订单超时时间(分钟)',
            'value' => 10,
            'attr' => 'input'
        ],
        [
            'name' => 'get_pay_name_type',
            'type' => 30,
            'title' => '获取支付用户姓名方式',
            'value' => 1,
            'attr' => 'input'
        ],
        [
            'name' => 'is_pay_name',
            'type' => 30,
            'title' => '支付页面是否提交姓名',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_amount_float_range',
            'type' => 30,
            'title' => '订单浮动金额个数',
            'value' => 20,
            'attr' => 'input'
        ],
        [
            'name' => 'order_amount_float_no_top',
            'type' => 30,
            'title' => '订单金额不上浮',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'money_is_float',
            'type' => 30,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'verify_ms_callback_ip',
            'type' => 30,
            'title' => '验证码商回调ip',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'money_close_code',
            'type' => 30,
            'title' => '余额1w禁用收款码',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'kzk_pay_template',
            'type' => 30,
            'title' => '收银台模板',
            'value' => 2,
            'extra' => '1:原始模板,2:最新模板,3:不要支付宝提示模板',
            'attr' => 'select'
        ],
        [
            'name' => 'order_invalid_time',
            'type' => 32,
            'title' => '订单超时时间(分钟)',
            'value' => 10,
            'attr' => 'input'
        ],
        [
            'name' => 'is_pay_name',
            'type' => 32,
            'title' => '支付页面是否提交姓名',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'money_is_float',
            'type' => 32,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_h5',
            'type' => 32,
            'title' => '是否启用支付宝H5',
            'value' => 2,
            'extra' => '1:启用,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_show_name',
            'type' => 32,
            'title' => '收银台展示收款人姓名',
            'value' => 2,
            'extra' => '1:启用,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_amount_float_range',
            'type' => 32,
            'title' => '订单浮动金额个数',
            'value' => 20,
            'attr' => 'input'
        ],
        [
            'name' => 'order_amount_float_no_top',
            'type' => 32,
            'title' => '订单金额不上浮',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'ms_code_money_status',
            'type' => 32,
            'title' => '码商设置收款码金额区间',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'api_min_money',
            'type' => 32,
            'title' => '收款码收款最小金额(码商设置收款码金额区间关闭后才有作用)',
            'value' => 0,
            'attr' => 'input'
        ],
        [
            'name' => 'order_invalid_time',
            'type' => 33,
            'title' => '订单超时时间(分钟)',
            'value' => 10,
            'attr' => 'input'
        ],
        [
            'name' => 'api_max_money',
            'type' => 32,
            'title' => '收款码收款最大金额(码商设置收款码金额区间关闭后才有作用)',
            'value' => 0,
            'attr' => 'input'
        ],
        [
            'name' => 'ms_code_money_status',
            'type' => 33,
            'title' => '码商设置收款码金额区间',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'api_min_money',
            'type' => 33,
            'title' => '收款码收款最小金额(码商设置收款码金额区间关闭后才有作用)',
            'value' => 0,
            'attr' => 'input'
        ],
        [
            'name' => 'api_max_money',
            'type' => 33,
            'title' => '收款码收款最大金额(码商设置收款码金额区间关闭后才有作用)',
            'value' => 0,
            'attr' => 'input'
        ],
        [
            'name' => 'is_show_name',
            'type' => 33,
            'title' => '收银台展示收款人姓名',
            'value' => 2,
            'extra' => '1:启用,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_h5',
            'type' => 33,
            'title' => '是否启用支付宝H5',
            'value' => 2,
            'extra' => '1:启用,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_amount_float_no_top',
            'type' => 33,
            'title' => '订单金额不上浮',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'money_is_float',
            'type' => 33,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_pay_name',
            'type' => 33,
            'title' => '支付页面是否提交姓名',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_amount_float_range',
            'type' => 33,
            'title' => '订单浮动金额个数',
            'value' => 20,
            'attr' => 'input'
        ],
        [
            'name' => 'order_amount_float_no_top',
            'type' => 33,
            'title' => '订单金额不上浮',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_invalid_time',
            'type' => 34,
            'title' => '订单超时时间(分钟)',
            'value' => 10,
            'attr' => 'input'
        ],
        [
            'name' => 'money_is_float',
            'type' => 34,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_amount_lock',
            'type' => 34,
            'title' => '锁码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_invalid_time',
            'type' => 35,
            'title' => '订单超时时间(分钟)',
            'value' => 10,
            'attr' => 'input'
        ],
        [
            'name' => 'money_is_float',
            'type' => 35,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_amount_lock',
            'type' => 35,
            'title' => '是否锁码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_pay_pass',
            'type' => 35,
            'title' => '确认收款是否需要确认安全码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_amount_float_range',
            'type' => 35,
            'title' => '订单浮动金额个数',
            'value' => 20,
            'attr' => 'input'
        ],
        [
            'name' => 'order_amount_float_no_top',
            'type' => 35,
            'title' => '订单金额不上浮',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_invalid_time',
            'type' => 36,
            'title' => '订单超时时间(分钟)',
            'value' => 10,
            'attr' => 'input'
        ],
        [
            'name' => 'money_is_float',
            'type' => 36,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_amount_lock',
            'type' => 36,
            'title' => '是否锁码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_invalid_time',
            'type' => 37,
            'title' => '订单超时时间(分钟)',
            'value' => 10,
            'extra' => '',
            'attr' => 'input'
        ],
        [
            'name' => 'money_is_float',
            'type' => 37,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_amount_lock',
            'type' => 37,
            'title' => '是否锁码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_pay_name',
            'type' => 37,
            'title' => '支付页面是否提交姓名',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'ms_code_money_status',
            'type' => 37,
            'title' => '码商设置收款码金额区间',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
//        [
//            'name' => 'order_invalid_time',
//            'type' => 38,
//            'title' => '订单超时时间(分钟)',
//            'value' => 10,
//            'attr' => 'input'
//        ],
        [
            'name' => 'ms_code_money_status',
            'type' => 38,
            'title' => '码商设置收款码金额区间',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'api_min_money',
            'type' => 38,
            'title' => '收款码收款最小金额(码商设置收款码金额区间关闭后才有作用)',
            'value' => 0,
            'attr' => 'input'
        ],
        [
            'name' => 'api_max_money',
            'type' => 38,
            'title' => '收款码收款最大金额(码商设置收款码金额区间关闭后才有作用)',
            'value' => 0,
            'attr' => 'input'
        ],
        [
            'name' => 'money_is_float',
            'type' => 38,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_amount_float_no_top',
            'type' => 38,
            'title' => '订单金额不上浮',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_amount_lock',
            'type' => 38,
            'title' => '是否锁码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_invalid_time',
            'type' => 39,
            'title' => '订单超时时间(分钟)',
            'value' => 10,
            'extra' => '',
            'attr' => 'input'
        ],
        [
            'name' => 'money_is_float',
            'type' => 39,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_pay_name',
            'type' => 39,
            'title' => '支付页面是否提交姓名',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'initialization_h5',
            'type' => 39,
            'title' => '初始化页面跳转支付宝',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_amount_float_no_top',
            'type' => 39,
            'title' => '订单金额不上浮',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'pay_template',
            'type' => 39,
            'title' => '支付页面模板',
            'value' => 1,
            'extra' => '1:模板-1,2:模板-2【支付宝加好友转账】,3:模板-3【聚合码】,4:模板-4【6-24修改】',
            'attr' => 'select'
	],
        [
            'name' => 'order_amount_float_range',
            'type' => 39,
            'title' => '订单浮动金额个数',
            'value' => 20,
            'attr' => 'input'
        ],
        [
            'name' => 'ms_code_money_status',
            'type' => 39,
            'title' => '码商设置收款码金额区间',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'api_min_money',
            'type' => 39,
            'title' => '收款码收款最小金额',
            'value' => 0,
            'attr' => 'input'
        ],
        [
            'name' => 'api_max_money',
            'type' => 39,
            'title' => '收款码收款最大金额',
            'value' => 0,
            'attr' => 'input'
        ],
        [
            'name' => 'is_amount_lock',
            'type' => 39,
            'title' => '锁码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
//        [
//            'name' => 'ms_shoukuan_vermoney',
//            'type' => 39,
//            'title' => '码商确认收款校验金额',
//            'value' => 2,
//            'extra' => '1:开启,2:关闭',
//            'attr' => 'select'
//        ],
        [
            'name' => 'is_amount_lock',
            'type' => 40,
            'title' => '锁码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'money_is_float',
            'type' => 40,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_amount_float_range',
            'type' => 40,
            'title' => '订单浮动金额个数',
            'value' => 20,
            'attr' => 'input'
        ],
        [
            'name' => 'order_amount_float_no_top',
            'type' => 40,
            'title' => '订单金额不上浮',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_pay_pass',
            'type' => 40,
            'title' => '确认收款是否需要确认安全码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_invalid_time',
            'type' => 40,
            'title' => '订单超时时间(分钟)',
            'value' => 10,
            'extra' => '',
            'attr' => 'input'
        ],
        [
            'name' => 'is_show_not_success',
            'type' => 40,
            'title' => '自动刷新时是否只显示未支付订单',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_confirm_box',
            'type' => 40,
            'title' => '确认收款时是否确认信息',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'ms_code_money_status',
            'type' => 40,
            'title' => '码商设置收款码金额区间',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'api_min_money',
            'type' => 40,
            'title' => '收款码收款最小金额(码商设置收款码金额区间关闭后才有作用)',
            'value' => 0,
            'attr' => 'input'
        ],
        [
            'name' => 'api_max_money',
            'type' => 40,
            'title' => '收款码收款最大金额(码商设置收款码金额区间关闭后才有作用)',
            'value' => 0,
            'attr' => 'input'
        ],
        [
            'name' => 'is_pay_pass',
            'type' => 40,
            'title' => '确认收款需要确认安全码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_invalid_time',
            'type' => 41,
            'title' => '订单超时时间(分钟)',
            'value' => 10,
            'extra' => '',
            'attr' => 'input'
        ],
        [
            'name' => 'money_is_float',
            'type' => 41,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_amount_lock',
            'type' => 41,
            'title' => '锁码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'ms_code_money_status',
            'type' => 41,
            'title' => '码商设置收款码金额区间',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
//        [
//            'name' => 'pay_template',
//            'type' => 41,
//            'title' => '支付页面模板',
//            'value' => 1,
//            'extra' => '1:模板-1【个人主页链接】,2:模板-2【个人主页二维码】',
//            'attr' => 'select'
//        ],
        [
            'name' => 'ms_code_money_status',
            'type' => 42,
            'title' => '码商设置收款码金额区间',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'api_min_money',
            'type' => 42,
            'title' => '收款码收款最小金额(码商设置收款码金额区间关闭后才有作用)',
            'value' => 0,
            'attr' => 'input'
        ],
        [
            'name' => 'api_max_money',
            'type' => 42,
            'title' => '收款码收款最大金额(码商设置收款码金额区间关闭后才有作用)',
            'value' => 0,
            'attr' => 'input'
        ],
        [
            'name' => 'is_pay_name',
            'type' => 42,
            'title' => '支付页面是否提交姓名',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'money_is_float',
            'type' => 42,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_invalid_time',
            'type' => 42,
            'title' => '订单超时时间(分钟)',
            'value' => 10,
            'extra' => '',
            'attr' => 'input'
        ],
        [
            'name' => 'pay_template',
            'type' => 42,
            'title' => '支付模板',
            'value' => 1,
            'extra' => '1:默认模板,2:gif引导',
            'attr' => 'select'
        ],
        [
            'name' => 'money_is_float',
            'type' => 43,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_invalid_time',
            'type' => 43,
            'title' => '订单超时时间(分钟)',
            'value' => 10,
            'extra' => '',
            'attr' => 'input'
        ],
        [
            'name' => 'is_pay_pass',
            'type' => 43,
            'title' => '确认收款是否需要确认安全码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_amount_lock',
            'type' => 43,
            'title' => '是否锁码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'ms_show_card',
            'type' => 43,
            'title' => '码商后台是否展示卡密',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'money_is_float',
            'type' => 44,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'ewm_is_pay_money',
            'type' => 44,
            'title' => '二维码是否有固定支付金额（1为有，2为没有）',
            'value' => 1,
            'extra' => '1:存在,2:没有',
            'attr' => 'select'
        ],
        [
            'name' => 'is_pay_name',
            'type' => 44,
            'title' => '支付页面是否提交姓名',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_invalid_time',
            'type' => 44,
            'title' => '订单超时时间(分钟)',
            'value' => 10,
            'extra' => '',
            'attr' => 'input'
        ],
        [
            'name' => 'order_invalid_time',
            'type' => 45,
            'title' => '订单超时时间(分钟)',
            'value' => 10,
            'attr' => 'input'
        ],
        [
            'name' => 'money_is_float',
            'type' => 45,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_amount_lock',
            'type' => 45,
            'title' => '是否锁码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
//        [
//            'name' => 'order_invalid_time',
//            'type' => 46,
//            'title' => '订单超时时间(分钟)',
//            'value' => 10,
//            'attr' => 'input'
//        ],
//        [
//            'name' => 'money_is_float',
//            'type' => 46,
//            'title' => '订单金额浮动',
//            'value' => 1,
//            'extra' => '1:开启,2:关闭',
//            'attr' => 'select'
//        ],
        [
            'name' => 'order_invalid_time',
            'type' => 46,
            'title' => '订单超时时间(分钟)',
            'value' => 10,
            'extra' => '',
            'attr' => 'input'
        ],
        [
            'name' => 'money_is_float',
            'type' => 46,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_pay_name',
            'type' => 46,
            'title' => '支付页面是否提交姓名',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'initialization_h5',
            'type' => 46,
            'title' => '初始化页面跳转支付宝',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_amount_float_no_top',
            'type' => 46,
            'title' => '订单金额不上浮',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'pay_template',
            'type' => 46,
            'title' => '支付页面模板',
            'value' => 1,
            'extra' => '1:模板-1,2:模板-2【支付宝加好友转账】,3:模板-3【聚合码】,4:模板-4【6-24修改】',
            'attr' => 'select'
        ],
        [
            'name' => 'order_amount_float_range',
            'type' => 46,
            'title' => '订单浮动金额个数',
            'value' => 20,
            'attr' => 'input'
        ],
        [
            'name' => 'ms_code_money_status',
            'type' => 46,
            'title' => '码商设置收款码金额区间',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'api_min_money',
            'type' => 46,
            'title' => '收款码收款最小金额',
            'value' => 0,
            'attr' => 'input'
        ],
        [
            'name' => 'api_max_money',
            'type' => 46,
            'title' => '收款码收款最大金额',
            'value' => 0,
            'attr' => 'input'
        ],
        [
            'name' => 'is_amount_lock',
            'type' => 46,
            'title' => '锁码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_amount_lock',
            'type' => 47,
            'title' => '是否锁码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_invalid_time',
            'type' => 47,
            'title' => '订单超时时间(分钟)',
            'value' => 10,
            'attr' => 'input'
        ],
        [
            'name' => 'money_is_float',
            'type' => 47,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_amount_lock',
            'type' => 48,
            'title' => '是否锁码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'money_is_float',
            'type' => 48,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_amount_float_no_top',
            'type' => 48,
            'title' => '订单金额不上浮',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_amount_lock',
            'type' => 50,
            'title' => '锁码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'money_is_float',
            'type' => 50,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_amount_float_range',
            'type' => 50,
            'title' => '订单浮动金额个数',
            'value' => 20,
            'attr' => 'input'
        ],
        [
            'name' => 'order_amount_float_no_top',
            'type' => 50,
            'title' => '订单金额不上浮',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_invalid_time',
            'type' => 51,
            'title' => '订单超时时间(分钟)',
            'value' => 10,
            'extra' => '',
            'attr' => 'input'
        ],
        [
            'name' => 'money_is_float',
            'type' => 51,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_pay_name',
            'type' => 51,
            'title' => '支付页面是否提交姓名',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'initialization_h5',
            'type' => 51,
            'title' => '初始化页面跳转支付宝',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_amount_float_no_top',
            'type' => 51,
            'title' => '订单金额不上浮',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'pay_template',
            'type' => 51,
            'title' => '支付页面模板',
            'value' => 1,
            'extra' => '1:模板-1,2:模板-2【支付宝加好友转账】,3:模板-3【聚合码】,4:模板-4【6-24修改】',
            'attr' => 'select'
        ],
        [
            'name' => 'order_amount_float_range',
            'type' => 51,
            'title' => '订单浮动金额个数',
            'value' => 20,
            'attr' => 'input'
        ],
        [
            'name' => 'ms_code_money_status',
            'type' => 51,
            'title' => '码商设置收款码金额区间',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'api_min_money',
            'type' => 51,
            'title' => '收款码收款最小金额',
            'value' => 0,
            'attr' => 'input'
        ],
        [
            'name' => 'api_max_money',
            'type' => 51,
            'title' => '收款码收款最大金额',
            'value' => 0,
            'attr' => 'input'
        ],
        [
            'name' => 'ms_shoukuan_vermoney',
            'type' => 51,
            'title' => '码商确认收款校验金额',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_amount_lock',
            'type' => 51,
            'title' => '锁码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'money_is_float',
            'type' => 52,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_invalid_time',
            'type' => 52,
            'title' => '订单超时时间(分钟)',
            'value' => 10,
            'extra' => '',
            'attr' => 'input'
        ],
        [
            'name' => 'is_pay_pass',
            'type' => 52,
            'title' => '确认收款是否需要确认安全码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_amount_lock',
            'type' => 52,
            'title' => '是否锁码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_amount_lock',
            'type' => 54,
            'title' => '是否锁码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_invalid_time',
            'type' => 54,
            'title' => '订单超时时间(分钟)',
            'value' => 10,
            'extra' => '',
            'attr' => 'input'
        ],
        [
            'name' => 'money_is_float',
            'type' => 54,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_amount_float_range',
            'type' => 54,
            'title' => '订单浮动金额个数',
            'value' => 20,
            'attr' => 'input'
        ],
        [
            'name' => 'is_pay_name',
            'type' => 54,
            'title' => '支付页面是否提交姓名',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_amount_float_no_top',
            'type' => 54,
            'title' => '订单金额不上浮',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'pay_template',
            'type' => 54,
            'title' => '支付页面模板',
            'value' => 1,
            'extra' => '1:模板-1,2:模板-2【支付宝加好友转账】,3:模板-3【聚合码】,4:模板-4【6-24修改】',
            'attr' => 'select'
        ],
        [
            'name' => 'ms_code_money_status',
            'type' => 54,
            'title' => '码商设置收款码金额区间',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'api_min_money',
            'type' => 54,
            'title' => '收款码收款最小金额(码商设置收款码金额区间关闭后才有作用)',
            'value' => 0,
            'attr' => 'input'
        ],
        [
            'name' => 'is_amount_lock',
            'type' => 54,
            'title' => '锁码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'api_max_money',
            'type' => 54,
            'title' => '收款码收款最大金额(码商设置收款码金额区间关闭后才有作用)',
            'value' => 0,
            'attr' => 'input'
        ],
        [
            'name' => 'order_invalid_time',
            'type' => 55,
            'title' => '订单超时时间(分钟)',
            'value' => 10,
            'attr' => 'input'
        ],
        [
            'name' => 'is_h5',
            'type' => 55,
            'title' => '是否启用支付宝H5',
            'value' => 2,
            'extra' => '1:启用,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_wechat',
            'type' => 55,
            'title' => '支持微信',
            'value' => 1,
            'extra' => '1:支持,2:不支持',
            'attr' => 'select'
        ],
        [
            'name' => 'is_pay_name',
            'type' => 55,
            'title' => '支付页面是否提交姓名',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'money_is_float',
            'type' => 55,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_invalid_time',
            'type' => 56,
            'title' => '订单超时时间(分钟)',
            'value' => 10,
            'attr' => 'input'
        ],
        [
            'name' => 'money_is_float',
            'type' => 56,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_amount_float_no_top',
            'type' => 56,
            'title' => '订单金额不上浮',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_amount_lock',
            'type' => 56,
            'title' => '锁码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_invalid_time',
            'type' => 57,
            'title' => '订单超时时间(分钟)',
            'value' => 10,
            'attr' => 'input'
        ],
        [
            'name' => 'order_amount_float_no_top',
            'type' => 57,
            'title' => '订单金额不上浮',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'money_is_float',
            'type' => 57,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_amount_lock',
            'type' => 57,
            'title' => '锁码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_reason',
            'type' => 57,
            'title' => '订单商品名称(用逗号隔开随机出1个)',
            'value' =>'护肤品,生活品,苹果,香蕉',
            'attr' => 'input'
        ],
        [
            'name' => 'order_invalid_time',
            'type' => 58,
            'title' => '订单超时时间(分钟)',
            'value' => 10,
            'attr' => 'input'
        ],
        [
            'name' => 'is_pay_name',
            'type' => 58,
            'title' => '支付页面是否提交姓名',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'money_is_float',
            'type' => 58,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
//        [
//            'name' => 'daifu_no_brushing_status',
//            'type' => 255,
//            'title' => '码商禁止刷单开关',
//            'value' => 1,
//            'extra' => '1:开启,2:关闭',
//            'attr' => 'select'
//        ],
        [
            'name' => 'order_invalid_time',
            'type' => 59,
            'title' => '订单超时时间(分钟)',
            'value' => 10,
            'attr' => 'input'
        ],
        [
            'name' => 'is_amount_lock',
            'type' => 59,
            'title' => '锁码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'pay_template',
            'type' => 59,
            'title' => '支付页面模板',
            'value' => 1,
            'extra' => '1:模板-1,2:模板-核销模式,3:跳转淘宝模板,4:核销模式-提交核销码',
            'attr' => 'select'
        ],
        [
            'name' => 'money_is_float',
            'type' => 60,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_invalid_time',
            'type' => 60,
            'title' => '订单超时时间(分钟)',
            'value' => 10,
            'extra' => '',
            'attr' => 'input'
        ],
        [
            'name' => 'is_pay_pass',
            'type' => 60,
            'title' => '确认收款是否需要确认安全码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_amount_lock',
            'type' => 60,
            'title' => '是否锁码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'ms_show_card',
            'type' => 60,
            'title' => '码商后台是否展示卡密',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_invalid_time',
            'type' => 61,
            'title' => '订单超时时间(分钟)',
            'value' => 10,
            'attr' => 'input'
        ],
        [
            'name' => 'money_is_float',
            'type' => 61,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_amount_lock',
            'type' => 61,
            'title' => '是否锁码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'money_is_float',
            'type' => 62,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_amount_lock',
            'type' => 62,
            'title' => '是否锁码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'money_is_float',
            'type' => 63,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_amount_lock',
            'type' => 63,
            'title' => '是否锁码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'money_is_float',
            'type' => 65,
            'title' => '订单金额浮动',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'is_amount_lock',
            'type' => 65,
            'title' => '锁码',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'order_invalid_time',
            'type' => 65,
            'title' => '订单超时时间(分钟)',
            'value' => 10,
            'attr' => 'input'
        ],
        [
            'name' => 'admin_daifu_number',
            'type' => 255,
            'title' => '码商代付抢单笔数限制',
            'value' => 3,
            'attr' => 'input'
        ],
        [
            'name' => 'daifu_success_uplode',
            'type' => 255,
            'title' => '代付成功上传图片',
            'value' => 1,
            'extra' => '1:关闭,2:开启',
            'attr' => 'select'
        ],
        [
            'name' => 'daifu_auto_uplode',
            'type' => 255,
            'title' => '代付成功强制上传图片',
            'value' => 1,
            'extra' => '1:关闭,2:开启',
            'attr' => 'select'
        ],
        [
            'name' => 'daifu_orders_username',
            'type' => 255,
            'title' => '码商订单列表展示商户名',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'daifu_5miu_no_Duplicate_card',
            'type' => 255,
            'title' => '5分钟内不允许重复卡号金额进单',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'daifu_refresh_isver',
            'type' => 255,
            'title' => '代付刷新限制方式',
            'value' => 1,
            'extra' => '1:禁止1分钟,2:输入二维码',
            'attr' => 'select'
        ],
        [
            'name' => 'daifu_refresh_frequency',
            'type' => 255,
            'title' => '代付列表刷新频率(2分钟内)',
            'value' => 120,
            'attr' => 'input'
        ],
        [
            'name' => 'daifu_expired_return',
            'type' => 255,
            'title' => '订单不处理返回时间（分钟）',
            'value' => 0,
            'attr' => 'input'
        ],
        [
            'name' => 'daifu_err_reason',
            'type' => 255,
            'title' => '代付失败原因',
            'value' => '',
            'attr' => 'textarea'
        ],
        [
            'name' => 'max_ewm_num',
            'type' => 30,
            'title' => '最大二维码数量',
            'value' => 20,
            'extra' => '',
            'attr' => 'input'
        ],
        [
            'name' => 'max_ewm_num',
            'type' => 32,
            'title' => '最大二维码数量',
            'value' => 20,
            'extra' => '',
            'attr' => 'input'
        ],
        [
            'name' => 'max_ewm_num',
            'type' => 33,
            'title' => '最大二维码数量',
            'value' => 20,
            'extra' => '',
            'attr' => 'input'
        ],
        [
            'name' => 'max_ewm_num',
            'type' => 34,
            'title' => '最大二维码数量',
            'value' => 20,
            'extra' => '',
            'attr' => 'input'
        ],
        [
            'name' => 'max_ewm_num',
            'type' => 35,
            'title' => '最大二维码数量',
            'value' => 20,
            'extra' => '',
            'attr' => 'input'
        ],
        [
            'name' => 'max_ewm_num',
            'type' => 36,
            'title' => '最大二维码数量',
            'value' => 20,
            'extra' => '',
            'attr' => 'input'
        ],
        [
            'name' => 'max_ewm_num',
            'type' => 37,
            'title' => '最大二维码数量',
            'value' => 20,
            'extra' => '',
            'attr' => 'input'
        ],
        [
            'name' => 'max_ewm_num',
            'type' => 38,
            'title' => '最大二维码数量',
            'value' => 20,
            'extra' => '',
            'attr' => 'input'
        ],
        [
            'name' => 'max_ewm_num',
            'type' => 39,
            'title' => '最大二维码数量',
            'value' => 20,
            'extra' => '',
            'attr' => 'input'
        ],
        [
            'name' => 'max_ewm_num',
            'type' => 40,
            'title' => '最大二维码数量',
            'value' => 20,
            'extra' => '',
            'attr' => 'input'
        ],
        [
            'name' => 'max_ewm_num',
            'type' => 41,
            'title' => '最大二维码数量',
            'value' => 20,
            'extra' => '',
            'attr' => 'input'
        ],
        [
            'name' => 'max_ewm_num',
            'type' => 42,
            'title' => '最大二维码数量',
            'value' => 20,
            'extra' => '',
            'attr' => 'input'
        ],
        [
            'name' => 'max_ewm_num',
            'type' => 43,
            'title' => '最大二维码数量',
            'value' => 20,
            'extra' => '',
            'attr' => 'input'
        ],
        [
            'name' => 'max_ewm_num',
            'type' => 44,
            'title' => '最大二维码数量',
            'value' => 20,
            'extra' => '',
            'attr' => 'input'
        ],
        [
            'name' => 'max_ewm_num',
            'type' => 45,
            'title' => '最大二维码数量',
            'value' => 20,
            'extra' => '',
            'attr' => 'input'
        ],
        [
            'name' => 'cardKey_order_view',
            'type' => 43,
            'title' => '卡密上传后显示订单',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'cardKey_order_view',
            'type' => 52,
            'title' => '卡密上传后显示订单',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'channel_show',
            'type' => '0',
            'title' => '是否展示通道',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
        [
            'name' => 'confirm_payment_show',
            'type' => '39',
            'title' => '确认收款显示',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select'
        ],
    ],
    'admin_sys_config' => [
        [
            'name' => 'no_orders',
            'type' => 256,
            'title' => '代收日切开关，开启后无法进单',
            'value' => 1,
            'extra' => '1:关闭,2:开启',
            'attr' => 'select',
            'option_type' => 'system'
        ],
        [
            'name' => 'daifu_no_orders',
            'type' => 256,
            'title' => '代付日切开关，开启后无法进单',
            'value' => 1,
            'extra' => '1:关闭,2:开启',
            'attr' => 'select',
            'option_type' => 'df'
        ],
        [
            'name' => 'riqie_success_orders',
            'type' => 256,
            'title' => '日切不许码商完成订单',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select',
            'option_type' => 'system'
        ],
        [
            'name' => 'ms_order_min_money',
            'type' => 256,
            'title' => '码商保底接单金额',
            'value' => 2000,
            'attr' => 'input',
            'option_type' => 'ms'
        ],
        [
            'name' => 'ms_rate_night_add',
            'type' => 256,
            'title' => '码商夜间费率加成',
            'value' => 0,
            'attr' => 'input',
            'option_type' => 'ms'
        ],
        [
            'name' => 'ms_rate_night_add_start_time_h',
            'type' => 256,
            'title' => '码商夜间费率开始',
            'value' => 0,
            'attr' => 'input',
            'option_type' => 'ms'
        ],
        [
            'name' => 'ms_rate_night_add_end_time_h',
            'type' => 256,
            'title' => '码商夜间费率结束',
            'value' => 0,
            'attr' => 'input',
            'option_type' => 'ms'
        ],
        [
            'name' => 'sys_operating_password',
            'type' => 256,
            'title' => '设置操作口令',
            'value' => 0,
            'attr' => 'input',
            'option_type' => 'system'
        ],
        [
            'name' => 'ms_df_night_add',
            'type' => 256,
            'title' => '码商代付夜间费率加成',
            'value' => 0,
            'attr' => 'input',
            'option_type' => 'ms'
        ],
        [
            'name' => 'ms_df_night_add_start_time_h',
            'type' => 256,
            'title' => '码商代付夜间费率开始',
            'value' => 0,
            'attr' => 'input',
            'option_type' => 'ms'
        ],
        [
            'name' => 'ms_df_night_add_end_time_h',
            'type' => 256,
            'title' => '码商代付夜间费率结束',
            'value' => 0,
            'attr' => 'input',
            'option_type' => 'ms'
        ],
        [
            'name' => 'daifu_to_daifumoney',
            'type' => 256,
            'title' => '码商代付完成到代付余额',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select',
            'option_type' => 'ms'
        ],
        [
            'name' => 'cashier_address',
            'type' => 256,
            'title' => '收银台服务器地址',
            'value' => '0',
            'extra' => '1:国内高防-【ip：43.228.71.217】-【217】,2:国内高防-【ip：103.37.17.40】-【40】,3:阿里云香港-【ip：web85sytpay.hengaofangfuwuo95.com】,4:腾讯云香港-【ip：web91txapipay.hengaofangfuwuqiniubibbc.com】,5:香港物理-【ip：apigogozz61pay.gaofangfuwuqi002.com】,6:国内高防-【IP：103.37.17.66】,7:国内高防-【IP：43.228.69.16】,8:国内高防-【IP：110.42.1.4】',
            'attr' => 'select',
            'option_type' => 'system'
        ],
        [
            'name' => 'sys_code_type',
            'type' => 256,
            'title' => '系统派单方式',
            'value' => 0,
            'extra' => '0:顺序轮询,1:随机权重分配',
            'attr' => 'select',
            'option_type' => 'system'
        ],
        [
            'name' => 'permit_ms_huafen_money',
            'type' => 256,
            'title' => '码商给下级划分余额',
            'value' => 1,
            'extra' => '2:关闭,1:开启',
            'attr' => 'select',
            'option_type' => 'ms'
        ],
        [
            'name' => 'disable_ms_money_status',
            'type' => 256,
            'title' => '码商进单冻结余额（正在跑量的时候切勿操作，否则后果自负！！！）',
            'value' => 2,
            'extra' => '2:关闭,1:开启',
            'attr' => 'select',
            'option_type' => 'ms'
        ],
        [
            'name' => 'user_withdraw_google_code_on',
            'type' => 256,
            'title' => '商户提现开启google验证码',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select',
            'option_type' => 'system'
        ],
        [
            'name' => 'daifu_on',
            'type' => 256,
            'title' => '开启代付',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select',
            'option_type' => 'df'
        ],
        [
            'name' => 'exclusive_transfer',
            'type' => 256,
            'title' => '专属中转【切勿乱动，请咨询客服后再操作】',
            'value' => 0,
            'attr' => 'input',
            'option_type' => 'system'
        ],
        [
            'name' => 'one_notify_transfer',
            'type' => 256,
            'title' => '第一次回调ip【切勿乱动，请咨询客服后再操作】',
            'value' => '',
            'attr' => 'input',
            'option_type' => 'system'
        ],
        [
            'name' => 'two_notify_transfer',
            'type' => 256,
            'title' => '第二次回调ip【切勿乱动，请咨询客服后再操作】',
            'value' => '',
            'attr' => 'input',
            'option_type' => 'system'
        ],
        [
            'name' => 'three_notify_transfer',
            'type' => 256,
            'title' => '第三次回调ip【切勿乱动，请咨询客服后再操作】',
            'value' => '',
            'attr' => 'input',
            'option_type' => 'system'
        ],
        [
            'name' => 'four_notify_transfer',
            'type' => 256,
            'title' => '第四次回调ip【切勿乱动，请咨询客服后再操作】',
            'value' => '',
            'attr' => 'input',
            'option_type' => 'system'
        ],
        [
            'name' => 'five_notify_transfer',
            'type' => 256,
            'title' => '第五次回调ip【切勿乱动，请咨询客服后再操作】',
            'value' => '',
            'attr' => 'input',
            'option_type' => 'system'
        ],
        [
            'name' => 'pay_img_service_address',
            'type' => 256,
            'title' => '收款码图片地址',
            'value' => '',
            'attr' => 'input',
            'option_type' => 'system'
        ],
        [
            'name' => 'df_img_service_address',
            'type' => 256,
            'title' => '代付图片地址',
            'value' => '',
            'attr' => 'input',
            'option_type' => 'system'
        ],
        [
            'name' => 'ms_add_son',
            'type' => 256,
            'title' => '码商添加下级',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select',
            'option_type' => 'ms'
        ],
        [
            'name' => 'ms_select_level',
            'type' => 256,
            'title' => '码商查看层级',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select',
            'option_type' => 'ms'
        ],
        [
            'name' => 'ms_single_account_login',
            'type' => 256,
            'title' => '码商单一账号登录',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select',
            'option_type' => 'ms'
        ],
        [
            'name' => 'ms_success_ver_money',
            'type' => 256,
            'title' => '码商确认收款校验余额',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select',
            'option_type' => 'ms'
        ],
        [
            'name' => 'admin_view_level1_ms',
            'type' => 256,
            'title' => '后台只查看一级码商',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select',
            'option_type' => 'system'
        ],
        [
            'name' => 'member_no_nullRate',
            'type' => 256,
            'title' => '码商未设置费率不让接单',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select',
            'option_type' => 'ms'
        ],
        [
            'name' => 'member_view_queue',
            'type' => 256,
            'title' => '码商显示排队位置',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select',
            'option_type' => 'ms'
        ],
        [
            'name' => 'ms_is_hexiao',
            'type' => 256,
            'title' => '码商后台显示核销',
            'value' => 1,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select',
            'option_type' => 'ms'
        ],
        [
            'name' => 'ms_accept_notice',
            'type' => 256,
            'title' => '码商开启公告提醒',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select',
            'option_type' => 'ms'
        ],
        [
            'name' => 'ms_djje_budan',
            'type' => 256,
            'title' => '码商用冻结金额补单',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select',
            'option_type' => 'ms'
        ],
        [
            'name' => 'ms_3min_qidan',
            'type' => 256,
            'title' => '码商代付接单后3分钟内不许弃单',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select',
            'option_type' => 'ms'
        ],
        [
            'name' => 'ms_shuadan_daojishi',
            'type' => 256,
            'title' => '码商刷单后禁用时间（秒）',
            'value' => '90',
            'attr' => 'input',
            'option_type' => 'ms'
        ],
        [
            'name' => 'ms_daifu_show10',
            'type' => 256,
            'title' => '码商代付显示10条',
            'value' => 2,
            'extra' => '1:开启,2:关闭',
            'attr' => 'select',
            'option_type' => 'ms'
        ],
    ]



];
