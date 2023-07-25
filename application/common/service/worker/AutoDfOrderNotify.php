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

namespace app\common\service\worker;

use app\common\logic\Api;
use app\common\logic\DaifuOrders;
use app\common\model\Config;
use GuzzleHttp\Client;
use think\Exception;
use think\Log;
use think\queue\Job;

class AutoDfOrderNotify
{
    public function fire(Job $job,$data){
//        Log::notice('最开始====' .$job->attempts(). ' == '.json_encode($data));
        $orderModel = new \app\common\model\DaifuOrders();
        $order = $orderModel->where('id', $data['id'])->find();

        Log::notice('代付订单:'.$order['out_trade_no'].'开始队列回调了');
        if (trim($order['notify_result']) == 'SUCCESS'){
            $job->delete();
            return;
        }
        try {
            $result = $this->retryNotify($data['id'], $job->attempts(),($data['status'] ==2) ? true:false);
        }catch (Exception $e){
            Log::notice($e->getMessage());
            $result['code'] = 0;
        }

        \think\Log::notice('代付异步通知：' .  json_encode($result));
        if ($result['code'] == '1') {
            $job->delete();
            print("<info>The DaifuOrder Job ID " . $data['id'] ." has been done and deleted"."</info>\n");
        }else {
            if ($job->attempts() >= 3) {
                print("<warn>The DaifuOrder Job ID " . $data['id'] . " has been deleted and retried more than 5 times!" . "</warn>\n");
                $job->delete();
            } else {
                print("<info>The DaifuOrder Job ID " . $data['id'] . " will be availabe again after 10s</info>\n");
                $job->release(1);
            }
        }
    }


    /**
     * 补发回调
     */
    public function retryNotify($id, $attempts, $status=true)
    {
        $modelDaifuOrders = new \app\common\model\DaifuOrders();
        $order = $modelDaifuOrders->where(['id' => $id])->find();
        if (!$order) {
            Log::notice('代付订单异步通知：订单不存在');
            return ['code' => '0', 'msg' => '订单不存在'];
        }

        $notify_result = $this->sendNotify($order, $attempts, $status);
        $result = $order->update(['notify_result' => $notify_result], ['id' => $id]);
        if (!$result) {
            return ['code' => '0', 'msg' => '保存失败'];
        }
        if ($notify_result != 'SUCCESS') {
            return ['code' => '0', 'msg' => '已补发，回调失败'];
        }
        return ['code' => '1', 'msg' => '回调成功'];
    }

    /**
     * 执行回调
     */
    protected function sendNotify($order, $attempts, $status = true)
    {
        Log::notice('代付订单:'.$order['out_trade_no'].'开始队列并执行回调了，次数：'.$attempts);
        $data = [
            'code' => 1,
            'msg' => 'ok',
            'out_trade_no' => $order['out_trade_no'],
            'trade_no' => $order['trade_no'],
            'amount' => $order['amount'],
            'status' => $status ? '2' : '0',
            'error_reason' => $order['error_reason']
        ];
        $agent = $this->checkAgent($order['uid']);
        $res = '';
        if ($agent) {
            $data['sign'] = $this->getSign2($data, $agent->key);

            $client = new Client();
            if ($attempts == 1){
                $response = $client->request(
                    'POST', $order['notify_url'], ['form_params' => $data,'timeout'=>5]
                );
            }elseif ($attempts == 2){
                $response = $client->request(
                    'POST', "http://68.178.164.187/daifuzz.php?notify_url=".$order['notify_url'], ['form_params' => $data,'timeout'=>5]
                );
            }else if ($attempts == 3){
                $response = $client->request(
                    'POST', "http://45.207.58.203/zz.php?notify_url=".$order['notify_url'], ['form_params' => $data,'timeout'=>5]
                );
            }

            $res = $response->getBody()->getContents();
//            Log::notice('=========='. $res);
            \think\Log::notice("代付异步队列回调 notify url " . $order['notify_url'] . "data" . json_encode($data).'返回内容:'.$res);
            if ($res != 'SUCCESS') {
                $res = 'ERROR';
            }
            $modelDaifuOrders = new \app\common\model\DaifuOrders();
            $modelDaifuOrders->where(['id' => $order['id']])->setInc('send_notify_times');
        } else {
            \think\Log::error("代付没有下级回调 notify url " . $order['notify_url'] . "data" . json_encode($data));
        }
        return $res;
    }

    /**
     * 获取商户
     */
    public function checkAgent($mchid)
    {
        $Api = new Api();
        return $Api->getApiInfo(['uid' => $mchid]);
    }


    public function getSign2($param, $key)
    {
        if (isset($param['sign'])) {
            unset($param['sign']);
        }
        ksort($param);
        \think\Log::notice(urldecode(http_build_query($param) . '&' . 'key'));
        return md5(urldecode(http_build_query($param) . '&' . $key));
    }

}
