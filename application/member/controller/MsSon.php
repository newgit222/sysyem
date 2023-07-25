<?php

namespace app\member\controller;
use app\common\library\enum\CodeEnum;
use app\common\logic\MsMoneyType;
use app\common\model\Ms;
use app\member\Logic\SecurityLogic;
use think\Cache;
use think\Db;
use think\Request;
use think\Log;
class MsSon extends Base
{

    public function index(){
        $ms_id = $this->agent_id;
        $son_ms_ids = $this->getIds($ms_id);
        $total_money = $this->modelMs->where('userid', 'in', $son_ms_ids)->sum('money');
        $permit_ms_huafen_money = getAdminPayCodeSys('permit_ms_huafen_money',256,$this->agent['admin_id']);
        if (empty($permit_ms_huafen_money)){
            $permit_ms_huafen_money = 1;
        }
        $ms_add_son = getAdminPayCodeSys('ms_add_son',256,$this->agent['admin_id']);
        if (empty($ms_add_son)){
            $ms_add_son = 1;
        }

        $ms_select_level = getAdminPayCodeSys('ms_select_level',256,$this->agent['admin_id']);
        if (empty($ms_select_level)){
            $ms_select_level = 1;
        }




        $this->assign('ms_select_level', $ms_select_level);
        $this->assign('ms_add_son', $ms_add_son);
        $this->assign('total_money', $total_money);
        $this->assign('permit_ms_huafen_money', $permit_ms_huafen_money);
        return $this->fetch();
    }

