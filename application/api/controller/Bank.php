<?php

namespace app\api\controller;
use app\api\service\ApiPayment;
use app\common\controller\BaseApi;
use app\common\library\enum\CodeEnum;
use app\common\logic\EwmOrder;
use app\ms\Logic\SecurityLogic;
use think\Log;
use think\Db;
class Bank extends BaseApi
{

    public function banksms(){

        $key= $this->request->param('key');

        if (empty($key)){
            $this->error('未知错误');
        }
        $replace = '+';
        $search = 'asdefg';
        $key = str_ireplace($search, $replace, $key);
        $ms = explode("+abc",encrypts($key,'D'));
        if (empty($ms)){
            $this->error('error');
        }
//        print_r($ms);die;
        $ms_id = $ms[0];

        if (empty($ms_id)){
            $this->error('未知错误');
        }



        $data =  $this->request->post();

        if(empty($data)){
            $this->error('没数据');
        }

        if (empty($data['source'])){
            $this->error('数据错误');
        }

        if (empty($data['context'])){
            $this->error('ERROR');
        }

        $ms = Db::name('ms')->where(['userid'=>$ms_id,'reg_date'=>$ms[1]])->find();

        $insetData = [
            'phone' => $data['source'],
            'context' => $data['context'],
            'ms_id' => $ms['userid'],
            'create_time' => date('Y-m-d H:i:s',time()),
            'ip'=> get_userip(),
            'admin_id' => $ms['admin_id'],
        ];

        $banktobankSmsID = Db::name('banktobank_sms')->insertGetId($insetData);

        if (empty($ms)){
            $this->error('请核对请求链接内容'.$key);
        }

        if ($ms['status'] == 0){
            $this->error('账号被禁用');
        }

        if ($ms['work_status'] == 0){
            $this->error('账号未开工');
        }
        $verify_ms_callback_ip = getAdminPayCodeSys('verify_ms_callback_ip',30,$ms['admin_id']);
        if (empty($verify_ms_callback_ip)){
            $verify_ms_callback_ip = 2;
        }
        if ($verify_ms_callback_ip == 1 && empty($ms['monitor_ip'])){
            $this->error('请联系管理员设置回调ip');
        }
//      if (empty($ms['monitor_ip'])){
//          $this->error('请联系管理员设置回调ip');
//      }

        // preg_match('/\d+/',$data['source'],$phoneCode);


        $pay_name_type = Db::name('config')->where(['name'=>'get_pay_name_type','type'=>30,'admin_id'=>$ms['admin_id']])->value('value');
        $info =  $this->getInfo( $data['context'],$data['source']);
        $balance = $this->getBalance( $data['context'],$data['source']);
        $balance=str_replace(",","",$balance);
        if(empty($info) || empty($info['banker_num']))
        {
            $this->error('未匹配到任何信息banknunm='.$info['banker_num'].'&balance='.$balance);
        }
        $banker_name  = $info['banker_name'];
        $banker_num  = trim($info['banker_num']);

        $smsTime  = $info['sms_time'];
        $is_pay_name = getAdminPayCodeSys('is_pay_name',30,$ms['admin_id']);
        if (empty($is_pay_name)){
            $is_pay_name = 1;
        }
        $where = [];
        if ($is_pay_name == 1){
            if (array_key_exists('pay_name',$info)){
//            农业银行你好
//            $result = substr($info['pay_name'],strripos($info['pay_name'],'行')+strlen('行'));
                if ($banker_name == '湖北农信'){
                    if (strrpos($info['pay_name'],'行')){
                        $info['pay_name'] = substr($info['pay_name'],strripos($info['pay_name'],'行')+strlen('行'));
                    }
                }
                if ($banker_name == '辽宁农信'){
                    $info['pay_name'] = substr($info['pay_name'],0,-4);
                }
                if (empty($pay_name_type)){
                    $where['pay_username'] = $info['pay_name'];
                }else{
                    if ($pay_name_type == 2){
                        $where['pay_username'] = $info['pay_name'];
                    }else{
                        $where['pay_username'] = $info['pay_name'];
                    }
                }
            }
        }

        $no_number_flag =  $info['no_number'];
        if (1){
            if ($no_number_flag == 1){
                $codes = Db::name('ewm_pay_code')
                    ->where([
                        'ms_id'=>$ms['userid'],
                        'code_type'=>30,
                        'bank_name' => $banker_name,
                        'is_delete' => 0,
                        'status' => 1
                    ])
                    ->select();
            }else{
                $codes = Db::name('ewm_pay_code')
                    ->where([
                        'ms_id'=>$ms['userid'],
                        'code_type'=>30,
                        'bank_name' => $banker_name,
                        'is_delete' => 0,
//                        'status' => 1
                    ])
                    ->select();
            }

            $bool = [];
            $bank_len =strlen($banker_num);
            if ($banker_name == '云南农信'){
                foreach($codes as $code)
                {
                    if( substr($code['account_number'],-7,-3) == $banker_num)
                    {

                        $bool = $code;
                    }
                    if($no_number_flag)
                    {
                        $bool =  $code;
                    }
                }
            }else{
                foreach($codes as $code)
                {
                    if( substr($code['account_number'],0-$bank_len) == $banker_num)
                    {

                        $bool = $code;
                    }
                    if($no_number_flag)
                    {
                        $bool =  $code;
                    }
                }
            }
            if (empty($bool)){
                $this->error('未找到匹配的银行卡 bank_num='.$banker_num.'and bank_name='.$banker_name.'&balance='.$balance);
            }
            //出
            Db::name('ewm_pay_code')->where(['id'=>$bool['id']])->update(['balance'=>$balance]);
            if (empty($info['sms_money'])){
                $this->error('未匹配到任何信息banknunm='.$info['banker_num'].'&money='.$info['sms_money'].'&balance='.$balance);
            }

            $smsMoney  = trim($info['sms_money']);


            $codeType = Db::name('ewm_pay_code')->where(['id'=>$bool['id']])->value('code_type');
            //查订单 订单有效期内 10分钟内的;
            $amount_lock = getAdminPayCodeSys('order_invalid_time',$codeType,$ms['admin_id']);

            if (empty($amount_lock)){
                $amount_lock_time = 360;
            }else{
                $amount_lock_time = $amount_lock * 60-1;
            }

            $start = time()-$amount_lock_time;
            $end = time();
            $find_order = Db::name('ewm_order')
                ->where($where)
                ->where([
                    'code_id'=>$bool['id'],
                    'code_type'=>30,
                    'order_pay_price'=>trim($smsMoney),
                    'status'=>0,
                    'gema_userid'=>$ms['userid']
                ])
                ->where('add_time','between',[$start,$end])
                ->order('id desc')
                ->select();
            if (empty($find_order)){
                if (array_key_exists('pay_name',$info)){
                    $this->error('无匹配订单'.'code id='.$bool['id'].'&bank num='.$banker_num.'&bankname='.$banker_name.'&money='.$smsMoney).'&pay_name='.$info['pay_name'];
                }else {
                    $this->error('无匹配订单'.'code id='.$bool['id'].'&bank num='.$banker_num.'&bankname='.$banker_name.'&money='.$smsMoney);
                }

            }
            if (count($find_order) > 1){
                $this->error('订单超出，请手动操作');
            }
            //判断用户余额是否足够
            $UserModel = new \app\common\model\Ms();
            $userMoney = $UserModel->where(['userid' => $ms['userid']])->value('money');
            if ($userMoney < $find_order[0]['order_price']) {
                return ['code' => CodeEnum::ERROR, 'msg' => '用户余额不足'];
            }
            if ($is_pay_name == 1){
                if(empty($find_order[0]['pay_username']))
                {
                    $this->error('订单匹配错误，未写名字'.'code id='.$bool['id'].'&bank num='.$banker_num.'&bankname='.$banker_name.'&money='.$smsMoney);
                }
            }


            //记录回调
            Log::notice('短信自动监控于：'.date('Y-m-d:H:i:s',time()).'回调订单：'.$find_order[0]['order_no']);
            $updateData = [
                'admin_id' => $find_order[0]['admin_id'],
                'code_id' => $find_order[0]['code_id'],
                'balance' => $userMoney-$find_order[0]['order_price'] ?? 0,
                'order_id' => $find_order[0]['id']
            ];

            $res = Db::name('banktobank_sms')->where('id', $banktobankSmsID)->update($updateData);
            if (!$res){
                $this->error('日志记录失败');
            }
            $ewm = new EwmOrder();
            if ($verify_ms_callback_ip == 1){
                if (in_array($_SERVER['REMOTE_ADDR'],explode(',',$ms['monitor_ip']))){
                    return $ewm->setOrderSucess($find_order[0], "短信自动回调",$ms['userid'],'短信');
                }else{
                    Db::name('ewm_order')->where('id',$find_order[0]['id'])->update(['legality'=>2,'ms_callback_ip'=>get_userip()]);
                    return ['code' => CodeEnum::ERROR, 'msg' => '非法ip'];
                }
            }else{
                return $ewm->setOrderSucess($find_order[0], "短信自动回调",$ms['userid'],'短信');
            }


        }
        return ['code' => CodeEnum::ERROR, 'msg' => '非法请求'];
    }

