<?php

namespace app\api\service\daifu;
use app\api\service\DaifuPayment;
use think\Db;
use think\Log;
class CashDfPay extends DaifuPayment
{
    public function pay($params,$dfChannel){
        $pay_secret_key = explode('|', $dfChannel['api_key']);
        $apikey = $pay_secret_key[0] ?? '';
        $aeskey = $pay_secret_key[1] ?? '';
//        $data = [
//            'mno' => $dfChannel['merchant_id'],
//        ];
        $content = [
            'mno' => $dfChannel['merchant_id'],
            'orderno' => $params['out_trade_no'],
            'amount' => sprintf("%.2f",$params['amount']) * 100,
            'pt_id' => '1',
            'bankname' => trim($params['bank_name']),
            'bank_address' => trim($params['bank_name']),
            'name' => trim($params['bank_owner']),
            'account' => trim($params['bank_number']),
            'async_notify_url' => $dfChannel['notify_url'],
        ];
        $content['sign'] = $this->getSign($content,$apikey);

        ksort($content);

        $plaintext = json_encode($content);

        $method = 'AES-128-ECB'; // AES-128-ECB 加密方式

        $ciphertext = openssl_encrypt($plaintext, $method, $aeskey, OPENSSL_RAW_DATA);

        $encoded = base64_encode($ciphertext);



        $data = [
            'mno' => $dfChannel['merchant_id'],
            'content' => $encoded
        ];
        Log::error('CashDfPayApiData :'.json_encode($data,true));
        $response = self::curlPost($dfChannel['api_url'],$data);
        Log::error('CreateCashDfPayReturnData:' . $response);
        $result = json_decode($response,true);
        if (!empty($result)){
            if ($result['code'] != 'success'){
                return ['code'=>0,'msg'=>$result['msg']];
            }
        }

        return ['code'=>1,'msg'=>$result['msg']];
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
        $sign = md5($string_a. 'key=' . $secret);

        return $sign;
    }

    public function notify(){
        $notifyData = $_POST;
        Log::notice('DaifuCashDfPayNotifyData:' . json_encode($notifyData,true));

//解密字符串
//        $encrypt = base64_decode($notifyData['content']);
//密钥
        $key = '0boUBSbk8h98Ct5W';
// 解密数据
        $decrypt = openssl_decrypt($notifyData['content'], 'AES-128-ECB', $key, 0);

        $res = json_decode($decrypt,true);
        if ($res['status'] == 1){
            echo "success";
            $data['out_trade_no'] = $notifyData['orderno'];
            $data['error_reason'] = '';
            $data['status'] = 2;
            return $data;
        }else{
            echo "success";
            $data['out_trade_no'] = $notifyData['orderno'];
            $data['error_reason'] = '出款失败';
            $data['status'] = 1;
            return $data;
        }

    }


    public function balance($channel){
        $pay_secret_key = explode('|', $channel['api_key']);
        $apikey = $pay_secret_key[0] ?? '';
        $aeskey = $pay_secret_key[1] ?? '';
            $content = [
                'mno' => $channel['merchant_id'],
                'pt_id' => '1',
            ];

            $content['sign'] = $this->getSign($content,$apikey);
        ksort($content);

        $plaintext = json_encode($content);

        $method = 'AES-128-ECB'; // AES-128-ECB 加密方式

        $ciphertext = openssl_encrypt($plaintext, $method, $aeskey, OPENSSL_RAW_DATA);

        $encoded = base64_encode($ciphertext);



        $data = [
            'mno' =>  $channel['merchant_id'],
            'content' => $encoded
        ];
        Log::error('CashDfPayBalanceApiData :'.json_encode($data,true));
        $response = self::curlPost($channel['query_balance_url'],$data);
        Log::error('CashDfPayBalanceReturnData :'.$response);

        $result = json_decode($response,true);

        if ($result['code'] != 'success'){

        }else{
            Db::name('daifu_orders_transfer')->where('id',$channel['id'])->update(['balance' => $result['data']['money']]);
        }
    }



}