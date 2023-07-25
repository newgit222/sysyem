<?php /*reated by PhpStorm.
 * User: zhangxiaohei
 * Date: 2020/2/10
 * Time: 20:40
 */

namespace app\api\controller;


use app\common\controller\DaifuBaseApi;
use app\common\logic\DaifuOrders;
use think\Exception;
use think\Log;
use think\Request;

class Dfpay2 extends DaifuBaseApi
{
    protected $verification = ['pay'];

    protected $queryUrl = [
	'http://txdev-third.9uymfl.com/thirdwithdraw/withdraw/verify',
        'https://callback.jptxcallbackac.com/thirdwithdraw/withdraw/verify',
        'https://callback.jptxcallbackab.com/thirdwithdraw/withdraw/verify',
        'https://callback.jptxcallbackbc.com/thirdwithdraw/withdraw/verify',
        'http://txdev-third.9uymfl.com/thirdwithdraw/withdraw/verify',
        'https://callback.jiantxcallbackac.com/thirdwithdraw/withdraw/verify',
        'https://callback.jiantxcallbackbc.com/thirdwithdraw/withdraw/verify',
        'https://callback.jiantxcallbackab.com/thirdwithdraw/withdraw/verify',
        'https://callback.jptxcallbackab.com/thirdwithdraw/withdraw/verify',
        'https://callback.jptxcallbackbc.com/thirdwithdraw/withdraw/verify',
        'https://callback.jptxcallbackac.com/thirdwithdraw/withdraw/verify',
        'http://txdev-third.9uymfl.com/thirdwithdraw/withdraw/verify',
        'https://callback.jptxcallbackac.com/thirdwithdraw/withdraw/verify',
        'https://callback.skgtxcallbackac.com/thirdwithdraw/withdraw/verify',
        'https://callback.skgtxcallbackbc.com/thirdwithdraw/withdraw/verify',
        'https://callback.skgtxcallbackab.com/thirdwithdraw/withdraw/verify',
        'http://txdev-third.9uymfl.com/thirdwithdraw/withdraw/verify',
        'https://callback.jptxcallbackac.com/thirdwithdraw/withdraw/verify',
        'https://callback.jptxcallbackab.com/thirdwithdraw/withdraw/verify',
        'https://callback.jptxcallbackbc.com/thirdwithdraw/withdraw/verify',
        'https://api.twwalletapigatewaybc.com/third/thirdwithdraw/withdraw/verify',
        'https://api.twwalletapigatewayac.com/third/thirdwithdraw/withdraw/verify',
        'https://api.twwalletapigatewayab.com/third/thirdwithdraw/withdraw/verify',
        'http://walletfat-api-gateway.4771m1.com/third/thirdwithdraw/withdraw/verify',
        'http://walletfat-api-gateway.4771m1.com/thirdwithdraw/withdraworder/query',
        'https://api.twwalletapigatewayab.com/thirdwithdraw/withdraworder/query',
        'https://api.twwalletapigatewayac.com/thirdwithdraw/withdraworder/query',
        'https://api.twwalletapigatewaybc.com/thirdwithdraw/withdraworder/query',
        'http://walletfat-api-gateway.4771m1.com/third/thirdwithdraw/withdraw/verify',
        'https://api.twwalletapigatewayab.com/third/thirdwithdraw/withdraw/verify',
        'https://api.twwalletapigatewayac.com/third/thirdwithdraw/withdraw/verify',
        'http://walletfat-api-gateway.ftgyhujiko5100.com/third/thirdwithdraw/withdraw/verify',
        'https://api.twwalletapigatewayab.com/third/thirdwithdraw/withdraw/verify',
        'http://txdev-third.cfvgbhnjmk5100.com/thirdwithdraw/withdraw/verify',
    ];
    /**
     * 代付
     */
    public function pay2(Request $request)
    {
        if (!$request->isPost()){
            $this->error('Request format error');
        }
        $DaifuOrders = new DaifuOrders();
        if(!empty($_POST['withdrawQueryUrl']))
        {
            Log::error($_POST['out_trade_no']."：fancha kaishi");
            $data = [
                'merchantId' => $_POST['mchid'], //商家号
                'money' => $_POST['amount'], //金额
                'orderNo' => $_POST['out_trade_no'], //订单号
                'token' =>  $_POST['callToken'], //Token，由系统产生并传送
                'target' => $_POST['bank_number'], //钱包地址或银行卡号
                'ownerName' => $_POST['bank_owner'], //选填 提款人姓名，银行卡要，虚拟币不需要
            ];
            if (in_array($_POST['withdrawQueryUrl'],$this->queryUrl)){
                $result = self::curl($_POST['withdrawQueryUrl'],$data);
                Log::error($_POST['out_trade_no'].':反查返回：'.$result);
                $result = json_decode($result,true);
                if ($result['status'] != 1){
                    Log::error($_POST['out_trade_no']."：fancha shibai ". json_encode($result));
                    return json_encode($result,true);

                }
            }
            unset($_POST['callToken']);
            unset($_POST['withdrawQueryUrl']);

        }
//        Log::error($_POST['out_trade_no']."：走反查接口，没提交反查地址");
        //  unset($_POST['callToken']);
        //  unset($_POST['withdrawQueryUrl']);
//      unset($_POST['']);


        $result = $DaifuOrders->createOrder($_POST);
        if ($result['code'] != '1') {
            $this->error($result['msg']);
        }
        $this->success('请求成功', null, $result['data']);
    }



