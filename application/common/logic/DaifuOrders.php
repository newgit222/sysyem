<?php
/**
 * Created by PhpStorm.
 * User: zhangxiaohei
 * Date: 2020/2/7
 * Time: 21:54
 */

namespace app\common\logic;


use app\api\service\DaifuPayment;
use app\common\library\exception\OrderException;
use app\common\model\Admin;
use think\Db;
use think\Exception;
use think\migration\command\seed\Run;
use think\Log;

class DaifuOrders extends BaseLogic
{


    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->setParam();
    }

    /**
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int $paginate
     * @return mixed
     * 获取订单列表
     */
    public function getOrderList($where = [], $field = true, $order = 'create_time desc', $paginate = 15)
    {
        $this->modelDaifuOrders->limit = !$paginate;
        return $this->modelDaifuOrders->getList($where, $field, $order, $paginate);
    }

    /**
     * @param array $where
     * @return mixed
     * 获取订单总数
     */
    public function getOrderCount($where = [])
    {
        return $this->modelDaifuOrders->getCount($where);
    }


    /*
     * 统计订单相关数据
     *
     * @param array $where
     */
    public function calOrdersData($where = [])
    {
        $where['chongzhen'] = 0;
        //订单总金额
        $data['total_money'] = $this->modelDaifuOrders->where($where)->value('sum(amount) as total_mount');
        //订单总订单数量
        $data['total_count'] = $this->modelDaifuOrders->where($where)->count('id');
        //订单完成金额
        $where['status'] = 2;
        $data['total_finish_money'] = $this->modelDaifuOrders->where($where)->value('sum(amount) as total_mount');
        //完成订单数量
        $data['total_finish_count'] = $this->modelDaifuOrders->where($where)->count('id');
        //成功率
        if ($data['total_finish_count'] == 0 || $data['total_count'] == 0) {
            $success_percent = '0.00';
        } else {
            $success_percent = sprintf("%.2f", $data['total_finish_count'] / $data['total_count']);
        }
        $data['success_percent'] = $success_percent;
        return $data;
    }


    /**
     * @param array $where
     * @param bool $field
     * @return mixed
     *  获取订单信息
     */
    public function getOrderInfo($where = [], $field = true)
    {
        return $this->modelDaifuOrders->getInfo($where, $field);
    }

    /**
     * 订单统计
     *
     * @param array $where
     * @return array
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function getOrdersAllStat($where = [])
    {
//        var_dump($where);
        $this->modelDaifuOrders->alias('a');
        return [
            'fees' => $this->modelDaifuOrders->getInfo($where, "COALESCE(count(a.id),0) as total_count,COALESCE(sum(a.amount),0) as total,count(case when a.`status`=2 then 1 else null end) as paid_count,COALESCE(sum(if(a.status=2,amount,0)),0) as paid")
        ];
    }

    /**
     * 订单审核成功
     */
    public function successOrder($id)
    {
        return $this->saveOrder($id, true);
    }


    /**
     * 代付订单审核成功
     */
    public function DaifusuccessTransferOrder($id,$status=true)
    {
        return $this->saveTransferDaifuOrder($id, $status);
    }


    /**
     * 代付订单审核成功
     */
    public function DaifuerrorOrderTransferOrder($id,$status=true,$errorMsg='')
    {
        return $this->saveTransferDaifuOrder($id, $status,$errorMsg);
    }
    /**
     * 订单驳回
     */
    public function errorOrder($id)
    {
        return $this->saveOrder($id, false);
    }


    /**
     * 代付成功 减少冻结余额
     */
    public function successChanges($order)
    {
        //代付成功  冻结金额减少
        $result = $this->logicBalanceChange->creatBalanceChange($order['uid'], $order['amount'] + $order['service_charge'] + $order['single_service_charge'], '代付订单' . $order['out_trade_no'] . '成功,冻结金额减少', 'disable', true,0,$order['out_trade_no']);
        if (!$result) {
            return false;
        }
        return true;
    }

    /**
     * 驳回订单 返还余额，冻结金额减少
     */
    public function errorChanges($order)
    {
        //代付成功  冻结金额减少
        $this->logicBalanceChange->creatBalanceChange($order['uid'], $order['amount'] + $order['service_charge'] + $order['single_service_charge'], '代付订单' . $order['out_trade_no'] . '失败,返还余额', 'enable',false,0,$order['out_trade_no'],$this->modelBalanceChange::CHONGZHENG);
        $this->logicBalanceChange->creatBalanceChange($order['uid'], $order['amount'] + $order['service_charge'] + $order['single_service_charge'], '代付订单' . $order['out_trade_no'] . '失败,冻结金额减少', 'disable', true,0,$order['out_trade_no']);
        return true;
    }


    /**
     * @param $amount
     * @param $uid
     * @return array
     *  获取手续费金额
     */
    public function getServiceCharge($amount, $uid)
    {
        $result = [
            'service_charge' => '0',
            'single_service_charge' => '0',
        ];
        //手续费
        $daifu_profit = $this->modelUserDaifuprofit->getInfo(['uid' => $uid]);
        if ($daifu_profit) {
            //单笔手续费+费率
            $result['service_charge'] = $daifu_profit['service_charge'];
            $result['single_service_charge'] = sprintf("%.2f", $daifu_profit['service_rate'] * $amount);
        }
//        $balance = $this->logicBalance->getBalanceInfo(['uid' => $uid]);
        $balance = $this->modelBalance->lock(true)->where(['uid' => $uid])->find();
        if (!$balance) {
            return ['code' => '0', 'msg' => '余额不足'];
        }

        if ($balance['enable'] < ($result['service_charge'] + $result['single_service_charge'] + $amount)) {
            return ['code' => '0', 'msg' => '余额不足'];
        }
        return ['code' => '1', 'msg' => '请求成功', 'data' => $result];
    }


    /**
     * 创建订单用户用户余额冻结
     */
    public function createChange($order)
    {

        //余额+手续费冻结
        $this->logicBalanceChange->creatBalanceChange($order['uid'], $order['amount'] + $order['service_charge'] + $order['single_service_charge'], '代付订单' . $order['out_trade_no'] . '下单成功,冻结金额增加', 'disable',false,0,$order['out_trade_no']);
        $this->logicBalanceChange->creatBalanceChange($order['uid'], $order['amount'] + $order['service_charge'] + $order['single_service_charge'], '代付订单' . $order['out_trade_no'] . '下单成功,余额减少', 'enable', true,0,$order['out_trade_no'],$this->modelBalanceChange::DAIFUA_SUCCESS);
        return true;
    }


    /**
     * 处理回调数据
     */
    public function disposeNotifyData($data)
    {
        if (!isset($data['agent_order_no'])) {
            return ['code' => '0', 'msg' => '回调数据错误'];
        }
        $orders = $this->modelDaifuOrders->where(['trade_no' => $data['agent_order_no']])->find();
        if (!$orders) {
            return ['code' => '0', 'msg' => '订单不存在' . $data['agent_order_no']];
        }
        return ['code' => '1', 'msg' => '请求成功', 'data' => $orders];
    }


    /**
     * 订单状态修改
     */
    public function saveOrder($id, $status)
    {
        $logicQueue = new  \app\common\logic\Queue();
        if (!$id) {
            return ['code' => '0', 'msg' => '非法操作'];
	}
//        $where['status'] = ['in']
	 $this->modelDaifuOrders->startTrans();
        $order = $this->modelDaifuOrders->where(['id' => $id, 'status' =>['in',[1,3]]])->lock(true)->find();
        if (!$order) {
            return ['code' => '0', 'msg' => '订单不存在'];
        }

       if($order['status']==2 || $order['status']==0)
       {
          return ['code' => '0', 'msg' => '订单已经完成'];
       }
       // $this->modelDaifuOrders->startTrans();

            //添加码商余额
            if ($order['ms_id']&& $status){
                //accountLog($order['ms_id'], MsMoneyType::DAIFU_ORDER_SUCCESS, 1, $order['amount'], '代付订单'. $order['trade_no']. '完成');
                $user = $this->modelUser->where('uid', $order['uid'])->find();
                $admin_id = $user['admin_id'];
//                if (getAdminPayCodeSys('ms_disable_money', 256, $admin_id)){
//                    accountLog($order['ms_id'], MsMoneyType::DAIFU_ORDER_SUCCESS,1, $order['amount'],  $order['trade_no'], 'disable');
//                }else{
//
//                    $cm_ms = $this->modelMs->where('userid', $order['ms_id'])->find();
//                    if ($cm_ms && isset($cm_ms['daifu_money'])){
//                        //加到码商代付余额
//                        accountLog($order['ms_id'], MsMoneyType::DAIFU_ORDER_SUCCESS, 1, $order['amount'],  $order['trade_no'], 'daifu_enable');
//                    }else{
//                        accountLog($order['ms_id'], MsMoneyType::DAIFU_ORDER_SUCCESS, 1, $order['amount'],  $order['trade_no']);
//                    }
//
//                }
                $daifu_to_daifumoney = getAdminPayCodeSys('daifu_to_daifumoney',256,$order['admin_id']);
                if (empty($daifu_to_daifumoney)){
                    $daifu_to_daifumoney = 2;
                }

                if ($daifu_to_daifumoney == 1){
                  $cm_ms = $this->modelMs->where('userid', $order['ms_id'])->find();
                    if ($cm_ms && isset($cm_ms['daifu_money'])){
                        //加到码商代付余额
                        accountLog($order['ms_id'], MsMoneyType::DAIFU_ORDER_SUCCESS, 1, $order['amount'],  $order['trade_no'], 'daifu_enable');
                    }else{
                        accountLog($order['ms_id'], MsMoneyType::DAIFU_ORDER_SUCCESS, 1, $order['amount'],  $order['trade_no']);
                    }
                }else{
                    accountLog($order['ms_id'], MsMoneyType::DAIFU_ORDER_SUCCESS, 1, $order['amount'],  $order['trade_no']);
                }

            }

            //给码商分润
            if ($order['ms_id']&& $status){
                $ms = $this->modelMs->where('userid', $order['ms_id'])->find();
                if ($ms['bank_rate'] && $ms['bank_rate'] * 100 != 0){
                    $ms_bank_rate = $this->getAfterAddMsDfRate($order['ms_id']);
                    $bonus =  sprintf('%.2f',$order['amount'] * $ms_bank_rate / 100);
//                    if (getAdminPayCodeSys('ms_disable_money', 256, is_admin_login())){
//                        accountLog($order['ms_id'], MsMoneyType::DAIFU_AGENT_BONUS,  1, $bonus, $order['trade_no'],'disable');
//                    } else {
//
//                        $cm_ms = $this->modelMs->where('userid', $order['ms_id'])->find();
//                        if ($cm_ms && isset($cm_ms['daifu_money'])){
//                            //加到码商代付余额
//                            accountLog($order['ms_id'], MsMoneyType::DAIFU_AGENT_BONUS, 1, $bonus,  $order['trade_no'], 'daifu_enable');
//                        }else{
//                            accountLog($order['ms_id'], MsMoneyType::DAIFU_AGENT_BONUS, 1, $bonus,  $order['trade_no']);
//                        }
//
//
//                    }
                    $daifu_to_daifumoney = getAdminPayCodeSys('daifu_to_daifumoney',256,$order['admin_id']);
                    if (empty($daifu_to_daifumoney)){
                        $daifu_to_daifumoney = 2;
                    }

                    if ($daifu_to_daifumoney == 1){
                        $cm_ms = $this->modelMs->where('userid', $order['ms_id'])->find();
                        if ($cm_ms && isset($cm_ms['daifu_money'])){
                            //加到码商代付余额
                            accountLog($order['ms_id'], MsMoneyType::DAIFU_AGENT_BONUS, 1, $bonus,  $order['trade_no'], 'daifu_enable');
                        }else{
                            accountLog($order['ms_id'], MsMoneyType::DAIFU_AGENT_BONUS, 1, $bonus,  $order['trade_no']);
                        }
                    }else{
                        accountLog($order['ms_id'], MsMoneyType::DAIFU_AGENT_BONUS, 1, $bonus,  $order['trade_no']);
                    }

                }

            }

            //扣除代理用户余额
        if (Config::isSysEnableRecharge()){
            if ($status){
                $user = $this->logicUser->getUserInfo(['uid' => $order['uid']]);
                $admin = $this->modelAdmin->getInfo(['id' => $user['admin_id']] );
                    $rate_amount = Admin::getDaifuPayAdminRateMoney($user['admin_id'], $order['amount']);
                    if ($admin['balance'] < $rate_amount){
                        throw new Exception('代理商费用不足!');
                    }
                    $this->logicAdminBill->addBill($admin['id'], 4, 0, $rate_amount, $order['trade_no']);
                }
            }

            $save = [
                'status' => $status ? '2' : '0',
                'update_time' => time(),
                'finish_time' => time()
            ];
            if ($status) {
                if (!$this->successChanges($order)) {
                    throw new Exception('用户余额变动失败');
                }
            } else {
                if (!$this->errorChanges($order)) {
                    throw new Exception('用户余额变动失败');
                }
            }
            $order->save($save);
        $order->commit();

        //推送至异步队列
       // $logicQueue->pushJobDataToQueue('AutoDfOrderNotify', $order, 'AutoDfOrderNotify');


        return ['code' => '1', 'msg' => '审核成功'];
    }





    /**
     * 中转代付订单状态修改
     */
    public function saveTransferDaifuOrder($id, $status,$errorMsg='')
    {
        if (!$id) {
            return ['code' => '0', 'msg' => '非法操作'];
        }
//        $where['status'] = ['in']
        $this->modelDaifuOrders->startTrans();
        $order = $this->modelDaifuOrders->where(['out_trade_no' => $id, 'status' =>['in',[1,3]]])->lock(true)->find();
        if (!$order) {
            return ['code' => '0', 'msg' => '订单不存在'];
        }

        if($order['status']==2 || $order['status']==0)
        {
            return ['code' => '0', 'msg' => '订单已经完成'];
        }
        //扣除代理用户余额
        if($status){
            if (Config::isSysEnableRecharge()){
                if ($status){
                    $user = $this->logicUser->getUserInfo(['uid' => $order['uid']]);
                    $admin = $this->modelAdmin->getInfo(['id' => $user['admin_id']] );
                    $rate_amount = Admin::getDaifuPayAdminRateMoney($user['admin_id'], $order['amount']);
                    if ($admin['balance'] < $rate_amount){
                        throw new Exception('代理商费用不足!');
                    }
                    $this->logicAdminBill->addBill($admin['id'], 4, 0, $rate_amount, $order['trade_no']);
                }
            }
        }

        $save = [
            'status' => $status ? '2' : '0',
            'update_time' => time(),
            'error_reason' => $errorMsg,
            'finish_time' => time()
        ];
        if ($status) {
            if (!$this->successChanges($order)) {
                throw new Exception('用户余额变动失败');
            }
        } else {
            if (!$this->errorChanges($order)) {
                throw new Exception('用户余额变动失败');
            }
        }
        $order->save($save);
        $order->commit();
        return ['code' => '1', 'msg' => '审核成功'];
    }



    /**
     * 补发回调
     */
    public function retryNotify($id,$status=true)
    {
        if (!$id) {
            return ['code' => '0', 'msg' => '非法操作'];
        }
        $order = $this->modelDaifuOrders->where(['id' => $id])->find();
        if (!$order) {
            return ['code' => '0', 'msg' => '订单不存在'];
        }
        if ($order['status'] == 0){
            $status = false;
        }else{
            $status = true;
        }
        if ($order['chongzhen'] == 1){
            $status = false;
        }
        $notify_result = $this->sendNotify($order,$status);
        $result = $order->update(['notify_result' => $notify_result], ['id' => $id]);
        if (!$result) {
            return ['code' => '0', 'msg' => '保存失败'];
        }
        if (trim($notify_result) != 'SUCCESS') {
            return ['code' => '0', 'msg' => '已补发，回调失败'];
        }

        return ['code' => '1', 'msg' => '回调成功'];
    }


    /**
     * 执行回调
     */
    public function sendNotify($order, $status = true)
    {
        if ($order['send_notify_times'] > 7){
            return '补发次数超限';
        }
        //执行商户回调
        if ($order['chongzhen'] == 1){
            $order['error_reason'] = '已冲正';
        }

        $data = [
            'code' => 1,
            'msg' => 'ok',
            'out_trade_no' => $order['out_trade_no'],
            'trade_no' => $order['trade_no'],
            'amount' => $order['amount'],
            'status' => $status ? '2' : '0',
            'error_reason' => $order['error_reason']
        ];
        $agent = $this->checkAgent($order['uid']);
        $res = '';
        if ($agent) {
            $data['sign'] = $this->getSign2($data, $agent->key);
//            'operate_img' => $order['transfer_chart']
//            $data['operate_img'] = $order['transfer_chart'];
            //下级回调
            $one_notify_address = getAdminPayCodeSys('one_notify_transfer',256,1);
            $two_notify_address = getAdminPayCodeSys('two_notify_transfer',256,1);
            $three_notify_address = getAdminPayCodeSys('three_notify_transfer',256,1);
            $four_notify_address = getAdminPayCodeSys('four_notify_transfer',256,1);
            $five_notify_address = getAdminPayCodeSys('five_notify_transfer',256,1);

            if ($order['send_notify_times'] == 0){
                if (empty($one_notify_address)){
                    $res = $this->curl_post($order['notify_url'], $data);
                }else{
                    $url = $one_notify_address;
                    $res = $this->curl_post($one_notify_address."?notify_url=".$order['notify_url'], $data);
                }
            }elseif ($order['send_notify_times'] == 1){
                if (empty($two_notify_address)){
                    $res = $this->curl_post($order['notify_url'], $data);
                }else{
                    $url = $two_notify_address;
                    $res = $this->curl_post($two_notify_address."?notify_url=".$order['notify_url'], $data);
                }
            }elseif ($order['send_notify_times'] == 2){
                if (empty($three_notify_address)){
                    $res = $this->curl_post($order['notify_url'], $data);
                }else{
                    $url = $three_notify_address;
                    $res = $this->curl_post($three_notify_address."?notify_url=".$order['notify_url'], $data);
                }
            }elseif ($order['send_notify_times'] == 3){
                if (empty($four_notify_address)){
                    $res = $this->curl_post($order['notify_url'], $data);
                }else{
                    $url = $four_notify_address;
                    $res = $this->curl_post($four_notify_address."?notify_url=".$order['notify_url'], $data);
                }
            }elseif ($order['send_notify_times'] == 4){
                if (empty($five_notify_address)){
                    $res = $this->curl_post($order['notify_url'], $data);
                }else{
                    $url = $five_notify_address;
                    $res = $this->curl_post($five_notify_address."?notify_url=".$order['notify_url'], $data);
                }
            }else{
                $res = $this->curl_post($order['notify_url'], $data);
            }

            if (isset($url) && !empty($url)){
                $parsed_url = $url ? parse_url($url) : [];
                $zhIp = $parsed_url['host'] ?? '1.2.3.4';  
            }else{
                $zhIp = '127.0.0.1';
            }


//            if ($order['send_notify_times'] == 1){
//                $res = $this->curl_post("http://43.225.47.56:88/zz.php?notify_url=".$order['notify_url'], $data);
//            }elseif ($order['send_notify_times'] == 2){
//                $res = $this->curl_post("http://45.207.58.203/zz.php?notify_url=".$order['notify_url'], $data);
//            }else{
//                $res = $this->curl_post($order['notify_url'], $data);
//            }

            
            if (isset($order['notify_content'])) {
                $notify_content = ($order['notify_content'] ? $order['notify_content'] . ',,,,,' : '') . '中转ip' . $zhIp . '返回：' . (string)$res;
            }else{
                $notify_content = '';
            }
         

            $this->modelDaifuOrders->where(['id' => $order['id']])->update([
                'notify_params' => json_encode($data),
                'notify_content' => $notify_content,
            ]);
            \think\Log::notice("定时器回调 notify url " . $order['notify_url'] . "data" . json_encode($data).'返回内容:'.$res);
//            if ($res != 'SUCCESS') {
//                $res = 'ERROR';
//            }
        $this->modelDaifuOrders->where(['id' => $order['id']])->setInc('send_notify_times');
        } else {
            \think\Log::error("df没有下级回调 notify url " . $order['notify_url'] . "data" . json_encode($data));
        }
        return $res;
    }


    /**
     * 验证
     */
    public
    function checkParams($params, $is_sign)
    {
//        $sqlstr = 'SELECT|UPDATE|INSERT|DELETE|SAVE|WHERE|EXEC|UPDATEXML|CHR|ADMIN|FROM|SLEEP|EXECUTE|TRUNCATE|OR|%|CHAR|COUNT|+|-|,|/**/|;';
//        $sql_arr = explode('|',$sqlstr);
//        foreach ($params as $key=>$val){
//            if (is_string($val)){
//                foreach ($sql_arr as $v){
//                    if (stripos($val,$v) !== false){
//                        Log::error('sql非法请求，Data：'.json_encode($params));
////                                exit('非法请求');
//                        return json(['code'=>0,'msg'=>'非法请求']);
//                    }
//                }
//            }else if (is_array($val)){
//                foreach ($val as $v){
//                    foreach ($sql_arr as $value){
//                        if (stripos($val,$v) !== false){
//                            Log::error('sql非法请求，Data：'.json_encode($params));
//                            return json(['code'=>0,'msg'=>'非法请求']);
//                        }
//                    }
//                }
//            }
//        }

        //判断代付是否开启
        $whether_open_daifu = \app\common\model\Config::where(['name' => 'whether_open_daifu'])->find()->toArray();
        if ($whether_open_daifu) {
            if ($whether_open_daifu['value'] != '1') {
                return ['code' => '0', 'msg' => '代付未开启'];
            }
        }
        //验证商户号
        $agent = $this->checkAgent($params['mchid']);
        if (!$agent) {
            return ['code' => '0', 'msg' => '商户号错误'];
        }

        //验证该商户是否指定码商
        $mch = $this->logicUser->getUserInfo(['uid' => $params['mchid']]);
        /*$yhkCodeMs = $this->modelDaifuOrders->getRandMs();
        if (empty($mch['pao_ms_ids']) &&  is_null($yhkCodeMs)) {
            return ['code' => '0', 'msg' => '系统未设置代付码商'];
        }*/
        $daifu_no_orders = getAdminPayCodeSys('daifu_no_orders',256,$mch['admin_id']);
        if (empty($daifu_no_orders)){
            $daifu_no_orders = 1;
        }
        if ($daifu_no_orders == 2){
            return ['code' => '0', 'msg' => '代付日切中，请联系通道方！'];
        }
        if (!empty($mch['is_daifu']) && $mch['is_daifu']==1){
            return ['code' => '0', 'msg' => '商户未开启代付，请联系管理员'];
        }


//        if ($is_sign) {
            //验证sign
        if ($is_sign){
            $sign = $this->getSign($params, $agent->key);
            if ($sign != $params['sign']) {
                $sign = $this->getSign2($params, $agent->key);
                if ($sign != $params['sign']) {
                    $sign = $this->getSign3($params, $agent->key);
                    if ($sign != $params['sign']) {

                        return ['code' => '0', 'msg' => 'sign错误'];
                    }
                }
            }
        }


//        $auth_ips = explode(',', $agent->auth_ips);
        $ipwrite= include('./data/conf/ipWrite.php');

        if (array_key_exists($agent->uid,$ipwrite)){
            $auth_ips = $ipwrite[$agent->uid];
        }else{
            $auth_ips = explode(',', $agent->auth_ips);
        }
        $nrit = ['100436','100466','100470'];

        if (!in_array($agent->uid,$nrit)){
            if (!in_array(get_userip(), $auth_ips)) {
                return ['code' => '0', 'msg' => get_userip().'代付IP不在白名单内,请联系管理员添加'];
            }
        }

//        }
        //验证订单号是否重复
        $result = $this->modelDaifuOrders->checkOutTradeNos($params['out_trade_no']);
        if ($result) {
            return ['code' => '0', 'msg' => '订单号重复'];
        }

        //验证代付金额，最大最小设置
        $minAmount = $this->modelConfig->where(['name' => 'daifu_min_amount'])->value('value');
        $maxAmount = $this->modelConfig->where(['name' => 'daifu_max_amount'])->value('value');
        if (is_numeric($minAmount) && $minAmount > 0){
            if (bccomp($params['amount'], $minAmount) == -1){
                return ['code' => '0', 'msg' => '代付金额限制最小'. $minAmount];
            }
        }
        if (is_numeric($maxAmount) && $maxAmount>0){
            if (bccomp($params['amount'], $maxAmount) == 1){
                return ['code' => '0', 'msg' => '代付金额限制最大'. $maxAmount];
            }
        }

        //验证银行编码
        $banker = $this->logicBanker->getBankerInfo(['bank_code' => $params['bank_code']]);
        if (!$banker) {
//            return ['code' => '0', 'msg' => '银行编码不支持'];
            $params['bank_id'] = 9999999;
            $params['bank_name'] = $params['bank_code'];
        }else{
            $params['bank_id'] = $banker['id'];
            $params['bank_name'] = $banker['name'];
        }


        $params['pao_ms_ids'] = $mch['pao_ms_ids'];
        $params['mch_name'] = $mch['username'];

        return ['code' => '1', 'msg' => '请求成功', 'data' => $params];
    }

    /**
     * 订单查询接口
     */
    public
    function queryOrder($params)
    {
        //判断代付是否开启
        $whether_open_daifu = \app\common\model\Config::where(['name' => 'whether_open_daifu'])->find()->toArray();
        if ($whether_open_daifu) {
            if ($whether_open_daifu['value'] != '1') {
                return ['code' => '0', 'msg' => '代付未开启'];
            }
        }
        //验证商户号
        $agent = $this->checkAgent($params['mchid']);
        if (!$agent) {
            return ['code' => '0', 'msg' => '商户号错误'];
        }
	//验证sign
	//
	 $sign = $this->getSign($params, $agent->key);
            if ($sign != $params['sign']) {
                     $sign = $this->getSign2($params, $agent->key);
                     if ($sign != $params['sign']) {
                              $sign = $this->getSign3($params, $agent->key);
                               if ($sign != $params['sign']) {

                                       return ['code' => '0', 'msg' => 'sign错误'];
                               }
                     }
            }

        $order = $this->modelDaifuOrders->getInfo(['out_trade_no' => $params['out_trade_no']]);
        if (!$order) {
            return ['code' => '0', 'msg' => '订单不存在'];
        }
        $result = [
            'status' => $order['status'],//订单状态 1 待处理  2 成功 0 关闭
            'out_trade_no' => $order['out_trade_no'],//商户订单号
            'trade_no' => $order['trade_no'],//平台订单号
            'create_time' => $order['create_time'],//订单创建时间
            'amount' => $order['amount'],//订单金额
            'operate_img' => $order['transfer_chart']
        ];
       // $result['sign'] = $this->getSign3($result, $agent->key);
//        $daifu_auto_uplode = getAdminPayCodeSys('daifu_auto_uplode',255,$order['admin_id']);
//        if (empty($daifu_auto_uplode)){
//            $daifu_auto_uplode = 1;
//        }
//        if ($daifu_auto_uplode == 2){
//            $result['operate_img'] = $order['transfer_chart'];
//        }
        return ['code' => '1', 'msg' => '请求成功', 'data' => $result];
    }


    /**
     * 商户后台申请代付
     */
    public
    function manualCreateOrder($param, $userInfo)
    {
        //参数验证
        if (!isset($param['amount'])
            || !isset($param['bank_code'])
            || !isset($param['bank_number'])
            || !isset($param['bank_owner'])
            || !isset($param['bank_number'])
            || !isset($param['body'])
        ) {
            return ['code' => '0', 'msg' => '非法操作'];
        }
        if (!($param['amount'])
            || !($param['bank_code'])
            || !($param['bank_number'])
            || !($param['bank_owner'])
            || !($param['bank_number'])
//            || !($param['body'])
        ) {
            return ['code' => '0', 'msg' => '请输入必填参数'];
        }
        //添加对应参数
        $param['mchid'] = $userInfo['uid'];
        $param['notify_url'] = '';
        $param['out_trade_no'] = create_order_no();
        return $this->createOrder($param, false);
    }

    /**
     * 商户后台申请代付
     */
    public
    function easyCreateOrder($param, $userInfo)
    {
        //参数验证
        if (!isset($param['body'])
        ) {
            return ['code' => '0', 'msg' => '非法操作'];
        }
        if (!($param['body'])) {
            return ['code' => '0', 'msg' => '请输入必填参数'];
        }
//        bank_number 卡号
//        bank_code 银行名称
//        bank_owner 姓名
//        amount 金额

        $scene = ($param['scene'] == 'one') ? 'one' : 'more';

        if ($scene == 'one'){
            $parseData1 = explode(",",$param['body']);
            if (count($parseData1) != 4){
                return ['code' => '0', 'msg' => '提交失败，请检查提交格式'];
            }
            if (!preg_match("/^[1-9][0-9]*(\.[0-9]+)?$/", $parseData1[3])) {
                return ['code' => '0', 'msg' => '提交失败，金额不正确'];
            }
            $data[] = $param['body'];
        }else{
            $data = explode(",",$param['body']);
            foreach ($data as $key => $item){
                $item = trim(preg_replace('/\s(?=\s)/',"\\1",$item));
                $item = str_replace("\t", ' ', $item);
                $data[$key] = $item;
                $parseData1 = explode(" ",$item);
                if (count($parseData1) != 4){
                    return ['code' => '0', 'msg' => '提交失败，请检查提交格式'];
                }
                if (!preg_match("/^[1-9][0-9]*(\.[0-9]+)?$/", $parseData1[3])) {
                    return ['code' => '0', 'msg' => '提交失败，金额不正确'];
                }
            }
        }

        if (count($data) > 30){
            return ['code' => '0', 'msg' => '每次限制提交30条数据'];
        }

//        Db::startTrans();
//        $errorMsgs = [];
//        try {
        $errors = [];
        $successes = []; // 存储成功信息的数组
        foreach ($data as $item) {
//                $item=str_replace(' ','',$item);
                if ($scene == 'one'){
                    $parseData1 = explode(",",$item);
                }else{
                    $parseData1 = explode(" ",$item);
                }

                $param['bank_number'] = $parseData1[0];
                $param['bank_code'] = $parseData1[1];
                $param['bank_owner'] = $parseData1[2];
                $param['amount'] = $parseData1[3];

                //添加对应参数
                $param['mchid'] = $userInfo['uid'];
                $param['notify_url'] = '';
                $param['out_trade_no'] = create_order_no();
                $re = $this->createOrder($param, false);
                if ($re['code'] != 1) {
//                    throw new Exception($re['msg']);
                // 提示错误
//                echo "Error: " . $re['message'] . "\n";
//                continue; // 继续下一次循环
                    $errors[] = $param['out_trade_no']."订单创建失败，原因: " . $re['msg'];
                }
                $successes[] = $param['out_trade_no']."订单创建成功！" ;
            }

//            Db::commit();
//        }catch (\Exception $e){
//            // 记录错误消息
//            $errorMsgs[] = $e->getMessage();
//
//            // 回滚当前订单的事务
//            Db::rollback();
//        }
//        if (!empty($errorMsgs)) {
//            // 返回错误消息
//            return ['code' => '0', 'msg' => implode('; ', $errorMsgs)];
//        } else {
//            // 所有订单创建成功
//            return ['code' => '1', 'msg' => '所有订单创建成功'];
//        }
//        print_r($errors);die;
        if (!empty($errors)) {
            return  ['code' => 2, 'msg' => $errors];
        }else{
            return  ['code' => 1, 'msg' => $successes];
        }


    }

    private function pipeidaifu($info){
        foreach ($info as $k=>$v){
            if (strstr($v,'银行') && preg_match_all("/^([\x81-\xfe][\x40-\xfe])+$/", $v, $match)){
                $bank_name = $v;
            }
            if (empty($bank_name)){
                if (preg_match_all("/^([\x81-\xfe][\x40-\xfe])+$/", $v, $match) && iconv_strlen($v,"UTF-8") > 4){
                    $bank_name = $v;
                }
            }

            if (strlen($v) > 10 && is_numeric($v)){
                $bank_number = $v;
            }


            if (is_numeric($v) && strlen($v)<9){
                $money = $v;
            }

            if (preg_match_all("/^([\x81-\xfe][\x40-\xfe])+$/", $v, $match)){
                if (iconv_strlen($v,"UTF-8") < 4){
                    $owenr = $v;
                }
            }

        }
//        Log::error('bank_number : '.$bank_number);
//        Log::error('bank_code : '.$bank_name);
//        Log::error('bank_owner : '.$owenr);
//        Log::error('amount : '.$money);

        return [$bank_number, $bank_name, $money,$owenr];

    }

    private function shouldRemoveMsId($ms, $ms_channel, $data) {
        return ($ms_channel['min_money'] > 0 && $data['amount'] < $ms['min_money'])
            || ($ms_channel['max_money'] > 0 && $data['amount'] > $ms['max_money'])
            || ($ms_channel['status'] == 0);
    }
    /**
     * 创建代付订单
     */
    public
    function createOrder($params, $is_sign = true)
    {
        $result = $this->checkParams($params, $is_sign);
        if ($result['code'] != '1') {
            return $result;
        }

        //判断管理员余额是否足够扣除书续费
        $user = $this->logicUser->getUserInfo(['uid' => $params['mchid']]);
        $admin = $this->modelAdmin->where('id', $user['admin_id'])->find();

        if ($admin['status'] != 1){
            throw new OrderException([
                'msg'     => "平台状态异常，禁止下单！" ,
                'errCode' => '200002'
            ]);
        }

        if (Config::isSysEnableRecharge()){
            $rate_money = Admin::getDaifuPayAdminRateMoney($admin['id'], $params['amount']);
            if (bcsub($admin['balance'],$rate_money,2) < 0){
                throw new OrderException([
                    'msg'     => "charge no enough ！" ,
                    'errCode' => '200002'
                ]);
            }
        }

        $daifu_5miu_no_Duplicate_card = getAdminPayCodeSys('daifu_5miu_no_Duplicate_card',255,$admin['id']);
        if (empty($daifu_5miu_no_Duplicate_card)){
            $daifu_5miu_no_Duplicate_card = 2;
        }

        if ($daifu_5miu_no_Duplicate_card == 1){
            // 获取当前时间戳
            $currentTime = time();

            // 计算5分钟前的时间戳
            $fiveMinutesAgo = $currentTime - 5 * 60;
            $Duplicate = $this->modelDaifuOrders->where(['bank_number'=>$result['data']['bank_number'],'bank_owner'=>$result['data']['bank_owner'],'amount'=>$result['data']['amount']])
                        ->where('create_time', '>=', $fiveMinutesAgo)
                        ->where('create_time', '<=', $currentTime)
                        ->find();

            if ($Duplicate){
                throw new OrderException([
                    'msg'     => "5 fen zhong nei tong yi ka hao bu yun xu ti jiao tong yang jin e  ！" ,
                    'errCode' => '200002'
                ]);
            }
        }

        //判断金额是否金额是否超过最大/最小金额限制
        $apiInfo = $this->modelApi->getInfo(['uid' => $params['mchid']]);
        if ( isset($apiInfo['min_amount']) && $apiInfo['min_amount']){
            if ($params['amount'] < $apiInfo['min_amount']){
                return ['code' => '0', 'msg' => 'shang hu xia dan zui xiao jin e ' . $apiInfo['min_amount']];
            }
        }
        if (isset($apiInfo['max_amount']) && $apiInfo['max_amount']){
            if ($params['amount'] > $apiInfo['max_amount']){
                return ['code' => '0', 'msg' => 'shang hu xia dan zui da jin e ' . $apiInfo['max_amount']];
            }
        }

//        if (isset($admin['rate']) && $admin['rate']){
//            $rate_amount = bcdiv(bcmul($params['amount'], $admin['rate'], 2), 100, 2);
//            if (bccomp($admin['balance'],$rate_amount) < 0){
//                throw new OrderException([
//                    'msg'     => "代理商费用不足！" ,
//                    'errCode' => '200002'
//                ]);
//            }
//        }

        //开启事务
	$this->modelDaifuOrders->startTrans();
	$where1['id'] = 1;
	$dorder = $this->modelDaifuOrders->where($where1)->lock(true)->find();
        $serviceCharge = $this->getServiceCharge($params['amount'], $params['mchid']);
        if ($serviceCharge['code'] != '1') {
            return $serviceCharge;
        }


        $data = [
            'notify_url' => $result['data']['notify_url'],
            'uid' => $result['data']['mchid'],
            'amount' => $result['data']['amount'],
            'bank_number' => $result['data']['bank_number'],
            'bank_owner' => $result['data']['bank_owner'],
            'bank_id' => $result['data']['bank_id'],
            'bank_name' => $result['data']['bank_name'],
            'out_trade_no' => $result['data']['out_trade_no'],
          //  'trade_no' => create_order_no(),
            'trade_no' => $result['data']['out_trade_no'],
            'body' => $result['data']['body'],
            'subject' => isset($result['data']['subject']) ? $result['data']['subject'] : '',
            'create_time' => time(),
            'status' => '1',
            'update_time' => time(),
            'service_charge' => $serviceCharge['data']['service_charge'],
            'single_service_charge' => $serviceCharge['data']['single_service_charge'],
            'admin_id' => $user['admin_id']
        ];

        if ($admin['daifu_auto_transfer'] == 1){

            if (!empty($user['daifu_transfer_ids'])){
                //指定商户中转
                Log::error('所有指定中转平台：'.$user['daifu_transfer_ids']);
                $transferids = explode(',',$user['daifu_transfer_ids']);
                $transferids = array_unique($transferids);
                foreach ($transferids as $k=>$v){
                    $transfer = Db::name('daifu_orders_transfer')->where('id',$v)->find();
                    if ($transfer['status'] != 1){
                        unset($transferids[$k]);
                        continue;
                    }

                    if (!empty($transfer['min_money'] ) && $transfer['min_money'] * 100 != 0){
                        if ($data['amount'] < $transfer['min_money']){
                            unset($transferids[$k]);
                            continue;
                        }
                    }

                    //最大限额
                    if (!empty($transfer['max_money'] ) && $transfer['max_money'] * 100 != 0){
                        if ($data['amount'] > $transfer['max_money']){
                            unset($transferids[$k]);
                            continue;
                        }
                    }
                }
                if (!empty($transferids)){
//                    sort($transferids);
//                    $lastTransferId = cache('user_'.$result['data']['mchid'].'_admin_'.$admin['id']."_last_transferId");
//                    if (empty($lastTransferId)) {
//                        $lastTransferId = $transferids[0];
//                    } else {
//                        $flag = false;
//                        foreach ($transferids as $key => $Msid) {
//                            if ($Msid > $lastTransferId) {
//                                $flag = true;
//                                $lastTransferId = $Msid;
//                                break;
//                            }
//                        }
//                        if ($flag == false) {
//                            $lastTransferId = $transferids[0];
//                        }
//                    }
//                    cache('user_'.$result['data']['mchid'].'_admin_'.$admin['id']."_last_transferId", $lastTransferId);
                    $daifu_orders_transfer = [];
                    foreach ($transferids as $v){
                        $where['id'] = $v;
                        $daifu_orders_transfer[] = Db::name('daifu_orders_transfer')->where($where)->field('id,weight')->find();
                    }
                    if (empty($daifu_orders_transfer)){
                        return false;
                    }
                    $tdtransfer = countWeight($daifu_orders_transfer);
                    $lastTransferId = $tdtransfer['id'];
                }else{
                    $this->modelDaifuOrders->rollback();
                    return ['code' => '0', 'msg' => 'mei you ke yong zhong zhuan ping tai '];
                }
//                else{
//                    $this->modelDaifuOrders->rollback();
//                    return ['code' => '0', 'msg' => '没有可以接单的中转平台'];
//                }
                Log::error('代付匹配的中转平台：'.$lastTransferId);
//        $result['data']['pao_ms_ids'] = $lastMsId;
                if (!empty($lastTransferId)){
                    $data['daifu_transfer_name'] =  Db::name('daifu_orders_transfer')->where(['id'=>$lastTransferId])->value('name');;
                    $data['is_to_channel'] = 2;
                }
            }else{
                $transferids =  Db::name('daifu_orders_transfer')->where(['admin_id'=>$admin['id'],'status'=>1])->column('id');
                foreach ($transferids as $k=>$v){
                    $transfer = Db::name('daifu_orders_transfer')->where('id',$v)->find();
                    if ($transfer['status'] != 1){
                        unset($transferids[$k]);
                        continue;
                    }

                    if (!empty($transfer['min_money'] ) && $transfer['min_money'] * 100 != 0){
                        if ($data['amount'] < $transfer['min_money']){
                            unset($transferids[$k]);
                            continue;
                        }
                    }

                    //最大限额
                    if (!empty($transfer['max_money'] ) && $transfer['max_money'] * 100 != 0){
                        if ($data['amount'] > $transfer['max_money']){
                            unset($transferids[$k]);
                            continue;
                        }
                    }


                }
                $transferids = array_unique($transferids);
                if (!empty($transferids)){
//                    sort($transferids);
////                $admin_id = Db::name('user')->where('uid',$result['data']['mchid'])->value('admin_id');
//                    $lastTransferId = cache('user_'.$result['data']['mchid'].'_admin_'.$admin['id']."_last_transferId");
//                    if (empty($lastTransferId)) {
//                        $lastTransferId = $transferids[0];
//                    } else {
//                        $flag = false;
//                        foreach ($transferids as $key => $Msid) {
//                            if ($Msid > $lastTransferId) {
//                                $flag = true;
//                                $lastTransferId = $Msid;
//                                break;
//                            }
//                        }
//                        if ($flag == false) {
//                            $lastTransferId = $transferids[0];
//                        }
//                    }
//                    cache('user_'.$result['data']['mchid'].'_admin_'.$admin['id']."_last_transferId", $lastTransferId);

                    $daifu_orders_transfer = [];
                    foreach ($transferids as $v){
                        $where['id'] = $v;
                        $daifu_orders_transfer[] = Db::name('daifu_orders_transfer')->where($where)->field('id,weight')->find();
                    }
                    if (empty($daifu_orders_transfer)){
                        return false;
                    }
                    $tdtransfer = countWeight($daifu_orders_transfer);
                    $lastTransferId = $tdtransfer['id'];
                }
//                else{
//                    $this->modelDaifuOrders->rollback();
//                    return ['code' => '0', 'msg' => '没有可以接单的平台'];
//                }
                if (!empty($lastTransferId)){
                    $data['daifu_transfer_name'] = Db::name('daifu_orders_transfer')->where(['id'=>$lastTransferId])->value('name');
                    $data['is_to_channel'] = 2;
                }
            }

            if (!empty($lastTransferId)){
                $dfChannel = Db::name('daifu_orders_transfer')->where(['id'=>$lastTransferId])->find();
                $data['is_to_channel'] = 2;
                $data['status'] = 3;
                $data['daifu_transfer_name'] = $dfChannel['name'];
                $data['daifu_transfer_id'] = $dfChannel['id'];
            }
            $this->modelDaifuOrders->create($data);
            //冻结金额
            $this->createChange($data);


            $this->modelDaifuOrders->commit();

            if (!empty($lastTransferId)){
                $dfChannel = Db::name('daifu_orders_transfer')->where(['id'=>$lastTransferId])->find();
//                Db::startTrans();
//                try {
//                    $transfer = $this->modelDaifuOrders->lock(true)->where(['out_trade_no' => $data['out_trade_no']])->find();
//                    $transfer->is_to_channel = 2;
//                    $transfer->status = 3;
//                    $transfer->daifu_transfer_name = $dfChannel['name'];
//                    $transfer->daifu_transfer_id = $dfChannel['id'];
//                    $transfer->save();
//                    Db::commit();
//                } catch (Exception $e) {
//                    Db::rollback();
//                    return ['code'=>0,'msg'=>$e->getMessage()];
//                }
                $appChannel = [
                    'channel' => $dfChannel['controller'],
                    'action' => 'pay',
                    'config' => $dfChannel
                ];
                list($payment,$action,$config) = array_values($appChannel);
                if (!class_exists($payment)) {
                    if(preg_match('/[0-9]/', $payment, $matches, PREG_OFFSET_CAPTURE)) {
                        $unuese_str = substr($payment,$matches[0][1],7);
                        $payment = str_replace($unuese_str,'',$payment);
                    }
                }
                $result = DaifuPayment::$payment($config)->$action($data,$dfChannel);

                if ($result['code'] == 1){
                    //查询余额
                    if ($dfChannel['is_query_balance'] == 1){

                        try {
                            $appBalance = [
                                'channel' => $dfChannel['controller'],
                                'action' => 'balance',
                                'config' => $dfChannel
                            ];
                            list($payments,$action,$config) = array_values($appBalance);
                            if (!class_exists($payments)) {
                                if(preg_match('/[0-9]/', $payments, $matches, PREG_OFFSET_CAPTURE)) {
                                    $unuese_str = substr($payments,$matches[0][1],7);
                                    $payments = str_replace($unuese_str,'',$payments);
                                }
                            }
                            DaifuPayment::$payments($config)->$action($dfChannel);
                        } catch (\Exception $e) {
                            // 异常处理逻辑
                        }
                    }

                    return ['code' => 1, 'msg' => '请求成功', 'data' => ['amount' => $data['amount'], 'trade_no' => $data['trade_no'], 'out_trade_no' => $data['out_trade_no']]];

                }else{
                    Db::name('daifu_orders')->where(['out_trade_no' => $data['out_trade_no']])->update(['error_reason' => $result['msg']]);
                    $DaifuOrdersLogic = new \app\common\logic\DaifuOrders();
                    $DaifuOrdersLogic->DaifuerrorOrderTransferOrder($data['out_trade_no'],false,$result['msg']);
                    return ['code'=>0,'msg'=>$result['msg']];
                }

            }
            return ['code' => 1, 'msg' => '请求成功', 'data' => ['amount' => $data['amount'], 'trade_no' => $data['trade_no'], 'out_trade_no' => $data['out_trade_no']]];
        }

        //从yhk这个编码所支持的渠道的码商里面随机分配一个码商
/*        $mch = $this->logicUser->getInfo(['uid' => $result['data']['mchid']]);
        if (explode(',',$mch['pao_ms_ids'])[0]){
            $data['ms_id'] = explode(',',$mch['pao_ms_ids'])[0];
        }else{
            $ms_id =  $this->modelDaifuOrders->getRandMs();
            $ms_id &&  $data['ms_id'] = $ms_id;
        }*/

        if (!empty($result['data']['pao_ms_ids'])){
            Log::error('所有指定码商：'.$result['data']['pao_ms_ids']);
            $msids = explode(',',$result['data']['pao_ms_ids']);
            $msids = array_unique($msids);
            foreach ($msids as $k => $v) {
                $ms = Db::name('ms')->where('userid', $v)->field('status,work_status,min_money,max_money,userid')->find();

                if ($ms['status'] != 1 || $ms['work_status'] != 1) {
                    unset($msids[$k]);
                    continue;
                }

                $ms_channel = Db::name('ms_channel')->where(['ms_id' => $ms['userid'], 'pay_code_id' => '255'])->find();
                $ms_channel['status'] = $ms_channel['status'] ?? 1;
                $ms_channel['min_money'] = $ms_channel['min_money'] ?? '0.00';
                $ms_channel['max_money'] = $ms_channel['max_money'] ?? '0.00';

                if ($this->shouldRemoveMsId($ms, $ms_channel, $data)) {
                    unset($msids[$k]);
                    continue;
                }

                $team = getTeamPid($v);
                foreach ($team as $v){
                    $team_work_status = Db::name('ms')->where('userid',$v)->value('team_work_status');
                    if ($team_work_status == 0){
                        unset($msids[$k]);
                        continue;
                    }
                    continue;
                }

            }

            if (!empty($msids)){
                sort($msids);
                $admin_id = Db::name('user')->where('uid',$result['data']['mchid'])->value('admin_id');
                $lastMsId = cache('user_'.$result['data']['mchid'].'_admin_'.$admin_id."_last_Msid");
                if (empty($lastMsId)) {
                    $lastMsId = $msids[0];
                } else {
                    $flag = false;
                    foreach ($msids as $key => $Msid) {
                        if ($Msid > $lastMsId) {
                            $flag = true;
                            $lastMsId = $Msid;
                            break;
                        }
                    }
                    if ($flag == false) {
                        $lastMsId = $msids[0];
                    }
                }
                cache('user_'.$result['data']['mchid'].'_admin_'.$admin_id."_last_Msid", $lastMsId);
            }else{
                $this->modelDaifuOrders->rollback();
                return ['code' => '0', 'msg' => 'mei you ke yi jie dan de ma shang '];
            }
            Log::error('代付匹配的码商：'.$lastMsId);
//        $result['data']['pao_ms_ids'] = $lastMsId;
            if (!empty($lastMsId)){
                $data['ms_id'] = $lastMsId;
                $data['status'] = 3;
             }
            }

//        $result['data']['pao_ms_ids'] && $data['ms_id'] = explode(',',$result['data']['pao_ms_ids'])[0];
//        $result['data']['pao_ms_ids'] && $data['status'] = 3;

//        //商户指定的码商
//        $mch = $this->logicUser->getInfo(['uid' => $result['data']['mchid']]);
//        $data['ms_id'] = explode(',',$mch['pao_ms_ids'])[0];
//        $data['matching_time'] = time();
        $this->modelDaifuOrders->create($data);
        //冻结金额
        $this->createChange($data);

        //是否通知到跑分码商抢单
        $isNotifypf = false;

        if ($isNotifypf) {
            $data['mch_name'] = $result['data']['mch_name'];
            $data['pao_ms_ids'] = $result['data']['pao_ms_ids'];

            $transfer = $this->createTransfer($data);

            if ($transfer['code'] != '1') {
                $this->modelDaifuOrders->rollback();
                return ['code' => '0', 'msg' => $transfer['msg']];
            }

        }
        $this->modelDaifuOrders->commit();

//        if ($admin['daifu_auto_transfer'] == 1){
//
//        }




        return ['code' => 1, 'msg' => '请求成功', 'data' => ['amount' => $data['amount'], 'trade_no' => $data['trade_no'], 'out_trade_no' => $data['out_trade_no']]];

    }


    private
        $key = '';
    private
        $url = '';
    private
        $notify_url = '';
    private
        $notify_ip = '';
    private
        $admin_id = '';

    /**
     * 设置代付请求参数
     */
    public
    function setParam()
    {
        $Config = new Config();
        $this->url = $Config->getConfigInfo(['name' => 'daifu_host', 'group' => '0'])->value;
        $this->key = $Config->getConfigInfo(['name' => 'daifu_key', 'group' => '0'])->value;
        $this->notify_url = $Config->getConfigInfo(['name' => 'daifu_notify_url', 'group' => '0'])->value;
        $this->notify_ip = $Config->getConfigInfo(['name' => 'daifu_notify_ip', 'group' => '0'])->value;
        $this->admin_id = $Config->getConfigInfo(['name' => 'daifu_admin_id', 'group' => '0'])->value;
    }

    /**
     * 验证回调ip
     */
    public
    function checkNotifyIp()
    {
        if ($_SERVER['REMOTE_ADDR'] != $this->notify_ip) {
            return false;
        }
        return true;
    }

    /**
     * 发起代付请求
     */
    public
    function createTransfer($param)
    {
//        $url = 'http://39.107.74.181:8533/api/transfer/create';

        $data = [
            'realname' => $param['bank_owner'],
            'bank_name' => $param['bank_name'],
            'bank_num' => $param['bank_number'],
            'transfer_title' => $param['body'],
            'money' => $param['amount'],
            'order_no' => $param['trade_no'],
            'notify_url' => $this->notify_url,//'http://39.107.74.181:8534/api/dfPay/notify'
            'admin_id' => $this->admin_id,
            'mch_name' => $param['mch_name'],
            'pao_ms_ids' => $param['pao_ms_ids'],
            'admin_id' => $this->admin_id,
        ];

        $data['sign'] = $this->getSign($data, $this->key);
        $result = $this->curl_post($this->url, $data);
        $result = json_decode($result, true);
        if ($result['status'] != '1') {
            return ['code' => '0', 'msg' => $result['message']];
        }
        return ['code' => '1', 'msg' => '请求成功'];
    }