    public function getMsSonList(Request $request){
        $where = [];
        if (!empty($request->param('username'))){
            $where['username'] = $request->param('username');
        }

        if ($request->param('work_status') !=null && $request->param('work_status') >=0){
            $where['work_status'] = $request->param('work_status');
        }
        $limit = $request->param('limit');
        $page = $request->param('page');
        $start=$limit*($page-1);
        $sonIds = $this->getIds($this->agent_id);
        if (empty($sonIds)){
            return json([
                'code'=>1,
                'msg'=>'暂无数据'
            ]);
        }
        $where['status'] = 1;
//        $sonIds = array_reverse($sonIds);
        $msSonLists = Db::name('ms')->where($where)
            ->where('userid','in',$sonIds)
            ->order('userid desc')->paginate($this->request->param('limit', 10));
        $msSonList = $msSonLists->items();
        // 构建用户ID列表
        $userIds = [];
        foreach ($msSonList as $item) {
            $userIds[] = $item['userid'];
        }

// 查询订单金额
        $ewmYesterdayAmount = Db::name('ewm_order')
            ->whereTime('add_time', 'yesterday')
            ->where('status', 1)
            ->whereIn('gema_userid', $userIds)
            ->field('gema_userid, sum(order_price) as yesterday_amount')
            ->group('gema_userid')
            ->select();

        $ewmTodayAmount = Db::name('ewm_order')
            ->whereTime('add_time', 'today')
            ->where('status', 1)
            ->whereIn('gema_userid', $userIds)
            ->field('gema_userid, sum(order_price) as today_amount')
            ->group('gema_userid')
            ->select();

// 查询代付金额
        $daifuYesterdayAmount = Db::name('daifu_orders')
            ->whereTime('create_time', 'yesterday')
            ->where('status', 2)
            ->whereIn('ms_id', $userIds)
            ->field('ms_id, sum(amount) as yesterday_daifu')
            ->group('ms_id')
            ->select();

        $daifuTodayAmount = Db::name('daifu_orders')
            ->whereTime('create_time', 'today')
            ->where('status', 2)
            ->whereIn('ms_id', $userIds)
            ->field('ms_id, sum(amount) as today_daifu')
            ->group('ms_id')
            ->select();

// 获取父级用户名
        $parentUsernames = Db::name('ms')
            ->whereIn('userid', array_column($msSonList, 'pid'))
            ->column('username', 'userid');

// 更新数据
        foreach ($msSonList as &$item) {
            $userId = $item['userid'];

            // 设置默认值
            $item['yesterday_amount'] = '0.00';
            $item['today_amount'] = '0.00';
            $item['yesterday_daifu'] = '0.00';
            $item['today_daifu'] = '0.00';

            // 更新订单金额
            foreach ($ewmYesterdayAmount as $ewmItem) {
                if ($ewmItem['gema_userid'] == $userId) {
                    $item['yesterday_amount'] = sprintf("%.2f", $ewmItem['yesterday_amount']);
                    break;
                }
            }

            foreach ($ewmTodayAmount as $ewmItem) {
                if ($ewmItem['gema_userid'] == $userId) {
                    $item['today_amount'] = sprintf("%.2f", $ewmItem['today_amount']);
                    break;
                }
            }

            // 更新代付金额
            foreach ($daifuYesterdayAmount as $daifuItem) {
                if ($daifuItem['ms_id'] == $userId) {
                    $item['yesterday_daifu'] = sprintf("%.2f", $daifuItem['yesterday_daifu']);
                    break;
                }
            }

            foreach ($daifuTodayAmount as $daifuItem) {
                if ($daifuItem['ms_id'] == $userId) {
                    $item['today_daifu'] = sprintf("%.2f", $daifuItem['today_daifu']);
                    break;
                }
            }

            // 更新父级用户名
            $item['pid'] = isset($parentUsernames[$item['pid']]) ? $parentUsernames[$item['pid']] : '';
        }

//        foreach ($msSonList as $k=>$v){
//            $msSonList[$k]['yesterday_amount']= sprintf("%.2f",Db::name('ewm_order')->whereTime('add_time', 'yesterday')->where('status', 1)->where('gema_userid',$v['userid'])->sum('order_price'));
//            $msSonList[$k]['today_amount']= sprintf("%.2f",Db::name('ewm_order')->whereTime('add_time', 'today')->where('status', 1)->where('gema_userid',$v['userid'])->sum('order_price'));
//            $msSonList[$k]['yesterday_daifu']= sprintf("%.2f",Db::name('daifu_orders')->whereTime('create_time', 'yesterday')->where('status', 2)->where('ms_id',$v['userid'])->sum('amount'));
//            $msSonList[$k]['today_daifu']= sprintf("%.2f",Db::name('daifu_orders')->whereTime('create_time', 'today')->where('status', 2)->where('ms_id',$v['userid'])->sum('amount'));
//            $msSonList[$k]['pid'] = Db::name('ms')->where('userid',$v['pid'])->value('username');
//        }

//        $count = Db::name('ms')->where('userid','in',$sonIds)->where($where)->count();
        return json([
            'code'=>0,
            'msg'=>'请求成功',
            'data'=>$msSonList,
            'count'=>$msSonLists->total(),
        ]);

    }

    function searchListMoney(){
        $where = [];
        if (!empty($this->request->param('username'))){
            $where['username'] = $this->request->param('username');
        }

        if ($this->request->param('work_status') !=null && $this->request->param('work_status') >=0){
            $where['work_status'] = $this->request->param('work_status');
        }
        $ms_id = $this->agent_id;
        $son_ms_ids = $this->getIds($ms_id);
        $total_money = $this->modelMs->where('userid', 'in', $son_ms_ids)->where($where)->sum('money');
        return json(['code' => CodeEnum::SUCCESS, 'data'=>compact('total_money') ]);
    }

    /**
     * 下级余额明细
     */
        public function balanceDetails(){

            $bill_types = MsMoneyType::getMoneyOrderTypes();
//            print_r($bill_types);die;
            $this->assign('billTypes', $bill_types);
            $this->assign('userid', $this->request->param('userid'));
            return $this->fetch();
        }

