<?php

namespace app\api\service\daifu;
use app\api\service\DaifuPayment;
use think\Db;
use think\Log;
class LaBiPay extends DaifuPayment
{
    public function pay($params,$dfChannel){

        $list = [
            [
                'out_trade_no' => $params['out_trade_no'],
                'amount' => $params['amount'],
                'accountname' => $params['bank_owner'],
                'bankname' => $params['bank_name'],
                'cardnumber' => $params['bank_number'],
                'subbranch' => '',
                'province' => '',
                'city' => '',
                'mobile' => '',
                'attach' => ''
            ]
        ];

        $list = json_encode($list);

        $data = [
            'mchid' => $dfChannel['merchant_id'],
            'addtime' => time(),
            'bankcode' => 'unionpay',
            'list' =>$list,
            'callback_url' => $dfChannel['notify_url'],
        ];

        $data['sign'] = $this->getSign($data,$dfChannel['api_key']);

        Log::error('LaBiPay Api Data :'.json_encode($data,true));


//        halt($dfChannel['api_url']);

        $result = self::curlPost($dfChannel['api_url'], $data);
        Log::error('LaBiPay Return Data :'.$result);


        $result = json_decode($result,true);


    if (!empty($result)){
        if ($result['data']['list'][0]['status'] != 1){
            return ['code'=>0,'msg'=>$result['msg']];
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

        //签名步骤三：MD5加密
        Log::error('LaBiPaysign str :'.$string_a . 'key=' . $secret);
        $sign = md5($string_a . 'key=' . $secret);

        // 签名步骤四：所有字符转为大写
        $result = strtoupper($sign);

        return $result;
    }

    public function notify(){
//        $input = file_get_contents("php://input");
//        Log::notice("Daifu ChangEDfPay Notify post json Data".$input);
        $notifyData = $_POST;
        Log::notice('DaifuLaBiPayNotifypostformData:' . json_encode($notifyData,true));
//        merchant_no=96376701&
//        order_amount=100.00&
//        order_no=2301311637154051&status=6
//        &sign=5122afc6b1c73dedb15e32e152ac82c2&attach=attach&platform_no=2023013117124636680023999&reason=

//        $notifyData = json_decode($input,true);
        if ($notifyData['status'] == 2){
            echo "success";
            $data['out_trade_no'] = $notifyData['out_trade_no'];
            $data['error_reason'] = '';
            $data['status'] = 2;
            return $data;
        }else{
            echo "success";
            $data['out_trade_no'] = $notifyData['out_trade_no'];
            $data['error_reason'] = '代付失败';
            $data['status'] =1;
            return $data;
        }
//        echo "Error";
//        Log::error('Daifu OgDfPay Notify Error Data:' . json_encode($notifyData,true));
    }



    public function balance($channel){
        $data = [
            'mchid' => $channel['merchant_id'],
            'timestamp' => time(),
        ];

        $data['sign'] = $this->getSign($data,$channel['api_key']);
        Log::error('LaBiPaybalancePaybalanceApiData :'.json_encode($data,true));
        $result = self::curlPost($channel['query_balance_url'], $data);
        Log::error('LaBiPaybalancePaybalanceReturnData :'.$result);


        $result = json_decode($result,true);

        if ($result['status'] != 1){

        }else{
            Db::name('daifu_orders_transfer')->where('id',$channel['id'])->update(['balance' => $result['data']['balance']]);
        }


    }

}