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

namespace app\api\controller;;

use app\api\service\DaifuPayment;
use app\api\service\ApiWithdraw;

use app\common\controller\BaseApi;
use app\common\library\enum\CodeEnum;
use app\common\library\exception\ForbiddenException;
use app\common\library\exception\OrderException;
use app\common\logic\BalanceCash;
use app\common\model\Orders;
use GuzzleHttp\Client;
use think\Exception;
use think\Log;

class Daifunotify extends BaseApi
{

    /**
     * 统一异步通知
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param string $channel
     *
     */
    public function notify($channel = 'wxpay'){

        //如果ｐｏｓｔ里面有ｃｈａｎｅｌ的话,以get为准
        if(!empty($_GET['channel']) && $channel!=$_GET['channel'])
        {
            $channel = $_GET['channel'];
        }
        $channel = explode("?",$channel)[0];
        $logicNotify = new \app\api\logic\Notify();
        $checkret  =  $logicNotify->checkTransferDaifuNotifyIp($channel);
        if($checkret['code'] == CodeEnum::ERROR)
        {
//            echo 'success.';die();
            throw new OrderException([
                'msg'   =>$checkret['msg'],
                'errCode'   => 200011
            ]);
        }

        if (!class_exists($channel)) {
            if(preg_match('/[0-9]/', $channel, $matches, PREG_OFFSET_CAPTURE)) {
                $unuese_str = substr($channel,$matches[0][1],7);
                $channel = str_replace($unuese_str,'',$channel);
            }

        }
        $result = DaifuPayment::$channel()->notify();

        Log::notice('处理结果:'.json_encode($result));

        $timeStart = time();
        $DaifuOrdersLogic = new \app\common\logic\DaifuOrders();
        if ($result['status'] == 2){
            $res = $DaifuOrdersLogic->DaifusuccessTransferOrder($result['out_trade_no'],true);
        }else{
            $res = $DaifuOrdersLogic->DaifuerrorOrderTransferOrder($result['out_trade_no'],false,$result['error_reason']);
        }

        $timeFinish = time();

        Log::notice($result['out_trade_no']." finish order take:".($timeFinish-$timeStart)." s".'，回调操作返回：'.json_encode($res,320));

    }


}
