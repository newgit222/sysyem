<?php
/**
 * @Created   by PhpStorm.
 * @author    StarsPhp
 * @date      2022/12/12
 * @time      0:43
 */
declare (strict_types=1);

namespace app\member\controller;

use app\api\logic\AlipayF2FPayService;
use app\common\library\enum\CodeEnum;
use app\common\library\exception\OrderException;
use app\common\logic\EwmOrder;
use app\common\logic\Queuev1Logic;
use app\common\model\Config;
use app\common\model\EwmPayCode;
use think\Db;
use think\Log;
use think\Request;
//use think\db\Raw;

class Channel extends Base
{
    protected $pay_code = null;
    public function __construct(Request $request = null)
    {
        $this->pay_code = $request->param('pay_code');
        $codes = $this->modelPayCode->where('status', 1)->column('code');
        if (!in_array( $this->pay_code,  $codes)){
            $this->error('错误的通道编码!');
        }
        parent::__construct($request);
    }


    public function index()
    {
        return $this->fetch();
    }

    public function channel()
    {
        return $this->{$this->pay_code}();
    }

    public function aliRedEnvelope()
    {
        return $this->fetch('aliRedEnvelope');
    }

    /**
     * 卡转卡
     * @return mixed
     */
    public function alipayF2F()
    {
        return $this->fetch('alipayF2F');
    }

    /**
     * 卡转卡
     * @return mixed
     */
    public function alipayWap()
    {
        return $this->fetch('alipayWap');
    }


    /**
     * 卡转卡
     * @return mixed
     */
    public function alipayTransfer()
    {
        return $this->fetch('alipayTransfer');
    }



    /**
     * 卡转卡
     * @return mixed
     */
    public function JunWeb()
    {
        return $this->fetch('JunWeb');
    }

    /**
     * 卡转卡
     * @return mixed
     */
    public function CnyNumberAuto()
    {
        $reg_date = Db::name('ms')->where('userid',$this->agent_id)->value('reg_date');
        $jimidata = $this->agent_id.'+abc'.$reg_date;
        $apikey = encrypts($jimidata,'E');
        $replace = 'asdefg';
        $search = '+';
        $apikey = str_ireplace($search, $replace, $apikey);
        $apiurl = $this->request->domain() .'/api/cnynumber/cnynumber?key='.$apikey;
        $this->assign('apiurl',$apiurl);
        return $this->fetch('CnyNumberAuto');
    }


    /**
     * 卡转卡
     * @return mixed
     */
    public function kzk()
    {
        $reg_date = Db::name('ms')->where('userid',$this->agent_id)->value('reg_date');
        $jimidata = $this->agent_id.'+abc'.$reg_date;
        $apikey = encrypts($jimidata,'E');
        $replace = 'asdefg';
        $search = '+';
        $apikey = str_ireplace($search, $replace, $apikey);
        $apiurl = $this->request->domain() .'/api/bank/banksms?key='.$apikey;
        $this->assign('apiurl',$apiurl);
        return $this->fetch('kzk');
    }

    /**
     * 支付宝扫码
     * @return mixed
     */
    public function alipayCode()
    {
        return $this->fetch('alipayCode');
    }


    /**
     * 支付宝扫码
     * @return mixed
     */
    public function alipayCodeSmall()
    {
        return $this->fetch('alipayCodeSmall');
    }


    /**
     * 支付宝口令红包
     * @return mixed
     */
    public function alipayPassRed()
    {
        return $this->fetch('alipayPassRed');
    }

    /**
     * 支付宝UID
     * @return mixed
     */
    public function alipayUid()
    {
        return $this->fetch('alipayUid');
    }

    /**
     * 支付宝小额UID
     * @return mixed
     */
    public function alipayUidSmall()
    {
        return $this->fetch('alipayUidSmall');
    }

    /**
     * 支付宝UID转账
     * @return mixed
     */
    public function alipayUidTransfer()
    {
        return $this->fetch('alipayUidTransfer');
    }

    /**
     * @user luomu
     * @return
     * @time
     * 抖音群红包
     */
    public function douyinGroupRed(){
        return $this->fetch('douyinGroupRed');
    }

