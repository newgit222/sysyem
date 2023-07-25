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


use app\common\library\enum\CodeEnum;
use think\Db;

class Pay extends BaseAdmin
{
    /**
     * 通道配置
     */
    public function sys_paycode(){
        $config = require_once('./data/conf/adminSysPayCode.php');
        if ($this->request->isPost()){
            $data = $this->request->param();
            if (empty($data)){
                return json(['code'=>2,'msg'=>'没有数据哦']);
            }
//            print_r($data);die;
            foreach ($data as $k=>$v){
                $admin_api_config = $config['admin_api_config'];
                foreach ($admin_api_config as $key=>$val){
                    if ($k == $val['name'] && ($val['type'] == $data['id'] or $val['type'] == 0)){
                        $insertData = $admin_api_config[$key];
                        if ($val['type'] == 0){
                            $insertData['type'] = $data['id'];
                        }
                        $insertData['value'] = $v;
                        $insertData['admin_id'] = is_admin_login();
                        $insertData['create_time'] = time();
                        $insertData['update_time'] = time();
                        //var_dump($insertData);
                        $where['admin_id'] = is_admin_login();
                        $where['name'] = $val['name'];
                        $where['type'] = ($val['type'] == 0) ? $data['id'] : $val['type']  ;
                        unset($insertData['attr']);
                        $is = $this->logicConfig->getConfigInfo($where,true);
                        if ($is){
                            //更新
                                unset($insertData['create_time']);
                                Db::name('config')->where($where)->update($insertData);
                        }else{
                            //添加
                            Db::name('config')->insert($insertData);
                        }
                    }
                }
            }
//            print_r($insertData);die;
            return json(['code'=>1,'msg'=>'设置成功']);
        }
        $id = $this->request->param('id');

        $list = $config['admin_api_config'];
        foreach ($list as $k=>$v){
            $where['admin_id'] = is_admin_login();
            $where['type'] = ($v['type'] == 0) ? $this->request->param('id') : $v['type'];
            $where['name'] = $v['name'];
            $res = Db::name('config')->where($where)->find();
            if ($res){
                if ($v['attr'] == 'select'){
//                    unset($res['extra']);
                    $res['extra'] = $v['extra'];
                }
                $list[$k] = $res;
                $list[$k]['attr'] = $v['attr'];

            }
        }
        $this->assign('list',$list);
        $this->assign('id',$id);
        return $this->fetch();
    }


    public function sysNullRate(){
        if ($this->request->isPost()){
            $where = [];
            $pay_code_id = $this->request->post('pay_code_id');
            $where['code_type_id'] = $pay_code_id;
            if (is_admin_login() != 1){
                $msids = Db::name('ms')->where('admin_id',is_admin_login())->column('userid');
                $where['ms_id'] = ['in',$msids];
            }
            $res = Db::name('ms_rate')->where($where)->delete();
            if ($res){
                return json(['code'=>1,'msg'=>'设置成功']);
            }else{
                return json(['code'=>0,'msg'=>'异常操作']);
            }

        }
    }

    public function sysMsRate(){
        if ($this->request->isPost()){
            $where = [];
            if (is_admin_login() != 1) {
                $adminSonMs = Db::name('ms')->where('admin_id', is_admin_login())->column('userid');
                $where['ms_id'] = ['in', $adminSonMs];
            }
            $tz_rate = trim($this->request->post('tz_rate'));
            if ($tz_rate < 0) {
                $tz_rate = 0;
            }
            $pay_code_id = $this->request->post('pay_code_id');
            $status = $this->request->post('status');
            $where['code_type_id'] = $pay_code_id;
            $msRate = Db::name('ms_rate')->where($where)->select();

            Db::transaction(function () use ($status, $tz_rate, $msRate) {
                if ($status == 1) {
                    foreach ($msRate as $k => $v) {
                        // 设置新的费率，确保它不低于0
                        $new_rate = max($v['rate'] + $tz_rate, 0);
                        Db::name('ms_rate')->where('id', $v['id'])->update(['rate' => $new_rate]);
                    }
                } else {
                    foreach ($msRate as $k => $v) {
                        // 设置新的费率，确保它不低于0
                        if ($v['rate'] - $tz_rate < 0){
                            $new_rate = $v['rate'];
                        }else{
                            $new_rate = $v['rate'] - $tz_rate;
                        }

                        Db::name('ms_rate')->where('id', $v['id'])->update(['rate' => $new_rate]);
                    }
                }
            });


            return json(['code'=>1,'msg'=>'设置成功']);

        }
        return json(['code'=>0,'msg'=>'非法请求']);
    }

    /**
     * 支付方式
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 支付渠道
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function channel(){
        return $this->fetch();
    }


    /**
     * 渠道回收站
     * @return mixed
     */
    public function channelHs(){
        return $this->fetch();
    }



