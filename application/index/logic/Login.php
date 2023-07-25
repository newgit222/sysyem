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

namespace app\index\logic;

use app\common\library\Activation;
use app\common\library\enum\CodeEnum;
use app\common\library\enum\UserStatusEnum;
use app\common\library\RsaUtils;
use app\common\model\User;
use app\common\model\UserPadmin;
use think\Db;
use think\Log;

class Login extends Base
{
    /**
     * 登录操作
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param string $username 账号
     * @param string $password  密码
     * @return array
     */
    public function dologin($username,$password,$google_code,$verfiy_code){
        $checkData = ['username'=>$username,'password'=>$password,'vercode'=>$verfiy_code];
        $validate = $this->validateLogin->check($checkData);
        if (!$validate) {

            return [ 'code' => CodeEnum::ERROR, 'msg' => $this->validateLogin->getError()];
        }


        $user = $this->logicUser->getUserInfo(['username' => $username]);
        //密码判断
        if (!empty($user['password']) && data_md5_key($password) == $user['password']) {
            //激活判断
            if ($user['status'] == UserStatusEnum::WAIT){
                action_log('登录', '商户'. $username . '账号未激活');
                return [ 'code' => CodeEnum::ERROR, 'msg' =>  '账号未激活'];
            }

            //ip限制
            $userIps = $this->logicUser->getUserInfo(['uid' => $user['uid']])['auth_login_ips'];
            if(!empty($userIps))
            {
                  //设置了ip限制
                $userIps = array_unique(array_filter(explode(',',$userIps)));
                if(!in_array(get_userip(),$userIps))
                {
                    return [ 'code' => CodeEnum::ERROR, 'msg' =>  'ip禁止访问，当前ip:' . get_userip()];
                }
            }

            //禁用判断
            if ($user['status'] == UserStatusEnum::DISABLE){
                return [ 'code' => CodeEnum::ERROR, 'msg' =>  '账号禁用'];
            }
            //google验证码判断
            if($user['is_need_google_verify'])
            {
                if(empty($google_code))
                {
                    return [ 'code' =>406, 'msg' =>  '请输入google验证码'];
                }
                if(false  == $this->logicGoogleAuth->checkGoogleCode($user['google_secret_key'],$google_code)) {
                    return ['code' => 406, 'msg' => 'google验证码错误'];
                }
            }

            $this->logicUser->setUserValue(['uid' => $user['uid']], 'update_time', time());
            $this->logicUser->setUserValue(['uid' => $user['uid']], 'last_login_time', time());

            $auth = ['uid' => $user['uid'], 'update_time'  =>  time()];

            session('user_info', $user);
            session('user_auth', $auth);
            session('user_auth_sign', data_auth_sign($auth));

            action_log('登录', '商户'. $username . '登录成功');

            return [ 'code' => CodeEnum::SUCCESS, 'msg' =>  '登录成功'];
        } else {
            $msg = empty($user['uid']) ? '用户账号不存在' : '密码输入错误';
            action_log('登录', '商户'. $username . '登录失败，' . $msg);
            return [ 'code' => CodeEnum::ERROR, 'msg' => $msg];
        }
    }

    /**
     * 数据检测
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param string $field
     * @param string $value
     * @return mixed
     */
    public function checkField($field='',$value=''){
        $user_field = $this->modelUser->getInfo([$field=>$value], $field);
        if($user_field){
            return [ 'code' => CodeEnum::ERROR, 'msg' => '账户已被使用'];
        }else{
            return [ 'code' => CodeEnum::SUCCESS, 'msg' =>  '账户可用'];
        }
    }

    /**
     * 用户注册
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $data 注册数据
     * @return array
     */
    public function doregister($data){
        $validate = $this->validateRegister->check($data);

//        $where['account'] = $data['account'];
        $User = new User();
//        $userInfo = $User->where($where)->find();
//        if(!empty($userInfo))
//        {
//            return [ 'code' => CodeEnum::ERROR, 'msg' => "邮箱已经存在"];
//        }
        $where['username'] = $data['username'];
        $userInfo = $User->where($where)->find();
        if(!empty($userInfo))
        {
            return [ 'code' => CodeEnum::ERROR, 'msg' => "商户名已经存在"];
        }
        unset($where);
        //数据检验
        if (!$validate) {
            return [ 'code' => CodeEnum::ERROR, 'msg' => $this->validateRegister->getError()];
        }

        //TODO 添加数据
        Db::startTrans();
        try{
            //密码
            if (!empty(is_login())){
                $admin_id = Db::name('user')->where('uid',is_login())->value('admin_id');
                $data['admin_id'] = $admin_id;
            }else{
                return [ 'code' => CodeEnum::ERROR, 'msg' => "非法请求"];
            }
            $data['password'] = data_md5_key($data['password']);
            //基本信息
            $data['is_verify'] = 1;
            $data['status'] = UserStatusEnum::ENABLE;
            $uid = $this->modelUser->setInfo($data);
            //账户记录
            $this->modelUserAccount->setInfo(['uid'  => $uid ]);

            //资金记录
            $this->modelBalance->setInfo(['uid'  => $uid ]);
            //生成API记录
            $this->modelApi->setInfo(['uid'  => $uid,'key'=>md5(time())]);

            //加入邮件队列
            $jobData = $this->logicUser->getUserInfo(['uid'=>$uid],'uid,account,username');

            //邮件场景
            $jobData['scene']   = 'register';
            $this->logicQueue->pushJobDataToQueue('AutoEmailWork' , $jobData , 'AutoEmailWork');

            action_log('新增', '新增商户。UID:'. $uid);

            //注册成功 入库user_padmin表
            //todo   暂时不要对接跑分平台的
            //  $padmin =$data['p_admin'];
            $padmin =[];
            $p_admin_id =config('paofen_super_admin_id');
            $p_admin_appkey ='';
            if($padmin)
            {
                $padmin  =explode('-',$padmin);
                $p_admin_id =$padmin[0];
                $p_admin_appkey =$padmin[1];
            }
            $userPadminModel = new UserPadmin();
            $ret  =$userPadminModel->save([
                'uid'=>$uid,
                'p_admin_id'=>$p_admin_id,
                'p_admin_appkey'=>$p_admin_appkey,
            ]);
            if($ret==false)
            {
                return [ 'code' => CodeEnum::ERROR, 'msg' => "绑定管理员失败"];
            }
            Db::commit();
            return ['code' => CodeEnum::SUCCESS, 'msg' => '注册成功'];
        }catch (\Exception $ex){
            Db::rollback();
            Log::error($ex->getMessage());
            return ['code' => CodeEnum::ERROR, 'msg' => config('app_debug') ? $ex->getMessage()
                : '哎呀！注册发生异常了~'];
        }
    }


    /**
     * 注销当前用户
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return string
     */
    public function logout()
    {

        clear_user_login_session();

        return url('index/login/login');
    }
}