        public function getBalanceDetails(Request $request){
            $userid = $request->param('userid');
            if (empty($userid)){
                return json([
                        'code'=>1,
                        'msg'=>'暂无数据'
                    ]);
            }
            $sonIds = $this->getIds($this->agent_id);
            if (!in_array($userid,$sonIds)){
                        return json([
                            'code'=>1,
                            'msg'=>'暂无数据'
                        ]);
            }
            $where['a.uid'] = $userid;
            if (!empty($request->param('bill_type'))){
                $where['a.jl_class'] = $request->param('bill_type');
            }
            if (!empty($request->param('jc_class'))){
                $where['a.jc_class'] = $request->param('jc_class');
            }
            $startTime = date('Y-m-d 00:00:00',time());
            $endTime = date('Y-m-d 23:59:59',time());
            $where['a.addtime'] = ['between', [$startTime, $endTime]];
            if (!empty($request->param('startDate'))){
                $startTime = $request->param('startDate');
                $where['a.addtime'] = ['egt', strtotime($startTime)];
            }

            if (!empty($request->param('endDate'))){
                $endTime = $request->param('endDate');
                $where['a.addtime'] = ['elt',strtotime($endTime)];
            }

            if ($startTime && $endTime) {
                $where['a.addtime'] = ['between', [strtotime($startTime), strtotime($endTime)]];
            }
            $limit = $request->param('limit');
            $page = $request->param('page');
            $start=$limit*($page-1);
            $bills = Db::name('ms_somebill')
                ->alias('a')
                ->join('ms m','a.uid = m.userid')
                ->field('a.*,m.username')
                ->where($where)
                ->limit($start,$limit)
                ->order('id desc')
                ->select();


            $count = Db::name('ms_somebill')->alias('a')->where($where)->count();
            if ($count <= 0){
                return json([
                    'code'=>1,
                    'msg'=>'暂无数据'
                ]);
            }
            $bill_types = MsMoneyType::getMoneyOrderTypes();
            foreach ($bills as $k=>$v){
                foreach ($bill_types as $key => $val){
                    if ($v['jl_class'] == $key){
                        $bills[$k]['jl_class'] = $val;
                        continue;
                    }
                }
            }
            return json([
                'code'=>0,
                'msg'=>'请求成功',
                'data'=>$bills,
                'count'=>$count
            ]);
        }

    /**
     * @return 设置下级费率
     * @time
     */

    protected function getTeamPidRate($id, $rate, $code_type)
    {
        $nav = Db::name('ms')->where('userid', $id)->where('status','neq','-1')->field('userid, pid')->find();
        if ($nav['pid'] > 0){
            $teamrate = Db::name('ms_rate')->where(['ms_id' => $nav['pid'], 'code_type_id' => $code_type])->value('rate');

            if (empty($teamrate)) {
                $result = self::getTeamPidRate($nav['pid'], 0.00, $code_type);
            } else {
                if ($rate > $teamrate) {
                    return false;
                }

                if ($nav['pid'] > 0) {
                    $result = self::getTeamPidRate($nav['pid'], $teamrate, $code_type);
                } else {
                    $result = true;
                }
            }

            return $result;
        }
        return true;
    }


