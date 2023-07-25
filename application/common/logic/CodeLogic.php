<?php

namespace app\common\logic;

use app\admin\model\UserModel;
use app\agent\model\AgentLogModel;
use app\api\model\BankStatementModel;
use app\common\library\enum\CodeEnum;
use app\common\model\EwmPayCode;
use app\common\model\GemapayCodeModel;
use app\ms\Logic\SecurityLogic;
use app\pay\controller\PayController;
use think\Db;

class CodeLogic extends BaseLogic
{

    public function getQueenPostion($codeId, $type, $admin_id)
    {
        $Queue = new Queuev1Logic();
        //获取队列
        $code_queue = $Queue->get($type, $admin_id);
        //如果不在队列中 返回0

        if (!$code_queue || !in_array($codeId, $code_queue)) {
            return '2147483647';
        }
        //在队列中 返回键+1
        return $Queue->kget($codeId, $type, $admin_id) + 1;

    }

      public function addQunQRcode($insertData)
      {

        //验证安全码
        $UserModel = $this->modelMs;
        $userId = session('agent_id');
        $userInfo = $UserModel->find($userId);
        //是否设置费率
        if (empty($userInfo['bank_rate'])) {
//            return ['code' => CodeEnum::ERROR, 'msg' => '未找到银行卡费率'];
        }

        $SecurityLogic = new SecurityLogic();
        $res = $SecurityLogic->checkSecurityByUserId($userId, $insertData['security']);
        if ($res['code'] == CodeEnum::ERROR) {
            return $res;
        }

        $data['ms_id'] = $userId;
      //  $data['bank_name'] = $insertData['bank_name'];
        $data['account_name'] = $insertData['account_name'];
//        $data['limit__total'] = $insertData['limit__total'];
        $data['bonus_points'] = $userInfo['bank_rate'];
        $data['user_name'] = $userInfo['username'];
        $data['create_time'] = time();
        $data['updated_at'] = time();
        $data['status'] = $insertData['is_active'];
		$data['code_type'] = 4;
		$data['image_url'] = $insertData['file_url'];
        $result = $this->modelEwmPayCode->insertGetId($data);
        if (!$result) {
            return ['code' => CodeEnum::ERROR, 'msg' => '保存失败,请一会儿再试'];
        }
        $is_active = $insertData['is_active'];
        //如果激活 添加到队列
        if ($is_active && $userInfo['work_status'] == 1) {
            $QueueLogic = new Queuev1Logic();
            $userInfo['add_admin_id'] = 1;//写死
            $QueueLogic->radd($result, 3, $userInfo['add_admin_id']);

        }
        return ['code' => CodeEnum::SUCCESS, 'msg' => '添加成功'];
    }


