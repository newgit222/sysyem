<?php
/**
 * Created by PhpStorm.
 * User: zhangxiaohei
 * Date: 2020/2/7
 * Time: 21:27
 */

namespace app\admin\controller;


use app\api\logic\AlipayWapService;
use app\common\library\CryptAes;
use app\common\library\enum\CodeEnum;
use app\common\logic\CodeLogic;
use app\common\logic\Config;
use app\common\logic\MsMoneyType;
use app\common\logic\MsSomeBill;
use app\common\logic\Queuev1Logic;
use app\common\model\ActionLog;
use app\common\model\EwmPayCode;
use app\common\model\MsWhiteIp;
use think\Cache;
use think\Db;
use think\Error;
use think\Exception;
use think\Log;
use think\Request;
use think\Validate;
use think\View;
use function Composer\Autoload\includeFile;


/**
 * 码商管理
 * Class Mch
 * @package app\admin\controller
 */
class Ms extends BaseAdmin
{

    /**
     * 一键停工
     */
    public function closemswork(){
        if (is_admin_login() != 1){
            $res = Db::name('ms')->where('admin_id',is_admin_login())->update(['work_status'=>0]);
        }else{
            $res = Db::name('ms')->where('work_status',1)->update(['work_status'=>0]);
        }
        if ($res === false){
            return json(['code'=>0]);
        }else{
            return json(['code'=>1]);
        }
    }

    /**
     * 码商统计
     */
    public function stats(){
        $code = Db::name('pay_code')->where('status',1)->select();

        $where = [];
        $msWhere = [];
        if (is_admin_login() != 1){
            $msWhere['admin_id'] = is_admin_login();

        }

        $start_time = date('Y-m-d 00:00:00',time());
        $end_time = date('Y-m-d 23:59:59',time());

        $where1['add_time'] = ['between time',[$start_time,$end_time]];

        $ms_ids = Db::name('ms')->where($msWhere)->where('status',1)->column('userid');

        $GemapayOrderModel = new \app\common\model\EwmOrder();

        $order_stats = $GemapayOrderModel->whereTime('add_time', 'today')->where('gema_userid', 'in', $ms_ids)->where($where)->where($where1)->field('sum(order_price) as total_money,count(id) as total_count')->find();

        $order_success_stats = $GemapayOrderModel->whereTime('add_time', 'today')->where('status',1)->where('gema_userid', 'in', $ms_ids)->where($where1)->where($where)->field('sum(order_price) as total_money,count(id) as total_count')->find();
        $starts = strtotime(date('Y-m-d 00:00:00',time())); // 替换为您所需的起始日期和时间
        $end = strtotime(date('Y-m-d 23:59:59',time())); // 替换为您所需的结束日期和时间
        $order_geri_success_stats = $GemapayOrderModel
//            ->where('gema_userid',$v['userid'])
            ->where('status', 1)
            ->where($where)
            ->whereTime('pay_time', 'today')
            ->where(function ($query) use ($starts, $end, $where1) {
                $query->whereTime('add_time', 'not between', [$starts, $end]);
//                        ->whereOr($where1);
            })
            ->field('sum(order_price) as total_money, count(id) as total_count')
            ->find();


        $total_order_num = (float)$order_stats['total_count'];

        $total_ok_order_num = (float)$order_success_stats['total_count'];

        $total_amount = (float)$order_stats['total_money'];

        $total_amount_ok = (float)$order_success_stats['total_money'];

        $geri_success_stats = (float)$order_geri_success_stats['total_money'];

        $this->assign('code',$code);
        $this->assign('total_order_num', $total_order_num);
        $this->assign('total_ok_order_num', $total_ok_order_num);
        $this->assign('total_amount', $total_amount);
        $this->assign('total_amount_ok', $total_amount_ok);
        $this->assign('geri_success_stats', $geri_success_stats);
        return $this->fetch();
    }

    /**
     * 获取码商统计
     */
    public function getMsStats(){
        $where = [];
        $msWhere = [];
        if (is_admin_login() != 1){
            $msWhere['admin_id'] = is_admin_login();

        }
        if (!empty($this->request->param('username'))){
            $where['gema_username'] = $this->request->param('username');
            $msWhere['username'] = $this->request->param('username');
        }

        if (!empty($this->request->param('code_type'))){
            $where['code_type'] = $this->request->param('code_type');
        }

        $start_time = date('Y-m-d 00:00:00',time());
        $end_time = date('Y-m-d 23:59:59',time());

        if (!empty($this->request->param('start')) && !empty($this->request->param('end'))){
            $start_time = $this->request->param('start');
            $end_time = $this->request->param('end');
        }

        $where1['add_time'] = ['between time',[$start_time,$end_time]];

        if (!empty($this->request->param('agent_name'))){
//            $where['code_type'] = $this->request->param('code_type');
            $this->son_id = [];
            $agent = Db::name('ms')->where('username',$this->request->param('agent_name'))->field('admin_id,userid')->find();
            if(is_admin_login() != 1){
                if ($agent['admin_id'] != is_admin_login()){
                    return json(['code'=>1,'msg'=>'非法请求']);
                }
            }
            $msids = $this->getIds($agent['userid']);
            array_push($msids,$agent['userid']);
            $msWhere['userid'] = ['in',$msids];
        }


        $limit = $this->request->param('limit');
        $page = $this->request->param('page');
        $start=$limit*($page-1);


        $msList = Db::name('ms')->where($msWhere)->where('status',1)->limit($start,$limit)->select();
        $GemapayOrderModel = new \app\common\model\EwmOrder();
        $starts = strtotime(date('Y-m-d 00:00:00',time())); // 替换为您所需的起始日期和时间
        $end = strtotime(date('Y-m-d 23:59:59',time())); // 替换为您所需的结束日期和时间

        foreach ($msList as $k=>$v){



            $order_stats = $GemapayOrderModel->where('gema_userid',$v['userid'])->where($where)->where($where1)->field('sum(order_price) as total_money,count(id) as total_count')->find();
            $order_success_stats = $GemapayOrderModel->where('status',1)->where('gema_userid',$v['userid'])->where($where)->where($where1)->field('sum(order_price) as total_money,count(id) as total_count')->find();
//            unset($where['add_time']);
            $order_geri_success_stats = $GemapayOrderModel
                ->where('gema_userid',$v['userid'])
                ->where('status', 1)
                ->where($where)
                ->whereTime('pay_time', 'today')
                ->where(function ($query) use ($starts, $end, $where1) {
                    $query->whereTime('add_time', 'not between', [$starts, $end]);
//                        ->whereOr($where1);
                })
                ->field('sum(order_price) as total_money, count(id) as total_count')
                ->find();

            $msList[$k]['total_number'] = (float)$order_stats['total_count'];

            $msList[$k]['success_number'] = (float)$order_success_stats['total_count'];

            $msList[$k]['total_amount'] = (float)$order_stats['total_money'];

            $msList[$k]['success_amount'] = (float)$order_success_stats['total_money'];
            $msList[$k]['geri_success_stats'] = (float)$order_geri_success_stats['total_money'];

                 if ($msList[$k]['success_number'] == 0){
                     $msList[$k]['success_rate'] = 0;
                 }else{
                     $mssuccessrate = sprintf("%.2f",$msList[$k]['success_number'] / $msList[$k]['total_number'] * 100);
                     $msList[$k]['success_rate'] = $mssuccessrate;
                 }


        }

        return json(['code'=>0,'data'=>$msList,'count'=>Db::name('ms')->where($msWhere)->where('status',1)->count()]);

    }

    public function searchMsStats()
    {
        $where = [];
        $msWhere = [];
        if (is_admin_login() != 1){
            $msWhere['admin_id'] = is_admin_login();

        }
        if (!empty($this->request->param('username'))){
            $where['gema_username'] = $this->request->param('username');
        }

        if (!empty($this->request->param('code_type'))){
            $where['code_type'] = $this->request->param('code_type');
        }

        $start_time = date('Y-m-d 00:00:00',time());
        $end_time = date('Y-m-d 23:59:59',time());

        if (!empty($this->request->param('start')) && !empty($this->request->param('end'))){
            $start_time = $this->request->param('start');
            $end_time = $this->request->param('end');
        }

        $where1['add_time'] = ['between time',[$start_time,$end_time]];
        $starts = strtotime(date('Y-m-d 00:00:00',time())); // 替换为您所需的起始日期和时间
        $end = strtotime(date('Y-m-d 23:59:59',time())); // 替换为您所需的结束日期和时间

        if (!empty($this->request->param('agent_name'))){
            $this->son_id = [];
            $agent = Db::name('ms')->where('username',$this->request->param('agent_name'))->field('admin_id,userid')->find();
            if(is_admin_login() != 1){
                if ($agent['admin_id'] != is_admin_login()){
                    return json(['code'=>1,'msg'=>'非法请求']);
                }
            }
            $msids = $this->getIds($agent['userid']);
            array_push($msids,$agent['userid']);
            $msWhere['userid'] = ['in',$msids];
        }

        $ms_ids = Db::name('ms')->where($msWhere)->where('status',1)->column('userid');




        $GemapayOrderModel = new \app\common\model\EwmOrder();

        $order_stats = $GemapayOrderModel->where('gema_userid', 'in', $ms_ids)->where($where)->where($where1)->field('sum(order_price) as total_money,count(id) as total_count')->find();
        $order_success_stats = $GemapayOrderModel->where('status',1)->where('gema_userid', 'in', $ms_ids)->where($where)->where($where1)->field('sum(order_price) as total_money,count(id) as total_count')->find();
        $order_geri_success_stats = $GemapayOrderModel
//            ->where('gema_userid',$v['userid'])
            ->where('status', 1)
            ->whereTime('pay_time', 'today')
            ->where($where)
            ->where(function ($query) use ($starts, $end, $where1) {
                $query->whereTime('add_time', 'not between', [$starts, $end]);
//                        ->whereOr($where1);
            })
            ->field('sum(order_price) as total_money, count(id) as total_count')
            ->find();

        $total_order_num = (float)$order_stats['total_count'];

        $total_ok_order_num = (float)$order_success_stats['total_count'];

        $total_amount = (float)$order_stats['total_money'];

        $total_amount_ok = (float)$order_success_stats['total_money'];
        $geri_success_stats = (float)$order_geri_success_stats['total_money'];




//
//        $total_order_num = Db::name('ewm_order')
//            ->where($where)
//            ->where('gema_userid', 'in', $ms_ids)
//            ->count();
//
//        $total_ok_order_num = Db::name('ewm_order')
//            ->where($where)
//            ->where('gema_userid', 'in', $ms_ids)
//            ->where('status',1)
//            ->count();
//
//        $total_amount = Db::name('ewm_order')
//            ->where('gema_userid', 'in', $ms_ids)
//            ->where($where)
//            ->sum('order_price');
//
//        $total_amount_ok = Db::name('ewm_order')
//            ->where('gema_userid', 'in', $ms_ids)
//            ->where($where)
//            ->where('status',1)
//            ->sum('order_price');
//        compact('total_order_num', 'total_ok_order_num', 'total_amount', 'total_amount_ok')
        return json(['code' => CodeEnum::SUCCESS, 'data' => ['total_order_num' => $total_order_num,'total_ok_order_num'=> $total_ok_order_num,'total_amount'=>$total_amount,'total_amount_ok'=>$total_amount_ok,'geri_success_stats'=>$geri_success_stats]]);

    }

    /**
     * @return mixed
     */
    public function index()
    {
        $where = [];
        if (is_admin_login() != 1){
            $where['admin_id'] = is_admin_login();
        }
        $where['status'] = ['neq', '-1'];

        // 获得sum(money)
        $msMoney = Db::name('ms')->where($where)->sum('money');

        $where['work_status'] = 1;
        $where['admin_work_status'] = 1;
        $where['money'] = ['gt',4000];

        // 获取需要的ms记录
        $nolineMs = Db::name('ms')->where($where)->column('userid');

        // 构建查询条件
        $where1 = ['ms_id' => ['in', $nolineMs], 'status' => 1];

        // 获取满足条件的ms_id
        $nolineCodeMsIds = Db::name('ewm_pay_code')->where($where1)->group('ms_id')->column('ms_id');

        // 计算满足条件的ms数量
        $nolineCode = count($nolineCodeMsIds);

        $this->assign('online',$nolineCode);
        $this->assign('msMoney',sprintf("%.2f",$msMoney));
        return $this->fetch();
    }




    /**
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * 修改码商权重
     */

        public function editMsWeight(){
            $userid = $this->request->param('userid');
            $value = $this->request->param('value');
            $ulist = Db::name('ms')->where('userid',$userid)->find();
            $weight_type = $this->request->param('weight_type');
            if (empty($ulist)){
                $this->error('非法操作');
            }
            if (is_admin_login() != 1){
                if (is_admin_login() != $ulist['admin_id']){
                    $this->error('非法操作');
                }
            }
            if($value < 1 || $value > 9999){
                return json(['code'=>404,'msg'=>'权重范围为1-9999']);
            }
            Db::startTrans();
            try {
                if($weight_type === 'team_weight') {
                    $sonMss = $this->getIds($userid);
                    Db::name('ms')->where('userid', 'in', $sonMss)->update(['weight' => $value]);
                }
                $res = Db::name('ms')->where('userid',$userid)->update(['weight'=>$value]);

                Db::commit();
            }catch (\Exception $e){
                return json([
                    'code' => 404,
                    'msg' => 'ERROR'
                ]);
            }

            if($res === false){
                return json([
                    'code' => 404,
                    'msg' => 'ERROR'
                ]);
            }else{
                return json([
                    'code' => 1
                ]);
            }
        }



    /**
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * 修改码商权重
     */

    public function editMsTeamWeight(){
        $userid = $this->request->param('userid');
        $value = $this->request->param('value');
        $ulist = Db::name('ms')->where('userid',$userid)->find();
        if (empty($ulist)){
            $this->error('非法操作');
        }
        if (is_admin_login() != 1){
            if (is_admin_login() != $ulist['admin_id']){
                $this->error('非法操作');
            }
        }
        if($value < 1 || $value > 9999){
            return json(['code'=>404,'msg'=>'权重范围为1-9999']);
        }
//        $res = Db::name('ms')->where('userid',$userid)->update(['weight'=>$value]);
//        if($res === false){
//            return json([
//                'code' => 404,
//                'msg' => 'ERROR'
//            ]);
//        }else{
//            return json([
//                'code' => 1
//            ]);
//        }

        try {
            $res = Db::name('ms')->where('userid',$userid)->update(['weight'=>$value]);
            if ($res === false){
                throw new \Exception('更新团队接单状态失败');
            }
                $this->son_id = [];
                $teams = $this->getIds($userid);
                $teams_work = Db::name('ms')->where('userid','in',$teams)->update(['weight'=>$value]);
                if ($teams_work === false){
                    throw new \Exception('更新团队权重失败');
                }
            return json([
                'code' => 1,
                'msg' => '操作成功'
            ]);

        }catch (\Exception $exception){
            return json([
                'code' => 404,
                'msg' => $exception->getMessage()
            ]);
        }








    }


