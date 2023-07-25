<?php

namespace app\api\service\daifu;
use app\api\service\DaifuPayment;
use think\Db;
use think\Log;
class RfywPay extends DaifuPayment
{
    public function pay($params){

        // amount,outOrderNum,mchNum,timestamp,account,accountName,bankName,notifyUrl,sign
        // MchNum,ChannelType,MchOrderNum,Money,CardAccount,CardNum,NotifyUrl,Sign

        $data = [
            'MchNum' => $this->config['merchant_id'],
            'ChannelType' => '01',
            'MchOrderNum' => $params['out_trade_no'],
            'Money' => $params['amount'],
            'CardAccount' => $params['bank_owner'],
            'CardNum' => $params['bank_number'],
            'NotifyUrl' => $this->config['notify_url'],
        ];


        $data['sign'] = $this->getSign($data,$this->config['api_key']);


        Log::error('AoksPay Api Data :'.json_encode($data,true));


        $result = self::curlPost($this->config['api_url'], $data);

        Log::error('AoksPay Return Data :'.$result);

        $result = json_decode($result,true);

        if ( !isset($result['success']) && !isset($result['errCode']) &&  $result['errCode'] != 0){
            return ['code'=>0,'msg'=>$result['msg']];
        }
        return ['code'=>1,'msg'=>$result['msg']];


    }


    private function getSign($data, $secret)
    {
        ksort($data);

        $string_a = '';
        foreach ($data as $k => $v) {
            $string_a .= "{$k}={$v}&";
        }
//        $string_a = substr($string_a,0,strlen($string_a) - 1);
        //签名步骤三：MD5加密
        Log::error('AoksPay str :'.$string_a . 'key=' . $secret);
        $sign = md5($string_a . 'key=' . $secret);


        return $sign;
    }

    public function notify(){
        $notifyData = $_POST;
        Log::notice("Daifu RfywPay Notify post json Data". json_encode($notifyData));

        if ($notifyData['ResultCode'] == 0){
            echo "success";
            $data['out_trade_no'] = $notifyData['MchOrderNum'];
            $data['error_reason'] = '';
            $data['status'] = 2;
            return $data;
        }else{
            echo "success";
            $data['out_trade_no'] = $notifyData['MchOrderNum'];
            $data['error_reason'] = $notifyData['ResultMsg'] ?? '';
            $data['status'] =1;
            return $data;
        }

    }


    public function balance($channel){

        $data = [
            'MchNum' => $channel['merchant_id'],
        ];

        // 	商户请求参数的签名串 MD5(mch_id&action&秘钥)

        $data['sign'] = $this->getSign($data,$channel['api_key']);

        Log::error('RfywPay :'.json_encode($data,true));


        $result = self::curlPost($channel['query_balance_url'], $data);
        
        Log::error('RfywPay :'.$result);

        $result = json_decode($result,true);

        if ($result['errCode'] != 0){

        }else{
            Db::name('daifu_orders_transfer')->where('id',$channel['id'])->update(['balance' => $result['data']['Money']]);
        }
    }

}