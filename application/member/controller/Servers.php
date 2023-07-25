<?php
/**
 * @copyright © 2022 by 技术先锋 All rights reserved。
 * @Created   by PhpStorm.
 * @author    StarsPhp
 * @date      2022/12/7
 */
declare (strict_types=1);

namespace app\member\controller;

use app\common\library\enum\CodeEnum;
use app\common\logic\GoogleAuth;
use app\common\logic\MsMoneyType;
use app\common\model\EwmOrder;
use app\common\model\Ms;
use app\member\Logic\SecurityLogic;
use think\Cookie;
use think\Db;
use think\Request;
use think\Validate;
use think\Cache;

class Servers extends Base
{
    /**
     * var string $secret_key 加解密的密钥
     */
    protected $secret_key  = 'f3a59b69324c831e';
    
    /**
     * var string $iv 加解密的向量，有些方法需要设置比如CBC
     */
    protected $iv = '7fc7fe7d74f4da93';
    
    public function index()
    {

        return $this->fetch();
    }

    public function setInfo(){
        $info = Db::name('ms')->where('userid',$this->agent_id)->select();
        return $this->result(['code'=>0,'data'=>$info]);
    }

    public function upload_https(Request $request)
    {
        // 请替换为您的 HTTP 上传接口 URL
        $pay_img_service_address = getAdminPayCodeSys('pay_img_service_address',256,$this->agent['admin_id']);
        if (empty($pay_img_service_address)){
            $uploadUrl = 'http://payimg1.niuniualicloudid001.xyz/upload.php';
        }else{
            $uploadUrl = $pay_img_service_address;
        }


        // 获取客户端请求的数据
        $file = $request->file('image');

        // 检查文件是否接收成功
        if (!$file) {
            return  json_encode(['code' => 404, 'msg' => '为获取到上传信息']);
        }

        // 使用 cURL 将文件 POST 到您的 HTTP 上传接口
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uploadUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'image' => curl_file_create($file->getRealPath(), $file->getMime(), $file->getInfo('name'))
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        // 将 HTTP 上传接口的响应返回给客户端
        return json(json_decode($response, true));
    }


    public function edit_work_status(){
        $status = $this->request->param('status') == 1?0:1;
        if ($status == 1){
            $ms = Ms::where(['userid' => $this->agent_id])->field('admin_work_status')->find();
            if (empty($ms['admin_work_status'])){
                return json([
                    'code' => 404,
                    'msg' => '你的接单权限已经被禁用，请联系管理员打开'
                ]);
            }
        }

    if ($status == 1){
        $team = getTeamPid($this->agent_id);
        foreach ($team as $v){
            $team_work_status = Db::name('ms')->where('userid',$v)->value('team_work_status');
            if ($team_work_status == 0){
                    return json([
                        'code' => 404,
                        'msg' => '你的所属团队未开启接单！'
                    ]);
                    break;
            }
            continue;
        }
    }



        $res = Ms::where(['userid' => $this->agent_id])->update(['work_status'=>$status]);
        if($res === false){
            return json([
                'code' => 404,
                'msg' => '操作失败'
            ]);
        }else{
            if ($status == 1){
                $reason = '开启';
            }else{
                $reason = '关闭';
            }

            action_log('码商编辑开工','码商'.$this->agent['username'].$reason.'接单状态');

            return json([
                'code' => 1,
                'msg' => '操作成功'
            ]);
        }

    }

//    public function edit_daifu_status(){
//        $status = $this->request->param('is_daifu') == 1?0:1;
//        if ($status == 1){
//            $team = getTeamPid($this->agent_id);
//            foreach ($team as $v){
//                $team_work_status = Db::name('ms')->where('userid',$v)->value('team_work_status');
//                if ($team_work_status == 0){
//                    return json([
//                        'code' => 404
//                    ]);
//                }
//                continue;
//            }
//        }
//        $res = Ms::where(['userid' => $this->agent_id])->update(['is_daifu'=>$status]);
//        if($res === false){
//            return json([
//                'code' => 404
//            ]);
//        }else{
//            return json([
//                'code' => 1
//            ]);
//        }
//
//    }

    public function new_order_music(){
        $status = $this->request->param('status');
        if ($status == 1){
            Cookie::set('new_order_music',1);
//            $this->assign('time','30');
            return json([
                'code' => 1,
                'msg' => '开启成功'
            ]);
        }else{
            Cookie::set('new_order_music',0);
            return json([
                'code' => 1,
                'msg' => '关闭成功'
            ]);
        }

    }





