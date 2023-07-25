<?php

namespace app\api\service\daifu;
use app\api\service\DaifuPayment;
use think\Db;
use think\Log;
class ShangHePay extends DaifuPayment
{
    public function pay($params){

        //mchid,out_trade_no,money,bankname,subbranch,accountname,cardnumber,notifyurl,pay_md5sign

        $data = [
            'mchid' => $this->config['merchant_id'],
            'out_trade_no' => $params['out_trade_no'],
            'money' => sprintf("%.2f",$params['amount']),
            'bankname' => $params['bank_name'],
            'subbranch' => $params['bank_name'],
            'accountname' => $params['bank_owner'],
            'cardnumber' => $params['bank_number'],
            'notifyurl' => $this->config['notify_url'],
        ];

     
        $data['pay_md5sign'] = $this->getSign($data,$this->config['api_key']);
        Log::error('ShangHePay Api Data :'.json_encode($data,true));


        $result = self::curlPost($this->config['api_url'], $data);
        Log::error('ShangHePay Return Data :'.$result);


        $result = json_decode($result,true);

        if ($result['status'] != 'success'){
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
        Log::error('ShangHePay sign str :'.$string_a . 'key=' . $secret);
        $sign = md5($string_a . 'key=' . $secret);

        // 签名步骤四：所有字符转为大写
       $result = strtoupper($sign);

        return $result;
    }


    public function notify(){
        $notifyData = $_POST;
        Log::notice('Daifu ShangHePay Notify post form Data:' . json_encode($notifyData,true));

        if ($notifyData['returncode'] == 2){
            echo "OK";
            $data['out_trade_no'] = $notifyData['orderid'];
            $data['error_reason'] = '';
            $data['status'] = 2;
            return $data;
        }else{
            echo "OK";
            $data['out_trade_no'] = $notifyData['orderid'];
            $data['error_reason'] = '代付失败';
            $data['status'] =1;
            return $data;
        }

    }


    public function balance($channel){
            $data = [
                'mchid' =>  $channel['merchant_id'],
            ];

            $data['pay_md5sign'] = $this->getSign($data,$channel['api_key']);
            $result = self::curlPost($channel['query_balance_url'],$data);
            Log::error('ShangHePayBalanceReturn Data :'.$result);
            $result = json_decode($result,true);
            
            if ($result['status'] != 'success'){

            }else{
                Db::name('daifu_orders_transfer')->where('id',$channel['id'])->update(['balance' => $result['balance']]);
            }


    }


}