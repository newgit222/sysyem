<?php
/**
 *
 * @Created   by PhpStorm.
 * @author    StarsPhp
 * @date      2022/12/8
 */
declare (strict_types=1);

namespace app\member\controller;

use app\common\library\enum\CodeEnum;
use app\common\logic\MsCacheQueue;
use app\common\logic\MsWeightQueue;
use app\common\model\EwmOrder;
use app\common\model\EwmPayCode;
use app\member\Logic\SecurityLogic;
use think\Cache;
use think\Db;
use think\Request;

class Pay extends Base
{

    protected $pay_code = null;

    public function __construct(Request $request = null)
    {
        $this->pay_code = $request->param('pay_code');
        $codes = $this->modelPayCode->where('status', 1)->column('code');
        if (!in_array($this->pay_code, $codes)) {
            $this->error('错误的通道编码!');
//            return json(['code'=>1,'msg'=>'错误的通道编码!']);
        }
        parent::__construct($request);
    }

    public function index()
    {
        return $this->fetch();
    }

    public function pay()
    {
        $isRefresh = $this->request->param('isRefresh',0);
        $member_id = $this->request->param('member_id',0);
        $pay_code_id = Db::name('pay_code')->where('code', $this->pay_code)->value('id');
        $where['code_type'] = $pay_code_id;

//        echo $pay_count_number_time;die;
        //总押金
        if ($isRefresh){
            if ($member_id != $this->agent_id){
                return json(['code'=>1,'data'=>['today_sk'=>0,'today_sk_ze'=>0,'today_tc' => 0,'disable_money'=>0]]);
            }
            //今日收款笔数
            $result = Db::name('ewm_order')
                ->field([
                    'COUNT(*) AS today_sk',
                    'SUM(order_pay_price) AS today_sk_ze',
                    'SUM(bonus_fee) AS today_tc'
                ])
                ->whereTime('add_time', 'today')
                ->where($where)
                ->where([
                    'gema_userid' => $this->agent_id,
                    'status' => 1
                ])
                ->find();

            $today_sk = !empty($result['today_sk']) ? $result['today_sk'] : 0;
            $today_sk_ze = !empty($result['today_sk_ze']) ? $result['today_sk_ze'] : 0;
            $today_tc = !empty($result['today_tc']) ? $result['today_tc'] : 0;
            Cache::set(date('Ymd',time()).'toady_success_count_msid_'.$this->agent_id.'_codeType_'.$pay_code_id,$today_sk);
            Cache::set(date('Ymd',time()).'toady_success_money_msid_'.$this->agent_id.'_codeType_'.$pay_code_id,$today_sk_ze);
            Cache::set(date('Ymd',time()).'toady_success_tc_msid_'.$this->agent_id.'_codeType_'.$pay_code_id,$today_tc);

            $today_sk = Cache::get(date('Ymd',time()).'toady_success_count_msid_'.$this->agent_id.'_codeType_'.$pay_code_id);
            //今日收款总额
            $today_sk_ze = Cache::get(date('Ymd',time()).'toady_success_money_msid_'.$this->agent_id.'_codeType_'.$pay_code_id);
            //今日提成
            $today_tc =Cache::get(date('Ymd',time()).'toady_success_tc_msid_'.$this->agent_id.'_codeType_'.$pay_code_id);
            $disable_money = $this->agent->disable_money;
            return json(['code'=>1,'data'=>['today_sk'=>$today_sk,'today_sk_ze'=>$today_sk_ze,'today_tc' => $today_tc,'disable_money'=>$disable_money]]);
        }else{
//            echo $pay_count_number_time;die;
            //今日收款笔数
            $today_sk = Cache::get(date('Ymd',time()).'toady_success_count_msid_'.$this->agent_id.'_codeType_'.$pay_code_id);
            //今日收款总额
            $today_sk_ze = Cache::get(date('Ymd',time()).'toady_success_money_msid_'.$this->agent_id.'_codeType_'.$pay_code_id);
            //今日提成
            $today_tc =Cache::get(date('Ymd',time()).'toady_success_tc_msid_'.$this->agent_id.'_codeType_'.$pay_code_id);
        }

        $total_yj = $this->agent->cash_pledge;
        //可用接单押金
        $normal_yj = $this->agent->cash_pledge;
        //最低接单押金
        $lowest_yj = $this->agent->cash_pledge;
        //在线人数
        $online_num = Db::name('ewm_pay_code')->where($where)->where(['status' => 1, 'is_delete' => 0,'ms_id'=>$this->agent_id])->count();
        //排队位置

        $admin_id = Db::name('ms')->where(['userid' => $this->agent_id])->value('admin_id');
        $sys_code_type = getAdminPayCodeSys('sys_code_type',256,$admin_id);
        if (empty($sys_code_type)){
            $sys_code_type = 0;
        }
        if ($sys_code_type == 1){
            $MsWeightQueue = new MsWeightQueue($pay_code_id.$admin_id."_weight_last_userids");
            $currentLocation = $MsWeightQueue->getCurrentLocation($this->agent_id);
        }else{
            $MsCaceQuQue = new MsCacheQueue($pay_code_id.$admin_id."_last_userids");
            $currentLocation = $MsCaceQuQue->getCurrentLocation($this->agent_id);

        }

        //冻结余额
        $disable_money = $this->agent->disable_money;

        //work_status
        $work_status = $this->agent->work_status;
        $head_data_arr = compact('today_sk', 'today_sk_ze', 'today_tc', 'total_yj', 'normal_yj', 'lowest_yj', 'online_num', 'work_status', 'currentLocation','disable_money');
        $this->assign('head_data', $head_data_arr);


        $member_view_queue = getAdminPayCodeSys('member_view_queue',$pay_code_id,$this->agent['admin_id']);
        if (empty($member_view_queue)){
            $member_view_queue = 1;
        }

        $ms_shoukuan_vermoney = getAdminPayCodeSys('ms_shoukuan_vermoney',$pay_code_id,$this->agent['admin_id']);
        if (empty($ms_shoukuan_vermoney)){
            $ms_shoukuan_vermoney = 2;
        }
        $this->assign('ms_shoukuan_vermoney',$ms_shoukuan_vermoney);

       $this->assign('member_view_queue',$member_view_queue);

        return $this->{$this->pay_code}();
    }


