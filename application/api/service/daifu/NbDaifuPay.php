<?php

namespace app\api\service\daifu;
use app\api\service\DaifuPayment;
use think\Db;
use think\Log;
class NbDaifuPay extends DaifuPayment
{
    public function pay($params,$dfChannel){
        $data = [


            'p1_merchantno' =>$dfChannel['merchant_id'],
            'p2_amount' => sprintf("%.2f",$params['amount']),
            "p3_orderno"=>$params['out_trade_no'],
            'p4_truename' => trim($params['bank_owner']),
            'p5_cardnumber' =>trim($params['bank_number']),

            'p6_branchbankname' =>trim($params['bank_name']),


//            'callback_url' => $dfChannel['notify_url'],
        ];
        $data['sign'] = $this->getSign($data,$dfChannel['api_key']);
        $apiurl = $dfChannel['api_url'];
        $result = self::curlPost($apiurl,$data);
        Log::error('NbDaifuPayReturnData :'.$result);
        //Log::error('Gppay Return Data :'.$result);
        //Log::error('Gppay Return Data :'.$result);


        $result = json_decode($result,true);

        if ($result['rspcode'] != 'A0'){
            return ['code'=>0,'msg'=>$result['rspmsg']];
        }
        return ['code'=>1,'msg'=>'请求成功'];


    }


    private function getSign($data, $secret)
    {

        //签名步骤一：按字典序排序参数
        ksort($data);
        $string_a = '';
        foreach ($data as $k => $v) {
            $string_a .= "{$k}={$v}&";
        }
//        $string_a = substr($string_a,0,strlen($string_a) - 1);
        //签名步骤三：MD5加密
        // Log::error(' str :'.$string_a . 'key=' . $secret);
        $sign = md5($string_a . 'key=' . $secret);

        // 签名步骤四：所有字符转为大写
        $result = strtoupper($sign);

        return $result;
    }


    //异步方法

    public function notify()
    {
//        $input = file_get_contents("php://input");
//        Log::notice("Daifu OgDfPay Notify post json Data" . $input);
        $notifyData = $_POST;
        Log::notice('DaifuNbDaifuPayNotifypostformData:' . json_encode($notifyData, true));
//        merchant_no=96376701&
//        order_amount=100.00&
//        order_no=2301311637154051&status=6
//        &sign=5122afc6b1c73dedb15e32e152ac82c2&attach=attach&platform_no=2023013117124636680023999&reason=

//        $notifyData = json_decode($input,true);
        if ($notifyData['status'] == "2") {
            echo "success";
            $data['out_trade_no'] = $notifyData['orderno'];
            $data['error_reason'] = '';
            $data['status'] = 2;
            return $data;
        } else {
            echo "success";
            $data['out_trade_no'] = $notifyData['orderno'];
            if ($notifyData['status'] == 3){
                $reason = '打款失败';
            }elseif($notifyData['status'] == 6){
                $reason = '审核失败';
            }else{
                $reason = '打款失败';
            }
            $data['error_reason'] = $reason;
            $data['status'] = 1;
            return $data;
        }
    }



    public function balance($channel){
        $data = [
            'p1_merchantno' => $channel['merchant_id']
        ];
        $data['sign'] = $this->getSign($data,$channel['api_key']);
        $result = self::curlPost($channel['query_balance_url'],$data);
        Log::error('NbDaifuPayBalanceReturn Data :'.$result);
        $result = json_decode($result,true);
        if ($result['rspcode'] != 'A0'){

        }else{
            Db::name('daifu_orders_transfer')->where('id',$channel['id'])->update(['balance' => $result['t0money']]);
        }
    }
}