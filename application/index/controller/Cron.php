<?php
/**
 * Created by PhpStorm.
 * User: zhangxiaohei
 * Date: 2020/2/7
 * Time: 22:19
 */

namespace app\index\controller;


use app\common\logic\AdminRechargeRecord;
use app\common\logic\DaifuOrders;
use app\common\logic\MsMoneyType;
use app\common\model\EwmOrder;
use app\common\model\Orders;
use think\Log;
use app\common\logic\MsCacheQueue;
use app\common\logic\MsWeightQueue;
use app\common\model\Config;
use app\common\model\EwmPayCode;
use app\common\model\OrdersNotify;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use think\Cache;
use think\Db;
use think\Exception;
use think\Request;

class Cron
{
//    public function topms(){
//        $start = strtotime('2023-06-22 00:00:00'); // 替换为您所需的起始日期和时间
//        $end = strtotime('2023-06-24 23:59:59'); // 替换为您所需的结束日期和时间
//        $a = Db::name('ms_somebill')->where('jl_class',14)->where('type','disable')->where('addtime', 'between time', [$start, $end])
//            ->field('sum(num) as total_num,uid')->group('uid')->select();
//        foreach ($a as $k=>$v){
//            $a[$k]['username'] =  Db::name('ms')->where('userid',$v['uid'])->value('username');
//            $fpid = Db::name('ms')->where('userid',$v['uid'])->value('pid');
//            $a[$k]['father'] = Db::name('ms')->where('userid',$fpid)->value('username');
//            $topid = getNavPid($v['uid']);
//            $a[$k]['top_ms'] =Db::name('ms')->where('userid',$topid)->value('username');
//        }
//        foreach ($a as $row) {
//            echo "码商id: " . $row["uid"] . "<br>";
//            echo "码商名称: " . $row["username"] . "<br>";
//            echo "上级码商: " . $row["father"] . "<br>";
//            echo "顶级码商: " . $row["top_ms"] . "<br>";
//            echo "操作金额: " . $row["total_num"] . "<br>";
//            echo "<hr>";
//        }
//    }
//    public function test1(){
////        $pageSize = 500; // 每次查询的记录数量
////        $page = 1; // 当前页数
//
//        $filteredOrders = [];
//        $start = strtotime('2023-06-26 01:00:00'); // 替换为您所需的起始日期和时间
//        $end = strtotime('2023-06-26 02:00:00'); // 替换为您所需的结束日期和时间
//        $orders = Db::name('orders')
//            ->where('status', 2)
//            ->where('update_time', 'between time', [$start, $end])
//            ->field('trade_no, platform_in')
////            ->limit(($page - 1) * $pageSize, $pageSize)
//            ->select();
//
//        if (!empty($orders)) {
//            $filteredOrders = [];
//            foreach ($orders as $tradeNosChunk) {
//                $mssb = Db::name('ms_somebill')
//                    ->where('addtime', 'between time', [$start, $end])
//                    ->where('jl_class', 'in', [5, 9])
//                    ->where('info', 'like', '%' . $tradeNosChunk['trade_no'] . '%')
//                    ->sum('num');
//                $mssb = sprintf("%.2f",$mssb);
//
//                $tradeNosChunk['num'] = $mssb;
//
////                if ($mssb > 0) {
////                    continue; // $mssb 大于0，继续下一次循环
////                }
//
//                if (($tradeNosChunk['platform_in'] - $mssb) < 0) {
//                    $filteredOrders[] = $tradeNosChunk;
//                }
//            }
//        }
//
//
//        print_r($filteredOrders);
//        die;
//    }
//
//
//    public function test2(){
////        $pageSize = 500; // 每次查询的记录数量
////        $page = 1; // 当前页数
//
//        $filteredOrders = [];
//        $start = strtotime('2023-06-26 02:00:00'); // 替换为您所需的起始日期和时间
//        $end = strtotime('2023-06-26 03:00:00'); // 替换为您所需的结束日期和时间
//        $orders = Db::name('orders')
//            ->where('status', 2)
//            ->where('update_time', 'between time', [$start, $end])
//            ->field('trade_no, platform_in')
////            ->limit(($page - 1) * $pageSize, $pageSize)
//            ->select();
//
//        if (!empty($orders)) {
//            $filteredOrders = [];
//            foreach ($orders as $tradeNosChunk) {
//                $mssb = Db::name('ms_somebill')
//                    ->where('addtime', 'between time', [$start, $end])
//                    ->where('jl_class', 'in', [5, 9])
//                    ->where('info', 'like', '%' . $tradeNosChunk['trade_no'] . '%')
//                    ->sum('num');
//                $mssb = sprintf("%.2f",$mssb);
//
//                $tradeNosChunk['num'] = $mssb;
//
////                if ($mssb > 0) {
////                    continue; // $mssb 大于0，继续下一次循环
////                }
//
//                if (($tradeNosChunk['platform_in'] - $mssb) > 100) {
//                    $filteredOrders[] = $tradeNosChunk;
//                }
//            }
//        }
//
//
//        print_r($filteredOrders);
//        die;
//    }
//    public function test_queue(){
//
//        // $logic = new MsWeightQueue('301_newweight_last_userids');
//
//
//        // $ret = $logic->filterQueue(['100']);
//
//        // halt($ret);
//
//    //    halt(Cache::get('301_newweight_last_userids'));
//
//    //    $a = Cache::get('test1', []);
//
//    //    Cache::set('test1', 3);
//    //    print_r($a);die;
////
//       $logic = new MsCacheQueue('test1');
//////
//        $logic->init();
//
//        echo 'pop([1,2,3,4])当前返回码商：'  . $logic->get_data([1,2,3,4]) . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//        echo 'pop([2,2,3,4])当前返回码商：'  . $logic->get_data([2,2,3,4]) . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//        echo 'pop([3,4,5])当前返回码商：'  . $logic->get_data([3,4,5])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//        echo 'pop([2,3,4,4,4,3,3,4,2])当前返回收款码：'  . $logic->get_data([2,3,4,4,4,3,3,4,2]) . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//        echo 'pop([2,2,3,3,3,2,1,2,3,4])当前返回收款码：'  . $logic->get_data([2,2,3,3,3,2,1,2,3,4]) . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//        echo 'pop([2,3,3,3])当前返回收款码：'  . $logic->get_data([2,3,3,3])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([1])当前返回收款码：'  . $logic->get_data([1])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([2,3,3,3,3,3,4])当前返回收款码：'  . $logic->get_data([2,3,3,3,3,3,4])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//
//        echo 'pop([6,2,3,2,3,4,3,1])当前返回收款码：'  . $logic->get_data([6,2,3,2,3,4,3,1])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([2,1,1,2,3,3])当前返回收款码：'  . $logic->get_data([2,1,1,2,3,3])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([1,1,1,1,1])当前返回收款码：'  . $logic->get_data([1,1,1,1,1])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([1,2,3])当前返回收款码：'  . $logic->get_data([1,1,1,1,1])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([1,2,3,4])当前返回收款码：'  . $logic->get_data([1,2,3,4])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([1,2,3,4])当前返回收款码：'  . $logic->get_data([1,2,3,4])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([1,2,3,4])当前返回收款码：'  . $logic->get_data([1,2,3,4])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([1,2,3,4])当前返回收款码：'  . $logic->get_data([1,2,3,4])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([2,3,4,5])当前返回收款码：'  . $logic->get_data([1,2,3,4])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([2,3,4,5])当前返回收款码：'  . $logic->get_data([1,2,3,4])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([5,5,6,2,3])当前返回收款码：'  . $logic->get_data([5,5,6,2,3])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([2,3,2,3,2])当前返回收款码：'  . $logic->get_data([2,3,2,3,2])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([2,3,4,5,5])当前返回收款码：'  . $logic->get_data([2,3,4,5,5])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([2,3,4,5,5])当前返回收款码：'  . $logic->get_data([2,3,4,5,5])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([2,3,4,5,5])当前返回收款码：'  . $logic->get_data([2,3,4,5,5])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([2,3,4,1])当前返回收款码：'  . $logic->get_data([2,3,4,1])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([1,2,3,4,5,6])当前返回收款码：'  . $logic->get_data([1,2,3,4,5,6])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([1,2,3,4,5,6])当前返回收款码：'  . $logic->get_data([1,2,3,4,5,6])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([1,2,3,4,5,6])当前返回收款码：'  . $logic->get_data([1,2,3,4,5,6])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([1,2,3,4,5,6])当前返回收款码：'  . $logic->get_data([1,2,3,4,5,6])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([1,2,3,4,5,6])当前返回收款码：'  . $logic->get_data([1,2,3,4,5,6])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([1,2,3,4,5,6])当前返回收款码：'  . $logic->get_data([1,2,3,4,5,6])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//
//        echo 'pop([1,2,3,3,4,5,6])当前返回收款码：'  . $logic->get_data([1,2,3,3,4,5,6])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([1,2,3,3,4,5,6])当前返回收款码：'  . $logic->get_data([1,2,3,3,4,5,6])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([1,2,3,3,4,5,6])当前返回收款码：'  . $logic->get_data([1,2,3,3,4,5,6])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([1,2,3,3,4,5,6])当前返回收款码：'  . $logic->get_data([1,2,3,3,4,5,6])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([1,2,3,3,4,5,6])当前返回收款码：'  . $logic->get_data([1,2,3,3,4,5,6])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([1,2,3,3,4,5,6])当前返回收款码：'  . $logic->get_data([1,2,3,3,4,5,6])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([1,2,3,3,4,5,6])当前返回收款码：'  . $logic->get_data([1,2,3,3,4,5,6])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([1,2,3,3,4,5,6])当前返回收款码：'  . $logic->get_data([1,2,3,3,4,5,6])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//        echo 'pop([3,3])当前返回收款码：'  . $logic->get_data([3,3])  . '<br>';
//        echo '当前队列：' . json_encode($logic->getQueue()) . '<br>';
//        echo '<hr>';
//
//    }