    public function sysSonRate(){
        if ($this->request->isPost()){
            $data = $this->request->post('r/a');

            if (is_array($data)){
                foreach ($data as  $key=>$item) {
                    $ms= Db::name('ms')->where('userid', $item['ms_id'])->find();
                    if (!$ms || $ms['pid'] != $this->agent['userid']){
                        return ['code' => CodeEnum::ERROR, 'msg'=>'非法请求'];
                    }
                    if ($item['son_rate'] < 0){
                        return ['code' => CodeEnum::ERROR, 'msg'=>'费率最低为0'];
                    }
                    $myRate = Db::name('ms_rate')->where(['ms_id'=>$this->agent['userid'],'code_type_id'=>$item['code_type_id']])->value('rate');
                    if ($myRate === null){
                        $myRate = 0.00;
                    }

                    if ($item['son_rate'] > $myRate){
                        action_log('码商设置下级费率异常',$this->agent['username'].'给下级：'. $item['ms_id'].'设置费率时高出自己的费率，设置通道：'.$item['code_type_id'].'，费率：'.$item['son_rate']);
                        return ['code' => CodeEnum::ERROR, 'msg'=>'下级费率不可大于自己费率'];
                    }

                    $team = $this->getTeamPidRate($item['ms_id'],$item['son_rate'],$item['code_type_id']);
                    if ($team === false){
                        return ['code' => CodeEnum::ERROR, 'msg'=>'设置费率异常！'];
                    }

                    $topms = getNavPid($item['ms_id']);
                    $topmsRate = Db::name('ms_rate')->where(['ms_id'=>$topms,'code_type_id'=>$item['code_type_id']])->value('rate');
                    if ($topmsRate === null){
                        $topmsRate = 0.00;
                    }
                    if ($item['son_rate'] >$topmsRate){
                        action_log('码商设置下级费率异常',$this->agent['username'].'给下级：'. $item['ms_id'].'给下级码商设置费率顶级码商，设置通道：'.$item['code_type_id'].'，费率：'.$item['son_rate']);
                        Log::error('码商：'.$this->agent['username'].'给下级码商设置费率超过限制，提交参数：'.$item['code_type_id'].'，费率：'.$item['son_rate']);
                        return ['code' => CodeEnum::ERROR, 'msg'=>'费率设置异常！'];
                    }

                    $msSonSubs = Db::name('ms')->where('pid', $item['ms_id'])->select();
                    foreach ($msSonSubs as $msSonSub) {
                        $sonSubRate = Db::name('ms_rate')->where(['ms_id' => $msSonSub['userid'], 'code_type_id' => $item['code_type_id']])->value('rate');
                        if ($item['son_rate'] < $sonSubRate || $sonSubRate > $myRate) {
                            return ['code' => CodeEnum::ERROR, 'msg' => '费率不可低于子级代理费率或上级代理费率'];
                        }
                    }


                    $res = Db::name('ms_rate')->where(['ms_id'=>$item['ms_id'],'code_type_id'=>$item['code_type_id']])->select();
                    if ($res){
                        //修改
                        Db::name('ms_rate')->where(['ms_id'=>$item['ms_id'],'code_type_id'=>$item['code_type_id']])->update(['rate' => $item['son_rate'], 'update_time' => time()]);
                    }else{
                        //新增
                        Db::name('ms_rate')->insert( [
                            'ms_id' => $item['ms_id'],
                            'code_type_id' => $item['code_type_id'],
                            'rate' => $item['son_rate'],
                            'create_time' => time(),
                            'update_time' => time(),
                        ]);
                    }

                }
                return ['code' => CodeEnum::SUCCESS, 'msg' => '费率配置成功'];
            }
        }
        $sonMsid = $this->request->param('userid');
        $sonMs = Db::name('ms')->where('userid',$sonMsid)->find();
        if (empty($sonMs)){
            return ['code' => CodeEnum::ERROR, 'msg'=>'暂无数据'];
        }
        if ($sonMs['pid'] != $this->agent_id){
            return ['code' => CodeEnum::ERROR, 'msg'=>'非法操作'];
        }
        $this->assign('userid',$sonMsid);
        //所有渠道列表
        $list = Db::name('pay_code')->where('status',1)->where('id','lt','255')->select();
        //查询自己的费率
        $myRate = Db::name('ms_rate')->where('ms_id',$this->agent_id)->select();
        //查询当前下级的费率
        $sonRate = Db::name('ms_rate')->where('ms_id',$sonMs['userid'])->select();
        if ($myRate){
            foreach ($list as $k=>$v){
                $list[$k]['my_rate'] = 0;
                foreach ($myRate as $key=>$val){
                    if ($v['id'] == $val['code_type_id']){
                        $list[$k]['my_rate'] = $val['rate'];
                    }
                }
            }
        }else{
            foreach ($list as $key=>$val){
                $list[$key]['my_rate'] = 0;
            }
        }

        if ($sonRate){
            foreach ($list as $k=>$v){
                $list[$k]['son_rate'] =0;
                foreach ($sonRate as $key=>$val){
                    if ($v['id'] == $val['code_type_id']){
                        $list[$k]['son_rate'] = $val['rate'];
                    }
                }
            }
        }else{
            foreach ($list as $key=>$val){
                $list[$key]['son_rate'] = 0;
            }
        }

        $this->assign('list', $list);
        return $this->fetch();

    }


