<?php
/**
 * Created by PhpStorm.
 * User: zhangxiaohei
 * Date: 2020/7/1
 * Time: 20:04
 */


if(!in_array($_SERVER['REMOTE_ADDR'],['210.16.122.3','45.159.57.9']) ){
    echo 'error';
    die();  
}



$notifyData =$_POST;
if($notifyData){

    $notifyData['ips'] = $_SERVER['REMOTE_ADDR'];
    $url = site_url().'/api/notify/notify/channel/FeilongPay';

    $result = curlPost($url,$notifyData);
    echo ($result);
}

function site_url() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domainName = $_SERVER['HTTP_HOST'];

    return $protocol . $domainName;
}


function curlPost($url = '', $postData = '', $options = array(),$timeOut=5)
{
    if (is_array($postData)) {
        $postData = http_build_query($postData);
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut); //设置cURL允许执行的最长秒数
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

    if (!empty($options)) {
        curl_setopt_array($ch, $options);
    }
    //https请求 不验证证书和host
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $data = curl_exec($ch);

    $headers = curl_getinfo($ch);
//        echo json_encode($headers);
    curl_close($ch);
    return $data;
}