    /**
     * @user luomu
     * @return
     * @time
     * 抖音群红包
     */
    public function wechatCode(){
        return $this->fetch('wechatCode');
    }


    /**
     * @user luomu
     * @return
     * @time
     * 微信黄金红包
     */
    public function WechatGoldRed(){
        return $this->fetch('WechatGoldRed');
    }


    /**
     * @user luomu
     * @return
     * @time
     * 微信群红包
     */

    public function wechatGroupRed()
    {
        return $this->fetch('wechatGroupRed');
    }


    /**
     * @user luomu
     * @return
     * @time
     * 京东E卡
     */

    public function JDECard()
    {
        return $this->fetch('JDECard');
    }



    /**
     * @user luomu
     * @return
     * @time
     * 数字人民币
     */

    public function CnyNumber()
    {
        return $this->fetch('CnyNumber');
    }



    /**
     * @user luomu
     * @return
     * @time
     * 小程序商品
     */

    public function AppletProducts()
    {
        return $this->fetch('AppletProducts');
    }



    /**
     * @user luomu
     * @return
     * @time
     * QQ面对面
     */

    public function QQFaceRed()
    {
        return $this->fetch('QQFaceRed');
    }



    /**
     * @user luomu
     * @return
     * @time
     *支付宝小荷包
     */

    public function alipaySmallPurse()
    {
        return $this->fetch('alipaySmallPurse');
    }

    /**
     * @user luomu
     * @return
     * @time
     *淘宝现金红包
     */

    public function taoBaoMoneyRed()
    {
        return $this->fetch('taoBaoMoneyRed');
    }

    /**
     * @user luomu
     * @return
     * @time
     *支付宝转账码
     */

    public function alipayTransferCode()
    {
        return $this->fetch('alipayTransferCode');
    }


    /**
     * @user luomu
     * @return
     * @time
     *支付宝工作证
     */

    public function alipayWorkCard()
    {
        return $this->fetch('alipayWorkCard');
    }



    /**
     * @user luomu
     * @return
     * @time
     *支付宝零花钱
     */

    public function alipayPinMoney()
    {
        return $this->fetch('alipayPinMoney');
    }



    /**
     * @user luomu
     * @return
     * @time
     *支付宝工作证大额
     */

    public function alipayWorkCardBig()
    {
        return $this->fetch('alipayWorkCardBig');
    }


    /**
     * @user luomu
     * @return
     * @time
     *淘宝E卡
     */

    public function taoBaoEcard()
    {
        return $this->fetch('taoBaoEcard');
    }


    /**
     * @user luomu
     * @return
     * @time
     *仟信好友转账
     */

    public function QianxinTransfer()
    {
        return $this->fetch('QianxinTransfer');
    }

    /**
     * @user luomu
     * @return
     * @time
     *仟信好友转账
     */

    public function usdtTrc()
    {
        return $this->fetch('usdtTrc');
    }


    /**
     * @user luomu
     * @return
     * @time
     *聚合码
     */

    public function AggregateCode()
    {
        return $this->fetch('AggregateCode');
    }


    /**
     * @user luomu
     * @return
     * @time
     *淘宝直付
     */

    public function taoBaoDirectPay()
    {
        return $this->fetch('taoBaoDirectPay');
    }


    /**
     * @user luomu
     * @return
     * @time
     *汇元易付卡
     */

    public function HuiYuanYiFuKa()
    {
        return $this->fetch('HuiYuanYiFuKa');
    }


    /**
     * @user luomu
     * @return
     * @time
     *乐付天宏卡
     */

    public function LeFuTianHongKa()
    {
        return $this->fetch('LeFuTianHongKa');
    }



    /**
     * @user luomu
     * @return
     * @time
     *乐付天宏卡
     */

    public function DingDingGroup()
    {
        return $this->fetch('DingDingGroup');
    }

    /**
     * QQ扫码
     * @return mixed
     */
    public function qqCode()
    {
        return $this->fetch('qqCode');
    }


