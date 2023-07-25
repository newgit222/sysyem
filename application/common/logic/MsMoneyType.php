<?php


namespace app\common\logic;


class MsMoneyType extends BaseLogic
{

    //操作方式,添加
    const OP_ADD = 1;

    //操作方式,减少
    const OP_SUB = 0;

    //充值
    const DEPOSIT = 1;

    //提现
    const WITHDRAW = 2;

    //抢单成功,押金
    const ORDER_DEPOSIT = 3;



    //关闭订单,押金返回
    const ORDER_DEPOSIT_BACK = 4;

    //订单完成,用户分润
    const USER_BONUS = 5;



    //后台强制完成订单
    const ORDER_FORCE_FINISH = 6;

    //站内转账
    const TRANSFER = 7;

    //平台手动调整余额
    const ADJUST = 8;

    //订单完成,代理分润
    const AGENT_BONUS = 9;

    //管理员手动分润
    const MANUAL_BONUS = 10;

    //订单完成，扣除余额
    const ORDER_SUCCESS = 11;

    //代付订单完成
    const DAIFU_ORDER_SUCCESS = 12;

    //代付订单完成，码商分润
    const DAIFU_AGENT_BONUS = 13;

    //冻结余额转可用
    const DISABLE_TO_ENABLE = 14;

    //订单冲正
    const REVERSAL = 15;
    const ORDER_DEPOSITS = 16;

    //平台手动调整余额
    const DISABLE_ADJUST = 17;

    //订单完成,用户分润
    const YE_USER_BONUS = 18;

    const BALANCE_RECHARGE = 19;

    //代付余额转可用
    const DAIFU_MONEY_TO_ENABLE = 20;

    public static function getMoneyOrderTypes()
    {
        return [
            self::DEPOSIT => "充值添加",
            self::WITHDRAW => "提现扣除",
            self::ORDER_DEPOSIT => "订单完成",
            self::ORDER_DEPOSIT_BACK => "冻结返还",
            self::ORDER_FORCE_FINISH => "后台强制",
            self::TRANSFER => "站内转账",
            self::ADJUST => "平台调整",
            self::AGENT_BONUS => '代理分润',
            self::USER_BONUS => '用户分润',
            self::MANUAL_BONUS => '管理员手动分润',
            self::ORDER_SUCCESS => '订单完成',
            self::DAIFU_ORDER_SUCCESS => '代付订单完成',
            self::DAIFU_AGENT_BONUS => '代付完成分润',
            self::DISABLE_TO_ENABLE => '冻结余额转可用',
            self::REVERSAL => '订单冲正',
            self::ORDER_DEPOSITS => '抢单押金',
            self::DISABLE_ADJUST=> '冻结余额平台调整',
            self::YE_USER_BONUS=> '夜间额外提成',
            self::BALANCE_RECHARGE=> '码商申请充值完成',
            self::DAIFU_MONEY_TO_ENABLE=> '代付余额转可用',
        ];
    }








}
