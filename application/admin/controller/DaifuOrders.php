<?php
/**
 * Created by PhpStorm.
 * User: zhangxiaohei
 * Date: 2020/2/7
 * Time: 21:27
 */

namespace app\admin\controller;


use app\common\library\enum\CodeEnum;
use app\common\logic\MsMoneyType;
use app\common\logic\TgLogic;
use app\common\model\Admin;
use think\Cache;
use think\Config;
use think\Db;
use think\Exception;
use think\Log;
use app\api\service\DaifuPayment;
use think\Request;

class DaifuOrders extends BaseAdmin
{

    /**
     * @return mixed
     * 代付订单列表
     */
    public function index(){
        $where['update_time'] = $this->parseRequestDate3();
        if (is_admin_login() != 1){
//            $adminSonMs = Db::name('user')->where('admin_id',is_admin_login())->column('uid');
//            $where['uid'] = ['in',$adminSonMs];
            $where['admin_id'] = is_admin_login();
        }
        $where['chongzhen'] = 0;
    	$orderCal  = $this->logicDaifuOrders->calOrdersData($where);


    	 $daifu_logic = new \app\common\logic\Config();
        $daifu_err_reason = $daifu_logic->getConfigInfo(['name'=> 'daifu_err_reason']);
        // $this->assign('list', $this->logicConfig->getConfigList(['name'=> 'daifu_err_reason'],true,'sort ace'));
        $data = $this->logicConfig->getConfigList(['name'=> 'daifu_err_reason'])->toarray();
   
        $res = explode(",",$daifu_err_reason['value']);
     
        // print($this->logicConfig->getConfigList(['name'=> 'daifu_err_reason'])->toarray());
        $daifu_success_uplode = Db::name('config')->where(['name'=>'daifu_success_uplode','admin_id'=>is_admin_login()])->value('value');
        if (!empty($daifu_success_uplode) && $daifu_success_uplode == 2){
            $this->assign('daifu_chars',$daifu_success_uplode);
        }else{
            $this->assign('daifu_chars',1);
        }
        $this->assign('res',$res);
        $this->assign('fees',$orderCal);
        //所有的支付渠道
        return $this->fetch();
    }


    /**
     * 中转平台列表
     */
    public function transferlist(){
        $res = Db::name('admin')->where('id',is_admin_login())->value('daifu_auto_transfer');
        $this->assign('daifu_auto_transfer',$res);
        return $this->fetch();
    }

    public function getTransferList(){
        $where = [];
        $where['status'] = ['neq','-1'];
        if (is_admin_login() != 1){
            $where['admin_id'] = is_admin_login();
        }
        if (!empty($this->request->param('name'))){
            $where['name'] = $this->request->param('name');
        }
        $limit = $this->request->param('limit');

        $page = $this->request->param('page');

        $start=$limit*($page-1);


        if (!empty($this->request->param('time_type'))){
            if ($this->request->param('time_type') != 1){
               $time = 'update_time';
            }else{
                $time = 'create_time';
            }
        }else{
            $time = 'update_time';
        }

        $list = Db::name('daifu_orders_transfer')->where($where)->limit($start,$limit)->order('id asc')->select();
        $DaifuOrderModel = new \app\common\model\DaifuOrders();
        foreach ($list as $k=>$v){
            // 全部
            $OrderData = $DaifuOrderModel->where(['is_to_channel'=>2,'daifu_transfer_id'=>$v['id'],'chongzhen'=>0])->field('sum(amount) as money,count(id) as number')->find();
            $SuccessOrderData = $DaifuOrderModel->where(['is_to_channel'=>2,'daifu_transfer_id'=>$v['id'],'status'=>2,'chongzhen'=>0])->field('sum(amount) as success_money,count(id) as success_number')->find();
            $list[$k]['number'] = (float)$OrderData['number'];
            $list[$k]['money'] =  (float)$OrderData['money'];
            $list[$k]['success_number'] = (float)$SuccessOrderData['success_number'];
            $list[$k]['success_money'] = (float)$SuccessOrderData['success_money'];


            // 今天
            $todayOrderData = $DaifuOrderModel->whereTime($time, 'today')->where(['is_to_channel'=>2,'daifu_transfer_id'=>$v['id'],'chongzhen'=>0])->field('sum(amount) as today_money,count(id) as today_number')->find();
            $list[$k]['today_number'] = (float)$todayOrderData['today_number'];
            $list[$k]['today_money'] = (float)$todayOrderData['today_money'];
            $todaySuccessOrderData = $DaifuOrderModel->whereTime($time, 'today')->where(['is_to_channel'=>2,'daifu_transfer_id'=>$v['id'],'status'=>2,'chongzhen'=>0])->field('sum(amount) as today_success_money,count(id) as today_success_number')->find();
            $list[$k]['today_success_number'] = (float)$todaySuccessOrderData['today_success_number'];
            $list[$k]['today_success_money'] = (float)$todaySuccessOrderData['today_success_money'];


            // 昨天
            $yesterdayOrderData = $DaifuOrderModel->whereTime($time, 'yesterday')->where(['is_to_channel'=>2,'daifu_transfer_id'=>$v['id'],'chongzhen'=>0])->field('sum(amount) as yesterday_money,count(id) as yesterday_number')->find();
            $list[$k]['yesterday_number'] = (float)$yesterdayOrderData['yesterday_number'];
            $list[$k]['yesterday_money'] = (float)$yesterdayOrderData['yesterday_money'];
            $yesterdaySuccessOrderData = $DaifuOrderModel->whereTime($time, 'yesterday')->where(['is_to_channel'=>2,'daifu_transfer_id'=>$v['id'],'status'=>2,'chongzhen'=>0])->field('sum(amount) as yesterday_success_money,count(id) as yesterday_success_number')->find();
            $list[$k]['yesterday_success_number'] = (float)$yesterdaySuccessOrderData['yesterday_success_number'];
            $list[$k]['yesterday_success_money'] = (float)$yesterdaySuccessOrderData['yesterday_success_money'];

        }

        $count = Db::name('daifu_orders_transfer')->where($where)->count();

        if ($count > 0){
            return json([
                'code'=>0,
                'msg'=>'请求成功',
                'data'=>$list,
                'count'=>$count
            ]);
        }else{
            return json([
                'code'=>1,
                'msg'=>'暂无数据'
            ]);
        }

    }


    public function editAutoTransferStatus(){
        $status = $this->request->param('status') == 1?0:1;
//        $res = Ms::where(['userid' => $this->agent_id])->update(['work_status'=>$status]);
        $res = Db::name('admin')->where('id',is_admin_login())->update(['daifu_auto_transfer'=>$status]);
        if($res === false){
            return json([
                'code' => 404
            ]);
        }else{
            return json([
                'code' => 1
            ]);
        }
    }


    /**
     * 删除中转通道
     */
    public function delDaifuTransfer()
    {
        $id = $this->request->param('id');

        $daifuOrdersTransfer = Db::name('daifu_orders_transfer')->where(['id'=>$id])->find();

        if (!$daifuOrdersTransfer){
            return json(['code'=>2,'msg'=>'通道不存在！']);
        }

    
        $ret = Db::name('daifu_orders_transfer')->where(['id'=>$id])->update(['status'=>'-1']);

        if ($ret === false){
            return json(['code'=>2,'msg'=>'删除失败！']);
        }else{
            action_log('删除中转通道', '中转通道id：' . $id . ' 中转通道名称：' . $daifuOrdersTransfer['name']);
            return json(['code'=>1,'msg'=>'删除成功！']);
        }
        

    }

