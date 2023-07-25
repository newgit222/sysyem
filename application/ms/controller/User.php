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


namespace app\ms\controller;

use app\common\library\enum\CodeEnum;
use app\common\logic\GoogleAuth;
use app\common\library\enum\UserStatusEnum;
use app\common\logic\MsMoneyType;
use app\common\model\UserPayCode;
use app\common\model\UserPayCodeAppoint;
use think\Request;
use think\Db;

class User extends Base
{

    /**
     * var string $secret_key 加解密的密钥
     */
    protected $secret_key  = 'f3a59b69324c831e';

    /**
     * var string $iv 加解密的向量，有些方法需要设置比如CBC
     */
    protected $iv = '7fc7fe7d74f4da93';


    /**
     * 码商下级列表
     *
     * @return mixed
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function index(Request $request)
    {
        $sonMs = Db::name('ms')->where(['pid'=>$this->agent_id,'status'=>1])->select();
        $this->assign('ms',$sonMs);
        return $this->fetch('index');
    }

    /**
     * @user luomu
     * @return 设置下级费率
     * @time
     */

    public function sysSonRate(){
        if ($this->request->isPost()){
            $data = $this->request->post('r/a');
            if (is_array($data)){
                foreach ($data as  $key=>$item) {

                    if ($item['son_rate'] < 0){
                        return ['code' => CodeEnum::ERROR, 'msg'=>'费率最低为0'];
                    }


                    if ($item['son_rate'] > $item['my_rate']){
                        return ['code' => CodeEnum::ERROR, 'msg'=>'下级费率不可大于自己费率'];
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
            $this->error('什么都没有');
        }
        if ($sonMs['pid'] != $this->agent_id){
            $this->error('非法操作');
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
        //print_r($list);die;
        return $this->fetch();

    }




    public function sonIndex(){
        $msId = $this->agent_id;

        $merchant = $this->modelMs->where('pid',$msId)->select();
        $level = Db::name('ms')->where('userid',$msId)->value('level');
        $data = $this->modelMs->sortTree($merchant,$msId,$level);
        if (!$data){
            $this->result([
                'code' => 1,
                'msg'=> '暂无数据',
//                'data'=>$data
            ]);
        }
        $this->result([
            'code' => 0,
            'msg'=> '',
            'data'=>$data
        ]);


}

    private function encrypt($data)
    {
        return base64_encode(openssl_encrypt($data,"AES-128-CBC",$this->secret_key,true,$this->iv));

    }


    private function decrypt($data)
    {
        return openssl_decrypt(base64_decode($data), "AES-128-CBC", $this->secret_key, true, $this->iv);
    }

    public function  blndGoogle()
    {
//        var_dump(session('agent_id'));die;

        $google = new GoogleAuth();
        if($this->request->isPost()){
            $data =  $this->request->post('');
            if(empty($data['google_secretkey']))
            {
                $this->result(0,'参数错误');
            }
            if(empty($data['google_code']))
            {
                $this->result(0,'请输入GOOGLE验证码');
            }
            $ret =  $google->checkGoogleCode($this->decrypt($data['google_secretkey']), $data['google_code']);
            if($ret==false)
            {
                $this->result(0,'绑定GOOGLE失败,请扫码重试');
            }
            unset($data['google_code']);
            $data['google_status'] = 1;
//            $ret = $this->modelAdmin->where(['id'=>$adminId])->update($data);
            $ret = Db::name('ms')->where(['userid'=>$this->agent_id])->update($data);
            if($ret!==false)
            {
                $this->result(1,'绑定成功');
            }
            $this->result(0,'绑定失败');
        }
        //获取商户详细信息

//        $adminInfo  = $this->logicAdmin->getAdminInfo(['id' =>$adminId]);
        $ms = Db::name('ms')->where('userid',$this->agent_id)->find();

        $this->assign('admin',$ms);
        if($ms['google_status'] == 0)
        {
            $google['google_secret'] = $this->encrypt($google->createSecretkey());
            $google['google_qr'] = $google->getQRCodeGoogleUrl($this->decrypt($google['google_secret']));
            $this->assign('google',$google);
        }
        return $this->fetch('index/edit_google');
    }

    /**
     * 码商给商户增加/减少余额
     */
    public function changeBalance(Request $request)
    {

        // post 是提交数据
        $uid = $this->request->param('uid/d');
        if ($this->request->isPost()) {

            $setDec = intval($this->request->param('change_type'));
            $amount = $this->request->param('amount');
            $field = intval($this->request->param('change_money_type'));
            $remarks = addslashes( htmlspecialchars($this->request->param('remarks/s')));

            $ret = $this->logicBalanceChange->creatBalanceChange($uid, $amount, $remarks, $field, $setDec, 1);


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
            $code = $ret ? CodeEnum::SUCCESS : CodeEnum::ERROR;
            $msg = $ret ? "操作成功" : "操作失败";
            $this->result($code, $msg);
        }
        //商户信息
        $balance =  $this->logicBalance->getBalanceInfo(['uid'=>$uid]);
        $this->assign('balance',$balance);
        return $this->fetch();
    }


    /**
     * 码商添加下级
     */
    public function add(){
        if ($this->request->isPost()){
            $postData = input('post.');
            $merchantId = $this->agent_id;

//            $bank_rate = $postData['ms_rate'];
//            if ($bank_rate < 0 ){
//                $this->error("费率最小为0");
//            }
//            $rate = Db::name('ms')->where('userid',$merchantId)->value('bank_rate');
//            if($bank_rate > $rate){
//                $this->error("费率不可大于当前代理");
//            }

            $level = Db::name('ms')->where('userid',$merchantId)->value('level');

            if ($level >= 5){
                $this->error("你无权添加下级！");
            }
            $chachong = Db::name('ms')->where('username',$postData['ms_name'])->find();
            if ($chachong){
                $this->error("此账号名不可添加！请更改！");
            }
            $users = [
                'username'      => $postData['ms_name'],
                'login_pwd'    => $postData['ms_password'],
                'relogin_pwd' => $postData['ms_password'],
//                'bank_rate' => $rate,
                'pid'   => $merchantId,
                'status' => 1
            ];

            $res = $this->logicMs->addMs($users);
            if ($res['code'] == CodeEnum::SUCCESS){
                $this->success('添加成功！','index');
            }

        }

        return $this->fetch('user/msadd');
    }

    /**
     * @return void 修改下级余额
     */

    public function editMerChantAmount(){

        $userid = $this->request->param('userid');
//            $this->error("无权操作");
        if (!$userid){
            $this->error("非法操作");
        }
        $money = trim($this->request->param('money'));
        if (!$money || $money <= 0){
            $this->error("非法金额");
        }

        $son = Db::name('ms')->where('userid',$userid)->find();
        if ($son['pid'] != $this->agent_id){
            $this->error("无权操作");
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
            $this->error($e->getMessage(),'index');
        }
        $this->success('变更下级余额成功',url('user/index'));

    }

    /**
     * @return void 下级代付订单
     */

    public function sonDfOrder(){
        $where['m.pid'] = $this->agent_id;
        !empty($this->request->param('msname')) && $where['m.userid']
            = ['eq', $this->request->param('msname')];

        !empty($this->request->param('trade_no')) && $where['d.trade_no']
            = ['eq', $this->request->param('trade_no')];
        !empty($this->request->param('bank_owner')) && $where['d.bank_owner']
            = ['eq', $this->request->param('d.bank_owner')];
        !empty($this->request->param('out_trade_no')) && $where['d.out_trade_no']
            = ['eq', $this->request->param('d.out_trade_no')];
        $sonDfOrder = Db::name('daifu_orders')
            ->alias('d')
            ->join('ms m','d.ms_id = m.userid')
            ->where($where)
            ->field('d.*,m.username')
            ->paginate(15);
        // 获取分页显示
        $page = $sonDfOrder->render();


        $sonMs = Db::name('ms')->where('pid',$this->agent_id)->select();
        $this->assign('sonMs',$sonMs);
        $this->assign('page', $page);
        $this->assign('sonOrder',$sonDfOrder);
        return $this->fetch('sondforder');
    }





    /**
     * 下级码商列表
     */
    public function sonList(){
        $userid = $this->request->param('userid');
        $sonMs = Db::name('ms')->where('userid',$userid)->find();
        if (empty($sonMs)){
            $this->error('未知错误');
        }

        $sonSonMs = Db::name('ms')->where('pid',$sonMs['userid'])->select();

        $this->assign('sonMs',$sonSonMs);

        return $this->fetch();

    }


    /**
     * 下级订单
     */

    public function sonOrders(){
        $userid = $this->request->param('userid');
        $sonMs = Db::name('ms')->where('userid',$userid)->field('pid,userid')->find();
        if (empty($sonMs)){
            $this->error('未知错误');
        }
//        if ($sonMs['pid'] != $this->agent_id){
//            $this->error('非法操作');
//        }

        $orders = Db::name('ewm_order')
                        ->alias('o')
                        ->join('ewm_pay_code e','o.code_id = e.id')
                        ->where('o.gema_userid',$sonMs['userid'])
                        ->field('o.*,e.account_name,e.bank_name,e.account_number')
                        ->paginate(15,false,['query' => request()->param()]);
        $page = $orders->render();
        $this->assign('orders',$orders);
        $this->assign('page', $page);

        return $this->fetch();


    }


    /**
     * 下级统计报表
     */
    public function sonStatic(){
        $start_time = date('Y-m-d 00:00:00',time());
        $end_time = date('Y-m-d 23:59:59',time());
        $this->assign('start_time',$start_time);
        $this->assign('end_time',$end_time);

        return $this->fetch();
    }

    public function getSonStatic(){
        $msId = $this->agent_id;
        $whereTime = [];
         $start_time = date('Y-m-d 00:00:00',time());
        $end_time = date('Y-m-d 23:59:59',time());
        $whereTime['add_time'] = ['between time', [$start_time, $end_time]];
        $merchant = $this->modelMs->where('pid',$msId)->select();
        $level = Db::name('ms')->where('userid',$msId)->value('level');
        $data = $this->modelMs->sortTree($merchant,$msId,$level);

        if (!empty($this->request->param('start'))){
            $start_time = $this->request->param('start');

        }
        if (!empty($this->request->param('end'))){
            $end_time = $this->request->param('end');
        }

        $whereTime['add_time'] = ['between time', [$start_time, $end_time]];
//        print_r($whereTime);die;
        if (empty($data)){
            $this->result([
                'code' => 1,
                'msg'=> '暂无数据',
//                'data'=>$data
            ]);
        }
        foreach ($data as $k=>$v){
            $data[$k]['total_number'] = Db::name('ewm_order')->where($whereTime)->where(['gema_userid'=>$v['userid']])->count();
            $data[$k]['success_number'] = Db::name('ewm_order')->where($whereTime)->where(['gema_userid'=>$v['userid'],'status'=>1])->count();
            $data[$k]['total_amount'] = Db::name('ewm_order')->where($whereTime)->where(['gema_userid'=>$v['userid']])->sum('order_price');
            $data[$k]['success_amount'] = Db::name('ewm_order')->where($whereTime)->where(['gema_userid'=>$v['userid'],'status'=>1])->sum('order_price');
            if ($data[$k]['total_number'] == 0){
                $data[$k]['success_rate'] = 0;
            }else{
                $success_rate = ($data[$k]['success_number'] / $data[$k]['total_number']) * 100;
                $data[$k]['success_rate'] = sprintf("%.2f",$success_rate);
            }

        }
//        print_r($data);die;
        if (!$data){
            $this->result([
                'code' => 1,
                'msg'=> '暂无数据',
//                'data'=>$data
            ]);
        }
        $this->result([
            'code' => 0,
            'msg'=> '',
            'data'=>$data
        ]);

    }
}