    protected  function getBalance($content, $source){

        $smsTemplate = require('./data/conf/smsTemplate.php');
        foreach ($smsTemplate as $k=>$v){
            if ($source===$v['phone'])
            {
                if( isset($v['preg_balance'])){
                    foreach ($v['preg_balance'] as $key=>$val){
                        $balance = $this->mysubstr($content, $val['balance_start'], $val['balance_end']);
                        //;die();
                        $balance=str_replace(",","",$balance);

                        if(!is_numeric(trim($balance)))
                        {
                            continue ;
                        }
                        return trim($balance);
                    }
                }

            }
        }
    }

    protected  function getInfo($content, $source){

        $smsTemplate = require_once('./data/conf/smsTemplate.php');

        foreach ($smsTemplate as $k=>$v){
            if ($source===$v['phone']) {
                foreach ($v['preg'] as $key=>$val){
                    $info['banker_num'] = isset($val['no_number'])?'0000000':$this->mysubstr($content, $val['number_start'], $val['number_end']);
                    $info['banker_name'] = $v['source'];
                    $info['sms_money'] = str_replace(",","",$this->mysubstr($content, $val['money_start'], $val['money_end']));
                    $info['sms_time'] = $this->mysubstr($content, $val['time_start'], $val['time_end']);

                    if (array_key_exists('pay_name_start',$val) && array_key_exists('pay_name_end',$val) ){

                        $info['pay_name'] = $this->mysubstr($content, $val['pay_name_start'], $val['pay_name_end']);
                    }
                    if(!empty($info['sms_money']) && !empty($info['sms_money'])){
                        if (array_key_exists('pay_name_jhdz',$val)){
                            preg_match('/[0-9]/', $content, $matches, PREG_OFFSET_CAPTURE);
                            $name_end = strpos($content,$matches[0][0]);
                            $info['pay_name'] = substr($content,0,$name_end);
                        }
                    }

                    $info['no_number'] = isset($val['no_number'])? 1:0;
                    if(empty($info['banker_num']) || !is_numeric(trim($info['banker_num'])) || !is_numeric(trim($info['sms_money'])) )
                    {
                        continue ;
                    }
                    return $info;
                }
            }
        }
        return false;
    }
    protected function mysubstr($str, $start, $end)
    {
        if(empty($start))
        {

            return false;

        }

        $arr = explode($start,$str);
        if (empty($arr) || !isset($arr[1])) {
            return false;
        }
        $str = $arr[1];
        if (strpos($str,$end) !== false) {
            return substr($str,0,strpos($str,$end));
        }else{
            return $str;
        }

        return $a;
    }

    private function decrypt($data)
    {
        return openssl_decrypt(base64_decode($data), "AES-128-CBC", config('secret_key'), true, config('iv'));
    }


}
