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


namespace app\common\model;

use app\common\library\exception\OrderException;
use think\Db;
use think\Log;

class Admin extends BaseModel
{


    /**
     * 无限极分类
     * @param $data  数据
     * @return array|bool
     */
    public static function sortTree($data,$pid,$level)
    {
        return   self::executSort($data,$pid,$level);
    }
    /**
    对数据进行无极限分类
     **/
    public static function executSort($data = [], $pid = 0, $level = 1)
    {
        if (!$data){
            return false;
        }
        static $arr = [];
        foreach ($data as $k => $v) {
            if ($v['pid'] == $pid) {
                $v['level'] =$level;
                $arr[]= $v;
                unset($data[$k]);
                $data1 = Db::name('ms')->where('pid',$v['userid'])->select();
                self::executSort($data1, $v['userid'], $level+1);
            }
        }
        return $arr;
    }


//    public function xunhuan($data,$orderInfo){
//        if (!$data){
//            return false;
//        }
//        static $arr = [];
//
//        for ($i=1;$i<$data['level'];$i++){
//            $v['rate'] = Db::name('ms_rate')->where(['ms_id'=>$data['pid'],'channel_id'=>$orderInfo['cnl_id']])->value('rate');
//            $v['rate'] = 0 ? 1 : $v['rate'];
//            $v['money'] =  sprintf('%.2f',$orderInfo['order_pay_price'] * $v['rate']  / 100);
//            $arr[]= $v;
//            $p_ms = Db::name('ms')->where('userid',$v['pid'])->find();
//            self::xunhuan($p_ms, $orderInfo);
//        }
//
//        return $arr;
//    }


    public static function getDaifuPayAdminRateMoney($admin_id, $money){
        $rate_money = 0;
        if (\app\common\logic\Config::isSysEnableRecharge()){
            $admin_rate = self::where('id', $admin_id)->value('rate');
            $admin_rate = ($admin_rate && $admin_rate > 0) ? $admin_rate: 5/100;
            $rate_money = bcdiv(bcmul($money, $admin_rate, 3), 100, 2);
        }
        return $rate_money;
    }

    public static function getPayAdminRateMoney($admin_id, $money, $code_type_id){
        $rate_money = 0;
        if (\app\common\logic\Config::isSysEnableRecharge()){
            $admin_rate = Db::name('admin_rate')->where([
                'admin_id' => $admin_id,
                'code_type_id' => $code_type_id
            ])->value('rate');
            $admin_rate = ($admin_rate && $admin_rate > 0) ? $admin_rate: 1/10;
            $rate_money = bcdiv(bcmul($money, $admin_rate, 3), 100, 2);
        }
        return $rate_money;
    }
}