    /**
     * 添加二维码现在只要银行卡
     * @param $data
     * @return array
     */
    public function addQRcode($insertData,$code_type)
    {
        //验证安全码
        $UserModel = $this->modelMs;
        $userId = session('agent_id');
        $userInfo = $UserModel->find($userId);
        //是否设置费率
        if (empty($userInfo['bank_rate'])) {
//            return ['code' => CodeEnum::ERROR, 'msg' => '未找到银行卡费率'];
        }

        //验证最大码子数量
//        $admin_id = $this->modelMs->where('userid', $userId)->value('admin_id');
//        $max_ewm_num = getAdminPayCodeSys('max_ewm_num', $code_type, $admin_id);
//        if (empty($max_ewm_num) && $max_ewm_num !==0){
//            $max_ewm_num = 20;
//        }
//        $code_num = $this->modelEwmPayCode->where(['ms_id' => $userId, 'code_type' => $code_type])->count();
//        if ($code_num >= $max_ewm_num){
//            return ['code' => CodeEnum::ERROR, 'msg' =>'二维码数量已达上限'];
//        }

        $SecurityLogic = new SecurityLogic();
        $res = $SecurityLogic->checkSecurityByUserId($userId, $insertData['security']);
        if ($res['code'] == CodeEnum::ERROR) {
            return $res;
        }
        $data['code_type'] = $code_type;
        $data['ms_id'] = $userId;
        $data['bank_name'] = $insertData['bank_name'] ?? '';
        $data['account_name'] = $insertData['account_name'] ?? '';
        $data['account_number'] = $insertData['account_number'] ?? '';
        $data['remark'] = $insertData['remark'] ?? '';
        $code_name = Db::name('pay_code')->where('id',$code_type)->value('name');
        $ms_username = Db::name('ms')->where('userid', $userId)->value('username');
        $admin_id = Db::name('ms')->where('userid', $userId)->value('admin_id');
        action_log('添加银通道'. $code_name, '码商'. $ms_username . '添加' . $code_name . '('. $code_type .')' .'，基本信息：账号-' . $data['account_number'] .
        '|账号名称-' . $data['account_name'] . '|银行名称-' .   $data['bank_name']
        );
        if ($code_type == 30){
            $data['bank_four_code'] = substr($insertData['account_number'],-4);
//            action_log('添加银行卡通道', '码商'. $userId . '添加银行卡，卡号：'. $insertData['account_number']);
            $chachong =$this->modelEwmPayCode->where(['ms_id'=>$data['ms_id'],'code_type'=>$data['code_type'],'account_number'=>$data['account_number'],'is_delete'=>0])->select();
            if (!empty($chachong)){
                return ['code' => CodeEnum::ERROR, 'msg' => '不可重复添加同一收款设备'];
            }
        }

        if ($code_type == 50){
            $data['bank_four_code'] = substr($insertData['account_number'],-4);
            $chachong =$this->modelEwmPayCode->where(['ms_id'=>$data['ms_id'],'code_type'=>$data['code_type'],'account_number'=>$data['account_number'],'is_delete'=>0])->select();
            if (!empty($chachong)){
                return ['code' => CodeEnum::ERROR, 'msg' => '不可重复添加同一收款设备'];
            }
        }
        $data['min_money'] = sprintf("%.2f",$insertData['min_money']);
        $data['max_money'] = sprintf("%.2f",$insertData['max_money']);
        $ms_code_money_status = getAdminPayCodeSys('ms_code_money_status',$code_type,$admin_id);
        if (empty($ms_code_money_status)){
            $ms_code_money_status = 1;
        }
        if ($ms_code_money_status == 2){
            $api_min_money = getAdminPayCodeSys('api_min_money',$code_type,$admin_id);
            if (!empty($api_min_money) && $api_min_money > 0){
                $data['min_money'] = sprintf("%.2f",$api_min_money);
            }

            $api_max_money = getAdminPayCodeSys('api_max_money',$code_type,$admin_id);
            if (!empty($api_max_money) && $api_max_money > 0){
                $data['max_money'] = sprintf("%.2f",$api_max_money);
            }
        }


        $data['bonus_points'] = $userInfo['bank_rate'];
        $data['user_name'] = $userInfo['username'];
        $data['limit__total'] = $insertData['limit__total'];
        $data['create_time'] = time();
        $data['updated_at'] = time();
//        $data['status'] = $insertData['is_active'];


        $data['api_pay_money'] = sprintf("%.2f",$insertData['api_pay_money'] ?? '') ;
        $data['pay_money'] = sprintf("%.2f",$insertData['pay_money'] ?? '');
        $data['success_order_num'] = $insertData['success_order_num'];
        $data['image_url'] = $insertData['image_url'] ?? '';
        $data['publicKey'] = $insertData['publicKey'] ?? '';
        $data['privateKey'] = $insertData['privateKey'] ?? '';
        $data['account_type'] = $insertData['account_type'] ?? 1;

        if (!empty($insertData['expiration_time'])){
            $data['expiration_time'] = strtotime($insertData['expiration_time']) ?? '';
        }


        $result = $this->modelEwmPayCode->insertGetId($data);
        if (!$result) {
            return ['code' => CodeEnum::ERROR, 'msg' => '保存失败,请一会儿再试'];
        }
//        $is_active = $insertData['is_active'];
        //如果激活 添加到队列
//        if ($is_active && $userInfo['work_status'] == 1) {
//            $QueueLogic = new Queuev1Logic();
//            $userInfo['add_admin_id'] = 1;//写死
//            $QueueLogic->radd($result, 3, $userInfo['add_admin_id']);
//
//        }

        return ['code' => CodeEnum::SUCCESS, 'msg' => '添加成功'];
    }


    /**
     * 激活二维码
     * @param $userId
     * @param $codeId
     * @return array
     */
    public function activeCode($userId, $codeId)
    {
        $UserModel = $this->modelMs;
        $userinfo = $UserModel->where(['userid' => $userId])->find();
        $GemapayCodeModel = $this->modelEwmPayCode;
        $where['id'] = $codeId;
        $where['ms_id'] = $userId;
        $data['status'] = $GemapayCodeModel::STATUS_YES;
        $data['forbidden_reason'] = '';
        $data['updated_at'] = time();
        $codeInfo = $GemapayCodeModel->where($where)->find();
        $ret = $GemapayCodeModel->where($where)->update($data);
        if ($ret !== false) {
            //判断用户是否在开工
            $user = $UserModel->where(['userid' => $userId])->find();
            if ($user['work_status'] == CodeEnum::SUCCESS) {
                $QueueLogic = new Queuev1Logic();
                $codeInfo['type'] = 3;
                $QueueLogic->radd($codeId, $codeInfo['type'], 1);
            }
            return ['code' => CodeEnum::SUCCESS, 'msg' => '激活成功！'];
        } else {
            return ['code' => CodeEnum::ERROR, 'msg' => '已经激活成功！'];
        }
    }



    /**
     * 冻结二维码
     * @param $userId
     * @param $codeId
     * @return array
     */
    public function disactiveCode($userId, $codeId)
    {
        $UserModel = $this->modelMs;
        $userinfo = $UserModel->where(['userid' => $userId])->find();
        $GemapayCodeModel = $this->modelEwmPayCode;
        $codeInfo = $GemapayCodeModel->find($codeId);
        $where['id'] = $codeId;
	$where['ms_id'] = $userId;
        $data['status'] = $GemapayCodeModel::STATUS_NO;
        $data['updated_at'] = time();
	$ret = $GemapayCodeModel->where( $where)->update($data);
        if ($ret !== false) {
            $QueueLogic = new Queuev1Logic();
	    $QueueLogic->delete($codeId,3,1);
            return ['code' => CodeEnum::SUCCESS, 'msg' => '冻结成功！'];
        } else {
            return ['code' => CodeEnum::ERROR, 'msg' => '已经冻结成功！'];
        }
    }


}
