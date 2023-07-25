<?php

namespace app\api\service\daifu;
use app\api\service\DaifuPayment;
use think\Db;
use think\Log;
class ChuanQiPay extends DaifuPayment
{
    public function pay($params,$dfChannel){

        //merchant_code,order_id,amount,notify_url,paytype,bank_name,username,cardno,fullname,sign

        $data = [
            'merchant_code' => $dfChannel['merchant_id'],
            'order_id' => $params['out_trade_no'],
            'amount' => $params['amount'],
            'notify_url' => $dfChannel['notify_url'],
            'paytype' => 'bank',
            'bank_name' => $params['bank_name'],
            'username' => $params['bank_owner'],
            'cardno' => $params['bank_number'],
            'fullname' => $params['bank_name'],
        ];


        $data['sign'] = $this->getSign($data,$dfChannel['api_key']);
        Log::error('ChuanQiPay :'.json_encode($data,true));



        $result = self::curlPost($dfChannel['api_url'], $data);

        Log::error('ChuanQiPay :'.$result);


        $result = json_decode($result,true);

        if (!isset($result['code']) or $result['code'] != 0){
            return ['code'=>0,'msg'=>$result['msg']];
        }

        return ['code'=>1,'msg'=>$result['msg']];
    }



    private function getSign($data, $secret)
    {

        /**
         * 签名
        第一步，除sign外非空的参数按照参数名ASCII码从小到大排序（字典序），使用URL键值对的格式（即k1=v1&k2=v2）拼接成字符串stringA。

        第二步，在stringA最后拼接上key（即k1=v1&k2=v2key）得到stringSignTemp字符串，并对stringSignTemp进行MD5运算，再将得到的字符串所有字符转换为小写

        例如
        下单非空的参数：
        a=1
        b=2
        c=3
        秘钥是：
        253a2723663579987697065e66bf1658

        待签名的字符串为：
        a=1&b=2&c=3253a2723663579987697065e66bf1658

         */

        //签名步骤一：按字典序排序参数
        ksort($data);
        $string_a = '';
        foreach ($data as $k => $v) {
            $string_a .= "{$k}={$v}&";
        }

        //把最后一个&去掉
        $string_a = rtrim($string_a, '&');

        //签名步骤三：MD5加密
        Log::error('ChanqiPay str :'.$string_a . $secret);
        $sign = md5($string_a  . $secret);

        // 签名步骤四：所有字符转为小写

//        $result = str($sign);

        return $sign;




       
    }


    public function notify(){
        $notifyData = $_POST;
        Log::notice("Daifu ChuanQiPay Notify post json Data".json_encode($notifyData));

        if ($notifyData['resp_code'] == '00'){
            echo "SUCCESS";
            $data['out_trade_no'] = $notifyData['order_id'];
            $data['error_reason'] = '';
            $data['status'] = 2;
            return $data;
        }else{
            echo "SUCCESS";
            $data['out_trade_no'] = $notifyData['outOrderId'];
            $data['error_reason'] = $notifyData['reason'];
            $data['status'] =1;
            return $data;
        }
    }

    public function balance($channel){
        $data = [
            'merchant_code' => $channel['merchant_id'],
        ];

        $data['sign'] = $this->getSign($data,$channel['api_key']);

        Log::error('ChuanQiPay :'.json_encode($data,true));

        $result = self::curlPost($channel['query_balance_url'], $data);
        Log::error('ChuanQiPay :'.$result);


        $result = json_decode($result,true);

        if ($result['code'] != 0){

        }else{
            Db::name('daifu_orders_transfer')->where('id',$channel['id'])->update(['balance' => $result['amount']]);
        }
    }

}