    /**
     * 支付渠道账户
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function account(){
        $this->assign('cnl_id',$this->request->param('cnl_id'));
        return $this->fetch();
    }

    /**
     * 银行
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function bank(){
        return $this->fetch();
    }

    /**
     * 支付渠道列表
     * @url getChannelList?page=1&limit=10
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function getCodeList(){

        $where = [];
        //code
        !empty($this->request->param('code')) && $where['code']
            = ['eq', $this->request->param('code')];
        //name
        !empty($this->request->param('name')) && $where['name']
            = ['like', '%'.$this->request->param('name').'%'];

        $data = $this->logicPay->getCodeList($where, true, 'create_time desc', false);
        foreach ($data as $v){
            $v['admin_id'] = is_admin_login();
            $channel_show = Db::name('config')->where(['type'=>$v['id'],'name' => 'channel_show','admin_id' => is_admin_login()])->value('value');
            $v['channel_show'] = $channel_show ?? 1;
        }

        $count = $this->logicPay->getCodeCount($where);

        $this->result($data || !empty($data) ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg'=> '',
                'count'=>$count,
                'data'=>$data
            ] : [
                'code' => CodeEnum::ERROR,
                'msg'=> '暂无数据',
                'count'=>$count,
                'data'=>$data
            ]);
    }


    /*
   *用户支付渠道pay_code
   *
   */
    public function codes()
    {
        $co_id= $this->request->param('id');
        if ($this->request->isPost())
        {
            $data = $this->request->post('r/a');
//            echo json_encode($data);die();
            $this->result($this->logicUser->doPayCodesUser($co_id,$data));
        }
        $data = $this->logicUser->getUserList([], true, 'create_time desc', false);
        foreach($data as $k=>$v)
        {
            $userCode =$this->logicUser->userPayCode(['uid' =>$v['uid'],'co_id'=>$co_id]);
            $data[$k]['status'] = $userCode?$userCode['status']:-1;
        }
        $this->assign('list', $data);;
        return $this->fetch();
    }

    /**
     * 开启全部商户code
     */
    public function openUserCode(){
        $co_id= $this->request->param('id');
        $this->result($this->logicUser->setUserCodeStatus($co_id,'1'));
    }

    /**
     * 关闭全部商户code
     */
    public function closeUserCode(){
        $co_id= $this->request->param('id');
        $this->result($this->logicUser->setUserCodeStatus($co_id,'0'));
    }