    public function checkOrderNoV2(Request $request){
        if (!$request->isPost()){
            $this->error('Request format error');
        }

        $data = $request->post();
        if (empty($data['mchid']) || empty($data['amount']) || empty($data['bank_owner']) || empty($data['bank_number']) || empty($data['out_trade_no'])){
            $this->error('参数不足！');
        }
        $checkOrderNoMchId = $data['mchid'];
        $remitId = 'xiongmaodaifu2';
        $checkOrderNoMd5Key = '0daf89e8aee6da547e0664c2edddd7da';
        $checkOrderNoAesKey = '26zeNUvoDqoqDlwXKGYQf6wtG3vt6gRM';
        $apiUrl= 'http://enoremit.agcjxnow.com/remit_platform/remit/v2/checkOrderNo/protection';
        $sign = md5('amount='.$data['amount']
            .'&cardName='.$data['bank_owner']
            .'&cardNumber='.$data['bank_number']
            .'&merchantNo='.$checkOrderNoMchId
            .'&orderNo='.$data['out_trade_no']
            .'&remitId='.$remitId
            .'&key='.$checkOrderNoMd5Key);

        $checkOrderData = [
            'sign' => $sign,
            'orderNo'=>$data['out_trade_no'],
            'amount'=>$data['amount'],
            'cardName'=>$data['bank_owner'],
            'cardNumber'=>$data['bank_number'],
        ];
        $checkOrderDataJson = json_encode($checkOrderData,true);

        $AesData = $this->encrypt($checkOrderDataJson,$checkOrderNoAesKey);

//        $apiData = [
//            'body' => $AesData
//        ];
        $result = $this->send_post_json($apiUrl,$AesData,$checkOrderNoMchId,$remitId);
        $result = $this->decrypt($result,$checkOrderNoAesKey);
        Log::error($data['out_trade_no'].'反查返回内容：'.$result);

        $result = json_decode($result,true);
        if(isset($result['status']) && $result['status']=='true'){
            $DaifuOrders = new DaifuOrders();
            $result = $DaifuOrders->createOrder($data);
            if ($result['code'] != '1') {
                $this->error($result['msg']);
            }
            $this->success('请求成功', null, $result['data']);
        }


        $this->error('反查失败');



    }



    private function curl($url,array $data)
    {
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    private function encrypt($input,$key)
    {
        return base64_encode(openssl_encrypt($input, 'AES-256-ECB',$key, OPENSSL_RAW_DATA));
    }
    private function decrypt($input,$key)
    {
        return openssl_decrypt(base64_decode($input), 'AES-256-ECB', $key, OPENSSL_RAW_DATA);

    }



    private function send_post_json($url, $jsonStr,$merchantNo,$remitId)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($jsonStr),
                'merchantNo: ' . $merchantNo,
                'remitId: '. $remitId
            )
        );
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $response;
    }

}
