<?php

namespace app\api\service\daifu;
use app\api\service\DaifuPayment;
use think\Db;
use think\Log;
class BaiDaPay extends DaifuPayment
{
    public function pay($params){

        // mch_id,out_trade_no,money,pay_type,notify_url,bank_name,acct_name,acct_no,open_name,sign
        $data = [
            'mch_id' => $this->config['merchant_id'],
            'out_trade_no' => $params['out_trade_no'],
            'money' => sprintf("%.2f",$params['amount']),
            'pay_type' => '1',
            'notify_url' => $this->config['notify_url'],
            'bank_name' => $params['bank_name'],
            'acct_name' => $params['bank_owner'],
            'acct_no' => $params['bank_number'],
            'open_name' => $params['bank_name'],
        ];


        $string = $data['mch_id'] . '&' . $data['out_trade_no'] . '&' . $data['money'] . '&' . $data['notify_url'] . '&' . $this->config['api_key'];

        $data['sign']  = md5($string);



        Log::error($params['out_trade_no'].' ：BaiDaPay Api Data :'.json_encode($data,true));
        $headers = array(
            "Content-type: application/json;charset='utf-8'",
            "Accept: application/json",
        );

        $result = self::curlPost($this->config['api_url'], json_encode($data),[CURLOPT_HTTPHEADER=>$headers]);
        Log::error($params['out_trade_no'].' BaiDaPay Return Data :'.$result);


        $result = json_decode($result,true);

        if ($result['code'] != 0){
            return ['code'=>0,'msg'=>$result['message']];
        }
        return ['code'=>1,'msg'=>'提交成功'];


    }


    public function notify(){
        $input = file_get_contents("php://input");
        Log::notice("Daifu BaiDaPay Notify post json Data".$input);

        $notifyData = json_decode($input,true);
        if ($notifyData['state'] == 1){
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

    }


    public function balance($channel){

        $data = [
            'mch_id' => $channel['merchant_id'],
            'action' => 'balance'
        ];

        // 	商户请求参数的签名串 MD5(mch_id&action&秘钥)

        $data['sign'] = md5($data['mch_id'] . '&' . $data['action'] . '&' . $channel['api_key']);

        Log::error('BaiDaPay :'.json_encode($data,true));

        $headers = array(
            "Content-type: application/json;charset='utf-8'",
            "Accept: application/json",
        );

        $result = self::curlPost($channel['query_balance_url'], json_encode($data), [CURLOPT_HTTPHEADER=>$headers]);
        
        Log::error('BaiDaPay :'.$result);

        $result = json_decode($result,true);

        if ($result['code'] != 0){

        }else{
            Db::name('daifu_orders_transfer')->where('id',$channel['id'])->update(['balance' => $result['data']['balance']]);
        }
    }

}