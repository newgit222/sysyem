<?php
/**
 * Created by PhpStorm.
 * User: zhangxiaohei
 * Date: 2020/5/1
 * Time: 1:19
 */

namespace app\api\service\payment;


use app\api\service\ApiPayment;
use app\common\library\exception\OrderException;
use app\common\logic\EwmOrder;
use app\common\logic\Orders;
use app\common\model\Config;
use think\Db;
use think\Log;
use think\Request;


/**
 * 跑分二维码支付
 * Class GumaV2Pay
 * @package app\api\service\payment
 */
class GumaV2Pay extends ApiPayment
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
        Log::error(date('Y-m-d H:i:s',time()).':创建订单开始：'.$params['out_trade_no']);

        $team_ids = isset($user['team_ids']) ? $user['team_ids'] : 0;
        $response = $EwmOrderLogic->createOrder($money, $params['trade_no'], $type, $params['out_trade_no'], $user['admin_id'], $this->config['notify_url'],$user['pao_ms_ids'],$params['body'],$team_ids,$user['uid']);
        Log::error(date('Y-m-d H:i:s',time()).':创建订单结束：'.$params['out_trade_no']);
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
        $is_pay_name = getAdminPayCodeSys('is_pay_name ',$type,$user['admin_id']);
        if (empty($is_pay_name)){
            $data['is_pay_name'] = 1;
        }else{
            $data['is_pay_name'] = $is_pay_name;
        }
        unset($data['key']);
        $exclusive_transfer = getAdminPayCodeSys('exclusive_transfer',256,$user['admin_id']);
        if (empty($exclusive_transfer)){
            $zhongzhuan = getAdminPayCodeSys('cashier_address',256,$user['admin_id']);
            if (empty($zhongzhuan)){
                $zhongzhuan = 1;
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
            }elseif ($zhongzhuan == 6){
                $zhongzhuan_url = $zhongzhuanconfig['gaofang_66'];
            }elseif ($zhongzhuan == 7){
                $zhongzhuan_url = $zhongzhuanconfig['gaofang_16'];
            }elseif ($zhongzhuan == 8){
                $zhongzhuan_url = $zhongzhuanconfig['ziyun_1104'];
            }else{
                $zhongzhuan_url = $zhongzhuanconfig['cf_aliyun_hangkang_85'];
            }
        }else{
            $zhongzhuan_url = $exclusive_transfer;
        }
