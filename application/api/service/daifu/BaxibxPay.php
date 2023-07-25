<?php

namespace app\api\service\daifu;
use app\api\service\DaifuPayment;
use think\Db;
use think\Log;
class BaxibxPay extends DaifuPayment
{
    public function pay($params,$dfChannel){

        // order_no,mch_account,amount,account_type,account_no,account_name,bank_code,bank_province,bank_city,bank_name,call_back_url,pay_type,verify_url,sign

        $data = [
            'order_no' => $params['out_trade_no'],
            'mch_account' => $dfChannel['merchant_id'],
            'amount' => intval($params['amount'] * 10000),
            'account_type' => 0,
            'account_no' => trim($params['bank_number']),
            'account_name' => trim($params['bank_owner']),
            'bank_code' => 'PIX',
            'bank_province' => 'CPF',
            'bank_city' => 'CPF',
            'bank_name' => 'CPF',
            'call_back_url' => $dfChannel['notify_url'],
            'pay_type' => '1',
        ];


        $data['sign'] = $this->getSign($data,$dfChannel['api_key']);

        Log::error('BaxibxPayApiData :'.json_encode($data,true));

        $headers = array(
            "Content-type: application/json;charset='utf-8'",
            "Accept: application/json",
        );

        $result = self::curlPost($dfChannel['api_url'], json_encode($data),[CURLOPT_HTTPHEADER=>$headers]);
        Log::error('BaxibxPayReturnData :'.$result);


        $result = json_decode($result,true);

       
        if (!isset($result['ret']) || $result['ret'] != 200){
            return ['code'=>0,'msg'=>$result['msg']];
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
        Log::error('BaxibxPaysign str :'.$string_a . 'key=' . $secret);
        $sign = md5($string_a . 'key=' . $secret);

        // 签名步骤四：所有字符转为大写
       $result = strtoupper($sign);

        return $result;
    }

    public function notify(){
        $input = file_get_contents("php://input");
        Log::notice("Daifu BaxibxPay Notify post json Data".$input);

//        merchant_no=96376701&
//        order_amount=100.00&
//        order_no=2301311637154051&status=6
//        &sign=5122afc6b1c73dedb15e32e152ac82c2&attach=attach&platform_no=2023013117124636680023999&reason=

        $notifyData = json_decode($input,true);
        if ($notifyData['status'] == 2){
            echo "success";
            $data['out_trade_no'] = $notifyData['order_no'];
            $data['error_reason'] = '';
            $data['status'] = 2;
            return $data;
        }else{
            echo "success";
            $data['out_trade_no'] = $notifyData['order_no'];
            $data['error_reason'] = '代付失败';
            $data['status'] =1;
            return $data;
        }
//        echo "Error";
//        Log::error('Daifu OgDfPay Notify Error Data:' . json_encode($notifyData,true));
    }



    public function balance($channel){
        $data = [
            'mch_account' => $channel['merchant_id'],
        ];

        $data['sign'] = $this->getSign($data,$channel['api_key']);
        Log::error('BaxibxPaybalancePaybalanceApiData :'.json_encode($data,true));
        $headers = array(
            "Content-type: application/json;charset='utf-8'",
            "Accept: application/json",
        );

        $result = self::curlPost($channel['query_balance_url'], json_encode($data),[CURLOPT_HTTPHEADER=>$headers]);
        Log::error('BaxibxPaybalancePaybalanceReturnData :'.$result);

        $result = json_decode($result,true);

        if ($result['ret'] != 200){

        }else{
            Db::name('daifu_orders_transfer')->where('id',$channel['id'])->update(['balance' => $result['balance']]);
        }


    }

}