<?php

namespace app\api\service\daifu;
use app\api\service\DaifuPayment;
use think\Db;
use think\Log;
class MiaoFuPay extends DaifuPayment
{
    public function pay($params,$dfChannel){

        //{“bankName”:”银行名称”,”accountName”:”账户名称”,”accountNumber”:”卡号”} 支付宝默认参数{“bankName”:”随便填”,”accountName”:”姓名”,”accountNumber”:”支付宝号”}

        $ext = array(
            'bankName' => $params['bank_name'],
            'accountName' => $params['bank_owner'],
            'accountNumber' => $params['bank_number'],
        );


        $ext = json_decode(json_encode($ext));

        $dfChannel['merchant_id'] = explode('|',$dfChannel['merchant_id']);

        $data = [
            'app_id' =>  $dfChannel['merchant_id'][0],
            'product_id' => $dfChannel['merchant_id'][1],
            'out_trade_no' => $params['out_trade_no'],
            'notify_url' => $this->config['notify_url'],
            'amount' => sprintf("%.2f",$params['amount']),
            'time' => time(),
            'desc' => 'abc',
            'ext' => $ext
        ];

        // $data = [
        //     'merchant_no' => $dfChannel['merchant_id'],
        //     'order_no' => $params['out_trade_no'],
        //     'order_amount' => $params['amount'],
        //     'ts' => time(),
        //     'receive_method' => 0,
        //     'bank_account' => trim($params['bank_number']),
        //     'bank_owner' => trim($params['bank_owner']),
        //     'bank_name' => trim($params['bank_name']),
        //     'attach' => 'attach',
        //     'callback_url' => $dfChannel['notify_url']
        // ];


        $data['sign'] = $this->getSign($data,$dfChannel['api_key']);


        $headers = array(
            "Content-type: application/json;charset='utf-8'",
            "Accept: application/json",
        );
      

        Log::error('MiaoFufPay Api Data :'.json_encode($data,true));

        $headers = array(
            "Content-type: application/json;charset='utf-8'",
            "Accept: application/json",
        );

        $result = self::curlPost($dfChannel['api_url'], json_encode($data),[CURLOPT_HTTPHEADER=>$headers]);

        Log::error('MiaoFufPay Return Data :'.$result);


        $result = json_decode($result,true);

        if (!empty($result)){
            if ($result['code'] != 200){
                return ['code'=>0,'msg'=>$result['msg']];
            }
        }

        return ['code'=>1,'msg'=>'请求成功'];

    }


    private function getSign($data, $secret)
    {


        if (isset($data['ext'])) unset($data['ext']);


        //签名步骤一：按字典序排序参数
        ksort($data);
        $string_a = '';
        foreach ($data as $k => $v) {
            $string_a .= "{$k}={$v}&";
        }
//        $string_a = substr($string_a,0,strlen($string_a) - 1);
        //签名步骤三：MD5加密
        Log::error('MiaoFufPay str :'.$string_a . 'key=' . $secret);
        $sign = md5($string_a . 'key=' . $secret);

        return $sign;
    }

    public function notify(){
        $notifyData = $_POST;
        Log::notice('Daifu MiaoFufPay Notify post form Data:' . json_encode($notifyData,true));

        if ($notifyData['trade_status'] == 1){
            echo "success";
            $data['out_trade_no'] = $notifyData['out_trade_no'];
            $data['error_reason'] = $notifyData['message'];
            $data['status'] = 2;
            return $data;
        }else{
            echo "success";
            $data['out_trade_no'] = $notifyData['out_trade_no'];
            $data['error_reason'] = $notifyData['message'];
            $data['status'] =1;
            return $data;
        }
    }



    public function balance($channel){
      
        $data = [
            'app_id' =>   explode('|',$channel['merchant_id'])[0],
            'time' => time(),
        ];

        $data['sign'] = $this->getSign($data,$channel['api_key']);
        Log::error('MiaoFufPay balance Api Data :'.json_encode($data,true));
        $headers = array(
            "Content-type: application/json;charset='utf-8'",
            "Accept: application/json",
        );

        $result = self::curlPost($channel['query_balance_url'], json_encode($data),[CURLOPT_HTTPHEADER=>$headers]);
        Log::error('MiaoFufPay balance Return Data :'.$result);


        $result = json_decode($result,true);
      
        if ($result['code'] != 200){

        }else{
            Db::name('daifu_orders_transfer')->where('id',$channel['id'])->update(['balance' => $result['data']['total_account']]);
        }


    }

}