//        $zhongzhuan_url = 'http://175.24.200.93/';
        if ($codes == 'alipayUid' || $codes == 'alipayUidSmall') {
//            $uidPayUrl = db('config')->where(['name' => 'thrid_url_uid'])->value('value');
//            $url = 'https://www.alipay.com/?appId=20000123&actionType=scan&biz_data={"s":"money","u":"'.$code['account_number'].'","a":"'.$data['order_pay_price'].'","m":"商城购物'.$params['trade_no'].'"}';
//            $data['gourl'] = 'alipays://platformapi/startapp?appId=68687093&url='.urlencode($url);
            $is_h5= getAdminPayCodeSys('is_h5',$type,$user['admin_id']);
            if (empty($is_h5)){
                $is_h5 = 2;
            }
            $data['is_h5'] = $is_h5;
            $is_show_name= getAdminPayCodeSys('is_show_name',$type,$user['admin_id']);
            if (empty($is_show_name)){
                $is_show_name = 2;
            }
            $data['is_show_name'] = $is_show_name;
            Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'uid.php?'. http_build_query($data)]);
            return $zhongzhuan_url.'uid.php?'. http_build_query($data);
        }elseif ($codes == 'alipayUidTransfer'){
            Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'uidtransfer.php?'. http_build_query($data)]);
            return $zhongzhuan_url.'uidtransfer.php?'. http_build_query($data);
	}elseif ($codes == 'taoBaoDirectPay'){
	    $pay_template = getAdminPayCodeSys('pay_template',$type,$user['admin_id']);
            if(empty($pay_template) || $pay_template == 1){
                $pay_template = '';
	    }else
	    {
	       $pay_template = '-'. $pay_template;
	    }
            
            $data['qr_img'] = $this->encrypt($code['image_url']);
            Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'taoBaoDirectPay'.$pay_template.'.php?'. http_build_query($data)]);
            return $zhongzhuan_url.'taoBaoDirectPay'.$pay_template.'.php?'. http_build_query($data);
        }elseif ($codes == 'alipayCode' || $codes == 'alipayCodeSmall' || $codes == 'QianxinTransfer' || $codes == 'taoBaoMoneyRed'){
            if ($code['account_type'] == 2){
                $data['account_number'] = $code['account_number'];
                Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'alipayCodeImg.php?'. http_build_query($data)]);
                return $zhongzhuan_url.'alipayCodeImg.php?'. http_build_query($data);
            }
            $pay_template = getAdminPayCodeSys('pay_template',$type,$user['admin_id']);
            if(empty($pay_template)){
                $pay_template = 1;
            }
            $initialization_h5 = getAdminPayCodeSys('initialization_h5',$type,$user['admin_id']);
            if (empty($initialization_h5)){
                $initialization_h5 = 2;
            }

            if($pay_template == 1){
                $data['initialization_h5'] = $initialization_h5;
                Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'alipaycode.php?'. http_build_query($data)]);
                return $zhongzhuan_url.'alipaycode.php?'. http_build_query($data);
            }elseif($pay_template == 2){
                $data['initialization_h5'] = $initialization_h5;
                Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'alipayCode-2.php?'. http_build_query($data)]);
                return $zhongzhuan_url.'alipayCode-2.php?'. http_build_query($data);
            }else{
                $data['initialization_h5'] = $initialization_h5;
                Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'alipayCode-3.php?'. http_build_query($data)]);
                return $zhongzhuan_url.'alipayCode-3.php?'. http_build_query($data);
            }

        }elseif ($codes == 'douyinGroupRed'){
                $data['ewm'] = $code['account_number'];
                $data['qr_img'] = $this->encrypt($code['image_url']);
            Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'douyinGroupRed.php?'. http_build_query($data)]);
                return $zhongzhuan_url.'douyinGroupRed.php?'. http_build_query($data);
        }elseif ($codes == 'wechatCode'){
            if ($code['account_type'] == 2){
                $data['account_number'] = $code['account_number'];
                Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'wechatCodeImg.php?'. http_build_query($data)]);
                return $zhongzhuan_url.'wechatCodeImg.php?'. http_build_query($data);
            }else{
                Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'wechatCode.php?'. http_build_query($data)]);
                $pay_template = getAdminPayCodeSys('pay_template',42,$user['admin_id']);
                if (empty($pay_template)){
                    $pay_template = 1;
                }
                if ($pay_template == 1){
                    return $zhongzhuan_url.'wechatCode.php?'. http_build_query($data);
                }else{
                    return $zhongzhuan_url.'wechatCode-2.php?'. http_build_query($data);
                }
            }
