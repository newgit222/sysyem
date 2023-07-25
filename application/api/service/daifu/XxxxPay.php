<?php

namespace app\api\service\daifu;
use app\api\service\DaifuPayment;
use think\Db;
use think\Log;
class XxxxPay extends DaifuPayment
{
    public function pay($params,$dfChannel){
        $Md5key = $dfChannel['api_key'];
        $data = [
            'amount' => $params['amount'],
            'bank_number' => $params['bank_number'],
            'bank_code' => $params['bank_name'],
            'bank_owner' => $params['bank_owner'],
            'mchid' => $dfChannel['merchant_id'],
            'out_trade_no' => $params['out_trade_no'],
            'notify_url' => $dfChannel['notify_url'],
            'body' => $params['body'],
            'subject' => $params['subject']
        ];
        ksort($data);
        $signData = http_build_query($data);
        $signData = $signData . '&' . $Md5key;
        $sign = md5($signData);

        $data['sign'] = $sign;
        Log::error('XXXXDaifuApiData :'.json_encode($data,true));
        $apiurl = $dfChannel['api_url'];
        $result = self::curlPost($apiurl,$data);
        Log::error('XXXXDaifuReturnData :'.$result);
        $result = json_decode($result,true);
        if (!empty($result)) {
            if ($result['code'] != 1) {
                return ['code' => 0, 'msg' => $result['msg']];
            }
        }
        return ['code'=>1,'msg'=>'请求成功'];
    }



    public function notify(){
        $notifyData = $_POST;
        Log::error("Daifu XxxxPay Notify Data : ".json_encode($notifyData,true));
        if ($notifyData['code'] == 1){
            echo "SUCCESS";
            $data['out_trade_no'] = $notifyData['out_trade_no'];
            $data['status'] = $notifyData['status'];
            $data['error_reason'] = $notifyData['error_reason'];
            return $data;
        }
        echo "Error";
        Log::error('Daifu XxxxPay Notify Error Data:' . json_encode($notifyData,true));
    }


    public function balance($channel){
        $data = [
            'mchid' => $channel['merchant_id']
        ];
        $result = self::curlPost($channel['query_balance_url'],$data);
        Log::error('XXXXDaifuBalanceReturn Data :'.$result);
        $result = json_decode($result,true);
        if ($result['code'] != 0){

        }else{
            Db::name('daifu_orders_transfer')->where('id',$channel['id'])->update(['balance' => $result['data']['balance']]);
        }
    }

}