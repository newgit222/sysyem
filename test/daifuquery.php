<?php
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


?>
