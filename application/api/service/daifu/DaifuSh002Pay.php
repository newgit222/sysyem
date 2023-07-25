<?php

namespace app\api\service\daifu;
use app\api\service\DaifuPayment;
use think\Log;
class DaifuSh002Pay extends DaifuPayment
{
    public function pay($params){
        $data = [
            'id' => $this->config['merchant_id'],
            'shbh' => $params['out_trade_no'],
            'yh' => $params['bank_name'],
            'kh' => $params['bank_number'],
            'hm' => $params['bank_owner'],
            'je' => sprintf("%.2f",$params['amount']) * 100,
            'url' => $this->config['notify_url']
        ];
        $data['sign'] = $this->getSign($data,$this->config['api_key']);
        Log::error('DaifuSh002Pay Api Data :'.json_encode($data,true));
        $headers = array(
            "Content-type: application/json;charset='utf-8'",
            "Accept: application/json",
        );

        $result = self::curlPost($this->config['api_url'], json_encode($data),[CURLOPT_HTTPHEADER=>$headers]);
        Log::error('DaifuSh002Pay Return Data :'.$result);


        $result = json_decode($result,true);

        if ($result['code'] != 200){
            return ['code'=>0,'msg'=>$result['msg']];
        }
        return ['code'=>1,'msg'=>$result['msg']];


    }


    private function getSign($data, $secret)
    {

//        //签名步骤一：按字典序排序参数
//        ksort($data);
        $string_a = '';
        foreach ($data as $k => $v) {
            $string_a .= "{$k}={$v}&";
        }
//        $string_a = substr($string_a,0,strlen($string_a) - 1);
        //签名步骤三：MD5加密
        Log::error('OgDfPay sign str :'.$string_a . 'key=' . $secret);
        $sign = md5($string_a . 'key=' . $secret);

        // 签名步骤四：所有字符转为大写
//        $result = strtoupper($sign);

        return $sign;
    }


    public function notify(){
        $notifyDatas = $_POST;
        Log::notice('Daifu OgDfPay Notify post form Data:' . json_encode($notifyDatas,true));
        $input = file_get_contents("php://input");
        Log::notice("Daifu OgDfPay Notify post json Data".$input);

        $notifyData = json_decode($input,true);
        if ($notifyData['zt'] == 2){
            echo "OK";
            $data['out_trade_no'] = $notifyData['shbh'];
            $data['error_reason'] = $notifyData['xx'];
            $data['status'] = 2;
            return $data;
        }else{
            echo "OK";
            $data['out_trade_no'] = $notifyData['shbh'];
            $data['error_reason'] = $notifyData['xx'];
            $data['status'] =1;
            return $data;
        }

    }


}