    /**
     * 下级订单列表
     */
    public function orders(){
        $pay_code = Db::name('pay_code')->where(['status'=>1,'id'=>['lt','255']])->select();
        $this->assign('pay_code',$pay_code);
        return $this->fetch();
    }

    public function getSonOrders(Request $request){
        $where = [];
        $startTime = date('Y-m-d 00:00:00',time());
        $endTime = date('Y-m-d 23:59:59',time());
        $where['a.add_time'] = ['between', [$startTime, $endTime]];
        if (!empty($request->param('startDate'))){
            $startTime = $request->param('startDate');
            $where['a.add_time'] = ['egt', strtotime($startTime)];
        }

        if (!empty($request->param('endDate'))){
            $endTime = $request->param('endDate');
            $where['a.add_time'] = ['elt',strtotime($endTime)];
        }

        if ($startTime && $endTime) {
            $where['a.add_time'] = ['between', [strtotime($startTime), strtotime($endTime)]];
        }
        if (!empty($request->param('code_type'))){
            $where['a.code_type'] = $request->param('code_type');
        }

        if (!empty($request->param('trade_no'))){
            $where['a.order_no'] = $request->param('trade_no');
        }

        if (!empty($request->param('merchant_order_no'))){
            $where['a.merchant_order_no'] = $request->param('merchant_order_no');
        }

        if (!empty($request->param('username'))){
            $where['m.username'] = $request->param('username');
        }

        if ($request->param('status') !=null && $request->param('status') >=0){
            $where['a.status'] = $request->param('status');
        }
        $sonIds = $this->getIds($this->agent_id);
        if (empty($sonIds)){
            return json([
                'code'=>1,
                'msg'=>'暂无数据'
            ]);
        }
        $where['a.gema_userid'] = ['in',$sonIds];
        $limit = $request->param('limit');
        $page = $request->param('page');
        $start=$limit*($page-1);

        $sonOrders = Db::name('ewm_order')
            ->alias('a')
            ->join('ms m','a.gema_userid = m.userid')
            ->join('pay_code p','a.code_type = p.id')
            ->field('a.*,m.username,p.name')
            ->where($where)
//            ->limit($start,$limit)
            ->order('a.add_time desc')
            ->paginate($this->request->param('limit', 15));


//        $count = Db::name('ewm_order')
//            ->alias('a')
//            ->join('ms m','a.gema_userid = m.userid')
//            ->join('pay_code p','a.code_type = p.id')
//            ->where($where)
//            ->count('a.id');

        return json([
            'code'=>0,
            'msg'=>'请求成功',
            'data'=>$sonOrders->items(),
            'count'=>$sonOrders->total()
        ]);


    }



    /**
     * 团队统计
     */
    public function stats(){
        return $this->fetch();
    }

