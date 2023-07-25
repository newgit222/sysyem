<?php

namespace app\api\service\payment;
//use AlipayF2FPayService;
use app\api\service\ApiPayment;
use app\common\library\exception\OrderException;
use app\common\logic\EwmOrder;
use app\common\logic\Orders;
use app\common\model\Config;
use AopClient;
use app\api\logic\AlipayWapNotifyService;
use think\Db;
use think\Log;
use think\Request;
use app\api\logic\AlipayWapService;

class AlipayWapPay extends ApiPayment
{

    /**
     * var string $secret_key 加解密的密钥
     */
    protected $secret_key  = '8e70f72bc3f53b12';

    /**
     * var string $iv 加解密的向量，有些方法需要设置比如CBC
     */
    protected $iv = '99b538370c7729c7';

    /**
     * 统一下单
     */
    private function pay($params, $type, $is_bzk = false,$codes='kzk')
    {

        //直接出码取得码的信息
        $money = sprintf('%.2f', $params['amount']);
        $EwmOrderLogic = new EwmOrder();
        $configModel = new Config();
        $userModel = new \app\common\model\User();
        $user = $userModel->where(['uid'=>$params['uid']])->find();
//        Log::error('接口商户：'.json_encode($user,true));
        $response = $EwmOrderLogic->createOrder($money, $params['trade_no'], $type, $params['out_trade_no'], $user['admin_id'], $this->config['notify_url'],$user['pao_ms_ids'],$params['body'],0,$user['uid']);
        if ($response['code'] != 1) {
            Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$response['msg']]);
            Log::error('Create GumaV2Pay API Error:' . ($response['msg'] ? $response['msg'] : ""));
            throw new OrderException([
                'msg' => 'Create GumaV2Pay API Error:' . ($response['msg'] ? $response['msg'] : ""),
                'errCode' => 200009
            ]);
        }
        $code = $response['data']['code'];
        $order = Db::name('ewm_order')->where('order_no',$params['trade_no'])->find();

        $pay_url = $this->alipayWapPay($params['trade_no'],$order,$code,'http://43.228.69.11/alipayNotify.php',$user['admin_id']);
//        Log::error('alipaywap: '.json_encode($pay_url,true));
//        print_r($pay_url);die;
//        if ($pay_url['code'] == 1){
//            return $zhongzhuan_url.'alipayF2F.php?'. http_build_query($data);
//        }else{
//            Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$pay_url['result']['msg']]);
//            throw new OrderException([
//                'msg' => 'Create GumaV2Pay API Error: 当面付接口返回：'.$pay_url['result']['msg'],
//                'errCode' => 200009
//            ]);
//        }
//        $zhongzhuan_url = 'http://zz.sxwzrj.cn/';
//        $ret = self::curlPost($zhongzhuan_url.'alipayWap.php', $data);

        return 'https://openapi.alipay.com/gateway.do?'. http_build_query($pay_url);

    }

    /**
     * 生成签名
     * @param $args
     * @return string
     */
    protected function getSign($args)
    {
        ksort($args);
        $mab = '';
        foreach ($args as $k => $v) {
            if ($k == 'sign' || $k == 'key' || $v == '') {
                continue;
            }
            $mab .= $k . '=' . $v . '&';
        }
        $mab .= 'key=' . $args['key'];
        return md5($mab);
    }

    public function alipayWap($params)
    {
        $codeType = Db::name('pay_code')->where('code','alipayWap')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }

    private function alipayWapPay($orderNo,$orderInfo,$apiData,$notify,$admin_id){
        /*** 获取通道的签名参数 end ***/
        $orderName = getAdminPayCodeSys('order_reason',57,$admin_id);
        if (empty($orderName)){
            $order_reason = '商城商品-'.$orderNo;
        }else{
            $orderName = explode(',',$orderName);
            $orderNameKey = rand(0, count($orderName)-1);
            $order_reason =$orderName[$orderNameKey].'-订单号：'.$orderNo;
        }
//        array_rand($a,1)
        $signData = [
            'appid'        => $apiData['account_number'],
            'returnUrl'     => $this->config['return_url'],
            'notifyUrl' => $notify,
            'outTradeNo'       => $orderNo,
            'payAmount' => $orderInfo['order_pay_price'],
            'orderName'  => $order_reason,
            'rsaPrivateKey'      => $apiData['privateKey'],
        ];
   //     Log::error('data :'.json_encode($signData,320));
        //p($signData);
//        vendor('alipay.AlipayWapService');
        //调用接口
        $aliPay = (new AlipayWapService());
        $aliPay->setAppid($signData['appid']);
        $aliPay->setReturnUrl($signData['returnUrl']);
        $aliPay->setNotifyUrl($signData['notifyUrl']);
        $aliPay->setRsaPrivateKey($signData['rsaPrivateKey']);
        $aliPay->setTotalFee($signData['payAmount']);
        $aliPay->setOutTradeNo($signData['outTradeNo']);
        $aliPay->setOrderName($signData['orderName']);
        $sHtml = $aliPay->doPay();
        //p($sHtml);
        return $sHtml;

    }

    private function encrypt($data)
    {
        return base64_encode(openssl_encrypt($data,"AES-128-CBC",$this->secret_key,true,$this->iv));

    }






}