<?php

namespace app\mobile\controller;

use app\common\controller\MobileBaseApi;
use app\common\library\MobileToken;

class Login extends MobileBaseApi
{

    protected $noNeedLogin = ['login', 'verfy_img'];

    public function login()
    {
        $account = $this->request->post('username');
        $password = $this->request->post('password');
        $code = $this->request->post('google_code');
        $verfy_code = $this->request->post('verfy_code');
        $t = $this->request->post('t');

        $captcha = new \app\common\library\Captcha();

        if ($captcha->check($verfy_code, $t . 'mobile_login') == false) {
            $this->error('验证码错误');
        }

        $IndexLogic = new \app\member\Logic\Index();

        // 验证用户名密码是否正确
        $ret = $IndexLogic->login($account, $password, null, false, 'mobile', $code, $verfy_code);


        if ($ret['code'] == 1) {
            $token = getUuid();
            $mobileToken = MobileToken::getInstance();
            $mobileToken->set($token, $ret['data']['user_info']['userid'], 86400*1);
            $tokenInfo = $mobileToken->get($token);

            $data = [
                'token' => $token,
                'uid' => $ret['data']['user_info']['userid'],
                'expire_time' => $tokenInfo['expiretime'],
                'expire_in' => $tokenInfo['expire_in'],
            ];

            $this->success('登录成功', $data);
        } else {
            $this->error($ret['msg']);
        }
    }

    /**
     * 生成验证码
     */
    public function verfy_img()
    {
        $t = $this->request->get('t');
        if (!$t){
            $this->error('参数错误');
        }
        $captcha = new \app\common\library\Captcha();
        $captcha->fontSize = 20;
        $captcha->length = 4;
        $captcha->useNoise = false;
        return $captcha->entry($t . 'mobile_login');
    }
}