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


use app\api\service\ApiPayment;
use app\api\service\payment\JiufuPay;
use app\common\library\enum\CodeEnum;
use PayPal\Api\Payment;
use think\Db;

class Balance extends BaseAdmin
{
    /**
     * 资产
     *
     * @return mixed
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function index()
    {
        $where['status'] = 1;
        if (is_admin_login() != 1){
            $adminSonUser = Db::name('user')->where('admin_id',is_admin_login())->column('uid');
            $where['uid'] = ['in',$adminSonUser];
        }
        //总商户资产统计
        $this->assign('balance', $this->logicBalance->getBalaceStat($where));
        return $this->fetch();
    }

    /**
     * 商户资产列表
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function getList()
    {
        $where = [];

        //组合搜索
        !empty($this->request->param('uid')) && $where['uid']
            = ['eq', $this->request->param('uid')];

        /*!empty($this->request->param('username')) && $where['username']
            = ['like', '%' . $this->request->param('username') . '%'];*/
        if (is_admin_login() != 1){
            $adminSonUser = Db::name('user')->where('admin_id',is_admin_login())->column('uid');
            $where['uid'] = ['in',$adminSonUser];
        }
        if (!empty($this->request->param('username'))){
            $uids =  $adminSonUser = Db::name('user')->where([
                'admin_id'=>is_admin_login(),
                'status'=> ['neq','-1'],
                'username'=>['like', '%' . $this->request->param('username') . '%']
            ])->column('uid');

            $where['uid'] = ['in',$uids];

        }

        $sort = 'id desc';
        //排序
        if (!empty($this->request->param('enable'))) {
            if ($this->request->param('enable') == '1') {
                //升序
                $sort = 'enable asc';
            } elseif ($this->request->param('enable') == '2') {

                //降序
                $sort = 'enable desc';
            }
        }

        $data = $this->logicBalance->getBalanceList($where, '*', $sort, input('limit'));

        foreach ($data as $k => $v) {
            //商户名称


            $data[$k]['username'] =$this->modelUser->where(['uid' => $v['uid']])->value('username');
            $data[$k]['last_online_time'] = $this->modelUser->where(['uid' => $v['uid']])->value('last_online_time');
        }

        $data = $data->toArray()['data'];
        $count = $this->logicBalance->getBalanceCount($where);

