<?php

namespace app\api\service\daifu;
use app\api\service\DaifuPayment;
use think\Db;
use think\Log;
class RuizeDfPay extends DaifuPayment
{
    public function pay($params,$dfChannel){
        $data = [
            'mch_id' => $dfChannel['merchant_id'],
            'out_trade_no' => $params['out_trade_no'],
            'amount' => sprintf("%.2f",$params['amount']),
            'receive_method' => '0',
            'bank_account' => trim($params['bank_number']),
            'bank_owner' => trim($params['bank_owner']),
            'bank_name' => trim($params['bank_name']),
            'attach' => '123',
            'callback_url' => $dfChannel['notify_url'],
            'timestamp' =>date('Y-m-d H:i:s',time())
        ];

        $data['sign'] = $this->getSign($data,$dfChannel['api_key']);

        Log::error('RuizeDfPayApiData :'.json_encode($data,true));

        $result = $this->send_post_json($dfChannel['api_url'],json_encode($data,true));

        Log::error('RuizeDfPayReturnData :'.$result);

        if (!empty($result)){
            if ($result['code'] != 0){
                return ['code'=>0,'msg'=>$result['msg']];
            }
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
        Log::error('RuizeDfPaySignStr :'.$string_a . 'key=' . $secret);
        $sign = md5($string_a . 'key=' . $secret);

        // 签名步骤四：所有字符转为大写
        $result = strtoupper($sign);

        return $result;
    }


    private function send_post_json($url, $jsonStr)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($jsonStr)
            )
        );
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $response;
    }



    public function notify(){
        $notifyData = $_POST;
        Log::notice('RuizeDfPayNotifyData:' . json_encode($notifyData,true));

        if ($notifyData['status'] == 3){
            echo "SUCCESS";
            $data['out_trade_no'] = $notifyData['out_trade_no'];
            $data['error_reason'] = $notifyData['reason'];
            $data['status'] = 2;
            return $data;
        }else{
            echo "SUCCESS";
            $data['out_trade_no'] = $notifyData['out_trade_no'];
            $data['error_reason'] = $notifyData['reason'];
            $data['status'] =1;
            return $data;
        }
    }

    public function balance($channel){
        $data = [
            'timestamp' => date('Y-m-d H:i:s',time()),
            'mch_id' => $channel['merchant_id'],
        ];
        $data['sign'] = $this->getSign($data,$channel['api_key']);
        $result = $this->send_post_json($channel['query_balance_url'],json_encode($data,true));
        Log::error('RuizeDfPayBalanceReturnData :'.$result);


        $result = json_decode($result,true);

        if ($result['code'] != 0){

        }else{
            Db::name('daifu_orders_transfer')->where('id',$channel['id'])->update(['balance' => $result['data']['balance']]);
        }


    }


}