    public function sys_pay_url(Request $request){
        if ($request->isPost()){

        }
        $code_id = $this->request->param('id');
        $code = Db::name('ewm_pay_code')->where('id',$code_id)->find();
        if (empty($code)) $this->error('error');
        if ($code['ms_id'] != $this->agent_id){
            $this->error('非法操作');
        }
        $info = json_encode($code['extra'],true);
        $this->assign('info',$info);
        return $this->fetch('sysPayUrl');
    }


    public function online_code(){
        if ($this->request->isPost()){
            $code_id = $this->request->post('code_id');

            $code = Db::name('ewm_pay_code')->where('id',$code_id)->find();
            if ($code){
                if ($code['ms_id'] != $this->agent_id){
                    $this->error('非法请求');
                }
            }else{
                $this->error('异常操作');
            }
            $Authorization = $this->request->post('account_number');
            if (empty($Authorization)){
                $this->error('请输入正确的Authorization');
            }

            $res = json_decode($this->getBills($Authorization),true);

            if (!empty($res['status']) && $res['status'] == 200){
                $online = Db::name('ewm_pay_code')->where('id',$code['id'])->update(['status'=>1,'account_number'=>$Authorization]);
                if ($online !== false){
                    $this->success('上线成功');
                }else{
                    $this->error('上线失败');
                }

            }else{
                $this->error('Authorization已失效');
            }

        }
    }


    public function upload_cookie()
    {
        if ($this->request->isPost()){
            $code_id = $this->request->post('code_id');

            $code = Db::name('ewm_pay_code')->where('id',$code_id)->find();
            if ($code){
                if ($code['ms_id'] != $this->agent_id){
                    $this->error('非法请求');
                }
            }else{
                $this->error('异常操作');
            }
            $cookie = $this->request->post('cookie');
            if (empty($cookie)){
                $this->error('请输入正确的Cookie');
            }


            $ret = Db::name('ewm_pay_code')->where('id',$code['id'])->update(['status'=>1,'extra'=>$cookie]);
            if ($ret !== false){
                $this->success('上传成功');
            }else{
                $this->error('上传失败');
            }

        }
    }

    private function getBills($Authorization){
        $url = 'https://b2b.eycard.cn/api/system/trade/getTotal';
        $start = date('Y-m-d H:i:s',time()-360);
        $end = date('Y-m-d H:i:s',time());
        $param = [
            'type' => '1,2',
            'tradeTime' => $start.','.$end,
            'sort' => 'id,desc',
            'page' => 0,
            'size' => 20
        ];
        // print_r(http_build_query($param));die;
        $header = [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: '.$Authorization,
        ];
        $result= $this->http($url,$param,'GET',$header);
        return $result;
        // print_r($res);die;

    }



    /**
     * 发送HTTP请求方法
     * @param  string $url    请求URL
     * @param  array  $params 请求参数
     * @param  string $method 请求方法GET/POST
     * @param  array $header 请求头
     * @param  bool $multi 是否传输文件
     * @return array  $data   响应数据
     */
    private function http($url, $params, $method = 'GET', $header = array(), $multi = false){
        $opts = array(
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER     => $header
        );
        /* 根据请求类型设置特定参数 */
        switch(strtoupper($method)){
            case 'GET':
                $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
                break;
            case 'POST':
                //判断是否传输文件
                $params = $multi ? $params : http_build_query($params);
                $opts[CURLOPT_URL] = $url;
                $opts[CURLOPT_POST] = 1;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            default:
                throw new Exception('不支持的请求方式！');
        }
        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data  = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if($error) throw new Exception('请求发生错误：' . $error);
        return  $data;
    }


