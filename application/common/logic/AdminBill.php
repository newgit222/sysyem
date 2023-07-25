<?php


namespace app\common\logic;

use app\common\logic\BaseLogic;
use think\Db;
use think\Exception;
use think\Log;

class AdminBill extends BaseLogic
{

    /**
     * 1 usdt 充值到账 2管理员账变 3订单完成 4代付订单完成 5管理员开户赠送
     * @param $uid  uwer_id
     * @param int $type 资金操作类型对应数据库中jl_class
     * @param int $add_subtract 添加或者减少
     * @param float $money 操作金额
     * @param string $tip_message 资金流水备注
     * @return bool
     */
    function addBill($admin_id, $type = 1, $add_subtract = 1, $money = 0.00, $tip_message = '')
    {
        $AdminModel = new  \app\common\model\Admin();
        $admin = $AdminModel->where(['id' => $admin_id])->find();
        if ($admin) {
            $moneys = ($add_subtract == 1) ? $money : 0 - $money;
            $updateBalanceRes = $AdminModel->where(['id' => $admin_id])->setInc('balance', $moneys);
            if ($updateBalanceRes) {
                //记录流水
                $insert['admin_id'] = $admin_id;
                $insert['jl_class'] = $type;
                $insert['info'] = $tip_message;
                $insert['addtime'] = time();
                $insert['jc_class'] = ($add_subtract) ? "+" : "-";
                $insert['amount'] = $money;
                $insert['pre_amount'] = $admin['balance'];
                $insert['last_amount'] = $admin['balance'] + $moneys;

                if ((new \app\common\model\AdminBill())->insert($insert)) {
                    return true;
                }
                return false;
            } else {
                return false;
            }
        }
        return false;
    }
}
