<?php

namespace app\api\service\daifu;
use app\api\service\DaifuPayment;
use think\Db;
use think\Log;
class NewNiuNiuPay extends DaifuPayment
{
    public function pay($params,$dfChannel){
        $data = [
            'merchantId' => $dfChannel['merchant_id'],
            'tradeNo' => $params['out_trade_no'],
            'bankCard' => trim($params['bank_number']),
            'realName' => trim($params['bank_owner']),
            'bankName' => trim($params['bank_name']),
            'notifyUrl' => $dfChannel['notify_url'],
            'amount' => floor($params['amount'])
        ];

        $data['sign'] = $this->getSign($data,$dfChannel['api_key']);
        Log::error('NewNiuNiuPayApiData :'.json_encode($data,true));

        $result = self::curlPost($dfChannel['api_url'],$data);
        Log::error('NewNiuNiuPayReturnData :'.$result);


        $result = json_decode($result,true);

        if (!empty($result)){
            if ($result['code'] != 1){
                return ['code'=>0,'msg'=>$result['info']];
            }
        }

        return ['code'=>1,'msg'=>$result['info']];
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
        Log::error('NewNiuNiuPaySignStr :'.$string_a . 'key=' . $secret);
        $sign = md5($string_a . 'key=' . $secret);

        // 签名步骤四：所有字符转为大写
//        $result = strtoupper($sign);

        return $sign;
    }


    public function notify(){
        $notifyData = $_POST;
        Log::notice('DaifuNewNiuNiuPayNotifyData:' . json_encode($notifyData,true));

        if ($notifyData['status'] == 4){
            echo "ok";
            $data['out_trade_no'] = $notifyData['tradeNo'];
            $data['error_reason'] = '';
            $data['status'] = 2;
            return $data;
        }else{
            echo "ok";
            $data['out_trade_no'] = $notifyData['tradeNo'];
            $data['error_reason'] = $notifyData['errMsg'];
            $data['status'] =1;
            return $data;
        }
    }


    public function balance($channel){
        $data = [
            'merchantId' => $channel['merchant_id']
        ];

        $data['sign'] = $this->getSign($data,$channel['api_key']);
        Log::error('NewNiuNiuPayBalancePayApiData :'.json_encode($data,true));
        $result = self::curlPost($channel['query_balance_url'],$data);
        Log::error('NewNiuNiuPayBalanceReturnData :'.$result);
        $result = json_decode($result,true);

        if ($result['code'] != 1){

        }else{
            Db::name('daifu_orders_transfer')->where('id',$channel['id'])->update(['balance' => $result['data']['balance']]);
        }

    }

}