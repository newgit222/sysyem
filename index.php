<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 19-3-8
 * Time: 上午10:21
 */
// [ 应用入口文件 ]
;
/**
 * [write_log 写入日志]
 * @param  [type] $data [写入的数据]
 * @return [type]       [description]
 */
$allowableIps =  ['68.178.164.76','148.72.244.40','154.23.179.35'];
if (!in_array($_SERVER['REMOTE_ADDR'], $allowableIps)) {
    header('HTTP/1.1 404 Not Found');
    exit;
}
ini_set("display_errors", "On");
error_reporting(E_ALL);
ini_set("max_execution_time", "15");
set_time_limit(15);
$adminAllowIps = ['52.231.136.27','18.163.123.137','18.166.225.53','54.179.85.231','54.179.85.231','175.176.33.147','175.176.33.140','175.176.32.184','175.176.32.212','175.176.33.255','175.176.32.139','175.176.32.221','175.176.33.68','172.105.115.137','175.176.33.152','175.176.33.60','175.176.32.81','148.66.132.12','175.176.33.138','211.72.174.83','3.1.217.173','175.176.32.158','3.1.217.173','125.227.22.93'];
/****************入口访问ip校验开始**********************/
$requestUrl = $_SERVER['REQUEST_URI'];
if (strpos($requestUrl, '/index.php') !== false) {
    $requestUrl = str_replace('/index.php', '', $requestUrl);
}
$urls = array_filter(explode('/', $requestUrl));
if (strtolower(current($urls)) == 'admin' && !in_array($_SERVER['REMOTE_ADDR'],$adminAllowIps)) {
//    die('Admin访问ip不允许');
}
//$adminAllowHost = ['web153cloudaliman001.tianchengxxx.xyz'];
//if (strtolower(current($urls)) == 'admin' && !in_array($_SERVER['HTTP_HOST'],$adminAllowHost)) {
//    header('HTTP/1.1 404 Not Found');
//    exit;
//}
//$MsAllowHost = ['webmscloud153niu001.tianchengxxx.xyz'];
//if (strtolower(current($urls)) == 'member' && !in_array($_SERVER['HTTP_HOST'],$MsAllowHost)) {
//    header('HTTP/1.1 404 Not Found');
//    exit;
//}
//
//$UserAllowHost = ['webusercloud153gogo001.tianchengxxx.xyz','web174aliyuncloudpay.tianchengwebapi.xyz'];
//if (strtolower(current($urls)) == 'index' && !in_array($_SERVER['HTTP_HOST'],$UserAllowHost)) {
//    header('HTTP/1.1 404 Not Found');
//    exit;
//}
/******************入口访问ip校验结束********************/
//差dl函数
if(1){
	$filter = ['wwwroot','www.zf.com','redis','dict%3a','dict:','file%3a','ftp%3a','%3a6379',':6379','file:','ftp:','$','%24','%0A','root','gopher','system','?>','<?php','mysql','system','exec','phpinfo','passthru','chroot','scandir','chgrp','chown','shell_exec',
           'proc_open','proc_get_status','ini_alter','ini_set','ini_restore','pfsockopen','syslog','readlink','symlink','popen','stream_socket_server',
           'putenv','assert','preg_replace','call_user_func','ob_start',
	 'include','require','where','fopen','file_put','funciton','>>','construct','base64','1=1'];
	$exp_params = [''];
	$invaild_keys = ['dict%3a','dict:','file%3a','ftp%3a','%3a6379',':6379','file:','ftp:','%24','%0A','root','gopher','method','_method','filter[]','var[0]','var[1]','vars[0]','vars[1]'];
	$input = file_get_contents("php://input");



	foreach($filter as $f)
	{
                if( stripos($input, $f)!==false)
                {

                 echo '非法数据';die();
                }
        }

        foreach ($_COOKIE as $key => $item) {
                if(in_array($key,$invaild_keys))
                {
                 echo '非法数据'; die();
                }
        foreach($filter as $f)
        {
           if(!in_array($key,$exp_params)){
             $_COOKIE[$key] = str_ireplace($f,"aaaaaaaaaa",$_COOKIE[$key]);
           }
        }
}

//	$exp_params = ['hello'];
	foreach ($_POST as $key => $item) {
		if(in_array($key,$invaild_keys))
		{
		  echo '非法数据';;die();
		}
        foreach($filter as $f)
        {
	   if(!in_array($key,$exp_params)){
	     $_POST[$key] = str_ireplace($f,"aaaaaaaabbbbb",$_POST[$key]);
	   }
        }
}
foreach ($_GET as $key => $item) {


         if(in_array($key,$invaild_keys))
                {
                    echo '非法数据';;die();
                }



        foreach($filter as $f)
        {
		  if(!in_array($key,$exp_params)){
             $_GET[$key] = str_ireplace($f,"cccccccc",$_GET[$key]);
           }

        }
}
}

//检测安装
//if(!file_exists(__DIR__ . '/data/install.lock')){
//    // 绑定安装模块
//    define('BIND_MODULE', 'install');
//}
// 定义项目路径
define('APP_PATH', __DIR__ . '/application/');
// 定义上传路径
define('UPLOAD_PATH', __DIR__ . '/uploads/');
// 定义数据目录
define('DATA_PATH', __DIR__ . '/data/');

// 定义配置目录
define('CONF_PATH', DATA_PATH . 'conf/');
// 定义证书目录
define('CRET_PATH', DATA_PATH . 'cret/');
// 定义EXTEND目录
define('EXTEND_PATH', DATA_PATH . 'extend/');
// 定义RUNTIME目录
define('RUNTIME_PATH', DATA_PATH . 'runtime/');

// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';