    public function add(Request $request=null)
    {
        if ($this->request->isPost()){
            $params = $request->param();
            $validate = $this->validateEwmPayCode->scene($this->pay_code . '_add')->check($params);
            if (false === $validate){
                return $this->error( $this->validateEwmPayCode->getError());
            }
            $code = Db::name('pay_code')->where('code',$this->pay_code)->value('id');
            if (empty($code)){
                $this->error('不支持的通道');
            }
            $result = $this->logicCodeLogic->addQRcode($params,$code);
            if ($result['code'] == CodeEnum::ERROR) {
                $this->error("上传失败," . $result['msg']);
            }
            $this->success($result['msg']);
        }
//        if ($this->pay_code == 'douyinGroupRed'){
//            $code = Db::name('pay_code')->where('code',$this->pay_code)->value('id');
//            $pay_template = getAdminPayCodeSys('pay_template',$code,$this->agent['admin_id']);
//            if (empty($pay_template)){
//                $pay_template = 1;
//            }
//            $this->assign('pay_template', $pay_template);
//        }

        $code = Db::name('pay_code')->where('code',$this->pay_code)->value('id');
//        $api_max_money = getAdminPayCodeSys('api_max_money',$code,$this->agent['admin_id']);
//        if (!empty($api_max_money) && $api_max_money > 0){
//            $ms_sys_code_money = 2;
//        }else{
//            $ms_sys_code_money = 1;
//        }
//
//        $api_min_money = getAdminPayCodeSys('api_min_money',$code,$this->agent['admin_id']);
//        if (!empty($api_min_money) && $api_min_money > 0){
//            $ms_sys_code_min_money = 2;
//        }else{
//            $ms_sys_code_min_money = 1;
//        }

        $ms_code_money_status = getAdminPayCodeSys('ms_code_money_status',$code,$this->agent['admin_id']);
        if (empty($ms_code_money_status)){
            $ms_code_money_status = 1;
        }


        $smsTemplate = require_once('./data/conf/smsTemplate.php');
//        $this->assign('ms_sys_code_max_money', $ms_sys_code_money);
//        $this->assign('ms_sys_code_min_money', $ms_sys_code_min_money);
        $this->assign('ms_code_money_status', $ms_code_money_status);
        $this->assign('banksList', $smsTemplate);
        $this->assign('code',  $this->pay_code);
        return $this->fetch('add_'. $this->pay_code);
    }

    public function starts(){
        $code = Db::name('pay_code')->where('code',$this->pay_code)->value('id');
        $res = Db::name('ewm_pay_code')->where(['ms_id'=>$this->agent_id,'code_type'=>$code,'is_delete'=>0])->update(['status'=>1]);
        if ($res === false){
            return ['code' => CodeEnum::ERROR, 'msg' => '更新,请一会儿再试'];
        }else{
            return ['code' => CodeEnum::SUCCESS, 'msg' => '开启成功'];
        }
    }