    public function a()
    {
//      $where['trade_no']='TX162211090133613479';
//      $orderLists = Db::name('daifu_orders')->where($where)->select();
//      $daifuNotify = new DaifuOrders();
//     $d= $daifuNotify->retryNotify($orderLists[0]['id'],0);
//     var_dump($d);die();


        if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1'){
//            echo 'error'; die();
        }

        $order_wh = [
            'status' => 2,
            'send_notify_times' => ['lt', 5],
            'finish_time' => ['gt', time()-60*20],
        ];

        $cron_map = [1,2,5,10,15];

        $orders_notify = Db::name('daifu_orders')
            ->where($order_wh)
            ->order('create_time asc')
            ->order('finish_time asc')
            ->field('id,send_notify_times,finish_time,notify_result')
            ->select();

        //取一条出来执行
        $take_out_order = '';
        foreach ($orders_notify as $order){
            if ( strtolower($order['notify_result']) == 'success'){
                continue;
            }

            if ($order['send_notify_times'] == 0 or ( time()>= ($order['finish_time'] + $cron_map[$order['send_notify_times']] * 60) )){
                $take_out_order = $order;
                break;
            }
        }

        try {
            if (!empty($take_out_order)){
                $daifuNotify = new DaifuOrders();
               $daifuNotify->retryNotify($take_out_order['id'],0);
            }
        }catch (\Exception $e){
            \think\Log::notice('代付回调error：' . $e->getMessage());
            echo 'error'; die();
        }