//            $data['qr_img'] = $this->encrypt($_SERVER['HTTP_HOST'].$code['image_url']);


        }elseif ($codes == 'wechatGroupRed'){
            $data['qr_img'] = $this->encrypt($code['image_url']);
            Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'wechatGroupRed.php?'. http_build_query($data)]);
            return $zhongzhuan_url.'wechatGroupRed.php?'. http_build_query($data);
        }elseif ($codes == 'DingDingGroup'){
            $data['qr_img'] = $this->encrypt($code['image_url']);
            Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'DingDingGroup.php?'. http_build_query($data)]);
            return $zhongzhuan_url.'DingDingGroup.php?'. http_build_query($data);
        }elseif ($codes == 'JDECard'){
            Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'JDECard.php?'. http_build_query($data)]);
            return $zhongzhuan_url.'JDECard.php?'. http_build_query($data);
        }elseif ($codes == 'CnyNumber'){
            Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'CnyNumber.php?'. http_build_query($data)]);
            return $zhongzhuan_url.'CnyNumber.php?'. http_build_query($data);
        }elseif ($codes == 'alipayPassRed'){
            Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'alipayPassRed.php?'. http_build_query($data)]);
            return $zhongzhuan_url.'alipayPassRed.php?'. http_build_query($data);
        }elseif ($codes == 'alipayTransfer'){
            Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'alipayTransfer.php?'. http_build_query($data)]);
            return $zhongzhuan_url.'alipayTransfer.php?'. http_build_query($data);
        }elseif ($codes == 'JunWeb'){
            Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'JunWeb.php?'. http_build_query($data)]);
            return $zhongzhuan_url.'JunWeb.php?'. http_build_query($data);
        }elseif ($codes == 'HuiYuanYiFuKa'){
            Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'HuiYuanYiFuKa.php?'. http_build_query($data)]);
            return $zhongzhuan_url.'HuiYuanYiFuKa.php?'. http_build_query($data);
        }elseif ($codes == 'AppletProducts'){
            $data['qr_img'] = $code['image_url'];
            Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'AppletProducts.php?'. http_build_query($data)]);
            return $zhongzhuan_url.'AppletProducts.php?'. http_build_query($data);
        }elseif ($codes == 'QQFaceRed'){
            Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'QQFaceRed.php?'. http_build_query($data)]);
            return $zhongzhuan_url.'QQFaceRed.php?'. http_build_query($data);
        }elseif ($codes == 'alipaySmallPurse'){
            $data['qr_img'] = $this->encrypt($code['image_url']);
            Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'alipaySmallPurse.php?'. http_build_query($data)]);
            return $zhongzhuan_url.'alipaySmallPurse.php?'. http_build_query($data);
        }
//        elseif ($codes == 'taoBaoMoneyRed'){
//            Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'taoBaoMoneyRed.php?'. http_build_query($data)]);
//            return $zhongzhuan_url.'taoBaoMoneyRed.php?'. http_build_query($data);
//        }
        elseif ($codes == 'alipayTransferCode'){
            $is_lock_code = getAdminPayCodeSys('is_amount_lock',$type,$user['admin_id']);
            if (empty($is_lock_code)){
                $is_lock_code = 1;
            }
            $data['is_lock_code'] = $is_lock_code;
            Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'alipayTransferCode.php?'. http_build_query($data)]);
            return $zhongzhuan_url.'alipayTransferCode.php?'. http_build_query($data);
        }elseif ($codes == 'alipayWorkCard') {
            $data['qr_img'] = $this->encrypt($code['image_url']);
            Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url . 'alipayWorkCard.php?' . http_build_query($data)]);
            return $zhongzhuan_url . 'alipayWorkCard.php?' . http_build_query($data);
        }
        elseif ($codes == 'alipayWorkCardBig') {
            $data['qr_img'] = $this->encrypt($code['image_url']);
            Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url . 'alipayWorkCardBig.php?' . http_build_query($data)]);
            return $zhongzhuan_url . 'alipayWorkCardBig.php?' . http_build_query($data);
        }elseif ($codes == 'CnyNumberAuto'){
            Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'CnyNumber.php?'. http_build_query($data)]);
            return $zhongzhuan_url.'CnyNumber.php?'. http_build_query($data);
        }elseif ($codes == 'taoBaoEcard'){
            Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'taoBaoEcard.php?'. http_build_query($data)]);
            return $zhongzhuan_url.'taoBaoEcard.php?'. http_build_query($data);
        }elseif ($codes == 'usdtTrc'){
            $Config = new Config();
            $data['usdt_rate'] = $this->encrypt($Config->where(['name'=>'usdt_rate'])->value('value'));
            $data['extra'] = $this->encrypt($order['extra']);
            Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'usdtTrc.php?'. http_build_query($data)]);
            return $zhongzhuan_url.'usdtTrc.php?'. http_build_query($data);
        }elseif($codes == 'AggregateCode'){
            $is_h5= getAdminPayCodeSys('is_h5',$type,$user['admin_id']);
            $is_wechat= getAdminPayCodeSys('is_wechat',$type,$user['admin_id']);
            if (empty($is_wechat)){
                $is_wechat = 1;
            }
            $data['is_wechat'] = $is_wechat;
            if (empty($is_h5)){
                $is_h5 = 2;
            }
            $data['is_h5'] = $is_h5;
            $data['qr_img'] = $this->encrypt($code['image_url']);
            Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'AggregateCode.php?'. http_build_query($data)]);
            return $zhongzhuan_url.'AggregateCode.php?'. http_build_query($data);
        }elseif ($codes == 'alipayPinMoney'){
            Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'alipayPinMoney.php?'. http_build_query($data)]);
            return $zhongzhuan_url.'alipayPinMoney.php?'. http_build_query($data);
        }elseif ($codes == 'LeFuTianHongKa'){
            Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'LeFuTianHongKa.php?'. http_build_query($data)]);
            return $zhongzhuan_url.'LeFuTianHongKa.php?'. http_build_query($data);
        }elseif ($codes == 'WechatGoldRed'){
            Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'wechatCode.php?'. http_build_query($data)]);
            return $zhongzhuan_url.'WechatGoldRed.php?'. http_build_query($data);
            
        }elseif ($codes == 'QqCode'){
            $data['account_type'] = $code['account_type'];
            if ($code['account_type'] == 2){
                $data['account_number'] = $code['account_number'];
                Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'QqCode.php?'. http_build_query($data)]);
                return $zhongzhuan_url.'QqCode.php?'. http_build_query($data);
            }else{
                Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'QqCode.php?'. http_build_query($data)]);
                return $zhongzhuan_url.'QqCode.php?'. http_build_query($data);
            }

        }