    /**
     *卡转卡
     * @return mixed
     */
    public function kzk()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'kzk';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('kzk');
    }

    /**
     * 支付宝转账
     * @return mixed
     */
    public function alipayTransfer()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'alipayTransfer';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('alipayTransfer');
    }



    /**
     * 支付宝转账
     * @return mixed
     */
    public function alipayPinMoney()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'alipayPinMoney';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('alipayPinMoney');
    }


    /**
     * 支付宝扫码
     * @return mixed
     */
    public function alipayCode()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'alipayCode';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('alipayCode');
    }


    /**
     * 支付宝扫码
     * @return mixed
     */
    public function alipayCodeSmall()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'alipayCodeSmall';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('alipayCodeSmall');
    }

    /**
     * 支付宝uid
     * @return mixed
     */
    public function alipayUid()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'alipayUid';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('alipayUid');
    }

    /**
     * 支付宝小额UID
     */
    public function alipayUidSmall()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'alipayUidSmall';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('alipayUidSmall');
    }

    /**
     * 支付宝UID转账
     */
    public function alipayUidTransfer()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'alipayUidTransfer';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }
        $pay_code_id = Db::name('pay_code')->where('code','alipayUidTransfer')->value('id');
        $is_pay_pass = getAdminPayCodeSys('is_pay_pass',$pay_code_id,$this->agent['admin_id']);
        if (empty($is_pay_pass)){
            $is_pay_pass = 1;
        }

        $this->assign('is_pay_pass',$is_pay_pass);
        $this->assign('fees',$fees);
        return $this->fetch('alipayUidTransfer');
    }


    /**
     * 抖音群红包
     */
    public function douyinGroupRed()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'douyinGroupRed';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('douyinGroupRed');
    }

    /**
     * 微信扫码
     */
    public function wechatCode()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'wechatCode';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('wechatCode');
    }

        /**
     * 微信黄金红包
     */
    public function wechatGoldRed()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'wechatCode';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('wechatGoldRed');
    }


    /**
     * 微信群红包
     */
    public function wechatGroupRed()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'wechatGroupRed';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('wechatGroupRed');
    }

    /**
     * 微信扫码
     */
    public function JDECard()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'JDECard';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        $pay_code_id = Db::name('pay_code')->where('code','JDECard')->value('id');
        $is_pay_pass = getAdminPayCodeSys('is_pay_pass',$pay_code_id,$this->agent['admin_id']);
        if (empty($is_pay_pass)){
            $is_pay_pass = 1;
        }
        $ms_show_card = getAdminPayCodeSys('ms_show_card',$pay_code_id,$this->agent['admin_id']);
        if (empty($ms_show_card)){
            $ms_show_card = 1;
        }
        $this->assign('ms_show_card',$ms_show_card);
        $this->assign('is_pay_pass',$is_pay_pass);

        return $this->fetch('JDECard');
    }

    /**
     * 微信扫码
     */
    public function alipayPassRed()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'alipayPassRed';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('alipayPassRed');
    }


    /**
     * 微信扫码
     */
    public function CnyNumber()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'CnyNumber';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $pay_code_id = Db::name('pay_code')->where('code','CnyNumber')->value('id');
        $is_pay_pass = getAdminPayCodeSys('is_pay_pass',$pay_code_id,$this->agent['admin_id']);
        $is_show_not_success = getAdminPayCodeSys('is_show_not_success',$pay_code_id,$this->agent['admin_id']);
        $is_confirm_box = getAdminPayCodeSys('is_confirm_box',$pay_code_id,$this->agent['admin_id']);
        if (empty($is_show_not_success)){
            $is_show_not_success = 2;
        }
        if (empty($is_confirm_box)){
            $is_confirm_box = 2;
        }
        if (empty($is_pay_pass)){
            $is_pay_pass = 1;
        }
        $this->assign('is_pay_pass',$is_pay_pass);
        $this->assign('is_show_not_success',$is_show_not_success);
        $this->assign('is_confirm_box',$is_confirm_box);
        $this->assign('fees',$fees);
        return $this->fetch('CnyNumber');
    }



    /**
     * 微信扫码
     */
    public function CnyNumberAuto()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'CnyNumberAuto';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('CnyNumberAuto');
    }



    /**
     * 微信扫码
     */
    public function AppletProducts()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'AppletProducts';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('AppletProducts');
    }




    /**
     * 微信扫码
     */
    public function QQFaceRed()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'QQFaceRed';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('QQFaceRed');
    }


    /**
     * 微信扫码
     */
    public function alipaySmallPurse()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'alipaySmallPurse';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('alipaySmallPurse');
    }


    /**
     * 微信扫码
     */
    public function taoBaoMoneyRed()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'taoBaoMoneyRed';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('taoBaoMoneyRed');
    }


    /**
     * 微信扫码
     */
    public function alipayTransferCode()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'alipayTransferCode';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('alipayTransferCode');
    }


    /**
     * 微信扫码
     */
    public function alipayWorkCard()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'alipayWorkCard';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('alipayWorkCard');
    }



    /**
     * 微信扫码
     */
    public function alipayWorkCardBig()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'alipayWorkCardBig';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('alipayWorkCardBig');
    }



    /**
     * 微信扫码
     */
    public function QianxinTransfer()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'QianxinTransfer';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('QianxinTransfer');
    }



    /**
     * 微信扫码
     */
    public function usdtTrc()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'usdtTrc';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('usdtTrc');
    }


    /**
     * 微信扫码
     */
    public function taoBaoEcard()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'taoBaoEcard';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }
        $pay_code_id = Db::name('pay_code')->where('code','taoBaoEcard')->value('id');
        $is_pay_pass = getAdminPayCodeSys('is_pay_pass',$pay_code_id,$this->agent['admin_id']);

        if (empty($is_pay_pass)){
            $is_pay_pass = 1;
        }
        $this->assign('is_pay_pass',$is_pay_pass);
        $this->assign('fees',$fees);
        return $this->fetch('taoBaoEcard');
    }



    /**
     * 聚合码
     * @return mixed
     */
    public function AggregateCode()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'AggregateCode';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('AggregateCode');
    }


    /**
     * 当面付
     * @return mixed
     */
    public function alipayF2F()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'alipayF2F';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('alipayF2F');
    }


    /**
     * 手机网站
     * @return mixed
     */
    public function alipayWap()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'alipayWap';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('alipayWap');
    }


    /**
     * 淘宝直付
     * @return mixed
     */
    public function taoBaoDirectPay()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'taoBaoDirectPay';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('taoBaoDirectPay');
    }




    /**
     * 淘宝直付
     * @return mixed
     */
    public function JunWeb()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'JunWeb';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }
        $pay_code_id = Db::name('pay_code')->where('code','JDECard')->value('id');
        $is_pay_pass = getAdminPayCodeSys('is_pay_pass',$pay_code_id,$this->agent['admin_id']);
        if (empty($is_pay_pass)){
            $is_pay_pass = 1;
        }
        $ms_show_card = getAdminPayCodeSys('ms_show_card',$pay_code_id,$this->agent['admin_id']);
        if (empty($ms_show_card)){
            $ms_show_card = 1;
        }
        $this->assign('ms_show_card',$ms_show_card);
        $this->assign('is_pay_pass',$is_pay_pass);
        $this->assign('fees',$fees);
        return $this->fetch('JunWeb');
    }


    /**
     * 汇元易付卡
     * @return mixed
     */
    public function HuiYuanYiFuKa()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'HuiYuanYiFuKa';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('HuiYuanYiFuKa');
    }

    /**
     * 汇元易付卡
     * @return mixed
     */
    public function LeFuTianHongKa()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'LeFuTianHongKa';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('LeFuTianHongKa');
    }






    /**
     * 汇元易付卡
     * @return mixed
     */
    public function DingDingGroup()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'DingDingGroup';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('DingDingGroup');
    }

    /**
     * Qq扫码
     */
    public function QqCode()
    {
        $where['a.gema_userid'] = $this->agent_id;
        $where['b.code'] = 'alipayCode';
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        $where['add_time'] = ['between time',[$startTime,$endTime]];;

        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->sum('a.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->sum('a.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($querys){
            $querys->where('a.status',1);
        })->count('a.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }

        $this->assign('fees',$fees);
        return $this->fetch('QqCode');
    }


    public function searchStats(){
        $request = Request::instance();
        $status = $request->param('status', -1, 'intval');
        $map = [];
        $order_id = addslashes(trim($request->param('id', '')));
        if (!empty($order_id)){
            $order_id = $order_id-20000000;
            $map['o.id'] = $order_id;
        }
        //订单编号
        $order_no = addslashes(trim($request->param('order_no', '')));
        $order_no && $map['o.order_no'] = ['like', '%' . $order_no . '%'];

        //code_id
        $code_id = addslashes(trim($request->param('code_id', '')));
        $code_id && $map['o.code_id'] = $code_id;

        //昵称
        $account_name = addslashes(trim($request->param('account_name', '')));
        $account_name && $map['e.account_name'] = ['like', '%' . $account_name . '%'];

        //账号
        $bank_name = addslashes(trim($request->param('bank_name', '')));
        $bank_name && $map['e.bank_name'] = ['like', '%' . $bank_name . '%'];
        $image_url = addslashes(trim($request->param('image_url', '')));
        $image_url && $map['e.image_url'] = ['like', '%' . $image_url . '%'];

        $remark = addslashes(trim($request->param('remark', '')));
        $remark && $map['e.remark'] = ['like', '%' . $remark . '%'];

        //账号
        $account_number = addslashes(trim($request->param('account_number', '')));
        $account_number && $map['e.account_number'] = ['like', '%' . $account_number . '%'];

        $map['p.code'] = $this->pay_code;


        //用户名
        $order_price = addslashes(trim($request->param('order_price', '')));
        !empty($order_price) && $map['o.order_price'] = $order_price;

        //订单金额
        $gema_username = addslashes(trim($request->param('gema_username', '')));
        !empty($gema_username) && $map['o.gema_username'] = ['like', '%' . $gema_username . '%'];

        $note = addslashes(trim($request->param('note', '')));
        !empty($note) && $map['o.note'] = ['like', '%' . $note . '%'];

        //收款人姓名
        $payUserName = addslashes(trim($request->param('pay_username', '')));
        $payUserName && $map['o.pay_username'] = addslashes(trim($payUserName));

        //收款人姓名
        $payUserName = addslashes(trim($request->param('cardAccount', '')));
        $payUserName && $map['o.cardAccount'] = addslashes(trim($payUserName));

        //正式姓名
        $pay_user_name = addslashes(trim($request->param('pay_user_name', '')));
        $pay_user_name && $map['o.pay_user_name'] = ['eq', $pay_user_name];


        //卡密
        $cardKey = addslashes(trim($request->param('cardKey', '')));
        $cardKey && $map['o.cardKey'] = ['eq', $cardKey];

        $amount = addslashes(trim($request->param('amount', '')));
        $amount && $map['order_price'] = $amount;
        //新增其他条件
        ($status != -1) && $map['o.status'] = intval($status);
        //时间
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        if (!empty($request->param('start_time')) && !empty($request->param('end_time'))) {
            $startTime = $request->param('start_time');
            $endTime = $request->param('end_time');
        }

        if (!empty($request->param('start_update_time')) && !empty($request->param('end_update_time'))) {
            $startUpdateTime = $request->param('start_update_time');
            $endUpdateTime = $request->param('end_update_time');
            $map['o.pay_time'] = ['between time',[$startUpdateTime,$endUpdateTime]];;
        }else{
            $map['o.add_time'] = ['between time',[$startTime,$endTime]];;
        }


        if (!empty($request->param('code_type')) && $request->param('code_type') != 1) {
            $map['p.code'] = $request->param('code_type');
        }

        $map['o.gema_userid'] = $this->agent_id;

//        print_r($map);die;
        $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('o')->join("ewm_pay_code e", "o.code_id=e.id", "left")->join('pay_code p','o.code_type = p.id')->where($map)->sum('o.order_price'));

        $fees['success_amount'] = sprintf("%.2f",Db::name('ewm_order')->alias('o')->join("ewm_pay_code e", "o.code_id=e.id", "left")->join('pay_code p','o.code_type = p.id')->where($map)->where(function ($querys){
            $querys->where('o.status',1);
        })->sum('o.order_price'));

        $fees['count'] = Db::name('ewm_order')->alias('o')->join("ewm_pay_code e", "o.code_id=e.id", "left")->join('pay_code p','o.code_type = p.id')->where($map)->count('o.id');
        $fees['success_count'] = Db::name('ewm_order')->alias('o')->join("ewm_pay_code e", "o.code_id=e.id", "left")->join('pay_code p','o.code_type = p.id')->where($map)->where(function ($querys){
            $querys->where('o.status',1);
        })->count('o.id');
        if ($fees['count'] > 0){
            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
        }else{
            $fees['success_rate'] = 0;
        }
        return json(['code'=>0,'msg'=>'请求成功','data'=>$fees]);
//        $this->assign('fees',$fees);
    }

    public function lists()
    {
        $request = Request::instance();
        $status = $request->param('status', -1, 'intval');
        $paycode = $request->param('pay_code', -1, 'intval');
        $map = [];
        $order_id = addslashes(trim($request->param('id', '')));
        if (!empty($order_id)){
            $order_id = $order_id-20000000;
            $map['o.id'] = $order_id;
        }
        //订单编号
        $order_no = addslashes(trim($request->param('order_no', '')));
        $order_no && $map['o.order_no'] = ['like', '%' . $order_no . '%'];

        //实际支付金额
        $order_pay_price = addslashes(trim($request->param('order_pay_price', '')));
        $order_pay_price && $map['order_pay_price'] = $order_pay_price;

        //code_id
        $code_id = addslashes(trim($request->param('code_id', '')));
        $code_id && $map['o.code_id'] = $code_id;

        //昵称
        $account_name = addslashes(trim($request->param('account_name', '')));
        $account_name && $map['e.account_name'] = ['like', '%' . $account_name . '%'];

        //账号
        $bank_name = addslashes(trim($request->param('bank_name', '')));
        $bank_name && $map['e.bank_name'] = ['like', '%' . $bank_name . '%'];
        //账号
        $image_url = addslashes(trim($request->param('image_url', '')));
        $image_url && $map['e.image_url'] = ['like', '%' . $image_url . '%'];


        $remark = addslashes(trim($request->param('remark', '')));
        $remark && $map['e.remark'] = ['like', '%' . $remark . '%'];
        //账号
        $account_number = addslashes(trim($request->param('account_number', '')));
        $account_number && $map['e.account_number'] = ['like', '%' . $account_number . '%'];
        $code_type = Db::name('pay_code')->where('code',$this->pay_code)->value('id');
//        print_r($code_type);die;
        $map['o.code_type'] = $code_type;



//        $payname = Db::name('pay_code')->where('id', $paycode)->value('code');
//        print_r($paycode);die;
//        $this->assign('pay_code', $payname);

        //用户名
        $order_price = addslashes(trim($request->param('order_price', '')));
        !empty($order_price) && $map['o.order_price'] = $order_price;

        //订单金额
        $gema_username = addslashes(trim($request->param('gema_username', '')));
        !empty($gema_username) && $map['o.gema_username'] = ['like', '%' . $gema_username . '%'];

        $note = addslashes(trim($request->param('note', '')));
        !empty($note) && $map['o.note'] = ['like', '%' . $note . '%'];

        //收款人姓名
        $payUserName = addslashes(trim($request->param('pay_username', '')));
        $payUserName && $map['o.pay_username'] = addslashes(trim($payUserName));

        //正式姓名
        $pay_user_name = addslashes(trim($request->param('pay_user_name', '')));
        $pay_user_name && $map['o.pay_user_name'] = ['eq', $pay_user_name];

        $pay_user_name = addslashes(trim($request->param('cardAccount', '')));
        $pay_user_name && $map['o.cardAccount'] = ['eq', $pay_user_name];

        $amount = addslashes(trim($request->param('amount', '')));
        $amount && $map['o.order_price'] = $amount;
        //新增其他条件
        ($status != -1) && $map['o.status'] = intval($status);

        $this->assign('status', $status);
        //时间
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        if (!empty($request->param('start_time')) && !empty($request->param('end_time'))) {
            $startTime = $request->param('start_time');
            $endTime = $request->param('end_time');
        }
        if (!empty($request->param('start_update_time')) && !empty($request->param('end_update_time'))) {
            $startUpdateTime = $request->param('start_update_time');
            $endUpdateTime = $request->param('end_update_time');
            $map['pay_time'] = ['between time',[$startUpdateTime,$endUpdateTime]];;
        }else{
            $map['add_time'] = ['between time',[$startTime,$endTime]];;
        }

//        if (!empty($request->param('code_type')) && $request->param('code_type') != 1) {
//            $map['p.code'] = $request->param('code_type');
//            $this->assign('code_type', $request->param('code_type'));
//        }
        $fileds = ["o.*", 'e.account_name','e.bank_name','e.account_number','e.image_url', 'e.remark', 'e.account_type'];
        $map['gema_userid'] = $this->agent_id;

        if ($this->request->isAjax()) {
            $list = Db::name('ewm_order')->alias('o')->field($fileds)
//                ->join("ms u", "o.gema_userid=u.userid", "left")
                ->join("ewm_pay_code e", "o.code_id=e.id", "left")
//                ->join("pay_code p", "o.code_type=p.id", "left")
                ->where($map)
                ->where('e.id', '>', 0)
                ->order('id desc')
                ->paginate($this->request->param('limit', 10));
//            print_r(Db::getlastSql());die;
            $data = $list->items();
            foreach ($data as $key => &$vals) {
//                $vals['id'] = $vals['id'] + 20000000;
                $vals['s_type_name'] = '无';
                $vals['s_type_name'] = '无';
//                $code = EwmPayCode::where(['id' => $vals['code_id']])->find();
//                $vals['account_number'] = $code['account_number'];
//                $vals['bank_name'] = $code['bank_name'];
//                $vals['account_name'] = $code['account_name'];
//                $vals['qr_image'] = $code['image_url'];
                $vals['strArea'] = '无';
                $vals['group_name'] = '无';
//                //用户真实姓名
                $vals['real_name'] = ctype_alnum($vals['pay_user_name']) ? $vals['pay_username'] : $vals['pay_user_name'];
                $vals['pay_time'] = $vals['pay_time'] ? date('Y-m-d H:i:s', $vals['pay_time']) : '---';
                $vals['add_time'] = $vals['add_time'] ? date('Y-m-d H:i:s', $vals['add_time']) : '---';
            }

            $this->result($list || !empty($list) ?
                [
                    'code' => CodeEnum::SUCCESS,
                    'msg' => '',
                    'count' => $list->total(),
                    'data' => $data
                ] : [
                    'code' => CodeEnum::ERROR,
                    'msg' => '暂无数据',
                    'count' => $list->count(),
                    'data' => []
                ]
            );
        }

    }



    public function Cnylists()
    {
        $request = Request::instance();
        $status = $request->param('status', -1, 'intval');
        $paycode = $request->param('pay_code', -1, 'intval');
        $map = [];
        //订单编号
        $order_no = addslashes(trim($request->param('order_no', '')));
        $order_no && $map['order_no'] = ['like', '%' . $order_no . '%'];

        //实际支付金额
        $order_pay_price = addslashes(trim($request->param('order_pay_price', '')));
        $order_pay_price && $map['order_pay_price'] = $order_pay_price;

        //code_id
        $code_id = addslashes(trim($request->param('code_id', '')));
        $code_id && $map['e.code_id'] = $code_id;

        //昵称
        $account_name = addslashes(trim($request->param('account_name', '')));
        $account_name && $map['e.account_name'] = ['like', '%' . $account_name . '%'];

        //账号
        $bank_name = addslashes(trim($request->param('bank_name', '')));
        $bank_name && $map['e.bank_name'] = ['like', '%' . $bank_name . '%'];

        //账号
        $account_number = addslashes(trim($request->param('account_number', '')));
        $account_number && $map['e.account_number'] = ['like', '%' . $account_number . '%'];

        $map['p.code'] = $this->pay_code;

        $payname = Db::name('pay_code')->where('id', $paycode)->value('code');
        $this->assign('pay_code', $payname);

        //用户名
        $order_price = addslashes(trim($request->param('order_price', '')));
        !empty($order_price) && $map['o.order_price'] = $order_price;

        //订单金额
        $gema_username = addslashes(trim($request->param('gema_username', '')));
        !empty($gema_username) && $map['o.gema_username'] = ['like', '%' . $gema_username . '%'];

        $note = addslashes(trim($request->param('note', '')));
        !empty($note) && $map['o.note'] = ['like', '%' . $note . '%'];

        //收款人姓名
        $payUserName = addslashes(trim($request->param('pay_username', '')));
        $payUserName && $map['pay_username'] = addslashes(trim($payUserName));

        //正式姓名
        $pay_user_name = addslashes(trim($request->param('pay_user_name', '')));
        $pay_user_name && $map['pay_user_name'] = ['eq', $pay_user_name];

        $amount = addslashes(trim($request->param('amount', '')));
        $amount && $map['order_price'] = $amount;

        $cardKey= addslashes(trim($request->param('cardKey', '')));
        $cardKey &&   $map['cardKey'] = ['eq', $cardKey];
        //新增其他条件
        ($status != -1) && $map['o.status'] = intval($status);

        $this->assign('status', $status);
        //时间
        $order_time = getAdminPayCodeSys('order_invalid_time',40,$this->agent['admin_id']);
        if (empty($order_time)){
            $order_time = 10;
        }
        $startTime = date('Y-m-d H:i:s', time() - $order_time*60);
        $endTime = date('Y-m-d H:i:s', time());
//        if (!empty($request->param('start_time')) && !empty($request->param('end_time'))) {
//            $startTime = $request->param('start_time');
//            $endTime = $request->param('end_time');
//        }
        $map['o.add_time'] = ['between time',[$startTime,$endTime]];
        if (!empty($request->param('code_type')) && $request->param('code_type') != 1) {
            $map['p.code'] = $request->param('code_type');
            $this->assign('code_type', $request->param('code_type'));
        }
        $fileds = ["o.*", "u.mobile", "u.account", "p.name"];
        $map['gema_userid'] = $this->agent_id;
        $map['o.status'] = 0;
//        print_r($map);die;

        if ($this->request->isAjax()) {
            $list = Db::name('ewm_order')->alias('o')->field($fileds)
                ->join("ms u", "o.gema_userid=u.userid", "left")
                ->join("ewm_pay_code e", "o.code_id=e.id", "left")
                ->join("pay_code p", "o.code_type=p.id", "left")
                ->where($map)
                ->order('id desc')
                ->paginate($this->request->param('limit', 10));

            $data = $list->items();
            foreach ($data as $key => &$vals) {
                $vals['s_type_name'] = '无';
                $vals['s_type_name'] = '无';
                $code = EwmPayCode::where(['id' => $vals['code_id']])->find();
                $vals['account_number'] = $code['account_number'];
                $vals['bank_name'] = $code['bank_name'];
                $vals['account_name'] = $code['account_name'];
                $vals['qr_image'] = $code['image_url'];
                $vals['strArea'] = '无';
                $vals['group_name'] = '无';
                //用户真实姓名
                $vals['real_name'] = ctype_alnum($vals['pay_user_name']) ? $vals['pay_username'] : $vals['pay_user_name'];
                $vals['pay_time'] = $vals['pay_time'] ? date('Y-m-d H:i:s', $vals['pay_time']) : '---';
                $vals['add_time'] = $vals['add_time'] ? date('Y-m-d H:i:s', $vals['add_time']) : '---';
            }

            $this->result($list || !empty($list) ?
                [
                    'code' => CodeEnum::SUCCESS,
                    'msg' => '',
                    'count' => $list->total(),
                    'data' => $data
                ] : [
                    'code' => CodeEnum::ERROR,
                    'msg' => '暂无数据',
                    'count' => $list->count(),
                    'data' => []
                ]
            );
        }

    }







    public function JdEKaLists()
    {
        $request = Request::instance();
        $status = $request->param('status', -1, 'intval');
        $paycode = $request->param('pay_code', -1, 'intval');
        $map = [];
        $order_id = addslashes(trim($request->param('id', '')));
        if (!empty($order_id)){
            $order_id = $order_id-20000000;
            $map['o.id'] = $order_id;
        }
        //订单编号
        $order_no = addslashes(trim($request->param('order_no', '')));
        $order_no && $map['order_no'] = ['like', '%' . $order_no . '%'];

        //实际支付金额
        $order_pay_price = addslashes(trim($request->param('order_pay_price', '')));
        $order_pay_price && $map['order_pay_price'] = $order_pay_price;

        //code_id
        $code_id = addslashes(trim($request->param('code_id', '')));
        $code_id && $map['o.code_id'] = $code_id;

        //昵称
        $account_name = addslashes(trim($request->param('account_name', '')));
        $account_name && $map['o.account_name'] = ['like', '%' . $account_name . '%'];

        //账号
        $bank_name = addslashes(trim($request->param('bank_name', '')));
        $bank_name && $map['o.bank_name'] = ['like', '%' . $bank_name . '%'];

        //账号
        $account_number = addslashes(trim($request->param('account_number', '')));
        $account_number && $map['o.account_number'] = ['like', '%' . $account_number . '%'];

        $map['p.code'] = $this->pay_code;

        $payname = Db::name('pay_code')->where('id', $paycode)->value('code');
        $this->assign('pay_code', $payname);

        //用户名
        $order_price = addslashes(trim($request->param('order_price', '')));
        !empty($order_price) && $map['o.order_price'] = $order_price;

        //订单金额
        $gema_username = addslashes(trim($request->param('gema_username', '')));
        !empty($gema_username) && $map['o.gema_username'] = ['like', '%' . $gema_username . '%'];

        $note = addslashes(trim($request->param('note', '')));
        !empty($note) && $map['o.note'] = ['like', '%' . $note . '%'];

        //收款人姓名
        $payUserName = addslashes(trim($request->param('pay_username', '')));
        $payUserName && $map['pay_username'] = addslashes(trim($payUserName));

        //正式姓名
        $pay_user_name = addslashes(trim($request->param('pay_user_name', '')));
        $pay_user_name && $map['pay_user_name'] = ['eq', $pay_user_name];

        $amount = addslashes(trim($request->param('amount', '')));
        $amount && $map['order_price'] = $amount;
        //新增其他条件
        ($status != -1) && $map['o.status'] = intval($status);

        $this->assign('status', $status);
        //时间
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        if (!empty($request->param('start_time')) && !empty($request->param('end_time'))) {
            $startTime = $request->param('start_time');
            $endTime = $request->param('end_time');
        }
        $map['add_time'] = ['between time',[$startTime,$endTime]];;
        if (!empty($request->param('code_type')) && $request->param('code_type') != 1) {
            $map['p.code'] = $request->param('code_type');
            $this->assign('code_type', $request->param('code_type'));
        }
        $fileds = ["o.*", "u.mobile", "u.account", "p.name"];
        $map['gema_userid'] = $this->agent_id;

        $admin_id = $this->modelMs->where('userid', $this->agent_id)->value('admin_id');
        $jd = getAdminPayCodeSys('cardKey_order_view', 43, $admin_id);
        $tb = getAdminPayCodeSys('cardKey_order_view', 52, $admin_id);
        if (empty($jd)){
            $jd = 2;
        }

        if (empty($tb)){
            $tb = 2;
        }

        if($jd == 1 || $tb == 1){
            $map['cardKey'] =['neq',''];
        }



        $cardKey= addslashes(trim($request->param('cardKey', '')));
        $cardKey &&   $map['cardKey'] = ['eq', $cardKey];

        if ($this->request->isAjax()) {
            $list = Db::name('ewm_order')->alias('o')->field($fileds)
                ->join("ms u", "o.gema_userid=u.userid", "left")
                ->join("pay_code p", "o.code_type=p.id", "left")
                ->where($map)
                ->order('id desc')
                ->paginate($this->request->param('limit', 10));

            $data = $list->items();
            foreach ($data as $key => &$vals) {
                $vals['s_type_name'] = '无';
                $vals['s_type_name'] = '无';
                $code = EwmPayCode::where(['id' => $vals['code_id']])->find();
                $vals['account_number'] = $code['account_number'];
                $vals['bank_name'] = $code['bank_name'];
                $vals['account_name'] = $code['account_name'];
                $vals['qr_image'] = $code['image_url'];
                $vals['strArea'] = '无';
                $vals['group_name'] = '无';
                //用户真实姓名
                $vals['real_name'] = ctype_alnum($vals['pay_user_name']) ? $vals['pay_username'] : $vals['pay_user_name'];
                $vals['pay_time'] = $vals['pay_time'] ? date('Y-m-d H:i:s', $vals['pay_time']) : '---';
                $vals['add_time'] = $vals['add_time'] ? date('Y-m-d H:i:s', $vals['add_time']) : '---';
            }

            $this->result($list || !empty($list) ?
                [
                    'code' => CodeEnum::SUCCESS,
                    'msg' => '',
                    'count' => $list->total(),
                    'data' => $data
                ] : [
                    'code' => CodeEnum::ERROR,
                    'msg' => '暂无数据',
                    'count' => $list->count(),
                    'data' => []
                ]
            );
        }

    }

    /**
     * 确认收款
     */
    public function issueOrder()
    {
        $orderId = intval($this->request->post('id'));

//        $pay_code_id = Db::name('pay_code')->where('code','JDECard')->value('id');
        $pay_code_id = Db::name('ewm_order')->where('id',$orderId)->value('code_type');
//        Db::name('ewm_order')->where('id',$orderId)->update(['status'=>0]);
//        $legality = Db::name('ewm_order')->where('id',$orderId)->value('legality');
//        if ($legality != 1){
//            $this->error('订单状态异常！');
//        }
        $is_pay_pass = getAdminPayCodeSys('is_pay_pass',$pay_code_id,$this->agent['admin_id']);
        if (empty($is_pay_pass)){
            $is_pay_pass = 1;
        }
//        print_r(cookie('member_check_command_ok'));die;

        $ms_shoukuan_vermoney = getAdminPayCodeSys('ms_shoukuan_vermoney',$pay_code_id,$this->agent['admin_id']);
        if (empty($ms_shoukuan_vermoney)){
            $ms_shoukuan_vermoney = 2;
        }

        if ($ms_shoukuan_vermoney == 1){
            $pay_price = trim($this->request->param('pay_price'));
            $GemaPayOrder = new \app\common\model\EwmOrder();
            $orderInfo = $GemaPayOrder->where(['id'=>$orderId,'gema_userid'=>$this->agent_id,'admin_id'=>$this->agent['admin_id']])->find();

            if ($orderInfo['order_pay_price'] != $pay_price){
                $this->error('收款金额校验失败');
            }
        }

        if ($is_pay_pass == 1){
            if (empty(cookie('member_check_command_ok'))){
                $security = $this->request->post('pass');

                $GemaOrder = new \app\common\logic\EwmOrder();
                $res = $GemaOrder->setOrderSucessByUser($orderId, $this->agent->userid, $security, 0, 0);
            }else{
                $GemaOrder = new \app\common\logic\EwmOrder();
                $res = $GemaOrder->setOrderSucessByUserNullPayPass($orderId, $this->agent->userid, 0, 0);

            }
        }else{
            $GemaOrder = new \app\common\logic\EwmOrder();
            $res = $GemaOrder->setOrderSucessByUserNullPayPass($orderId, $this->agent->userid, 0, 0);

        }

        if ($res['code'] == CodeEnum::ERROR) {
            $this->error($res['msg']);
        }
        $orderNo = Db::name('ewm_order')->where('id', $orderId)->value('order_no');
        action_log('补单', '码商' . $this->agent->userid . '强制成功订单：' . $orderNo);
        $this->success('操作成功');
    }


    public function selectCard(){
        $orderId = intval($this->request->post('id'));
        $security = $this->request->post('pass');
        $SecurityLogic = new SecurityLogic();
        //判断交易密码
        $result = $SecurityLogic->checkSecurityByUserId($this->agent_id, $security);

        //判断用收款ip是否和最近登录的ip是否一致
        if ($result['code'] == CodeEnum::ERROR) {
            $this->error($result['msg']);
        }
        $orderInfo = Db::name('ewm_order')->where('id',$orderId)->field('code_type,add_time,cardKey,gema_userid')->find();
        if ($orderInfo['gema_userid'] != $this->agent_id){
            $this->error('非法操作');
        }
        $order_invalid_time = getAdminPayCodeSys('order_invalid_time',$orderInfo['code_type'],$this->agent['admin_id']);
        if (empty($order_invalid_time)){
            $order_invalid_time = 10;
        }

        if ($orderInfo['add_time'] > time() - $order_invalid_time*60){
            $this->success('查询成功','',$orderInfo['cardKey']);
        }else{
            $this->error('订单已过期,不可查看');
        }



    }


    /**
     * 确认收款
     */
    public function issueOrders()
    {
        $riqie_success_orders = getAdminPayCodeSys('riqie_success_orders',256,$this->agent['admin_id']);
        if (empty($riqie_success_orders)){
            $riqie_success_orders = 2;
        }

        $no_orders = getAdminPayCodeSys('no_orders',256,$this->agent['admin_id']);

        if (empty($no_orders)){
            $no_orders = 1;
        }
        if ($no_orders == 2 ){
            if ($riqie_success_orders == 1){
                $this->error('系统日切中，无法完成订单！');
            }
        }
        $orderId = intval($this->request->post('id'));

//        $pay_code_id = Db::name('pay_code')->where('code','JDECard')->value('id');
        $pay_code_id = Db::name('ewm_order')->where('id',$orderId)->value('code_type');
        Db::name('ewm_order')->where('id',$orderId)->update(['status'=>0]);
//        $legality = Db::name('ewm_order')->where('id',$orderId)->value('legality');
//        if ($legality != 1){
//            $this->error('订单状态异常！');
//        }
        $is_pay_pass = getAdminPayCodeSys('is_pay_pass',$pay_code_id,$this->agent['admin_id']);
        if (empty($is_pay_pass)){
            $is_pay_pass = 1;
        }
        if ($is_pay_pass == 1){
            $security = $this->request->post('pass');

            $GemaOrder = new \app\common\logic\EwmOrder();
            $res = $GemaOrder->setOrderSucessByUser($orderId, $this->agent->userid, $security, 0, 0);

        }else{
            $GemaOrder = new \app\common\logic\EwmOrder();
            $res = $GemaOrder->setOrderSucessByUserNullPayPass($orderId, $this->agent->userid, 0, 0);

        }

        if ($res['code'] == CodeEnum::ERROR) {
            $this->error($res['msg']);
        }
        $orderNo = Db::name('ewm_order')->where('id', $orderId)->value('order_no');
        action_log('补单', '码商' . $this->agent->userid . '强制成功订单：' . $orderNo);
        $this->success('操作成功');
    }


    /**
     * 标记异常
     */
    public function islegality(){
        $orderId = intval($this->request->post('id'));
        $ms_id = Db::name('ewm_order')->where('id',$orderId)->value('gema_userid');
        if ($ms_id != $this->agent_id){
            $this->error('非法操作！');
        }
        $status = Db::name('ewm_order')->where('id',$orderId)->value('status');
//        $status = Db::name('ewm_order')->where('id',$orderId)->value('status');
        if ($status != 0){
            $this->error('订单状态异常，不可操作！');
        }
        $res = Db::name('ewm_order')->where('id',$orderId)->update(['legality' => 2]);
        if ($res === false){
            $this->error('操作异常！');
        }else{
            $this->success('操作成功');
        }

    }

    /**
     * 导出订单
     */
    public function exportOrder()
    {
        $request = Request::instance();
        $status = $request->param('status', -1, 'intval');
        $paycode = $request->param('pay_code', -1, 'intval');
        $map = [];
        //订单编号
        $order_no = addslashes(trim($request->param('order_no', '')));
        $order_no && $map['order_no'] = ['like', '%' . $order_no . '%'];

        //code_id
        $code_id = addslashes(trim($request->param('code_id', '')));
        $code_id && $map['o.code_id'] = $code_id;

        //昵称
        $account_name = addslashes(trim($request->param('account_name', '')));
        $account_name && $map['o.account_name'] = ['like', '%' . $account_name . '%'];

        //账号
        $bank_name = addslashes(trim($request->param('bank_name', '')));
        $bank_name && $map['o.bank_name'] = ['like', '%' . $bank_name . '%'];

        //账号
        $account_number = addslashes(trim($request->param('account_number', '')));
        $bank_name && $map['o.bank_name'] = ['like', '%' . $bank_name . '%'];

        $map['p.code'] = $this->pay_code;

        $payname = Db::name('pay_code')->where('id', $paycode)->value('code');
        $this->assign('pay_code', $payname);

        //用户名
        $order_price = addslashes(trim($request->param('order_price', '')));
        !empty($order_price) && $map['o.order_price'] = $order_price;

        //订单金额
        $gema_username = addslashes(trim($request->param('gema_username', '')));
        !empty($gema_username) && $map['o.gema_username'] = ['like', '%' . $gema_username . '%'];

        $note = addslashes(trim($request->param('note', '')));
        !empty($note) && $map['o.note'] = ['like', '%' . $note . '%'];

        //收款人姓名
        $payUserName = addslashes(trim($request->param('pay_username', '')));
        $payUserName && $map['pay_username'] = addslashes(trim($payUserName));

        //正式姓名
        $pay_user_name = addslashes(trim($request->param('pay_user_name', '')));
        $pay_user_name && $map['pay_user_name'] = ['eq', $pay_user_name];

        $amount = addslashes(trim($request->param('amount', '')));
        $amount && $map['order_price'] = $amount;
        //新增其他条件
        ($status != -1) && $map['o.status'] = intval($status);

        $this->assign('status', $status);
        //时间
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        if (!empty($request->param('start_time')) && !empty($request->param('end_time'))) {
            $startTime = $request->param('start_time');
            $endTime = $request->param('end_time');
        }
        $map['add_time'] = ['between time',[$startTime,$endTime]];;
        if (!empty($request->param('code_type')) && $request->param('code_type') != 1) {
            $map['p.code'] = $request->param('code_type');
            $this->assign('code_type', $request->param('code_type'));
        }
        $fileds = ["o.*", "u.mobile", "u.account", "p.name"];
        $map['gema_userid'] = $this->agent_id;
        $data = Db::name('ewm_order')->alias('o')->field($fileds)
                ->join("ms u", "o.gema_userid=u.userid", "left")
                ->join("pay_code p", "o.code_type=p.id", "left")
                ->where($map)
                ->order('id desc')
                ->select();
        foreach ($data as $key => &$vals) {
            $vals['s_type_name'] = '无';
            $vals['s_type_name'] = '无';
            $code = EwmPayCode::where(['id' => $vals['code_id']])->find();
            $vals['account_number'] = $code['account_number'];
            $vals['bank_name'] = $code['bank_name'];
            $vals['account_name'] = $code['account_name'];
            $vals['qr_image'] = $code['image_url'];
            $vals['strArea'] = '无';
            $vals['group_name'] = '无';
            //用户真实姓名
            $vals['real_name'] = ctype_alnum($vals['pay_user_name']) ? $vals['pay_username'] : $vals['pay_user_name'];
            $vals['pay_time'] = $vals['pay_time'] ? date('Y-m-d H:i:s', $vals['pay_time']) : '---';
            $vals['add_time'] = $vals['add_time'] ? date('Y-m-d H:i:s', $vals['add_time']) : '---';
        }

        //组装header 响应html为execl 感觉比PHPExcel类更快
        $orderStatus = ['待支付', '已支付', '已关闭', '已退款'];
        $strTable = '<table width="500" border="1">';
        $strTable .= '<tr>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">订单号</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">订单金额</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="100">收款人</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">收款账号</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">收款方</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">下单时间</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">支付时间</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">实际支付金额</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">备注</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">订单状态</td>';
        $strTable .= '</tr>';
        if (is_array($data)) {
            foreach ($data as $k => $val) {
                $strTable .= '<tr>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['order_no'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['order_price'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['account_name'] . ' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['account_number'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['bank_name']. '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'. $val['add_time'] .'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['pay_time'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['order_pay_price'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['note'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $orderStatus[$val['status']] . '</td>';
                $strTable .= '</tr>';
                unset($data[$k]);
            }
        }
        $strTable .= '</table>';
        downloadExcel($strTable, 'orders_' . $this->pay_code);
    }

    public function lastOrder()
    {
        $ewmOrderModel = new EwmOrder();
        $where = [
            'gema_userid' => $this->agent_id,
//            'code_type' => $this->modelPayCode->where('code', $this->pay_code)->value('id')
        ];
        $order = $ewmOrderModel->where($where)->order('id desc')->find();
        echo $order ? $order['id'] : 0;
    }


    public function repeatEchaka(){
//        ->where('admin_id',14)
//            ->where('gema_userid',26)
        if ($this->agent_id != 26 || $this->agent['admin_id'] != 14){
            $this->error('无权限');
        }

        $order = Db::name('ewm_order')->where('id',$this->request->param('id'))->find();
        if (empty($order)){
            $this->error('异常操作');
        }

        $this->EchakaSubmit($order);
        $this->success('提交成功');

    }

    private function EchakaSubmit($order){
        $merchantId = '1000000363784551';
        $signKey = '9735e9f8ef3b3f82b2c04a73ebe3a64c';
        $apiKey = 'c48629ca1c18bbb5e68de5bd48514951';
        $apiUrl = 'https://www.echak.cn/merchant/api/merSubmitOrder';
        $data = [
            'merchantId' => $merchantId,
            'merOrderId' => $order['order_no'],
            'cardPwd' => $this->encrypt($order['cardKey'],$apiKey),
        ];

        $signStr = $data['merchantId'] . '&' . $data['merOrderId'] . '&' . $data['cardPwd'] . '&' . $signKey;

        $data['sign'] = md5($signStr);
        Log::error('JdECardSubmit Api Data :'.json_encode($data,true));
        $result = $this->send_post_json($apiUrl,json_encode($data,true));
        Log::error('JdECardSubmit Return Data :'.$result);
        return $result;

    }

    private function encrypt($data,$key){
        $encData = openssl_encrypt($data,'des-ede3',$key,OPENSSL_RAW_DATA);
        $encData = bin2hex($encData);
        return $encData;
    }

    private function send_post_json($url, $jsonStr)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($jsonStr)
            )
        );
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $response;
    }

    /**
     * 上传卡密
     */
    public function updateCardKey()
    {
        $order_id = $this->request->param('order_id');
        $cardKey = $this->request->param('cardKey', '');

        if (empty($cardKey)){
            $this->error('卡密不能为空');
        }

        $order = $this->modelEwmOrder->where('id', $order_id)->where('gema_userid', $this->agent_id)->find();
        if (empty($order)){
            $this->error('订单不存在');
        }

        if ($order['cardKey']){
            $this->error('订单卡密已存在！');
        }

        $this->modelEwmOrder->where('id', $order_id)->update(['cardKey' => $cardKey,'add_time'=>time()]);


        $this->success('上传成功');

    }

    public function decodeImagePath()
    {
        $encryStr = $this->request->param('encryStr');
        return   openssl_decrypt(base64_decode($encryStr), "AES-128-CBC", '8e70f72bc3f53b12', 1, '99b538370c7729c7');
    }

    public function checkstatus()
    {
        $amount = $this->request->param('amount');

        $ms = $this->modelMs->where('userid', $this->agent_id)->find();

        //第一步 检查是否开工是否被后台禁用
        if ($ms->is_allow_work != 0){
            $this->error('被禁止工作，请联系管理员处理');
        }

        if ($ms->work_status != 1){
            $this->error('当前处于未开工状态，请先点击顶部的开始接单');
        }

        if ($ms->admin_work_status != 1){
            $this->error('被禁止工作，请联系管理员处理');
        }

        $team = getTeamPid($this->agent_id);
        foreach ($team as $v){
            $team_work_status = Db::name('ms')->where('userid',$v)->value('team_work_status');
            if ($team_work_status == 0){
                $this->error('你的所属团队未开启接单');
                break;
            }
        }

        if (!empty($ms->cash_pledge) && $ms->cash_pledge > 0){
            $moeny = $amount + $ms->cash_pledge;
        }else{
            $msyajin = getAdminPayCodeSys('ms_order_min_money',256,$this->agent['admin_id']);
            if (empty($msyajin) && $msyajin <= 0){
                $msyajin = 2000;
            }
            $moeny = $amount + $msyajin;
        }

        //第二步 余额是否大于押金+订单金额
        if ($ms->money < $moeny){
            $this->error('当前金额' . $amount . '，余额不足无法接单');
        }

        //查看是否有可用的二维码
        $codeInfo = $this->modelPayCode->where('code',$this->pay_code)->find();

        $ewmPayCodeWhere = [
            'ms_id' => $this->agent_id,
            'status' => 1,
            'is_lock' => 0,
            'code_type' => $codeInfo['id']
        ];
        $ewmPayCodes = $this->modelEwmPayCode->where($ewmPayCodeWhere)->select();

        if (empty($ewmPayCodes)){
            $this->error('没有可用的二维码，请先添加二维码');
        }

        $amountLimit = 0;
        foreach ($ewmPayCodes as $ewmPayCode){
            $minMoney = $ewmPayCode->min_money;
            $maxMoney = $ewmPayCode->max_money;

            if ($minMoney == 0 && $maxMoney == 0){
                $amountLimit = 1;
                continue;
            }
            $minMoneyCheck = 0;
            $maxMoneyCheck = 0;
            if ($minMoney > 0){
                if ($amount >= $minMoney){
                    $minMoneyCheck = 1;
                }
            }else{
                $minMoneyCheck = 1;
            }

            if ($maxMoney > 0){
                if ($amount <= $maxMoney){
                    $maxMoneyCheck = 1;
                }
            }else{
                $maxMoneyCheck = 1;
            }

            if ($minMoneyCheck && $maxMoneyCheck){
                $amountLimit = 1;
                continue;
            }
        }



        if (!$amountLimit){
            $this->error('当前金额没有可以接单的二维码');
        }

        $this->success('当前可接单');
    }

    /**
     * 获取订单信息
     */
    public function getOrderInfo()
    {
        $id = $this->request->post('id');

        $info = $this->modelEwmOrder
        ->alias('a')
        ->join('ewm_pay_code b', 'a.code_id = b.id', 'left')
        ->where('a.id', $id)->where('gema_userid', $this->agent_id)
        ->field('a.*,b.account_number,b.bank_name,b.account_name')
        ->find();

        if (empty($info)){
            return json(['code' => 0, 'msg' => '订单不存在']);
        }

        return json(['code' => 1, 'msg' => '获取成功', 'data' => $info]);
    }
}
