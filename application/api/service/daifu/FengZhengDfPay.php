<?php

namespace app\api\service\daifu;
use app\api\service\DaifuPayment;
use think\Log;
class FengZhengDfPay extends DaifuPayment
{
    public function pay($params){
        $data = [
            'method' => 'pay_auto',
            'app_id' => $this->config['merchant_id'],
            'order_sn' => $params['out_trade_no'],
            'order_amount' => floor($params['amount']),
            'user_name' => $params['bank_owner'],
            'user_account' => $params['bank_number'],
//            'wallet_id' => 101,
        ];

        $data['signature'] = $this->getSign($data,$this->config['api_key']);
        Log::error('FengZhengDfPay Api Data :'.json_encode($data,true));

//        $headers = array(
//            "Content-type: application/json;charset='utf-8'",
//            "Accept: application/json",
//        );

//        $result = self::curlPost($this->config['order_addr'], json_encode($data),[CURLOPT_HTTPHEADER=>$headers]);
        $result = self::curlPost($this->config['api_url'],$data);
        Log::error('FengZhengDfPay Return Data :'.$result);
        $result = json_decode($result, true);

        if ($result['code'] != 0){
            return ['code'=>0,'msg'=>$result['message']];
        }
        return ['code'=>1,'msg'=>$result['message']];
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
        $sign = md5($string_a . 'key=' . $secret);

        // 签名步骤四：所有字符转为大写
        $result = strtoupper($sign);

        return $result;
    }


    public function notify(){
        $notifyData = $_POST;
        Log::error("Daifu FengZhengDfPay Notify Data : ".json_encode($notifyData,true));
        if ($notifyData['code'] == 0){
            $data['out_trade_no'] = $notifyData['data']['order_sn'];
            if ($notifyData['data']['pay_status'] == 1){
                $data['status'] = 2;
            }else{
                $data['status'] = 1;
            }
            $data['error_reason'] = '';
            return $data;
        }

    }

}