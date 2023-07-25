<?php

namespace app\api\service\daifu;
use app\api\service\DaifuPayment;
use think\Db;
use think\Log;
class WuXianPay extends DaifuPayment
{
    public function pay($params,$dfChannel){
        $data = [
            'merchant_id' => $dfChannel['merchant_id'],
            'pay_type' => '1',
            'out_order_no' => $params['out_trade_no'],
            'amount' => sprintf("%.2f",$params['amount']) * 100,
            'notify_url' =>  $dfChannel['notify_url'],
            'account_name' => trim($params['bank_owner']),
            'account_no' => trim($params['bank_number']),
            'bank_name' => trim($params['bank_name']),
        ];

        $data['sign'] = $this->getSign($data,$dfChannel['api_key']);

        Log::error('WuXianPayApiData :'.json_encode($data,true));
        $apiurl = $dfChannel['api_url'];
        $result = self::curlPost($apiurl,$data);
        Log::error('WuXianPayReturnData :'.$result);
        $result = json_decode($result,true);
        if (!empty($result)) {
            if ($result['code'] != 200) {
                return ['code' => 0, 'msg' => $result['msg']];
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
        Log::error('WuXianPaySignStr: '.$string_a . 'key=' . $secret);
        $sign = md5($string_a . 'key=' . $secret);

        // 签名步骤四：所有字符转为大写
        $result = strtoupper($sign);

        return $result;
    }


    public function notify(){
        $notifyData = $_POST;
        Log::error("DaifuWuXianPayNotifyData: ".json_encode($notifyData,true));
        if ($notifyData['code'] == 2 || $notifyData['code'] == 3){
            echo "success";
            $data['out_trade_no'] = $notifyData['out_order_no'];
            $data['error_reason'] = '';
            $data['status'] = 2;
            return $data;
        }else{
            echo "success";
            $data['out_trade_no'] = $notifyData['out_order_no'];
            $data['error_reason'] = '代付失败';
            $data['status'] =1;
            return $data;
        }
//        Log::error('DaifuWuXianPayNotifyErrorData:' . json_encode($notifyData,true));
    }

    public function balance($channel){
        $data = [
            'merchant_id' => $channel['merchant_id'],
            'req_time' => date('YmdHis',time())
        ];

        $data['sign'] = $this->getSign($data,$channel['api_key']);
        $result = self::curlPost($channel['query_balance_url'],$data);
        Log::error('WuXianPayBalanceReturnData :'.$result);
        $result = json_decode($result,true);
        if ($result['code'] != 200){

        }else{
            Db::name('daifu_orders_transfer')->where('id',$channel['id'])->update(['balance' => $result['balance']]);
        }
    }
}