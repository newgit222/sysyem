<?php

namespace app\api\service\payment;
use app\api\logic\AlipayF2FPayService;
use app\api\service\ApiPayment;
use app\common\library\exception\OrderException;
use app\common\logic\EwmOrder;
use app\common\logic\Orders;
use app\common\model\Config;
use alipay\aop\AopClient;
use think\Db;
use think\Log;
use think\Request;

class AlipayF2FPay extends ApiPayment
{

    /**
     * var string $secret_key 加解密的密钥
     */
    protected $secret_key  = '8e70f72bc3f53b12';

    /**
     * var string $iv 加解密的向量，有些方法需要设置比如CBC
     */
    protected $iv = '99b538370c7729c7';



    private function encrypt($data)
    {
        return base64_encode(openssl_encrypt($data,"AES-128-CBC",$this->secret_key,true,$this->iv));

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
        $data['is_bzk'] = $is_bzk;
        $data['account_name'] = $this->encrypt($code['account_name']);
        $data['bank_name'] = $this->encrypt($code['bank_name']);
        $data['account_number'] = $this->encrypt($code['account_number']);
        $data['trade_no'] = $params['trade_no'];
        $data['order_pay_price'] = $response['data']['money'];
        $data['key'] = config('inner_transfer_secret');
        $data['sign'] = $this->getSign($data);
        $data['user'] = $this->encrypt($_SERVER['HTTP_HOST']);
        $data['remark'] = $order['id'];
        unset($data['key']);
        $zhongzhuan = getAdminPayCodeSys('cashier_address',256,$user['admin_id']);
        if (empty($zhongzhuan)){
            $zhongzhuan = 0;
        }

        $zhongzhuanconfig = require_once('./data/conf/zhongzhuan.php');
        if($zhongzhuan == 1){
            $zhongzhuan_url = $zhongzhuanconfig['gaofang_197'];
        }elseif ($zhongzhuan == 2){
            $zhongzhuan_url = $zhongzhuanconfig['gaofang_119'];
        }elseif ($zhongzhuan == 3){
            $zhongzhuan_url = $zhongzhuanconfig['cf_aliyun_hangkang_85'];
        }elseif ($zhongzhuan == 4){
            $zhongzhuan_url = $zhongzhuanconfig['cf_tengcent_hangkang_91'];
        }elseif ($zhongzhuan == 5){
            $zhongzhuan_url = $zhongzhuanconfig['gaofang_cf56'];
        }else{
            $zhongzhuan_url = $zhongzhuanconfig['cf_aliyun_hangkang_85'];
        }
//        $zhongzhuan_url = 'http://zz.sxwzrj.cn/';
        $pay_url = $this->alipayF2FPay($params['trade_no'],$order,$code,'http://'.$_SERVER["HTTP_HOST"].'/api/pay/alipayNotify');
        if ($pay_url['code'] == 1){
            $data['url'] = $this->encrypt($pay_url['result']['qr_code']);
            return $zhongzhuan_url.'alipayF2F.php?'. http_build_query($data);
        }else{
            Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$pay_url['result']['msg']]);
            throw new OrderException([
                'msg' => 'Create GumaV2Pay API Error: 当面付接口返回：'.$pay_url['msg'],
                'errCode' => 200009
            ]);
        }


    }

    public function alipayF2F($params)
    {
        $codeType = Db::name('pay_code')->where('code','alipayF2F')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }

    private function alipayF2FPay($orderNo,$orderInfo,$apiData,$notify){
        /*** 获取通道的签名参数 end ***/

        $f2fpayService=new AlipayF2FPayService();


        /*** 配置开始 ***/
        $f2fpayService->setAppid($apiData['account_number']);//https://open.alipay.com 账户中心->密钥管理->开放平台密钥，填写添加了电脑网站支付的应用的APPID
        $f2fpayService->setNotifyUrl($notify); //付款成功后的异步回调地址
        //商户私钥，填写对应签名算法类型的私钥，如何生成密钥参考：https://docs.open.alipay.com/291/105971和https://docs.open.alipay.com/200/105310
        $f2fpayService->setRsaPrivateKey($apiData['privateKey']);
        $f2fpayService->setTotalFee($orderInfo['order_pay_price']);//付款金额，单位:元
        $f2fpayService->setOutTradeNo($orderNo);//你自己的商品订单号，不能重复
        $f2fpayService->setOrderName('商品-'.$orderNo);//订单标题
        /*** 配置结束 ***/

        //var_dump($f2fpayService);die;
        //调用接口
        $result = $f2fpayService->doPay();
        $result = $result['alipay_trade_precreate_response'];
        //var_dump($result);die;
        Log::error('f2fpay return data : '. json_encode($result));
        if(!empty($result['code']) && $result['code'] && $result['code']=='10000') {
            //请求成功
            return ['code'=>1,'msg'=>'请求成功','result'=>$result];
        }
        return ['code'=>0,'msg'=>'请求失败','result'=>$result];
    }


}