    /**
     *
     * 通道列表
     * @throws \think\exception\DbException
     */
    public function lists(Request $request)
    {
        if ($this->request->isAjax()){
            $id = $request->param('id', '', 'intval');
            $bank_name = $request->param('bank_name', '', 'trim');
            $account_name = $request->param('account_name', '', 'trim');
            $account_number = $request->param('account_number', '', 'trim');
            $status = $request->param('status', -1, 'intval');
            !empty($id) && $where['a.id'] = $id;
            !empty($bank_name) && $where['a.bank_name'] = $bank_name ;
            !empty($account_name) && $where['a.account_name'] = $account_name ;
            !empty($account_number) && $where['a.account_number'] = $account_number ;
            $status != -1 && $where['a.status'] = $status;

            $where['a.ms_id'] = $this->agent_id;
            $where['a.is_delete'] = 0;
            $where['pc.code'] = $this->pay_code;
            $limit = $request->param('limit');
            $page = $request->param('page');
            $start=$limit*($page-1);
//             查询所有账号信息
            $accounts = new EwmPayCode();
            $data = $this->modelEwmPayCode
                ->alias('a')
                ->join('pay_code pc', 'pc.id = a.code_type', 'left')
                ->where($where)->order('a.id', 'desc')
                ->field('a.*, pc.code')
                ->paginate($this->request->param('limit', 10));
            $result = $data->items();

// 今日订单统计数据
//            $todayOrdersResult = $accounts->orders()
//                ->whereTime('add_time', 'today')
//                ->group('code_id')
//                ->field([
//                    'code_id',
//                    'COUNT(*) AS count',
//                    'SUM(IF(status = 1, 1, 0)) AS success_count',
//                    'SUM(IF(status = 1, order_price, 0)) AS success_amount',
//                ])
//                ->select();
//
//            $todayOrdersData = [];
//            foreach ($todayOrdersResult as $order) {
//                $todayOrdersData[$order['code_id']] = $order;
//            }
//
//// 昨日订单统计数据
//            $yesterdayOrdersResult = $accounts->orders()
//                ->whereTime('add_time', 'yesterday')
//                ->group('code_id')
//                ->field([
//                    'code_id',
//                    'SUM(IF(status = 1, 1, 0)) AS success_count',
//                    'SUM(IF(status = 1, order_price, 0)) AS success_amount',
//                ])
//                ->select();
//
//            $yesterdayOrdersData = [];
//            foreach ($yesterdayOrdersResult as $order) {
//                $yesterdayOrdersData[$order['code_id']] = $order;
//            }
//
//            foreach ($result as $k => $v) {
//                $todayOrders = isset($todayOrdersData[$v['id']]) ? $todayOrdersData[$v['id']] : null;
//                $yesterdayOrders = isset($yesterdayOrdersData[$v['id']]) ? $yesterdayOrdersData[$v['id']] : null;
//
//                // 计算统计数据
//                $successRate = $todayOrders['count'] > 0 ? $todayOrders['success_count'] / $todayOrders['count'] : 0;
//                $todaySuccessAmount = $todayOrders['success_amount'] ?? 0;
//                $yesterdaySuccessAmount = $yesterdayOrders['success_amount'] ?? 0;
//
//                // 将统计数据合并到账号模型中
//                $result[$k]['today_receiving_rate'] = sprintf("%.2f", $successRate) * 100 . '%';
//                $result[$k]['today_receiving_amount'] = $todaySuccessAmount;
//                $result[$k]['today_receiving_number'] = $todayOrders['success_count'] ?? 0;
//                $result[$k]['yesterday_receiving_amount'] = $yesterdaySuccessAmount;
//                $result[$k]['yesterday_receiving_number'] = $yesterdayOrders['success_count'] ?? 0;
//            }



            $this->result($data || !empty($data) ?
                [
                    'code' => CodeEnum::SUCCESS,
                    'msg' => '',
                    'count' => $data->total(),
                    'data' =>$result
                ] : [
                    'code' => CodeEnum::ERROR,
                    'msg' => '暂无数据',
                    'count' => $data->count(),
                    'data' => []
                ]
            );
        }
    }