    /**
     * 支付渠道列表
     * @url getChannelList?page=1&limit=10
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function getChannelList(){

        $where = [];
        //组合搜索
        !empty($this->request->param('id')) && $where['id']
            = ['eq', $this->request->param('id')];
        //name
        !empty($this->request->param('name')) && $where['name']
            = ['like', '%'.$this->request->param('name').'%'];


        $data = $this->logicPay->getChannelList($where,true, 'create_time desc',false);
        $where['status'] = 1;
        $count = $this->logicPay->getChannelCount($where);

        $this->result($data || !empty($data) ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg'=> '',
                'count'=>$count,
                'data'=>$data
            ] : [
                'code' => CodeEnum::ERROR,
                'msg'=> '暂无数据',
                'count'=>$count,
                'data'=>$data
            ]);
    }

    public function getChannelHsList(){

        $where = [];
        //组合搜索
        !empty($this->request->param('id')) && $where['id']
            = ['eq', $this->request->param('id')];
        //name
        !empty($this->request->param('name')) && $where['name']
            = ['like', '%'.$this->request->param('name').'%'];

        $where['status'] = -1;

        $data = $this->logicPay->getChannelList($where,true, 'create_time desc',false);
        $count = $this->logicPay->getChannelCount($where);

        $this->result($data || !empty($data) ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg'=> '',
                'count'=>$count,
                'data'=>$data
            ] : [
                'code' => CodeEnum::ERROR,
                'msg'=> '暂无数据',
                'count'=>$count,
                'data'=>$data
            ]);
    }

    /**
     * 支付渠道账户列表
     * @url getChannelList?page=1&limit=10
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function getAccountList(){
        $where = [];
        $cnl_id = $this->request->param('cnl_id',0,'intval');
        if($cnl_id)
        {
            $where = [
                'cnl_id' =>$cnl_id,
            ];
        }

        //组合搜索
        !empty($this->request->param('id')) && $where['id']
            = ['eq', $this->request->param('id')];
        //name
        !empty($this->request->param('name')) && $where['name']
            = ['like', '%'.$this->request->param('name').'%'];


        $data = $this->logicPay->getAccountList($where,true, 'create_time desc',false);

        $count = $this->logicPay->getAccountCount($where);

        $this->result($data || !empty($data) ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg'=> '',
                'count'=>$count,
                'data'=>$data
            ] : [
                'code' => CodeEnum::ERROR,
                'msg'=> '暂无数据',
                'count'=>$count,
                'data'=>$data
            ]);
    }

    /**
     * 支付渠道列表
     * @url getChannelList?page=1&limit=10
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function getBankList(){

        $where = [];
        //组合搜索
        !empty($this->request->param('keywords')) && $where['id|name']
            = ['like', '%'.$this->request->param('keywords').'%'];

        $data = $this->logicBanker->getBankerList($where,true, 'create_time desc',false);

        $count = $this->logicBanker->getBankerCount($where);

        $this->result($data || !empty($data) ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg'=> '',
                'count'=>$count,
                'data'=>$data
            ] : [
                'code' => CodeEnum::ERROR,
                'msg'=> '暂无数据',
                'count'=>$count,
                'data'=>$data
            ]);
    }

    /**
     * 新增支付渠道
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function addChannel()
    {
        // post 是提交数据
        $this->request->isPost() && $this->result($this->logicPay->saveChannelInfo($this->request->post()));
        return $this->fetch();
    }

    /**
     * 新增渠道账户
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function addAccount()
    {
        // post 是提交数据
        $this->request->isPost() && $this->result($this->logicPay->saveAccountInfo($this->request->post()));

        //获取渠道列表
        $channel = $this->logicPay->getChannelList([], true, 'create_time desc',false);
        //获取方式列表
        $codes = $this->logicPay->getCodeList([], true, 'create_time desc',false);

        $this->assign('cnl_id',$this->request->param('cnl_id'));

        $this->assign('channel',$channel);
        $this->assign('codes',$codes);

        return $this->fetch();
    }

    /**
     * 新增支付方式
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function addCode()
    {
        // post 是提交数据
        $this->request->isPost() && $this->result($this->logicPay->saveCodeInfo($this->request->post()));
        //支持渠道列表
        $this->assign('channel',$this->logicPay->getChannelList([],'id,name','id asc'));

        return $this->fetch();
    }

    /**
     * 新增支付银行
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function addBank()
    {
        // post 是提交数据
        $this->request->isPost() && $this->result($this->logicBanker->saveBankerInfo($this->request->post()));

        return $this->fetch();
    }

    /**
     * 编辑支付渠道
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function editChannel(){
        // post 是提交数据
        $this->request->isPost() && $this->result($this->logicPay->saveChannelInfo($this->request->post()));
        //获取渠道详细信息
        $channel = $this->logicPay->getChannelInfo(['id' =>$this->request->param('id')]);
        //时间转换
        $channel['timeslot'] = json_decode($channel['timeslot'],true);
        //ip装换
        $channel['notify_ips']  = explode(',',$channel['notify_ips']);
        $this->assign('channel',$channel);
        return $this->fetch();
    }

    /**
     * 编辑支付渠道
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function editAccount(){
        // post 是提交数据
        $this->request->isPost() && $this->result($this->logicPay->saveAccountInfo($this->request->post()));
        //获取账户详细信息
        $account = $this->logicPay->getAccountInfo(['id' =>$this->request->param('id')]);
        //时间转换
        $account['timeslot'] = json_decode($account['timeslot'],true);
        //获取方式列表
        $codes = $this->logicPay->getCodeList([], true, 'create_time desc',false);
        //获取渠道列表
        $channels = $this->logicPay->getChannelList([], true, 'create_time desc',false);

        $this->assign('codes',$codes);
        $this->assign('channels',$channels);
        $this->assign('account',$account);

        return $this->fetch();
    }

    /**
     * 编辑支付方式
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function editCode(){
        // post 是提交数据

        $this->request->isPost() && $this->result($this->logicPay->saveCodeInfo($this->request->post()));
        //支持渠道列表
        $this->assign('channel',$this->logicPay->getChannelList([],'id,name','id asc',false));
        //获取支付方式详细信息
        $this->assign('code',$this->logicPay->getCodeInfo(['id' =>$this->request->param('id')]));
        return $this->fetch();
    }

    /**
     * 编辑支付银行
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function editBank(){
        // post 是提交数据
        $this->request->isPost() && $this->result($this->logicBanker->saveBankerInfo($this->request->post()));
        //获取支付方式详细信息
        $this->assign('bank',$this->logicBanker->getBankerInfo(['id' =>$this->request->param('id')]));

        return $this->fetch();
    }

    public function delBank(){
        $this->request->isPost() && $this->result($this->logicBanker->delBankerInfo(['id' =>$this->request->param('id')]));
    }

    /**
     * 删除支付方式
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function delCode(){
        // post 是提交数据
        $this->request->isPost() && $this->result(
            $this->logicPay->delCode(
                [
                    'id' => $this->request->param('id')
                ])
        );

        // get 直接报错
        $this->error('未知错误');
    }

    /**
     * 删除渠道
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function delChannel(){
        // post 是提交数据
        $this->request->isPost() && $this->result(
            $this->logicPay->delChannel(
                [
                    'id' => $this->request->param('id')
                ])
        );

        // get 直接报错
        $this->error('未知错误');
    }


    public function deleteChannel(){
        // post 是提交数据
        $this->request->isPost() && $this->result(
            $this->logicPay->deleteChannel(
                [
                    'id' => $this->request->param('id')
                ])
        );

        // get 直接报错
        $this->error('未知错误');
    }



    /*
     *还原channel
     *
     */
    public function hyChannel(){
        // post 是提交数据
        $this->request->isPost() && $this->result(
            $this->logicPay->hyChannel(
                [
                    'id' => $this->request->param('id')
                ])
        );

        // get 直接报错
        $this->error('未知错误');
    }