    public function getTeamStats(Request $request){

        $where = [];
        if (!empty($request->param('username'))){
            $where['username'] = $request->param('username');
        }

        if (!empty($request->param('level'))){
            $where['level'] = $request->param('level');
        }

        $sonMsIds = $this->getIds($this->agent_id);

        if (empty($sonMsIds)){
            return json([
                'code'=>1,
                'msg'=>'暂无数据'
            ]);
        }
        $where['userid'] = ['in',$sonMsIds];
        $limit = $request->param('limit');
        $page = $request->param('page');
        $start=$limit*($page-1);
        $startTime = date('Y-m-d 00:00:00', time());
        $endTime = date('Y-m-d 23:59:59', time());
        if (!empty($request->param('start_time')) && !empty($request->param('end_time'))) {
            $startTime = $request->param('start_time');
            $endTime = $request->param('end_time');
        }
        $sonMsLists = Db::name('ms')->where($where)->order('userid desc')->paginate($this->request->param('limit', 15));
        //时间

        $sonMsList = $sonMsLists->items();
        $batchSize = 45; // 每个批次的大小
//        $batchedSonMsList = array_chunk($sonMsList, $batchSize);
//        $batchSize = 100; // 每个批次的大小
        $batchedSonMsList = array_chunk($sonMsList, $batchSize);

        foreach ($batchedSonMsList as $batch) {
            $userIds = array_column($batch, 'userid');
            $isAgent = Db::name('ms')->where('pid', 'in', $userIds)->select();
            foreach ($batch as $key => $val) {
                if (empty($isAgent)) {

                    $sonMsList[$key]['team_people_number'] = 0;
                    $sonMsList[$key]['today_amount'] = sprintf("%.2f", Db::name('ewm_order')->where('add_time', 'between time', [$startTime, $endTime])->where('status', 1)->where('gema_userid', $val['userid'])->sum('order_price'));
                    $sonMsList[$key]['today_daifu'] = sprintf("%.2f", Db::name('daifu_orders')->where('create_time', 'between time', [$startTime, $endTime])->where('status', 2)->where('ms_id', $val['userid'])->sum('amount'));
                    $sonMsList[$key]['total_ewmcode_num'] = $this->modelEwmPayCode->where('status', 1)->where('ms_id', $val['userid'])->count();
                    $total_orders = $this->modelEwmOrder->where('add_time', 'between time', [$startTime, $endTime])->where('gema_userid', $val['userid'])->count();
                    $success_orders = $this->modelEwmOrder->where('add_time', 'between time', [$startTime, $endTime])->where('status', 1)->where('gema_userid', $val['userid'])->count();
                    $sonMsList[$key]['total_success_rate'] = sprintf("%.1f%s", $total_orders ? $success_orders / $total_orders * 100 : 0, '%');
                } else {
                    $this->son_id = [];
                    $Msteams = $this->getIds($val['userid']);
                    $sonMsList[$key]['team_people_number'] = sizeof($Msteams);
                    array_unshift($Msteams, $val['userid']);
                    $sonMsList[$key]['money'] = sprintf("%.2f", Db::name('ms')->where('userid', 'in', $Msteams)->sum('money'));
                    $sonMsList[$key]['cash_pledge'] = sprintf("%.2f", Db::name('ms')->where('userid', 'in', $Msteams)->sum('cash_pledge'));
                    $sonMsList[$key]['today_amount'] = sprintf("%.2f", Db::name('ewm_order')->where('add_time', 'between time', [$startTime, $endTime])->where('status', 1)->where('gema_userid', 'in', $Msteams)->sum('order_price'));
                    $sonMsList[$key]['today_daifu'] = sprintf("%.2f", Db::name('daifu_orders')->where('create_time', 'between time', [$startTime, $endTime])->where('status', 2)->where('ms_id', 'in', $Msteams)->sum('amount'));
                    //当日成功率
                    $total_orders = $this->modelEwmOrder->where('add_time', 'between time', [$startTime, $endTime])->where('gema_userid', 'in', $Msteams)->count();
                    $success_orders = $this->modelEwmOrder->where('add_time', 'between time', [$startTime, $endTime])->where('status', 1)->where('gema_userid', 'in', $Msteams)->count();
                    $sonMsList[$key]['total_success_rate'] = sprintf("%.1f%s", $total_orders ? $success_orders / $total_orders * 100 : 0, '%');
                    //收款码数量
                    $sonMsList[$key]['total_ewmcode_num'] = $this->modelEwmPayCode->where('status', 1)->where('ms_id', 'in', $Msteams)->count();
                }
            }

        }
//        $count = sizeof($sonMsList);



        return json([
            'code'=>0,
            'msg'=>'请求成功',
            'data'=>$sonMsList,
            'count'=>$sonMsLists->total()
        ]);

    }