    /**
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * 修改码商状态
     */
    public function editMsStatus(){
        $userid = $this->request->param('userid');
        $status = $this->request->param('ms_status')==1?0:1;
        $ulist = Db::name('ms')->where('userid',$userid)->find();
        if (empty($ulist)){
            $this->error('非法操作');
        }
        if (is_admin_login() != 1){
            if (is_admin_login() != $ulist['admin_id']){
                $this->error('非法操作');
            }
        }
        $res = Db::name('ms')->where('userid',$userid)->update(['status'=>$status]);
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
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * 修改码商代付状态
     */
    public function editDaifuStatus(){
        $userid = $this->request->param('userid');
        $status = $this->request->param('is_daifu')==1?0:1;
        $ulist = Db::name('ms')->where('userid',$userid)->find();
        if (empty($ulist)){
            $this->error('非法操作');
        }
        if (is_admin_login() != 1){
            if (is_admin_login() != $ulist['admin_id']){
                $this->error('非法操作');
            }
        }
//        if ($status == 1){
//            $team = getTeamPid($userid);
//            foreach ($team as $v){
//                $team_work_status = Db::name('ms')->where('userid',$v)->value('team_work_status');
//                if ($team_work_status == 0){
//                   // $this->error('所属团队未开启接单');
//                    break;
//                }
//                continue;
//            }
//        }
        $res = Db::name('ms')->where('userid',$userid)->update(['is_daifu'=>$status]);
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
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * 修改码商接单状态
     */
    
        public function editMsJdStatus(){
        $userid = $this->request->param('userid');
        $status = $this->request->param('ms_jd_status')==1?0:1;
        // print_r($userid);die;
            $ulist = Db::name('ms')->where('userid',$userid)->find();
            if (empty($ulist)){
                $this->error('非法操作');
            }
            if (is_admin_login() != 1){
                if (is_admin_login() != $ulist['admin_id']){
                    $this->error('非法操作');
                }
            }

            if ($status == 1){
                $team = getTeamPid($userid);
                foreach ($team as $v){
                    $team_work_status = Db::name('ms')->where('userid',$v)->value('team_work_status');
                    if ($team_work_status == 0){
                        return json([
                            'code' => 404,
                            'msg' => '所属团队未开启接单！'
                        ]);
                        break;
                    }
                    continue;
                }
            }

        $res = Db::name('ms')->where('userid',$userid)->update(['work_status'=>$status]);
        if($res === false){
        return json([
                'code' => 404
                    ]);
        }else{
            if ($status == 1){
                $reason = '开启';
            }else{
                $reason = '关闭';
            }

            action_log('管理员操作码商开工','管理员'.session('admin_info')['username'].'给码商：'.$ulist['username'].$reason.'接单状态');
            $where['status'] = 1;
            if (is_admin_login() != 1){
                $where['admin_id'] = is_admin_login();
            }
            $where['work_status'] = 1;
            $where['money'] = ['GT',0];
            $nolineMs = Db::name('ms')->where($where)->select();
            $where1['status'] = 1;
//            $where1['ms_id'] = ['in',$nolineMs];
//            $nolineCode = Db::name('ewm_pay_code')->where($where1)->count();
            foreach ($nolineMs as $k=>$v){
                $nolineCode = Db::name('ewm_pay_code')->where('ms_id',$v['userid'])->where($where1)->count();
                if ($nolineCode == 0){
                    unset($nolineMs[$k]);
                }
            }
            $nolineCode = count($nolineMs);
         return json([
                'code' => 1,
                'data' => $nolineCode
                ]);
        }
    }


    /**
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * 修改团队接单状态
     */
    public function editTeamStatus(){
        $userid = $this->request->param('userid');
        $status = $this->request->param('team_status')==1?0:1;
        $ulist = Db::name('ms')->where('userid',$userid)->find();
        if (empty($ulist)){
            $this->error('非法操作');
        }
        if (is_admin_login() != 1){
            if (is_admin_login() != $ulist['admin_id']){
                $this->error('非法操作');
            }
        }
        try {
            $res = Db::name('ms')->where('userid',$userid)->update(['team_work_status'=>$status]);
            if ($res === false){
                throw new \Exception('更新团队接单状态失败');
            }

            if ($status == 0){
                $this->son_id = [];
                $teams = $this->getIds($userid);
                $teams_work = Db::name('ms')->where('userid','in',$teams)->update(['work_status'=>0]);
                if ($teams_work === false){
                    throw new \Exception('更新团队接单状态失败');
                }
            }

            return json([
                'code' => 1,
                'msg' => '操作成功'
            ]);

        }catch (\Exception $exception){
            return json([
                'code' => 404,
                'msg' => $exception->getMessage()
            ]);
        }
    }

    /**
     * 更新码子状态
     *
     */
    public function disactiveCode(){
        $coid = $this->request->param('coid');
        $status = $this->request->param('status')==1?0:1;
        // print_r($userid);die;
        $res = Db::name('ewm_pay_code')->where('id',$coid)->update(['status'=>$status]);
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
     * @user luomu
     * @return
     * @time
     *
     */

    public function subnotify()
    {
        $order = Db::name('ewm_order')->where('id',$this->request->param('order_id'))->find();
        if (is_admin_login() != 1){
            $adminSonMs = Db::name('ms')->where('admin_id',is_admin_login())->column('userid');
            if (!in_array($order['gema_userid'],$adminSonMs)){
                return $this->error('非法请求');
            }
        }

        $order_id = Db::name('orders')->where('trade_no',$order['order_no'])->value('id');
        $this->result($this->logicOrders->pushOrderNotifyV2($order_id));
    }



    /**
     * 统计金额根据筛选条件变动 旧
     */

    public function searchMsOrderMoney_old(){

        $code_type = $this->request->param('code_type');

        $where['e.code_type'] = $code_type;
        $where['e.add_time'] = $this->parseRequestDate3();
        if (trim($this->request->param('start')) != '' && trim($this->request->param('start')) != ''){
            $startTime = $this->request->param('start');
            $endTime = $this->request->param('end');
            $where['e.add_time'] = ['between time',[$startTime,$endTime]];
        }
        if (trim($this->request->param('order_no')) != ''){
            unset($where['create_time']);
            $where['e.order_no'] = trim($this->request->param('order_no'));
        }

        if (trim($this->request->param('username')) != ''){
            $where['e.gema_username'] = trim($this->request->param('username'));
        }

        if (trim($this->request->param('uid')) != ''){
            $where['o.uid'] = trim($this->request->param('uid'));
        }



        if (trim($this->request->param('pay_username')) != ''){
            $where['e.pay_username'] = trim($this->request->param('pay_username'));
        }

        if (trim($this->request->param('pay_user_name')) != ''){
            $where['e.pay_user_name'] = trim($this->request->param('pay_user_name'));
        }

        if (trim($this->request->param('status')) != ''){
            $where['e.status'] = $this->request->param('status');
        }
        !empty($this->request->param('order_pay_price')) && $where['a.order_pay_price']
            = ['eq', $this->request->param('order_pay_price')];

        if (is_admin_login() != 1){
            $where['u.admin_id'] = is_admin_login();
//            $adminsonuser = Db::name('user')->where('admin_id',is_admin_login())->column('uid');
//            $where['o.uid'] = ['in',$adminsonuser];
        }

        $fees['total'] = Db::name('ewm_order')
            ->alias('e')
            ->join('orders o','e.order_no = o.trade_no','left')
            ->join('user u','o.uid = u.uid')
            ->where($where)
            ->sum('e.order_price');


        $fees['paid'] = Db::name('ewm_order')
            ->alias('e')
            ->join('orders o','e.order_no = o.trade_no','left')
            ->join('user u','o.uid = u.uid')
            ->where($where)
            ->where('e.status',1)
            ->sum('e.order_price');


        $fees['total_num'] = Db::name('ewm_order')
            ->alias('e')
            ->join('orders o','e.order_no = o.trade_no','left')
            ->join('user u','o.uid = u.uid')
            ->where($where)
            ->count();

        $fees['success_num'] = Db::name('ewm_order')
            ->alias('e')
            ->join('orders o','e.order_no = o.trade_no','left')
            ->join('user u','o.uid = u.uid')
            ->where($where)
            ->where('e.status',1)
            ->count();
        if ($fees['success_num'] == 0 ){
            $fees['success_rate'] = 0;
        } else{
            $fees['success_rate'] = sprintf("%.2f",$fees['success_num']/$fees['total_num']*100);
        }

        return json($fees);
    }

    /**
     * 统计金额根据筛选条件变动 新
     */

    public function searchMsOrderMoney(){
        return $this->searchMsUidOrOrderMoney('kzk');
    }

    /**
     * 统计金额根据筛选条件变动 新
     */
    public function searchMsUidOrOrderMoney($type){
        
        $where['o.update_time'] = $this->parseRequestDate3();
//        if ($this->request->param('time_type') != ''){
//            if ($this->request->param('time_type') == 1){
//                $where['e.add_time'] = $this->parseRequestDate3();
//            }else{
//                $where['e.pay_time'] = $this->parseRequestDate3();
//            }
//        }
//        if (trim($this->request->param('start')) != '' && trim($this->request->param('start')) != ''){
//            $startTime = $this->request->param('start');
//            $endTime = $this->request->param('end');
//            $where['e.add_time'] = ['between time',[$startTime,$endTime]];
//        }
//
//        if (trim($this->request->param('start_update')) != '' && trim($this->request->param('end_update')) != ''){
//            unset( $where['e.add_time']);
//            $startUpdateTime = $this->request->param('start_update');
//            $endUpdateTime = $this->request->param('end_update');
//            $where['e.pay_time'] = ['between time',[$startUpdateTime,$endUpdateTime]];
//        }


        if (trim($this->request->param('id')) != ''){
            $orderid = trim($this->request->param('id')) - 20000000;
            $where['e.id'] = $orderid;
        }
        if (trim($this->request->param('order_no')) != ''){
            unset($where['o.update_time']);
            $where['e.order_no'] = ['=', trim($this->request->param('order_no'))];
        }

        if (trim($this->request->param('username')) != ''){
            $where['e.gema_username'] = ['=', trim($this->request->param('username'))];
        }

        if (trim($this->request->param('uid')) != ''){
            $where['o.uid'] = ['=', trim($this->request->param('uid'))];
        }
        
        if (trim($this->request->param('pay_username')) != ''){
            $where['e.pay_username'] = ['=', trim($this->request->param('pay_username'))];
        }

        if (trim($this->request->param('pay_user_name')) != ''){
            $where['e.pay_user_name'] = ['=', trim($this->request->param('pay_user_name'))];
        }

        if (trim($this->request->param('cardKey')) != ''){
            $where['e.cardKey'] = ['=', trim($this->request->param('cardKey'))];
        }

        if (trim($this->request->param('status')) != ''){
            $where['e.status'] = ['=', $this->request->param('status')];
        }
        !empty($this->request->param('order_pay_price')) && $where['order_pay_price']
            = ['eq', $this->request->param('order_pay_price')];

        !empty($this->request->param('visite_ip')) && $where['visite_ip']
            = ['eq', $this->request->param('visite_ip')];

        if (is_admin_login() != 1){
            $where['u.admin_id'] = is_admin_login();
        }

        if (trim($this->request->param('admin_username')) != ''){
            $admin_id = Db::name('admin')->where('username',$this->request->param('admin_username'))->value('id');
            $where['e.admin_id'] = $admin_id;
        }
        if (trim($this->request->param('agent_name')) != ''){
            if (is_admin_login() != 1){
                $admin_id = Db::name('ms')->where('username',$this->request->param('agent_name'))->value('admin_id');
                if ($admin_id != is_admin_login()){
                    return json(['code'=>0,'msg'=>'非法请求']);
                }
            }
            $agent_id = Db::name('ms')->where('username',$this->request->param('agent_name'))->value('userid');
            $this->son_id = [];
            $agentids = $this->getIds($agent_id);
            array_push($agentids,$agent_id);
            $where['e.gema_userid'] = ['in', $agentids];
        }

        if ($this->request->param('is_status') != "") {
            $is_status = $this->request->param('is_status');
            if($is_status =='0'){
                $where['n.is_status'] = ['neq', '200'];
            }elseif($is_status == '-1') {
                $where['n.is_status'] = ['eq', ' '];
            }else{
                $where['n.is_status'] = ['eq', $this->request->param('is_status')];
            }
        }

        if (trim($this->request->param('note')) != ''){
            $where['e.note']
                = ['like', '%'.$this->request->param('note').'%'];
        }
        $code_type = $this->request->param('code_type');
        
        
        $query = Db::name('ewm_order');


        
        if ($type == 'uidOrders') {
             $where['p.code'] = ['in',['alipayUid','alipayUidSmall','alipayUidTransfer']];
//            $query = $query->alias('e')->join('pay_code p','e.code_type = p.id')
//                ->where('p.code', 'in', ['alipayUid','alipayUidSmall']);
            $code_type && $where['p.code'] = $code_type;
        }else{
              $where['p.code'] = $type;
                $code_type && $where['e.code_type'] = ['=', $code_type];
        }
//        $fees['total']

//        $orderStats = $query->alias('e')
//            ->join('orders o','e.order_no = o.trade_no','left')
//            ->join('user u','o.uid = u.uid')
//            ->join('pay_code p','e.code_type = p.id')
//            ->join( 'orders_notify n', 'n.order_id = o.id')
//            ->where($where)
//            ->field('sum(e.order_price) as today_money,count(e.id) as today_number')->find();
//            ->sum('e.order_price');

//        $fees['paid']
        $successOrderStats = $query->alias('e')
            ->join('orders o','e.order_no = o.trade_no','left')
            ->join('user u','o.uid = u.uid')
            ->join('pay_code p','e.code_type = p.id')
            ->join( 'orders_notify n', 'n.order_id = o.id')
            ->where($where)
            ->where('e.status',1)
            ->field('sum(e.order_price) as today_money,count(e.id) as today_number')->find();



        $fees['total_num'] = $query->alias('e')
            ->join('orders o','e.order_no = o.trade_no','left')
            ->join('user u','o.uid = u.uid', 'left')
            ->join('pay_code p','e.code_type = p.id', 'right')
            ->where($where)
            ->count();


        $fees['total'] = $query->alias('e')
            ->join('orders o','e.order_no = o.trade_no','left')
            ->join('user u','o.uid = u.uid', 'left')
            ->join('pay_code p','e.code_type = p.id', 'right')
            ->where($where)
            ->sum('order_price');

        //$fees['total'] = (float)$orderStats['today_money'];
        $fees['paid'] = (float)$successOrderStats['today_money'];




//        $fees['total_num'] = (float)$orderStats['today_number'];
        $fees['success_num'] = (float)$successOrderStats['today_number'];

//        $fees['success_num'] = $query->alias('e')
//            ->join('orders o','e.order_no = o.trade_no','left')
//            ->join('user u','o.uid = u.uid')
//            ->join('pay_code p','e.code_type = p.id')
//            ->join( 'orders_notify n', 'n.order_id = o.id')
//            ->where($where)
//            ->where('e.status',1)
//            ->count();
        if ($fees['success_num'] == 0 ){
            $fees['success_rate'] = 0;
        } else{
            $fees['success_rate'] = sprintf("%.2f",$fees['success_num']/$fees['total_num']*100);
        }


        return json($fees);
    }


    public function searchMsUidOrderMoney(){
        return $this->searchMsUidOrOrderMoney('uidOrders');
    }

    public function searchMsAlipayCodeOrderMoney(){
        return $this->searchMsUidOrOrderMoney('alipayCode');
    }

    public function searchMsQqCodeOrderMoney(){
        return $this->searchMsUidOrOrderMoney('QqCode');
    }

    public function searchMswechatCodeOrderMoney(){
        return $this->searchMsUidOrOrderMoney('wechatCode');
    }

    public function searchMswechatGoldRedOrderMoney(){
        return $this->searchMsUidOrOrderMoney('wechatGoldRed');
    }

    public function searchMsDingDingGroupOrderMoney(){
        return $this->searchMsUidOrOrderMoney('DingDingGroup');
    }


    public function searchMsDouyinGroupRedOrderMoney(){
        return $this->searchMsUidOrOrderMoney('douyinGroupRed');
    }

    public function searchMsWechatGroupRedOrderMoney(){
        return $this->searchMsUidOrOrderMoney('wechatGroupRed');
    }
    public function searchMsCnyNumberOrderMoney(){
        return $this->searchMsUidOrOrderMoney('CnyNumber');
    }
    public function searchMsAppletProductsOrderMoney(){
        return $this->searchMsUidOrOrderMoney('AppletProducts');
    }

    public function searchMsQQFaceRedOrderMoney(){
        return $this->searchMsUidOrOrderMoney('QQFaceRed');
    }

    public function searchMsJDECardOrderMoney(){
        return $this->searchMsUidOrOrderMoney('JDECard');
    }

    public function searchMstaoBaoDirectPayOrderMoney(){
        return $this->searchMsUidOrOrderMoney('taoBaoDirectPay');
    }

    public function searchMsalipayF2FOrderMoney(){
        return $this->searchMsUidOrOrderMoney('alipayF2F');
    }

    public function searchMsalipayPinMoneyOrderMoney(){
        return $this->searchMsUidOrOrderMoney('alipayPinMoney');
    }


    public function searchMsalipayWapOrderMoney(){
        return $this->searchMsUidOrOrderMoney('alipayWap');
    }

    public function searchMsHuiYuanYiFuKaOrderMoney(){
        return $this->searchMsUidOrOrderMoney('HuiYuanYiFuKa');
    }

    public function searchMsLeFuTianHongKaOrderMoney(){
        return $this->searchMsUidOrOrderMoney('LeFuTianHongKa');
    }

    public function searchMsJunWebOrderMoney(){
        return $this->searchMsUidOrOrderMoney('JunWeb');
    }

    public function searchMsusdtTrcOrderMoney(){
        return $this->searchMsUidOrOrderMoney('usdtTrc');
    }

    public function searchMsalipaySmallPurseOrderMoney(){
        return $this->searchMsUidOrOrderMoney('alipaySmallPurse');
    }

    public function searchMstaoBaoMoneyRedOrderMoney(){
        return $this->searchMsUidOrOrderMoney('taoBaoMoneyRed');
    }


    public function searchMsalipayTransferCodeOrderMoney(){
        return $this->searchMsUidOrOrderMoney('alipayTransferCode');
    }

    public function searchMsCnyNumberAutoOrderMoney(){
        return $this->searchMsUidOrOrderMoney('CnyNumberAuto');
    }


    public function searchMsQianxinTransferOrderMoney(){
        return $this->searchMsUidOrOrderMoney('QianxinTransfer');
    }


    public function searchMstaoBaoEcardOrderMoney(){
        return $this->searchMsUidOrOrderMoney('taoBaoEcard');
    }


    public function searchMsalipayCodeSmallOrderMoney(){
        return $this->searchMsUidOrOrderMoney('alipayCodeSmall');
    }


    public function searchMsalipayPassRedOrderMoney(){
        return $this->searchMsUidOrOrderMoney('alipayPassRed');
    }


    public function searchMsalipayWorkCardOrderMoney(){
        return $this->searchMsUidOrOrderMoney('alipayWorkCard');
    }

    public function searchMsalipayWorkCardBigOrderMoney(){
        return $this->searchMsUidOrOrderMoney('alipayWorkCardBig');
    }


    public function searchMsAggregateCodeOrderMoney(){
        return $this->searchMsUidOrOrderMoney('AggregateCode');
    }

    public function searchMsalipayTransferOrderMoney(){
        return $this->searchMsUidOrOrderMoney('alipayTransfer');
    }


    /**
     * 获取商户列表
     */
    public function getmslist()
    {

        $where = [];
        !empty($this->request->param('userid')) && $where['a.userid']
            = $this->request->param('userid');
        !empty($this->request->param('username')) && $where['a.username']
            = ['like', '%' . $this->request->param('username') . '%'];
        !empty($this->request->param('admin_username')) && $where['m.username']
            = ['like', '%' . $this->request->param('admin_username') . '%'];

        if (!empty($this->request->param('agent_name'))){
            if (is_admin_login() != 1){
                $admin_id =  Db::name('ms')->where('username',$this->request->param('agent_name'))->value('admin_id');
                if ($admin_id != is_admin_login()){
                    return json(['code'=>0,'msg'=>'非法请求']);
                }
            }
            $agent_id = Db::name('ms')->where('username',$this->request->param('agent_name'))->value('userid');
            $this->son_id = [];
            $sonIds = $this->getIds($agent_id);
            array_push($sonIds,$agent_id);

            $where['a.userid'] = ['in',$sonIds];

        }
        if (is_admin_login() != 1){
            $where['a.admin_id'] = is_admin_login();

        }
        $where['a.status'] =['neq',-1];

        if ($this->request->param('work_status') != '' && $this->request->param('work_status') != '-1'){
            $where['a.work_status'] = $this->request->param('work_status');

        }
        if ($this->request->param('jd_status') != '' && $this->request->param('jd_status') != '-1'){
            $where['a.work_status'] = 1;
            $where['a.admin_work_status'] = 1;
            $where['a.money'] = ['gt',4000];

        }
//        $data = $this->logicMs->getMsList($where, true, 'reg_date desc', false);

        $admin_view_level1_ms = getAdminPayCodeSys('admin_view_level1_ms',256,is_admin_login());
        if (empty($admin_view_level1_ms)){
            $admin_view_level1_ms = 2;
        }

        if ($admin_view_level1_ms == 1){
            $where['a.level'] = 1;
        }

        $data = $this->modelMs->alias('a')
            ->join('admin m', 'm.id = a.admin_id')
            ->where($where)
            ->field('a.*,m.username as admin_username,a.weight as team_weight')
           ->paginate($this->request->param('limit', 15));

        foreach ($data as $k=>$v){
            $data[$k]['p_username'] = Db::name('ms')->where('userid',$v['pid'])->value('username');
        }
//        $count = $this->logicMs->getMsCount($where);

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
            ]);
    }





    /**
     * 获取商户列表
     */
    public function exportMsList()
    {

        $where = [];
        !empty($this->request->param('username')) && $where['a.username']
            = ['like', '%' . $this->request->param('username') . '%'];
        !empty($this->request->param('admin_username')) && $where['m.username']
            = ['like', '%' . $this->request->param('admin_username') . '%'];

        if (!empty($this->request->param('agent_name'))){
            if (is_admin_login() != 1){
                $admin_id =  Db::name('ms')->where('username',$this->request->param('agent_name'))->value('admin_id');
                if ($admin_id != is_admin_login()){
                    return json(['code'=>0,'msg'=>'非法请求']);
                }
            }
            $agent_id = Db::name('ms')->where('username',$this->request->param('agent_name'))->value('userid');
            $this->son_id = [];
            $sonIds = $this->getIds($agent_id);
            array_push($sonIds,$agent_id);

            $where['a.userid'] = ['in',$sonIds];

        }
        if (is_admin_login() != 1){
            $where['a.admin_id'] = is_admin_login();

        }
        $where['a.status'] =['neq',-1];

        if ($this->request->param('work_status') != '' && $this->request->param('work_status') != '-1'){
            $where['a.work_status'] = $this->request->param('work_status');

        }
        if ($this->request->param('jd_status') != '' && $this->request->param('jd_status') != '-1'){
            $where['a.work_status'] = 1;
            $where['a.admin_work_status'] = 1;
            $where['a.money'] = ['gt',4000];

        }
//        $data = $this->logicMs->getMsList($where, true, 'reg_date desc', false);

        $admin_view_level1_ms = getAdminPayCodeSys('admin_view_level1_ms',256,is_admin_login());
        if (empty($admin_view_level1_ms)){
            $admin_view_level1_ms = 2;
        }

        if ($admin_view_level1_ms == 1){
            $where['a.level'] = 1;
        }

        $orderList = $this->modelMs->alias('a')
            ->join('admin m', 'm.id = a.admin_id')
            ->where($where)
            ->field('a.*,m.username as admin_username,a.weight as team_weight')
            ->select();

        foreach ($orderList as $k=>$v){
            $orderList[$k]['p_username'] = Db::name('ms')->where('userid',$v['pid'])->value('username');
        }
//        $count = $this->logicMs->getMsCount($where);

        $strTable ='<table width="500" border="1">';
        $strTable .= '<tr>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">ID标识</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">码商名称</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">余额</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">押金</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">上级代理</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">权重</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">开工状态</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">接单权限</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">团队接单权限</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">代付状态</td>';
//        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">状态</td>';
        $strTable .= '</tr>';
        if(is_array($orderList)){
            foreach($orderList as $k=>$val){
                $val['work_status'] = ($val['work_status'] == 0) ? "关闭" : "开启";
                $val['admin_work_status'] = ($val['admin_work_status'] == 0) ? "关闭" : "开启";
                $val['team_work_status'] = ($val['team_work_status'] == 0) ? "关闭" : "开启";
                $val['is_daifu'] = ($val['is_daifu'] == 0) ? "关闭" : "开启";
                $strTable .= '<tr>';
                $strTable .= '<td style="text-align:center;font-size:12px;">&nbsp;'.$val['userid'].'</td>';
                $strTable .= '<td style="text-align:center;font-size:12px;">&nbsp;'.$val['username'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">&nbsp;' . $val['money'] . ' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['cash_pledge'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['p_username'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['weight'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">&nbsp;' . $val['work_status'] . ' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['admin_work_status'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['team_work_status'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['is_daifu'].'</td>';
//                $strTable .= '<td style="text-align:left;font-size:12px;">'.$orderStatus[$val['status']].'</td>';
                $strTable .= '</tr>';
                unset($orderList[$k]);
            }
        }
        $strTable .='</table>';
        downloadExcel($strTable,'msList');
    }













    public function searchMsMoney(){
//        $where['a.status'] = 1;
        $where = [];
        if (is_admin_login() != 1){
            $where['a.admin_id'] = is_admin_login();
        }
//        !empty($this->request->param('username')) && $where['a.username']
//            = ['like', '%' . $this->request->param('username') . '%'];

        if (!empty($this->request->param('username'))){
            $admin_id =  Db::name('ms')->where('username',$this->request->param('username'))->value('admin_id');
            if ($admin_id != is_admin_login()){
                return json(['code'=>0,'msg'=>'非法请求']);
            }
            $where['a.username'] = ['like', '%' . $this->request->param('username') . '%'];
        }

        !empty($this->request->param('admin_username')) && $where['m.username']
            = ['like', '%' . $this->request->param('admin_username') . '%'];
        $where['a.status'] = ['neq','-1'];
        if ($this->request->param('work_status') != '' && $this->request->param('work_status') != '-1'){
            $where['a.work_status'] = $this->request->param('work_status');

        }

        if (!empty($this->request->param('agent_name'))){
            if (is_admin_login() != 1){
                $admin_id =  Db::name('ms')->where('username',$this->request->param('agent_name'))->value('admin_id');
                if ($admin_id != is_admin_login()){
                    return json(['code'=>0,'msg'=>'非法请求']);
                }
            }
            $agent_id = Db::name('ms')->where('username',$this->request->param('agent_name'))->value('userid');
            $this->son_id = [];
            $sonIds = $this->getIds($agent_id);
            array_push($sonIds,$agent_id);

            $where['a.userid'] = ['in',$sonIds];

        }
        $msMoney = Db::name('ms')->alias('a')
            ->join('admin m', 'm.id = a.admin_id')->where($where)->sum('a.money');

        $where['a.work_status'] = 1;
//        $where['a.work_status'] =1;
        $where['a.admin_work_status'] = 1;
        $where['a.money'] = ['gt',4000];
      //  $nolineMs = Db::name('ms')->alias('a') ->join('admin m', 'm.id = a.admin_id')->where($where)->select();

//        foreach ($nolineMs as $k=>$v){
//            $nolineCode = Db::name('ewm_pay_code')->where('ms_id',$v['userid'])->where(['status' => 1])->count();
//            if ($nolineCode == 0){
//                unset($nolineMs[$k]);
//            }
//        }


        // 获取需要的ms记录
        $nolineMs = Db::name('ms')->alias('a') ->join('admin m', 'm.id = a.admin_id')->where($where)->column('userid');

        // 构建查询条件
        $where1 = ['ms_id' => ['in', $nolineMs], 'status' => 1,'is_delete'=>0];

        // 获取满足条件的ms_id
        $nolineCodeMsIds = Db::name('ewm_pay_code')->where($where1)->group('ms_id')->column('ms_id');

        // 计算满足条件的ms数量
        $nolineCode = count($nolineCodeMsIds);



        $data = [
            'msMoney'   => sprintf("%.2f",$msMoney),
            'onlineMs' => count($nolineCode)
        ];

        $this->result(['code'=>CodeEnum::SUCCESS,'data'=>$data]);
    }

    /**
     * 设置码商费率
     */

    public function assign_channels(){
        if ($this->request->isPost()){
            $data = $this->request->post('r/a');
            if (is_array($data)){
                foreach ($data as  $key=>$item) {
                    if (is_admin_login() != 1){
                        $admin_id =  Db::name('ms')->where('userid',$item['ms_id'])->value('admin_id');
                        if ($admin_id != is_admin_login()){
                            return json(['code'=>0,'msg'=>'非法请求']);
                        }
                    }
                    if ($item['ms_rate'] < 0){
                        return ['code' => CodeEnum::ERROR, 'msg'=>'费率最低为0'];
                    }

                    $mspid = Db::name('ms')->where('userid',$item['ms_id'])->where('status','neq','-1')->value('pid');
                    if ($mspid > 0){
                            $parRate = Db::name('ms_rate')->where(['ms_id'=>$mspid,'code_type_id'=>$item['code_type_id']])->value('rate');
                            if ($item['ms_rate'] > $parRate){
                                return ['code' => CodeEnum::ERROR, 'msg'=>'费率不可高于其上级费率'];
                            }
                    }

                    $msSons = Db::name('ms')->where('pid', $item['ms_id'])->where('status','neq','-1')->select();
                    foreach ($msSons as $msSon){
                        $sonRate = Db::name('ms_rate')->where(['ms_id'=>$msSon['userid'],'code_type_id'=>$item['code_type_id']])->value('rate');
                        if ($item['ms_rate'] < $sonRate){
                            return ['code' => CodeEnum::ERROR, 'msg'=>'费率不可低于下级：【'.$msSon['username'].'】的费率'];
                        }
                    }

                    $res = Db::name('ms_rate')->where(['ms_id'=>$item['ms_id'],'code_type_id'=>$item['code_type_id']])->select();
                    if ($res){
                        //修改
                        Db::name('ms_rate')->where(['ms_id'=>$item['ms_id'],'code_type_id'=>$item['code_type_id']])->update(['rate' => $item['ms_rate'], 'update_time' => time()]);
                    }else{
                        //新增
                        Db::name('ms_rate')->insert( [
                            'ms_id' => $item['ms_id'],
                            'code_type_id' => $item['code_type_id'],
                            'rate' => $item['ms_rate'],
                            'create_time' => time(),
                            'update_time' => time(),
                        ]);
                    }

                }
                return ['code' => CodeEnum::SUCCESS, 'msg' => '费率配置成功'];
            }
        }

//        $msRate = $this->modelMsRate->where('ms_id',$this->request->param('id'))->select();
//        $sonMsid = $this->request->param('userid');
        $sonMs = Db::name('ms')->where('userid',$this->request->param('id'))->find();
        if (empty($sonMs)){
            $this->error('什么都没有');
        }

        //所有渠道列表
//        $list = $this->logicPay->getChannelList(['status' => '1'], true, 'create_time desc', false);
        $list = Db::name('pay_code')->where('status',1)->select();
//                print_r($list);die;
        /*        $channel_array = [];
                foreach ($list as $k => $v) {
                    $channel_array[] = $v['id'];
                }*/
        //所有渠道列表
//        $channel = $this->logicPay->getAccountList(['cnl_id' => ['in', $channel_array]], true, 'create_time desc', false);

        //查询自己的费率

        $myRate = Db::name('ms_rate')->where('ms_id',$this->request->param('id'))->select();

        //查询当前下级的费率
//        $sonRate = Db::name('ms_rate')->where('ms_id',$sonMs['userid'])->select();


        if ($myRate){
		foreach ($list as $k=>$v){
		 $list[$k]['ms_rate'] = 0;
                foreach ($myRate as $key=>$val){
                    if ($v['id'] == $val['code_type_id']){
                        $list[$k]['ms_rate'] = sprintf("%.2f",$val['rate']);
                    }
                }
            }
        }else{
            foreach ($list as $key=>$val){
                $list[$key]['ms_rate'] = 0;
            }
        }


        $this->assign('list', $list);
        //print_r($list);die;
        return $this->fetch();
    }


    /**
     *
     *
     * @return mixed
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function add()
    {
        // post 是提交数据
        $this->request->isPost() && $this->result($this->logicMs->addMs($this->request->post(),0,1,is_admin_login()));
        $where = [];
        $where['status'] = 1;
        if (is_admin_login() != 1){
            $where['admin_id'] = is_admin_login();
        }

        $ms = Db::name('ms')
                    ->where($where)
                    ->field('userid,username')
                    ->select();
        $this->assign('ms',$ms);
        return $this->fetch();
    }

    /**
     *
     *编辑码商
     * @return mixed
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function edit(Request $request)
    {
        $userid = trim($request->param('userid'));
        if (is_admin_login() != 1){
            $admin_id =  Db::name('ms')->where('userid',$userid)->value('admin_id');
            if ($admin_id != is_admin_login()){
                return json(['code'=>0,'msg'=>'非法请求']);
            }
        }
        if (!$userid) {
            $this->error('参数错误');
        }
        $ulist = Db::name('ms')->where(array('userid' => $userid))->find();
        if (!$ulist) {
            $this->error('会员不存在');
        }


        if (is_admin_login() != 1){
            if (is_admin_login() != $ulist['admin_id']){
                $this->error('非法操作');
            }
        }

        if ($request->isPost()) {
//            print_r($request->param());die;
            if ($request->param('cash_pledge') < 0){
                $this->error('押金最小为0');
            }
            $data['username'] = trim($request->param('username'));
            $data['mobile'] = trim($request->param('mobile'));
            $data['status'] = $request->param('status');
            $data['is_daifu'] = $request->param('is_daifu');
            $data['cash_pledge'] = sprintf("%.2f",$request->param('cash_pledge'));
            $data['min_money'] = sprintf("%.2f",$request->param('min_money'));
            $data['max_money'] = sprintf("%.2f",$request->param('max_money'));
//            $data['pid'] = $request->param('pid', '');

//            if( !empty($request->param('pid', '')) ){
//                $data['pid'] = $request->param('pid');
//                $data['level'] = $this->modelMs->where(['userid' => $data['pid']])->value('level') + 1;
//            }

            $login_pwd = trim($request->param('login_pwd', ''));
            $relogin_pwd = trim($request->param('relogin_pwd', ''));
            if ($login_pwd && $login_pwd != $relogin_pwd) {
                $this->error('修改密码时,两次密码不一致');
            }

            if ($login_pwd) {
                $data['login_pwd'] = pwdMd52($login_pwd, $ulist['login_salt']);
            }

            $safety_pwd = trim($request->param('safety_pwd', ''));


            //安全密码
            if ($safety_pwd) {
                $data['security_pwd'] = pwdMd5($safety_pwd, $ulist['security_salt']); //safety_salt
            }


            $auth_ips = $this->request->param('auth_ips');
            $auth_ips = array_filter(explode("\r\n", $auth_ips));
            $tempIps = [];
            foreach ($auth_ips as $ip) {
                $ip = trim($ip);
                if (empty($ip)) {
                    continue;
                }
                if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                    $this->error('ip格式填写错误');
                    die();
                }
                $tempIps[] = $ip;
            }


            $monitor_ips = $this->request->param('monitor_ip');
            $monitor_ips = array_filter(explode(",", $monitor_ips));
//            $this->error('ip格式填写错误:'.json_encode($monitor_ips,true));
            $monitor_ip = [];

            foreach ($monitor_ips as $ip) {
                $ip = trim($ip);
                if (empty($ip)) {
                    continue;
                }
                if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                    $this->error('ip格式填写错误');
                    die();
                }
                $monitor_ip[] = $ip;
            }
            $data['monitor_ip'] = trim(implode(',', $monitor_ip));
            $data['auth_ips'] = trim(implode(',', $tempIps));
            $data['updatetime'] = time();
            $data['bank_rate'] = request()->param('bank_rate', 0);
            $data['deposit_floating_money'] = request()->param('deposit_floating_money', 0.00);

            //判断码商代付费率不能高于上级代理，不能小于下级代理
            if ($data['bank_rate']){
                $my_ms = Db::name('ms')->where(array('userid' => $userid))->find();
                if ($my_ms['pid'] > 0){
                    $fa_ms = Db::name('ms')->where(array('userid' => $my_ms['pid']))->find();
                    if ($data['bank_rate'] >$fa_ms['bank_rate'] ){
                        $this->error('代付费率设置不能高于上级代理！');
                    }
                    $son_mss = Db::name('ms')->where(array('pid' => $my_ms['userid']))->select();
                    if ($son_mss){
                        foreach ($son_mss as $son_ms){
                            if ($data['bank_rate']<$son_ms['bank_rate']){
                                $this->error('代付费率设置不能小于下级代理！');
                            }
                        }
                    }
                }
            }

            $re = Db::name('ms')->where(array('userid' => $userid))->update($data);
            if ($re) {
                $this->success('资料修改成功');
            } else {
                $this->error('资料修改失败');
            }
        } else {
            $ms = $this->modelMs->where(['status' => 1,'admin_id' => is_admin_login()])->field('userid,username')->select();
            $this->assign('ms', $ms);
            $this->assign('info', $ulist);
            return $this->fetch();
        }
    }

    /**
     * 删除码商
     */
    public function del(Request $request)
    {
        $userid = trim($request->param('userid'));
        $msInfo =  Db::name('ms')->where('userid',$userid)->find();
        if (is_admin_login() != 1){

            if ($msInfo['admin_id'] != is_admin_login()){
                return json(['code'=>0,'msg'=>'非法请求']);
            }
        }
        //判断是否有下级会员
        $pUser = Db::name('ms')->where(['pid' => $userid, 'status' => ['neq', '-1']])->select();
        if ($pUser) {
            $this->error('会员有下级，不能删除');
        }

        action_log('删除码商','管理员'.session('admin_info')['username'].'删除码商：'.$msInfo['username'].'，余额剩余：'.sprintf("%.2f",$msInfo['money']));
        Db::name('ms')->where(array('userid' => $userid))->delete();
        $this->success('会员删除成功');
    }



    public function orders()
    {
        return $this->ordersOrUidOrder('kzk');
        
    }

    /*
     * 码商订单列表 和 uid 订单列表
     */
    protected function ordersOrUidOrder($type)
    {
        $where['o.update_time'] = $this->parseRequestDate3();
        if (is_admin_login() != 1){
//            $adminSonMs = Db::name('ms')->where('admin_id',is_admin_login())->column('userid');
//            $where['a.gema_userid'] = ['in',$adminSonMs];
//            $adminSonUser = Db::name('user')->where('admin_id',is_admin_login())->column('uid');
//            $where['o.uid'] = ['in',$adminSonUser];
            $where['eo.admin_id'] = is_admin_login();
        }
        
//        $query = Db::name('ewm_order');

        if ($type == 'uidOrder') {
            $pay_code_id = Db::name('pay_code')->where('code','in',['alipayUid','alipayUidSmall','alipayUidTransfer'])->column('id');
//            $where['b.code'] = ['in',['alipayUid','alipayUidSmall','alipayUidTransfer']];
            $where['eo.code_type'] = ['in',$pay_code_id];
        }else{
            $pay_code_id = Db::name('pay_code')->where('code',$type)->value('id');
            $where['eo.code_type'] = $pay_code_id;
        }
        $fees = [];

        $order_stats = Db::name('ewm_order')->alias('eo')->join('orders o','eo.order_no = o.trade_no')->where($where)->field('sum(eo.order_price) as total_money,count(eo.id) as total_count')->find();
        $fees['total'] = (float)$order_stats['total_money'];
        $fees['total_num'] = (float)$order_stats['total_count'];
//        $fees['total_num'] = $query->alias('a')->join('pay_code b','b.id = a.code_type')->join('orders o','a.order_no = o.trade_no')->where($where)->count();

//        $order_stats = $GemapayOrderModel->whereTime('add_time', 'today')->where('gema_userid', 'in', $ms_ids)->where($where)->field('sum(order_price) as total_money,count(id) as total_count')->find();
//        $order_success_stats = $GemapayOrderModel->whereTime('add_time', 'today')->where('status',1)->where('gema_userid', 'in', $ms_ids)->where($where)->field('sum(order_price) as total_money,count(id) as total_count')->find();

//
//        $total_order_num = (float)$order_stats['total_count'];
//
//        $total_ok_order_num = (float)$order_success_stats['total_count'];
//
//        $total_amount = (float)$order_stats['total_money'];
//
//        $total_amount_ok = (float)$order_success_stats['total_money'];


        $where['eo.status'] = ['=', 1];


            $order_success_stats =Db::name('ewm_order')->alias('eo')->join('orders o','eo.order_no = o.trade_no')->where($where)->field('sum(eo.order_price) as total_money,count(eo.id) as total_count')->find();
//        $fees['success_num'] = $query->alias('a')->join('pay_code b','b.id = a.code_type')->join('orders o','a.order_no = o.trade_no')->where($where)->count();
        $fees['paid']  = (float)$order_success_stats['total_money'];
        $fees['success_num']  = (float)$order_success_stats['total_count'];

        $fees['success_rate'] =  $fees['success_num']==0?0: sprintf("%.2f",$fees['success_num']/$fees['total_num']*100);

        $pay_code_id1 = Db::name('pay_code')->where('code',$type)->value('id');
        $this->assign('confirm_payment_show', getAdminPayCodeSys('confirm_payment_show', $pay_code_id1, is_admin_login()) ?? 1);

        if (is_array($pay_code_id)){
            $pay_code_id = implode(',',$pay_code_id);
        }
        echo '<script type="text/javascript">';
        echo 'window.onload = function() {';
        echo 'var pay_code_id = "'  . $pay_code_id . '";';
        echo 'var input = document.createElement("input");';
        echo 'input.type = "text";';
        echo 'input.style.display = "none";';
        echo 'input.name = "pay_code_id";';
        echo 'input.value = pay_code_id;';
        echo 'var form = document.getElementsByClassName("layui-form")[0];';
        echo 'form.appendChild(input);';
        echo '}';
        echo '</script>';

        $this->assign('fees',$fees);
        return $this->fetch();
    }



    public function uidOrder()
    {
        return $this->ordersOrUidOrder('uidOrder');
    }



    public function alipayCodeOrder()
    {
        return $this->ordersOrUidOrder('alipayCode');
    }

    public function qqCodeOrder()
    {
        return $this->ordersOrUidOrder('QqCode');
    }

    public function wechatCodeOrder()
    {
        return $this->ordersOrUidOrder('wechatCode');
    }

    public function wechatGoldRedOrder()
    {
        return $this->ordersOrUidOrder('wechatGoldRed');
    }

    public function DingDingGroupOrder()
    {
        return $this->ordersOrUidOrder('DingDingGroup');
    }


    public function douyinGroupRedOrder()
    {
        return $this->ordersOrUidOrder('douyinGroupRed');
    }

    public function wechatGroupRedOrder()
    {
        return $this->ordersOrUidOrder('wechatGroupRed');
    }

    public function CnyNumberOrder()
    {
        return $this->ordersOrUidOrder('CnyNumber');
    }

    public function AppletProductsOrder()
    {
        return $this->ordersOrUidOrder('AppletProducts');
    }

    public function QQFaceRedOrder()
    {
        return $this->ordersOrUidOrder('QQFaceRed');
    }

    public function JDECardOrder()
    {
        return $this->ordersOrUidOrder('JDECard');
    }

    public function taoBaoDirectPayOrder()
    {
        return $this->ordersOrUidOrder('taoBaoDirectPay');
    }

    public function alipayF2FOrder()
    {
        return $this->ordersOrUidOrder('alipayF2F');
    }

    public function alipayPinMoneyOrder()
    {
        return $this->ordersOrUidOrder('alipayPinMoney');
    }

    public function alipayWapOrder()
    {
        return $this->ordersOrUidOrder('alipayWap');
    }

    public function HuiYuanYiFuKaOrder()
    {
        return $this->ordersOrUidOrder('HuiYuanYiFuKa');
    }

    public function LeFuTianHongKaOrder()
    {
        return $this->ordersOrUidOrder('LeFuTianHongKa');
    }

    public function JunWebOrder()
    {
        return $this->ordersOrUidOrder('JunWeb');
    }


    public function usdtTrcOrder()
    {
        return $this->ordersOrUidOrder('usdtTrc');
    }

    public function alipaySmallPurseOrder()
    {
        return $this->ordersOrUidOrder('alipaySmallPurse');
    }

    public function taoBaoMoneyRedOrder()
    {
        return $this->ordersOrUidOrder('taoBaoMoneyRed');
    }

    public function alipayTransferCodeOrder()
    {
        return $this->ordersOrUidOrder('alipayTransferCode');
    }

    public function alipayWorkCardBigOrder()
    {
        return $this->ordersOrUidOrder('alipayWorkCardBig');
    }

    public function CnyNumberAutoOrder()
    {
        return $this->ordersOrUidOrder('CnyNumberAuto');
    }


    public function QianxinTransferOrder()
    {
        return $this->ordersOrUidOrder('QianxinTransfer');
    }

    public function taoBaoEcardOrder()
    {
        return $this->ordersOrUidOrder('taoBaoEcard');
    }

    public function alipayCodeSmallOrder()
    {
        return $this->ordersOrUidOrder('alipayCodeSmall');
    }

    public function alipayPassRedOrder()
    {
        return $this->ordersOrUidOrder('alipayPassRed');
    }

    public function AggregateCodeOrder()
    {
        return $this->ordersOrUidOrder('AggregateCode');
    }

    public function alipayTransferOrder()
    {
        return $this->ordersOrUidOrder('alipayTransfer');
    }

    public function alipayWorkCardOrder()
    {
        return $this->ordersOrUidOrder('alipayWorkCard');
    }

    public function getOrdersList()
    {
        return $this->getUidOrOrdersList('kzk');
    }


    public function getalipayCodeOrdersList()
    {
        return $this->getUidOrOrdersList('alipayCode');
    }

    public function getQqCodeOrdersList()
    {
        return $this->getUidOrOrdersList('QqCode');
    }

    public function getwechatCodeOrdersList()
    {
        return $this->getUidOrOrdersList('wechatCode');
    }

    public function getwechatGoldRedOrdersList()
    {
        return $this->getUidOrOrdersList('wechatGoldRed');
    }

    public function getDingDingGroupOrdersList()
    {
        return $this->getUidOrOrdersList('DingDingGroup');
    }


    public function getdouyinGroupRedOrdersList()
    {
        return $this->getUidOrOrdersList('douyinGroupRed');
    }

    public function getwechatGroupRedOrdersList()
    {
        return $this->getUidOrOrdersList('wechatGroupRed');
    }

    public function getCnyNumberOrdersList()
    {
        return $this->getUidOrOrdersList('CnyNumber');
    }

    public function getAppletProductsOrdersList()
    {
        return $this->getUidOrOrdersList('AppletProducts');
    }

    public function getQQFaceRedOrdersList()
    {
        return $this->getUidOrOrdersList('QQFaceRed');
    }


    public function getJDECardOrdersList()
    {
        return $this->getUidOrOrdersList('JDECard');
    }

    public function gettaoBaoDirectPayOrdersList()
    {
        return $this->getUidOrOrdersList('taoBaoDirectPay');
    }

    public function getalipayWapOrdersList()
    {
        return $this->getUidOrOrdersList('alipayWap');
    }

    public function getHuiYuanYiFuKaOrdersList()
    {
        return $this->getUidOrOrdersList('HuiYuanYiFuKa');
    }

    public function getLeFuTianHongKaOrdersList()
    {
        return $this->getUidOrOrdersList('LeFuTianHongKa');
    }

    public function getJunWebOrdersList()
    {
        return $this->getUidOrOrdersList('JunWeb');
    }

    public function getalipayF2FOrdersList()
    {
        return $this->getUidOrOrdersList('alipayF2F');
    }


    public function getalipayPinMoneyOrdersList()
    {
        return $this->getUidOrOrdersList('alipayPinMoney');
    }

    public function getusdtTrcOrdersList()
    {
        return $this->getUidOrOrdersList('usdtTrc');
    }

    public function getalipaySmallPurseOrdersList()
    {
        return $this->getUidOrOrdersList('alipaySmallPurse');
    }


    public function gettaoBaoMoneyRedOrdersList()
    {
        return $this->getUidOrOrdersList('taoBaoMoneyRed');
    }


    public function getalipayTransferCodeOrdersList()
    {
        return $this->getUidOrOrdersList('alipayTransferCode');
    }


    public function getalipayWorkCardBigOrdersList()
    {
        return $this->getUidOrOrdersList('alipayWorkCardBig');
    }

    public function getCnyNumberAutoOrdersList()
    {
        return $this->getUidOrOrdersList('CnyNumberAuto');
    }

    public function getQianxinTransferOrdersList()
    {
        return $this->getUidOrOrdersList('QianxinTransfer');
    }

    public function gettaoBaoEcardOrdersList()
    {
        return $this->getUidOrOrdersList('taoBaoEcard');
    }

    public function getalipayCodeSmallOrdersList()
    {
        return $this->getUidOrOrdersList('alipayCodeSmall');
    }

    public function getalipayPassRedOrdersList()
    {
        return $this->getUidOrOrdersList('alipayPassRed');
    }


    public function getAggregateCodeOrdersList()
    {
        return $this->getUidOrOrdersList('AggregateCode');
    }

    public function getalipayTransferOrdersList()
    {
        return $this->getUidOrOrdersList('alipayTransfer');
    }


    public function getalipayWorkCardOrdersList()
    {
        return $this->getUidOrOrdersList('alipayWorkCard');
    }


    private function getUidOrOrdersList($type){

        //时间搜索  时间戳搜素
        $parseTime =  $this->parseRequestDate3();
        $where['a.create_time'] =$parseTime;
        if ($this->request->param('time_type') != ''){
            if ($this->request->param('time_type') != 1){
                $where['a.update_time'] = $parseTime;
            }else{
                $where['a.create_time'] =$parseTime;
            }
        }
        if ($this->request->param('status') != "") {
            $where['eo.status'] = ['eq', $this->request->param('status')];
        }

        if (trim($this->request->param('id')) != ''){
            $orderid = trim($this->request->param('id')) - 20000000;
            $where['eo.id'] = $orderid;
        }

                !empty($this->request->param('admin_username')) && $where['am.username']
            = ['like', '%'.$this->request->param('admin_username').'%'];

        //        !empty($this->request->param('admin_username')) && $where['m.username']
//            = ['like', '%'.$this->request->param('admin_username').'%'];
//
        !empty($this->request->param('username')) && $where['m.username']
            = ['like', '%'.$this->request->param('username').'%'];

//        !empty($this->request->param('username')) && $where['c.username']
//            = ['like', '%'.$this->request->param('username').'%'];


        !empty($this->request->param('pay_username')) && $where['eo.pay_username']
            = ['eq', $this->request->param('pay_username')];


                !empty($this->request->param('cardKey')) && $where['eo.cardKey']
            = ['eq', $this->request->param('cardKey')];


                if(!empty($this->request->param('order_no'))){
            unset($where['a.create_time']);
            $where['a.trade_no']= ['eq', $this->request->param('order_no')];
        }



        if (is_admin_login() != 1){
            $users = Db::name('user')->where('admin_id',is_admin_login())->column('uid');
            $where['a.uid'] = ['in',$users];
        }


                if (!empty($this->request->param('uid'))){
            if (is_admin_login() != 1){
                $adminSonUser = Db::name('user')->where('admin_id',is_admin_login())->column('uid');
                if (!in_array($this->request->param('uid'),$adminSonUser)){
                    $this->result([
                        'code' => CodeEnum::ERROR,
                        'msg' => '非法操作',
                    ]);
                }
            }

            $where['a.uid'] = ['eq', $this->request->param('uid')];
        }


        if ($this->request->param('is_status') != "") {
            $is_status = $this->request->param('is_status');
            if($is_status =='0'){
                $where['e.is_status'] = ['neq', '200'];
            }elseif($is_status == '-1') {
                $where['e.is_status'] = ['eq', ' '];
            }else{
                $where['e.is_status'] = ['eq', $this->request->param('is_status')];
            }
        }





        if (trim($this->request->param('agent_name')) != ''){
            if (is_admin_login() != 1){
                $admin_id =  Db::name('ms')->where('username',$this->request->param('agent_name'))->value('admin_id');
                if ($admin_id != is_admin_login()){
                    return json(['code'=>0,'msg'=>'非法请求']);
                }
            }

            $agent_id = Db::name('ms')->where('username',$this->request->param('agent_name'))->value('userid');

            $this->son_id = [];
            $agentids = $this->getIds($agent_id);
            array_push($agentids,$agent_id);
            $where['eo.gema_userid'] = ['in', $agentids];
        }

        if (trim($this->request->param('admin_username')) != ''){
            $admin_id = Db::name('admin')->where('username',$this->request->param('admin_username'))->value('id');
            $where['eo.admin_id'] = $admin_id;
        }


        if (trim($this->request->param('note')) != ''){
            $where['eo.note']
                = ['like', '%'.$this->request->param('note').'%'];
        }

        if (trim($this->request->param('visite_ip')) != ''){
            $where['eo.visite_ip']
                = ['eq', $this->request->param('visite_ip')];
        }
        if ($type == 'uidOrders') {
            $where['a.channel'] = ['in',['alipayUid','alipayUidSmall','alipayUidTransfer']];
        }else{
            $where['a.channel'] = $type;
        }

        if(!empty($this->request->param('code_type'))){
//            unset($where['p.add_time']);
            $where['d.code']= ['eq', $this->request->param('code_type')];

        }
      //  $fields = ['a.*', 'b.account_name', 'b.bank_name', 'account_number', 'c.username', 'eo.id as block_ip_id','u.username as shname','u.uid as shuid','n.is_status',  'n.times','m.username as admin_username'];
        $field = 'eo.id,u.username as shname,gema_username as username,m.pid,eo.order_pay_price,a.uid as shuid,a.out_trade_no as order_no,eo.pay_user_name,a.amount as order_price,a.create_time as add_time,eo.pay_time,eo.pay_username,eo.legality,eo.status,ec.account_name,ec.bank_name,ec.account_number,e.is_status,e.times,eo.visite_ip,eo.visite_clientos,a.remark,eo.note,eo.cardKey,eo.cardAccount,eo.updateTime';
        $data = $this->logicOrders->getOrderList4($where, $field, 'a.id desc', false);
        $count = $this->logicOrders->getOrdersCount4($where);

        $this->result($data || !empty($data) ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg' => '',
                'count' => $count,
                'data' => $data,
            ] : [
                'code' => CodeEnum::ERROR,
                'msg' => '暂无数据',
                'count' => $count,
                'data' => $data
            ]
        );
    }

//    protected function getUidOrOrdersList($type)
//    {
//        //状态
//        if ($this->request->param('status') != "") {
//            $where['a.status'] = ['eq', $this->request->param('status')];
//        }
//
//        //时间搜索  时间戳搜素
//        $where['a.add_time'] = $this->parseRequestDate3();
//
//        if (trim($this->request->param('id')) != ''){
//            $orderid = trim($this->request->param('id')) - 20000000;
//            $where['a.id'] = $orderid;
//        }
//
//        !empty($this->request->param('admin_username')) && $where['m.username']
//            = ['like', '%'.$this->request->param('admin_username').'%'];
//
//        !empty($this->request->param('username')) && $where['c.username']
//            = ['like', '%'.$this->request->param('username').'%'];
//
//        !empty($this->request->param('username')) && $where['c.username']
//            = ['like', '%'.$this->request->param('username').'%'];
//
//
//        !empty($this->request->param('account_name')) && $where['b.account_name']
//            = ['eq', $this->request->param('account_name')];
//
//        !empty($this->request->param('pay_username')) && $where['a.pay_username']
//            = ['eq', $this->request->param('pay_username')];
//
//        !empty($this->request->param('pay_user_name')) && $where['a.pay_user_name']
//            = ['eq', $this->request->param('pay_user_name')];
//
//        !empty($this->request->param('order_pay_price')) && $where['a.order_pay_price']
//            = ['eq', $this->request->param('order_pay_price')];
//
//        !empty($this->request->param('cardKey')) && $where['a.cardKey']
//            = ['eq', $this->request->param('cardKey')];
//
//        if(!empty($this->request->param('order_no'))){
//            unset($where['a.add_time']);
//            $where['a.order_no']= ['eq', $this->request->param('order_no')];
//        }
//
//        if (!empty($this->request->param('start_update')) && !empty($this->request->param('end_update'))) {
//            unset($where['a.add_time']);
//            $startUpdateTime = $this->request->param('start_update');
//            $endUpdateTime = $this->request->param('end_update');
//            $where['a.pay_time'] = ['between time',[$startUpdateTime,$endUpdateTime]];;
//        }
//
//        if (is_admin_login() != 1){
//            $adminSonUser = Db::name('user')->where('admin_id',is_admin_login())->column('uid');
//            $where['o.uid'] = ['in',$adminSonUser];
//        }
//
//
//        if (!empty($this->request->param('uid'))){
//            if (is_admin_login() != 1){
//                $adminSonUser = Db::name('user')->where('admin_id',is_admin_login())->column('uid');
//                if (!in_array($this->request->param('uid'),$adminSonUser)){
//                    $this->result([
//                        'code' => CodeEnum::ERROR,
//                        'msg' => '非法操作',
//                    ]);
//                }
//            }
//
//            $where['o.uid'] = ['eq', $this->request->param('uid')];
//        }
//
//
//        if ($this->request->param('is_status') != "") {
//            $is_status = $this->request->param('is_status');
//            if($is_status =='0'){
//                $where['n.is_status'] = ['neq', '200'];
//            }elseif($is_status == '-1') {
//                $where['n.is_status'] = ['eq', ' '];
//            }else{
//                $where['n.is_status'] = ['eq', $this->request->param('is_status')];
//            }
//        }
//
//        if ($type == 'uidOrders') {
//            $where['p.code'] = ['in',['alipayUid','alipayUidSmall','alipayUidTransfer']];
//            $fields = ['a.*', 'b.account_name', 'b.bank_name', 'account_number', 'c.username', 'eo.id as block_ip_id','u.username as shname','u.uid as shuid','n.is_status', 'n.times','p.name', 'm.username as admin_username'];
//        }else{
//            $where['p.code'] = $type;
//            $fields = ['a.*', 'b.account_name', 'b.bank_name', 'account_number', 'c.username', 'eo.id as block_ip_id','u.username as shname','u.uid as shuid','n.is_status',  'n.times','m.username as admin_username'];
//        }
//
//        if(!empty($this->request->param('code_type'))){
////            unset($where['p.add_time']);
//            $where['p.code']= ['eq', $this->request->param('code_type')];
//
//        }
//
//        if (trim($this->request->param('agent_name')) != ''){
//            if (is_admin_login() != 1){
//                $admin_id =  Db::name('ms')->where('username',$this->request->param('agent_name'))->value('admin_id');
//                if ($admin_id != is_admin_login()){
//                    return json(['code'=>0,'msg'=>'非法请求']);
//                }
//            }
//
//            $agent_id = Db::name('ms')->where('username',$this->request->param('agent_name'))->value('userid');
//
//            $this->son_id = [];
//            $agentids = $this->getIds($agent_id);
//            array_push($agentids,$agent_id);
//            $where['a.gema_userid'] = ['in', $agentids];
//        }
//
//        if (trim($this->request->param('admin_username')) != ''){
//            $admin_id = Db::name('admin')->where('username',$this->request->param('admin_username'))->value('id');
//            $where['a.admin_id'] = $admin_id;
//        }
//
//
//        if (trim($this->request->param('note')) != ''){
//            $where['a.note']
//                = ['like', '%'.$this->request->param('note').'%'];
//        }
//
//        if (trim($this->request->param('visite_ip')) != ''){
//            $where['a.visite_ip']
//                = ['eq', $this->request->param('visite_ip')];
//        }
////        print_r($where);die;
//
//        $data = $this->logicEwmOrder->getOrderList($where, $fields, 'a.add_time desc', false);
////        print_r($this->logicEwmOrder->getLastSql());die;
//
//        !empty($this->request->param('pay_username')) && $where['pay_username']
//            = ['eq', $this->request->param('pay_username')];
//
//
//        $count = $this->logicEwmOrder->getOrdersCount($where);
//        $this->result($data || !empty($data) ?
//            [
//                'code' => CodeEnum::SUCCESS,
//                'msg' => '',
//                'count' => $count,
//                'data' => $data,
//            ] : [
//                'code' => CodeEnum::ERROR,
//                'msg' => '暂无数据',
//                'count' => $count,
//                'data' => $data
//            ]
//        );
//    }


    public function smsinfo(){
        $orderid = $this->request->param('id');
        $where = [];
        if (is_admin_login() != 1){
            $sonMS = Db::name('ms')->where('admin_id',is_admin_login())->column('userid');
            $where['ms_id'] = ['in',$sonMS];
        }
        $sms = Db::name('banktobank_sms')->where($where)->where('order_id',$orderid)->find();
       /* if (empty($sms)){
            $this->error('Error Empty Data!');
        }*/
        $this->assign('sms',$sms);
        return $this->fetch();
    }



    public function getuidOrdersList()
    {
        return $this->getUidOrOrdersList('uidOrders');
    }

    public function abnormalOrders()
    {
        return $this->fetch();
    }

    public function getAbnormalOrdersList()
    {

        $where['name_abnormal|money_abnormal'] = 1;

        //状态
        if ($this->request->param('status') != "") {
            $where['a.status'] = ['eq', $this->request->param('status')];
        }
        !empty($this->request->param('order_no')) && $where['order_no']
            = ['eq', $this->request->param('order_no')];
        //时间搜索  时间戳搜素
        $where['add_time'] = $this->parseRequestDate3();

        !empty($this->request->param('username')) && $where['c.username']
            = ['eq', $this->request->param('username')];

        !empty($this->request->param('amount')) && $where['order_pay_price']
            = ['eq', $this->request->param('amount')];

        !empty($this->request->param('account_name')) && $where['b.account_name']
            = ['eq', $this->request->param('account_name')];

        !empty($this->request->param('pay_username')) && $where['pay_username']
            = ['eq', $this->request->param('pay_username')];

        !empty($this->request->param('pay_user_name')) && $where['pay_user_name']
            = ['eq', $this->request->param('pay_user_name')];

        $fields = ['a.*', 'b.account_name', 'b.bank_name', 'account_number', 'c.username'];
        $data = $this->logicEwmOrder->getOrderList($where, $fields, 'add_time desc', false);

        !empty($this->request->param('pay_username')) && $where['pay_username']
            = ['eq', $this->request->param('pay_username')];


        $count = $this->logicEwmOrder->getOrdersCount($where);
        $this->result($data || !empty($data) ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg' => '',
                'count' => $count,
                'data' => $data,
            ] : [
                'code' => CodeEnum::ERROR,
                'msg' => '暂无数据',
                'count' => $count,
                'data' => $data
            ]
        );
    }

    public function abnormalOrderSave()
    {
        $orderId = $this->request->post('id');
        if (is_admin_login() != 1){
            $uid = Db::name('orders')->where('id',$orderId)->value('uid');
            $adminid = Db::name('user')->where('uid',$uid)->value('admin_id');
            if ($adminid != is_admin_login()){
                $this->error('非法请求');
            }
        }
        $abnormal = $this->request->post('abnormal');
        $order = $this->modelEwmOrder->find($orderId);

        if (!$order or !in_array($abnormal, [1,2])){
            $this->error('操作失败');
        }
        ($abnormal == 1) && $order->name_abnormal = 1;
        ($abnormal == 2) && $order->money_abnormal = 1;
        $order->save();

        $this->success('操作成功');
    }

    /**
     * 后台管理员确认收款
     * @param Request $request
     */
    public function issueOrder(Request $request)
    {
        $orderId = $this->request->post('id');
        if (is_admin_login() != 1){

            $order_no = Db::name('ewm_order')->where('id',$orderId)->value('order_no');
            $uid = Db::name('orders')->where('trade_no',$order_no)->value('uid');

            $adminid = Db::name('user')->where('uid',$uid)->value('admin_id');

            if ($adminid != is_admin_login()){
                $this->error('非法请求');
            }
        }

        $coerce = $this->request->post('coerce');//是否强制补单
        $GemaOrder = new \app\common\logic\EwmOrder();
        $res = $GemaOrder->setOrderSucessByAdmin($orderId, $coerce);
        if ($res['code'] == CodeEnum::ERROR) {
            $this->error($res['msg']);
        }

        $order_no = Db::name('ewm_order')->where('id',$orderId)->value('order_no');
        //加个操作日志
        action_log('管理员补单', '管理员补单：订单号，' . $order_no);

        $this->success('操作成功');
    }

    /**
     * 后台管理员确认退款
     * @param Request $request
     */
    public function refundOrder(Request $request){
        $orderId = $this->request->post('id');
        if (is_admin_login() != 1){
            $uid = Db::name('orders')->where('id',$orderId)->value('uid');
            $adminid = Db::name('user')->where('uid',$uid)->value('admin_id');
            if ($adminid != is_admin_login()){
                $this->error('非法请求');
            }
        }
        $where['id'] = $orderId;
        $where['status'] = ['in',[0,2]];

        $order = $this->modelEwmOrder->where($where)->lock(true)->find();

        if ( empty($order)){
            $this->error('订单不存在');
        }

        $order->status = 3;
        $order->pay_time = time();
        $order->save();

        $this->success('更新成功');



    }


    /**
     * 码商流水列表
     * @param Request $request
     * @return mixed
     */
    public function bills(Request $request)
    {
        $uid = $request->param('uid', 0);
        $type = $request->param('type');
        if (!empty($uid))
        {
            $this->assign('uid', $uid);
            $map['b.userid'] = $uid;
        }

        if (!empty($type))
        {
            $this->assign('type', $type);
            $map['a.type'] = $type;
        }

        $map['a.addtime'] = $this->parseRequestDate3();
        if (is_admin_login() != 1){
            $adminSonMs = Db::name('ms')->where('admin_id',is_admin_login())->column('userid');
            $map['b.userid'] = ['in',$adminSonMs];
        }

        //获取平台调整总加金额，总减金额
        list($inc, $des) = $this->logicMsSomeBill->changAmount($map);
        $this->assign('montey_types', MsMoneyType::getMoneyOrderTypes());
        $this->assign('inc', sprintf("%.2f",$inc));
        $this->assign('dec', sprintf("%.2f",$des));

        return $this->fetch();
    }






    /**
     * @param Request $request
     * @throws \think\exception\DbException
     */
    public function getBillsList(Request $request)
    {
        //时间搜索  时间戳搜素
        $map['addtime'] = $this->parseRequestDate3();
        $billType = $request->param('bill_type', 0, 'intval');
        $billType && $map['jl_class'] = $billType;
        $username = $request->param('username', '', 'trim');
        $username && $map ['b.username'] = $username;
        $info = $request->param('info', '', 'trim');
        $info && $map ['a.info'] = ['like', '%' . $info . '%'];

        $uid = $request->param('uid', 0, 'intval');
        $uid && $map ['a.uid'] = $uid;

        $admin_id = $request->param('admin_id', 0, 'intval');
        $admin_id && $map ['a.admin_id'] = $admin_id;

        $type = $request->param('type');
        $type && $map['a.type'] = $type;


        $fields = ['a.*', 'b.username'];
        if (is_admin_login() != 1){
            $adminSonMs = Db::name('ms')->where('admin_id',is_admin_login())->column('userid');
            $map['b.userid'] = ['in',$adminSonMs];
//            $map['uid'] = ['in',$adminSonMs];
        }
        $data = $this->logicMsSomeBill->getBillsList($map, $fields, 'id desc', false);
        if ($data) {
            $types = MsMoneyType::getMoneyOrderTypes();
            foreach ($data as $k => $v) {
                $data[$k]['jl_class_text'] = $types[$v['jl_class']];
            }
        }


        $count = $this->logicMsSomeBill->getBillsCount($map);
        $this->result($data || !empty($data) ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg' => '',
                'count' => $count,
                'data' => $data,
            ] : [
                'code' => CodeEnum::ERROR,
                'msg' => '暂无数据',
                'count' => $count,
                'data' => $data
            ]
        );

    }


    /**
     * 平台手动调整用户余额
     */
    public function changeBalance(Request $request)
    {
        $userId = $request->param('userid');
        if (is_admin_login() != 1){
            $admin_id =  Db::name('ms')->where('userid',$userId)->value('admin_id');
            if ($admin_id != is_admin_login()){
                return json(['code'=>0,'msg'=>'非法请求']);
            }
        }
        $user = Db::name('ms')->where(['userid' => $userId])->find();

        if (!$user) {
            $this->error('会员不存在');
        }
        if (is_admin_login() != 1){
            if ($user['admin_id'] != is_admin_login()){
                $this->error('非法操作');
            }
        }
        $curretuserMoney = Db::name('ms')->where(['userid' => $userId])->value('money');
        if ($request->isPost()) {
            //看了存储引擎不支持事务算了 M()->startTrans();
            $data = $request->post();

            $result = $this->validate(
                [
                    '__token__' => $this->request->post('__token__'),
                ],
                [
                    '__token__' => 'require|token'
                ]);

            if (true !== $result) {
                $this->error($result);
            }

            if (bccomp(0.00, $data['money']) != -1) {
                $this->error('操作资金不可小于或等于0.00');
            }
            if ($data['op_type'] == 0 && bccomp($data['money'], $curretuserMoney) == 1) { //减少
                $this->error('减少资金不可小于用户本金');
            }

            Db::startTrans();
            $data['opInfo'] =  str_replace("\n", ",", $data['opInfo']);
            $ret = accountLog($userId, MsMoneyType::ADJUST, $data['op_type'], $data['money'], $data['opInfo']);
            if ($ret) {
                Db::commit();
                $this->success('操作成功', url('index'));
            }

            Db::rollback();
            $this->error('操作失败');
        }
        $this->assign('user', $user);
        $this->assign('curretuserMoney', $curretuserMoney);
        return $this->fetch();

    }


    /**
     * 平台手动调整用户冻结余额
     */
    public function changeDisableBalance(Request $request)
    {
        $userId = $request->param('userid');
        if (is_admin_login() != 1){
            $admin_id =  Db::name('ms')->where('userid',$userId)->value('admin_id');
            if ($admin_id != is_admin_login()){
                return json(['code'=>0,'msg'=>'非法请求']);
            }
        }
        $user = Db::name('ms')->where(['userid' => $userId])->find();

        if (!$user) {
            $this->error('会员不存在');
        }
        if (is_admin_login() != 1){
            if ($user['admin_id'] != is_admin_login()){
                $this->error('非法操作');
            }
        }
        $curretuserMoney = Db::name('ms')->where(['userid' => $userId])->value('disable_money');
        if ($request->isPost()) {
            //看了存储引擎不支持事务算了 M()->startTrans();
            $data = $request->post();

            $result = $this->validate(
                [
                    '__token__' => $this->request->post('__token__'),
                ],
                [
                    '__token__' => 'require|token'
                ]);

            if (true !== $result) {
                $this->error($result);
            }

            if (bccomp(0.00, $data['money']) != -1) {
                $this->error('操作资金不可小于或等于0.00');
            }
            if ($data['op_type'] == 0 && bccomp($data['money'], $curretuserMoney) == 1) { //减少
                $this->error('减少资金不可小于用户本金');
            }

            Db::startTrans();
            $ret = accountLog($userId, MsMoneyType::DISABLE_ADJUST, $data['op_type'], $data['money'], $data['opInfo'], 'disable');
            if ($ret) {
                Db::commit();
                $this->success('操作成功', url('index'));
            }

            Db::rollback();
            $this->error('操作失败');
        }
        $this->assign('user', $user);
        $this->assign('curretuserMoney', $curretuserMoney);
        return $this->fetch();

    }


    /**
     * 授权码商的登录白名单
     * @param Request $request
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function changeWhiteIp(Request $request)
    {
        $userId = $request->param('ms_id');
        if (is_admin_login() != 1){
            $admin_id =  Db::name('ms')->where('userid',$userId)->value('admin_id');
            if ($admin_id != is_admin_login()){
                return json(['code'=>0,'msg'=>'非法请求']);
            }
        }
        $user = Db::name('ms')->where(['userid' => $userId])->find();

        if (!$user) {
            $this->error('码商不存在');
        }

        if (is_admin_login() != 1){
            if ($user['admin_id'] != is_admin_login()){
                $this->error('非法操作');
            }
        }
        $aesKey = config('aes_key', 'kqwwFRmKyloO');
        $aes = new CryptAes($aesKey);
        $msWhiteIp = new MsWhiteIp;

        if ($request->isPost()) {
            Db::startTrans();
            try {
                //删除当前码商已经有的白名单
                $msWhiteIp->where('ms_id', $userId)->delete();
                //新增新的
                $ips = $request->post('ips', '', 'trim');
                if ($ips) {
                    $ips = array_unique(array_filter(explode(PHP_EOL, $ips)));
                    $ipArr = [];
                    foreach ($ips as $ip) {
                        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                            throw  new \Exception("{$ip}输入不合法");
                        }
                        $row['ms_id'] = $userId;
                        $row['encrypt_ip'] = $aes->encrypt($ip);
                        array_push($ipArr, $row);
                    }
                }
                $msWhiteIp->insertAll($ipArr);
                Db::commit();
            } catch (\Exception $ex) {
                Db::rollback();
                $this->error($ex->getMessage());
            }
            $this->success('操作成功', url('index'));
        }
        $ips = $msWhiteIp->where('ms_id', $userId)->column('encrypt_ip');
        $ips = array_map([$aes, 'decrypt'], $ips);
        $this->assign('ips', $ips);
        return $this->fetch();

    }


    /**
     * 码商二维码列表 旧
     * @param Request $request
     * @return mixed
     */
    public function paycodes_old(Request $request)
    {
        $where = [];
        if (is_admin_login() !=1){
            $sonMs = Db::name('ms')->where('admin_id',is_admin_login())->column('userid');
            $where['ms_id'] = ['in',$sonMs];
        }
        $accont_sum = Db::name('ewm_pay_code')->where($where)->where(['code_type'=>30,'is_delete'=>0])->count('id');
        $account_on_sum = Db::name('ewm_pay_code')->where($where)->where(['code_type'=>30,'status'=>1,'is_delete'=>0])->count('id');
        $this->assign('accont_sum',$accont_sum);
        $this->assign('account_on_sum',$account_on_sum);
        return $this->fetch();
    }
    
    /**
     * 码商二维码列表 新 改造后
     * @return mixed
     */
    public function paycodes()
    {
        return $this->payCodesAndUidList('kzk');
    }
    
    /**
     * 码商二维码列表  和 码商uid列表
     * @return mixed
     */
    protected function payCodesAndUidList($htmlName)
    {
        $where = [];
        $ms_on_sumwhere = [];
        if (is_admin_login() !=1){
            $sonMs = Db::name('ms')
                ->where('admin_id',is_admin_login())
                ->column('userid');
            $where['a.ms_id'] = ['in',$sonMs];
            $ms_on_sumwhere['admin_id'] = is_admin_login();
        }
        $where['a.is_delete'] = 0;
        
        if ($htmlName == 'uid_list') {
            $where['b.code'] = ['in',['alipayUid','alipayUidSmall','alipayUidTransfer']];
        }else{
            $where['b.code'] = $htmlName;
        }
        $where['c.status'] = ['neq',-1];
        $accont_sum = Db::name('ewm_pay_code')->alias('a')->join('pay_code b','a.code_type = b.id')->join('ms c','a.ms_id = c.userid')->where($where)
            ->count();
        
        $account_on_sum = Db::name('ewm_pay_code')->alias('a')->join('pay_code b','a.code_type = b.id')->join('ms c','a.ms_id = c.userid')->where($where)
            ->where('a.status',1)->count();

        $code_id = Db::name('pay_code')->where('code',$htmlName)->value('id');
        $validUserIds = $this->modelEwmPayCode
            ->where(['is_delete'=>0, 'code_type'=>$code_id, 'status'=>1])
            ->group('ms_id')
            ->having('count(id) > 0')
            ->column('ms_id');

        $data = $this->modelMs->where(['status'=>1,'work_status'=>1,'admin_work_status'=>1,'money'=>['gt',4000]])->where($ms_on_sumwhere)->whereIn('userid', $validUserIds)->field('username,userid')->paginate(input('limit'));

        foreach ($data as $k => $v) {
            $v['codeCount'] = $this->modelEwmPayCode->where(['ms_id'=>$v['userid'],'is_delete'=>0,'code_type'=>$code_id,'status'=>1])->count('id');
            $data->offsetSet($k, $v);
        }

        $ms_on_sum = $data->total();
//        $ms_on_sum = Db::name('ewm_pay_code')
//            ->alias('a')
//            ->join('pay_code b','a.code_type = b.id')
//            ->join('ms c','a.ms_id = c.userid')
//            ->where($where)
//            ->where(['c.work_status' => 1, 'c.money' => ['gt', 4000],'c.admin_work_status' => 1])
//            ->where('a.status',1)->group('ms_id')->count();

        $ewm_on_sum = Db::name('ewm_pay_code')
            ->alias('a')
            ->join('pay_code b','a.code_type = b.id')
            ->join('ms c','a.ms_id = c.userid')
            ->where($where)
            ->where(['c.work_status' => 1, 'c.money' => ['gt', 4000],'c.admin_work_status' => 1])
            ->where('a.status',1)->count();

        $this->assign('accont_sum',$accont_sum);
        $this->assign('account_on_sum',$account_on_sum);
        $this->assign('ms_on_sum',$ms_on_sum);
        $this->assign('ewm_on_sum',$ewm_on_sum);
        if ($htmlName == 'uid_list'){
            return $this->fetch($htmlName);
        }else{
            return $this->fetch($htmlName.'_list');
        }

    }

    /**
     * @return void
     * @throws \think\Exception
     * 卡转卡账号列表联动统计 旧
     */
    public function kzkCodeListCount_old(){
        $where = [];
        $where['code_type'] = 30;
        $where['is_delete'] = 0;
        if (!empty($this->request->param('username'))){
            $where['user_name'] = $this->request->param('username');
        }

        if (!empty($this->request->param('account_name'))){
            $where['account_name'] = $this->request->param('account_name');
        }

        if (!empty($this->request->param('status'))){

            $where['status'] = $this->request->param('status');
            if ($this->request->param('status') == -1){
                unset($where['status']);
            }
        }

        $accont_sum = Db::name('ewm_pay_code')->where($where)->count('id');


        $account_on_sum = Db::name('ewm_pay_code')->where($where)->where(function ($query){
            $query->where('status',1);
        })->count('id');
        $data = [];
        $data['accont_sum'] = $accont_sum;
        $data['account_on_sum'] = $account_on_sum;
        return $this->result(['code'=>0,'data'=>$data]);
    }
    
    
    /**
     * @return void
     * @throws \think\Exception
     * 卡转卡账号列表联动统计 新
     */
    public function kzkCodeListCount(){
        return $this->kzkOrUidCodeListCount('kzk');
    }



    public function alipayCodeListCount(){
        return $this->kzkOrUidCodeListCount('alipayCode');
    }

    public function qqCodeListCount(){
        return $this->kzkOrUidCodeListCount('QqCode');
    }

    public function wechatCodeListCount(){
        return $this->kzkOrUidCodeListCount('wechatCode');
    }

    public function wechatGoldRedListCount(){
        return $this->kzkOrUidCodeListCount('wechatGoldRed');
    }

    public function DingDingGroupListCount(){
        return $this->kzkOrUidCodeListCount('DingDingGroup');
    }

    public function douyinGroupRedListCount(){
        return $this->kzkOrUidCodeListCount('douyinGroupRed');
    }

    public function wechatGroupRedListCount(){
        return $this->kzkOrUidCodeListCount('wechatGroupRed');
    }

    public function CnyNumberListCount(){
        return $this->kzkOrUidCodeListCount('CnyNumber');
    }

    public function AppletProductsListCount(){
        return $this->kzkOrUidCodeListCount('AppletProducts');
    }

    public function QQFaceRedListCount(){
        return $this->kzkOrUidCodeListCount('QQFaceRed');
    }

    public function JDECardListCount(){
        return $this->kzkOrUidCodeListCount('JDECard');
    }

    public function taoBaoDirectPayListCount(){
        return $this->kzkOrUidCodeListCount('taoBaoDirectPay');
    }

    public function alipayWapListCount(){
        return $this->kzkOrUidCodeListCount('alipayWap');
    }

    public function HuiYuanYiFuKaListCount(){
        return $this->kzkOrUidCodeListCount('HuiYuanYiFuKa');
    }

    public function LeFuTianHongKaListCount(){
        return $this->kzkOrUidCodeListCount('LeFuTianHongKa');
    }

    public function JunWebListCount(){
        return $this->kzkOrUidCodeListCount('JunWeb');
    }


    public function alipayF2FListCount(){
        return $this->kzkOrUidCodeListCount('alipayF2F');
    }

    public function alipayPinMoneyListCount(){
        return $this->kzkOrUidCodeListCount('alipayPinMoney');
    }

    public function usdtTrcListCount(){
        return $this->kzkOrUidCodeListCount('usdtTrc');
    }

    public function alipaySmallPurseListCount(){
        return $this->kzkOrUidCodeListCount('alipaySmallPurse');
    }

    public function taoBaoMoneyRedListCount(){
        return $this->kzkOrUidCodeListCount('taoBaoMoneyRed');
    }


    public function alipayTransferCodeListCount(){
        return $this->kzkOrUidCodeListCount('alipayTransferCode');
    }

    public function alipayWorkCardBigListCount(){
        return $this->kzkOrUidCodeListCount('alipayWorkCardBig');
    }

    public function CnyNumberAutoListCount(){
        return $this->kzkOrUidCodeListCount('CnyNumberAuto');
    }

    public function QianxinTransferListCount(){
        return $this->kzkOrUidCodeListCount('QianxinTransfer');
    }


    public function taoBaoEcardListCount(){
        return $this->kzkOrUidCodeListCount('taoBaoEcard');
    }

    public function alipayCodeSmallListCount(){
        return $this->kzkOrUidCodeListCount('alipayCodeSmall');
    }


    public function alipayPassRedListCount(){
        return $this->kzkOrUidCodeListCount('alipayPassRed');
    }


    public function AggregateCodeListCount(){
        return $this->kzkOrUidCodeListCount('AggregateCode');
    }

    public function alipayTransferListCount(){
        return $this->kzkOrUidCodeListCount('alipayTransfer');
    }

    public function alipayWorkCardListCount(){
        return $this->kzkOrUidCodeListCount('alipayWorkCard');
    }

    protected function kzkOrUidCodeListCount($type)
    {
        $where = [];
//        $where['a.is_delete'] = 0;
        if (empty($this->request->param('is_delete'))){
            $where['a.is_delete'] = 0;
        }else{
            $where['a.is_delete'] = 1;
        }
        if (!empty($this->request->param('username'))){
            $where['a.user_name'] = ['=', $this->request->param('username')];
        }
    
        if (!empty($this->request->param('account_name'))){
            $where['a.account_name'] = ['=', $this->request->param('account_name')];
        }
    
        if (!empty($this->request->param('status'))){
            $where['a.status'] = ['=', $this->request->param('status')];
            if ($this->request->param('status') == -1){
                unset($where['status']);
            }
        }
    
        if (!empty($this->request->param('code_type'))){
            $where['b.code'] = ['=', $this->request->param('code_type')];
        }
    
        if (!empty($this->request->param('status'))){
            $where['a.status'] = ['=', $this->request->param('status')];
            if ($this->request->param('status') == -1){
                unset($where['a.status']);
            }
        }
        $ms_on_sumwhere = [];
        if (is_admin_login() != 1){
            $where['c.admin_id'] = is_admin_login();
            $ms_on_sumwhere['admin_id'] = is_admin_login();
        }

        $where['c.status'] = ['neq',-1];

        if ($type == 'uid') {
            $accont_sum = Db::name('ewm_pay_code')->alias('a')
                ->join('pay_code b','a.code_type = b.id')
                ->join('ms c','a.ms_id = c.userid')
                ->where('b.code', 'in', ['alipayUid','alipayUidSmall','alipayUidTransfer'])
                ->where($where)->count('a.id');
            $account_on_sum = Db::name('ewm_pay_code')->alias('a')
                ->join('pay_code b','a.code_type = b.id')
                ->join('ms c','a.ms_id = c.userid')
                ->where('b.code', 'in', ['alipayUid','alipayUidSmall','alipayUidTransfer'])->where($where)->where(function ($query){
                $query->where('a.status',1);
            })->count('a.id');
        }else{
            $accont_sum = Db::name('ewm_pay_code')->alias('a')
                ->join('pay_code b','a.code_type = b.id')
                ->join('ms c','a.ms_id = c.userid')
                ->where('b.code', $type)
                ->where($where)->count('a.id');
            $account_on_sum = Db::name('ewm_pay_code')->alias('a')
                ->join('pay_code b','a.code_type = b.id')
                ->join('ms c','a.ms_id = c.userid')
                ->where('b.code', $type)->where($where)->where(function ($query){
                    $query->where('a.status',1);
                })->count('a.id');
        }

        //可接单码商总数
//        $ms_on_sum = Db::name('ewm_pay_code')->alias('a')
//            ->join('pay_code b','a.code_type = b.id')
//            ->join('ms c','a.ms_id = c.userid')
//            ->where('b.code', $type)->where($where)->where(function ($query){
//                $query->where('a.status',1);
//            })
//            ->where(['work_status' => 1, 'money' => ['gt', 2000]])
//            ->group('ms_id')->count('a.id');

        $code_id = Db::name('pay_code')->where('code',$type)->value('id');
        $validUserIds = $this->modelEwmPayCode
            ->where(['is_delete'=>0, 'code_type'=>$code_id, 'status'=>1])
            ->group('ms_id')
            ->having('count(id) > 0')
            ->column('ms_id');

        $data = $this->modelMs->where(['status'=>1,'work_status'=>1,'admin_work_status'=>1,'money'=>['gt',4000]])->whereIn('userid', $validUserIds)->where($ms_on_sumwhere)->field('username,userid')->paginate(input('limit'));

        foreach ($data as $k => $v) {
            $v['codeCount'] = $this->modelEwmPayCode->where(['ms_id'=>$v['userid'],'is_delete'=>0,'code_type'=>$code_id,'status'=>1])->count('id');
            $data->offsetSet($k, $v);
        }

        $ms_on_sum = $data->total();

        //可用二维码总数
        $ewm_on_sum = Db::name('ewm_pay_code')->alias('a')
            ->join('pay_code b','a.code_type = b.id')
            ->join('ms c','a.ms_id = c.userid')
            ->where('b.code', $type)->where($where)->where(function ($query){
                $query->where('a.status',1);
            })
            ->where(['work_status' => 1, 'money' => ['gt', 2000]])
            ->count('a.id');


        $data = [];
        $data['accont_sum'] = $accont_sum;
        $data['account_on_sum'] = $account_on_sum;
        $data['ms_on_sum'] = $ms_on_sum;
        $data['ewm_on_sum'] = $ewm_on_sum;
        return $this->result(['code'=>0,'data'=>$data]);
    }

    /**
     * @return void
     * @throws \think\Exception
     * uid账号列表联动统计 旧
     */
    public function uidCodeListCount_old(){
        $where = [];
        $where['b.code'] = ['in',['alipayUid','alipayUidSmall']];
        $where['a.is_delete'] = 0;
        if (!empty($this->request->param('username'))){
            $where['a.user_name'] = $this->request->param('username');
        }
        if (!empty($this->request->param('code_type'))){
            $where['b.code'] = $this->request->param('code_type');
        }
        if (!empty($this->request->param('account_name'))){
            $where['a.account_name'] = $this->request->param('account_name');
        }

        if (!empty($this->request->param('status'))){
            $where['a.status'] = $this->request->param('status');
            if ($this->request->param('status') == -1){
                unset($where['a.status']);
            }
        }

        $accont_sum = Db::name('ewm_pay_code')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->count('a.id');


        $account_on_sum = Db::name('ewm_pay_code')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(function ($query){
            $query->where('a.status',1);
        })->count('a.id');
        $data = [];
        $data['accont_sum'] = $accont_sum;
        $data['account_on_sum'] = $account_on_sum;
        return $this->result(['code'=>0,'data'=>$data]);
    }
    
    
    /**
     * @return void
     * @throws \think\Exception
     * uid账号列表联动统计 新
     */
    public function uidCodeListCount(){
        return $this->kzkOrUidCodeListCount('uid');
    }


    /**
     * 码商uid列表 旧
     * @param Request $request
     * @return mixed
     */
    public function uidList_old(Request $request)
    {
        $where = [];
        if (is_admin_login() !=1){
            $sonMs = Db::name('ms')->where('admin_id',is_admin_login())->column('userid');
            $where['a.ms_id'] = ['in',$sonMs];
        }


        $where['b.code'] = ['in',['alipayUid','alipayUidSmall']];

        $accont_sum = Db::name('ewm_pay_code')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(['a.is_delete'=>0])->count('a.id');
        
        $account_on_sum = Db::name('ewm_pay_code')->alias('a')->join('pay_code b','a.code_type = b.id')->where($where)->where(['a.status'=>1,'a.is_delete'=>0])->count('a.id');
        $this->assign('accont_sum',$accont_sum);
        $this->assign('account_on_sum',$account_on_sum);
        return $this->fetch();
    }
    
    
    /**
     * 码商uid列表 新 改造后
     * @return mixed
     */
    public function uidList(Request $request)
    {
        return $this->payCodesAndUidList('uid_list');
    }


    /**
     * uid列表测码
     */
    public function testuid(){


            $id = $this->request->param('id');

            $code = Db::name('ewm_pay_code')->where('id',$id)->find();
            if (empty($code)){
                $this->error('未知错误');
            }

            $this->assign('code',$code);


            return $this->fetch();


    }

    public function testuidpay(){
//        $url = $this->request->param('url');
//        $this->assign('url',$url);


            $code = Db::name('ewm_pay_code')->where('id',$this->request->param('code_id'))->find();
            $data['uid'] = $code['account_number'];

            $data['amount'] = sprintf("%.2f",$this->request->param('amount'));

            $data['orderNo'] = rand(2000000,2999999);

            $url = 'https://www.alipay.com/?appId=20000123&actionType=scan&biz_data={"s":"money","u":"'.$data['uid'].'","a":"'.$data['amount'].'","m":"商城购物'.$data['orderNo'].'"}';
            $encode_url = 'alipays://platformapi/startapp?appId=68687093&url='.urlencode($url);
            $data = str_replace('/&amp;/g','&',$encode_url);
                $this->assign('url',$data);
                return $this->fetch();

    }


    /**
     * 码子统计
     */

    public function kzkStatic(){
        return $this->fetch();
    }

    public function uidStatic(){
        return $this->fetch();
    }

    public function alipayCodeStatic(){
        return $this->fetch('alipayCodeStatic');
    }

    public function wechatCodeStatic(){
        return $this->fetch('wechatCodeStatic');
    }

    public function DingDingGroupStatic(){
        return $this->fetch('DingDingGroupStatic');
    }

    public function alipayTransferStatic(){
        return $this->fetch('alipayTransferStatic');
    }


    public function alipaySmallPurseStatic(){
        return $this->fetch('alipaySmallPurseStatic');
    }


    public function alipayTransferCodeStatic(){
        return $this->fetch('alipayTransferCodeStatic');
    }


    public  function get_ewm_static(){
        $code = $this->request->param('code_type');
        $where['e.code_type'] = $code;
        $where['e.is_delete'] = 0;

        $status = $this->request->param('status', -1);
        ($status != -1) && $where['e.status'] = $status;
        if (!empty(trim($this->request->param('username')))){
            $where['m.username'] = trim($this->request->param('username'));
        }

        if (is_admin_login() != 1){
            $where['m.admin_id'] = is_admin_login();
        }

        $ewmPayCode = Db::name('ewm_pay_code')
            ->alias('e')
            ->join('ms m','e.ms_id = m.userid')
            ->field('e.*,m.username')
            ->where($where)
            ->paginate($this->request->param('limit', 10));
        $ewmPayCode = $ewmPayCode->items();

        $start = date('Y-m-d 00:00:00',time());
        $end = date('Y-m-d 23:59:59',time());
        if (!empty($this->request->param('start'))){
            $start = $this->request->param('start');
        }
        if (!empty($this->request->param('end'))){
            $end = $this->request->param('end');
        }
        $GemapayOrderModel = new \app\common\model\EwmOrder();
        $whereTime['add_time'] = ['between time',[$start,$end]];
        foreach ($ewmPayCode as $k=>$v){
//            总
            $nowOrderData = $GemapayOrderModel->where($whereTime)->where('code_id',$v['id'])->field('sum(order_price) as total_money,count(id) as total_count')->find();

            $ewmPayCode[$k]['total_amount'] = (float)$nowOrderData['total_money'];
            $ewmPayCode[$k]['total_number'] = (float)$nowOrderData['total_count'];
            $SuccessnowOrderData = $GemapayOrderModel->where($whereTime)->where(['code_id'=>$v['id'],'status'=>1])->field('sum(order_price) as total_money,count(id) as total_count')->find();

            $ewmPayCode[$k]['success_amount'] = (float)$SuccessnowOrderData['total_money'];
            $ewmPayCode[$k]['success_number'] = (float)$SuccessnowOrderData['total_count'];
            // 今

//            $TodaynowOrderData = $GemapayOrderModel->where($whereTime)->where('code_id',$v['id'])->field('sum(order_price) as total_money,count(id) as total_count')->find();
//
//            $ewmPayCode[$k]['today_total_amount'] = (float)$TodaynowOrderData['total_money'];
//            $ewmPayCode[$k]['today_total_number'] = (float)$TodaynowOrderData['total_count'];
//            $TodaySuccessnowOrderData = $GemapayOrderModel->where($whereTime)->where(['code_id'=>$v['id'],'status'=>1])->field('sum(order_price) as total_money,count(id) as total_count')->find();
//
//            $ewmPayCode[$k]['today_success_amount'] = (float)$TodaySuccessnowOrderData['total_money'];
//            $ewmPayCode[$k]['today_success_number'] = (float)$TodaySuccessnowOrderData['total_count'];
//
            if ($ewmPayCode[$k]['success_number'] == 0 ){
                $ewmPayCode[$k]['success_rate'] = 0;
            }else{
                $ewmPayCode[$k]['success_rate'] = sprintf("%.2f",$ewmPayCode[$k]['success_number'] / $ewmPayCode[$k]['total_number'] * 100);
            }
//
//            if ($ewmPayCode[$k]['today_success_number'] == 0 ){
//                $ewmPayCode[$k]['today_success_rate'] = 0;
//            }else{
//                $ewmPayCode[$k]['today_success_rate'] = sprintf("%.2f",$ewmPayCode[$k]['today_success_number'] / $ewmPayCode[$k]['today_total_number'] * 100);
//            }
        }

//        print_r($ewmPayCode);die;
        return json(['code'=>0,'data'=>$ewmPayCode,'count'=>Db::name('ewm_pay_code')->alias('e')
            ->join('ms m','e.ms_id = m.userid')
            ->field('e.*,m.username')
            ->where($where)->count('e.id')]);

    }


    public  function get_uid_static(){
//        $code = $this->request->param('code_type');
//        $where['code_type'] = $code;

        $where['b.code'] = ['in',['alipayUid','alipayUidSmall','alipayUidTransfer']];
        $where['a.is_delete'] = 0;
        if (is_admin_login() != 1){
            $adminSonMs = Db::name('ms')->where('admin_id',is_admin_login())->column('userid');
            $where['a.ms_id'] = ['in',$adminSonMs];
        }
        if (!empty($this->request->param('code_type'))){
//            $whereTime['']
            $where['b.code'] = $this->request->param('code_type');

        }
        $ewmPayCode = Db::name('ewm_pay_code')->alias('a')->join('pay_code b','a.code_type = b.id')->field('a.*')->where($where)->select();

        $start = date('Y-m-d 00:00:00',time());
        $end = date('Y-m-d 23:59:59',time());
        if (!empty($this->request->param('start'))){
            $start = $this->request->param('start');
        }
        if (!empty($this->request->param('end'))){
            $end = $this->request->param('end');
        }


        $whereTime['add_time'] = ['between time',[$start,$end]];
        if (trim($this->request->param('start_update')) != '' && trim($this->request->param('end_update')) != ''){
            unset( $where['e.add_time']);
            $startUpdateTime = $this->request->param('start_update');
            $endUpdateTime = $this->request->param('end_update');
            $where['pay_time'] = ['between time',[$startUpdateTime,$endUpdateTime]];
        }


        foreach ($ewmPayCode as $k=>$v){
            $ewmPayCode[$k]['total_number'] = Db::name('ewm_order')->where($whereTime)->where('code_id',$v['id'])->count();
            $ewmPayCode[$k]['total_amount'] = Db::name('ewm_order')->where($whereTime)->where('code_id',$v['id'])->sum('order_price');
            $ewmPayCode[$k]['success_number'] = Db::name('ewm_order')->where($whereTime)->where(['code_id'=>$v['id'],'status'=>1])->count();
            $ewmPayCode[$k]['success_amount'] = Db::name('ewm_order')->where($whereTime)->where(['code_id'=>$v['id'],'status'=>1])->sum('order_price');
            if ($ewmPayCode[$k]['success_number'] == 0 ){
                $ewmPayCode[$k]['success_rate'] = 0;
            }else{
                $ewmPayCode[$k]['success_rate'] = sprintf("%.2f",$ewmPayCode[$k]['success_number'] / $ewmPayCode[$k]['total_number'] * 100);
            }
        }

//        print_r($ewmPayCode);die;
        return json(['code'=>0,'data'=>$ewmPayCode,'count'=>count($ewmPayCode)]);

    }
    /**
     * 管理员删除二维码
     * @param Request $request
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function delPayCode(Request $request)
    {
        $id = trim($request->param('id'));
        $id = trim($request->param('id'));
        $GemapayCodeModel = new EwmPayCode();
        $codeInfo = $GemapayCodeModel->find($id);
        if (is_admin_login() != 1){
            $user = Db::name('ms')->where('userid',$codeInfo['ms_id'])->find();
            if (is_admin_login() != $user['admin_id']){
                $this->error('非法请求');
            }
        }


        $re = Db::name('ewm_pay_code')
            ->where('id', $id)
            ->delete();

        if ($re) {
            //从队列中删除此二维码
            $QueueLogic = new Queuev1Logic();
            $QueueLogic->delete($id, 3, 1);
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }


    /**
     * @param Request $request
     * @return mixed
     */
    public function getPaycodesLists_old(Request $request)
    {
        $map = [];
        $account_name = $request->param('account_name', 0, 'intval');
        $account_name && $map['a.account_name'] = ['like', '%' . $account_name . '%'];
        $map['a.code_type'] = 30;
        $map['a.is_delete'] = 0;
        if (is_admin_login() != 1){
            $map['b.admin_id'] = is_admin_login();
        }

        $status = $request->param('status', -1);
        ($status != -1) && $map['a.status'] = $status;
        $username = $this->request->param('username');
        if ($username) {
            $map['b.username'] = $username;
        }
        $fields = ['a.*', 'b.username'];
        $data = $this->logicEwmPayCodes->getCodeList($map, $fields, 'id desc', false);

        $count = $this->logicEwmPayCodes->getCodeCount($map);
        $this->result($data || !empty($data) ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg' => '',
                'count' => $count,
                'data' => $data,
            ] : [
                'code' => CodeEnum::ERROR,
                'msg' => '暂无数据',
                'count' => $count,
                'data' => $data
            ]
        );
    }
    
    
    public function getPaycodesLists()
    {
        return $this->getPayCodesOrUidLists('kzk');
    }
    
