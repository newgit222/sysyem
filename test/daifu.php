<?php
//ret1
$host = $_SERVER["HTTP_HOST"];
if(!empty($_GET['amount'])) {
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
    die();
}
else {
?>
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Document</title>
		<style>
			html, body, h1, form, fieldset, legend, ol ,li{
				padding:0;
				margin:0;
			}
			ol{
				list-style:none;
			}
			body{
				background:#fff;
				color:#111;
				padding:20px;
			}
			form#payment{
				background:#9cbc2c;
				-webkit-border-radius:5px;
				border-radius:5px;
				padding:20px;
				width:400px;
			}
			form#payment fieldset{
				border:none;
				margin-bottom:10px;
			}
			form#payment fieldset:last-of-type{ margin-bottom:0; }
			form#payment legend{
				color:#384313;
				font-size:16px;
				font-weight:bold;
				padding-bottom:10;
				text-shadow:0px 1px 1px #c0d576;
			}
			form#payment > fieldset>legend:before{
				content:"Step" counter(fieldset)":";
				counter-increment:fieldsets;
			}
			form#payment fieldset fieldset legend{
				color:#111;
				font-size:13px;
				font-weight:normal;
				padding-bottom:0;
			}
			form#payment ol li{
				background:#b9cf6a;
				background:rgba(255, 255, 255, 0.3);
				border:#e3ebc3;
				border-color:rgba(255, 255, 255, 0.6);
				border-style:solid;
				border-width:2px;
				-webkit-border-radius:5px;
				line-height:30px;
				padding:5px 10px;
				margin-bottom:2px;
			}
			form#payment ol ol li{
				bakcground:none;
				border:none;
				float:left;
			}
			form#payment label{
				float:left;
				font-size:13px;
				width:110px;
			}
			form#payment fieldset fieldset label{
				background:none no-repeat left 50%;
				line-height:20px;
				padding:0 0 0 30px;
				width:auto;
			}
			form#payment fieldset fieldset label:hover{cursor:pointer;}
			form#payment input:not([type=radio]), form#payment textarea{
				background:#fff;
				border:#fc3 solid 1px;
				-webkit-border-radius:3px;
				outline:none;
				padding:5px;
			}
		</style>
	</head>
	<body>
		<form action="/test/daifu.php" method="get" id=payment>
		<fieldset>
			<legend>金额详细资料</legend>
			<ol>
<!--				<li>-->
<!--					支付方式 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-->
<!--					<select name="method">-->
<!--                        <option value="guma_vx">微信扫码</option>-->
<!--                        <option value="h5_vx">微信H5</option>-->
<!--                        <option value="wap_vx">微信wap</option>-->
<!--                        <option value="guma_zfb">支付宝扫码</option>-->
<!--                        <option value="wap_zfb">支付宝wap</option>-->
<!--                        <option value="h5_zfb">支付宝h5</option>-->
<!--                        <option value="wechatPay" > Pk微信</option>-->
<!--                        <option value="h5_zfb" > 支付宝h5</option>-->
<!--<!--                        <option value="jhm" >聚合码</option>-->-->
<!--                        <option value="test" > 测试</option>-->
<!--	               </select>-->
<!--				</li>-->

				<li>
					<label for="name">金额：</label>
					<input type="text" id="amount" name="amount" placeholder="请输入金额" required autofocus>
				</li>

                <li>
                    <label for="name">银行编码：</label>
                    <input type="text" id="bank_code" name="bank_code" placeholder="请输入银行编码" required autofocus>
                </li>

                <li>
                    <label for="name">银行卡号：</label>
                    <input type="text" id="bank_number" name="bank_number" placeholder="请输入银行卡号" required autofocus>
                </li>

                <li>
                    <label for="name">姓名：</label>
                    <input type="text" id="bank_owner" name="bank_owner" placeholder="请输入姓名" required autofocus>
                </li>

<!--                <li>-->
<!--                    <label for="name">测试：</label>-->
<!--                    是 <input type="radio"  name="debug" placeholder="" value="1">-->
<!--                    否 <input type="radio"  name="debug" placeholder="" value="0">-->
<!--                </li>-->

			</ol>
		</fieldset>
		<fieldset>
			<button type="submit">现在支付</button>
		</fieldset>
	</form>
	</body>
	</html>
<?php } ?>