        echo 'success';


    }
    private function shouldDaiFuProcessOrder($order,$times)
    {
        $delayTimes = [0, 60, 120, 180, 300, 420];
        $currentTime = time();
        $paymentTime = $order['create_time']; // update_time字段是支付时间
        if (isset($delayTimes[$times])) {
            $delay = $delayTimes[$times];
            if ($currentTime - $paymentTime >= $delay){
                return  true;
            }
            return false;
        }

        return false;
    }

    public function notify_old()
    {
        $cron_map = [1,2,5,10,15];
        //状态
	    $where['o.status'] = ['in',[0,2]];
	   // notify_result
//		  $where['notify_result'] = ['neq','SUCCESS'];
          $start = time()-(120*60);
          $end = time();
          $where['o.create_time'] =['between time',[$start,$end]];
          $where['a.is_notify_status'] = 1;
          $where['o.send_notify_times'] = ['<',5];
        $daifuNotify = new DaifuOrders();


        $orderLists = Db::name('daifu_orders')
                                        ->alias('o')
                                        ->join('api a','o.uid = a.uid')
                                        ->where($where)

                                        ->where(function ($query){
                                            $query->where('o.notify_result',['neq','SUCCESS'],NULL,'or');
                                        })
                                        ->field('o.*')
                                        ->order('o.id desc')->limit(50)->select();
//        print_r($orderLists);die;

            if ($orderLists){
                foreach ($orderLists as $k=>$v){
                    if ($v['send_notify_times'] == 0 or ( time()>= ($v['create_time'] + $cron_map[$v['send_notify_times']] * 60) )) {
                        if($v['status']==2)
                        {
                            $status=true;
                        }
                        else
                        {
                            $status=false;
                        }
                        $res = $daifuNotify->retryNotify($v['id'],$status);
//                        echo json_encode($res,320);
                        echo "订单号：".$v['out_trade_no']."</br>";
                        continue;
                    }
                        continue;
                }
            }

        echo 3;die();
    }
    public function notify()
    {
//        $cron_map = [1,2,5,10,15];
        //状态
        $where['o.status'] = ['in',[0,2]];
        // notify_result
//		  $where['notify_result'] = ['neq','SUCCESS'];
        $start = time()-(120*60);
        $end = time();
        $where['o.create_time'] =['between time',[$start,$end]];
        $where['a.is_notify_status'] = 1;
        $where['o.send_notify_times'] = ['<',5];
        $where['o.chongzhen'] = 0;
        $daifuNotify = new DaifuOrders();


        $orderLists = Db::name('daifu_orders')
            ->alias('o')
            ->join('api a','o.uid = a.uid')
            ->where($where)

            ->where(function ($query){
                $query->where('o.notify_result',['neq','SUCCESS'],NULL,'or');
            })
            ->field('o.*')
            ->order('o.id desc')->limit(50)->select();
//        print_r($orderLists);die;

        if ($orderLists){
            foreach ($orderLists as $k=>$v){
                if ($this->shouldDaiFuProcessOrder($v,$v['send_notify_times']) === true){
                    $status = ($v['status'] == 2) ? true : false;
                    $res = $daifuNotify->retryNotify($v['id'],$status);
                    echo "订单号：".$v['out_trade_no']."</br>";
                    continue;
                }
                continue;
            }
        }

        echo 3;die();
    }

    /**
     * usdt充值自动完成
     */
    public function usdtRechargeTest()
    {
        $usdt_address = 'TMAdjy4u5v3qqJrBNyD7zrqyLNRNAvrd3k';
        $http_url = 'https://apilist.tronscanapi.com/api/new/token_trc20/transfers?limit=30&start=0&sort=-timestamp&count=true&filterTokenValue=1&relatedAddress=';
        $http_url .= $usdt_address;

        $client = new Client();
        $result = $client->get($http_url);
        if ($result->getStatusCode() == 200){
            $data = json_decode($result->getBody()->getContents(),true);

            foreach (isset($data['token_transfers']) ? $data['token_transfers'] : [] as $transfers ){
//                            print_r($data);die;
                if ($transfers['to_address'] == $usdt_address){
                    $usdt_num = bcdiv($transfers['quant'], str_pad(1,$transfers['tokenInfo']['tokenDecimal']+1,0,STR_PAD_RIGHT) , 2);
                    $AdminRechargeRecord = new AdminRechargeRecord();
                    $AdminRechargeRecord->completeRecharge($usdt_num, $transfers['from_address'], $transfers['transaction_id']);
                }
            }

        }

    }

    /**
     * usdt 充值订单过期
     */
    public function usdtOrderExpired()
    {
        $modelAdminRechargeRecord = new \app\common\model\AdminRechargeRecord();
        $modelAdminRechargeRecord->where(array(
            'status' => 0,
            'create_time' => ['lt' , time()-10*60]
        ))->update(array(
            'status' => -1
        ));
    }

    /**
     * 二维码账号每天十二点定时清理今日订单统计数据
     */
    public function clearEwmPayCodeTodayData()
    {

//        if (strtotime(date('Y-m-d 00:00:00',time())) != time()){
//            echo "Time Out";
//            die;
//        }
        set_time_limit(0);
        $EwmPayCode = new EwmPayCode();

// Use chunk method to handle large data sets to avoid memory issues
        $EwmPayCode->where('is_delete',0)->chunk(200, function($ewmPayCodes) use ($EwmPayCode) {
            $updateCodeArr = [];

            // Prepare the data to update
            foreach ($ewmPayCodes as $code){
                $updateCodeArr[] = [
                    'id' => $code['id'],
                    'yesterday_receiving_number' => $code['today_receiving_number'],
                    'yesterday_receiving_amount' => $code['today_receiving_amount']
                ];
            }

            // Perform the update in chunks
            foreach (array_chunk($updateCodeArr, 20) as $chunkArr){
                $EwmPayCode->isUpdate(true)->saveAll($chunkArr);
            }
        });

// Reset today's data
        $EwmPayCode->where([
           'today_receiving_number' =>['neq', 0],
            'today_receiving_amount' =>[ 'neq', 0],
            'is_delete' =>[ '=', 0],
        ])->update([
            'today_receiving_number' => 0,
            'today_receiving_amount' => 0
        ]);


        echo 'SUCCESS';
    }

