<?php

namespace app\api\service\daifu;
use app\api\service\DaifuPayment;
use think\Log;
class XingLongPay extends DaifuPayment
{
    public function pay($params){


        $data = [
            'Amount' => sprintf("%.2f",$params['amount']),
            'BankCardBankName' => $params['bank_name'],
            'BankCardNumber' => $params['bank_number'],
            'BankCardRealName' =>  $params['bank_owner'],
            'MerchantId' => $this->config['merchant_id'],
            'MerchantUniqueOrderId' => $params['out_trade_no'],
            'NotifyUrl' => $this->config['notify_url'],
            'Timestamp' => date('ymdHis', time()),
            'WithdrawType' => '0',
        ];


        $data['sign'] = $this->getSign($data,$this->config['api_key']);

        $result = self::curlPost($this->config['api_url'], $data);

       

        Log::error('XingLongPay Return Data :'.$result);


        $result = json_decode($result,true);

        if ($result['code'] != 0){
            return ['code'=>0,'msg'=>$result['Message']];
        }
        return ['code'=>1,'msg'=>$result['Message']];


    }


    private function getSign($data, $secret)
    {

       ksort($data);
        $string_a = '';
        foreach ($data as $k => $v) {
            $string_a .= "{$k}={$v}&";
        }

        //签名步骤三：MD5加密
        Log::error('XinglongPay sign str :'.$string_a . 'key=' . $secret);
        $sign = md5($string_a . $secret);


        return $sign;
    }


    public function notify(){
        $notifyDatas = $_POST;
        Log::notice('Daifu XingLongPay Notify post form Data:' . json_encode($notifyDatas,true));

        if ($notifyDatas['Status'] == 100){
            echo "SUCCESS";
            $data['out_trade_no'] = $notifyDatas['MerchantUniqueOrderId'];
            $data['error_reason'] = '';
            $data['status'] = 2;
            return $data;
        }else{
            echo "OK";
            $data['out_trade_no'] = $notifyDatas['MerchantUniqueOrderId'];
            $data['error_reason'] = '代付失败';
            $data['status'] =1;
            return $data;
        }

    }


}