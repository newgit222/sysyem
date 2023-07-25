<?php

namespace app\api\service\daifu;
use app\api\service\DaifuPayment;
use think\Db;
use think\Log;
class HongYuanPay extends DaifuPayment
{
    public function pay($params,$dfChannel){
        $data = [
            'mchid' => $dfChannel['merchant_id'],
            'out_trade_no' => $params['out_trade_no'],
            'money' => sprintf("%.2f",$params['amount']),
            'notifyurl' => $dfChannel['notify_url'],
            'bankname' => trim($params['bank_name']),
            'subbranch' => trim($params['bank_name']),
            'accountname' => trim($params['bank_owner']),
            'cardnumber' => trim($params['bank_number']),
            'cardtype' => '0'
        ];

        $data['sign'] = $this->getSign($data,$dfChannel['api_key']);

        Log::error('HongYuanPayApiData :'.json_encode($data,true));
        $apiurl = $dfChannel['api_url'];
        $result = self::curlPost($apiurl,$data);
        Log::error('HongYuanPayReturnData :'.$result);
        $result = json_decode($result,true);

        if (!empty($result)) {
            if ($result['status'] != 'success') {
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
        Log::error('HongYuanPaySignStr: '.$string_a . 'key=' . $secret);
        $sign = md5($string_a . 'key=' . $secret);

        // 签名步骤四：所有字符转为大写
        $result = strtoupper($sign);

        return $result;
    }


    public function notify(){
        $notifyData = $_POST;
        Log::error("DaifuHongYuanPayNotifyData: ".json_encode($notifyData,true));
        if ($notifyData['refCode'] == '3'){
            echo "success";
            $data['out_trade_no'] = $notifyData['out_trade_no'];
            $data['error_reason'] = '';
            $data['status'] = 2;
            return $data;
        }else{
            echo "success";
            $data['out_trade_no'] = $notifyData['out_trade_no'];
            if ($notifyData['refCode'] == 4){
                $reason = '已驳回';
            }else{
                $reason = '已冲正';
            }
            $data['error_reason'] = $reason;
            $data['status'] =1;
            return $data;
        }
    }



    public function balance($channel){
        $data = [
            'mchid' => $channel['merchant_id'],
        ];

        $data['sign'] = $this->getSign($data,$channel['api_key']);
        $result = self::curlPost($channel['query_balance_url'],$data);
        Log::error('HongYuanPayBalanceReturnData :'.$result);
        $result = json_decode($result,true);
        if ($result['status'] != 'success'){

        }else{
            Db::name('daifu_orders_transfer')->where('id',$channel['id'])->update(['balance' => $result['balance']]);
        }
    }
}