<?php

namespace app\api\service;
use app\common\library\exception\ParameterException;
use app\common\logic\Orders;

class DaifuPayment
{
    /**
     * 支付配置
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @var array
     */
    protected $config;

    /**
     * constructor.
     *
     * @param array $config
     */
    private function __construct(array $config = [])
    {
        $this->config = $config;
    }
    /**
     * 魔术方法
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param string $method
     * @param array $params
     *
     * @return mixed
     * @throws \Exception
     */
    public static function __callStatic($method, $params)
    {
        $app = new self(...$params);

        return $app->create($method);
    }

    /**
     * 构建
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $method
     * @return mixed
     * @throws \Exception
     */
    public function create($method){
        $method = explode("?", $method)[0];
        $payment = __NAMESPACE__ . '\\daifu\\' . ucwords($method);
//        $path=str_replace('\\','/',$payment);

        if (class_exists($payment)) {
            return new $payment($this->config);
        }
        if(preg_match('/[0-9]/', $payment, $matches, PREG_OFFSET_CAPTURE)) {
            $unuese_str = substr($payment,$matches[0][1],7);

            $payment = str_replace($unuese_str,'',$payment);
            if (class_exists($payment)) {
                return new $payment($this->config);
            }
        }
//        throw new ParameterException(['msg' => 'DaifuPayment [{'.$method.'}] Not Exists']);
        var_dump(['msg' => 'DaifuPayment [{'.$method.'}] Not Exists']);die;
    }


    /**
     * curl get
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param string $url
     * @param array $options
     * @return mixed
     */
    public static function curlGet($url = '', $options = array())
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }
        //https请求 不验证证书和host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * curl post
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param string $url
     * @param string $postData
     * @param array $options
     * @return mixed
     */
    public static function curlPost($url = '', $postData = '', $options = array(),$timeOut=5)
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
        curl_close($ch);
        return $data;
    }

}