    public function addtransferlist(){
        if ($this->request->isPost()){
            $data = $this->request->param();
            $result = $this->validate([
                '__token__' => $this->request->post('__token__'),
            ],
            ['__token__' => 'require|token',]);

            if (true !== $result) {
                return json(['code'=>2,'msg'=>$result]);
            }

            unset($data['__token__']);
            $data['notify_url'] = $_SERVER['HTTP_ORIGIN'].'/api/Daifunotify/notify/channel/'.$data['controller'];
            $data['admin_id'] = is_admin_login();

            $isname = Db::name('daifu_orders_transfer')->where('name',$data['name'])->find();
            if ($isname){
                return ['code' => 2, 'msg' => '平台名称已存在！'];
            }

            if (empty($data['controller'])){
                return ['code' => 2, 'msg' => '不可用的代付模板'];
            }

                if(preg_match('/[0-9]/', $data['controller'], $matches, PREG_OFFSET_CAPTURE)) {
                    $unuese_str = substr($data['controller'],$matches[0][1],7);
                    $channel = str_replace($unuese_str,'',$data['controller']);
                    if (!class_exists($channel)){
                        return ['code' => 2, 'msg' => '不可用的代付模板'];
                    }

                }



            /*if (isset($data['notify_ips'])) {
                $auth_ips = $data['notify_ips'];
                $auth_ips = explode(',', $auth_ips) ? explode(',', $auth_ips) : [];
                //验证ip
                foreach ($auth_ips as $ip) {
                    $ip = trim($ip);
                    if (empty($ip)) {
                        continue;
                    }
                    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                        return ['code' => 2, 'msg' => 'ip格式填写错误'];
                    }
                }
            }*/

            // 修改notify_ips ，现在是传入得一个数组过来
            if (isset($data['notify_ips'])) {
                $new_ips = [];
                foreach ($data['notify_ips'] as $ip) {
                    $ip = trim($ip);
                    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                        return ['code' => 2, 'msg' => 'ip格式填写错误'];
                    }
                    $new_ips[] = $ip;
                }
                $auth_ips = implode(',', $new_ips);
                $data['notify_ips'] = $auth_ips;
            }else{
                $data['notify_ips'] = '';
            }



            $contreller = $data['controller'];
            unset($data['controller']);

            $channelId = Db::name('daifu_orders_transfer')->insertGetId($data);

            $contreller = $this->createAction($channelId,$contreller);
            $notify_url = $_SERVER['HTTP_ORIGIN'].'/api/Daifunotify/notify/channel/'.$contreller;
            db('daifu_orders_transfer')->where(['id'=>$channelId])->update([
                'controller'=>$contreller,
                'notify_url' => $notify_url
            ]);
            if ($channelId){
                return json(['code'=>1,'msg'=>'添加成功']);
            }
            return json(['code'=>2,'msg'=>'添加失败']);
        }
        return $this->fetch();
    }


    public function editDaifuTransfer(){
        if ($this->request->isPost()){
            $data = $this->request->param();
            $result = $this->validate([
                '__token__' => $this->request->post('__token__'),
            ],
                ['__token__' => 'require|token',]);

            if (true !== $result) {
                return json(['code'=>2,'msg'=>$result]);
            }

            unset($data['__token__']);
            $data['notify_url'] = $_SERVER['HTTP_ORIGIN'].'/api/Daifunotify/notify/channel/'.$data['controller'];
//            $data['admin_id'] = is_admin_login();

            /*if (isset($data['notify_ips'])) {
                $auth_ips = $data['notify_ips'];
                $auth_ips = explode(',', $auth_ips) ? explode(',', $auth_ips) : [];
                //验证ip
                foreach ($auth_ips as $ip) {
                    $ip = trim($ip);
                    if (empty($ip)) {
                        continue;
                    }
                    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                        return ['code' => 2, 'msg' => 'ip格式填写错误'];
                    }
                }
            }*/

            // 修改notify_ips ，现在是传入得一个数组过来
            if (isset($data['notify_ips'])) {
                $new_ips = [];
                foreach ($data['notify_ips'] as $ip) {
                    $ip = trim($ip);
                    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                        return ['code' => 2, 'msg' => 'ip格式填写错误'];
                    }
                    $new_ips[] = $ip;
                }
                $auth_ips = implode(',', $new_ips);
                $data['notify_ips'] = $auth_ips;
            }else{
                $data['notify_ips'] = '';
            }
            

            if (is_admin_login() != 1){
                $where['admin_id'] = is_admin_login();
            }
//            $res = Db::name('daifu_orders_transfer')->where('name',$data['name'])->find();
//            if ($res){
//                return ['code' => 2, 'msg' => '名称已经存在'];
//            }
            $where['id'] = $data['id'];
            $res = Db::name('daifu_orders_transfer')->where($where)->update($data);
            if ($res === false){
                return json(['code'=>2,'msg'=>'更新失败']);

            }
            return json(['code'=>1,'msg'=>'更新成功']);
        }