    public function editPass()
    {
        if ($this->request->isPost()) {
            $oldPassword = $this->request->post('old_password');
            $newPassword = $this->request->post('new_password');
            $newRePassword = $this->request->post('re_new_password');
            return $this->updateLoginPassword($this->agent->userid, $oldPassword, $newPassword, $newRePassword);
        }
        $this->assign('info', $this->agent);
        return $this->fetch('editPass');
    }
    
    private function updateLoginPassword($userId, $oldPassword, $newPassword, $newRePassword)
    {
        if (empty($oldPassword)) {
            return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '请输入登录密码'];
        }
        
        if (strlen($newPassword) < 6 || strlen($newPassword) > 16) {
            return ['code' => CodeEnum::ERROR, 'msg' => '密码必须大于6位,小于16位！'];
        }

        if ($newPassword != $newRePassword) {
            return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '两次输入登录密码不一致'];
        }
        $vadata['pass'] = $newPassword;

        $validate = new Validate([
//            'pass'  => 'regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]+$/',
            'pass'  => 'require|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/',
        ],[
            'pass.regex' => '密码必须至少包含一个大写字母，一个小写字母，一个数字，且长度大于7位'
        ]);


        if (!$validate->check($vadata)) {
            return json(['code'=>CodeEnum::ERROR, 'msg'=>$validate->getError()]);
        }


        $User = $this->modelMs;
        // $User->startTrans();
        //验证旧密码
        if (!$this->check_pwd_one($oldPassword, $userId)) {
            return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '旧登录密码错误'];
        }

        //=============登录密码加密==============
        if ($newPassword) {
            $salt = substr(md5(time().''), 0, 3);
            $data['login_salt'] = $salt;
            $data['login_pwd'] = pwdMd52($newPassword, $salt);
        }

        
        $where['userid'] = $userId;
        $data['firs_login'] = 1;
        $res = $User->where($where)->update($data);
        if ($res) {
            //     $User->commit();
//            Cache::set('user_LoginPassword_'.$userId,'0');
            $this->success('操作成功', url("Index/index"));
        } else {
            //   $User->rollback();
            return ['code' => \app\common\library\enum\CodeEnum::ERROR, 'msg' => '修改失败'];
        }
    }
    
    //验证登录密码是否正确
    public function check_pwd_one($value, $userId)
    {
        $where['userid'] = $userId;
        
        $modelMs = new \app\common\model\Ms();
        $u_info = $modelMs->where($where)->field('login_pwd,login_salt')->find();
        $salt = $u_info['login_salt'];
        $pwd = $u_info['login_pwd'];
        if ($pwd == pwdMd52($value, $salt)) {
            return true;
        } else {
            return false;
        }
    }
    
    
    public function bindGoogle()
    {
        $google = new GoogleAuth();
        if($this->request->isPost()){
            $data =  $this->request->post();
            if(empty($data['google_secretkey']))
            {
                return ['code' => CodeEnum::ERROR, 'msg' => '参数错误！'];
            }
            if(empty($data['google_code']))
            {
                return ['code' => CodeEnum::ERROR, 'msg' => '请输入GOOGLE验证码！'];
            }
            $ret =  $google->checkGoogleCode($this->decrypt($data['google_secretkey']), $data['google_code']);
            if($ret === false)
            {
                return ['code' => CodeEnum::ERROR, 'msg' => '绑定GOOGLE失败,请扫码重试！'];
            }
            unset($data['google_code']);
            $data['google_status'] = 1;
//            $ret = $this->modelAdmin->where(['id'=>$adminId])->update($data);
            $res = Db::name('ms')->where(['userid'=>$this->agent_id])->update($data);
            if($res === false)
            {
                return ['code' => CodeEnum::ERROR, 'msg' => '绑定失败！'];
            }
            return ['code' => CodeEnum::SUCCESS, 'msg' => '绑定成功'];

        }
        //获取商户详细信息
    
        $this->assign('admin',$this->agent);
        if($this->agent['google_status'] == 0)
        {
            $google['google_secret'] = $this->encrypt($google->createSecretkey());
            $google['google_qr'] = $google->getQRCodeGoogleUrl($this->decrypt($google['google_secret']));
            $this->assign('google',$google);
        }
        return $this->fetch('bindGoogle');
    }
    
    
    private function encrypt($data)
    {
        return base64_encode(openssl_encrypt($data,"AES-128-CBC",$this->secret_key,1,$this->iv));
        
    }
    
    private function decrypt($data)
    {
        return openssl_decrypt(base64_decode($data), "AES-128-CBC", $this->secret_key, 1, $this->iv);
    }
    
    public function editPayPass()
    {
        if ($this->request->isPost()) {
            $SecurityLogic = new SecurityLogic();
            $security = $this->request->post('security');
            $re_security = $this->request->post('re_security');
            $old_security = $this->request->post('old_security');
        
            return $SecurityLogic->changeSecurity($this->agent->userid, $security, $re_security, $old_security);
        }
        $this->assign('data', $this->agent);
        return $this->fetch('editPayPass');
    }

    

    //划分余额
    public function dividedBalance(){
        if ($this->request->isPost()){
            $result = $this->validate(
                [
                    '__token__' => $this->request->post('__token__'),
                    'dividedBalance' => $this->request->post('dividedBalance'),
                ],
                [
                    '__token__' => 'require|token',
                    'dividedBalance' => 'require'
                ],[
                'dividedBalance.require' => '金额不能为空！',
            ]);
            if (true !== $result) {
                $this->error($result);
            }
            $money = $this->request->param('dividedBalance');
            if (!empty($money)){
                //查询自己余额
                Db::startTrans();
                try {
                    $my = Db::name('ms')->where('userid',$this->agent_id)->find();
                    if ($my['money'] < $money){
                        $this->error('余额不足');
                    }
                    if ($my['pid'] <= 0){
                        $this->error('未知错误');
                    }
                    $superior = Db::name('ms')->where('userid',$my['pid'])->find();
                    if (empty($superior)){
                        $this->error('上级信息有误');
                    }
                    $info = '码商（ID：'.$this->agent_id.'）手动给上级（'.$superior['userid'].'），增加余额'.$money;
                    $ret = accountLog($superior['userid'], MsMoneyType::TRANSFER, 1, $money, $info);

                    $infos = '码商（ID：'.$this->agent_id.'）手动给上级（'.$superior['userid'].'），扣减自身余额'.$money;
                    $rets = accountLog($this->agent_id, MsMoneyType::TRANSFER, 0, $money, $infos);
                    if ($ret && $rets) {
                        Db::commit();
                    }
                } catch (\Exception $e){
                    Db::rollback(); // 回滚事务
                    $this->error($e->getMessage(),'index/index');
                }
                $this->success('余额划分成功',url('index/index'));
            }
        }
        return $this->fetch();
    }



    public function lastOrder()
    {
//        echo 1;die;
//        $ewmOrderModel = new EwmOrder();
//        $where = [
//            'gema_userid' => $this->agent_id,
////            'code_type' => $this->modelPayCode->where('code', $this->pay_code)->value('id')
//        ];
//        $order = $ewmOrderModel->where($where)->order('id desc')->find();
//        echo $order ? $order['id'] : 0;
        // 获取当前登录用户的代理商ID
        $agentId = $this->agent_id; // 将该变量替换为获取当前登录用户代理商ID的逻辑

        // 获取上一次记录的最大订单ID
        $lastOrderId = Cache::get('last_order_id_' . $agentId);
        // 分页查询新订单
      //  $pageSize = 100; // 每页查询数量，可根据实际情况调整
        // 查询新订单
        $newOrders = Db::name('ewm_order')
            ->where('gema_userid', $agentId)
            ->where('status', '0')
            ->order('id desc')
//            ->where('id', '>', $lastOrderId)
//            ->limit($pageSize)
            ->find();

        if ($newOrders['id']  - $lastOrderId > 0){
           // $currentOrderId = $newOrders[$newOrderCount - 1]['id']; // 最新的订单ID
            Cache::set('last_order_id_' . $agentId, $newOrders['id']);
            return json(['status' => 1, 'count' => $newOrders['id']]);
        } else {
            // 没有新订单
            return json(['status' => 0]);
        }
//
//        $newOrderCount = count($newOrders);
//
//        if ($newOrderCount > 0) {
//            // 有新订单
//            $currentOrderId = $newOrders[$newOrderCount - 1]['id']; // 最新的订单ID
//            Cache::set('last_order_id_' . $agentId, $currentOrderId);
//            return json(['status' => 1, 'count' => $newOrderCount]);
//        } else {
//            // 没有新订单
//            return json(['status' => 0]);
//        }
    }

}
