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

use app\api\service\payment\DnPay;
use app\common\library\enum\CodeEnum;
use think\Db;
use think\helper\Time;
use think\Log;

class Index extends BaseAdmin
{


    public function testpay(){
        if ($this->request->isPost()){
            $uid = $this->request->param('uid');
            $pay_code = $this->request->param('type');
            $amount = $this->request->param('amount');
            if (empty($uid) || empty($pay_code) || empty($amount)){
                return json(['code'=>404,'msg'=>'参数错误']);
            }
            $u = Db::name('user')->where('uid',$uid)->find();
            if (is_admin_login() != 1){
                if ($u['admin_id'] != is_admin_login()){
                    return json(['code'=>404,'msg'=>'非法请求']);
                }
            }
            $md5key = Db::name('api')->where('uid',$uid)->value('key');
            $host = $_SERVER["HTTP_HOST"];
            $requestUrl = 'http://'.$host.'/api/pay/unifiedorder';
            $data = array(
                'mchid' => $uid,
                'out_trade_no' => date('ymdHis').rand(1000,9999),
                'amount' => $amount,
                'channel' =>$pay_code,
                //'notify_url' => 'http://47.242.51.211/notify.php',
                'notify_url' => $host . '/index/tg/payNotify',
                'return_url' => $host.'/test/return.php',
                'time_stamp' => date("Ymdhis"),
                'body' => "addH",
            );
            ksort($data);
            $signData = "";
            foreach ($data as $key=>$value)
            {
                $signData = $signData.$key."=".$value;
                $signData = $signData . "&";
            }

            $signData = $signData."key=".$md5key;
            $sign = md5($signData);

            $data['sign'] = $sign;
//            Log::error(date('Y-m-d H:i:s',time()).':请求开始：'.$data['out_trade_no']);
            //初始化
            $curl = curl_init();
//设置抓取的url
            curl_setopt($curl, CURLOPT_URL, $requestUrl);
//设置头文件的信息作为数据流输出
            curl_setopt($curl, CURLOPT_HEADER, 0);
//设置获取的信息以文件流的形式返回，而不是直接输出。
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//设置post方式提交
            curl_setopt($curl, CURLOPT_POST, 1);

            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            $json = curl_exec($curl);

            //关闭URL请求
            curl_close($curl);
//显示获得的数据
            $data = json_decode($json, true);
//            Log::error(date('Y-m-d H:i:s',time()).':请求结束：'.$data['out_trade_no']);
//        print_r($data);die;
            if($data['code'] == 0)
            {
//                header("location: ".$data['data']['request_url']);
                return  json(['code'=>0,'pay_url'=>$data['data']['request_url']]);
            }
            else
            {
                return  json(['code'=>404,'msg'=>$data['msg']]);
            }
        }
        $where['status'] = 1;
        if (is_admin_login() != 1){
            $where['admin_id'] = is_admin_login();
        }
        $user = Db::name('user')->where($where)->select();
        $pay_code = Db::name('pay_code')->where('status',1)->select();
        $this->assign('paycode',$pay_code);
        $this->assign('user',$user);
        return $this->fetch();
    }

