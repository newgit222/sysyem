<?php


namespace app\member\validate;


use think\Validate;


/**
 * 二维码验证规则
 * Class EwmPayCode
 * @package app\ms\validate
 */
class EwmPayCode extends Validate
{

    protected $rule = [
        'bank_name' => 'require|max:25',
        'account_name' => 'require|max:25|checkIsChinese',
        'account_number' => 'require|number|max:30',
//        'account_number' => 'require|integer|max:30',
        'security' => 'require|max:25',
        'min_money' => 'require|number',
        'max_money' => 'require|number',
        'pay_money' => 'require|number',
        'api_pay_money' => 'require|number',
        'limit__total' => 'require|number',
        'success_order_num' =>  'require|integer',
        'image_url' => 'require',
        'publicKey' => 'require',
        'privateKey' => 'require',
        '__token__' => 'require|token'
    ];

    protected $message = [
        'account_name.require' => '开户姓名必填',
        'account_name.max' => '开户姓名超出最大限制',
        'account_number.require' => '银行卡必填',
        'account_number.number' => '银行卡卡号必须是数字',
        'bank_name.require' => '开户行必填',
        'security.require' => '安全码必填',
        'min_money.require' => '单笔最小金额不能为空',
        'min_money.number' => '单笔最小金额只能为数字',
        'max_money.require' => '单笔最大金额不能为空',
        'max_money.number' => '单笔最大金额只能为数字',
        'pay_money.require' => '支付金额不能为空',
        'pay_money.number' => '支付金额只能为数字',
        'api_pay_money.require' => '请求金额不能为空',
        'api_pay_money.number' => '请求金额只能为数字',
        'limit__total.require' => '日限额金额不能为空',
        'limit__total.number' => '日限额金额只能为数字',
        'success_order_num.require' => '笔数上限不能为空',
        'success_order_num.integer' => '笔数上限只能为整数',
        '__token__.require' => '令牌为空',
        '__token__.token' => '令牌过期请刷新页面',
    ];

    protected function checkIsChinese($value)
    {
        return checkIsChinese($value) ? true : '开户姓名必须为中文';
    }


    protected function vlidateError($value)
    {
        return vlidateError($value) ? true : '开户姓名必须为中文';
    }