        $this->result($data || !empty($data) ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg' => '',
                'count' => $count,
                'data' => $data
            ] : [
                'code' => CodeEnum::ERROR,
                'msg' => '暂无数据',
                'count' => $count,
                'data' => $data
            ]
        );
    }


    /*
     *导出商户资金
     *
     */
    public function exportBalance()
    {
        $where = [];

        //组合搜索
        !empty($this->request->param('uid')) && $where['a.uid']
            = ['eq', $this->request->param('uid')];
        !empty($this->request->param('username')) && $where['u.username']
            = ['like', '%' . $this->request->param('username') . '%'];
        if(is_admin_login() != 1){
            $sonUser = Db::name('user')->where('admin_id',is_admin_login())->column('uid');
            $where['a.uid'] = ['in',$sonUser];
        }

        $where['u.status'] = ['neq','-1'];
//        $data = $this->logicBalance->getBalanceList($where, 'a.*,u.username', 'a.create_time desc', false);


        $data = $this->modelBalance->alias('a')
            ->join('user u','a.uid = u.uid')
            ->where($where)
            ->field('a.*,u.username')
            ->order('a.create_time desc')
            ->select();

        //组装header 响应html为execl 感觉比PHPExcel类更快
        $strTable = '<table width="500" border="1">';
        $strTable .= '<tr>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">商户UID</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">商户UID</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="100">可用余额</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">冻结余额</td>';
//        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">状态</td>';
//        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">创建时间</td>';

        $strTable .= '</tr>';
        $userStatus = ['禁止', '正常'];

        if (is_array($data)) {
            foreach ($data as $k => $val) {
                $strTable .= '<tr>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['uid'] . ' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['username'] . ' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['enable'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['disable'] . '</td>';
//                $strTable .= '<td style="text-align:left;font-size:12px;">' . $userStatus[$val['status']] . '</td>';
//                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['create_time'] . '</td>';
                $strTable .= '</tr>';
                unset($data[$k]);
            }
            $strTable .= '</table>';
            downloadExcel($strTable, 'balace_cash');
        }
        $this->error("暂无导出记录");
    }


    /**
     * 商户账户收支明细信息（仅做记录）
     *
     * @return mixed
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function details()
    {
        $uid = $this->request->param('id');
        if (is_admin_login() != 1) {
            $user = Db::name('user')->where('admin_id',is_admin_login())->column('uid');
//            if ($user['admin_id'] != is_admin_login()) {
//                return $this->error('非法请求');
//            }
            $where['uid'] = ['in',$user];

        }
        $where['create_time'] = $this->parseRequestDate3();
        $where['type'] = ['eq', $this->request->param('type', 'enable')];
        $uid && $where['uid'] = $uid;
        $data = $this->logicBalanceChange->getBalanceChangeInfo($where);
        $this->assign('uid', $uid);
        $this->assign('data', $data);
        return $this->fetch();
    }
   
    public function details_tixian()
    {
        $id = $this->request->param('id');
        $where['id'] = $id;
        $data  = $this->logicBalanceCash->getOrderInfo($where);;
        $this->assign('data', $data);
        return $this->fetch();
    }
    /**
     *商户账户收支明细信息
     *
     */
    public function exportBalanceChange()
    {



        $where = [];

        if (is_admin_login() != 1){
            $where['u.admin_id'] = is_admin_login();
        }

        //组合搜索
        if ($this->request->param('uid')) {
            $where['a.uid'] = ['eq', $this->request->param('uid')];
        }


        if ($this->request->param('username')) {
            $where['u.username'] = ['like', '%' . $this->request->param('username') . '%'];
        }

        $where['a.type'] = ['eq', $this->request->param('type', 'enable')];
        $amount = $this->request->param('amount');
        $remarks = $this->request->param('remarks');
        $remarks && $where['a.remarks'] = ['like', "%" . $remarks . "%"];
        //时间搜索  时间戳搜素
        $where['a.create_time'] = $this->parseRequestDate3();

        $isPlarOp = $this->request->param('is_flat_op', -1);
        ($isPlarOp != -1) && $where['a.is_flat_op'] = $isPlarOp;
        $amount &&     $where['a.increase|a.reduce'] = ['eq', $amount];
        $field = '*,sum(increase) as total_increase,sum(reduce) as total_reduce';
//        $data = $this->logicBalanceChange->getBalanceChangeList($where, true, 'id desc', false);

        $data = $this->modelBalanceChange->alias('a')
            ->alias('a')
            ->join('user u', 'u.uid = a.uid', 'left')
            ->field('a.*,u.username')
            ->where($where)
            ->select();

        //组装header 响应html为execl 感觉比PHPExcel类更快
        $strTable = '<table width="500" border="1">';
        $strTable .= '<tr>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">商户UID</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">商户名称</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="100">资金类型</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">可用余额</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">增加金额</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">减少金额</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">变动后金额</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">变动备注</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">更新时间</td>';

        $strTable .= '</tr>';
        $userStatus = ['禁止', '正常'];

        if (is_array($data)) {
            foreach ($data as $k => $val) {
                $strTable .= '<tr>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['uid'] . ' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['username'] . ' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['type'] . ' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['preinc'] . ' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['increase'] . ' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['reduce'] . ' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['suffixred'] . ' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['remarks'] . ' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['update_time'] . ' </td>';
                $strTable .= '</tr>';
                unset($data[$k]);
            }
            $strTable .= '</table>';
            downloadExcel($strTable, 'balace_change');
        }
        $this->error("暂无导出记录");
    }


    /**
     * 账变统计
     */
    public function searchBalanceCal()
    {
        $where = [];
        if (is_admin_login() != 1){
            $where['u.admin_id'] = is_admin_login();
        }
        //组合搜索
        $uid =  $this->request->param('uid');
        $uid && $where['a.uid'] = ['eq', $uid];

        $username = $this->request->param('username');
        $username && $where['u.username'] = ['like', "%" . $username . "%"];

        $where['a.type'] = ['eq', $this->request->param('type', 'enable')];
        $remarks = $this->request->param('remarks');
        $remarks && $where['remarks'] = ['like', "%" . $remarks . "%"];
        $amount = $this->request->param('amount');
        $amount &&     $where['increase|reduce'] = ['eq', $amount];
        //时间搜索  时间戳搜素
        $where['a.create_time'] = $this->parseRequestDate3();

        $isPlarOp = $this->request->param('is_flat_op', -1);
        ($isPlarOp != -1) && $where['is_flat_op'] = $isPlarOp;
        //$data = $this->logicBalanceChange->getBalanceChangeInfo($where);

        $field = 'COALESCE(sum(`increase`),0) as total_increase,COALESCE(sum(`reduce`),0) as total_reduce';

        $data = $this->modelBalanceChange->alias('a')->join('user u', 'u.uid = a.uid', 'left')->where($where)->field($field)->find();

        exit(json_encode($data));

    }


    /**
     * 获取商户账户收支明细信息
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function getDetails()
    {
        $where = [];

        if (is_admin_login() != 1){
            $where['u.admin_id'] = is_admin_login();
        }

        //组合搜索
        $uid = $this->request->param('uid');
        $username = $this->request->param('username');
        $where['a.type'] = ['eq', $this->request->param('type', 'enable')];

        $uid && $where['a.uid'] = $uid;
        $username && $where['u.username'] = ['like', "%" . $username . "%"];

        $remarks = $this->request->param('remarks');
        $amount = $this->request->param('amount');
        $remarks && $where['a.remarks'] = ['like', "%" . $remarks . "%"];
        //时间搜索  时间戳搜素
        $where['a.create_time'] = $this->parseRequestDate3();

        $isPlarOp = $this->request->param('is_flat_op', -1);
        ($isPlarOp != -1) && $where['a.is_flat_op'] = $isPlarOp;
        $amount &&     $where['increase|reduce'] = ['eq', $amount];

        $field = '*,sum(increase) as total_increase,sum(reduce) as total_reduce';
//        $data = $this->logicBalanceChange->getBalanceChangeList($where, true, 'id desc', false);
//
//        $count = $this->logicBalanceChange->getBalanceChangeCount($where);

        $data = $this->modelBalanceChange->alias('a')
            ->join('user u','a.uid = u.uid','left')
            ->where($where)
            ->field('a.*,u.username')
            ->order('id desc')
            ->paginate();

        $this->result($data->total()?
            [
                'code' => CodeEnum::SUCCESS,
                'msg' => '',
                'count' => $data->total(),
                'data' => $data->items()
            ] : [
                'code' => CodeEnum::ERROR,
                'msg' => '暂无数据',
                'count' => $data->total(),
                'data' => []
            ]
        );

    }

    /**
     * 打款记录(仅作记录)
     *
     * @return mixed
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function paid()
    {
        //时间搜索  时间戳搜素
        $parseDate = $this->parseRequestDate();
        $where['create_time'] = $parseDate;
        if (is_admin_login() != 1){
            $adminSonUser = Db::name('user')->where('admin_id',is_admin_login())->column('uid');
            $where['uid'] = ['in',$adminSonUser];
        }
        $data['total'] = Db::name('balance_cash')->where($where)->sum('amount');
        $data['success_total'] = Db::name('balance_cash')->where($where)->where('status',2)->sum('amount');
        $this->assign('fees',$data);
        return $this->fetch();
    }


    public function searchBalanceCash(){
        $parseDate = $this->parseRequestDate();
        $where['create_time'] = $parseDate;
        if (!empty($this->request->param('uid'))){
            $where['uid'] = $this->request->param('uid');
            $adminSonUser = Db::name('user')->where('admin_id',is_admin_login())->column('uid');
            if (!in_array($where['uid'],$adminSonUser)){
                $this->result([
                        'code' => CodeEnum::ERROR,
                        'msg' => '非法请求',
                    ]
                );
            }
        }

        if (!empty($this->request->param('username'))){
            $uids = model('user')->where('username',$this->request->param('username'))->column('uid');
            $where['uid'] = ['in',$uids];
        }

        if ($this->request->param('status') != '' && $this->request->param('status') >= 0){
            $where['status'] = $this->request->param('status');
        }

        $data['total'] = Db::name('balance_cash')->where($where)->sum('amount');
        $data['success_total'] = Db::name('balance_cash')->where($where)->where(function ($query){
            $query->where('status',2);
        })->sum('amount');
        $this->result([
                'code' => CodeEnum::SUCCESS,
                'msg' => '请求成功',
                'data'=> $data
            ]
        );


    }


    public function every()
    {
        return $this->fetch();
    }

    public function everyList()
    {
        $where = [];
        //组合搜索
        if (!empty($this->request->param('uid'))) {
            $where['a.uid'] = $this->request->param('uid');
            $wheres['uid'] = $this->request->param('uid');
        }

        //时间搜索  时间戳搜素
        $parseDate = $this->parseRequestDate3();
        $where['a.createtime'] = $parseDate;

        $data = $this->logicBalanceEveryday->getEverydayList($where, 'a.*,u.username', 'a.createtime desc', input('limit'));
        $wheres['createtime'] = $parseDate;
        $count = $this->logicBalanceEveryday->getEverydayCount($wheres);
        $data = $data->toArray()['data'];

        $this->result($data || !empty($data) ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg' => '',
                'count' => $count,
                'data' => $data
            ] : [
                'code' => CodeEnum::ERROR,
                'msg' => '暂无数据',
                'count' => $count,
                'data' => $data
            ]
        );
    }

    /**
     * @return void
     * 商户资金tip
     */

    public function searchUserBalance(){
        $where['status'] = 1;

        if (is_admin_login() != 1){
            $adminSonUser = Db::name('user')
                ->where('admin_id',is_admin_login())
                ->where('status','neq','-1')
                ->column('uid');
            $where['uid'] = ['in',$adminSonUser];
        }
        if (!empty($this->request->param('uid'))) {
                $where['uid'] = $this->request->param('uid');
        }


        if (!empty($this->request->param('username'))){
            $uids =  $adminSonUser = Db::name('user')->where([
                'admin_id'=>is_admin_login(),
                'status' => ['neq','-1'],
                'username'=>['like', '%' . $this->request->param('username') . '%']
            ])->column('uid');

            $where['uid'] = ['in',$uids];

        }

        $data = $this->logicBalance->searchgetBalaceStat($where)->toarray();
//        print_r($data);die;

        return json(['code'=>0,'msg'=>'请求成功','data'=>$data]);

    }

    /**
     * 获取打款记录API
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function paidList()
    {
        $where = [];
        //组合搜索
        !empty($this->request->param('id')) && $where['a.id|a.uid']
            = ['like', '%' . $this->request->param('id') . '%'];

        !empty($this->request->param('cash_no')) && $where['a.cash_no']
            = ['like', '%' . $this->request->param('a.cash_no') . '%'];

        //状态
        if (!empty($this->request->get('status')) || $this->request->get('status') === '0') {
            $where[config('database.prefix') . 'balance_cash.status'] = $this->request->get('status');
        }
        !empty($this->request->param('uid')) && $where['a.uid'] = $this->request->param('uid');


        if (!empty($this->request->param('username'))){
            $uids =  $adminSonUser = Db::name('user')->where([
                'admin_id'=>is_admin_login(),
                'username'=>['like', '%' . $this->request->param('username') . '%']
            ])->column('uid');

            $where['a.uid'] = ['in',$uids];

        }

        //时间搜索  时间戳搜素
        $parseDate = $this->parseRequestDate();
        $where['a.create_time'] = $parseDate;
        if (is_admin_login() != 1){
            $adminSonUser = Db::name('user')->where('admin_id',is_admin_login())->column('uid');
            $where['a.uid'] = ['in',$adminSonUser];
        }
        $data = $this->logicBalanceCash->getOrderCashList($where, 'a.*,uu.username,u.account,u.account_name,b.name as method,pc.name as channel_name,uu.mark_abnormal', 'a.create_time desc', input('limit'));
        unset($where['a.create_time']);
        unset($where['a.uid']);
        $where['create_time'] = $parseDate;
        $count = $this->logicBalanceCash->getOrderCashCount($where);
        $data = $data->toArray()['data'];

//        $is_open_channel_fund = 0;
//        $is_open_channel_fund_config = \app\common\model\Config::where(['name'=>'is_open_channel_fund'])->find()->toArray();
//        if($is_open_channel_fund_config) {
//            if ($is_open_channel_fund_config['value'] == '1') {
//                $is_open_channel_fund = 1;
//            }
//        }

        $is_paid_select_channel = 0;
        $is_paid_select_channel_config = \app\common\model\Config::where(['name' => 'is_paid_select_channel'])->find()->toArray();
        if ($is_paid_select_channel_config) {
            if ($is_paid_select_channel_config['value'] == '1') {
                $is_paid_select_channel = 1;
            }
        }

        foreach ($data as $k => $v) {
            $data[$k]['is_paid_select_channel'] = $is_paid_select_channel;
            if($data[$k]['status']==3){
           //   $data[$k]['account']= '*****';
            }else
           {
              $data[$k]['account']= '*****';
           }
        }

        $this->result($data || !empty($data) ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg' => '',
                'count' => $count,
                'data' => $data
            ] : [
                'code' => CodeEnum::ERROR,
                'msg' => '暂无数据',
                'count' => $count,
                'data' => $data
            ]
        );
    }


    public function select_channel()
    {
        //渠道列表
        $channel = $this->logicPay->getChannelList(['status' => '1'], true, 'create_time desc', false);
        $this->assign('channel', $channel);
        return $this->fetch();
    }


    public function select_channelv2()
    {
        //获取单条记录
        $order = $this->logicBalanceCash->getOrderCashInfoV2(['a.id' => $this->request->param('id')]);
        //渠道列表
        $channel = $this->logicPay->getChannelList(['status' => '1'], true, 'create_time desc', false);

        foreach ($channel as $k => $v) {

            $class_name = 'app\api\service\payment\\' . $v['action'];

            if (!class_exists($class_name)) {
                unset($channel[$k]);
                continue;
            }

            if (!method_exists($class_name, 'getBankCode')) {
                unset($channel[$k]);
                continue;
            }
            if (!ApiPayment::$v['action']()->getBankCode(isset($order['bank_code']) ? $order['bank_code'] : '')) {
                unset($channel[$k]);
                continue;
            }

        }
        $this->assign('channel', $channel);
        return $this->fetch('select_channel');
    }


    /**
     * 通过  提交队列后台打款
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     *
     */
    public function deal()
    {
        $this->result($this->logicBalanceCash->pushBalanceCash(['a.id' => $this->request->param('cash_id')], $this->request->param('audit_remarks'), $this->request->param('channel_id', '0')));
    }

    public function successOrder()
    {
        $this->result($this->logicBalanceCash->successTranspondBalanceCash(['a.id' => $this->request->param('cash_id')], $this->request->param('audit_remarks')));
    }

    /**
     * 转发到跑分平台
     */
    public function transpond()
    {
        $this->result($this->logicBalanceCash->transpondBalanceCash(['a.id' => $this->request->param('cash_id')], $this->request->param('admin_id'), $this->request->param('audit_remarks')));
    }

    /**
     *  转发到渠道代付
     */
    public function transpond_channel()
    {
        $this->result($this->logicBalanceCash->transpond_channel(['a.id' => $this->request->param('cash_id')], $this->request->param('cnl_id'), $this->request->param('audit_remarks')));
    }
   
     /**
     *  处理
     */
    public function handle()
    {
        $this->result($this->logicBalanceCash->handle(['a.id' => $this->request->param('cash_id')], $this->request->param('cnl_id'), $this->request->param('audit_remarks')));
    }


    /**
     * 撤回
     */
    public function revocation()
    {
        $this->result($this->logicBalanceCash->revocationBalanceCash(['a.id' => $this->request->param('cash_id')]));
    }


    /**
     * 选择商户
     */
    public function select_admin()
    {

        $this->assign('adminList', $this->logicBalanceCash->getPaofenAdminList()['data']);

        return $this->fetch();
    }


    /**
     * 驳回申请
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     *
     */
    public function rebut()
    {
        $this->result($this->logicBalanceCash->rebutBalanceCash(['a.id' => $this->request->param('cash_id')], $this->request->param('audit_remarks')));
    }


    /**
     * 读取审核配置开关
     */
    public function getAuditSwitch()
    {
        $this->result($this->logicBalanceCash->getAuditSwitch());
    }


    /**
     * 修改余额
     */
    public function changeBalance()
    {
        if (is_admin_login() != 1) {
            $user = $this->logicUser->getUserInfo(['uid' => $this->request->param('uid')]);
            if ($user['admin_id'] != is_admin_login()) {
                return $this->error('非法请求');
            }
        }
        $is_open_channel_fund = 0;
        $is_open_channel_fund_config = \app\common\model\Config::where(['name' => 'is_open_channel_fund'])->find()->toArray();
        if ($is_open_channel_fund_config) {
            if ($is_open_channel_fund_config['value'] == '1') {
                $is_open_channel_fund = 1;
            }
        }

        // post 是提交数据
        $uid = $this->request->param('uid/d');
        if ($this->request->isPost()) {
            if (session('__token__') != $this->request->param('__token__')) {
                $this->result(CodeEnum::ERROR, '请刷新页面重试');
            }
            $setDec = $this->request->param('change_type');
            $amount = $this->request->param('amount');
            $field = $this->request->param('change_money_type');
            $remarks = htmlspecialchars($this->request->param('remarks/s'));


            //判断 如果操作的是增加 并且 是余额  这里要给渠道增加 余额 并且计算费率

            $channel_id = $this->request->param('channel_id');
            $account_id = $this->request->param('account_id');
            if ($is_open_channel_fund && $field == 'enable' && $setDec == '0' && $channel_id && $account_id) {
                // 计算费率
                //获取用户分成
                $profit = $this->logicUser->getUserProfitInfo(['uid' => $uid, 'cnl_id' => $account_id]);

                $account = $this->logicPay->getAccountInfo(['id' => $account_id]);

                if (empty($profit)) $profit = $account;

                //渠道分成
                $channel_amount = bcmul($amount, $account['rate'], 3);
                //用户分成
                $amount = bcmul($amount, $profit['urate'], 3);
                //渠道
                $this->logicPayChannelChange->creatPayChannelChange($channel_id, $channel_amount, $remarks, false, 1);
            }

            $order_no = '';
            $type_reason = $this->request->param('type_reason');
            if(empty( $type_reason) ||  $type_reason==0)
            {
                $msg = "操作失败,请选择操作原因";
                $this->result(CodeEnum::ERROR, $msg);
            }
            if( $type_reason == 3 || $type_reason == 5)
            {

                $order_no = $this->request->param('remarks');

                if (preg_match("/[{4e00}-\x{9fa5}]+/u", $order_no)) {
                    $this->result(CodeEnum::ERROR, "备注有误，请输入正确订单号");
                }

            }
            $ret = $this->logicBalanceChange->creatBalanceChange($uid, $amount, $remarks, $field, $setDec, 1, $order_no,$type_reason);

            /**  2020-2-20 update  **/
            //如果操作的是增加冻结金额
            if ($field == 'disable') {
                //增加对应余额
                if (!$setDec) {
                    $result = $this->logicBalanceChange->creatBalanceChange($uid, $amount, $remarks, 'enable', !$setDec, 1);
                    if (!$result) {
                        return false;
                    }
                }

            }
            session('__token__', null);
            $code = $ret ? CodeEnum::SUCCESS : CodeEnum::ERROR;
            $msg = $ret ? "操作成功" : "操作失败";
            $this->result($code, $msg);
        }

        $this->assign('user', $this->modelUser->where('uid', $uid)->find());
        //渠道列表
        $this->assign('payChannelList', $this->logicPay->getChannelList(['status' => '1'], true, 'create_time desc', false));
        $this->assign('is_open_channel_fund', $is_open_channel_fund);
        return $this->fetch();
    }


    /**
     * 导出用户提现记录
     */
    public function exportBalanceCash()
    {
        $where = [];
        //组合搜索
        !empty($this->request->param('id')) && $where['a.id|a.uid']
            = ['like', '%' . $this->request->param('id') . '%'];

        !empty($this->request->param('cash_no')) && $where['a.cash_no']
            = ['like', '%' . $this->request->param('a.cash_no') . '%'];

        //状态
        if (!empty($this->request->get('status')) || $this->request->get('status') === '0') {
            $where[config('database.prefix') . 'balance_cash.status'] = $this->request->get('status');
        }
        //时间搜索  时间戳搜素
        $parseDate = $this->parseRequestDate();
        $where['a.create_time'] = $parseDate;
        $data = $this->logicBalanceCash->getOrderCashList($where, 'a.*,u.account,u.account_name,b.name as method', 'a.create_time desc', false);
        //组装header 响应html为execl 感觉比PHPExcel类更快
        $balanceCashStatus = ['驳回', '等待中', '已打款', '处理中'];
        $strTable = '<table width="500" border="1">';
        $strTable .= '<tr>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">ID标识</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="100">交易商户</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">打款单号</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">交易金额</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">交易手续费</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">交易方式</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">收款姓名</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">收款账号</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">状态</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">创建时间</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">	
更新时间</td>';
        $strTable .= '</tr>';

        if (is_array($data)) {
            foreach ($data as $k => $val) {
                $strTable .= '<tr>';
                $strTable .= '<td style="text-align:center;font-size:12px;">&nbsp;' . $val['id'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['uid'] . ' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['cash_no'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['amount'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['commission'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['method'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['account_name'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['account'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $balanceCashStatus[$val['status']] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['create_time'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['update_time'] . '</td>';
                $strTable .= '</tr>';
                unset($data[$k]);
            }
            $strTable .= '</table>';
            downloadExcel($strTable, 'balace_cash');
        }
        $this->error("暂无导出记录");
    }


    /**
     * 资产
     *
     * @return mixed
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function change()
    {
        //总商户资产统计
        $parseDate = $this->parseRequestDate3();
        $where['create_time'] = $parseDate;
        $where['is_flat_op'] = 1;
        $this->assign('change', $this->logicBalanceChange->getBalanceChangeInfo($where));
        return $this->fetch();
    }

    public function searchChangeMoney()
    {
        $where = [];
        //组合搜索
        !empty($this->request->param('uid')) && $where['uid']
            = $this->request->param('uid');
        $parseDate = $this->parseRequestDate3();
        $where['create_time'] = $parseDate;
        $where['is_flat_op'] = 1;
        $result = $this->logicBalanceChange->getBalanceChangeInfo($where);
        $this->result('1', '请求成功', $result);
    }

    /**
     * 获取打款记录API
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function changeList()
    {
        $where = [];
        //组合搜索
        !empty($this->request->param('uid')) && $where['uid']
            = $this->request->param('uid');

        //时间搜索  时间戳搜素
        $parseDate = $this->parseRequestDate3();
        $where['create_time'] = $parseDate;
        $where['is_flat_op'] = 1;
        $data = $this->logicBalanceChange->getBalanceChangeList($where, '*', 'create_time desc', input('limit'));
        $count = $this->logicBalanceChange->getBalanceChangeCount($where);
        $data = $data->toArray()['data'];
        $this->result($data || !empty($data) ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg' => '',
                'count' => $count,
                'data' => $data
            ] : [
                'code' => CodeEnum::ERROR,
                'msg' => '暂无数据',
                'count' => $count,
                'data' => $data
            ]
        );
    }

    /**
     * 增减余额
     */
    public function changeAdd()
    {
        //获取商户列表
        $where['status'] = '1';
        $this->assign('userList', $this->logicUser->getUserList($where, true, 'create_time desc', false));
        return $this->fetch();
    }

}