    /**
     * 访问首页  -  加载框架
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function index()
    {
        //读取配置 判断是否开启渠道统计悬浮窗
        $is_channel_statistics = 0;
        $config = \app\common\model\Config::where(['name'=>'is_channel_statistics'])->find()->toArray();
        if($config){
            if($config['value'] == '1'){
                $is_channel_statistics = '1';
            }
        }
        $admin_money = Db::name('admin')->where('id',is_admin_login())->value('balance');
        $this->assign('is_channel_statistics',$is_channel_statistics);
        $this->assign('admin_money',$admin_money);
        return $this->fetch();
    }

    /**
     * 欢迎主页  -  展示数据
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
//    public function welcome()
//    {
//
//        $where['status'] = 2;
//        if (is_admin_login() != 1){
//            $adminsonuser = Db::name('user')->where('admin_id',is_admin_login())->column('uid');
//            $where['uid'] = ['in',$adminsonuser];
//        }
//        // 今日平台收入--商户收入
//        $platform_in_user = Db::name('orders')
//                            ->where($where)
//                            ->whereTime('create_time', 'today')->sum('platform_in');
//
//        //查找今日成功订单
//        $where1 = [];
//        $where5 = [];
//        $where3['jl_class'] = 9;
//        $where1['jl_class'] = 5;
//        $where5['jl_class'] = 18;
//        if (is_admin_login() != 1){
//
//            $adminsonms = Db::name('ms')->where('admin_id',is_admin_login())->column('userid');
////            $where1['gema_userid'] = ['in',$adminsonms];
//            $where3['uid'] = ['in',$adminsonms];
//            $where1['uid'] = ['in',$adminsonms];
//            $where5['uid'] = ['in',$adminsonms];
//        }
//        //码商分润
//        $todaydayOrders = Db::name('ms_somebill')->where($where1)->whereTime('addtime', 'today')->sum('num');
//        //码商代理分润
//        $todayMsAgentIn = Db::name('ms_somebill')->where($where3)->whereTime('addtime', 'today')->sum('num');
//        $todayMsyejianIn = Db::name('ms_somebill')->where($where5)->whereTime('addtime', 'today')->sum('num');
//
////        $mscount = $todaydayOrders + $todayMsAgentIn;
//        //今日平台收入
//        $today_platform_in = $platform_in_user - $todaydayOrders - $todayMsAgentIn - $todayMsyejianIn;
//        $today_platform_in = sprintf("%.2f",$today_platform_in);
//
//        $where2['status'] = 1;
//        if (is_admin_login() != 1){
//            $where2['admin_id'] = is_admin_login();
//        }
//        $todaySuccessAmount = Db::name('ewm_order')
//                                ->where($where2)
//                                ->whereTime('add_time', 'today')->sum('order_price');
//
//
//        $this->assign('todaySuccessAmount',sprintf("%.2f",$todaySuccessAmount));
//        $this->assign('today_platform_in',$today_platform_in);
////        $this->assign('yesterday_platform_in',$yestday_platform_in);
//
//        return $this->fetch('',$this->logicOrders->getWelcomeStat());
//    }
    public function welcome1()
    {
        $adminId = is_admin_login();

        // 如果是管理员，获取他所有的用户id和msid
        $adminsonuser = [];
        $adminsonms = [];
        if ($adminId != 1) {
            $result = Db::name('user')->alias('u')
                ->join('ms m', 'u.admin_id = m.admin_id')
                ->where('u.admin_id', $adminId)
                ->field('u.uid, m.userid')
                ->select();

            $adminsonuser = array_column($result, 'uid');
            $adminsonms = array_column($result, 'userid');
        }

        // 今日平台收入--商户收入
        $where = ['status' => 2];
        if (!empty($adminsonuser)) {
            $where['uid'] = ['in', $adminsonuser];
        }
        $platform_in_user = Db::name('orders')
            ->where($where)
            ->whereTime('update_time', 'today')->sum('platform_in');

        // 合并数据库查询
        $whereSomebill = ['jl_class' => ['in', [5, 9, 18]]];
        if (is_admin_login() != 1) {
            $whereSomebill['uid'] = ['in', $adminsonms];
        }
        $somebills = Db::name('ms_somebill')
            ->where($whereSomebill)
            ->whereTime('addtime', 'today')
            ->field('jl_class, sum(num) as num')
            ->group('jl_class')
            ->select();
//        print_r($platform_in_user);die;

        $todaydayOrders = $todayMsAgentIn = $todayMsyejianIn = 0;
        foreach($somebills as $bill) {
            switch ($bill['jl_class']) {
                case 5:
                    $todaydayOrders = $bill['num'];
                    break;
                case 9:
                    $todayMsAgentIn = $bill['num'];
                    break;
                case 18:
                    $todayMsyejianIn = $bill['num'];
                    break;
            }
        }

        //今日平台收入
        $today_platform_in = $platform_in_user - $todaydayOrders - $todayMsAgentIn - $todayMsyejianIn;
        $today_platform_in = sprintf("%.2f", $today_platform_in);

        $whereOrder = ['status' => 1];
        if ($adminId != 1) {
            $whereOrder['admin_id'] = $adminId;
        }
        $todaySuccessAmount = Db::name('ewm_order')
            ->where($whereOrder)
            ->whereTime('add_time', 'today')->sum('order_price');

        $this->assign('todaySuccessAmount', sprintf("%.2f", $todaySuccessAmount));
        $this->assign('today_platform_in', $today_platform_in);

        return $this->fetch('welcome_new', $this->logicOrders->getWelcomeStat());
    }


    public function welcome(){
        if ($this->request->isPost()){
            $time = $this->request->param('time');
            if (!in_array($time,['today','yesterday','week'])){
                $this->result(['code'=>404,'msg'=>'非法请求']);
            }
            $admin_id = is_admin_login();
            $is_super_admin = $admin_id == 1;

            // 当天统计数据
            $todayData = $this->modelEwmOrder
                ->alias('eo')
                ->where('eo.status', 1)
                ->where(function ($query) use ($is_super_admin, $admin_id) {
                    if (!$is_super_admin) {
                        $query->where('eo.admin_id', $admin_id);
                    }
                })
                ->whereTime('eo.pay_time', $time)
                ->field([
                    'sum(eo.order_price) as total_money',
//                    'count(eo.id) as total_count'
                ])
                ->find();
            // 检查 yesterday_money 是否为空，如果为空则设置为 0.00
            $todayData['total_money'] = !empty($todayData['total_money']) ? $todayData['total_money'] : 0.00;
            $where = [];
            $where['o.status'] = 2;
            if (is_admin_login() != 1){
//                $adminsonuser = Db::name('user')->where('admin_id',is_admin_login())->column('uid');
//                $where['o.uid'] = ['in',$adminsonuser];
                $where['cm_ewm_order.admin_id'] = is_admin_login();
            }
            // 今日平台收入--商户收入
            $platform_in_user = Db::name('orders')
                ->alias('o')
                ->join('cm_ewm_order', 'o.trade_no = cm_ewm_order.order_no')
                ->where($where)
                ->whereTime('cm_ewm_order.pay_time', $time)
                ->sum('o.platform_in');

            //查找今日成功订单
            $where1 = [];
            $where5 = [];
            $where3['jl_class'] = 9;
            $where1['jl_class'] = 5;
            $where5['jl_class'] = 18;
            if (is_admin_login() != 1){

                $adminsonms = Db::name('ms')->where('admin_id',is_admin_login())->column('userid');
//            $where1['gema_userid'] = ['in',$adminsonms];
                $where3['uid'] = ['in',$adminsonms];
                $where1['uid'] = ['in',$adminsonms];
                $where5['uid'] = ['in',$adminsonms];
            }
            //码商分润
            $todaydayOrders = Db::name('ms_somebill')->where($where1)->whereTime('addtime', $time)->sum('num');
            //码商代理分润
            $todayMsAgentIn = Db::name('ms_somebill')->where($where3)->whereTime('addtime', $time)->sum('num');
            $todayMsyejianIn = Db::name('ms_somebill')->where($where5)->whereTime('addtime', $time)->sum('num');

//        $mscount = $todaydayOrders + $todayMsAgentIn;
            //今日平台收入
            $platform_in = $platform_in_user - $todaydayOrders - $todayMsAgentIn - $todayMsyejianIn;
            $platform_in = sprintf("%.2f",$platform_in);



            $dfwhere = [];
            $dfwhere['status'] = 2;

            $mswhere['jl_class'] = 13;
            if (is_admin_login() != 1){
//                $admin_user = Db::name('user')->where('admin_id',is_admin_login())->column('uid');
//                $dfwhere['uid'] = ['in',$admin_user];
                $dfwhere['admin_id'] = is_admin_login();
//                $admin_ms = Db::name('ms')->where('admin_id',is_admin_login())->column('userid');
//                $mswhere['uid'] = ['in',$admin_ms];
            }
            $df_in_user = Db::name('daifu_orders')
                ->where($dfwhere)
                ->whereTime('finish_time', $time)
                ->field([
                    'sum(service_charge) as service_charge',
                    'sum(single_service_charge) as single_service_charge',
                    'sum(amount) as df_amount',
//                    'count(id) as df_count',
                ])
                ->find();

            $df_in_user['df_amount'] = !empty($df_in_user['df_amount']) ? $df_in_user['df_amount'] : 0.00;
//            $this->assign('df',$df_in_user);
            //码商分润
//            $df_in_ms = Db::name('ms_somebill')->where($mswhere)->whereTime('addtime', $time)->sum('num');
//            $df_in = $df_in_user['service_charge'] + $df_in_user['single_service_charge'] - $df_in_ms;
//            $df_in  = sprintf("%.2f",$df_in);

            return json(['total_money' => sprintf("%.2f",$todayData['total_money']),'platform_in_user'=>sprintf("%.2f",$platform_in_user),
                'todaydayOrders'=>sprintf("%.2f",$todaydayOrders),'todayMsAgentIn'=>sprintf("%.2f",$todayMsAgentIn),'platform_in'=>sprintf("%.2f",$platform_in) ,
                'df_amount'=>sprintf("%.2f",$df_in_user['df_amount']),'df_in'=>sprintf("%.2f",$df_in_user['service_charge']+$df_in_user['single_service_charge'])]);
        }

        $where = [];
//        $where['status'] = 1;
        $admin_id = is_admin_login();
        $is_super_admin = $admin_id == 1;

        // 当天统计数据
        $todayData = $this->modelEwmOrder
            ->alias('eo')
            ->where('eo.status', 1)
            ->where(function ($query) use ($is_super_admin, $admin_id) {
                if (!$is_super_admin) {
                    $query->where('eo.admin_id', $admin_id);
                }
            })
            ->whereTime('eo.pay_time', 'today')
            ->field([
                'sum(eo.order_price) as total_money',
//                'count(eo.id) as total_count'
            ])
            ->find();
        // 检查 yesterday_money 是否为空，如果为空则设置为 0.00
        $todayData['total_money'] = !empty($todayData['total_money']) ? $todayData['total_money'] : 0.00;
//        print_r($yesterday);die;
        $this->assign('today',$todayData);





        $where = [];
        $where['o.status'] = 2;
        if (is_admin_login() != 1){
//            $adminsonuser = Db::name('user')->where('admin_id',is_admin_login())->column('uid');
//            $where['o.uid'] = ['in',$adminsonuser];
            $where['cm_ewm_order.admin_id'] = is_admin_login();
        }
        // 今日平台收入--商户收入
        $platform_in_user = Db::name('orders')
                            ->alias('o')
                            ->join('cm_ewm_order', 'o.trade_no = cm_ewm_order.order_no')
                            ->where($where)
                            ->whereTime('cm_ewm_order.pay_time', 'today')
                            ->sum('o.platform_in');

        $this->assign('platform_in_user',$platform_in_user);
        //查找今日成功订单
        $where1 = [];
        $where5 = [];
        $dfwhere = [];
        $where3['jl_class'] = 9;
        $where1['jl_class'] = 5;
        $where5['jl_class'] = 18;
        if (is_admin_login() != 1){

            $adminsonms = Db::name('ms')->where('admin_id',is_admin_login())->column('userid');
//            $where1['gema_userid'] = ['in',$adminsonms];
            $where3['uid'] = ['in',$adminsonms];
            $where1['uid'] = ['in',$adminsonms];
            $where5['uid'] = ['in',$adminsonms];
            $dfwhere['admin_id'] = is_admin_login();
        }
        //码商分润
        $todaydayOrders = Db::name('ms_somebill')->where($where1)->whereTime('addtime', 'today')->sum('num');
        $todaydayOrders = !empty($todaydayOrders) ? $todaydayOrders : 0.00;
        $this->assign('todaydayOrders',sprintf("%.2f",$todaydayOrders));
        //码商代理分润
        $todayMsAgentIn = Db::name('ms_somebill')->where($where3)->whereTime('addtime', 'today')->sum('num');

        $todayMsAgentIn = !empty($todayMsAgentIn) ? $todayMsAgentIn : 0.00;
        $this->assign('todayMsAgentIn',sprintf("%.2f",$todayMsAgentIn));
        $todayMsyejianIn = Db::name('ms_somebill')->where($where5)->whereTime('addtime', 'today')->sum('num');

//        $mscount = $todaydayOrders + $todayMsAgentIn;
        //今日平台收入
        $today_platform_in = $platform_in_user - $todaydayOrders - $todayMsAgentIn - $todayMsyejianIn;
        $today_platform_in = sprintf("%.2f",$today_platform_in);
        $this->assign('platform_in',$today_platform_in);


        //代付
        //
        $dfwhere['status'] = 2;
        $mswhere['jl_class'] = 13;
//        if (is_admin_login() != 1){
//            $admin_user = Db::name('user')->where('admin_id',is_admin_login())->column('uid');
//            $dfwhere['uid'] = ['in',$admin_user];
//            $admin_ms = Db::name('ms')->where('admin_id',is_admin_login())->column('userid');
//            $mswhere['uid'] = ['in',$admin_ms];
//        }
        $df_in_user = Db::name('daifu_orders')
            ->where($dfwhere)
            ->whereTime('finish_time', 'today')
            ->field([
                'sum(service_charge) as service_charge',
                'sum(single_service_charge) as single_service_charge',
                'sum(amount) as df_amount',
                'count(id) as df_count',
            ])
            ->find();

        $df_in_user['df_amount'] = !empty($df_in_user['df_amount']) ? $df_in_user['df_amount'] : 0.00;
        $df_in_user['df_in'] = sprintf("%.2f",$df_in_user['service_charge'] + $df_in_user['single_service_charge']);
        $this->assign('df',$df_in_user);
        //码商分润
//        $df_in_ms = Db::name('ms_somebill')->where($mswhere)->whereTime('addtime', 'today')->sum('num');
//        $df_in = $df_in_user['service_charge'] + $df_in_user['single_service_charge'] - $df_in_ms;
//        $df_in  = sprintf("%.2f",$df_in);

//        $this->assign('df_in',$df_in);





        return $this->fetch('welcome_new');



    }


    /**
     * 订单月统计
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function getOrderStat(){

        $where = [];
        if (is_admin_login() != 1){
            $adminSonUid = Db::name('user')->where('uid',is_admin_login())->column('uid');
            $where['uid'] = ['in',$adminSonUid];
        }

        $res = $this->logicOrders->getOrdersMonthStat($where);

        $data = [
            'orders' => get_order_month_stat($res,'total_orders'),
            'fees' => get_order_month_stat($res,'total_amount'),
        ];
        $this->result(CodeEnum::SUCCESS,'',$data);
    }

    /**
     * 本月最近订单
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function getOrdersList(){
        $where = [];
        //当月时间
        list($start, $end) = Time::month();

        $where['create_time'] = ['between time', [$start,$end]];

        if (is_admin_login() != 1){
            $adminSonUid = Db::name('user')->where('admin_id',is_admin_login())->column('uid');
            $where['uid'] = ['in',$adminSonUid];
        }
        $data = $this->logicOrders->getOrderList($where,true, 'create_time desc',false);

        $this->result($data || !empty($data) ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg'=> '',
                'data'=>$data
            ] : [
                'code' => CodeEnum::ERROR,
                'msg'=> '暂无数据',
                'data'=>$data
            ]
        );
    }
}