    protected $scene = [
            'kzk_add' => ['bank_name', 'account_name', 'account_number', 'security', 'success_order_num', '__token__'],
            'alipayCode_add' =>  [
                'bank_name'=>'require',
                'account_name' => 'require',
                'account_number' => 'require',
                'security', 'success_order_num','__token__'
            ],
        'alipayCodeSmall_add' =>  [
            'bank_name'=>'require',
            'account_name' => 'require',
            'account_number' => 'require',
            'security', 'success_order_num','__token__'
        ],
            'alipayPassRed_add' => [
                'account_name'=>'require',
                'bank_name'=>'require','__token__'
            ],
            'alipayUid_add' => [
                'bank_name'=>'require',
                'account_name' => 'require',
                'account_number' => 'require',
                'security', 'success_order_num','__token__'
            ],
            'alipayUidSmall_add' => [
                'bank_name'=>'require',
                'account_name' => 'require',
                'account_number' => 'require',
                'security', 'success_order_num','__token__'
            ],
            'alipayUidTransfer_add' => [
                'bank_name'=>'require',
                'account_name' => 'require',
                'account_number' => 'require',
                'security', 'success_order_num','__token__'
            ],
        'douyinGroupRed_add' => [
            'account_name' => 'require',
            'image_url' => 'require',
            'account_number' => 'require',
            'security', 'success_order_num','__token__'
        ],
        'wechatCode_add' => [
            'bank_name'=>'require',
            'account_name' => 'require',
            'security', 'success_order_num','__token__'
        ],
        'wechatGroupRed_add' => [
            'bank_name'=>'require',
            'account_name' => 'require',
            'image_url' => 'require',
            'security', 'success_order_num','__token__'
        ],
        'JDECard_add' => [
            'account_name' => 'require',
            'security', 'success_order_num','__token__'
        ],
        'CnyNumber_add' => [
            'bank_name'=>'require',
            'account_number' => 'require',
            'security', 'success_order_num','__token__'
        ],
        'AppletProducts_add' => [
            'bank_name'=>'require',
            'account_name' => 'require',
            'image_url' => 'require',
            'pay_money' => 'require',
            'api_pay_money' => 'require',
            'security', 'success_order_num','__token__'
        ],
        'QQFaceRed_add' => [
            'account_number' => 'require',
            'security', 'success_order_num','__token__'
        ],
        'alipaySmallPurse_add' => [
            'account_name' => 'require',
            'bank_name'=>'require',
            'image_url' => 'require',
            'security', 'success_order_num','__token__'
        ],
        'taoBaoMoneyRed_add' => [
            'bank_name'=>'require',
            'account_name' => 'require',
            'account_number' => 'require',
            'security', 'success_order_num','__token__'
        ],
        'alipayTransferCode_add' => [
            'account_name' => 'require',
            'bank_name'=>'require',
            'security', 'success_order_num','__token__'
        ],
        'alipayWorkCard_add' => [
            'account_name' => 'require',
            'bank_name'=>'require',
            'image_url' => 'require',
            'security', 'success_order_num','__token__'
        ],
        'alipayWorkCardBig_add' => [
            'account_name' => 'require',
            'bank_name'=>'require',
            'image_url' => 'require',
            'security', 'success_order_num','__token__'
        ],
        'CnyNumberAuto_add' => [
            'bank_name'=>'require',
            'account_number' => 'require',
            'security', 'success_order_num','__token__'
        ],
        'QianxinTransfer_add' => [
            'bank_name'=>'require',
            'account_name' => 'require',
            'account_number' => 'require',
            'security', 'success_order_num','__token__'
        ],
        'taoBaoEcard_add' => [
            'account_number' => 'require',
            'bank_name' => 'require',
            'security', 'success_order_num','__token__'
        ],
        'usdtTrc_add' => [
            'account_number' => 'require',
            'bank_name' => 'require',
            'security', 'success_order_num','__token__'
        ],
        'AggregateCode_add' =>  [
            'bank_name'=>'require',
            'image_url' => 'require',
            'security', 'success_order_num','__token__'
        ],
        'alipayF2F_add' =>  [
            'bank_name'=>'require',
            'account_number' => 'require',
            'publicKey' => 'require',
            'privateKey' => 'require',
            'security', 'success_order_num','__token__'
        ],
        'alipayWap_add' =>  [
            'bank_name'=>'require',
            'account_number' => 'require',
            'publicKey' => 'require',
            'privateKey' => 'require',
            'security', 'success_order_num','__token__'
        ],
        'alipayTransfer_add' =>  [
            'account_name'=>'require',
            'bank_name' => 'require',
            'security', 'success_order_num','__token__'
        ],
        'taoBaoDirectPay_add' =>  [
            'bank_name'=>'require',
            'image_url' => 'require',
            'security', 'success_order_num','__token__'
        ],
        'JunWeb_add' => [
            'account_name' => 'require',
            'security', 'success_order_num','__token__'
        ],
        'HuiYuanYiFuKa_add' => [
            'account_name' => 'require',
            'security', 'success_order_num','__token__'
        ],
        'alipayPinMoney_add' => [
            'account_name' => 'require',
            'account_number' => 'require',
            'security', 'success_order_num','__token__'
        ],
        'LeFuTianHongKa_add' => [
            'account_name' => 'require',
            'security', 'success_order_num','__token__'
        ],
        'DingDingGroup_add' => [
            'bank_name'=>'require',
            'account_name' => 'require',
            'image_url' => 'require',
            'security', 'success_order_num','__token__'
        ],
        'WechatGoldRed_add' => [
            'bank_name'=>'require',
            'account_name' => 'require',
            'security', 'success_order_num','__token__'
        ],
        'QqCode_add' => [
            'bank_name'=>'require',
            'account_name' => 'require',
            'account_number' => 'require',
            'security', 'success_order_num','__token__'
        ],
    ];
}


