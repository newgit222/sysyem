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
use think\Cache;
use think\Config;
use think\Db;
use think\Exception;
use think\Log;

class Admin extends BaseAdmin
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
     * 管理员列表
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function index(){
        $this->userCommon();
        return $this->fetch();
    }



    public function editadminstatus(){
        $userid = $this->request->param('userid');
        $status = $this->request->param('admin_status')==1?0:1;
        if($userid == 1){
            $this->error('无权操作');
        }
        if (is_admin_login() != 1){
            $this->error('非法操作');
        }
        try {
        $res = Db::name('admin')->where('id',$userid)->update(['status'=>$status]);
            if ($res === false){
                throw new \Exception('更新管理员状态失败');
            }
//            if ($status == 0) {
//                $son_ms_status = Db::name('ms')->where('admin_id', $userid)->update(['status' => $status]);
//                if ($son_ms_status === false) {
//                    throw new \Exception('更新管理员下所有码商状态失败');
//                }
//
//                $son_user_status = Db::name('user')->where('admin_id', $userid)->update(['status' => $status]);
//
//                if ($son_user_status === false) {
//                    throw new \Exception('更新管理员下所有商户状态失败');
//                }
//            }

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
     * 获取管理员列表
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function userList(){
        $where = [];

        //组合搜索
        !empty($this->request->param('id')) && $where['id']
            = ['eq', $this->request->param('id')];

        !empty($this->request->param('username')) && $where['username']
            = ['like', '%'.$this->request->param('username').'%'];

        !empty($this->request->param('email')) && $where['email']
            = ['like', '%'.$this->request->param('email').'%'];

        !empty($this->request->param('role')) && $where['id']
            = ['eq', $this->request->param('role')];

        $data = $this->logicAdmin->getAdminList($where,true,'id asc',false);

        $count = $this->logicAdmin->getAdminCount($where);

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
            ]
        );
    }

    /**
     * 管理员添加
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function userAdd()
    {
        $this->userCommon();
        //代理组写死为2
        $groupAgentId = 2;
        //代理角色下的所有人
        $agentUserList = $this->logicAuthGroupAccess->getUserGroupInfoByGroupId($groupAgentId);
        $this->assign('agents', $agentUserList);
        $this->request->isPost() && $this->result($this->logicAdmin->seveAdminInfo($this->request->post()));

        return $this->fetch('user_add');
    }

    /**
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function userEdit()
    {

        if (is_admin_login() != 1){
            $this->error('非法操作');
        }
        $this->request->isPost() && $this->result($this->logicAdmin->seveAdminInfo($this->request->post()));
        //代理组写死为2
        $groupAgentId = 2;
        //代理角色下的所有人
        $agentUserList = $this->logicAuthGroupAccess->getUserGroupInfoByGroupId($groupAgentId);
        $this->assign('agents', $agentUserList);
        $this->assign('info',$this->logicAdmin->getAdminInfo(['id' => $this->request->param('id')]));

        return $this->fetch('user_edit');
    }

    /**
     * 管理授权
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function userAuth()
    {
        if (is_admin_login() != 1) {
            $this->error('非法操作');
        }
        $this->userCommon();

        $this->request->isPost() && $this->result($this->logicAdmin->userAuth($this->request->post()));
        $id = $this->request->param('id');
        $groupInfo = $this->logicAuthGroupAccess->getAuthGroupAccessInfoByUid($id);
        $groupId = $groupInfo['group_id'] ?? 0;
        $this->assign('groupId', $groupId);
        $this->assign('id', $id);
        return $this->fetch();
    }

    /**
     * 管理员删除
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param int $id
     */
    public function userDel($id = 0)
    {
        if (is_admin_login() != 1){
            $this->error('非法操作');
        }
        $this->result($this->logicAdmin->userDel(['id' => $id]));
    }

    /**
     * 权限组列表
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function group()
    {
        return $this->fetch();
    }

    /**
     * 获取权限组列表
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function groupList()
    {
        $where = [];

        $data = $this->logicAuthGroup->getAuthGroupList($where);

        $count = $this->logicAuthGroup->getAuthGroupCount($where);

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
            ]
        );
    }

    /**
     * 权限组添加
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function groupAdd()
    {

        $this->groupCommon();

        return $this->fetch('group_add');
    }

    /**
     * 权限组编辑
     */
    public function groupEdit()
    {
        $this->groupCommon();

        $this->assign('info', $this->logicAuthGroup->getGroupInfo(['id' => $this->request->param('id')]));

        return $this->fetch('group_edit');
    }

    /**
     * 权限组删除
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param int $id
     */
    public function groupDel($id = 0)
    {

        $this->result($this->logicAuthGroup->groupDel(['id' => $id]));
    }


    /**
     * 菜单授权
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function menuAuth()
    {

        $this->request->isPost() && $this->result($this->logicAuthGroup->setGroupRules($this->request->post()));

        $this->assign('id', $this->request->param('id'));

        return $this->fetch();
    }

    /**
     * 获取权限菜单
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function getAuthMenu(){

        $data = [
            'list' =>  $this->logicMenu->getMenuList([],'id,pid,name'),
            'checked' => str2arr($this->logicAuthGroup->getGroupRules(['id'=>$this->request->param('id')],'rules')),
        ];

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


    private function encrypt($data)
    {
        return base64_encode(openssl_encrypt($data,"AES-128-CBC",$this->secret_key,true,$this->iv));

    }


    private function decrypt($data)
    {
        return openssl_decrypt(base64_decode($data), "AES-128-CBC", $this->secret_key, true, $this->iv);
    }
    /**
     * 管理员
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    private function userCommon(){
        $this->assign('auth',$this->logicAuthGroup->getAuthGroupList());
    }


    /**
     * 权限组
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    private function groupCommon(){
        $this->request->isPost() && $this->result($this->logicAuthGroup->saveGroupInfo($this->request->post()));
    }


    /*
   *商户绑定GOOGLE验证码
   *
   */
    public function  blndGoogle()
    {
        $adminId = session('admin_info.id');
        if($this->request->isPost()){
            $data =  $this->request->post('');
            if(empty($data['google_secret_key']))
            {
                $this->result(0,'参数错误');
            }
            if(empty($data['google_code']))
            {
                $this->result(0,'请输入GOOGLE验证码');
            }
            $ret =  $this->logicGoogleAuth->checkGoogleCode($this->decrypt($data['google_secret_key']), $data['google_code']);
            if($ret==false)
            {
                $this->result(0,'绑定GOOGLE失败,请扫码重试');
            }
            unset($data['google_code']);
            $data['google_status'] = 1;
            $ret = $this->modelAdmin->where(['id'=>$adminId])->update($data);
            if($ret!==false)
            {
                $this->result(1,'绑定成功');
            }
            $this->result(0,'绑定失败');
        }
        //获取商户详细信息

        $adminInfo  = $this->logicAdmin->getAdminInfo(['id' =>$adminId]);
        $this->assign('admin',$adminInfo);
        if($adminInfo['google_status'] == 0)
        {
            $google['google_secret'] = $this->encrypt($this->logicGoogleAuth->createSecretkey());
            $google['google_qr'] = $this->logicGoogleAuth->getQRCodeGoogleUrl($this->decrypt($google['google_secret']));
            $this->assign('google',$google);
        }
        return $this->fetch('blind_google');
    }

    /**
     * 网站统计列表
     * @return mixed
     */
    public function userListStat(){
        $this->userCommon();
        return $this->fetch();
    }

    /**
     * 网站统计数据获取
     */
    public function getUserListStat(){
        $where['status'] = ['neq', -1];
        if (is_admin_login() != 1) {
            //查询
            $where['agent_id'] = is_admin_login();
        } else {
            //网站管理员组 = 5 下所有网站
            $agentUserList = $this->logicAuthGroupAccess->getUserGroupInfoByGroupId(5);
            $adminIds = array_column($agentUserList, 'uid');
            $where['id'] = ['in', $adminIds];
        }
        $whereOrder = [];
        if (!empty($this->request->param('start')) && !empty($this->request->param('end'))) {
            $whereOrder['create_time'] = ['between', [strtotime($this->request->param('start')), strtotime($this->request->param('end'))]];
        }
        $data = $this->logicAdmin->getAdminListStat($where, true, 'id asc', 10, $whereOrder);
        $count = $this->logicAdmin->getAdminCount($where);

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
            ]
        );
    }

    public function restGoogle()
    {
        if (is_admin_login() != 1) {
            $this->error('非法操作');
        }
        $ret = $this->modelAdmin->isUpdate(true)->save(array(
            'id' => $this->request->param('id', 0),
            'google_status' => 0,
            'google_secret_key' => ''
        ));
        if ($ret){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }

    }

    public function adminBill()
    {
       return $this->fetch();
    }

    public function getBillList()
    {
        $where = [];

        if (is_admin_login() !== 1){
            $where['a.admin_id'] = is_admin_login();
        }else{
            $this->request->param('admin_id') && $where['admin_id']
                = $this->request->param('admin_id');
        }

        //时间搜索  时间戳搜素
        $where['a.addtime'] = $this->parseRequestDate();

        $this->request->param('amount') && $where['a.amount']
            = $this->request->param('amount');

        $this->request->param('jl_class') && $where['a.jl_class']
            = $this->request->param('jl_class');


        $data = $this->modelAdminBill
            ->alias('a')
            ->where($where)
            ->order('addtime desc')
            ->paginate($this->request->param('limit'));


        foreach ($data as &$bill){
            if ($bill['jl_class'] == 3){
                $ewmOrder = Db::name('ewm_order')->where(['order_no'=>$bill['info']])->find();
                if ($ewmOrder){
                    $pay_type_name = Db::name('pay_code')->where('id', $ewmOrder['code_type'])->value('name');
                    $order_no = $ewmOrder['order_no'];
                    $amount = $ewmOrder['order_price'];
                    $bill['info'] = $pay_type_name . '-' . $order_no . '-' . $amount;
                }
            }

            if ($bill['jl_class'] == 4){
                $daifuOrder = Db::name('daifu_orders')->where(['trade_no'=>$bill['info']])->find();
                if ($daifuOrder){
                    $pay_type_name = '代付';
                    $order_no = $daifuOrder['trade_no'];
                    $amount = $daifuOrder['amount'];
                    $bill['info'] = $pay_type_name . '-' . $order_no . '-' . $amount;
                }
            }

        }

        $this->result($data->total() ?
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
            ]
        );
    }

    public function usdtRecharge()
    {
        return $this->fetch('usdtRecharge');
    }

    public function rechargeAdd()
    {
        if ($this->request->isAjax()){
            $amount = $this->request->param('amount', 0);
            if ($amount <= 0){
                $this->error('金额错误');
            }
            $usdt_num = $this->logicAdminRechargeRecord->getOnlyRechargeAmount($amount);
            if (!$usdt_num){
                $this->error('USDT金额计算失败');
            }

            $usdt_address = $this->modelConfig->where('name', 'usdt_recharge_address')->value('value');

            if (!$usdt_address){
                $this->error('usdt充值地址未配置，请通知管理员配置地址！');
            }

            $ret = $this->modelAdminRechargeRecord->save(array(
                'admin_id' => is_admin_login(),
                'status' => 0,
                'amount' => $amount,
                'usdt_num' => $usdt_num,
                'usdt_address' => $usdt_address
            ));

            if ($ret){
                $this->result([
                    'code' => CodeEnum::SUCCESS,
                    'msg' => '操作成功',
                    'usdt_num' => $usdt_num,
                    'usdt_address' => $usdt_address
                ]);
            }else{
                $this->error('操作失败');
            }
        }
        return $this->fetch('rechargeAdd');
    }

    public function getRechargeList()
    {
        $where = [];
        $where['a.admin_id'] = is_admin_login();

        //时间搜索  时间戳搜素
        $where['create_time'] = $this->parseRequestDate();

        $this->request->param('amount') && $where['a.amount']
            = $this->request->param('amount');
        $this->request->param('usdt_num') && $where['a.usdt_num']
            = $this->request->param('usdt_num');

        $data = $this->modelAdminRechargeRecord
            ->alias('a')
            ->where($where)
            ->order('create_time desc')
            ->paginate($this->request->param('limit'));

        $this->result($data->total() ?
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
            ]
        );
    }

    public function editRate()
    {
        if (is_admin_login() != 1){
            $this->error('非法操作');
        }

        if ($this->request->isPost()){
            $data = $this->request->post('r/a');
            if (is_array($data)){
                foreach ($data as  $key=>$item) {

                    if ($item['admin_rate'] < 0){
                        return ['code' => CodeEnum::ERROR, 'msg'=>'费率最低为0'];
                    }

                    $res = Db::name('admin_rate')->where(['admin_id'=>$item['admin_id'],'code_type_id'=>$item['code_type_id']])->select();

                    if ($res){
                        //修改
                        Db::name('admin_rate')->where(['admin_id'=>$item['admin_id'],'code_type_id'=>$item['code_type_id']])->update(['rate' => $item['admin_rate'], 'update_time' => time()]);
                    }else{
                        //新增
                        Db::name('admin_rate')->insert( [
                            'admin_id' => $item['admin_id'],
                            'code_type_id' => $item['code_type_id'],
                            'rate' => $item['admin_rate'],
                            'create_time' => time(),
                            'update_time' => time(),
                        ]);
                    }

                }
                return ['code' => CodeEnum::SUCCESS, 'msg' => '费率配置成功'];
            }
        }

        $list = Db::name('pay_code')->where('status',1)->select();
        //查询自己的费率
        $myRate = Db::name('admin_rate')->where('admin_id',$this->request->param('id'))->select();

        if ($myRate){
            foreach ($list as $k=>$v){
                $list[$k]['admin_rate'] = 0;
                foreach ($myRate as $key=>$val){
                    if ($v['id'] == $val['code_type_id']){
                        $list[$k]['admin_rate'] = sprintf("%.3f",$val['rate']);
                    }
                }
            }
        }else{
            foreach ($list as $key=>$val){
                $list[$key]['admin_rate'] = '0.000';
            }
        }
        $this->assign('list', $list);
        return $this->fetch();


       /* $adminInfo = $this->modelAdmin->where('id', $this->request->param('id'))->find();

        if ($this->request->isPost()){
            $rate = $this->request->param('rate');
            if (empty($rate)){
                $this->error('费率不能为空');
            }

            $adminInfo->rate = $rate;
            $adminInfo->save();
            $this->success('操作成功');
        }
        $this->assign('info', $adminInfo);
        return $this->fetch();*/
    }

    public function changeBalance()
    {
        if (is_admin_login() != 1){
            $this->error('非法操作');
        }
        $adminInfo = $this->modelAdmin->where('id', $this->request->param('id'))->find();
        if ($this->request->isPost()){

            $data = $this->request->post();

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
            if ($data['op_type'] == 0 && bccomp($data['money'], $adminInfo['balance']) == 1) { //减少
                $this->error('减少资金不可小于用户本金');
            }
            Db::startTrans();
            $ret = $this->logicAdminBill->addBill($adminInfo['id'], 2, $data['op_type'], $data['money'], $data['opInfo']);
            if ($ret) {
                Db::commit();
                $this->success('操作成功', url('index'));
            }

            Db::rollback();
            $this->error('操作失败');
        }
        $this->assign('adminInfo', $adminInfo);
        return $this->fetch();
    }

    public function adminBalance()
    {
//       $balance = $this->modelAdmin->where('id', is_admin_login())->value('balance');
        $balance = Db::name('admin')->where('id',is_admin_login())->value('balance');
       if ( \app\common\logic\Config::isSysEnableRecharge() && $balance<50){
           return json(['code' => 1,  'msg' => '您的余额不足50，请及时充值！']);
       }
        return json(['code' => 0]);
    }

    public function setCommand()
    {
//        $adminId = session('admin_info.id');

        if ($this->request->post()){
            $at_command = $this->request->post('at_command', '');
            $command = $this->request->post('command');
            $confirm_command = $this->request->post('confirm_command');
            $sys_operating_password = getAdminPayCodeSys('sys_operating_password', 256 , is_admin_login());
            if ($sys_operating_password){
                if (md5($at_command) != $sys_operating_password){
                    return json(['code' => 0,  'msg' => '当前口令错误' ]);
                }
            }
            if (empty($command) or empty($confirm_command)){
                return json(['code' => 0,  'msg' => '数据错误!' ]);
            }
            if ($command!=$confirm_command){
                return json(['code' => 0,  'msg' => '两次口令输入不一致!' ]);
            }
            if (strlen($command) < 4) {
                return ['code' => CodeEnum::ERROR, 'msg' => '口令必须大于4位'];
                return json(['code' => 0,  'msg' => '口令必须大于4位!' ]);
            }
            if (is_numeric($command)){
                return json(['code' => 0,  'msg' => '口令不能未纯数字' ]);
            }
            $command = md5($command);
            $where = [
                'type' => 256,
                'name' => 'sys_operating_password',
                'admin_id' => is_admin_login()
            ];
            $config = $this->modelConfig->where($where)->find();
            if ($config){
                $config->value = $command;
                $config->save();
            }else{
                $insertData['name'] = 'sys_operating_password';
                $insertData['title'] = '设置操作口令';
                $insertData['type'] = 256;
                $insertData['value'] = $command;
                $insertData['admin_id'] = is_admin_login();
                $insertData['create_time'] = time();
                $insertData['update_time'] = time();
                $this->modelConfig->insert($insertData);
            }
            return json(['code' => 1,  'msg' => '设置成功' ]);
        }
        $this->assign('sys_operating_password', getAdminPayCodeSys('sys_operating_password', 256 , is_admin_login()));
        return $this->fetch();
    }

    public function adminCommand()
    {
        $command = getAdminPayCodeSys('sys_operating_password', 256, is_admin_login());
        if (!$command){
            return json(['code' => 1]);
        }
        return json(['code' => 0]);
    }


    public function security_code(){
        $admin_info =  $this->modelAdmin->find(is_admin_login());
        if ($this->request->isPost()){
            $data = $this->request->param();
//            print_r($data);die;
            if ($data['security_code'] != $data['confirm_security_code']){
                return json(['code' => 0,  'msg' => '两次输入的安全码不一致' ]);
            }
            $rule = [
                'security_code' => 'require|regex:/^(?=.*[a-zA-Z])(?=.*\d).{6,}$/',
                'confirm_security_code' => 'require|regex:/^(?=.*[a-zA-Z])(?=.*\d).{6,}$/',
            ];

            $message = [
                'security_code.require' => '安全码不能为空',
                'confirm_security_code.require' => '安全码不能为空',
                'security_code.regex' => '安全码必须大于6位并且包含数字和字母',
                'confirm_security_code.regex' => '安全码必须大于6位并且包含数字和字母',
            ];

            $validate = new \think\Validate($rule, $message);
//            $data = [
//                'security_code' => $data['security_code'],
//            ];
            if (!$validate->check($data)) {
                // 验证失败，输出错误信息
                return json(['code' =>0,  'msg' => $validate->getError()]);
            }


            if (!$admin_info['security_code']){
                $res = Db::name('admin')->where('id',is_admin_login())->update(['security_code'=>md5($data['security_code'])]);
                if ($res){
                    return json(['code' => 1,  'msg' => '设置成功' ]);
                }
                return json(['code' => 0,  'msg' => '设置失败' ]);
            }else{
                if (md5($data['at_security_code']) != $admin_info['security_code']){
                    return json(['code' => 0,  'msg' => '安全码校验失败' ]);
                }
                $res = Db::name('admin')->where('id',is_admin_login())->update(['security_code'=>md5($data['security_code'])]);
                if ($res){
                    return json(['code' => 1,  'msg' => '设置成功' ]);
                }
                return json(['code' => 0,  'msg' => '设置失败' ]);

            }
        }
        $this->assign('security_code',$admin_info['security_code']);
        return $this->fetch('security_code');
    }

    public function viewConfig()
    {

        if (is_admin_login() != 1){
            $this->error('非法操作');
        }

        $admin_id = $this->request->param('id');

        $config = require_once('./data/conf/adminSysPayCode.php');


        $list = $config['admin_sys_config'];
        foreach ($list as $k=>$v){
            if ($v['name'] == 'sys_operating_password'){
                unset($list[$k]);
                continue;
            }
            $where['admin_id'] = $admin_id;
            $where['type'] = $v['type'];
            $where['name'] = $v['name'];
            $res = Db::name('config')->where($where)->find();
            if ($res){
                if ($v['attr'] == 'select'){
//                    unset($res['extra']);
                    $res['extra'] = $v['extra'];
                }
                if ($res['name'] == 'cashier_address'){
                    unset($res['extra']);
                    $res['extra'] = $v['extra'];
                }

                $list[$k] = $res;
                $list[$k]['attr'] = $v['attr'];
                $list[$k]['option_type'] = $v['option_type'];
            }
        }

        $this->assign('list',$list);

        return $this->fetch();
    }

}