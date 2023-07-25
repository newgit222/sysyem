<?php

namespace app\api\service\daifu;
use app\api\service\DaifuPayment;
use think\Db;
use think\Log;
class OgDfPay extends DaifuPayment
{
    public function pay($params,$dfChannel){
        $data = [
            'merchant_no' => $dfChannel['merchant_id'],
            'order_no' => $params['out_trade_no'],
            'order_amount' => $params['amount'],
            'ts' => time(),
            'receive_method' => 0,
            'bank_account' => trim($params['bank_number']),
            'bank_owner' => trim($params['bank_owner']),
            'bank_name' => trim($params['bank_name']),
            'attach' => 'attach',
            'callback_url' => $dfChannel['notify_url']
        ];

        $data['sign'] = $this->getSign($data,$dfChannel['api_key']);

        Log::error('OgDfPay Api Data :'.json_encode($data,true));

        $headers = array(
            "Content-type: application/json;charset='utf-8'",
            "Accept: application/json",
        );

        $result = self::curlPost($dfChannel['api_url'], json_encode($data),[CURLOPT_HTTPHEADER=>$headers]);
        Log::error('OgDfPay Return Data :'.$result);


        $result = json_decode($result,true);

        if (!empty($result)){
            if ($result['code'] != 200 || $result['success'] != 'true'){
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
        Log::error('OgDfPaysign str :'.$string_a . 'key=' . $secret);
        $sign = md5($string_a . 'key=' . $secret);

        // 签名步骤四：所有字符转为大写
//        $result = strtoupper($sign);

        return $sign;
    }

    public function notify(){
        $input = file_get_contents("php://input");
        Log::notice("Daifu OgDfPay Notify post json Data".$input);
        $notifyData = $_POST;
        Log::notice('Daifu OgDfPay Notify post form Data:' . json_encode($notifyData,true));
//        merchant_no=96376701&
//        order_amount=100.00&
//        order_no=2301311637154051&status=6
//        &sign=5122afc6b1c73dedb15e32e152ac82c2&attach=attach&platform_no=2023013117124636680023999&reason=

//        $notifyData = json_decode($input,true);
        if ($notifyData['status'] == 6){
            echo "success";
            $data['out_trade_no'] = $notifyData['order_no'];
            $data['error_reason'] = $notifyData['reason'];
            $data['status'] = 2;
            return $data;
        }else{
            echo "success";
            $data['out_trade_no'] = $notifyData['order_no'];
            $data['error_reason'] = $notifyData['reason'];
            $data['status'] =1;
            return $data;
        }
//        echo "Error";
//        Log::error('Daifu OgDfPay Notify Error Data:' . json_encode($notifyData,true));
    }



    public function balance($channel){
        $data = [
            'merchant_no' => $channel['merchant_id'],
            'ts' => time(),
        ];

        $data['sign'] = $this->getSign($data,$channel['api_key']);
        Log::error('OgDfbalancePay balance Api Data :'.json_encode($data,true));
        $headers = array(
            "Content-type: application/json;charset='utf-8'",
            "Accept: application/json",
        );

        $result = self::curlPost($channel['query_balance_url'], json_encode($data),[CURLOPT_HTTPHEADER=>$headers]);
        Log::error('OgDfbalancePay balance Return Data :'.$result);


        $result = json_decode($result,true);

        if ($result['code'] != 200){

        }else{
            Db::name('daifu_orders_transfer')->where('id',$channel['id'])->update(['balance' => $result['data']['balance']]);
        }


    }

}