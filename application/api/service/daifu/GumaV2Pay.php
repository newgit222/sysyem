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
    protected $secret_key  = 'f3a59b69324c831e';

    /**
     * var string $iv 加解密的向量，有些方法需要设置比如CBC
     */
    protected $iv = '7fc7fe7d74f4da93';

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
        $response = $EwmOrderLogic->createOrder($money, $params['trade_no'], $type, $params['out_trade_no'], $user['admin_id'], $this->config['notify_url'],$user['pao_ms_ids'],$params['body']);
        if ($response['code'] != 1) {
            Log::error('Create GumaV2Pay API Error:' . ($response['msg'] ? $response['msg'] : ""));
            throw new OrderException([
                'msg' => 'Create GumaV2Pay API Error:' . ($response['msg'] ? $response['msg'] : ""),
                'errCode' => 200009
            ]);
        }
		$code = $response['data']['code'];

        $data['is_bzk'] = $is_bzk;
        $data['account_name'] = $this->encrypt($code['account_name']);
        $data['bank_name'] = $this->encrypt($code['bank_name']);
        $data['account_number'] = $this->encrypt($code['account_number']);
        $data['trade_no'] = $params['trade_no'];
        $data['order_pay_price'] = $response['data']['money'];
        $data['key'] = config('inner_transfer_secret');
        $data['sign'] = $this->getSign($data);
        $data['user'] = $this->encrypt($_SERVER['HTTP_HOST']);
        unset($data['key']);

        if ($codes == 'alipayUid' || $codes == 'alipayUidSmall') {
//            $uidPayUrl = db('config')->where(['name' => 'thrid_url_uid'])->value('value');
//            $url = 'https://www.alipay.com/?appId=20000123&actionType=scan&biz_data={"s":"money","u":"'.$code['account_number'].'","a":"'.$data['order_pay_price'].'","m":"商城购物'.$params['trade_no'].'"}';
//            $data['gourl'] = 'alipays://platformapi/startapp?appId=68687093&url='.urlencode($url);

            return config('zhongzhuan_url').'uid.php?'. http_build_query($data);
        }elseif ($codes == 'alipayUidTransfer'){
            return config('zhongzhuan_url').'uidtransfer.php?'. http_build_query($data);
        }
//        $paofenPayUrl = db('config')->where(['name' => 'thrid_url_gumapay'])->value('value');;
       if($type==30)
       {
           $data['money'] = $money;
	       $r['money'] =  $money;
	     $r['account_name'] = $code['account_name'];
             $r['bank_name'] = $code['bank_name'];
	     $r['account_number'] = $code['account_number'];
	     $r['request_url'] = config('zhongzhuan_url').'index.php?' . http_build_query($data);
             return $r;
       }
       else
       {
       
         return config('zhongzhuan_url').'index.php?' . http_build_query($data);
       
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

    public function notify()
    {
        //跑分平台秘钥
        $data["out_trade_no"] = $_POST['out_trade_no'];
        echo "SUCCESS";
        return $data;
    }
}
