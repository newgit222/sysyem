<?php

namespace app\api\service\daifu;
use app\api\service\DaifuPayment;
use think\Db;
use think\Log;
class JjDfPay extends DaifuPayment
{
    public function pay($params,$dfChannel){
        $data = [
            'userName' => $dfChannel['merchant_id'],
            'version' => '2.0',
            'cardName' => trim($params['bank_owner']),
            'cardNum' => trim($params['bank_number']),
            'openBank' => trim($params['bank_name']),
            'amount' => sprintf("%.2f",$params['amount']),
            'outOrderId' => $params['out_trade_no'],
            'returnUrl' => $dfChannel['notify_url'],
        ];

        $data['sign'] = $this->getSign($data,$dfChannel['api_key']);
        Log::error('JjDfPayApiData :'.json_encode($data,true));

        $headers = array(
            "Content-type: application/json;charset='utf-8'",
            "Accept: application/json",
        );

//        $result = self::curlPost($dfChannel['api_url'], json_encode($data),[CURLOPT_HTTPHEADER=>$headers]);
        $result = $this->send_post_json($dfChannel['api_url'],json_encode($data,true));
        Log::error('JjDfPayReturnData :'.$result);


        $result = json_decode($result,true);
        if (!empty($result)){
            if ($result['code'] != 1){
                return ['code'=>0,'msg'=>$result['msg']];
            }
        }

        return ['code'=>1,'msg'=>$result['msg']];
    }



    private function getSign($data, $secret)
    {

        //签名步骤一：按字典序排序参数
//        ksort($data);
        $string_a = '';
        foreach ($data as $k => $v) {
            $string_a .= "{$k}={$v}&";
        }

        //签名步骤三：MD5加密
        Log::error('JjDfPaySignStr :'.$string_a . 'access_token=' . $secret);
        $sign = md5($string_a . 'access_token=' . $secret);

        // 签名步骤四：所有字符转为大写
        $result = strtoupper($sign);

        return $result;
    }


    public function notify(){
        $input = file_get_contents("php://input");
        Log::notice("JjDfPayNotifyData".$input);
        $notifyData = json_decode($input,true);
        if ($notifyData['status'] == 2){
            echo "success";
            $data['out_trade_no'] = $notifyData['outOrderId'];
            $data['error_reason'] = '';
            $data['status'] = 2;
            return $data;
        }else{
            echo "success";
            $data['out_trade_no'] = $notifyData['outOrderId'];
            $data['error_reason'] = $notifyData['orderMsg'];
            $data['status'] =1;
            return $data;
        }
    }

    public function balance($channel){
        $data = [
            'userName' => $channel['merchant_id'],
            'version' => '2.0'
        ];

        $data['sign'] = $this->getSign($data,$channel['api_key']);

        Log::error('JjDfPaybalancePayBalanceApiData :'.json_encode($data,true));
        $headers = array(
            "Content-type: application/json;charset='utf-8'",
            "Accept: application/json",
        );

//        $result = self::curlPost($channel['query_balance_url'], json_encode($data),[CURLOPT_HTTPHEADER=>$headers]);
        $result = $this->send_post_json($channel['query_balance_url'],json_encode($data,true));
        Log::error('JjDfPaybalancePaybalanceReturnData :'.$result);


        $result = json_decode($result,true);

        if ($result['code'] != 1){

        }else{
            Db::name('daifu_orders_transfer')->where('id',$channel['id'])->update(['balance' => $result['balance']]);
        }
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


}