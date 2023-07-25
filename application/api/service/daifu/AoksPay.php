<?php

namespace app\api\service\daifu;
use app\api\service\DaifuPayment;
use think\Db;
use think\Log;
class AoksPay extends DaifuPayment
{
    public function pay($params){

        // amount,outOrderNum,mchNum,timestamp,account,accountName,bankName,notifyUrl,sign

        $data = [
            'amount' => $params['amount'],
            'outOrderNum' => $params['out_trade_no'],
            'mchNum' => $this->config['merchant_id'],
            'timestamp' => time(),
            'account' => $params['bank_number'],
            'accountName' => $params['bank_owner'],
            'bankName' => $params['bank_name'],
            'notifyUrl' => $this->config['notify_url'],
        ];



        $data['sign'] = $this->getSign($data,$this->config['api_key']);
        

        Log::error('AoksPay Api Data :'.json_encode($data,true));


        $result = self::curlPost($this->config['api_url'], $data);

        Log::error('AoksPay Return Data :'.$result);

        $result = json_decode($result,true);

        if ($result['code'] != 200){
            return ['code'=>0,'msg'=>$result['msg']];
        }
        return ['code'=>1,'msg'=>$result['msg']];


    }


    private function getSign($data, $secret)
    {

        unset($data['bankName']);
        unset($data['notifyUrl']);

        $string_a = '';
        foreach ($data as $k => $v) {
            $string_a .= "{$k}={$v}&";
        }
//        $string_a = substr($string_a,0,strlen($string_a) - 1);
        //签名步骤三：MD5加密
        Log::error('AoksPay str :'.$string_a . 'key=' . $secret);
        $sign = md5($string_a . 'key=' . $secret);

        // 签名步骤四：所有字符转为大写
        $result = strtoupper($sign);

        return $result;
    }

    public function notify(){
        $notifyData = $_POST;
        Log::notice("Daifu AoksPay Notify post json Data". json_encode($notifyData));

        if ($notifyData['state'] == 'success'){
            echo "success";
            $data['out_trade_no'] = $notifyData['outOrderNum'];
            $data['error_reason'] = '';
            $data['status'] = 2;
            return $data;
        }else{
            echo "success";
            $data['out_trade_no'] = $notifyData['outOrderNum'];
            $data['error_reason'] = '代付失败';
            $data['status'] =1;
            return $data;
        }

    }


    public function balance($channel){

        $data = [
            'mchNum' => $channel['merchant_id'],
            'timestamp' => date('ymdhis',time()),
        ];

        // 	商户请求参数的签名串 MD5(mch_id&action&秘钥)

        $data['sign'] = $this->getSign($data,$channel['api_key']);

        Log::error('AoksPay :'.json_encode($data,true));


        $result = self::curlPost($channel['query_balance_url'], $data);
        
        Log::error('AoksPay :'.$result);

        $result = json_decode($result,true);

        if ($result['code'] != 200){

        }else{
            Db::name('daifu_orders_transfer')->where('id',$channel['id'])->update(['balance' => $result['attrData']['balance']]);
        }
    }

}