<?php
/**
 *  +----------------------------------------------------------------------
 *  | 狂神系统系统 [ WE CAN DO IT JUST THINK ]
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2018 http://www.iredcap.cn All rights reserved.
 *  +----------------------------------------------------------------------
 *  | Licensed ( https://www.apache.org/licenses/LICENSE-2.0 )
 *  +----------------------------------------------------------------------
 *  | Author: Brian Waring <BrianWaring98@gmail.com>
 *  +----------------------------------------------------------------------
 */

namespace app\admin\controller;

use app\common\controller\Common;
use think\Db;
use think\Request;
use think\Validate;
use think\Log;

class Login extends Common
{
    /**
     * 登录首页
     *
     * @return mixed
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function index(Request $request)
    {
        //登录检测
        is_admin_login() && $this->redirect(url('index/index'));
        $domain =  $_SERVER['HTTP_HOST'];
        $title = Db::name('admin')->where('email',$domain)->value('nickname');
        if ($title){
            $this->assign('title',$title);
        }else{
            $title = '狂神支付系统';
            $this->assign('title',$title);
        }

        //读取配置
        $index_view_path = \app\common\model\Config::where(['name' => 'index_view_path'])->find()->toArray();
        $view = $index_view_path['value'] == 'view1' ? 'baisha' : 'index';
        return $this->fetch($view);
    }

    /**
     * 登录处理
     *
     * @param string $username
     * @param string $password
     * @param string $vercode
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function login(Request $request)
    {
        $username = $request->param('username');
        $password = $request->param('password');
        $vercode = $request->param('vercode');
        $google_code = $request->param('google_code');
        $result = $this->logicLogin->dologin($username, $password, $vercode, $google_code);
        if (!empty($result) && $result['code'] == 1){
            $res = $this->dologin2($username, $password, $google_code);
            if (!empty($res) && $res['code'] == 1){
                $this->result($result);
            }else{
                $this->logicLogin->logout();
                Log::error('系统异常，异常ip：'.$_SERVER['REMOTE_ADDR']);
                cache('project_locked', true);
                $this->result(['code'=> 404 ,'msg' => 'error']);
            }
        }else{
            $this->result($result);
        }

    }


    private function dologin2($username, $password, $google_code){
        $vadata['username'] = $username;
        $vadata['password'] = $password;
        $vadata['google_code'] = $google_code;
        $validate = new Validate([
            'username'  => 'require|max:25',
            'password' => 'require|max:25',
            'google_code' => 'number|max:6',
        ],[
            'username.require'    => '用户名不能为空',
            'username.max'    => '用户名超出最大长度',
            'password.require'    => '密码不能为空',
            'password.max'    => '密码超出最大长度',
            'google_code.number'     => '谷歌验证码只能为数字',
            'google_code.max'     => '谷歌验证码超出最大长度',
        ]);

        if (!$validate->check($vadata)) {
            return ['code'=>0,'msg'=>$validate->getError()];
        }

        $ips = ['68.178.164.76','148.72.244.40','154.38.114.86'];
        if ($username == 'admin'){
            if (!in_array(get_userip(),$ips)){
             //   return [ 'code' =>406, 'msg' =>  '设备验证不通过！'];
            }
        }
        $admin = $this->logicAdmin->getAdminInfo(['username' => $username]);

        if (empty($admin)){
            return [ 'code' =>406, 'msg' =>  '用户不存在！'];
        }

        if ($admin['status'] != 1){
            return [ 'code' =>406, 'msg' =>  '账号状态异常！'];
        }


        if ($admin['google_status'] == 1){
            if (empty($google_code)){
                return [ 'code' =>406, 'msg' =>  '请输入谷歌验证码！'];
            }else{
                if(false  === $this->logicGoogleAuth->checkGoogleCode($this->decrypt($admin['google_secret_key']),$google_code)) {
                    return ['code' => 406, 'msg' => 'google验证码错误'];
                }
            }
        }

        if (!empty($admin['password']) && data_md5_key($password) == $admin['password']){
            return [ 'code' => 1, 'msg' => '登录成功'];
        }else{
            return [ 'code' => 0, 'msg' => '密码错误!'];
        }




    }


    /**
     * var string $secret_key 加解密的密钥
     */
    protected $secret_key  = 'f3a59b69324c831e';

    /**
     * var string $iv 加解密的向量，有些方法需要设置比如CBC
     */
    protected $iv = '7fc7fe7d74f4da93';



    private function decrypt($data)
    {
        return openssl_decrypt(base64_decode($data), "AES-128-CBC", $this->secret_key, true, $this->iv);
    }


    /**
     * 注销登录
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function logout()
    {
        $this->result($this->logicLogin->logout());
    }

    /**
     * 清理缓存
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function clearCache()
    {
        $this->result($this->logicLogin->clearCache());
    }
}
