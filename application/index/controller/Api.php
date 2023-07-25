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


use app\common\library\RsaUtils;
use app\common\model\Config;
use think\Db;
use think\Request;

class Api extends Base
{

    /**
     * 接口基本
     *
     * @return mixed
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function index(Request $request)
    {
        $this->apiCommon();
        $admin_id = Db::name('user')->where('uid',is_login())->value('admin_id');
      //  print_r($admin_id);die;
        $zhongzhuanconfig = require_once('./data/conf/zhongzhuan.php');
        $one_notify_address = getAdminPayCodeSys('one_notify_address',256,$admin_id);

        if (empty($one_notify_address)){
            $one_zhongzhuan_url = $request->host();
        }else{
            if ($one_notify_address == 0){
                $one_zhongzhuan_url = $zhongzhuanconfig['zhongzhuan_url'];
            }else if($one_notify_address == 1){
                $one_zhongzhuan_url = $zhongzhuanconfig['aliyun_zhongzhuan'];
            }else if($one_notify_address == 2){
                $one_zhongzhuan_url = $zhongzhuanconfig['hk_wuli_zhongzhuan'];
            }else if($one_notify_address == 3){
                $one_zhongzhuan_url = $zhongzhuanconfig['cbd_zhongzhuan_url'];
            }else if($one_notify_address == 4){
                $one_zhongzhuan_url = $zhongzhuanconfig['tengcent_cloud'];
            }else if($one_notify_address == 5){
                $one_zhongzhuan_url = $zhongzhuanconfig['hk61_wuli_zhongzhuan'];
            }else if($one_notify_address == 6){
                $one_zhongzhuan_url = $zhongzhuanconfig['35_zhongzhuan_url'];
            }else if($one_notify_address == 7){
                $one_zhongzhuan_url = $zhongzhuanconfig['73_zhongzhuan_url'];
            }else if($one_notify_address == 8){
                // $one_zhongzhuan_url = $zhongzhuanconfig['hk174_wuli_zhongzhuan'];
                $one_zhongzhuan_url = $zhongzhuanconfig['aliyun_hangzhou_zhongzhuan'];
            }else if($one_notify_address == 11){
                $one_zhongzhuan_url = $zhongzhuanconfig['zhongzhuan_url'];
            }
        }


        $two_notify_address = getAdminPayCodeSys('two_notify_address',256,$admin_id);
        if (empty($two_notify_address)){
            $two_zhongzhuan_url = $request->host();
        }else{
            if ($two_notify_address == 0){
                $two_zhongzhuan_url = $zhongzhuanconfig['zhongzhuan_url'];
            }else if($two_notify_address == 1){
                $two_zhongzhuan_url = $zhongzhuanconfig['aliyun_zhongzhuan'];
            }else if($two_notify_address == 2){
                $two_zhongzhuan_url = $zhongzhuanconfig['hk_wuli_zhongzhuan'];
            }else if($two_notify_address == 3){
                $two_zhongzhuan_url = $zhongzhuanconfig['cbd_zhongzhuan_url'];
            }else if($two_notify_address == 4){
                $two_zhongzhuan_url = $zhongzhuanconfig['tengcent_cloud'];
            }else if($two_notify_address == 5){
                $two_zhongzhuan_url = $zhongzhuanconfig['hk61_wuli_zhongzhuan'];
            }else if($two_notify_address == 6){
                $two_zhongzhuan_url = $zhongzhuanconfig['35_zhongzhuan_url'];
            }else if($two_notify_address == 7){
                $two_zhongzhuan_url = $zhongzhuanconfig['73_zhongzhuan_url'];
            }else if($two_notify_address == 8){
                $two_zhongzhuan_url = $zhongzhuanconfig['aliyun_hangzhou_zhongzhuan'];
            }else if($two_notify_address == 11){
                $two_zhongzhuan_url = $zhongzhuanconfig['zhongzhuan_url'];
            }
        }




        $three_notify_address = getAdminPayCodeSys('three_notify_address',256,$admin_id);
        if (empty($three_notify_address)){
            $three_zhongzhuan_url = $request->host();
        }else{
            if ($three_notify_address == 0){
                $three_zhongzhuan_url = $zhongzhuanconfig['zhongzhuan_url'];
            }else if($three_notify_address == 1){
                $three_zhongzhuan_url = $zhongzhuanconfig['aliyun_zhongzhuan'];
            }else if($three_notify_address == 2){
                $three_zhongzhuan_url = $zhongzhuanconfig['hk_wuli_zhongzhuan'];
            }else if($three_notify_address == 3){
                $three_zhongzhuan_url = $zhongzhuanconfig['cbd_zhongzhuan_url'];
            }else if($three_notify_address == 4){
                $three_zhongzhuan_url = $zhongzhuanconfig['tengcent_cloud'];
            }else if($three_notify_address == 5){
                $three_zhongzhuan_url = $zhongzhuanconfig['hk61_wuli_zhongzhuan'];
            }else if($three_notify_address == 6){
                $three_zhongzhuan_url = $zhongzhuanconfig['35_zhongzhuan_url'];
            }else if($three_notify_address == 7){
                $three_zhongzhuan_url = $zhongzhuanconfig['73_zhongzhuan_url'];
            }else if($three_notify_address == 8){
                $three_zhongzhuan_url = $zhongzhuanconfig['aliyun_hangzhou_zhongzhuan'];
            }else if($three_notify_address == 11){
                $three_zhongzhuan_url = $zhongzhuanconfig['zhongzhuan_url'];
            }
        }

        $Config = new Config();
        $orginal_host = $Config->where(['name'=>'orginal_host'])->value('value');

        $noify = $request->host().','. $one_zhongzhuan_url . ',' . $two_zhongzhuan_url . ','.$three_zhongzhuan_url.','.'45.207.58.203';
        $noify = explode(',',$noify);
        $noify =  array_unique($noify);

        $noify = implode(',',$noify);

        $this->assign('notify_ip', $noify);
        return $this->fetch();
    }

    /**
     * 重置密钥 已删除
     */


    /**
     * 可用渠道
     *
     * @return mixed
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function channel()
    {

        //查询当前商户对应的支付产品
        $where['a.status'] = 1;
        $uid = is_login();
        $where['uid'] = $uid;
        $userCodes = $this->logicUser->userPayCodes($where, 'co_id,code,b.name as code_name', 'a.create_time desc', false);
        $userCodes = collection($userCodes)->toArray();
        if (is_array($userCodes) && !empty($userCodes)) {
            //随机一个支付产品的渠道账户对应的当前商户的费率
            foreach ($userCodes as $k => $paycode) {
                $urate = $this->logicUser->userCodeProfit($paycode['co_id'], $uid);
                $userCodes[$k]['urate'] = $urate;

            }
        }
        $this->assign('list', $userCodes);
        return $this->fetch();
    }


    /**
     * API公共
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function apiCommon()
    {
        $this->assign('api', $this->logicApi->getApiInfo(['uid' => is_login()]));

        $this->assign('rsa', $this->logicConfig->getConfigInfo(['name' => 'rsa_public_key'], 'value'));
//        $this->assign('notify_ip', $this->logicConfig->getConfigInfo(['name' => 'notify_ip'], 'value'));


    }




}
