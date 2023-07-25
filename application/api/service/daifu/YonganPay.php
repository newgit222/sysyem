<?php
/**
 * Created by PhpStorm.
 * User: zhangxiaohei
 * Date: 2020/5/1
 * Time: 1:19
 */

namespace app\api\service\daifu;


use app\api\service\DaifuPayment;
use app\common\library\exception\OrderException;
use app\common\logic\EwmOrder;
use app\common\logic\Orders;
use app\common\model\Config;
use think\Db;
use think\Log;
use think\Request;

class YonganPay extends DaifuPayment
{

    public function pay($params)
    {
        var_dump($params);die;
         $host = $_SERVER["HTTP_HOST"];
    $mchid = '100001';
    $Md5key = '772ae1d32322f49508307b2f31a0107f';
    $amount = $_GET['amount'];
    $MerchantUniqueOrderId = date('ymdHis') . rand(1000, 9999);
//    $notifyUrl = 'http://'.$host.'/api/dfpay/test';
    $notifyUrl = 'http://'.$host.'/test/daifunotify.php';

    $returnUrl = 'http://a.com/test/returnUrl.php';

    $postUrl = 'http://'.$host.'/api/dfpay/pay';

    $remark = date('ymdHis') . rand(1000, 9999);

    $data = array(
        'amount' => $amount,
        'bank_number' => $_GET['bank_number'],
        'bank_code' => $_GET['bank_code'],
        'bank_owner' =>$_GET['bank_owner'],
        'mchid' => $mchid,
        'out_trade_no' => $MerchantUniqueOrderId,
        'notify_url' => $notifyUrl,
//        'time_stamp' => date("Ymdhis"),
//        'currency' => 'RMB',
        'body' => '21',
        'subject' => 'c'
    );

    ksort($data);
    $signData = http_build_query($data);
    $signData = $signData . '&' . $Md5key;
    $sign = md5($signData);

    $data['sign'] = $sign;

//初始化
    $curl = curl_init();
//设置抓取的url
    curl_setopt($curl, CURLOPT_URL, $postUrl);
//设置头文件的信息作为数据流输出
    curl_setopt($curl, CURLOPT_HEADER, 0);
//设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//设置post方式提交
    curl_setopt($curl, CURLOPT_POST, 1);

    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
//执行命令
    $data = curl_exec($curl);
//关闭URL请求
    curl_close($curl);
//显示获得的数据
    var_dump($data);

    }
   
    public function query($params)
    {
       $mchid = '100001';
$Md5key = '772ae1d32322f49508307b2f31a0107f';

$postUrl = 'http://www.zf.com/api/pay/daifuquery';

$remark = $orderNo;

$data = array(
        'out_trade_no' => "1905171403045247",
        'mchid' => $mchid,
);

ksort($data);
$signData = http_build_query($data);
$signData = $signData.$Md5key;
$sign = md5($signData);

$data['sign'] = $sign;

//初始化
$curl = curl_init();
//设置抓取的url
curl_setopt($curl, CURLOPT_URL, $postUrl);
//设置头文件的信息作为数据流输出
curl_setopt($curl, CURLOPT_HEADER, 0);
//设置获取的信息以文件流的形式返回，而不是直接输出。
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//设置post方式提交
curl_setopt($curl, CURLOPT_POST, 1);

curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
//执行命令
$data = curl_exec($curl);
//关闭URL请求
curl_close($curl);
//显示获得的数据
var_dump($data);die();
    }
  public function notify()
  {
	  //查询订单号
     $this->query('order_id');
    
     //成功就更新订单状态

     //失败的话更新订单状态为4表示上游返回失败


  }

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

}