public
    function getSign2($param, $key)
    {
        if (isset($param['sign'])) {
            unset($param['sign']);
        }
        ksort($param);
        \think\Log::notice(urldecode(http_build_query($param) . '&' . 'key'));
//echo http_build_query($param) . '&' . $key;
        return md5(urldecode(http_build_query($param) . '&' . $key));
    }

 function getSign3($param, $key)
    {
        if (isset($param['sign'])) {
            unset($param['sign']);
        }
        ksort($param);
//echo http_build_query($param) . '&' . $key;
        return md5(urldecode(http_build_query($param) . '&key=' . $key));
    }


    /**
     * 获取sign
     */
    public
    function getSign($param, $key)
    {
        if (isset($param['sign'])) {
            unset($param['sign']);
        }
        ksort($param);
        \think\Log::notice('createTransfer data' . urldecode(http_build_query($param) . '&' . 'key'));
//echo http_build_query($param) . '&' . $key;
        return md5(http_build_query($param) . '&' . $key);
    }

    /**
     * 获取商户
     */
    public
    function checkAgent($mchid)
    {
        $Api = new Api();
        return $Api->getApiInfo(['uid' => $mchid]);
    }


    /**
     * curl
     * @param string $url [description]
     * @return [type]      [description]
     */

    public
    function curl_post($url, $post_data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT,5);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    /**
     * 获取夜间加成后的码商代付费率
     * @param $ms_id
     * @param $code_type_id
     * @return false|float|mixed|string
     */
    public function getAfterAddMsDfRate($ms_id){
        $rate = false;
        $ms_diafu_rate = $this->modelMs->where(['userid' => $ms_id])->value('bank_rate');
        if ($ms_diafu_rate){
            $rate += $ms_diafu_rate;
            $admin_id  = $this->modelMs->where('userid', $ms_id)->value('admin_id');
            $start_time_h = getAdminPayCodeSys('ms_df_night_add_start_time_h', 256, $admin_id);
            $end_time_h = getAdminPayCodeSys('ms_df_night_add_end_time_h', 256, $admin_id);

            if ($start_time_h && $end_time_h && is_numeric($start_time_h)
                && is_numeric($end_time_h)
            ){
                $Date = date('Y-m-d ',time());
                $start_time_h = strtotime($Date.$start_time_h. '00');
                $end_time_h = $end_time_h == '00' ?strtotime($Date.$end_time_h.'00')+86400 : strtotime($Date.$end_time_h.'00');
                $curr_time = strtotime(date('Y-m-d H:i',time()));
                if ($curr_time>=$start_time_h && $curr_time<=$end_time_h){
                    $ms_rate_night_add = getAdminPayCodeSys('ms_df_night_add', 256, $admin_id);
                    $ms_rate_night_add && $rate+=  $ms_rate_night_add;
                }
            }
        }
        return $rate;
    }

}