    /**
     * 删除二维码
     * @param Request $request
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del(Request $request)
    {
        $id =intval(trim($request->param('id')));
        $codeInfo =  Db::name('ewm_pay_code')->where(['id'=>$id])->find();
        if ($codeInfo['ms_id'] != $this->agent_id) {
            $this->error('删除失败,信息错误');
        }
        $re = Db::name('ewm_pay_code')->where(['id'=>$id])->update(['is_delete'=>1,'status'=>0]);
        if ($re) {
            $QueueLogic = new Queuev1Logic();
            $codeInfo['type'] = 3;
            $QueueLogic->delete($id, $codeInfo['type'], 1);
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    public function upload()
    {
        if($this->request->isPost()) {
            $this->result($this->logicFile->fileUpload('file', 'ewm'));
        }
    }



//    public function payCodeOrder(){
//        $pay_code_id = $this->request->param('code_id');
//        $this->assign('code_id',$pay_code_id);
//        return$this->fetch();
//    }
//
//    public function getPayCodeOrder(Request $request){
//        $where = [];
//        $where['code_id'] = $request->param('code_id');
//        $where['gema_userid'] = $this->agent_id;
//        $startTime = date('Y-m-d 00:00:00',time());
//        $endTime = date('Y-m-d 23:59:59',time());
//        $where['add_time'] = ['between', [$startTime, $endTime]];
//        if (!empty($request->param('startDate'))){
//            $startTime = $request->param('startDate');
//            $where['add_time'] = ['egt', strtotime($startTime)];
//        }
//
//        if (!empty($request->param('endDate'))){
//            $endTime = $request->param('endDate');
//            $where['add_time'] = ['elt',strtotime($endTime)];
//        }
//
//        if ($startTime && $endTime) {
//            $where['add_time'] = ['between', [strtotime($startTime), strtotime($endTime)]];
//        }
//
//        if (!empty($request->param('order_no'))){
//            unset($where['add_time']);
//            $where['order_no'] = $request->param('order_no');
//        }
//        if (!empty($request->param('pay_username'))){
//            $where['pay_username'] = $request->param('pay_username');
//        }
//
//        if (!empty($request->param('status'))){
//            $where['status'] = $request->param('status');
//        }
//
//        $limit = $request->param('limit');
//        $page = $request->param('page');
//        $start=$limit*($page-1);
//
//        $List = Db::name('ewm_order')->where($where)->limit($start,$limit)->select();
//
//        $count = Db::name('ewm_order')->where($where)->count();
//
//        if ($count <= 0){
//            return json([
//                'code'=>1,
//                'msg'=>'暂无数据'
//            ]);
//        }
//
//        return json([
//            'code'=>0,
//            'msg'=>'请求成功',
//            'data'=>$List,
//            'count'=>$count
//        ]);
//    }

        public function accountFlow(){
                $where['pay_time'] = $this->parseRequestDate3();
                $where['code_id'] = $this->request->param('code_id');
                $where['status'] = 1;
                $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->where($where)->sum('order_pay_price'));
                $this->assign('fees',$fees);
                return $this->fetch();
        }

        public function getAccountFlow(){
        $where = [];
        $where['pay_time'] = $this->parseRequestDate3();
        $where['status'] = 1;
        $where['code_id'] = $this->request->param('code_id');
             if (!empty($this->request->param('order_no'))){
                    $where['order_no'] = $this->request->param('order_no');
                }

            if (!empty($this->request->param('pay_username'))){
                $where['pay_username'] = $this->request->param('pay_username');
            }
            if (!empty($this->request->param('start_time'))){
                $where['pay_time'] = ['egt', strtotime($this->request->param('start_time'))];
            }

            if (!empty($this->request->param('end_time'))){
                $where['pay_time'] = ['elt', strtotime($this->request->param('end_time'))];
            }

            if (!empty($this->request->param('start_time')) && !empty($this->request->param('end_time'))){
                $where['pay_time'] = ['between', [strtotime($this->request->param('start_time')), strtotime($this->request->param('end_time'))]];
            }

            $limit = $this->request->param('limit');
            $page = $this->request->param('page');
            $start=$limit*($page-1);



            $data = Db::name('ewm_order')->where($where)->limit($limit,$start)->field('order_pay_price,pay_username,order_no,pay_time')->select();

            if (empty($data)){
             return json([
                'code'=>1,
                'msg'=>'暂无数据'
            ]);
            }else{
               return json([
                    'code'=>1,
                    'msg'=>'请求成功',
                    'data'=>$data,
                    'count'=>count($data)
                ]);
            }


        }


        public function searchStats(){
            $where = [];
            $where['pay_time'] = $this->parseRequestDate3();
            $where['status'] = 1;
            $where['code_id'] = $this->request->param('code_id');
            if (!empty($this->request->param('order_no'))){
                $where['order_no'] = $this->request->param('order_no');
            }

            if (!empty($this->request->param('pay_username'))){
                $where['pay_username'] = $this->request->param('pay_username');
            }
            if (!empty($this->request->param('start_time'))){
                $where['pay_time'] = ['egt', strtotime($this->request->param('start_time'))];
            }

            if (!empty($this->request->param('end_time'))){
                $where['pay_time'] = ['elt', strtotime($this->request->param('end_time'))];
            }

            if (!empty($this->request->param('start_time')) && !empty($this->request->param('end_time'))){
                $where['pay_time'] = ['between', [strtotime($this->request->param('start_time')), strtotime($this->request->param('end_time'))]];
            }

            $fees['amount'] = sprintf("%.2f",Db::name('ewm_order')->where($where)->sum('order_pay_price'));
            return json(['code'=>0,'msg'=>'请求成功','data'=>$fees]);
        }


        public function editNote(Request $request){
                if ($request->isPost()){
                    $id = $request->post('code_id');
                    $value = $request->post('value');
                    $res = Db::name('ewm_pay_code')->where(['id'=>$id,'ms_id'=>$this->agent_id])->update(['account_name'=>$value]);
                    if ($res === false){
                        return json(['code'=>1,'msg'=>'更新失败']);
                    }else{
                        return json(['code'=>0,'msg'=>'更新成功']);
                    }
                }
        }

        public function alipayF2F_test_ma(Request $request)
        {
            $data = $request->param();

            $codeinfo = $this->modelEwmPayCode->where('id', $data['id'])->find();

            if (empty($codeinfo)){
                return ['code'=>0,'msg'=>'非法请求'];
            }

            $f2fpayService=new AlipayF2FPayService();
            $orderNo = date('ymdHis').rand(1000,9999);
            $notify = 'http://'.$_SERVER["HTTP_HOST"].'/api/pay/alipayNotify';
            /*** 配置开始 ***/
            $f2fpayService->setAppid($codeinfo['account_number']);//https://open.alipay.com 账户中心->密钥管理->开放平台密钥，填写添加了电脑网站支付的应用的APPID
            $f2fpayService->setNotifyUrl($notify); //付款成功后的异步回调地址
            //商户私钥，填写对应签名算法类型的私钥，如何生成密钥参考：https://docs.open.alipay.com/291/105971和https://docs.open.alipay.com/200/105310
            $f2fpayService->setRsaPrivateKey($codeinfo['privateKey']);
            $f2fpayService->setTotalFee($data['amount']);//付款金额，单位:元
            $f2fpayService->setOutTradeNo($orderNo);//你自己的商品订单号，不能重复
            $f2fpayService->setOrderName('商品-'.$orderNo);//订单标题
            /*** 配置结束 ***/