//        $paofenPayUrl = db('config')->where(['name' => 'thrid_url_gumapay'])->value('value');;
       if($type==30)
       {
           $data['money'] = $money;
	       $r['money'] =  $money;
	     $r['account_name'] = $code['account_name'];
             $r['bank_name'] = $code['bank_name'];
	     $r['account_number'] = $code['account_number'];
           $kzk_pay_template = getAdminPayCodeSys('kzk_pay_template',$type,$user['admin_id']);
           if (empty($kzk_pay_template)){
               $kzk_pay_template = 2;
           }
           if ($kzk_pay_template == 1){
               $r['request_url'] = $zhongzhuan_url.'index_old.php?' . http_build_query($data);
           }elseif ($kzk_pay_template == 3){
              $r['request_url'] = $zhongzhuan_url.'index-3.php?' . http_build_query($data);
           }else{
               $r['request_url'] = $zhongzhuan_url.'index.php?' . http_build_query($data);
           }

           Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'index.php?' . http_build_query($data)]);
             return $r;
       }
       else
       {
           Db::name('orders')->where('trade_no',$params['trade_no'])->update(['remark'=>$zhongzhuan_url.'index.php?' . http_build_query($data)]);
           $kzk_pay_template = getAdminPayCodeSys('kzk_pay_template',$type,$user['admin_id']);
           if (empty($kzk_pay_template)){
               $kzk_pay_template = 2;
           }
           if ($kzk_pay_template == 1){
               return $zhongzhuan_url.'index_old.php?' . http_build_query($data);
           }elseif ($kzk_pay_template == 3){
               return $zhongzhuan_url.'index-3.php?' . http_build_query($data);
           }else{
               return $zhongzhuan_url.'index.php?' . http_build_query($data);
           }

       
       }
       // return "{$paofenPayUrl}?" . http_build_query($data);
    }


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

    public function guma_bzk($params)
    {
        $data = $this->pay($params, 3, 1);
        return [
            'request_url' => $data
        ];
    }
 public function yhk($params)
    {
        $data = $this->pay($params, 3, 1);
        return [
            'request_url' => $data
        ];
    }

 public function KZK($params)
    {
        $codeType = Db::name('pay_code')->where('code','kzk')->value('id');
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType);
        return $data;
    }

    public function guma_yhk($params)
    {
        $data = $this->pay($params, 3);
        return [
            'request_url' => $data
        ];
    }

    public function test($params)
    {
        $data = $this->pay($params, 3);
        return [
            'request_url' => $data
        ];
    }
 public function wap_vx($params)
    {
        $data = $this->pay($params, 4);
        return [
            'request_url' => $data
        ];
    }

    public function alipayUid($params)
    {
        $codeType = Db::name('pay_code')->where('code','alipayUid')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }

    public function alipayUidSmall($params)
    {
        $codeType = Db::name('pay_code')->where('code','alipayUidSmall')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }

    public function alipayUidTransfer($params)
    {
        $codeType = Db::name('pay_code')->where('code','alipayUidTransfer')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }


    public function alipayCode($params)
    {
        $codeType = Db::name('pay_code')->where('code','alipayCode')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }


    public function alipayCodeSmall($params)
    {
        $codeType = Db::name('pay_code')->where('code','alipayCodeSmall')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }


    public function douyinGroupRed($params)
    {
        $codeType = Db::name('pay_code')->where('code','douyinGroupRed')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }

    public function wechatCode($params)
    {
        $codeType = Db::name('pay_code')->where('code','wechatCode')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }


    public function wechatGroupRed($params)
    {
        $codeType = Db::name('pay_code')->where('code','wechatGroupRed')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }

    public function JDECard($params)
    {
        $codeType = Db::name('pay_code')->where('code','JDECard')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }

    public function CnyNumber($params)
    {
        $codeType = Db::name('pay_code')->where('code','CnyNumber')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }

    public function alipayPassRed($params)
    {
        $codeType = Db::name('pay_code')->where('code','alipayPassRed')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }

    public function AppletProducts($params)
    {
        $codeType = Db::name('pay_code')->where('code','AppletProducts')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }


    public function QQFaceRed($params)
    {
        $codeType = Db::name('pay_code')->where('code','QQFaceRed')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }


    public function alipaySmallPurse($params)
    {
        $codeType = Db::name('pay_code')->where('code','alipaySmallPurse')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }


    public function taoBaoMoneyRed($params)
    {
        $codeType = Db::name('pay_code')->where('code','taoBaoMoneyRed')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }


    public function alipayTransferCode($params)
    {
        $codeType = Db::name('pay_code')->where('code','alipayTransferCode')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }


    public function alipayWorkCard($params)
    {
        $codeType = Db::name('pay_code')->where('code','alipayWorkCard')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }


    public function alipayPinMoney($params)
    {
        $codeType = Db::name('pay_code')->where('code','alipayPinMoney')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }



    public function CnyNumberAuto($params)
    {
        $codeType = Db::name('pay_code')->where('code','CnyNumberAuto')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }


    public function QianxinTransfer($params)
    {
        $codeType = Db::name('pay_code')->where('code','QianxinTransfer')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }


    public function taoBaoEcard($params)
    {
        $codeType = Db::name('pay_code')->where('code','taoBaoEcard')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }


    public function usdtTrc($params)
    {
        $codeType = Db::name('pay_code')->where('code','usdtTrc')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }


    public function AggregateCode($params)
    {
        $codeType = Db::name('pay_code')->where('code','AggregateCode')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }

    public function alipayTransfer($params)
    {
        $codeType = Db::name('pay_code')->where('code','alipayTransfer')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }


    public function taoBaoDirectPay($params)
    {
        $codeType = Db::name('pay_code')->where('code','taoBaoDirectPay')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }

    public function JunWeb($params)
    {
        $codeType = Db::name('pay_code')->where('code','JunWeb')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }


    public function HuiYuanYiFuKa($params)
    {
        $codeType = Db::name('pay_code')->where('code','HuiYuanYiFuKa')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }

    public function LeFuTianHongKa($params)
    {
        $codeType = Db::name('pay_code')->where('code','LeFuTianHongKa')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }

    public function DingDingGroup($params)
    {
        $codeType = Db::name('pay_code')->where('code','DingDingGroup')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }


    public function wechatGoldRed($params)
    {
        $codeType = Db::name('pay_code')->where('code','wechatGoldRed')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }

    public function QqCode($params)
    {
        $codeType = Db::name('pay_code')->where('code','QqCode')->find();
        if (!$codeType){
            return $this->error('未识别的通道');
        }
        $data = $this->pay($params, $codeType['id'],false,$codeType['code']);
        return [
            'request_url' => $data
        ];
    }
//
//
//    public function notify()
//    {
//        //跑分平台秘钥
//        $data["out_trade_no"] = $_POST['out_trade_no'];
//        echo "SUCCESS";
//        return $data;
//    }
}
