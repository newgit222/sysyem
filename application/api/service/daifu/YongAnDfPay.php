<?php

namespace app\api\service\daifu;
use app\api\service\DaifuPayment;
use think\Log;
class YongAnDfPay extends DaifuPayment
{
    public function pay($params){
        $data = [
            'mchno' => $this->config['merchant_id'],
            'orderid' => $params['out_trade_no'],
            'amount' => sprintf("%.2f",$params['amount']),
            'accno' => $params['bank_number'],
            'accnm'=> $params['bank_owner'],
            'banknm' => $params['bank_name'],
            'acctype' => 1,
            'callback' => $this->config['notify_url']
        ];
        $data['sign'] = $this->getSign($data,$this->config['api_key']);
        Log::error('YongAnDfPay Api Data :'.json_encode($data,true));

        $headers = array(
            "Content-type: application/json;charset='utf-8'",
            "Accept: application/json",
        );

        $result = self::curlPost($this->config['api_url'], json_encode($data),[CURLOPT_HTTPHEADER=>$headers]);
//        $result = self::curlPost($this->config['order_addr'],$data);
        Log::error('YongAnDfPay Return Data :'.$result);

        $result = json_decode($result,true);

        if ($result['status'] != 1){
            return ['code'=>0,'msg'=>$result['msg']];
        }
        return ['code'=>1,'msg'=>$result['msg']];
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


        return $sign;
    }


    public function notify(){
        $input = file_get_contents("php://input");
        Log::error("Daifu YongAnDfPay Notify Data : ".$input);
        
        $notifyData = json_decode($input, true);

        if ($notifyData['status'] == 0 || $notifyData['status'] == 10 || $notifyData['status'] == 12){
            echo "success";
            $data['out_trade_no'] = $notifyData['orderid'];
            if ($notifyData['status'] == 0){
                $data['status'] = 2;
                $data['error_reason'] = '';
            }else{
                $data['status'] = 1;
                $data['error_reason'] = $notifyData['msg'];
            }
            return $data;
        }
        echo "Error";
        Log::error('Daifu YongAnDfPay Notify Error Data:' . json_encode($notifyData,true));
    }
}