    /**
     * 添加下级码商
     */
    public function addSonUser(Request $request){
        if ($request->isPost()){
            $validate = $this->validateMsSonAdd->scene('addMs')->check($request->post());
            if (false === $validate){
                 $this->error($this->validateMsSonAdd->getError());
            }
            $level = Db::name('ms')->where('userid',$this->agent_id)->value('level');
            if ($level >= 10){
                $this->error('你无权添加下级');
            }
            $chachong = Db::name('ms')->where('username',$request->post('ms_name'))->find();
            if ($chachong){
                $this->error('此账号名不可添加！请更改！');
            }
            $users = [
                'username'      => $request->param('ms_name'),
                'login_pwd'    => $request->param('ms_password'),
                'relogin_pwd' => $request->param('ms_repassword'),
                'pid'   => $this->agent_id,
                'status' => 1
            ];

            return $this->logicMs->addMs($users);

        }
        return $this->fetch();
    }



    /**
     * @var array 修改下级接单状态
     */
    public function edit_son_work_status(){
        if ($this->request->isPost()){
            $status = $this->request->param('status') == 1?0:1;
            $userid = $this->request->param('userid');
            $sonIds = $this->getIds($this->agent_id);
            if (!in_array($userid,$sonIds)){
                return json([
                    'code' => 404
                ]);
            }
            $ms = Ms::where(['userid' => $this->agent_id])->field('admin_work_status')->find();
            if (empty($ms['admin_work_status'])){
                return json([
                    'code' => 404
                ]);
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


            $res = Ms::where(['userid' => $userid])->update(['work_status'=>$status]);
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

                action_log('码商编辑开工','码商'.$this->agent['username'].'将下级码商：'.$ms['username'].'接单状态'.$reason);
                return json([
                    'code' => 1
                ]);
            }
        }
    }



    /**
     * @return void 修改下级余额
     */

    public function editMerChantAmount(){

        $permit_ms_huafen_money = getAdminPayCodeSys('permit_ms_huafen_money',256,$this->agent['admin_id']);
        if (empty($permit_ms_huafen_money)){
            $permit_ms_huafen_money = 1;
        }
        if ($permit_ms_huafen_money != 1){
            return json(['code' => CodeEnum::ERROR, 'msg'=>'权限未开启']);
        }
        $security = $this->request->param('pass');

        $SecurityLogic = new SecurityLogic();
        $result = $SecurityLogic->checkSecurityByUserId($this->agent_id, $security);

        //判断用收款ip是否和最近登录的ip是否一致
        if ($result['code'] == CodeEnum::ERROR) {
            return json(['code' => CodeEnum::ERROR, 'msg'=>'安全码错误']);
        }
        $userid = $this->request->param('userid');
//            $this->error("无权操作");
        if (!$userid){
//            $this->error("非法操作");
            return json(['code' => CodeEnum::ERROR, 'msg'=>'非法操作']);
        }
        $money = trim($this->request->param('money'));
        if (!$money || $money <= 0){
//            $this->error("非法金额");
            return json(['code' => CodeEnum::ERROR, 'msg'=>'非法金额']);
        }


        $son = Db::name('ms')->where('userid',$userid)->find();
        $this->son_id = [];
        $sonIds = $this->getIds($this->agent_id);

//        if ($this->agent->pid == 0){
//            if (!in_array($son['userid'], $sonIds)){
////            $this->error("无权操作");
//                return json(['code' => CodeEnum::ERROR, 'msg'=>'无权操作']);
//
//            }
//        }else{
//            if ($son['pid'] != $this->agent_id){
////            $this->error("无权操作");
//                return json(['code' => CodeEnum::ERROR, 'msg'=>'无权操作']);
//
//            }
//        }

        if (!in_array($userid,$sonIds)){
            return json(['code' => CodeEnum::ERROR, 'msg'=>'无权操作']);
        }



        $query = Db::name('ms');
        $myname = Db::name('ms')->where('userid',$this->agent_id)->value('username');
        $myMoney = Db::name('ms')->where('userid',$this->agent_id)->value('money');
        Db::startTrans();

        try {
            if ($this->request->param('status') == 0){
                if ($son['money'] < $money){
                    throw new \Exception("他没有这么多哦,给孩子留点吧!");
                }
//                $updateSonAmount = $query->where('userid',$userid)->update(['money'=>$userMoney-$money]);
                $infos = '码商（：'.$myname.'）手动给下级（'.$son['username'].'），扣减余额给自己增加'.$money;
                $rets = accountLog($this->agent_id, MsMoneyType::TRANSFER, 1, $money, $infos);

                $info = '码商（ID：'.$myname.'）手动给下级（'.$son['username'].'），扣减余额'.$money;
                $ret = accountLog($userid, MsMoneyType::TRANSFER, 0, $money, $info);

            }elseif ($this->request->param('status') == 1){
                if ($myMoney < $money){
                    throw new \Exception("余额不足!");
                }
                $infos = '码商（'.$myname.'）手动给下级（'.$son['username'].'），增加余额'.$money;
                $rets = accountLog($userid, MsMoneyType::TRANSFER, 1, $money, $infos);

                $info = '码商（ID：'.$myname.'）手动给下级（'.$son['username'].'），增加余额扣减自己余额'.$money;
                $ret = accountLog($this->agent_id, MsMoneyType::TRANSFER, 0, $money, $info);

            }

            if (!$ret && !$rets){
                throw new \Exception("变更下级余额失败，此次操作已终结");
            }
            Db::commit(); // 提交事务
        } catch (\Exception $e){
            Db::rollback(); // 回滚事务
//            $this->error($e->getMessage(),'index');
            return json(['code' => CodeEnum::ERROR, 'msg'=>$e->getMessage()]);
        }
//        $this->success('变更下级余额成功',url('user/index'));
        return json(['code' => CodeEnum::SUCCESS, 'msg'=>'变更下级余额成功']);

    }