            //var_dump($f2fpayService);die;
            //调用接口
            $result = $f2fpayService->doPay();
            $result = $result['alipay_trade_precreate_response'];

            if(!empty($result['code']) && $result['code'] && $result['code']=='10000') {
                //请求成功
                return json( ['code'=>1,'msg'=>'请求成功','result'=>$result['qr_code']]);
            }
            return json(['code'=>0,'msg'=>'请求失败']);
        }

    public function decodeImagePath()
    {
        $encryStr = $this->request->param('encryStr');
        return   openssl_decrypt(base64_decode($encryStr), "AES-128-CBC", '8e70f72bc3f53b12', 1, '99b538370c7729c7');
    }

    public function recycle()
    {
        return $this->fetch();
    }

    public function getRecycleList()
    {
        $where['ms_id'] = $this->agent_id;
        $where['is_delete'] = 1;
        $code_id = $this->modelPayCode->where('code',  $this->pay_code)->value('id');
        $where['code_type'] = $code_id;
        $data = $this->modelEwmPayCode->where($where)->paginate();
        if (empty($data)){
            return json([
                'code'=>0,
                'msg'=>'暂无数据'
            ]);
        }else{
            return json([
                'code'=>1,
                'msg'=>'请求成功',
                'data'=>$data->items(),
                'total'=> $data->total()
            ]);
        }
    }

    public function recovery()
    {
        if ($this->request->isAjax()){
            $id = $this->request->param('id');

            $code_id = $this->modelPayCode->where('code',  $this->pay_code)->value('id');

            $order = $this->modelEwmPayCode->where(['id'=>$id, 'code_type' => $code_id])->find();
            if (empty($order)){
                return json(['code'=>0,'msg'=>'数据错误']);
            }

            $res = $this->modelEwmPayCode->where('id',$id)->update(['is_delete'=>0]);

            if ($res === false){
                return json(['code'=>0,'msg'=>'恢复失败']);
            }else{
                return json(['code'=>1,'msg'=>'恢复成功']);
            }

        }
    }

}
