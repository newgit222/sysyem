<?php

namespace app\api\service\daifu;
use app\api\service\DaifuPayment;
use think\Db;
use think\Log;
class EDaifuPay extends DaifuPayment
{
    public function pay($params,$dfChannel){

        // merchantUUID,amount,channelCode,productType,currency,approveType,merchantOrderNo,notifyUrl,remark,extInfo.bankName,extInfo.fullName,extInfo.bankcardNumber,sign

        $extInfo = [
            'bankName' => $params['bank_name'],
            'fullName' => $params['bank_owner'],
            'bankcardNumber' => $params['bank_number'],
        ];

        $data = [
            'merchantUUID' => $this->config['merchant_id'],
            'amount' => sprintf("%.2f",$params['amount']),
            'channelCode' => 'EDF',
            'productType' => 'chn.bank.payout',
            'currency' => 'CNY',
            'approveType'=> '2',
            'merchantOrderNo' => $params['out_trade_no'],
            'notifyUrl' => $this->config['notify_url'],
            'remark' => 'remark',
        ];

        $data['sign'] = $this->getSign($data,$this->config['api_key']);
        $data['extInfo'] =$extInfo;
        Log::error('EDaifuPayApiData :'.json_encode($data,true));
        $headers = array(
            "Content-type: application/json;charset='utf-8'",
            "Accept: application/json",
        );


//        halt(json_encode($data));

        $result = self::curlPost($this->config['api_url'], json_encode($data),[CURLOPT_HTTPHEADER=>$headers]);
        Log::error('EDaifuPayReturnData :'.$result);


        $result = json_decode($result,true);

        if ($result['code'] != 0){
            return ['code'=>0,'msg'=>$result['msg']];
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
        Log::error('EDaifuPaysignstr :'.$string_a . 'key=' . $secret);
        $sign = md5($string_a . 'key=' . $secret);

        // 签名步骤四：所有字符转为大写
        $result = strtoupper($sign);

        return $result;
    }

    public function notify(){
        $input = file_get_contents("php://input");
        Log::notice("DaifuEDaifuPayNotifypostjsonData".$input);

        $notifyData = json_decode($input,true);
        if ($notifyData['status'] == 4){
            echo "SUCCESS";
            $data['out_trade_no'] = $notifyData['merchantOrderNo'];
            $data['error_reason'] = '';
            $data['status'] = 2;
            return $data;
        }else{
            echo "SUCCESS";
            $data['out_trade_no'] = $notifyData['merchantOrderNo'];
            $data['error_reason'] = '代付失败';
            $data['status'] =1;
            return $data;
        }

    }


    public function balance($channel){

        $data = [
            'merchantUUID' => $channel['merchant_id'],
        ];

        // 	商户请求参数的签名串 MD5(mch_id&action&秘钥)

        $data['sign'] = $this->getSign($data,$channel['api_key']);

        Log::error('EDaifuPayBalanceApiData :'.json_encode($data,true));

        $headers = array(
            "Content-type: application/json;charset='utf-8'",
            "Accept: application/json",
        );

        $result = self::curlPost($channel['query_balance_url'], json_encode($data), [CURLOPT_HTTPHEADER=>$headers]);
        
        Log::error('EDaifuPayBalanceReturnData :'.$result);

        $result = json_decode($result,true);

        if ( $result['code'] != 0){

        }else{
            Db::name('daifu_orders_transfer')->where('id',$channel['id'])->update(['balance' => $result['data']['backupBalance']]);
        }
    }

}