<?php

namespace app\api\service\daifu;
use app\api\service\DaifuPayment;
use think\Db;
use think\Log;
class TmzgPay extends DaifuPayment
{
    public function pay($params,$dfChannel){

        // 毫秒时间戳
        $time = explode ( " ", microtime () );
        $time = $time [1] . ($time [0] * 1000);
        $time2 = explode ( ".", $time );
        $time = $time2 [0];


        //merchant_id,channel_code,order_amount,device,request_time,user_name,bank_name,bank_card,notify_url,return_url,sign
        $data = [
            'merchant_id' => $dfChannel['merchant_id'],
            'merchant_no' => $params['out_trade_no'],
            'channel_code' => 'XM500206',
            'order_amount' => $params['amount'],
            'device' => 'pc',
            'request_time' => $time,
            'user_name' => $params['bank_owner'],
            'bank_name' => $params['bank_name'],
            'bank_card' => $params['bank_number'],
//            'notify_url' => urlencode($dfChannel['notify_url']),
            'notify_url' => $dfChannel['notify_url'],






        ];

        ksort($data);
        $md5str = "";
        foreach ($data as $key => $val) {
            $md5str = $md5str . $key . "=" . urlencode($val) . "&";
        }
        $string_a = substr($md5str,0,strlen($md5str) - 1);
        Log::error('TmzgPaySignStr :'.$string_a . $dfChannel['api_key']);
        $sign = strtoupper(md5($string_a . $dfChannel['api_key']));
        $data['sign'] = $sign;




        Log::error('TmzgPayApiData :'.json_encode($data,true));

        $headers = array(
            "Content-type: application/json;charset='utf-8'",
            "Accept: application/json",
        );


//        halt( json_encode($data));

        $result = self::curlPost($dfChannel['api_url'], json_encode($data),[CURLOPT_HTTPHEADER=>$headers]);
        Log::error('TmzgPayReturnData :'.$result);


        $result = json_decode($result,true);
//        halt($result);
        if (isset($result['code']) && $result['code'] != 200 ){
            return ['code'=>0,'msg'=>$result['msg']];
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

        $string_a = substr($string_a,0,strlen($string_a) - 1);

//        halt($string_a . $secret);
        
        $signstr = $string_a . $secret;
        $sign = md5($signstr);

        // 签名步骤四：所有字符转为大写
        $result = strtoupper($sign);

//        halt($result);

        return $result;
    }

    public function notify(){
        $input = file_get_contents("php://input");
        Log::notice("Daifu TmzgPay Notify post json Data".$input);
//        merchant_no=96376701&
//        order_amount=100.00&
//        order_no=2301311637154051&status=6
//        &sign=5122afc6b1c73dedb15e32e152ac82c2&attach=attach&platform_no=2023013117124636680023999&reason=

        $notifyData = json_decode($input,true);
        if ($notifyData['status'] == 1){
            echo "SUCCESS";
            $data['out_trade_no'] = $notifyData['merchant_no'];
            $data['error_reason'] = '';
            $data['status'] = 2;
            return $data;
        }else{
            echo "SUCCESS";
            $data['out_trade_no'] = $notifyData['merchant_no'];
            $data['error_reason'] = $notifyData['error_reason'];
            $data['status'] =1;
            return $data;
        }
//        echo "Error";
//        Log::error('Daifu OgDfPay Notify Error Data:' . json_encode($notifyData,true));
    }



    public function balance($channel){

        // 毫秒时间戳
        $time = explode ( " ", microtime () );
        $time = $time [1] . ($time [0] * 1000);
        $time2 = explode ( ".", $time );
        $time = $time2 [0];


        $data = [
            'merchant_id' => $channel['merchant_id'],
            'merchant_no' => time().rand(10000000000,99999999999),
            'request_time' => time(),
        ];

        $data['sign'] = $this->getSign($data,$channel['api_key']);
        Log::error('TmzgPaybalanceApi Data :'.json_encode($data,true));
        $headers = array(
            "Content-type: application/json;charset='utf-8'",
            "Accept: application/json",
        );

        $result = self::curlPost($channel['query_balance_url'], json_encode($data),[CURLOPT_HTTPHEADER=>$headers]);
        Log::error('TmzgPaybalanceReturnData :'.$result);


        $result = json_decode($result,true);

        if ($result['code'] != 200){

        }else{
            Db::name('daifu_orders_transfer')->where('id',$channel['id'])->update(['balance' => $result['data']['balance']]);
        }


    }

}