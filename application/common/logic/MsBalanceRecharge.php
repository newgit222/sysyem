<?php

namespace app\common\logic;

use app\common\library\enum\CodeEnum;

class MsBalanceRecharge extends BaseLogic
{




    public function add($data)
    {
        $MsBalanceRecharge = new \app\common\model\MsBalanceRecharge();
        $ret = $MsBalanceRecharge->allowField(true)->save($data);
        return $ret ? ['code'=>1,'msg'=>'添加成功'] : ['code'=>0,'msg'=>'添加失败'];

    }

    public function balanceRechargeAgree($data)
    {
        $MsBalanceRecharge = new \app\common\model\MsBalanceRecharge();
        $info = $MsBalanceRecharge->where(['id'=>$data['id']])->find();
        $MsBalanceRecharge->startTrans();
        try {

            //添加码商余额
            $tip_message = '码商余额充值申请同意，充值金额：'.$info['amount'].'元';
            accountLog($info['ms_id'], 19,  1, $info['amount'], $tip_message, 'enable');

            $info->status = 2;
            $info->save();
            $MsBalanceRecharge->commit();

            return ['code'=>CodeEnum::SUCCESS,'msg'=>'操作成功'];

        }catch (\Exception $e){
            $MsBalanceRecharge->rollback();
            return ['code'=>CodeEnum::ERROR,'msg'=>$e->getMessage()];
        }
    }
}