//    public function clearC(){
//        Cache::clear();
//    }


    private function curl($url,$postdata=[]) {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);
        if ($this->req_method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
        }
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch, CURLOPT_TIMEOUT,6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt ($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
        ]);
        $output = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        return $output;
    }
//    public function orderCallback(Request $request)
//    {
//        $num = trim($request->param('number'));
//        if (empty($num)){
//            $num = 50;
//        }
//	    for ($i = 1; $i <=$num; $i++) {
//            $this->processOrders();
//        }
//    }



    public function orderCallback()
    {
        $orders = $this->getOrdersToProcess();

        foreach ($orders as $order) {
            $take_out_order = $order;
            // try {
//            $orderNotify = (new OrdersNotify())->where(['order_id' => $take_out_order['order_id']])->find();
            $order = Db::name('orders')->where('id', $take_out_order['order_id'])->find();
            if ($this->shouldProcessOrder($order,$take_out_order['times']) === true) {
                (new OrdersNotify())->where(['order_id' => $take_out_order['order_id']])->update([
                    'times'   => $take_out_order['times'] + 1,
                    'update_time'=>time(),
                ]);
                $result = $this->doOrderCallback($order, $take_out_order['times']);
                $result_content = $result['result_content'] ?? '';
                unset($result['result_content']);
                // 更新订单的回调次数和其他相关信息
                if ($result && strtoupper(trim($result['result'])) == 'SUCCESS') {
                    //成功记录数据
                    $result['result'] = strtoupper(trim($result['result']));
                    $result['content'] =  $take_out_order['content'] . (empty($take_out_order['content']) ? '' : ',,,,,') .   $result_content;
                    
                    (new OrdersNotify())->where(['order_id' => $take_out_order['order_id']])->update($result);

                    \think\Log::notice('订单回调成功，订单号：' . $order['id']);
                }
                if ($result  && $result['result'] != 'SUCCESS'){
                    //失败记录数据
                    (new OrdersNotify())->where(['order_id' => $take_out_order['order_id']])->update([
//                        'times'   => $take_out_order['times'] + 1,
//                        'update_time'=>time(),
                        'result' => $result['result'],
                        'content' => $take_out_order['content'] . (empty($take_out_order['content']) ? '' : ',,,,,') .   $result_content
                    ]);
                }
            }
        }
    }


    // 获取需要处理的订单
    private function getOrdersToProcess()
    {
        $order_wh = [
            'is_status' => ['eq', 404],
            'create_time' => ['gt', time()-60*20],
            'times' => ['lt', 5],
            'result' => ['neq', 'SUCCESS']
        ];

        //对比成功订单跟异步订单差异
        $diffCache = Cache::get('order_notify_diff');
        if (empty($diffCache) || $diffCache < time()-2*60){
            $ordersWhere = [
                'status' => 2,
                'create_time' => ['gt', time()-20*60]
            ];
            $orders = Db::name('orders')->where($ordersWhere)->column('id');
            if ($orders){
                $orders_notify_0 = Db::name('orders_notify')->where('order_id', 'in', $orders)->column('order_id');
                $diff_temp_orders = array_diff($orders, $orders_notify_0);
                $add_order_notify = [];
                foreach ($diff_temp_orders as $val){
                    $add_order_notify[] = ['order_id' => $val];
                    \think\Log::error('订单成功，异步未写入的订单号：' . $val);
                }

                $modelOrderNotify = new OrdersNotify();
                count($add_order_notify) && $modelOrderNotify->saveAll($add_order_notify);
            }
            Cache::set('order_notify_diff', time());
        }

        $orders_notify = Db::name('orders_notify')
            ->where($order_wh)
            ->order('update_time asc')
//            ->limit(1)
            ->field('order_id,times,create_time,content')
            ->select();

        return $orders_notify;
    }

    private function shouldProcessOrder($order,$times)
    {
        $delayTimes = [0, 60, 120, 180, 300, 420];
        $currentTime = time();
        $paymentTime = $order['update_time']; // update_time字段是支付时间
        if (isset($delayTimes[$times])) {
            $delay = $delayTimes[$times];
             if ($currentTime - $paymentTime >= $delay){
                 return  true;
             }
            return false;
        }

        return false;
    }




    private function doOrderCallback($data, $times)
    {
            //要签名的数据
            $where = array();
            $where['uid'] = $data['uid'];
            $LogicApi = new \app\common\logic\Api();
            $admin_id =Db::name('user')->where($where)->value('admin_id');
            $appKey = $LogicApi->getApiInfo($where, "key",($data['uid']==100063||$data['uid']==100068|| $data['uid']==100067));
            $to_sign_data =  $this->buildSignData($data, $appKey["key"],($data['uid']==100063||$data['uid']==100068|| $data['uid']==100067));
            //签名串
            $one_notify_address = getAdminPayCodeSys('one_notify_transfer',256,1);
            $two_notify_address = getAdminPayCodeSys('two_notify_transfer',256,1);
            $three_notify_address = getAdminPayCodeSys('three_notify_transfer',256,1);
            $four_notify_address = getAdminPayCodeSys('four_notify_transfer',256,1);
            $five_notify_address = getAdminPayCodeSys('five_notify_transfer',256,1);

            \think\Log::notice("\r\n");
            \think\Log::notice("posturl: ".$data['notify_url']);
            \think\Log::notice("sign data: ".json_encode($to_sign_data));
            try{
                $client = new Client();
                $Config = new Config();
                $proxy_debug = $Config->where(['name'=>'transfer_ip_list'])->value('value');
                $orginal_host = $Config->where(['name'=>'orginal_host'])->value('value');
                $time=5;
                if($data['uid']==100110||$data['uid']==100099){
                    $time = 9;
                }
                if ($times == 0){
                    if (empty($one_notify_address)){
                        \think\Log::notice('本服务器回调'.$times);
                        $response = $client->request(
                            'POST', $data['notify_url'], ['form_params' => $to_sign_data,'timeout'=>5]
                        );
                    }else{
                        $url = $one_notify_address.'?notify_url='.urlencode($data['notify_url']);
                        $zhongzhuan_address = $one_notify_address;
                        $response = $client->request(
                            'POST', $url, ['form_params' => $to_sign_data,'timeout'=>5]
                        );
//                       Log::error(date('Y-m-d H:i:s',time()).'Cron订单回调：'. $data['out_trade_no'] . '在Cron第2次回调，回调ip：'.$one_zhongzhuan_url );
                    }
                }elseif ($times == 1){
                    if (empty($two_notify_address)){
                        \think\Log::notice('本服务器回调'.$times);
                        $response = $client->request(
                            'POST', $data['notify_url'], ['form_params' => $to_sign_data,'timeout'=>5]
                        );
                      // Log::error(date('Y-m-d H:i:s',time()).'Cron订单回调：'. $data['out_trade_no'] . '在Cron第2次回调，回调ip：本机' );
                    }else{
                        $url = $two_notify_address.'?notify_url='.urlencode($data['notify_url']);
                        $zhongzhuan_address = $two_notify_address;
                        $response = $client->request(
                            'POST', $url, ['form_params' => $to_sign_data,'timeout'=>5]
                        );
                //  Log::error(date('Y-m-d H:i:s',time()).'Cron订单回调：'. $data['out_trade_no'] . '在Cron第2次回调，回调ip：' . $two_zhongzhuan_url);
                    }

                }elseif ($times == 2){
                    if (empty($three_notify_address)){
                        \think\Log::notice('本服务器回调'.$times);
                        $response = $client->request(
                            'POST', $data['notify_url'], ['form_params' => $to_sign_data,'timeout'=>5]
                        );
               //   Log::error(date('Y-m-d H:i:s',time()).'Cron订单回调：'. $data['out_trade_no'] . '在Cron第3次回调，回调ip：本机');
                    }else{
                        \think\Log::notice($three_notify_address.'服务器回调'.$times);
                        $url = $three_notify_address.'?notify_url='.urlencode($data['notify_url']);
                        $zhongzhuan_address = $three_notify_address;
                        $response = $client->request(
                            'POST', $url, ['form_params' => $to_sign_data,'timeout'=>5]
                        );
                    //    Log::error(date('Y-m-d H:i:s',time()).'Cron订单回调：'. $data['out_trade_no'] . '在Cron第3次回调，回调ip：' . $three_notify_address);
                    }
                }elseif ($times == 3){
                    if (empty($four_notify_address)){
                        \think\Log::notice('本服务器回调'.$times);
                        $response = $client->request(
                            'POST', $data['notify_url'], ['form_params' => $to_sign_data,'timeout'=>5]
                        );
                        //   Log::error(date('Y-m-d H:i:s',time()).'Cron订单回调：'. $data['out_trade_no'] . '在Cron第3次回调，回调ip：本机');
                    }else{
                        \think\Log::notice($four_notify_address.'服务器回调'.$times);
                        $url = $four_notify_address.'?notify_url='.urlencode($data['notify_url']);
                        $zhongzhuan_address = $four_notify_address;
                        $response = $client->request(
                            'POST', $url, ['form_params' => $to_sign_data,'timeout'=>5]
                        );
                        //    Log::error(date('Y-m-d H:i:s',time()).'Cron订单回调：'. $data['out_trade_no'] . '在Cron第3次回调，回调ip：' . $three_notify_address);
                    }
                }elseif ($times == 4){
                    if (empty($five_notify_address)){
                        \think\Log::notice('本服务器回调'.$times);
                        $response = $client->request(
                            'POST', $data['notify_url'], ['form_params' => $to_sign_data,'timeout'=>5]
                        );
                        //   Log::error(date('Y-m-d H:i:s',time()).'Cron订单回调：'. $data['out_trade_no'] . '在Cron第3次回调，回调ip：本机');
                    }else{
                        \think\Log::notice($five_notify_address.'服务器回调'.$times);
                        $url = $five_notify_address.'?notify_url='.urlencode($data['notify_url']);
                        $zhongzhuan_address = $five_notify_address;
                        $response = $client->request(
                            'POST', $url, ['form_params' => $to_sign_data,'timeout'=>5]
                        );
                        //    Log::error(date('Y-m-d H:i:s',time()).'Cron订单回调：'. $data['out_trade_no'] . '在Cron第3次回调，回调ip：' . $three_notify_address);
                    }
                }else{
                    \think\Log::notice('本服务器回调'.$times);
                    $response = $client->request(
                        'POST', $data['notify_url'], ['form_params' => $to_sign_data,'timeout'=>5]
                    );
                }



//                if($proxy_debug  && $times >=2 && $orginal_host )
////                if(config('proxy_debug') && $attempts >=2 )
//                {
//                    \think\Log::notice('中转服务器回调'.$times);
//                    \think\Log::notice('中转服务器回调'.$orginal_host);
//                    //是否需要代理服务器处理让代理请求
////                    $hosts = config('orginal_host');
//                    $hosts = $orginal_host;
//                    $url = $hosts.'?notify_url='.urlencode($data['notify_url']);
//                    $response = $client->request(
//                        'POST', $url, ['form_params' => $to_sign_data,'timeout'=>5]
//                    );
//
//                }else{
//                    \think\Log::notice('本服务器回调'.$times);
//                    $response = $client->request(
//                        'POST', $data['notify_url'], ['form_params' => $to_sign_data,'timeout'=>5]
//                    );

//                }


                $statusCode = $response->getStatusCode();
                $contents = $response->getBody()->getContents();
               Log::error('['. date('Y-m-d H:i:s',time()).']订单'.  $data['out_trade_no'] . '从'. ($zhongzhuan_address ?? '本机') .',第' . ($times+1) . '次回调，返回内容：' . $contents);
                \think\Log::notice("订单回调 notify url " . $data['notify_url'] . "data" . json_encode($to_sign_data).'返回内容:'.$contents);
                \think\Log::notice("response code: ".$statusCode." response contents: ".$contents);

                $notifyContent = (new \app\common\logic\OrdersNotify())->where(['order_id' => $data['id']])->value('content');
                (new \app\common\logic\OrdersNotify())->where(['order_id' => $data['id']])->update([
                    'content' =>$notifyContent . (empty($notifyContent) ? '' : ',,,,,') .  $contents
                ]);

                print("<info>response code: ".$statusCode." response contents: ".$contents."</info>\n");
                // JSON转换对象
                if ( $statusCode == 200 && !is_null($contents)){
                    //判断放回是否正确
//                    if ($contents == "SUCCESS"){
                    //TODO 更新写入数据

                    if (isset($url)){
                        $parsed_url = parse_url($url);  // 解析 URL
                        $zhIp = $parsed_url['host'] ?? '1.2.3.4';  
                    }else{
                        $zhIp = '127.0.0.1';
                    }

                    

                    return [
                        'result'   => $contents,
                        'result_content'   => '中转ip ' . $zhIp . '返回：' . $contents,
                        'is_status'   => $statusCode,
//                        'update_time' => time()
                    ];
//                    }
//                    return false;
                }
                return false;
            }catch (RequestException $e){
                \think\Log::error('Notify Error:['.$e->getMessage().']');
                return false;
            }

        return false;
    }

    private function buildSignData($data,$md5Key,$need_remark=false){

        //除去不需要字段
        unset($data['id']);
        unset($data['uid']);
        unset($data['cnl_id']);
        unset($data['puid']);
        unset($data['status']);
        unset($data['create_time']);
        unset($data['update_time']);
        unset($data['update_time']);
        unset($data['income']);
        unset($data['user_in']);
        unset($data['agent_in']);
        unset($data['platform_in']);
        unset($data['currency']);
        unset($data['client_ip']);
        unset($data['return_url']);
        unset($data['notify_url']);
        unset($data['extra']);
        unset($data['subject']);
        unset($data['bd_remarks']);
        unset($data['remark']);
        unset($data['visite_show_time']);
        unset($data['real_need_amount']);
        unset($data['image_url']);
        unset($data['request_log']);
        unset($data['visite_time']);
        unset($data['request_elapsed_time']);

        $data['amount'] = sprintf("%.2f", $data['amount']);

        $data['order_status'] = 1;

        ksort($data);

        $signData = "";
        foreach ($data as $key=>$value)
        {
            $signData = $signData.$key."=".$value;
            $signData = $signData . "&";
        }
        $str = $signData."key=".$md5Key;

        $sgin = md5($str);
        $data['sign'] = $sgin;
        //返回
        return $data;
    }


    //关闭过期订单
    protected function closeOrders_old(Request $request){

//        print_r($request->server());die;
        $EwmOrders = new EwmOrder();
        $Orders = new Orders();
        $start = time()-1800;
        $end = time();
        $expired_ewm_orders = $EwmOrders->where([
            'status' => 0,
        ])->where('add_time','between',[$start,$end])->select();

        if (empty($expired_ewm_orders)){
            echo '暂无可处理订单'; die;
        }

        foreach ($expired_ewm_orders as $k=>$v){
            $EwmOrders->startTrans();
            $Orders->startTrans();
            $admin_sys_time = getAdminPayCodeSys('order_invalid_time',$v['code_type'],$v['admin_id']);
            if (empty($admin_sys_time)){
                $admin_sys_time = 600;
            }else{
                $admin_sys_time = $admin_sys_time * 60;
            }

            if ($v['add_time'] + $admin_sys_time < time()){
                $admin_sys_disable_ms_money = getAdminPayCodeSys('disable_ms_money_status',256,$v['admin_id']);
                if (empty($admin_sys_disable_ms_money)){
                    $admin_sys_disable_ms_money = 2;
                }
                if ($admin_sys_disable_ms_money == 1) {
                    if (accountLog($v['gema_userid'], MsMoneyType::ORDER_DEPOSIT_BACK, MsMoneyType::OP_ADD, $v['order_price'], $v['out_trade_no']) == false) {
                        Log::error('订单 ：' . $v['out_trade_no'] . '过期释放码商 ：' . $v['gema_username'] . '金额失败');
                        $EwmOrders->rollback();
                        $Orders->rollback();
                        continue;
                    }
                }
                $EwmOrders->where('id', $v['id'])->update(['status' => 3]);
                $EwmOrders->commit();
                $Orders->where('trade_no', $v['order_no'])->update(['status' => 0]);
                $Orders->commit();
            }


        }

        echo 'SUCCESS';die;
    }

    public function closeOrders(Request $request)
    {
        $time = $request->param('time');
        $current_time = time();
        if ($time){
            if ($time == 'today'){
                    $start = strtotime(date('Y-m-d 00:00:00',time())); // 替换为您所需的起始日期和时间
                    $end = strtotime(date('Y-m-d 23:59:59',time()));// 替换为您所需的结束日期和时间
              $where['add_time'] = ['between time', [$start, $end]];
            }else{
                $where['add_time'] = ['>', $current_time - 20 * 60];
            }
        }else{
            $where['add_time'] = ['>', $current_time - 20 * 60];
        }

        $EwmOrders = new EwmOrder();
        // 获取当前时间戳
        // 查询满足条件的订单，不加锁
        $expired_ewm_orders = $EwmOrders->where('status', '<>', 1)
            ->where('status', '<>', 3)
            ->where($where)
//            ->whereTime('add_time','today')
//            ->where('add_time', '>', $current_time - 20 * 60)
            ->select();

        if (empty($expired_ewm_orders)) {
            echo '暂无可处理订单';
            die;
        }
        // 循环遍历订单
        foreach ($expired_ewm_orders as $v) {
            // 计算订单已存在的时间
            $admin_sys_time = getAdminPayCodeSys('order_invalid_time', $v['code_type'], $v['admin_id']);
            if (empty($admin_sys_time)) {
                $admin_sys_time = 600;
            } else {
                $admin_sys_time = $admin_sys_time * 60;
            }
            if ($v['add_time'] + $admin_sys_time < time()) {
                try {
                    // 开始一个新的事务
                    $EwmOrders->startTrans();
                    // 通过主键锁定单个订单行并更新订单
                    $locked_order = $EwmOrders->where('id', $v['id'])->lock(true)->find();
                    if ($locked_order->status != 1 && $locked_order->status != 3) {
                        $admin_sys_disable_ms_money = getAdminPayCodeSys('disable_ms_money_status', 256, $v['admin_id']);
                        if (empty($admin_sys_disable_ms_money)) {
                            $admin_sys_disable_ms_money = 2;
                        }
                        if ($admin_sys_disable_ms_money == 1) {
                            if (accountLog($v['gema_userid'], MsMoneyType::ORDER_DEPOSIT_BACK, MsMoneyType::OP_ADD, $v['order_price'], $v['out_trade_no']) === false) {
                                throw new \Exception('过期释放码商金额失败，订单号： '. $v['out_trade_no']);
                            }
                            if (accountLog($v['gema_userid'], MsMoneyType::ORDER_DEPOSIT_BACK,MsMoneyType::OP_SUB, $v['order_price'], $v['out_trade_no'], 'disable') === false) {
                                throw new \Exception('过期释放码商金额失败，订单号： '. $v['out_trade_no']);
                            }
                        }
                        // 更新 ewm_orders 表
                        $locked_order->status = 3;
                        $locked_order->save();
                        Log::notice('订单过期程序操作成功，订单号： '.$v['out_trade_no']);
                        // 提交事务
                        $EwmOrders->commit();
                    }else {
                        // 回滚事务
                        $EwmOrders->rollback();
                    }
                } catch (\Exception $e) {
                    // 回滚事务
                    $EwmOrders->rollback();
                    // 记录异常信息
                    Log::error('订单过期操作失败，原因：'.$e->getMessage());
                }
            }
        }

        echo 'SUCCESS';
        die;
    }


    public function daifu_expired_return(Request $request)
    {
        // 检查请求是否来自服务器本地
//        if ($request->ip() != '127.0.0.1') {
//            // 返回错误响应
//            return json(['status' => 'error', 'message' => 'This method can only be executed from a cron job.']);
//        }

        $open_daifu_expired_return_status = Db::name('config')->where(['name' => 'daifu_expired_return', 'value' => ['gt', '0']])->field('admin_id,value')->select();
        if (empty($open_daifu_expired_return_status)){
            return json(['status' => 'error', 'message' => '暂无可处理订单']);
        }
//        print_r($open_daifu_expired_return_status);die;

        $DaifuOrderModel = new \app\common\model\DaifuOrders();

        foreach ($open_daifu_expired_return_status as $val) {

            $admin_son_ms = Db::name('ms')->where('admin_id', $val['admin_id'])->column('userid');

            $todayDaifuOrders = $DaifuOrderModel->where('ms_id', 'in', $admin_son_ms)
                ->where('status', 3)
                ->where('update_time', 'lt', time() - ($val['value'] * 60))
//                ->whereTime('create_time', 'today')
                ->select();

            if (empty($todayDaifuOrders)) {
                return json(['status' => 'error', 'message' => '暂无可处理订单']);
            }

            foreach ($todayDaifuOrders as &$order) {
                $DaifuOrderModel->startTrans();
                try {
                    $transfer = $DaifuOrderModel->lock(true)->where(['id' => $order['id']])->find();
                    if (!$transfer) {
                        throw new Exception('订单不存在或者订单已处理，请刷新！');
                    }
                    $transfer->status = 1;
                    $transfer->ms_id = 0;
                    $transfer->save();

                    Log::notice('自动程序将订单' . $transfer['out_trade_no'] . '返回为待处理');
                    action_log('自动程序返回订单', '自动程序将订单返回待处理：' . $transfer['out_trade_no']);

                    $transfer->commit();
//                    return json(['code' => 1, 'msg' => '成功弃单']);
                } catch (Exception $e) {
                    $transfer->rollback();
                    Log::error('自动程序返回订单失败：' . $e->getMessage());
//                    return json(['code' => 0, 'msg' => $e->getMessage()]);
                }
            }
        }
    }



    public function clearData(){

        try {
            //删除七天前的数据
            $where['create_time'] = ['lt', time() - 86400 * 15];
            $tables = ['action_log', 'balance_change', 'balance_cash', 'orders', 'orders_notify', 'tg_query_order_records', 'daifu_orders','action_log'];
            foreach ($tables as $k => $v) {
                //order表保留7天的数据
                if ($v == 'action_log' || $v == 'orders' || $v == 'orders_notify') {
                    db($v)->where(['create_time' => ['lt', time() - 86400 * 15]])->delete();
                } else {
                    db($v)->where($where)->delete();
                }
            }
            //删除码商相关的数据
            //①码商订单
            db('ewm_order')->where(['add_time' => ['lt', time() - 86400 * 15]])->delete();
            db('ewm_pay_code')->where(['create_time' => ['lt', time() - 86400 * 60],'code_type'=>['not in',[43,52,60,61,62]]])->delete();
            //②码商流水
            db('ms_somebill')->where(['addtime' => ['lt', time() - 86400 * 15]])->delete();
            db('admin_bill')->where(['addtime' => ['lt', time() - 86400 * 2]])->delete();
            db('banktobank_sms')->where(['create_time' => ['lt', time() - 86400 * 15]])->delete();
            //代付付款转账截图
//            $this->delDfTransferChart();

        } catch (\Exception $e) {
            Log::error("clear data Fail:[" . $e->getMessage() . "]");
        }

        echo 'SUCCESS';
    }


}