    /**
     * @param string $type
     *
     * @return mixed
     */
    protected function getPayCodesOrUidLists(string $type)
    {
        $request = $this->request;
        $map = [];
        $account_name = $request->param('account_name', 0, 'intval');
        $account_name && $map['a.account_name'] = ['like', '%' . $account_name . '%'];
        if ($type == 'uid') {
            $map['c.code'] = ['in',['alipayUid','alipayUidSmall','alipayUidTransfer']];
            $fields = ['a.*', 'b.username','c.name'];
        }else{
            $map['c.code'] = $type;
            $fields = ['a.*', 'b.username'];
        }

        if (!empty($this->request->param('account_name'))){
            $map['a.account_name'] = ['like', '%' . $this->request->param('account_name') . '%'];
        }
        
//        $map['a.is_delete'] = 0;
        if (empty($this->request->param('is_delete'))){
            $map['a.is_delete'] = 0;
        }else{
            $map['a.is_delete'] = 1;
        }

        if (is_admin_login() != 1){
            $map['b.admin_id'] = is_admin_login();
        }
        
        $status = $request->param('status', -1);
        ($status != -1) && $map['a.status'] = $status;
        $username = $this->request->param('username');
        if ($username) {
            $map['b.username'] = $username;
        }
        if(!empty($this->request->param('code_type'))){
//            unset($where['p.add_time']);
            $map['c.code']= ['eq', $this->request->param('code_type')];
        
        }
        $map['b.status'] = ['neq',-1];
        $data = $this->logicEwmPayCodes->getCodeList($map, $fields, 'id desc', false);

        // 获取当前时间
                $currentTime = time();

        // 计算10分钟前的时间
                $tenMinutesAgo = $currentTime - (10 * 60);

        foreach ($data as $k=>$v) {
            //今日成功
            $data[$k]['today_payment_ok_amount'] = Db::name('ewm_order')->where(['code_id'=>$v['id'],'status'=>1])->whereTime('add_time', 'today')->sum('order_price');
            //进单总额（今）
            $data[$k]['today_payment_all_amount'] = Db::name('ewm_order')->where(['code_id'=>$v['id']])->whereTime('add_time', 'today')->sum('order_price');


            $data[$k]['10min_payment_ok_count'] = Db::name('ewm_order')->where(['code_id'=>$v['id'],'status'=>1])
                ->where('add_time', '>=', date('Y-m-d H:i:s', $tenMinutesAgo))
                ->where('add_time', '<=', date('Y-m-d H:i:s', $currentTime))->count('id');

            $data[$k]['10min_payment_all_count'] = Db::name('ewm_order')->where(['code_id'=>$v['id']])
                ->where('add_time', '>=', date('Y-m-d H:i:s', $tenMinutesAgo))
                ->where('add_time', '<=', date('Y-m-d H:i:s', $currentTime))->count('id');

// 计算成功率
            $successRate = ($data[$k]['10min_payment_all_count'] > 0) ? (sprintf("%.2f",$data[$k]['10min_payment_ok_count'] / $data[$k]['10min_payment_all_count'] * 100 )) : 0;

            $data[$k]['10min_payment_success_rate'] = $successRate.'%';
        }

        $count = $this->logicEwmPayCodes->getCodeCount($map);
        $this->result($data || !empty($data) ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg' => '',
                'count' => $count,
                'data' => $data,
            ] : [
                'code' => CodeEnum::ERROR,
                'msg' => '暂无数据',
                'count' => $count,
                'data' => $data
            ]
        );
    }






    /**
     * @param Request $request
     * @return mixed
     */
    public function getuidLists_old(Request $request)
    {
        $map = [];
        $account_name = $request->param('account_name', 0, 'intval');
        $account_name && $map['a.account_name'] = ['like', '%' . $account_name . '%'];
        $map['c.code'] = ['in',['alipayUid','alipayUidSmall']];
        $map['a.is_delete'] = 0;
        if (is_admin_login() != 1){
            $map['b.admin_id'] = is_admin_login();
        }
        $status = $request->param('status', -1);
        ($status != -1) && $map['a.status'] = $status;
        $username = $this->request->param('username');
        if ($username) {
            $map['b.username'] = $username;
        }
        if(!empty($this->request->param('code_type'))){
//            unset($where['p.add_time']);
            $map['c.code']= ['eq', $this->request->param('code_type')];

        }
        $fields = ['a.*', 'b.username','c.name'];
        $data = $this->logicEwmPayCodes->getCodeList($map, $fields, 'id desc', false);

        $count = $this->logicEwmPayCodes->getCodeCount($map);
        $this->result($data || !empty($data) ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg' => '',
                'count' => $count,
                'data' => $data,
            ] : [
                'code' => CodeEnum::ERROR,
                'msg' => '暂无数据',
                'count' => $count,
                'data' => $data
            ]
        );
    }
    
    public function getuidLists()
    {
        return $this->getPayCodesOrUidLists('uid');
    }


    public function alipaycodeList(){
        return $this->payCodesAndUidList('alipayCode');
    }

    public function qqcodeList(){
        return $this->payCodesAndUidList('QqCode');
    }

    public function getalipaycodeLists()
    {
        return $this->getPayCodesOrUidLists('alipayCode');
    }

    public function getQqCodeLists()
    {
        return $this->getPayCodesOrUidLists('QqCode');
    }

    public function wechatCodeList(){
        return $this->payCodesAndUidList('wechatCode');
    }

    public function wechatGoldRedList(){
        return $this->payCodesAndUidList('wechatGoldRed');
    }

    public function DingDingGroupList(){
        return $this->payCodesAndUidList('DingDingGroup');
    }


    public function getwechatCodeLists()
    {
        return $this->getPayCodesOrUidLists('wechatCode');
    }

    public function getwechatGoldRedLists()
    {
        return $this->getPayCodesOrUidLists('wechatGoldRed');
    }


    public function getDingDingGroupLists()
    {
        return $this->getPayCodesOrUidLists('DingDingGroup');
    }

    public function douyinGroupRedList(){
        return $this->payCodesAndUidList('douyinGroupRed');
    }


    public function getdouyinGroupRedLists()
    {
        return $this->getPayCodesOrUidLists('douyinGroupRed');
    }


    public function wechatGroupRedList(){
        return $this->payCodesAndUidList('wechatGroupRed');
    }


    public function getwechatGroupRedLists()
    {
        return $this->getPayCodesOrUidLists('wechatGroupRed');
    }

    public function CnyNumberList(){
        return $this->payCodesAndUidList('CnyNumber');
    }

    public function AppletProductsList(){
        return $this->payCodesAndUidList('AppletProducts');
    }


    public function QQFaceRedList(){
        return $this->payCodesAndUidList('QQFaceRed');
    }

    public function getCnyNumberLists()
    {
        return $this->getPayCodesOrUidLists('CnyNumber');
    }


    public function getAppletProductsLists()
    {
        return $this->getPayCodesOrUidLists('AppletProducts');
    }


    public function getQQFaceRedLists()
    {
        return $this->getPayCodesOrUidLists('QQFaceRed');
    }


    public function JDECardList(){
        return $this->payCodesAndUidList('JDECard');
    }


    public function taoBaoDirectPayList(){
        return $this->payCodesAndUidList('taoBaoDirectPay');
    }


    public function alipayWapList(){
        return $this->payCodesAndUidList('alipayWap');
    }

    public function HuiYuanYiFuKaList(){
        return $this->payCodesAndUidList('HuiYuanYiFuKa');
    }

    public function LeFuTianHongKaList(){
        return $this->payCodesAndUidList('LeFuTianHongKa');
    }

    public function JunWebList(){
        return $this->payCodesAndUidList('JunWeb');
    }



    public function alipayF2FList(){
        return $this->payCodesAndUidList('alipayF2F');
    }

    public function alipayPinMoneyList(){
        return $this->payCodesAndUidList('alipayPinMoney');
    }


    public function usdtTrcList(){
        return $this->payCodesAndUidList('usdtTrc');
    }

    public function alipaySmallPurseList(){
        return $this->payCodesAndUidList('alipaySmallPurse');
    }

    public function taoBaoMoneyRedList(){
        return $this->payCodesAndUidList('taoBaoMoneyRed');
    }


    public function alipayTransferCodeList(){
        return $this->payCodesAndUidList('alipayTransferCode');
    }

    public function alipayWorkCardBigList(){
        return $this->payCodesAndUidList('alipayWorkCardBig');
    }

    public function CnyNumberAutoList(){
        return $this->payCodesAndUidList('CnyNumberAuto');
    }


    public function QianxinTransferList(){
        return $this->payCodesAndUidList('QianxinTransfer');
    }


    public function taoBaoEcardList(){
        return $this->payCodesAndUidList('taoBaoEcard');
    }


    public function alipayCodeSmallList(){
        return $this->payCodesAndUidList('alipayCodeSmall');
    }

    public function alipayPassRedList(){
        return $this->payCodesAndUidList('alipayPassRed');
    }


    public function AggregateCodeList(){
        return $this->payCodesAndUidList('AggregateCode');
    }

    public function alipayTransferList(){
        return $this->payCodesAndUidList('alipayTransfer');
    }

    public function alipayWorkCardList(){
        return $this->payCodesAndUidList('alipayWorkCard');
    }

    public function getJDECardLists()
    {
        return $this->getPayCodesOrUidLists('JDECard');
    }


    public function gettaoBaoDirectPayLists()
    {
        return $this->getPayCodesOrUidLists('taoBaoDirectPay');
    }

    public function getalipayWapLists()
    {
        return $this->getPayCodesOrUidLists('alipayWap');
    }

    public function getHuiYuanYiFuKaLists()
    {
        return $this->getPayCodesOrUidLists('HuiYuanYiFuKa');
    }

    public function getLeFuTianHongKaLists()
    {
        return $this->getPayCodesOrUidLists('LeFuTianHongKa');
    }

    public function getJunWebLists()
    {
        return $this->getPayCodesOrUidLists('JunWeb');
    }

    public function getalipayF2FLists()
    {
        return $this->getPayCodesOrUidLists('alipayF2F');
    }

    public function getalipayPinMoneyLists()
    {
        return $this->getPayCodesOrUidLists('alipayPinMoney');
    }

    public function getusdtTrcLists()
    {
        return $this->getPayCodesOrUidLists('usdtTrc');
    }


    public function getalipaySmallPurseLists()
    {
        return $this->getPayCodesOrUidLists('alipaySmallPurse');
    }

    public function gettaoBaoMoneyRedLists()
    {
        return $this->getPayCodesOrUidLists('taoBaoMoneyRed');
    }


    public function getalipayTransferCodeLists()
    {
        return $this->getPayCodesOrUidLists('alipayTransferCode');
    }


    public function getalipayWorkCardBigLists()
    {
        return $this->getPayCodesOrUidLists('alipayWorkCardBig');
    }

    public function getCnyNumberAutoLists()
    {
        return $this->getPayCodesOrUidLists('CnyNumberAuto');
    }

    public function getQianxinTransferLists()
    {
        return $this->getPayCodesOrUidLists('QianxinTransfer');
    }

    public function gettaoBaoEcardLists()
    {
        return $this->getPayCodesOrUidLists('taoBaoEcard');
    }


    public function getalipayCodeSmallLists()
    {
        return $this->getPayCodesOrUidLists('alipayCodeSmall');
    }



    public function getalipayPassRedLists()
    {
        return $this->getPayCodesOrUidLists('alipayPassRed');
    }


    public function getAggregateCodeLists()
    {
        return $this->getPayCodesOrUidLists('AggregateCode');
    }

    public function getalipayTransferLists()
    {
        return $this->getPayCodesOrUidLists('alipayTransfer');
    }


    public function getalipayWorkCardLists()
    {
        return $this->getPayCodesOrUidLists('alipayWorkCard');
    }



    /**
     * 导出
     */
    public function exportOrder()
    {
        //  set_time_limit(0);
        // ini_set('max_execution_time', '5000');
        // ini_set('memory_limit', '4096M');
        //组合搜索
        //状态
        //状态

      
        if ($this->request->param('pay_code_id')){
            $where['b.code_type'] = ['in', $this->request->param('pay_code_id')];
        }
     
        if ($this->request->param('status') != "") {
            $where['a.status'] = ['eq', $this->request->param('status')];
        }
        !empty($this->request->param('order_no')) && $where['order_no']
            = ['eq', $this->request->param('order_no')];
        //时间搜索  时间戳搜素
        $where['add_time'] = $this->parseRequestDate3();


        if (!empty($this->request->param('username'))){
            if (is_admin_login() != 1){
                $admin_id = Db::name('ms')->where('username',$this->request->param('username'))->value('admin_id');
                if ($admin_id == is_admin_login()){
                    $where['c.username'] = ['eq', $this->request->param('username')];
                }
            }
        }

        !empty($this->request->param('amount')) && $where['order_pay_price']
            = ['eq', $this->request->param('amount')];

        !empty($this->request->param('account_name')) && $where['b.account_name']
            = ['eq', $this->request->param('account_name')];

        !empty($this->request->param('pay_username')) && $where['pay_username']
            = ['eq', $this->request->param('pay_username')];

        !empty($this->request->param('visite_ip')) && $where['visite_ip']
            = ['eq', $this->request->param('visite_ip')];

        !empty($this->request->param('uid')) && $where['u.uid']
            = ['eq', $this->request->param('uid')];

        if (is_admin_login() != 1){
            $where['c.admin_id'] = is_admin_login();
            $where['u.admin_id'] = is_admin_login();
        }
        $fields = ['a.*', 'b.account_name', 'b.bank_name', 'account_number', 'c.username','u.username as uname'];
        $orderList = $this->logicEwmOrder->getOrderList($where, $fields, 'add_time desc', false);


        //组装header 响应html为execl 感觉比PHPExcel类更快
        $orderStatus =['等待支付','支付完成','支付失败','已过期'];
        $strTable ='<table width="500" border="1">';
        $strTable .= '<tr>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">ID标识</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">订单号</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">所属商户</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">所属码商</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">支付用户【商户上报】</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">订单金额</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">支付金额</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">收款信息</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">访问信息</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">创建时间</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">支付时间</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">付款人姓名</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">订单状态</td>';
        $strTable .= '</tr>';
        if(is_array($orderList)){
            foreach($orderList as $k=>$val){
                $strTable .= '<tr>';
                $strTable .= '<td style="text-align:center;font-size:12px;">&nbsp;'.$val['id'].'</td>';
                $strTable .= '<td style="text-align:center;font-size:12px;">&nbsp;'.$val['order_no'].'</td>';
                $strTable .= '<td style="text-align:center;font-size:12px;">&nbsp;'.$val['uname'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['username'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['pay_user_name'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['order_price'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['order_pay_price'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'. '账户:' . $val['account_name' ].' 银行:'.$val['bank_name' ]. ' 卡号:'.$val['account_number'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.'IP:' . $val['visite_ip']. ' 设备:' .$val['visite_clientos'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.date('Y-m-d H:i:s', $val['add_time']).'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.date('Y-m-d H:i:s', $val['pay_time']).'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['pay_username'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$orderStatus[$val['status']].'</td>';
                $strTable .= '</tr>';
                unset($orderList[$k]);
            }
        }
        $strTable .='</table>';
        downloadExcel($strTable,'order');
    }

    public function searchBalanceCal(Request $request)
    {
        if (is_admin_login() != 1){
            $map['b.admin_id'] = is_admin_login();
        }
        $uid = $request->param('uid', 0);
        $type = $request->param('type');
        if (!empty($uid))
        {
            $this->assign('uid', $uid);
            $map['b.userid'] = $uid;
        }

        if (!empty($type))
        {
            $this->assign('type', $type);
            $map['a.type'] = $type;
        }
        $map['addtime'] = $this->parseRequestDate3();
        $info = $request->param('info', '', 'trim');
        $info && $map['info'] = ['like', "%{$info}%"];
        $billType = $request->param('bill_type', 0, 'intval');
        $billType && $map['jl_class'] = $billType;
        $username = $request->param('username', '', 'trim');
        $username && $map ['b.username'] = $username;
        $uid = $request->param('uid', '');
        $uid && $map ['b.userid'] = $uid;

        $admin_id = $request->param('admin_id', '');
        $admin_id && $map ['a.admin_id'] = $admin_id;
        list($inc, $dec) = $this->logicMsSomeBill->changAmount($map);
        echo json_encode(['inc' => sprintf("%.2f",$inc), 'dec' => sprintf("%.2f",$dec)]);
    }

    /**
     * 码商流水导出
     */
    public function exportMsBills(Request $request)
    {
//        if (is_admin_login() != 1){
//            $adminSonMs = Db::name('ms')->where('admin_id',is_admin_login())->column('userid');
//            $map['a.uid'] = ['in',$adminSonMs];
//        }
//        //时间搜索  时间戳搜素
//        $map['addtime'] = $this->parseRequestDate3();
//        $billType = $request->param('bill_type', 0, 'intval');
//        $billType && $map['jl_class'] = $billType;
//        $username = $request->param('username', '', 'trim');
//        $username && $map ['b.username'] = $username;
//        $info = $request->param('info', '', 'trim');
//        $info && $map ['a.info'] = ['like', '%' . $info . '%'];
//
//        $uid = $request->param('uid', 0, 'intval');
//        $uid && $map ['a.uid'] = $uid;
//



        //时间搜索  时间戳搜素
        $map['addtime'] = $this->parseRequestDate3();
        $billType = $request->param('bill_type', 0, 'intval');
        $billType && $map['jl_class'] = $billType;
        $username = $request->param('username', '', 'trim');
        $username && $map ['b.username'] = $username;
        $info = $request->param('info', '', 'trim');
        $info && $map ['a.info'] = ['like', '%' . $info . '%'];



        $type = $request->param('type');
        $type && $map['a.type'] = $type;
//
//
//        $fields = ['a.*', 'b.username'];
        if (is_admin_login() != 1){
            $adminSonMs = Db::name('ms')->where('admin_id',is_admin_login())->column('userid');
            $map['b.userid'] = ['in',$adminSonMs];
//            $map['uid'] = ['in',$adminSonMs];
        }
        $uid = $request->param('uid', 0, 'intval');
        $uid && $map ['a.uid'] = $uid;



//print_r($uid);die;










        $fields = ['a.*', 'b.username'];
        $data = $this->logicMsSomeBill->getBillsList($map, $fields, 'addtime desc', false);


        foreach ($data as $key => $item){

        }

        //组装header 响应html为execl 感觉比PHPExcel类更快
        $strTable ='<table width="500" border="1">';
        $strTable .= '<tr>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">ID</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="100">用户名</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">账变类型</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">变动前</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">变动金额</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">变动后</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">流水备注</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">时间</td>';
        $strTable .= '</tr>';

        if(is_array($data)){

            foreach($data as $k=>$val){
                $strTable .= '<tr>';
                $strTable .= '<td style="text-align:center;font-size:12px;">&nbsp;'.$val['id'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['username'].' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['jl_class_text'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['pre_amount'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['num'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['last_amount'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'.$val['info'].'</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">'. date('Y-m-d H:i:s', $val['addtime']) .'</td>';
                $strTable .= '</tr>';
                unset($data[$k]);

            }
        }
        $strTable .='</table>';

        downloadExcel($strTable,'msBills');
    }

    /**
     *解绑此码商的TG群
     */
    public function unblindTgGroup()
    {
        $userId = $this->request->param('userid');
        if (!$userId) {
            $this->error('非法操作');
        }
        $result = \app\common\model\Ms::where(['userid' => $userId])->update(['tg_group_id' => '']);
        if ($result !== false) {
            $this->success('操作成功');
        }
        $this->error('错误请重试');

    }

    /**
     * 拉黑IP
     */
    public function blockIp(Request  $request)
    {
        $this->result($this->logicMs->blockIp($request->param()));
    }


    /**
     * UID商户报表
     */
    public function uidshstatic(){

//        $this->assign('userList',$userList);
        return $this->fetch();
    }

    /**
     * 获取uid订单商户统计列表
     */

    public function getuidshstatic(){
        $where = [];

        if (is_admin_login() != 1){
            $where['admin_id'] = is_admin_login();
        }

        $userList = Db::name('user')->where($where)->field('uid,username')->select();
        if (!empty($this->request->param('username'))){
            $where1['username'] = $this->request->param('username');
            $userList = Db::name('user')->where($where1)->field('uid,username,admin_id')->select();
            if (is_admin_login() != 1){
               if ($userList['admin_id']!= is_admin_login()) {
                   $this->error('非法操作');
               };

            }
        }
        if (empty($userList)){
            $this->error('没有数据啦');
        }

        $start = date('Y-m-d 00:00:00',time());
        $end = date('Y-m-d 23:59:59',time());

        if (!empty($this->request->param('start'))){
            $start = $this->request->param('start');
        }

        if (!empty($this->request->param('end'))){
            $end = $this->request->param('end');
        }
        $where2['c.code'] = ['in',['alipayUid','alipayUidSmall','alipayUidTransfer']];
        if (!empty($this->request->param('code_type'))){
            $where2['c.code'] = $this->request->param('code_type');
        }
        $where2['o.create_time'] = ['between time',[$start,$end]];
        foreach ($userList as $k=>$v){
            $userList[$k]['alipayUid_total'] = Db::name('orders')
                ->alias('o')
                ->join('ewm_order e','o.trade_no = e.order_no')
                ->join('pay_code c','e.code_type = c.id')
                ->where($where2)
                ->where(['o.uid'=>$v['uid']])
                ->sum('e.order_price');
            $userList[$k]['success_money'] = Db::name('orders')
                ->alias('o')
                ->join('ewm_order e','o.trade_no = e.order_no')
                ->join('pay_code c','e.code_type = c.id')
                ->where($where2)
                ->where(['o.uid'=>$v['uid'],'e.status'=>1,'o.status'=>2])
                ->sum('e.order_price');

            $userList[$k]['zongdanliang'] = Db::name('orders')
                ->alias('o')
                ->join('ewm_order e','o.trade_no = e.order_no')
                ->join('pay_code c','e.code_type = c.id')
                ->where($where2)
                ->where(['o.uid'=>$v['uid']])
                ->count('e.id');

            $userList[$k]['chenggongdanliang'] = Db::name('orders')
                ->alias('o')
                ->join('ewm_order e','o.trade_no = e.order_no')
                ->join('pay_code c','e.code_type = c.id')
                ->where($where2)
                ->where(['o.uid'=>$v['uid'],'e.status'=>1,'o.status'=>2])
                ->count('e.id');

            if ($userList[$k]['chenggongdanliang'] == 0 ){
                $userList[$k]['success_rate'] = 0;
            }else{
                $userList[$k]['success_rate'] = sprintf("%.2f",$userList[$k]['chenggongdanliang'] / $userList[$k]['zongdanliang'] * 100);
            }

            if ($userList[$k]['alipayUid_total']== 0 &&  $userList[$k]['success_money'] ==0  && $userList[$k]['zongdanliang'] == 0 && $userList[$k]['chenggongdanliang'] == 0){
                unset($userList[$k]);
            }

        }

//        print_r($userList);die;

        return json(['code'=>0,'data'=>$userList,'count'=>count($userList)]);
    }


    /**
     * 清除绑定Google
     */
    public function clearGoogleAuth()
    {
        $userId = $this->request->param('id');
        if (!$userId) {
            $this->error('非法操作');
        }


        if (is_admin_login() != 1){
            $ms = Db::name('ms')->where('userid',$userId)->value('admin_id');
            if (!$ms || $ms != is_admin_login()){
                $this->error('非法操作');
            }
        }

        $result = \app\common\model\Ms::where(['userid' => $userId])->update(['google_status' => 0, 'google_secretkey' => '']);
        if ($result !== false) {
            action_log('清除码商谷歌', '管理员：' .session('admin_info')['username'].'清除码商： '.$userId.' 谷歌验证码');
            $this->success('操作成功');
        }
        $this->error('错误请重试');

    }



    /**
     * 清除绑定Google
     */
    public function clear_df_ver()
    {
        $userId = $this->request->param('id');
        if (!$userId) {
            $this->error('非法操作');
        }


        if (is_admin_login() != 1){
            $ms = Db::name('ms')->where('userid',$userId)->value('admin_id');
            if (!$ms || $ms != is_admin_login()){
                $this->error('非法操作');
            }
        }
        $reg_date = Db::name('ms')->where('userid',$userId)->value('reg_date');
        Cache::rm('getDaifuOrderList'.$userId);
        Cache::rm('getDaifuOrderList'.$userId.'count');
        Cache::set('getDaifuOrderList'.$userId.$reg_date,0);
        $this->success('操作成功');

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
        $orderWhere['status'] = 1;
//        $orderWhere['m.status'] = 1;
        $start = date('Y-m-d 00:00:00',time());
        $end = date('Y-m-d 23:59:59',time());
        if (!empty($this->request->param('start'))){
            $start = $this->request->param('start');

        }
        if (!empty($this->request->param('end'))){
            $end = $this->request->param('end');
        }


        if (is_admin_login() != 1){
            $adminSonMs = Db::name('ms')->where('admin_id',is_admin_login())->column('userid');
            $orderWhere['gema_userid'] = ['in',$adminSonMs];
            $where['admin_id'] = ['in',$adminSonMs];
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
            if ($ms['pid'] != 0){
                $this->result([
                    'code' => 1,
                    'msg' => '不是团长',
                    'count' => 0,
                    'data' => ''
                ]);
            }
            $this->son_id = [];
            $mss = $this->getIds($ms['userid']);

            array_unshift($mss, $ms['userid']);
//                        print_r($mss);die;
            $orderWhere['gema_userid'] = ['in',$mss];
            $where['username'] = $this->request->param('username');
        }

        $orderWhere['add_time'] = ['between time',[$start,$end]];
        $order = Db::name('ewm_order')->alias('o')->field('order_price,gema_userid')->where($orderWhere)->select();
        $order = $this->GetRepeatValGroup($order,'gema_userid');
        $ms = [];
        foreach ($order as $k=>$v){
            $ms[$k]['total_amount'] = array_sum(array_column($v,'order_price'));
        }
        $mslist = [];
        foreach ($ms as $k=>$v){
            $mslist[$k]['username'] = Db::name('ms')->where('userid',$k)->value('username');
            $mslist[$k]['pid'] = Db::name('ms')->where('userid',$k)->value('pid');
            $mslist[$k]['total_amount'] = $v['total_amount'];
            $mslist[$k]['userid'] = $k;
            $mslist[$k]['uid_rate'] = Db::name('ms_rate')->where(['ms_id'=>$k,'code_type_id'=>32])->value('rate');
        }

        foreach ($mslist as $k=>$v){
            if ($v['pid'] != 0){
                $ffmsid = getNavPid($k);
                if(array_key_exists($ffmsid,$mslist)){
                    $mslist[$ffmsid]['total_amount'] = $mslist[$ffmsid]['total_amount'] + $v['total_amount'];
                }else{
                    $mslist[$ffmsid]['total_amount'] = 0 + $v['total_amount'];
                    $mslist[$ffmsid]['username'] = Db::name('ms')->where('userid',$ffmsid)->value('username');
                    $mslist[$ffmsid]['pid'] = 0;
                    $mslist[$ffmsid]['userid'] = $ffmsid;
                    $mslist[$ffmsid]['uid_rate'] = Db::name('ms_rate')->where(['ms_id'=>$ffmsid,'code_type_id'=>32])->value('rate');
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
            if ($v['pid'] != 0){
                $ffmsid = getNavPid($v['pid']);
                foreach ($data as $key=>$val){
                    if ($val['userid'] ==$ffmsid){
                        $data[$key]['money'] = $val['money'] + $v['money'];
                    }
                }
            }
            foreach ($mslist as $key=>$val){
                if ($key == $v['userid']){
                    $data[$k]['total_amount'] = $val['total_amount'];
                }
            }
        }

//        print_r($topMs);die;
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


    public function add_ms_callback_ip(Request $request){
        $this->result($this->logicMs->add_ms_callback_ip($request->param()));
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

    /**
     * 团队统计
     */
//    public function team(){
//        //查询今日完成订单
//        $orderWhere = [];
//        $orderWhere['status'] = 1;
//        $start = date('Y-m-d 00:00:00',time());
//        $end = date('Y-m-d 23:59:59',time());
//        $orderWhere['add_time'] = ['between time',[$start,$end]];
//        $order = Db::name('ewm_order')->where($orderWhere)->select();
////        $msWhere['status'] = 1;
////        if (is_admin_login() != 1){
////            $msWhere['admin_id'] = is_admin_login();
////        }
////        $msList = Db::name('ms')->where($msWhere)->field('userid,username,pid,admin_id,money')->select();
////        foreach ($msList as $key=>$val){
////            foreach ($order as $k=>$v){
////                if ($v['userid'] == $val['gema_userid']){
////
////                }
////            }
////        }
//        $order = $this->GetRepeatValGroup($order);
//        print_r($order);die;
//
//
//
//    }

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

    /*
     * 一键设置码商代付费率
     */
//
//    public function batchSetDfRate()
//    {
//        $admin_id = is_admin_login();
//        $bank_rate = $this->request->post('bank_rate');
//        if ( empty($bank_rate) or !is_numeric($bank_rate)){
//            $this->error('费率不合法！');
//        }
//        $ret = $this->modelMs->where('admin_id', $admin_id)->update([
//            'bank_rate' => $bank_rate
//        ]);
//        if ($ret){
//           $this->success('设置成功');
//        }else{
//            $this->error('设置失败');
//        }
//
//    }
//

//    public function batchSetPayRate()
//    {
//        if ($this->request->isPost()){
//            $data = $this->request->post('r/a');
//            //获取admin下面的所有码商
//            $mss_wh = [
//                'admin_id' => is_admin_login(),
//                'status' => 1
//            ];
//            $mss = $this->modelMs->where($mss_wh)->select();
//
//            foreach ($mss as $ms){
//                $ms_id = $ms['userid'];
//                if (is_admin_login() != 1){
//                    $admin_id =  Db::name('ms')->where('userid',$ms_id)->value('admin_id');
//                    if ($admin_id != is_admin_login()){
//                        return json(['code'=>0,'msg'=>'非法请求']);
//                    }
//                }
//                foreach ($data as  $key=>$item) {
//                    //没有设置费率，不批量设置
//                    if (empty($item['ms_rate']) or !is_numeric($item['ms_rate'])){
//                        continue;
//                    }
//                    $res = Db::name('ms_rate')->where(['ms_id'=>$ms_id,'code_type_id'=>$item['code_type_id']])->select();
//                    if ($res){
//                        //修改
//                        Db::name('ms_rate')->where(['ms_id'=>$ms_id,'code_type_id'=>$item['code_type_id']])->update(['rate' => $item['ms_rate'], 'update_time' => time()]);
//                    }else{
//                        //新增
//                        Db::name('ms_rate')->insert( [
//                            'ms_id' => $ms_id,
//                            'code_type_id' => $item['code_type_id'],
//                            'rate' => $item['ms_rate'],
//                            'create_time' => time(),
//                            'update_time' => time(),
//                        ]);
//                    }
//
//                }
//            }
//            return ['code' => CodeEnum::SUCCESS, 'msg' => '费率配置成功'];
//        }
//        $list = Db::name('pay_code')->where('status',1)->select();
//        $this->assign('list', $list);
//        return $this->fetch('batchSetPayRate');
//    }

    public function toEnableMoney()
    {

        $ms_id = $this->request->param('userid');
        $type = $this->request->param('type');
        
        $ms =  $this->modelMs->lock(true)->where(['userid' => $ms_id, 'admin_id' => is_admin_login()])->find();
        if (empty($ms)){
            return json(['code' => CodeEnum::ERROR, 'msg' => '非法操作']);
        }

        if (in_array($type, ['disable', 'daifu_enable']) == false){
            return json(['code' => CodeEnum::ERROR, 'msg' => '非法操作']);
        }

        Db::startTrans();
        try {

            if ($type == 'disable'){
                if ( $ms['disable_money'] > 0){
                    accountLog($ms_id, MsMoneyType::DISABLE_TO_ENABLE, 1, $ms['disable_money'] , '管理员解冻余额','enable');
                    accountLog($ms_id, MsMoneyType::DISABLE_TO_ENABLE, 0, $ms['disable_money'] , '管理员清零冻结余额', 'disable');
                }
            }else{
                if ( $ms['daifu_money'] > 0){
                    accountLog($ms_id, MsMoneyType::DAIFU_MONEY_TO_ENABLE, 1, $ms['daifu_money'] , '管理员解冻代付余额','enable');
                    accountLog($ms_id, MsMoneyType::DAIFU_MONEY_TO_ENABLE, 0, $ms['daifu_money'] , '管理员清零代付余额', 'daifu_enable');
                }
            }
           
            Db::commit();
        }catch (Exception $e){
            Db::rollback();
            \think\Log::error('解除码商冻结余额:' . $e->getMessage());
           return  json(['code' => CodeEnum::ERROR, 'msg' => '操作失败']);
        }
        return json(['code' => CodeEnum::SUCCESS, 'msg' => '操作成功']);
    }


//    public function enableToMoney()
//    {
//
//        $ms_id = $this->request->param('userid');
//        $ms =  $this->modelMs->lock(true)->where(['userid' => $ms_id, 'admin_id' => is_admin_login()])->find();
//        if (empty($ms)){
//            return json(['code' => CodeEnum::ERROR, 'msg' => '非法操作']);
//        }
//
//        Db::startTrans();
//        try {
//            if ( $ms['disable_money'] > 0){
//                accountLog($ms_id, MsMoneyType::DISABLE_TO_ENABLE, 1, $ms['disable_money'] , '管理员解冻余额','enable');
//                accountLog($ms_id, MsMoneyType::DISABLE_TO_ENABLE, 0, $ms['disable_money'] , '管理员清零冻结余额', 'disable');
//            }
//            Db::commit();
//        }catch (Exception $e){
//            Db::rollback();
//            \think\Log::error('解除码商冻结余额:' . $e->getMessage());
//            return  json(['code' => CodeEnum::ERROR, 'msg' => '操作失败']);
//        }
//        return json(['code' => CodeEnum::SUCCESS, 'msg' => '操作成功']);
//    }

    /**
     * 管理员禁用码商接单（包括子孙级）
     */
    public function change_admin_work_status()
    {
        $userid = $this->request->param('userid');
        $type = $this->request->param('type');
        if (!in_array($type, [0,1])){
            $this->error('操作不合法');
        }
        $where =[
            'userid' =>   $userid,
            'admin_id' => is_admin_login(),
            'admin_work_status' => $type ? 0 : 1
        ];

        $ms = $this->modelMs->where($where)->find();
        if (empty($ms)){
            $this->error('操作不合法');
        }

//        $son_mss = $this->logicMs->getSonMsIds($userid);
//        array_push($son_mss, (int)$userid);
//        $up_where = [
//            'admin_id' => is_admin_login(),
//            'userid' => ['in', $son_mss]
//        ];

        $this->modelMs->where('userid',$userid)->update([
            'admin_work_status' => $type,
//            'work_status' => $type
        ]);

        $this->success('操作成功');

    }

    /**
     * 总后台上传卡密
     */
    public function updateCardKey()
    {
        $order_id = $this->request->param('order_id');
        $cardKey = $this->request->param('cardKey', '');

        if (empty($cardKey)){
            $this->error('卡密不能为空');
        }

        $order = $this->modelEwmOrder->where(['id'=>$order_id,'status'=>['in',[0,3]]])->find();
        if (empty($order)){
            $this->error('订单不存在');
        }

        if ($order['cardKey']){
            $this->error('订单卡密已存在！');
        }


        $where =[
            'userid' =>   $order['gema_userid'],
            'admin_id' => is_admin_login(),
        ];

        $ms = $this->modelMs->where($where)->find();
        if (empty($ms)){
            $this->error('操作不合法');
        }

        $this->modelEwmOrder->where('id', $order_id)->update(['cardKey' => $cardKey,'add_time'=>time(),'status'=>0]);


        $this->success('上传成功');

    }


    /**
     * 详情
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function details()
    {
        $orderInfo = $this->modelEwmOrder->find($this->request->param('id', '0'));
//halt($orderInfo);
        if (empty($orderInfo)){
            $this->error('数据错误');
        }

//        $where['id'] = $this->request->param('id', '0');
        $where['trade_no'] = $orderInfo['order_no'];

//        halt($where);


        //订单
        $order = $this->logicOrders->getOrderInfo($where);
        if (is_admin_login() != 1){
            $user = Db::name('user')->where('uid',$order['uid'])->find();
            if (is_admin_login() != $user['admin_id']){
                $this->error('非法请求');
            }
        }
        $notify = [];
        //当支付成功的时候才会看有没有回调成功
        if ($order['status'] == '2') {
            //回调
            $notify = $this->logicOrders->getOrderNotify(['order_id' => $order['id']]);
        }

        $this->assign('order', $order);
        $this->assign('notify', $notify);

        return $this->fetch();
    }

    public function decodeImagePath()
    {
        $encryStr = $this->request->param('encryStr');
        return  json(['data'=>openssl_decrypt(base64_decode($encryStr), "AES-128-CBC", '8e70f72bc3f53b12', 1, '99b538370c7729c7')]) ;
    }

    /**
     * 移分功能
     */
    public function yifen()
    {
        $current_ms_id = $this->request->param('current_ms_id');
        $ms_account = $this->request->param('username');
        $amount = $this->request->param('amount');

        if (empty($ms_account) or empty($amount) or empty($current_ms_id)){
            $this->error('码商用户名或金额不能为空');
        }

        if (!preg_match('/^[1-9]\d*(\.\d+)?$/', $amount)) {
            $this->error('请输入正确的金额');
        }

        $current_ms = $this->modelMs->where(['userid' => $current_ms_id])->find();

        if (empty($current_ms)){
            $this->error('码商信息不正确');
        }

        $ms = $this->modelMs->where(['account' => $ms_account])->find();


        if (empty($ms)){
            $this->error('码商用户名不存在');
        }else{
            if ($ms->status != 1){
                $this->error('该码商已经被禁用');
            }
        }

        if ($current_ms['userid'] == $ms['userid']){
            $this->error('不能给自己移分');
        }

        if (is_admin_login() !==1 ){
            if (is_admin_login() !== $current_ms->admin_id or is_admin_login()!= $ms->admin_id){
                $this->error('非法操作');
            }
        }

        if ($current_ms['money'] < $amount){
            $this->error('当前码商没有足够的余额可以进行移分');
        }

        Db::startTrans();

        try {
            $infos = '管理员['. is_admin_login() .']手动移分,从码商（'.$current_ms['username'].'）移分给码商（'.$ms['username'].'),增加余额' . $amount;
            accountLog($ms['userid'], MsMoneyType::TRANSFER, 1, $amount, $infos);

            $info = '管理员['. is_admin_login() .']手动移分,从码商（'.$current_ms['username'].'）移分给码商（'.$ms['username'].'),扣减余额' . $amount;
            accountLog($current_ms['userid'], MsMoneyType::TRANSFER, 0, $amount, $info);
            Db::commit();
        }catch (\Exception $e){
            Db::rollback();
            return json(['code' => CodeEnum::ERROR, 'msg'=>$e->getMessage()]);
        }

        return json(['code' => CodeEnum::SUCCESS, 'msg'=>'移分成功']);


    }

    public function msTeamStats()
    {
        $pay_code = Db::name('pay_code')->cache(true,600)->where(['status'=>1,'id'=>['lt',255]])->select();
        $this->assign('pay_code',$pay_code);
        return $this->fetch();
    }

    public function getMsTeamStats(Request $request)
    {
        $where = [];
        if (!empty($request->param('username'))){
            $where['username'] = $request->param('username');
        }

        if (!empty($request->param('level'))){
            $where['level'] = $request->param('level');
        }
        $where_code = [];
        if (!empty($request->param('channel'))){
            $where_code['code_type'] = $request->param('channel');
        }
        /* $sonMsIds = $this->getIds($this->agent_id);

         if (empty($sonMsIds)){
             return json([
                 'code'=>1,
                 'msg'=>'暂无数据'
             ]);
         }*/
//        $where['userid'] = ['in',$sonMsIds];

        if (is_admin_login() != 1){
            $where['admin_id'] = is_admin_login();
        }

//        $limit = $request->param('limit');
//        $page = $request->param('page');
//        $start=$limit*($page-1);
        $sonMsLists = Db::name('ms')->where($where)->order('userid desc')->paginate($this->request->param('limit', 10));
        $sonMsList = $sonMsLists->items();
        foreach($sonMsList as $key=>$val){
            $isAgent = Db::name('ms')->where('pid',$val['userid'])->select();
            if (empty($isAgent)){
                $sonMsList[$key]['team_people_number'] = 0;
                $sonMsList[$key]['yesterday_amount'] = sprintf("%.2f",Db::name('ewm_order')->where($where_code)->whereTime('pay_time', 'yesterday')->where('status', 1)->where('gema_userid',$val['userid'])->sum('order_price'));
                $sonMsList[$key]['today_amount'] = sprintf("%.2f",Db::name('ewm_order')->where($where_code)->whereTime('pay_time', 'today')->where('status', 1)->where('gema_userid',$val['userid'])->sum('order_price'));
                $sonMsList[$key]['yesterday_daifu']= sprintf("%.2f",Db::name('daifu_orders')->whereTime('update_time', 'yesterday')->where('chongzhen',0)->where('status', 2)->where('ms_id',$val['userid'])->sum('amount'));
                $sonMsList[$key]['today_daifu']= sprintf("%.2f",Db::name('daifu_orders')->whereTime('update_time', 'today')->where('chongzhen',0)->where('status', 2)->where('ms_id',$val['userid'])->sum('amount'));
                $sonMsList[$key]['total_ewmcode_num']= $this->modelEwmPayCode->where( 'status',1 )->where('ms_id',$val['userid'])->count();
                $total_orders = $this->modelEwmOrder->whereTime('add_time', 'today')->where($where_code)->where('gema_userid',$val['userid'])->count();
                $success_orders = $this->modelEwmOrder->whereTime('add_time', 'today')->where($where_code)->where('status', 1)->where('gema_userid',$val['userid'])->count();
                $sonMsList[$key]['total_success_rate'] = sprintf("%.1f%s",$total_orders ? $success_orders/$total_orders*100 : 0, '%');
            }else{
                $this->son_id = [];
                $Msteams = $this->getIds($val['userid']);
               /* if ($val['userid'] == 100){
                    halt($Msteams);
                }*/

                $sonMsList[$key]['team_people_number'] = sizeof($Msteams);
                array_unshift($Msteams,$val['userid']);
                $sonMsList[$key]['money'] = sprintf("%.2f",Db::name('ms')->where('userid','in',$Msteams)->sum('money'));
                $sonMsList[$key]['cash_pledge'] = sprintf("%.2f",Db::name('ms')->where('userid','in',$Msteams)->sum('cash_pledge'));
                $sonMsList[$key]['yesterday_amount'] = sprintf("%.2f",Db::name('ewm_order')->where($where_code)->whereTime('pay_time', 'yesterday')->where('status', 1)->where('gema_userid','in',$Msteams)->sum('order_price'));
                $sonMsList[$key]['today_amount'] = sprintf("%.2f",Db::name('ewm_order')->where($where_code)->whereTime('pay_time', 'today')->where('status', 1)->where('gema_userid','in',$Msteams)->sum('order_price'));
                $sonMsList[$key]['yesterday_daifu']= sprintf("%.2f",Db::name('daifu_orders')->whereTime('update_time', 'yesterday')->where('status', 2)->where('chongzhen',0)->where('ms_id','in',$Msteams)->sum('amount'));
                $sonMsList[$key]['today_daifu']= sprintf("%.2f",Db::name('daifu_orders')->whereTime('update_time', 'today')->where('status', 2)->where('chongzhen',0)->where('ms_id','in',$Msteams)->sum('amount'));
                //当日成功率
                $total_orders = $this->modelEwmOrder->whereTime('add_time', 'today')->where($where_code)->where('gema_userid', 'in',$Msteams)->count();
                $success_orders = $this->modelEwmOrder->whereTime('add_time', 'today')->where($where_code)->where('status', 1)->where('gema_userid', 'in',$Msteams)->count();
                $sonMsList[$key]['total_success_rate'] = sprintf("%.1f%s",$total_orders ? $success_orders/$total_orders*100 : 0, '%');
                //收款码数量
                $sonMsList[$key]['total_ewmcode_num']= $this->modelEwmPayCode->where( 'status' ,1 )->where('ms_id','in',$Msteams)->count();
            }

           /* if ($val['pid'] == $this->agent_id){
                // 搜索关键词
                $searchKeyword ='子码商（ID：'.$val['userid'].'）-【'.$val['username'].'】产生交易订单';
                $sonMsList[$key]['son_income'] = sprintf("%.2f",Db::name('ms_somebill')->where(['jl_class'=>9, 'uid'=>$this->agent_id])
                    ->where('info', 'like', '%' . $searchKeyword . '%')->whereTime('addtime','today')->sum('num'));
            }*/

        }

        if ($sonMsLists->total()>0){
            return json([
                'code'=>1,
                'msg'=>'请求成功',
                'data'=>$sonMsList,
                'count'=>$sonMsLists->total()
            ]);
        }else{
            return json([
                'code'=>0,
                'msg'=> '暂无数据',
                'data'=>[],
                'count'=>0
            ]);
        }

    }

    /**
     * 关闭订单
     */
    public function closeorder()
    {
        $id = $this->request->param('id');

        Db::startTrans();
        try {
            $ewmOrderInfo = $this->modelEwmOrder->lock(true)->where('id', $id)->find();
            if (empty($ewmOrderInfo)){
                throw new \Exception('订单不存在');
            }

            if ($ewmOrderInfo->status != '0'){
                throw new \Exception('当前订单不是待支付订单，请刷新页面');
            }

            $admin_sys_disable_ms_money = getAdminPayCodeSys('disable_ms_money_status', 256, is_admin_login());
            if (empty($admin_sys_disable_ms_money)) {
                $admin_sys_disable_ms_money = 2;
            }
            if ($admin_sys_disable_ms_money == 1) {
                if (accountLog($ewmOrderInfo['gema_userid'], MsMoneyType::ORDER_DEPOSIT_BACK, MsMoneyType::OP_ADD, $ewmOrderInfo['order_price'], $ewmOrderInfo['out_trade_no']) === false) {
                    throw new \Exception('管理员关闭订单，返回码商可用金额失败，订单号： '. $ewmOrderInfo['out_trade_no']);
                }
                if (accountLog($ewmOrderInfo['gema_userid'], MsMoneyType::ORDER_DEPOSIT_BACK,MsMoneyType::OP_SUB, $ewmOrderInfo['order_price'], $ewmOrderInfo['out_trade_no'], 'disable') === false) {
                    throw new \Exception('管理员关闭订单，扣除码商冻结金额失败，订单号： '. $ewmOrderInfo['out_trade_no']);
                }
            }
            // 更新 ewm_orders 表
            $ewmOrderInfo->status = 3;
            $ewmOrderInfo->save();
            Log::notice('管理员关闭订单操作成功，订单号： '.$ewmOrderInfo['out_trade_no']);

            Db::commit();
        }catch (\Exception $e){
            Db::rollback();
            \think\Log::error('总后台关闭订单错误：' . $e->getMessage());
            $this->error($e->getMessage());
        }

        $this->success('关闭订单成功');

    }

    public function viewSuperiorMs()
    {
        $orderId = $this->request->param('id');

        $orderInfo = $this->modelEwmOrder->where('id', $orderId)->find();

        if (!$orderInfo){
            $this->error('数据错误');
        }

        $ms_id = $orderInfo->gema_userid;

        if (!$ms_id){
            $this->error('未查询到上级码商信息');
        }

        $msInfo = $this->modelMs->where('userid', $ms_id)->find();

        if (!$msInfo){
            $this->error('码商不存在');
        }

        if ($msInfo->pid == 0){
            $this->error('当前无上级码商');
        }

        $fatherMsIds = $this->getFatherMsIds($msInfo->userid);

        $fatherMs = $this->modelMs->where('userid', 'in', $fatherMsIds)->field('userid,username,level')->select();
        $this->assign('faterMsList', $fatherMs);

        return $this->fetch();
    }


    private function getFatherMsIds($id){
        $ids = [];
        $pid = $id;
        while ($pid > 0) {
            $pid = Db::name('ms')->where('userid', $pid)->value('pid');
            if ($pid > 0) {
                $ids[] = $pid;
            }
        }
        return $ids;
    }




    /** 码商类型通道管理页面
     * @author luomu
     * @time 2023-5-6
     */
    public function MschannelList(Request $request){
        $merchantId = $request->param('merchant_id');
        if ($this->request->isPost()){
            $data = $this->request->post('r/a');
            if (is_array($data)){
                foreach ($data as  $key=>$item) {
                    $vadata['status'] = $item['status'];
                    $vadata['min_money'] = $item['min_money'];
                    $vadata['max_money'] = $item['max_money'];
                    $vadata['ms_id'] = $item['ms_id'];
                    $validate = new Validate([
                        'status'  => 'require|number',
                        'min_money'  => 'require|float|egt:0',
                        'max_money' => 'require|float|egt:0',
                        'ms_id' => 'require|number',
                    ],[
                        'min_money.require' => '金额必须填写',
                        'min_money.float' => '金额必须是数字',
                        'min_money.gt' => '金额必须大于等于0',
                        'max_money.require' => '金额必须填写',
                        'max_money.float' => '金额必须是数字',
                        'max_money.gt' => '金额必须大于等于0',
                    ]);

                    if (!$validate->check($vadata)) {
//            $this->error($validate->getError());
                        return json(['code'=>0,'msg'=>$validate->getError()]);
                    }

                    if (is_admin_login() != 1){
                        $admin_id =  Db::name('ms')->where('userid',$item['ms_id'])->value('admin_id');
                        if ($admin_id != is_admin_login()){
                            return json(['code'=>0,'msg'=>'非法请求']);
                        }
                    }


                    $res = Db::name('ms_channel')->where(['ms_id'=>$item['ms_id'],'pay_code_id'=>$item['pay_code_id']])->find();
                    if ($res){
                        //修改
                        Db::name('ms_channel')->where(['ms_id'=>$item['ms_id'],'pay_code_id'=>$item['pay_code_id']])->update(['min_money' => sprintf("%.2f",$item['min_money']), 'max_money' => sprintf("%.2f",$item['max_money']),'status'=>$item['status']]);
                    }else{
                        //新增
                        Db::name('ms_channel')->insert( [
                            'ms_id' => $item['ms_id'],
                            'pay_code_id' => $item['pay_code_id'],
                            'min_money' => sprintf("%.2f",$item['min_money']),
                            'max_money' => sprintf("%.2f",$item['max_money']),
                            'status' =>$item['status'],
                        ]);
                    }

                }
                return ['code' => CodeEnum::SUCCESS, 'msg' => '配置成功'];
            }
        }


        if($merchantId<1){
            $this->result([
                'code' => CodeEnum::ERROR,
                'msg'=> '非法请求',
            ]);
        }

        if (is_admin_login() != 1){
            $admin_id = model('ms')->where('userid',$merchantId)->value('admin_id');
            if ($admin_id != is_admin_login()){
                $this->result([
                    'code' => CodeEnum::ERROR,
                    'msg'=> '非法请求',
                ]);
            }
        }

        $channelList =model('pay_code')
            ->where(['status'=>1])
            ->order('id desc')
            ->select();

        foreach($channelList as $k=>$v){
            $status = Db::name('ms_channel')->where(['pay_code_id'=>$v['id'],'ms_id'=>$merchantId])->value('status');
            $channelList[$k]['isSwitch'] = $status !== null ? $status : 1;
            $channelList[$k]['min_money'] = Db::name('ms_channel')->where(['pay_code_id'=>$v['id'],'ms_id'=>$merchantId])->value('min_money') ? Db::name('ms_channel')->where(['pay_code_id'=>$v['id'],'ms_id'=>$merchantId])->value('min_money') : '0.00';
            $channelList[$k]['max_money'] = Db::name('ms_channel')->where(['pay_code_id'=>$v['id'],'ms_id'=>$merchantId])->value('max_money') ? Db::name('ms_channel')->where(['pay_code_id'=>$v['id'],'ms_id'=>$merchantId])->value('max_money') : '0.00';
        }
        //p($channelList);

        return view('ms_channel',['channelList'=>$channelList]);
    }


    /**
     * 回收站
     */
    public function recycle(){
        return $this->fetch();
    }

    public function onlineCode(){
        return $this->fetch('onlineCode');
    }


    public function getOnlineCode(){
        $code_id = $this->request->param('pay_code');
        $ms_id = $this->request->param('userid');

//        print_r($code_id);die;
        $data = $this->modelEwmPayCode->where(['ms_id'=>$ms_id,'is_delete'=>0,'code_type'=>$code_id,'status'=>1])->paginate(input('limit'));

        $this->result($data->total() > 0 ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg' => '',
                'count' => $data->total(),
                'data' => $data->items(),
            ] : [
                'code' => CodeEnum::ERROR,
                'msg' => '暂无数据',
                'count' => 0,
                'data' =>[]
            ]
        );
    }

    /**
     * 获取回收站列表
     */
    public function getRecycleList(){

        $code_id = $this->request->param('pay_code');
        if (is_admin_login() != 1){
//            $son_ms = Db::name('ms')->where('admin_id',is_admin_login())->column('admin_id');
            $where['admin_id'] = is_admin_login();
        }
            $where['status'] = 1;
            $where['work_status'] = 1;
            $where['admin_work_status'] = 1;
            $where['money'] = ['gt',4000];
//        $where['is_delete'] = 1;
//        $where['code_type'] = $code_id;
//        print_r($where);die;
//        $data = $this->modelMs->where($where)->field('username,userid')->paginate(input('limit'));
//
//        foreach ($data->items() as $k=>$v){
//            $codeCount = $this->modelEwmPayCode->where(['ms_id'=>$v['userid'],'is_delete'=>0,'code_type'=>$code_id,'status'=>1])->count('id');
//            if ($codeCount < 1){
//                unset($data->items()[$k]);
//                continue;
//            }
//
//            $data->items()[$k]['codeCount'] = $codeCount;
//        }
        $validUserIds = $this->modelEwmPayCode
            ->where(['is_delete'=>0, 'code_type'=>$code_id, 'status'=>1])
            ->group('ms_id')
            ->having('count(id) > 0')
            ->column('ms_id');

        $data = $this->modelMs->where($where)->whereIn('userid', $validUserIds)->field('username,userid')->paginate(input('limit'));

        foreach ($data as $k => $v) {
            $v['codeCount'] = $this->modelEwmPayCode->where(['ms_id'=>$v['userid'],'is_delete'=>0,'code_type'=>$code_id,'status'=>1])->count('id');
            $data->offsetSet($k, $v);
        }





        $this->result($data->total() > 0 ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg' => '',
                'count' => $data->total(),
                'data' => $data->items(),
            ] : [
                'code' => CodeEnum::ERROR,
                'msg' => '暂无数据',
                'count' => 0,
                'data' =>[]
            ]
        );


    }

    public function balanceRecharge()
    {
        return $this->fetch();
    }

    public function getBalanceRechargeList()
    {
        $where = [];
        if (is_admin_login() != 1){
            $where['a.admin_id'] = is_admin_login();
        }

        $money = $this->request->param('amount');
        $status = $this->request->param('status');
        $username = $this->request->param('username');


        $start = date('Y-m-d 00:00:00',time());
        $end = date('Y-m-d 23:59:59',time());
        if (!empty($this->request->param('start'))){
            $start = $this->request->param('start');

        }
        if (!empty($this->request->param('end'))){
            $end = $this->request->param('end');
        }


        $where['a.update_time'] = ['between time',[$start,$end]];

        if ($money) {
            $where['a.amount'] = $money;
        }

        if ($status && $status != -1) {
            $where['a.status'] = $status;
        }

        if ($username) {
            $where['b.username'] = ['LIKE', "%{$username}%"];
        }

        $data = $this->modelMsBalanceRecharge->alias('a')
            ->join('ms b','a.ms_id = b.userid')
            ->where($where)
            ->field('a.*,b.username')
            ->order('a.update_time desc')
            ->paginate(input('limit'));
        $this->result($data->total() > 0 ?
            [
                'code' => 0,
                'msg' => '',
                'count' => $data->total(),
                'data' => $data->items(),
            ] : [
                'code' => 1,
                'msg' => '暂无数据',
                'count' => 0,
                'data' =>[]
            ]
        );
    }

    //充值同意
    public function balanceRechargeAgree()
    {
        $id = $this->request->param('id');
        $where = [
            'id' => $id,
            'admin_id' => is_admin_login(),
        ];
        $info = $this->modelMsBalanceRecharge->where($where)->find();

        if (!$info) {
            $this->error('非法请求');
        }

        if ($info['status'] != 1) {
            $this->error('非法请求');
        }

        $res = $this->logicMsBalanceRecharge->balanceRechargeAgree($where);

        if ($res) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    //充值拒绝
    public function balanceRechargeRefuse()
    {
        $id = $this->request->param('id');
        $where = [
            'id' => $id,
            'admin_id' => is_admin_login(),
        ];
        $info = $this->modelMsBalanceRecharge->where($where)->find();

        if (!$info) {
            $this->error('非法请求');
        }

        if ($info['status'] != 1) {
            $this->error('非法请求');
        }

        $res = $this->modelMsBalanceRecharge->where(['id' => $id])->update(['status' => 3]);

        if ($res) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }



    //重新核销
    public function repeatWriteOff(){
        $orderid = $this->request->param('id');
        $where['id'] = $orderid;
        if (is_admin_login() != 1){
            $where['admin_id'] = is_admin_login();
        }


        $orderInfo = $this->modelEwmOrder->where($where)->find();
        if (empty($orderInfo)){
            $this->error('无此订单信息');
        }


        if ($orderInfo['status'] == 1){
            $this->error('此订单已经处理成功');
        }

        if (empty($orderInfo['xiaoka_id'])){
            $this->error('此订单尚未提交至销卡平台，请提交后再操作！');
        }
        if ($this->request->isPost()){
            $apiInfo = Db::name('jdek_sale')->where(['id'=>$orderInfo['xiaoka_id'],'ms_id'=>$orderInfo['gema_userid']])->find();
            if ($apiInfo['type'] == 1){
                $result = json_decode($this->EchakaSubmit($orderInfo,$apiInfo),true);
                if ($result['code'] == 200){
                    Db::name('ewm_order')->where('id',$orderInfo['id'])->update(['legality'=>2,'note'=>'已提交至E查卡平台']);
                    $this->success('重新核销成功！');
                }else{
                    Db::name('ewm_order')->where('id',$orderInfo['id'])->update(['legality'=>2,'note'=>'E查卡返回：'.$result['extend']['message']]);
                    $this->error($result['extend']['message']);
                }

            }elseif ($apiInfo['type'] == 2){
                $result = json_decode($this->PuXiJiShouCardSubmit($orderInfo),true);
                if ($result['code'] == 0){
                    Db::name('ewm_order')->where('id',$orderInfo['id'])->update(['legality'=>2,'note'=>'已提交至蒲曦寄售平台']);
                    $this->success('重新核销成功！');
                }else{
                    Db::name('ewm_order')->where('id',$orderInfo['id'])->update(['legality'=>2,'note'=>'蒲曦寄售返回：'.$result['msg'],'status'=>2]);
                    $this->error($result['msg']);
                }
            }elseif ($apiInfo['type'] == 3){
                $result = json_decode($this->KuKeSubmit($orderInfo,$apiInfo),true);
                if ($result['code'] == 200){
                    Db::name('ewm_order')->where('id',$orderInfo['id'])->update(['legality'=>2,'note'=>'已提交至酷氪销卡平台']);
                    $this->success('重新核销成功！');
                }else{
                    Db::name('ewm_order')->where('id',$orderInfo['id'])->update(['legality'=>2,'note'=>'酷氪销卡返回：'.$result['message']]);
                    $this->error($result['message']);
                }
            }else{
                $this->error('技术努力开发中');
            }
        }
        $this->assign('orderInfo',$orderInfo);
        return $this->fetch('repeatWriteOff');
    }













    private function EchakaSubmit($order,$apiInfo){
//        $apiInfo = Db::name('jdek_sale')->where(['ms_id'=>$order['gema_userid'],'type'=>1])->find();
        $merchantId = $apiInfo['merchantId'];
        $signKey = $apiInfo['signkey'];
        $apiKey = $apiInfo['encryptionkey'];;
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

        Db::name('ewm_order')->where('id',$order['id'])->update(['updateTime'=>time(),'xiaoka_id'=>$apiInfo['id']]);
        return $result;

    }

    private function PuXiJiShouCardSubmit($order){
        if ($order['code_type'] == 43){
            //京东E卡
            $cardId = '565';
            $apiInfo = Db::name('jdek_sale')->where(['ms_id'=>$order['gema_userid'],'type'=>2])->find();
        }elseif($order['code_type'] == 61){
            //汇元易付
            $cardId = '628';
            $apiInfo = Db::name('jdek_sale')->where(['ms_id'=>$order['gema_userid'],'type'=>5])->find();
        }elseif ($order['code_type'] == 60) {
            //骏网
            $cardId = '548';
            $apiInfo = Db::name('jdek_sale')->where(['ms_id'=>$order['gema_userid'],'type'=>5])->find();
        }elseif ($order['code_type'] == 62) {
            //骏网
            $cardId = '626';
            $apiInfo = Db::name('jdek_sale')->where(['ms_id'=>$order['gema_userid'],'type'=>5])->find();
        }else {
            $cardId = '565';
            $apiInfo = Db::name('jdek_sale')->where(['ms_id'=>$order['gema_userid'],'type'=>2])->find();
        }

        $account = $apiInfo['merchantId'];
        $apiurl = 'http://www.puxijishou.com/app-api/openApi/cardSupply/batchSupplyCard';
        $signKey = $apiInfo['signkey'];
//        $edeKey = '09b18f85350148b7abda312ceecf8a4a';

        $data = [
            'account' => $account,
            'cardId' => $cardId,
//            'isCustomFaceValue' => 'true',
            'faceValue' => floor($order['order_price'])
        ];
        if ($order['code_type'] == 43){
            $dataStr = $order['cardKey'].','.$order['order_no'];
        }else{
            $dataStr = $order['cardAccount'].','.$order['cardKey'].','.$order['order_no'];
        }


        $data['data'] =  $this->encrypt($dataStr,$signKey);
        $data['callbackUrl'] = $this->request->domain().'/api/pay/PuXiJiShouCardNotify';
        $data['sign'] = $this->getSign($data,$signKey);
        Log::error('PuXiJiShouCardSubmitApiData:'.json_encode($data,true));

        $result = $this->curlPost($apiurl,$data);

        Log::error('PuXiJiShouCardSubmitReturnData : '.$result);
        Db::name('ewm_order')->where('id',$order['id'])->update(['updateTime'=>time(),'xiaoka_id'=>$apiInfo['id']]);
        return $result;

    }



    private function getSign($data, $secret)
    {
        ksort($data);
        $string_a = '';
        foreach ($data as $k => $v) {
            $string_a .= $k.$v;
        }
        Log::error('JdECardSubmit sign str ：'.$string_a  . $secret);
        $sign = md5($string_a  . $secret);
        return $sign;
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
     * curl post
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param string $url
     * @param string $postData
     * @param array $options
     * @return mixed
     */
    private function curlPost($url = '', $postData = '', $options = array(),$timeOut=5)
    {
        if (is_array($postData)) {
            $postData = http_build_query($postData);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut); //设置cURL允许执行的最长秒数
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }
        //https请求 不验证证书和host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);

        $headers = curl_getinfo($ch);
        curl_close($ch);
        return $data;
    }



    private function KukegetSign($data, $secret)
    {

        //签名步骤一：按字典序排序参数
        ksort($data);
        $string_a = '';
        foreach ($data as $k => $v) {
            $string_a .= "{$k}={$v}&";
        }
//        $string_a = substr($string_a,0,strlen($string_a) - 1);
        //签名步骤三：MD5加密
        $sign = md5($string_a . 'secret=' . $secret);

        // 签名步骤四：所有字符转为大写
//        $result = strtoupper($sign);

        return $sign;
    }


    private function KuKeSubmit($order,$apiInfo){
       // $apiInfo = Db::name('jdek_sale')->where(['ms_id'=>$order['gema_userid'],'type'=>3])->find();
        $merchantId = $apiInfo['merchantId'];
        $signKey = $apiInfo['signkey'];
//        $apiKey = $apiInfo['encryptionkey'];;
        $apiUrl = 'https://autoapi.imcookie.io/api/merchant/order';
        $data = [
            'merchantId' => $merchantId,
            'actionType' => '2',
            'cardPwd' => $order['cardKey'],
            'notify' => '1',
            'bizOrderNo' => $order['order_no'],
            'bizNotifyUrl' => $this->request->domain().'/api/pay/KuKeNotify',
            'expectCardType' => '3',
            'expectAmount' => sprintf("%.2f",$order['order_pay_price']) * 100
        ];

//        $signStr = $data['merchantId'] . '&' . $data['merOrderId'] . '&' . $data['cardPwd'] . '&' . $signKey;

//        $data['sign'] = md5($signStr);
        $apiData['data'] = $data;
        $apiData['sign'] = $this->KukegetSign($data,$signKey);
        Log::error('KuKeSubmit Api Data :'.json_encode($apiData,true));
        $result = $this->send_post_json($apiUrl,json_encode($apiData,true));
        Log::error('KuKeSubmit Return Data :'.$result);
        Db::name('ewm_order')->where('id',$order['id'])->update(['updateTime'=>time(),'xiaoka_id'=>$apiInfo['id']]);
        return $result;

    }

    /**
     * 解除码商所有代付余额
     * 
     */
    public function unfreeze()
    {
        
        Db::startTrans();
        try{
            $mss = $this->modelMs->where('admin_id', is_admin_login())->field('userid,daifu_money')->select();
            if ($mss){
                foreach($mss as $k => $ms){
                    accountLog($ms['userid'], MsMoneyType::DAIFU_MONEY_TO_ENABLE, 0, $ms['daifu_money'],  '管理员解除所有代付余额，清零代付余额' . $ms['daifu_money'], 'daifu_enable');
                    accountLog($ms['userid'], MsMoneyType::DAIFU_MONEY_TO_ENABLE, 1, $ms['daifu_money'],  '管理员解除所有代付余额，增加可用余额' . $ms['daifu_money'], 'enable');
                }
            }
            Db::commit();
        }catch(\Exception $e){
            Db::rollback();
            return json(['code'=>0,'msg'=>$e->getMessage()]);
        }

        return json(['code'=>1,'msg'=>'解除成功']);
    }

    /**
     * 批量修改码商押金
     */
    public function batchsetdeposit()
    {
        $amount = $this->request->param('amount');

        if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $amount)){
            return json(['code'=>0,'msg'=>'请输入正确的金额']);
        }

        $msids =  $this->modelMs->where('admin_id', is_admin_login())->column('userid');

        $ret = $this->modelMs->where('userid', 'in', $msids)->update(['cash_pledge' => $amount]);

        if ($ret){
            return json(['code'=>1,'msg'=>'修改成功']);
        }else{
            return json(['code'=>0,'msg'=>'修改失败']);
        }
    }



    public function testPaymentAlipayWap(){
        $code_id = $this->request->param('id');
        $amount = $this->request->param('amount');
        if (!preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $amount)){
            return json(['code'=>0,'msg'=>'请输入正确的金额']);
        }
        $code_info = Db::name('ewm_pay_code')->where('id',$code_id)->find();
        if (empty($code_info)){
            return json(['code'=>0,'msg'=>'未找到可用码子']);
        }

        $order = [
            'order_pay_price' => $amount
        ];

        $result = $this->alipayWapPay( date('ymdHis').rand(1000,9999),$order,$code_info,'http://www.baidu.com',is_admin_login());
        return json(['code'=>1,'url'=>'https://openapi.alipay.com/gateway.do?'. http_build_query($result)]);
    }


    private function alipayWapPay($orderNo,$orderInfo,$apiData,$notify,$admin_id){
        /*** 获取通道的签名参数 end ***/
        $orderName = getAdminPayCodeSys('order_reason',57,$admin_id);
        if (empty($orderName)){
            $order_reason = '商城商品-'.$orderNo;
        }else{
            $orderName = explode(',',$orderName);
            $orderNameKey = rand(0, count($orderName)-1);
            $order_reason =$orderName[$orderNameKey].'-订单号：'.$orderNo;
        }
//        array_rand($a,1)
        $signData = [
            'appid'        => $apiData['account_number'],
            'returnUrl'     => 'http://www.baidu.com',
            'notifyUrl' => $notify,
            'outTradeNo'       => $orderNo,
            'payAmount' => $orderInfo['order_pay_price'],
            'orderName'  => $order_reason,
            'rsaPrivateKey'      => $apiData['privateKey'],
        ];
        //     Log::error('data :'.json_encode($signData,320));
        //p($signData);
//        vendor('alipay.AlipayWapService');
        //调用接口
        $aliPay = (new AlipayWapService());
        $aliPay->setAppid($signData['appid']);
        $aliPay->setReturnUrl($signData['returnUrl']);
        $aliPay->setNotifyUrl($signData['notifyUrl']);
        $aliPay->setRsaPrivateKey($signData['rsaPrivateKey']);
        $aliPay->setTotalFee($signData['payAmount']);
        $aliPay->setOutTradeNo($signData['outTradeNo']);
        $aliPay->setOrderName($signData['orderName']);
        $sHtml = $aliPay->doPay();
        //p($sHtml);
        return $sHtml;

    }

}