    /**
     * 删除渠道账户
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function delAccount(){
        // post 是提交数据
        $this->request->isPost() && $this->result(
            $this->logicPay->delAccount(
                [
                    'id' => $this->request->param('id')
                ])
        );

        // get 直接报错
        $this->error('未知错误');
    }


    /*
     *批量设置用户对应某个渠道账户的分润会覆盖掉以前的
     *
     */
    public function  profitByAccount()
    {

         $urate  =$this->request->param('urate');
         $where =  [
             'id' => $this->request->param('id')
         ];
         $account = $this->logicPay->getAccountInfo($where);
        //所有商户
        $users= $this->logicUser->getUserList([], '*', $order = 'uid desc', $paginate = false);
        foreach($users as $user)
        {
            $profit = $this->logicUser->getUserProfitInfo(['uid' => $user['uid'], 'cnl_id' => $where['id']]);
            if ($profit) {
                $data_update[] = [
                    'id' => $profit['id'],
                    'uid' => $user['uid'],
                    'cnl_id' => $where['id'],
                    'urate' => $urate,
                    'grate' => $account['grate']
                ];
            } else {
                $data_update[] = [
                    'uid' => $user['uid'],
                    'cnl_id' =>$where['id'],
                    'urate' => $urate,
                    'grate' => $account['grate']
                ];
            }
        }

        $this->result($this->logicUser->saveUserProfit($data_update));
    }


    /*
     *为每个pay_code赋值渠道权重
     * @return mixed
     */
    public function editCodeChannelWeight()
    {

        if ($this->request->isPost()) {
            $weight = $this->request->param('cnl_weight/a');
            $weight = json_encode($weight);
            $ret  =$this->modelPayCode->where(['id'=>$this->request->param('id')])->setField('cnl_weight',$weight);
            if($ret!==false)
            {
               $this->result(['code'=>CodeEnum::SUCCESS,'msg'=>'执行成功']);
            }
            $this->result(['code'=>CodeEnum::ERROR,'msg'=>'执行失败']);

        }
        //当前pay_code 渠道权重信息
        $codeInfo = $this->logicPay->getCodeInfo(['id' => $this->request->param('id')]);
        //channel
        $channels = $this->logicPay->getChannelList(['id' => ['in', $codeInfo['cnl_id'], 'status' => ['eq', '1']]],'id,name',['id'=>'asc'],false);
        if ($channels)
        {
            $codeWeight = json_decode($codeInfo['cnl_weight'],true);
            foreach($channels as $k=>$channel)
            {
                $channels[$k]['weight'] = (empty($codeWeight) || !isset($codeWeight[$channel['id']]))?0:$codeWeight[$channel['id']];
            }
        }
        $this->assign('list', $channels);;
        return $this->fetch('code_weight');
    }

    /**
     *解绑此商户的TG群
     */
    public function unblindTgGroup()
    {
        $userId = $this->request->param('id');
        if (!$userId) {
            $this->error('非法操作');
        }
        $result = \app\common\model\PayChannel::where(['id' => $userId])->update(['tg_group_id' => '']);
        if ($result !== false) {
            $this->success('操作成功');
        }
        $this->error('错误请重试');

    }

    //通道显示开关
    public function channelShowSwitch()
    {
        $id = $this->request->param('id');
        $channel_show = $this->request->param('channel_show');

        if (!$id || !in_array($channel_show, [1, 2])) {
            $this->error('非法操作');
        }
        $result = model('Config')::where([
            'name' => 'channel_show',
            'type' => $id,
            'admin_id' => is_admin_login()
        ])->find();

        if (!empty($result)){
            $result->value = $channel_show;
            $result->save();
        }else{
            $result = model('Config')::create([
                'name' => 'channel_show',
                'type' => $id,
                'title' => '是否展示通道',
                'create_time' => time(),
                'update_time' => time(),
                'value' => $channel_show,
                'admin_id' => is_admin_login()
            ]);
        }

        if ($result !== false) {
            $this->success('操作成功');
        }
        $this->error('错误请重试');
    }
}