//        print_r($this->request->param());die;

        if (is_admin_login() != 1){
            $where['admin_id'] = is_admin_login();
        }
        $where['id'] = $this->request->param('id');
        $info = Db::name('daifu_orders_transfer')->where($where)->find();

        $this->assign('info',$info);
        return $this->fetch();
    }


    /**
     * 获取代付转发通道
     */
    public function getTransferDfChannel(){
//        $dfChannel = require_once('./data/conf/daifu.php');
        $where = [];
        if (is_admin_login() != 1){
                $where['admin_id'] = is_admin_login();
        }
        $where['status'] = 1;
        $dfChannel = Db::name('daifu_orders_transfer')->where($where)->field('id,name')->select();

        return json(['code'=>0,'data'=>$dfChannel]);
    }



    /**
     * 转发通道start
     */
    public function dfTransfer(){
        if ($this->request->isPost()){
            $dfInfo = Db::name('daifu_orders')->where('id',$this->request->param('id'))->find();
            if (is_admin_login() != 1){
                $where['admin_id'] = is_admin_login();
            }

            $where['status'] = 1;

            $where['id'] = $this->request->param('channel');
            $dfChannel = Db::name('daifu_orders_transfer')->where($where)->find();
            if (!empty($dfChannel['min_money'] )&&  $dfChannel['min_money'] * 100 != 0){
                if ($dfInfo['amount'] < $dfChannel['min_money']){
                    return json(['code'=>0,'msg'=>'超出最小出款金额 !']);
                }
            }
                //最大限额
                if (!empty($dfChannel['max_money'] )&&  $dfChannel['max_money'] * 100 != 0){
                    if ($dfInfo['amount'] > $dfChannel['max_money']){
                        return json(['code'=>0,'msg'=>'超出最大出款金额 !']);
                    }
                }


            Db::startTrans();
            try {
                $transfer = $this->modelDaifuOrders->lock(true)->where(['id' => $this->request->param('id')])->find();
                if (!$transfer|| $transfer['status']!=1) {
                    Db::rollback();
                    throw new Exception('订单不存在或者订单已处理，请刷新！');
                }
                $transfer->is_to_channel = 2;
                $transfer->status = 3;
                $transfer->daifu_transfer_name = $dfChannel['name'];
                $transfer->daifu_transfer_id = $dfChannel['id'];
                $res = $transfer->save();
                if ($res){
                    Log::notice('管理员'.is_admin_login().'中转代付订单：'.$dfInfo['out_trade_no'].'到'.$dfChannel['name']);
//                    return json($result);
                }else{
                    Db::rollback();
                    throw new Exception('订单状态更新失败！');
                }
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
                return json(['code'=>0,'msg'=>$e->getMessage()]);
            }

            $appChannel = [
                'channel' => $dfChannel['controller'],
                'action' => 'pay',
                'config' => $dfChannel
            ];
            list($payment,$action,$config) = array_values($appChannel);


            $result = DaifuPayment::$payment($config)->$action($dfInfo,$dfChannel);

            if ($result['code'] == 1){
                if ($dfChannel['is_query_balance'] == 1){
                    $appBalance = [
                        'channel' => $dfChannel['controller'],
                        'action' => 'balance',
                        'config' => $dfChannel
                    ];
                    list($payment,$action,$config) = array_values($appBalance);
                    if (!class_exists($payment)) {
                        if(preg_match('/[0-9]/', $payment, $matches, PREG_OFFSET_CAPTURE)) {
                            $unuese_str = substr($payment,$matches[0][1],7);
                            $payment = str_replace($unuese_str,'',$payment);
                        }
                    }
                    DaifuPayment::$payment($config)->$action($dfChannel);

                }
            }
            return json($result);
        }
    }

    public function query_balance(){
        $id = $this->request->param('id');
        $channel = Db::name('daifu_orders_transfer')->where('id',$id)->find();
        if (empty($channel)){
            return json(['code'=>0,'msg'=>'Error Data !']);
        }

        if (is_admin_login() != 1){
            if ($channel['admin_id'] != is_admin_login()){
                return json(['code'=>0,'msg'=>'非法操作 !']);
            }
        }

        if ($channel['is_query_balance'] != 1){
            return json(['code'=>0,'msg'=>'该通道未开启余额查询 !']);
        }

        $appBalance = [
            'channel' => $channel['controller'],
            'action' => 'balance',
            'config' => $channel
        ];
        list($payment,$action,$config) = array_values($appBalance);

        if (!class_exists($payment)) {
            if(preg_match('/[0-9]/', $payment, $matches, PREG_OFFSET_CAPTURE)) {
                $unuese_str = substr($payment,$matches[0][1],7);
                $payment = str_replace($unuese_str,'',$payment);
            }
        }
//    print_r($payment);die;

        DaifuPayment::$payment($config)->$action($channel);


        return json(['code'=>1,'msg'=>'查询成功 !']);


    }




    /**
     * 代付参数设置
     */
    public function setting(){
//        // $postData = input('post.');
//        // print_r($postData);die;
//        $this->common();
//
//        $this->assign('list', $this->logicConfig->getConfigList(['group'=> '3'],true,'sort ace'));
//        return $this->fetch();

        $config = require_once('./data/conf/adminSysPayCode.php');
        if ($this->request->isPost()){
            $data = $this->request->param();
            if (empty($data)){
                return json(['code'=>2,'msg'=>'没有数据哦']);
            }
            foreach ($data as $k=>$v){

                $admin_api_config = $config['admin_api_config'];
                foreach ($admin_api_config as $key=>$val){
                    if ($k == $val['name'] && $val['type'] == 255){
                        $insertData = $admin_api_config[$key];
                        unset($insertData['attr']);
                        $insertData['value'] = $v;
                        $insertData['admin_id'] = is_admin_login();
                        $insertData['create_time'] = time();
                        $insertData['update_time'] = time();
                        $where['admin_id'] = is_admin_login();
                        $where['name'] = $val['name'];
                        $where['type'] = $val['type'];
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

        $list = $config['admin_api_config'];
        foreach ($list as $k=>$v){
            if ($v['type']!=255) {
                unset($list[$k]);
                continue;
            }
            $where['admin_id'] = is_admin_login();
            $where['type'] = $v['type'];
            $where['name'] = $v['name'];
            $res = Db::name('config')->where($where)->find();
            if ($res){
                $list[$k] = $res;
                $list[$k]['attr'] = $v['attr'];
            }
        }
        $this->assign('list',$list);
        return $this->fetch();

    }

    /**
     * Common
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    private function common(){
        // print_r($this->request->post());die;
        $this->request->isPost() && $this->result(
            $this->logicConfig->settingSave(
                $this->request->post()
            )
        );
    }
    /**
     * 代付订单列表
     */
    public function getorderslist(){

        $where = [];
        //code
        //状态
        if ($this->request->param('status') != "") {
            $where['a.status'] = ['eq', $this->request->param('status')];
        }

        if ($this->request->param('chongzhen') != "") {
            $where['a.chongzhen'] = ['eq', $this->request->param('chongzhen')];
        }


        if ($this->request->param('notify_result') != "") {
            if ($this->request->param('notify_result') != 1){
                $where['a.notify_result'] = ['neq', 'SUCCESS'];
            }else{
                $where['a.notify_result'] = ['eq', 'SUCCESS'];
            }

        }


        !empty($this->request->param('daifu_transfer_name')) && $where['a.daifu_transfer_name']
            = ['eq', $this->request->param('daifu_transfer_name')];


        !empty($this->request->param('daifu_transfer_id')) && $where['a.daifu_transfer_id']
            = ['eq', $this->request->param('daifu_transfer_id')];

        !empty($this->request->param('orderNum')) && $where['a.trade_no']
            = ['eq', trim($this->request->param('orderNum'))];


        if (!empty($this->request->param('username'))){
            $ms_aid = Db::name('ms')->where('username',$this->request->param('username'))->value('admin_id');
            if (is_admin_login() != 1){
                if (is_admin_login() != $ms_aid){
                    $this->error('非法请求');
                }
            }
            $where['m.username'] = ['eq', trim($this->request->param('username'))];
        }

        if (!empty($this->request->param('time_type'))){
            if ($this->request->param('time_type') != 1){
                $where['a.update_time'] = $this->parseRequestDate3();
            }else{
                $where['a.create_time'] = $this->parseRequestDate3();
            }
        }else{
            $where['a.create_time'] = $this->parseRequestDate3();
        }


        !empty($this->request->param('bank_owner')) && $where['a.bank_owner']
            = ['eq', trim($this->request->param('bank_owner'))];

        !empty($this->request->param('trade_no')) && $where['a.out_trade_no']
            = ['eq', trim($this->request->param('trade_no'))];


        //组合搜索
        // !empty($this->request->param('trade_no')) && $where['trade_no']
        //   = ['eq', $this->request->param('trade_no')];


    if (is_admin_login() != 1){
//        $sonuser = Db::name('user')->where('admin_id',is_admin_login())->column('uid');
//        $where['a.uid'] = ['in',$sonuser];
        $where['a.admin_id'] = is_admin_login();
    }

        if (!empty($this->request->param('uid'))){
            if (is_admin_login() != 1){
                $u_aid = Db::name('user')->where('uid',$this->request->param('uid'))->value('admin_id');
                if ($u_aid != is_admin_login()){
                    $this->error('非法请求');
                }
            }
            $where['a.uid'] = ['eq', $this->request->param('uid')];

        }

        //时间搜索  时间戳搜素
        $where['create_time'] = $this->parseRequestDate3();

        if (!empty($this->request->param('trade_no'))){
            unset($where['create_time']);
        }



       $data =  $this->modelDaifuOrders
           ->alias('a')
            ->where($where)
            ->join('cm_ms m', 'a.ms_id = m.userid', 'left')
            ->field('a.*, m.username')
            ->order('create_time desc')
            ->paginate($this->request->param('limit', 10));

        foreach($data->items() as $key=>$val){
            $data->items()[$key]['uname'] =  Db::name('user')->where('uid',$val['uid'])->value('username');
            $data->items()[$key]['notify_result'] = htmlspecialchars($val['notify_result'], ENT_QUOTES, 'UTF-8');
        }
        $this->result($data->items() ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg'=> '',
                'count'=>$data->total(),
                'data'=>$data->items()
            ] : [
                'code' => CodeEnum::ERROR,
                'msg'=> '暂无数据',
                'count'=>$data->total(),
                'data'=>$data->items()
            ]);
    }


    /**
     * 审核成功
     */
    public function auditSuccess(){
        if (is_admin_login() != 1){
            $uid = Db::name('daifu_orders')->where('id',$this->request->post('id'))->value('uid');
            $admin_id = Db::name('user')->where('uid',$uid)->value('admin_id');
            if ($admin_id != is_admin_login()){
                $this->error('非法操作');
            }
        }
        $order = Db::name('daifu_orders')->where('id',$this->request->post('id'))->value('out_trade_no');
        Log::notice('管理员：'.is_admin_login().'强制完成代付订单：'.$order);
        action_log('代付完成','管理员'.session('admin_info')['username'].'强制完成代付订单：'.$order);
        $this->result($this->logicDaifuOrders->successOrder($this->request->post('id')));
    }

    /**
     * 驳回
     */
    public function auditError(){
        // $res = $this->modelDaifuOrders->where(['id' => $id])->update($up);
        $orders = Db::name('daifu_orders')->where('id',$this->request->post('id'))->find();
        if (is_admin_login() != 1){
            $uid = Db::name('daifu_orders')->where('id',$this->request->post('id'))->value('uid');
            $admin_id = Db::name('user')->where('uid',$uid)->value('admin_id');
            if ($admin_id != is_admin_login()){
                $this->error('非法操作');
            }

            $ms_3min_qidan = getAdminPayCodeSys('ms_3min_qidan',256,is_admin_login());
            if (empty($ms_3min_qidan)){
                $ms_3min_qidan = 2;
            }
            if ($ms_3min_qidan == 1){
                $is_qidan = Cache::get($orders['out_trade_no'].'_not_qidan');
                if ($is_qidan){
//            throw new Exception('抢单后3分钟内不允许弃单！');
                    $this->error('抢单后3分钟内不允许弃单！');
                }
            }
        }


        Db::name('daifu_orders')->where('id',$this->request->post('id'))->update(['error_reason'=>$this->request->post('reason')]);
        $order = Db::name('daifu_orders')->where('id',$this->request->post('id'))->value('out_trade_no');
        Log::notice('管理员：'.is_admin_login().'强制关闭代付订单：'.$order);
        action_log('驳回代付','管理员'.session('admin_info')['username'].'强制关闭代付订单：'.$order);
        $res = $this->logicDaifuOrders->errorOrder($this->request->post('id'),0);
        $notify_status= Db::name('api')->where('uid',$orders['uid'])->value('is_notify_status');
        if ($notify_status != 1){
            $this->modelDaifuOrders->where('id',$orders['id'])->update(['notify_result'=>'SUCCESS']);
        }else{
//            $status = ($status == 2) ? true : false;
            $this->logicDaifuOrders->retryNotify($orders['id'],false);
        }
        $this->result($res);
    }

    /**
     * 驳回
     */
    public function add_notify(){
        if (is_admin_login() != 1){
            $uid = Db::name('daifu_orders')->where('id',$this->request->post('id'))->value('uid');
            $admin_id = Db::name('user')->where('uid',$uid)->value('admin_id');
            if ($admin_id != is_admin_login()){
                $this->error('非法操作');
            }
        }
        $this->result($this->logicDaifuOrders->retryNotify($this->request->post('id')));
    }



    /**
     * @return mixed
     * 订单详情
     */
    public function details()
    {
        $where['a.id'] = $this->request->param('id', '0');
        //订单
        //$order = $this->logicDaifuOrders->getOrderInfo($where);

        $order = $this->modelDaifuOrders->alias('a')
            ->where($where)
            ->join('cm_ms m', 'a.ms_id = m.userid', 'left')
            ->field('a.*,m.username')
            ->find();

        $this->assign('order', $order);

        return $this->fetch();
    }


    /**
     * 查询订单金额
     * 按照简单的来
     */
    public function  searchOrderMoney(){

        //状态
        if ($this->request->param('status') != "") {
            $where['a.status'] = ['eq', $this->request->param('status')];
        }


        !empty($this->request->param('daifu_transfer_name')) && $where['a.daifu_transfer_name']
            = ['eq', $this->request->param('daifu_transfer_name')];

        if ($this->request->param('notify_result') != "") {
            if ($this->request->param('notify_result') != 1){
                $where['a.notify_result'] = ['neq', 'SUCCESS'];
            }else{
                $where['a.notify_result'] = ['eq', 'SUCCESS'];
            }

        }

        !empty($this->request->param('orderNum')) && $where['a.trade_no']
            = ['eq', $this->request->param('orderNum')];
        !empty($this->request->param('trade_no')) && $where['a.out_trade_no']
            = ['eq', $this->request->param('trade_no')];
        //组合搜索
        // !empty($this->request->param('trade_no')) && $where['trade_no']
        //   = ['eq', $this->request->param('trade_no')];



        if (!empty($this->request->param('username'))){
            $ms_id = Db::name('ms')->where('username',$this->request->param('username'))->field('userid,admin_id')->find();
            if (is_admin_login() != 1){
                if (is_admin_login() != $ms_id['admin_id']){
                    $this->error('非法请求');
                }
            }
            $where['a.ms_id'] = ['eq',$ms_id['userid']];
         }

        //时间搜索  时间戳搜素


        if (!empty($this->request->param('time_type'))){
            if ($this->request->param('time_type') != 1){
                $where['a.update_time'] = $this->parseRequestDate3();
            }else{
                $where['a.create_time'] = $this->parseRequestDate3();
            }
        }else{
            $where['a.update_time'] = $this->parseRequestDate3();
        }

        if (!empty($this->request->param('uid'))){
            if (is_admin_login() != 1){
                $u_aid = Db::name('user')->where('uid',$this->request->param('uid'))->value('admin_id');
                if ($u_aid != is_admin_login()){
                    $this->error('非法请求');
                }
            }
            $where['a.uid'] = ['eq', $this->request->param('uid')];

        }
        if ($this->request->param('chongzhen') != "") {
            $where['a.chongzhen'] = ['eq', $this->request->param('chongzhen')];
        }else{
            $where['a.chongzhen'] = 0;
        }
        if (is_admin_login() != 1){
//            $sonuser = Db::name('user')->where('admin_id',is_admin_login())->column('uid');
//            $where['a.uid'] = ['in',$sonuser];
            $where['a.admin_id'] = is_admin_login();
        }
        $orderCal  = $this->logicDaifuOrders->getOrdersAllStat($where)['fees'];
//        echo json_encode($where);

        $orderCal['percent'] =  $orderCal['paid_count']==0?0: sprintf("%.2f",$orderCal['paid_count']/$orderCal['total_count'])*100;

        exit(json_encode($orderCal));
        // echo  sprintf('%.2f',$searchTotalOrderAmount['searchTotalOrderAmount']);
    }


    /**
     * 导出订单
     */
    public function  exportOrder(){
        //组合搜索
        $where = [];
        //code
        //状态
        if ($this->request->param('status') != "") {
            $where['a.status'] = ['eq', $this->request->param('status')];
        }
        !empty($this->request->param('orderNum')) && $where['a.trade_no']
            = ['eq', $this->request->param('orderNum')];

        !empty($this->request->param('trade_no')) && $where['a.out_trade_no']
            = ['eq', $this->request->param('trade_no')];
        //组合搜索
        // !empty($this->request->param('trade_no')) && $where['trade_no']
        //   = ['eq', $this->request->param('trade_no')];

        if (is_admin_login() != 1){
//            $sonuser = Db::name('user')->where('admin_id',is_admin_login())->column('uid');
//            $where['a.uid'] = ['in',$sonuser];
            $where['a.admin_id'] = is_admin_login();
        }
        if (!empty($this->request->param('uid'))){
            if (is_admin_login() != 1){
                $u_aid = Db::name('user')->where('uid',$this->request->param('uid'))->value('admin_id');
                if ($u_aid != is_admin_login()){
                    $this->error('非法请求');
                }
            }
            $where['a.uid'] = ['eq', $this->request->param('uid')];

        }

        //时间搜索  时间戳搜素
        $where['a.create_time'] = $this->parseRequestDate3();
        //导出默认为选择项所有
        //$orderList = $this->logicDaifuOrders->getOrderList($where,true, 'create_time desc', false);
        $orderList = $this->modelDaifuOrders->alias('a')->where($where)->field('a.*,m.username')->join('ms m', 'a.ms_id = m.userid', 'left')->select();

        //组装header 响应html为execl 感觉比PHPExcel类更快
        $orderStatus =['订单关闭','等待支付','支付完成','处理中','异常订单'];
        $strTable ='<table width="500" border="1">';
        $strTable .= '<tr>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">ID标识</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">交易商户</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="100">打款单号</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">交易金额</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">交易手续费</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">交易方式</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">收款姓名</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">收款账号</td>';

        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">状态</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">创建时间</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">更新时间</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">卡商名称</td>';

//        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">码商号</td>';
//        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">支付渠道</td>';


        $strTable .= '</tr>';
        if(is_array($orderList)){
            foreach($orderList as $k=>$val){

                $strTable .= '<tr>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['id'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['uid'].'</td>';
                $strTable .= '<td style="text-align:center;font-size:12px;">&nbsp;'.$val['out_trade_no'].'</td>';
//                $strTable .= '<td style="text-align:center;font-size:12px;">&nbsp;'.$val['id'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['amount'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'. bcadd($val['service_charge'],$val['single_service_charge'], 2).'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">代付</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'. $val['bank_owner'] .'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'. $val['bank_number'] .'</td>';

                $strTable .= '<td style="text-align:left;font-size:12px;">'.$orderStatus[$val['status']].'</td>';

//                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['channel'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['create_time'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['update_time'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['username'].'</td>';
                $strTable .= '</tr>';
                unset($orderList[$k]);
            }
        }
        $strTable .='</table>';
        downloadExcel($strTable,'daifu_orders');
    }

    //充值申请列表
    public function applyList()
    {
        $where = [];
        //code
        //状态
        if ($this->request->param('status') != "") {
            $where['status'] = ['eq', $this->request->param('status')];
        }
        !empty($this->request->param('orderNum')) && $where['trade_no']
            = ['eq', $this->request->param('orderNum')];

        //按照商户号搜索
        if ($this->request->param('uid') != "") {
            $where['uid'] = ['eq', $this->request->param('uid')];
        }

        //时间搜索  时间戳搜素
        $where['create_time'] = $this->parseRequestDate3();

        $data = $this->logicDepositOrders->getOrderList($where, true, 'create_time desc',false);

        $count = $this->logicDepositOrders->getOrderCount($where);

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

    //驳回申请
    public function rejectApply($id)
    {
        $this->result($this->logicDepositeOrder->delDepositeCard(['id' => $id]));
        //1.设置申请充值订单状态为失败状态
    }

    //完成申请
    public function acceptApply($id)
    {

        $this->result($this->logicDepositeOrder->acceptApply(['id' => $id]));

    }

    /**
     * @return mixed
     *  充值银行卡首页
     */
    public function depositeCard()
    {
        return $this->fetch();
    }

    /**
     * 获取充值银行卡列表
     */
    public function getDepositeCardList(){
        $where = [];
        if ($this->request->param('status') != "") {
            $where['status'] = ['eq', $this->request->param('status')];
        }
        !empty($this->request->param('bank_account_username')) && $where['bank_account_username']
            = ['like', '%'.$this->request->param('bank_account_username').'%'];

        !empty($this->request->param('bank_account_number')) && $where['bank_account_number']
            = ['like', '%'.$this->request->param('bank_account_number').'%'];
        $fields = 'a.*,b.name';
        $data = $this->logicDepositeCard->getCardList($where, $fields, 'create_time desc',false);
        $count = $this->logicDepositeCard->getCardCount($where);
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



    //添加充值银行卡
    public function addDepositeCard()
    {
        $this->request->isPost() && $this->result($this->logicDepositeCard->saveCard($this->request->post(),'add'));
        $this->assign('bank',$this->logicBanker->getBankerList());
        return $this->fetch();
    }

    //编辑充值银行卡
    public function editDepositeCard()
    {

        $this->request->isPost() && $this->result($this->logicDepositeCard->saveCard($this->request->post(),'edit'));
        $this->assign('bank',$this->logicBanker->getBankerList());
        $this->assign('info',$this->logicDepositeCard->getCard($this->request->param('id')));
        return $this->fetch();
    }

    //删除充值银行卡
    public function delDepositeCard()
    {
        $this->result($this->logicDepositeCard->delCard(['id' => $this->request->param('id')]));
    }

    /**
     * 指定码商
     */
    public function appoint_ms()
    {
	    //todo 这里需要判断是通道还是码商 如果是通道的话就选择哪个通道，然后转发过去
	    $id = $this->request->param('id', '');
	    $ms_name = trim($this->request->param('ms_name', ''));
        if ($this->request->isAjax()){
            $this->modelDaifuOrders->startTrans();
            $ms_id = $this->request->param('ms_id');
            $order = $this->modelDaifuOrders->lock(true)->where(['id' => $id])->find();
            if ($order['ms_id'])
            {
           //     $this->error('该订单已分配码商！');
            }
            if (!$order){
               $this->error('订单不存在！');
	    }
            $ms = $this->modelMs->where('username', $ms_name)->find();
            if (!$ms) {
                $ms = $this->modelMs->where('userid', $ms_name)->find();
                if (!$ms){
                    $this->error('码商不存在！');
                }

	    }
            if (is_admin_login() != 1){
                if ($ms['admin_id'] != is_admin_login()){
                    $this->error('非法操作！');
                }
            }

	    $ms_id =  $ms['userid'];
            $order->ms_id = $ms_id;
            $order->status = 3;
            $order->save();
            $this->modelDaifuOrders->commit();

//            $Ms =  $this->modelMs->where('userid', $ms_id)->find();
//            if ($Ms && $Ms['tg_group_id']){
//                //发送信息给渠道飞机群
//                $TgLogic = new TgLogic(['tg_bot_type' => 'df']);
//                //文本信息
//		$sendText1 = '金额：' . intval($order['amount']).PHP_EOL.PHP_EOL.
//			     '姓名：' . $order['bank_owner'] . PHP_EOL .
//                             '卡号：' . $order['bank_number'] . PHP_EOL .
//                             '银行：' . $order['bank_name'] . PHP_EOL ;
//                $sendText2 = '请按照金额打款，不要多打和重复打款，否则无法追回，打款完麻烦发转账成功截图在群里，谢谢—';
//                $TgLogic->sendMessageTogroup($sendText1, $Ms['tg_group_id']);
//                $TgLogic->sendMessageTogroup($sendText2, $Ms['tg_group_id']);
//            }
            Log::notice('管理员'.is_admin_login().'将代付订单：'.$order['out_trade_no'].'指派给码商'.$ms['username']);
            action_log('代付指派','管理员'.session('admin_info')['username'].'将代付订单：'.$order['out_trade_no'].'指派给码商'.$ms['username']);
            $this->success('操作成功');
        }
//        $yhhMs =  $this->modelDaifuOrders->getYhkMs();
//        $where['status'] = 1;
//        $where['work_status'] = 1;
//        if (is_admin_login() != 1){
//            $where['admin_id'] = is_admin_login();
//        }
//        $yhhMs = Db::name('ms')->where($where)->select();
//        $this->assign('yhkMs', $yhhMs);
        $this->assign('id', $id);
        return $this->fetch();
    }



    /**
     * 代付商户统计
     */
    public function UserStats(){
        return $this->fetch();
    }

    /**
     * 获取代付商户统计
     */

    public function getUserStats()
    {
        $where = [];
        $where['status'] = ['neq','-1'];
        if (is_admin_login() != 1) {
            $where['admin_id'] = is_admin_login();
        }

        if (!empty($this->request->param('username'))) {
            $where['username'] = $this->request->param('username');
        }

        $userList = Db::name('user')->where($where)->field('uid,username,admin_id')->select();

        if (empty($userList)) {
            $this->result( [
                'code' => CodeEnum::SUCCESS,
                'msg' => '没有数据啦',
                'count' => 0,
                'data' => []
            ]);
        }

        $start = date('Y-m-d 00:00:00', time());
        $end = date('Y-m-d 23:59:59', time());
        if (!empty($this->request->param('start'))) {
            $start = $this->request->param('start');
        }

        if (!empty($this->request->param('end'))) {
            $end = $this->request->param('end');
        }


        if (!empty($this->request->param('time_type'))){
            if ($this->request->param('time_type') != 1){
                $where2['update_time'] = ['between time', [$start, $end]];
            }else{
                $where2['create_time'] = ['between time', [$start, $end]];
            }
        }else{
            $where2['update_time'] = ['between time', [$start, $end]];
        }


        $where2['chongzhen'] = 0;
        $uidList = array_column($userList, 'uid');

        $daifuOrders = Db::name('daifu_orders')
            ->whereIn('uid', $uidList)
            ->where($where2)
            ->field('uid, status, amount, service_charge, single_service_charge')
            ->select();

        $userStats = [];
        foreach ($daifuOrders as $order) {
            $uid = $order['uid'];
            if (!isset($userStats[$uid])) {
                $userStats[$uid] = [
                    'daifu_total' => 0,
                    'daifu_success_total' => 0,
                    'daifu_total_number' => 0,
                    'daifu_success_number' => 0,
                    'profit' => 0,
                ];
            }

            $userStats[$uid]['daifu_total'] += $order['amount'];
            $userStats[$uid]['daifu_total_number']++;

            if ($order['status'] == 2) {
                $userStats[$uid]['daifu_success_total'] += $order['amount'];
                $userStats[$uid]['daifu_success_number']++;
                $userStats[$uid]['profit'] += $order['service_charge'] + $order['single_service_charge'];
            }
        }

        foreach ($userList as $k => $v) {
            $uid = $v['uid'];
            if (isset($userStats[$uid])) {
                $userList[$k] = array_merge($v, $userStats[$uid]);
                $userList[$k]['success_rate'] = $userList[$k]['daifu_success_number'] == 0 ? 0 : sprintf("%.2f", $userList[$k]['daifu_success_number'] / $userList[$k]['daifu_total_number'] * 100);
            } else {
                unset($userList[$k]);
            }
        }

        $this->result($userList || !empty($userList) ?
            [
                'code' => 0,
                'msg' => '请求成功',
                'count' => count($userList),
                'data' => $userList,
            ] : [
                'code' => CodeEnum::SUCCESS,
                'msg' => '暂无数据',
                'count' => count($userList),
                'data' => []
            ]
        );
    }

    /**
     * 团队跑量统计
     */
    public function teamStats(){
        return $this->fetch();

    }

    /**
     * 获取团队跑量统计
     */
    public function getTeamStats(){

        //查询今日完成订单
        $orderWhere = [];
        if (is_admin_login() != 1){
            $adminSonMs = Db::name('ms')->where('admin_id',is_admin_login())->column('userid');
            $orderWhere['ms_id'] = ['in',$adminSonMs];
            $where['admin_id'] = ['in',$adminSonMs];
        }
        $orderWhere['status'] = 2;
        $start = date('Y-m-d 00:00:00',time());
        $end = date('Y-m-d 23:59:59',time());
        if (!empty($this->request->param('start'))){
            $start = $this->request->param('start');

        }
        if (!empty($this->request->param('end'))){
            $end = $this->request->param('end');
        }

        if (!empty($this->request->param('username'))){
            $username = trim($this->request->param('username'));
            $ms = Db::name('ms')->where(['username'=>$username,'status'=>1])->field('userid,pid,admin_id')->find();
            if (is_admin_login() != 1){
                if (is_admin_login() != $ms['admin_id']){
                    $this->result([
                        'code' => 1,
                        'msg' => '非法请求',
                        'count' => 0,
                        'data' => ''
                    ]);
                }
            }
//            if ($ms['pid'] != 0){
//                $this->result([
//                    'code' => 1,
//                    'msg' => '不是团长',
//                    'count' => 0,
//                    'data' => ''
//                ]);
//            }
            $this->son_id = [];
            $mss = $this->getIds($ms['userid']);

            array_unshift($mss, $ms['userid']);
//                        print_r($mss);die;
            $orderWhere['o.ms_id'] = ['in',$mss];
            $where['username'] = $this->request->param('username');
        }

        $orderWhere['chongzhen'] = 0;

        $orderWhere['update_time'] = ['between time',[$start,$end]];
        $order = Db::name('daifu_orders')->alias('o')->field('amount,ms_id')->where($orderWhere)->select();
        $order = $this->GetRepeatValGroup($order,'ms_id');
        $ms = [];
        foreach ($order as $k=>$v){
            $ms[$k]['total_amount'] = array_sum(array_column($v,'amount'));
        }

        $mslist = [];
        foreach ($ms as $k=>$v){
//            $mslist[$k]['username'] = Db::name('ms')->where('userid',$k)->value('username');
            $mslist[$k]['pid'] = Db::name('ms')->where('userid',$k)->value('pid');
            $mslist[$k]['total_amount'] = $v['total_amount'];
            $mslist[$k]['userid'] = $k;
        }
//                print_r($mslist);die;
//

        foreach ($mslist as $k=>$v){
            if ($v['pid'] != 0){
                $ffmsid = getNavPid($k);
                if(array_key_exists($ffmsid,$mslist)){
                    $mslist[$ffmsid]['total_amount'] = $mslist[$ffmsid]['total_amount'] + $v['total_amount'];
                }else{
                    $mslist[$ffmsid]['total_amount'] = 0 + $v['total_amount'];
//                    $mslist[$ffmsid]['username'] = Db::name('ms')->where('userid',$ffmsid)->value('username');
                    $mslist[$ffmsid]['pid'] = 0;
//                    $mslist[$ffmsid]['userid'] = $ffmsid;
//                    unset($mslist[$k]);
                }

            }
        }

        $where['status'] = 1;
        $where['level'] = 1;
        $where['pid'] = 0;
        if (is_admin_login() != 1){
            $where['admin_id'] = is_admin_login();
        }

        $topMs = Db::name('ms')->where($where)->select();
        $data = $this->logicMs->sortTrees($topMs,0,1);

        foreach ($data as $k=>$v){
            foreach ($mslist as $key=>$val){
                if ($key == $v['userid']){
                    $data[$k]['total_amount'] = sprintf("%.2f",$val['total_amount']);
                }
            }
        }


        $count = count($data);
      $this->result($data || !empty($data) ?
            [
                'code' => 0,
                'msg' => '请求成功',
                'count' => $count,
                'data' => $data,
            ] : [
                'code' => CodeEnum::SUCCESS,
                'msg' => '暂无数据',
                'count' => $count,
                'data' => $data
            ]
        );

    }

    public $son_id=array();

    public function getIds($parentid){
        $list=Db::name("ms")->where(["pid"=>$parentid])->field('pid,userid,username,level')->select();
        foreach ($list as $key => $value) {
            $this->son_id[]=$value['userid'];
            $this->getIds($value['userid']);
        }
        return $this->son_id;
    }

    public function GetRepeatValGroup($arr,$keys)
    {
        if(!is_array($arr) || !$keys) {
            return false;
        }
        $temp = array();
        foreach($arr as $key=>$val) {
            $temp[$val[$keys]][] = $val;
        }
        return $temp;
    }

    /**
     * 锁定代付订单
     */
    public function lock_df_operation()
    {

        $id = $this->request->post('id');
        $uid = $this->request->post('uid');
        $is_lock = $this->request->post('is_lock');
        $user = $this->modelUser->where('uid', $uid)->find();

        if ( !isset($user['admin_id']) or $user['admin_id'] != is_admin_login()){
            $this->error('非法操作');
        }

        $ret = true;
        $msg = '操作成功';

        Db::startTrans();
        try {
            $where = ['id' => $id, 'uid' => $uid, 'is_lock' => $is_lock];
            $order = $this->modelDaifuOrders->lock(true)->where($where)->find();
            if (!$order){
                $ret = false;
                $msg = '数据错误';
            }else if ($order['status'] != 1){
                $ret = false;
                $msg = '该订单已被码商接单';
            }else{
                $order->is_lock = $is_lock?0:1;
                $order->save();
            }
            Db::commit();

        }catch (\Exception $e){
            Db::rollback();
            Log::error('锁定代付订单error:' . $e->getMessage());
            $this->error('操作失败'. (Config::get('app_debug') ? $e->getMessage() : ''));
        }
        if ($ret){
            $this->success($msg);
        }else{
            $this->error($msg);
        }
    }


    /**
     * 代付冲正（订单完成后可以操作）
     */
    public function chongzhen(Request $request)
    {
        $id =  $request->param('id', 0, 'intval');
        $order = $this->modelDaifuOrders->where('id', $id)->find();
        if (empty($order)){
            $this->error('订单不存在');
        }
        if (is_admin_login() != 1){
            $User = new \app\common\model\User();
            $orderUser = $User->where('uid',$order['uid'])->value('admin_id');
            if (is_admin_login() != $orderUser){
                $this->error('非法请求！');
            }
        }

        if ($order['status'] != '2'){
            $this->error('订单状态错误');
        }
        if ($order['chongzhen'] == '1'){
            $this->error('订单已冲正');
        }

            Db::startTrans();
        try {
            //代付冲正  商户金额增加
            $this->logicBalanceChange->creatBalanceChange($order['uid'],
                $order['amount'] + $order['service_charge'] + $order['single_service_charge'],
                '代付订单' . $order['out_trade_no'] . '冲正,返还余额', 'enable',
                false,0,$order['out_trade_no'],
                $this->modelBalanceChange::CHONGZHENG);

            //代付冲正，减少码商余额
            accountLog($order['ms_id'], MsMoneyType::REVERSAL, 0, $order['amount'],  $order['trade_no']);


            //代付冲正, 扣除订单成功后给码商分润的金额
            $ms = $this->modelMs->where('userid', $order['ms_id'])->find();
            if ($ms['bank_rate'] && $ms['bank_rate'] * 100 != 0){
                $ms_bank_rate = $this->logicDaifuOrders->getAfterAddMsDfRate($order['ms_id']);
                $bonus =  sprintf('%.2f',$order['amount'] * $ms_bank_rate / 100);
                accountLog($order['ms_id'], MsMoneyType::REVERSAL, 0, $bonus,  $order['trade_no']);
            }

            //代付冲正,增加代付订单完成后减少的代理用户余额
            if (\app\common\logic\Config::isSysEnableRecharge()){
                $user = $this->logicUser->getUserInfo(['uid' => $order['uid']]);
                $admin = $this->modelAdmin->where(['id' => $user['admin_id']])->find( );
                $rate_amount = Admin::getDaifuPayAdminRateMoney($user['admin_id'], $order['amount']);
                $this->logicAdminBill->addBill($admin['id'], 6, 1, $rate_amount, '代付订单冲正增加代理用户费用');
            }
            $order->chongzhen = 1;
            $order->save();
            action_log('代付冲正','管理员'.session('admin_info')['username'].'冲正代付订单：'.$order['out_trade_no']);
            Db::commit();
            $daifuNotify = new \app\common\logic\DaifuOrders();
            $res = $daifuNotify->retryNotify($order['id'],false);
        }catch (\Exception $e){
            Db::rollback();
            Log::error('代付订单冲正error:' . $e->getMessage());
            $this->error($e->getMessage());
        }

        $this->success('操作成功');
    }

    /*
        搜索支付配置文件
    */
    public function searchConfig(){
        $api_url = trim($this->request->param('api_url'));
        //  $pay_address = 'http://www.baidu.com/gateway/dopay';
        $p =  parse_url($api_url);;
        $query ="";
        if(!empty($p['query']))
        {
            $query = "?".$p['query'];
        }
        $api_url = $p['path'].$query;;
        $data = [];
        $daifuConfigArr = require_once('./data/conf/daifuconfig.php');

        if(is_array($daifuConfigArr['template'])){
            foreach ($daifuConfigArr['template'] as $k=>$v){
                //$find = stripos($pay_address, $v);
                //	    var_dump($v['request_url'],$pay_address);
                if($api_url == '/'.$v['request_url']){
                    $data[] = $v;
                }
            }
        }
        $html = '<select lay-filter="template" style="display:block" name="controller" id="template" lay-verify="required">';
        if(!empty($data))
        {
            foreach($data as $d)
            {

                $html.= '<option value="'.$d['pay_controller'].'" >'.$d['name'].',参数:'.$d['param'].'</option>';

            }

        }
        $html.= '</select>';//<div class="layui-form-mid layui-word-aux">注：请选择模板</div>';
//	$html ='<input type=""';


        echo $html;
    }

    private function createAction($id,$start){
        //$start = 'XiangLong';
        $start = str_replace("Pay",'',$start);
        $fixId = $id < 1000 ? '0'.$id : $id;
        $fixId = sprintf("%03d", $fixId);
        $randNumber = substr ( md5($id.'abccdd'),0,1 );
        $end = 'Pay';
        return $start.$fixId.$randNumber.$end;
    }


    //关闭订单确认是否有可接单码商
    public function closeOrderAffirm()
    {
        $okMsList = $this->modelMs->where([
            'is_daifu' => 1,
            'work_status' => 1,
            'admin_id' => is_admin_login()
        ])->field('userid,username')->select();

        if (!empty($okMsList)){
            return json(['code' => 1, 'msg' => '当前有可接单码商，确认关闭订单？']);
        }
        return json(['code' => 0]);
    }


    /**
     * 补发代付
     */
    public function dfTransferBf()
    {
        $where = [
            'id' => $this->request->param('id'),
            'status' => 3,
            'is_to_channel' => 2
        ];
        $daifuOrder =  Db::name('daifu_orders')->where($where)->find();
        if (!$daifuOrder){
            $this->error('订单状态错误，不能补发，请刷页面！');
        }

        $dfChannel = Db::name('daifu_orders_transfer')->where(['name'=> $daifuOrder['daifu_transfer_name'], 'status' => 1])->find();
        if (!$dfChannel){
            $this->error('转发平台不存在');
        }

        $appChannel = [
            'channel' => $dfChannel['controller'],
            'action' => 'pay',
            'config' => $dfChannel
        ];
        list($payment,$action,$config) = array_values($appChannel);

        $result = DaifuPayment::$payment($config)->$action($daifuOrder,$dfChannel);


        if ($result['code'] == 1){
            if ($dfChannel['is_query_balance'] == 1){
                $appBalance = [
                    'channel' => $dfChannel['controller'],
                    'action' => 'balance',
                    'config' => $dfChannel
                ];
                list($payment,$action,$config) = array_values($appBalance);
                if (!class_exists($payment)) {
                    if(preg_match('/[0-9]/', $payment, $matches, PREG_OFFSET_CAPTURE)) {
                        $unuese_str = substr($payment,$matches[0][1],7);
                        $payment = str_replace($unuese_str,'',$payment);
                    }
                }
                DaifuPayment::$payment($config)->$action($dfChannel);

            }
        }else{
            $this->error('补发失败');
        }
        $this->success('补发成功');

    }

    public function orderInfo()
    {
       $id = $this->request->param('id');
       $order = $this->modelDaifuOrders->where(['id' => $id])->find();
       if (empty($order)){
           $this->error('订单不存在');
       }
       return json(['code'=>1,'data'=>$order]);
    }

    /**
     * 代付统计导出
     */
    public function exportDaifuStats()
    {
        $where = [];
        $where['status'] = ['neq','-1'];
        if (is_admin_login() != 1) {
            $where['admin_id'] = is_admin_login();
        }

        if (!empty($this->request->param('username'))) {
            $where['username'] = $this->request->param('username');
        }

        $userList = Db::name('user')->where($where)->field('uid,username,admin_id')->select();


        $start = date('Y-m-d 00:00:00', time());
        $end = date('Y-m-d 23:59:59', time());
        if (!empty($this->request->param('start'))) {
            $start = $this->request->param('start');
        }

        if (!empty($this->request->param('end'))) {
            $end = $this->request->param('end');
        }


        if (!empty($this->request->param('time_type'))){
            if ($this->request->param('time_type') != 1){
                $where2['update_time'] = ['between time', [$start, $end]];
            }else{
                $where2['create_time'] = ['between time', [$start, $end]];
            }
        }else{
            $where2['update_time'] = ['between time', [$start, $end]];
        }


        $where2['chongzhen'] = 0;
        $uidList = array_column($userList, 'uid');

        $daifuOrders = Db::name('daifu_orders')
            ->whereIn('uid', $uidList)
            ->where($where2)
            ->field('uid, status, amount, service_charge, single_service_charge')
            ->select();

        $userStats = [];
        foreach ($daifuOrders as $order) {
            $uid = $order['uid'];
            if (!isset($userStats[$uid])) {
                $userStats[$uid] = [
                    'daifu_total' => 0,
                    'daifu_success_total' => 0,
                    'daifu_total_number' => 0,
                    'daifu_success_number' => 0,
                    'profit' => 0,
                ];
            }

            $userStats[$uid]['daifu_total'] += $order['amount'];
            $userStats[$uid]['daifu_total_number']++;

            if ($order['status'] == 2) {
                $userStats[$uid]['daifu_success_total'] += $order['amount'];
                $userStats[$uid]['daifu_success_number']++;
                $userStats[$uid]['profit'] += $order['service_charge'] + $order['single_service_charge'];
            }
        }

        foreach ($userList as $k => $v) {
            $uid = $v['uid'];
            if (isset($userStats[$uid])) {
                $userList[$k] = array_merge($v, $userStats[$uid]);
                $userList[$k]['success_rate'] = $userList[$k]['daifu_success_number'] == 0 ? 0 : sprintf("%.2f", $userList[$k]['daifu_success_number'] / $userList[$k]['daifu_total_number'] * 100);
            } else {
                unset($userList[$k]);
            }
        }

        //组装header 响应html为execl 感觉比PHPExcel类更快
        $strTable = '<table width="500" border="1">';
        $strTable .= '<tr>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">商户UID</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">商户名</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="100">代付订单总数</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">代付完成订单总数</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">代付交易总额</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">代付订单成功总额</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">代付利润</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">成功率（%）</td>';

        $strTable .= '</tr>';

        if (is_array($userList)) {
            foreach ($userList as $k => $val) {
                $strTable .= '<tr>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['uid'] . ' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['username'] . ' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['daifu_total_number'] . ' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['daifu_success_number'] . ' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['daifu_total'] . ' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['daifu_success_total'] . ' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['profit'] . ' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['success_rate'] . ' </td>';
                $strTable .= '</tr>';
                unset($userList[$k]);
            }
            $strTable .= '</table>';
            downloadExcel($strTable, 'daifu_orders_stats');
        }
        $this->error("暂无导出记录");

    }
}
