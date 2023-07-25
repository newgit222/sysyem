<?php

namespace app\api\service\daifu;
use app\api\service\DaifuPayment;
use think\Db;
use think\Log;
class HaiOuPay extends DaifuPayment
{
    public function pay($params,$dfChannel){
        $merchant_id = explode('|',$dfChannel['merchant_id']);
        $data = [
            'Timestamp' => time(),
            'AccessKey' => $merchant_id[0],
            'PayChannelId' => $merchant_id[1],
            'Payee' => trim($params['bank_owner']),
            'PayeeAddress' => trim($params['bank_name']),
            'PayeeNo' => trim($params['bank_number']),
            'OrderNo' => $params['out_trade_no'],
            'Amount' => sprintf("%.2f",$params['amount']),
            'CallbackUrl' => $dfChannel['notify_url'],
        ];

        $data['Sign'] = $this->getSign($data,$dfChannel['api_key']);


        Log::error('HaiOuPay Api Data :'.json_encode($data,true));

        $result = $this->send_post_json($dfChannel['api_url'],json_encode($data,true));

        Log::error('HaiOuPay Return Data :'.$result);
        $result = json_decode($result,true);

        if ($result['Code'] != 0){
            return ['code'=>0,'msg'=>$result['Message']];
        }
        return ['code'=>1,'msg'=>$result['Message']];



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
        Log::error('HaiOuPaySignStr :'.$string_a . 'SecretKey=' . $secret);
        //签名步骤三：MD5加密
        $sign = md5($string_a . 'SecretKey=' . $secret);

        // 签名步骤四：所有字符转为大写
//        $result = strtoupper($sign);

        return $sign;
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
        $input = file_get_contents("php://input");
        Log::notice("DaifuHaiOuPay Notify post json Data".$input);

        $notifyData = json_decode($input,true);
        if ($notifyData['Status'] == 4){
            echo "ok";
            $data['out_trade_no'] = $notifyData['OrderNo'];
            $data['error_reason'] = '';
            $data['status'] = 2;
            return $data;
        }else if ($notifyData['Status'] == 16){
            echo "ok";
            $data['out_trade_no'] = $notifyData['OrderNo'];
            $data['error_reason'] = '处理失败';
            $data['status'] = 1;
            return $data;
        }
    }

    public function balance($channel){
        $merchant_id = explode('|',$channel['merchant_id']);
        $data = [
            'Timestamp' => time(),
            'AccessKey' => $merchant_id[0],
        ];
        $data['Sign'] = $this->getSign($data,$channel['api_key']);
        $result = $this->send_post_json($channel['query_balance_url'],json_encode($data,true));
        Log::error('HaiOuPayBalance Return Data :'.$result);


        $result = json_decode($result,true);

        if ($result['Code'] != 0){

        }else{
            Db::name('daifu_orders_transfer')->where('id',$channel['id'])->update(['balance' => $result['Data'][0]['Balance']]);
        }


    }

}