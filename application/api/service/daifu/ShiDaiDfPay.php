<?php

namespace app\api\service\daifu;
use app\api\service\DaifuPayment;
use think\Db;
use think\Log;
class ShiDaiDfPay extends DaifuPayment
{
    public function pay($params,$dfChannel){
        $data = [
            'Timestamp' => time(),
            'AccessKey' => $dfChannel['merchant_id'],
            'PayChannelId' => '600',
            'Payee' => trim($params['bank_owner']),
            'PayeeNo' => trim($params['bank_number']),
            'PayeeAddress' => trim($params['bank_name']),
            'OrderNo' => $params['out_trade_no'],
            'Amount' => sprintf("%.2f",$params['amount']),
            'CallbackUrl' => $dfChannel['notify_url'],
        ];
        $data['Sign'] = $this->getSign($data,$dfChannel['api_key']);

        Log::error('ShiDaiDfPayApiData :'.json_encode($data,true));


        $result = $this->send_post_json($dfChannel['api_url'],json_encode($data,true));
        Log::error('ShiDaiDfPayReturnData :'.$result);
        $result = json_decode($result,true);
        if (!empty($result)) {
            if ($result['Code'] != 0) {
                return ['code' => 0, 'msg' => $result['Message']];
            }
        }

        return ['code'=>1,'msg'=>'请求成功'];
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
        Log::error('ShiDaiDfPay str :'.$string_a . 'SecretKey=' . $secret);
        $sign = md5($string_a . 'SecretKey=' . $secret);

        // 签名步骤四：所有字符转为大写
//        $result = strtoupper($sign);

        return $sign;
    }

    public function notify(){
        $input = file_get_contents("php://input");
        Log::notice("ShiDaiDfPayNotifyData".$input);

        $notifyData = json_decode($input,true);

        if ($notifyData['Status'] == 4){
            echo "ok";
            $data['out_trade_no'] = $notifyData['OrderNo'];
            $data['error_reason'] = '';
            $data['status'] = 2;
            return $data;
        }else{
            echo "ok";
            $data['out_trade_no'] = $notifyData['OrderNo'];
            $data['error_reason'] = '代付失败';
            $data['status'] =1;
            return $data;
        }
    }

    public function balance($channel){
        $data = [
            'Timestamp' => time(),
            'AccessKey' => $channel['merchant_id'],
        ];

        $data['Sign'] = $this->getSign($data,$channel['api_key']);
        

        $result = $this->send_post_json($channel['query_balance_url'],json_encode($data,true));
        Log::error('ShiDaiDfPayBalanceReturnData :'.$result);
        $result = json_decode($result,true);
        if ($result['Code'] != '0'){

        }else{
            Db::name('daifu_orders_transfer')->where('id',$channel['id'])->update(['balance' => $result['Data']['Balance']]);
        }

    }
}