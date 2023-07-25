<?php


namespace app\common\logic;

use app\common\logic\BaseLogic;
use think\Db;
use think\Exception;
use think\Log;

class AdminRechargeRecord extends BaseLogic
{

    /**
     * 获取唯一的充值金额
     */
    public function getOnlyRechargeAmount($amount)
    {
        $rate = 6.82;
        $usdt_num =  ceil(bcdiv($amount, $rate, 2));
        $model = $this->modelAdminRechargeRecord;
        $model->startTrans();
        try {
            $info = $this->modelAdminRechargeRecord->where('usdt_num', $usdt_num)->find();
            while ($info){
                $usdt_num++;
                $info = $this->modelAdminRechargeRecord->where('usdt_num', $usdt_num)->find();
            }
            $model->commit();
        }catch (Exception $e){
            $model->rollback();
            Log::error('获取usdt充值金额错误：' . $e->getMessage());
        }
        return $usdt_num;
    }

    public function completeRecharge($usdt_num, $from_usdt_address, $transaction_id)
    {
        $ret = false;
        Db::startTrans();
        try {
            $where = [
                'status' => 0,
                'usdt_num'=> $usdt_num,
            ];
            $Record = $this->modelAdminRechargeRecord->where($where)->find();
            if ($Record) {
                $billRet = $this->logicAdminBill->addBill($Record['admin_id'], 1, 1, $Record['amount'], 'USDT充值区块链查询自动到账');
                if ($billRet){
                        Log::notice('usdt充值到账成功，ID ' . $Record['id'] . ' ,金额' . $Record['amount']);
                }
                $Record-> status = 1;
                $Record-> from_usdt_address = $from_usdt_address;
                $Record-> transaction_id = $transaction_id;
                $Record->save();
                $ret = true;
            }
            Db::commit();
        }catch (Exception $e){
            Db::rollback();
            Log::notice('usdt充值到账成功ERROR ' . $e->getMessage());
        }
        return $ret;
    }
}
