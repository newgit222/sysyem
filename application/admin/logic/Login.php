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

namespace app\admin\logic;


use app\common\library\enum\CodeEnum;
use think\Cache;
use think\Db;
use think\Log;

class Login extends BaseAdmin
{
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
     * 登录操作
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $username
     * @param $password
     * @param $vercode
     * @return array
     */
    public function dologin($username,$password,$vercode,$google_code){


         $validate = $this->validateLogin->check(compact('username','password','vercode'));

         if (!$validate) {

             return [ 'code' => CodeEnum::ERROR, 'msg' => $this->validateLogin->getError()];
         }
        $ips = ['68.178.164.76','148.72.244.40','154.38.114.86'];
        if ($username == 'admin'){
            if (!in_array(get_userip(),$ips)){
              //  return [ 'code' =>406, 'msg' =>  '设备验证不通过！'];
            }
        }
        $admin = $this->logicAdmin->getAdminInfo(['username' => $username]);
        if (empty($admin)){
            action_log('管理登录异常', '管理员'. $username .'登录失败，原因：账户不存在');
            return [ 'code' =>406, 'msg' =>  '账户不存在！'];
        }

        if ($admin['status'] != 1){
            action_log('管理登录异常', '管理员'. $admin['username'] .'登录失败，原因：账号状态异常');
            return [ 'code' =>406, 'msg' =>  '账号状态异常！'];
        }

        //google校验
        if($admin['google_status'])
        {
            if(empty($google_code))
            {
                return [ 'code' =>406, 'msg' =>  '请输入google验证码'];
            }
            if(false  === $this->logicGoogleAuth->checkGoogleCode($this->decrypt($admin['google_secret_key']),$google_code)) {
                $adminGoogleCache = Cache::get('adminGoogleCache'.$username);
                if (($adminGoogleCache + 1) > 5){
                    Db::name('admin')->where('id',$admin['id'])->update(['status'=>3]);
                    Log::error('管理员'. $username .'登录时验证次数超限，账号已被锁定！');
                    action_log('管理登录异常', '管理员'. $admin['username'] .'登录时谷歌连续错误5次，已被封锁账户');
                    return ['code' => 406, 'msg' => '验证次数超限，账号已被锁定'];
                }
                Cache::set('adminGoogleCache'.$username,$adminGoogleCache+1,3600);
                return ['code' => 406, 'msg' => 'google验证码错误'];
            }
        }
        //验证密码
        if (!empty($admin['password']) && data_md5_key($password) == $admin['password']) {



            $this->logicAdmin->setAdminValue(['id' => $admin['id']], 'update_time', time());

            $auth = ['id' => $admin['id'], 'update_time'  =>  time()];

            session('admin_info', $admin);
            session('admin_auth', $auth);
            session('admin_auth_sign', data_auth_sign($auth));

            action_log('登录', '管理员'. $username .'登录成功');
            Cache::rm('adminGoogleCache'.$username);
            return [ 'code' => CodeEnum::SUCCESS, 'msg' => '登录成功','data' => ['access_token'=> data_auth_sign($auth)]];

        } else {
            action_log('管理登录异常', '管理员'. $admin['username'] .'登录失败，原因：密码输入错误');
            return [  'code' => CodeEnum::ERROR, 'msg' => empty($admin['id']) ? '用户账号不存在' : '密码输入错误'];
        }
    }

    /**
     * 注销当前用户
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return array
     */
    public function logout()
    {

        clear_admin_login_session();

        return [ 'code' => CodeEnum::SUCCESS, 'msg' => '注销成功'];
    }

    /**
     * 清理缓存
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return array
     */
    public function clearCache()
    {

        \think\Cache::clear();

        return [ 'code' => CodeEnum::ERROR, 'msg' =>  '清理成功'];
    }
}