    public $son_id = array();

    public function getIds($parentid)
    {
        $list = Db::name("ms")->where(["pid" => $parentid])->field('userid')->select();
        foreach ($list as $key => $value) {
            $this->son_id[] = $value['userid'];
            $this->getIds($value['userid']);
        }
        return $this->son_id;
    }


    /**
     * 设置子代付费率
     */
    public function setSonDaifuRate()
    {
        $bank_rate = $this->request->post('bank_rate');
        $son_ms_id = $this->request->post('userid');
        if ( empty($bank_rate) or $bank_rate<0 or !is_numeric($bank_rate) ){
            return json(['code' => 0, 'msg' => '费率错误']);
        }
        $son_ms = $this->modelMs->where('userid', $son_ms_id)->find();

        if (empty($son_ms) or $son_ms['pid'] != $this->agent_id){
            return json(['code' => 0, 'msg' => '非法操作']);
        }

        //判断码商代付费率不能高于上级代理，不能小于下级代理
        $fa_ms = Db::name('ms')->where(array('userid' => $this->agent_id))->find();
        if ($bank_rate >$fa_ms['bank_rate'] ){
            return json(['code' => 0, 'msg' => '代付费率设置不能高于上级代理！']);
        }

        $son_son_mss = Db::name('ms')->where(array('pid' => $son_ms['userid']))->select();
        if ($son_son_mss){
            foreach ($son_son_mss as $son_son_ms){
                if ($bank_rate<$son_son_ms['bank_rate']){
                    return json(['code' => 0, 'msg' => '代付费率设置不能小于下级代理！']);
                }
            }
        }

        $this->modelMs->where('userid', $son_ms_id)->update(['bank_rate' => $bank_rate]);

        return json(['code' => 1, 'msg' => '设置成功']);

    }


    /**
     * 关闭所有子级代理的接单状态
     */
    public function batchCloseSonMsWorkStatus()
    {
        $ms_id = $this->agent_id;
        $son_ms_ids = $this->getIds($ms_id);
        $this->modelMs->where('userid', 'in',$son_ms_ids)->update(['work_status' => 0]);
        return json(['code' => CodeEnum::SUCCESS, 'msg' => '设置成功']);

    }

}
