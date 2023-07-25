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

namespace app\index\controller;


class Login extends Basis
{

    /**
     * 登录
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function login(){

        //登录检测
        is_login() && $this->redirect(url('index/User/index'));
        $this->request->isPost() && $this->result(
            $this->logicLogin->dologin(
                $this->request->post('username'),
                $this->request->post('password', '123456'),
                $this->request->post('google_code'),
                $this->request->post('vercode')
            )
        );
        return $this->fetch();
    }



    /**
     * 注销登录
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function logout()
    {
        $this->redirect($this->logicLogin->logout());
    }

    /**
     * 检查手机号码
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function checkPhone(){
        $this->request->isPost() && $this->result(
            $this->logicLogin->checkField('phone',$this->request->post('phone'))
        );
    }



}