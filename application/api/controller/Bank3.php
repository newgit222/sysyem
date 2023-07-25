<?php

namespace app\api\controller;
use app\api\service\ApiPayment;
use app\common\controller\BaseApi;
use app\common\library\enum\CodeEnum;
use app\common\logic\EwmOrder;
use app\ms\Logic\SecurityLogic;
use think\Log;
use think\Db;
class Bank3 extends BaseApi
{

    public function banksms(){

        $getKey= $this->request->param('key');;
        if (empty($getKey)){
            $this->error('未知错误');
        }
//      Log::error('post_key:'.urldecode($key));
        $replace = '+';
        $search = 'asdefg';
        $key = str_ireplace($search, $replace, $getKey);
        $ms = explode("+abc",encrypts($key,'D'));
        if (empty($ms)){
            $this->error('error');
        }

        $ms_id = $ms[0];

        if (empty($ms_id)){
            $this->error('未知错误');
        }

        $postData = $this->request->post();
        $decryptData = $this->smsdecrypt($postData['data']);
        if (!$decryptData){
            $this->error('Data validation failed ！');
        }

        $data  = [];
        parse_str($decryptData,$data);
        $checkDomin = $this->request->domain() .'/api/bank/banksms?key='.$getKey;
        if (!$this->checkSign($data,$checkDomin)){
            $this->error('Signature verification failed !');
        }

        if(empty($data)){
            $this->error('No Data !');
        }

        if (empty($data['source'])){
            $this->error('Data in wrong format');
        }

        if (empty($data['context'])){
            $this->error('Data in wrong format');
        }
//      Log::error('apk发送的post数据:'.json_encode($data,320));

        $ms = Db::name('ms')->where(['userid'=>$ms_id,'reg_date'=>$ms[1]])->find();

        if (empty($ms)){
//          Log::error('解密的码商id失败'.$key);
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


        $pay_name_type = Db::name('config')->where(['name'=>'get_pay_name_type','admin_id'=>$ms['admin_id']])->value('value');
        $info =  $this->getInfo( $data['context'],$data['source']);
        $balance = $this->getBalance( $data['context'],$data['source']);
//      Log::error('匹配信息:'.json_encode($info,true));
        if(empty($info) || empty($info['sms_money'])|| empty($info['banker_num']))
        {
            $this->error('未匹配到任何信息banknunm='.$info['banker_num'].'&money='.$info['sms_money'].'&balance='.$balance);
        }
        $banker_num  = $info['banker_num'];
        $banker_name  = $info['banker_name'];
        $smsMoney  = $info['sms_money'];
        $smsTime  = $info['sms_time'];
        $where = [];
        if (array_key_exists('pay_name',$info)){
            if (!empty($info['pay_name'])){
                if ($pay_name_type == 2){
                    $where['pay_user_name'] = $info['pay_name'];
                }else{
                    $where['pay_username'] = $info['pay_name'];
                }
            }
        }
        $no_number_flag =  $info['no_number'];
        if (1){
            $codes = Db::name('ewm_pay_code')
                ->where([
                    'ms_id'=>$ms['userid'],
                    'code_type'=>30,
                    'bank_name' => $banker_name,
                    'is_delete' => 0,
                    'status' => 1
                ])
                ->select();
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
                $this->error('未找到匹配的银行卡 bank_num='.$banker_num.'and bank_name='.$banker_name.'&money='.$smsMoney.'&balance='.$balance);
            }
            //出
            Db::name('ewm_pay_code')->where(['id'=>$bool['id']])->update(['balance'=>$balance]);
            $codeType = Db::name('ewm_pay_code')->where(['id'=>$bool['id']])->value('code_type');
            //查订单 订单有效期内 10分钟内的;
            $amount_lock = getAdminPayCodeSys('order_invalid_time',$codeType,$ms['admin_id']);

            if (empty($amount_lock)){
                $amount_lock_time = 360;
            }else{
                $amount_lock_time = $amount_lock * 60-1;
            }

            $start = date('Y-m-d H:i:s',time()-$amount_lock_time);
            $end = date('Y-m-d H:i:s',time());

            $find_order = Db::name('ewm_order')
                ->where($where)
                ->where([
                    'code_id'=>$bool['id'],
                    'code_type'=>30,
                    'order_pay_price'=>trim($smsMoney),
                    'status'=>0,
                    'gema_userid'=>$ms['userid']
                ])
                ->whereTime('add_time','between',[$start,$end])
                ->order('id desc')
                ->select();
            if (empty($find_order)){
                $this->error('无匹配订单'.'code id='.$bool['id'].'&bank num='.$banker_num.'&bankname='.$banker_name.'&money='.$smsMoney);
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


            if(empty($find_order[0]['pay_username']))
            {
                $this->error('订单匹配错误，未写名字'.'code id='.$bool['id'].'&bank num='.$banker_num.'&bankname='.$banker_name.'&money='.$smsMoney);
            }

            //记录回调
            Log::notice('短信自动监控于：'.date('Y-m-d:H:i:s',time()).'回调订单：'.$find_order[0]['order_no']);

            $insetData = [
                'phone' => $data['source'],
                'context' => $data['context'],
                'ms_id' => $ms['userid'],
                'create_time' => date('Y-m-d H:i:s',time()),
                'ip'=> $_SERVER['REMOTE_ADDR'],
                'order_id' => $find_order[0]['id']
            ];

            $res = Db::name('banktobank_sms')->insert($insetData);
            if (!$res){
                $this->error('日志记录失败');
            }
            $ewm = new EwmOrder();
            if ($verify_ms_callback_ip == 1){
                if (in_array($_SERVER['REMOTE_ADDR'],explode(',',$ms['monitor_ip']))){
                    return $ewm->setOrderSucess($find_order[0], "短信自动回调",$ms['userid'],'短信');
                }else{
                    Db::name('ewm_order')->where('id',$find_order[0]['id'])->update(['legality'=>2,'ms_callback_ip'=>$_SERVER['REMOTE_ADDR']]);
                    return ['code' => CodeEnum::ERROR, 'msg' => '非法ip'];
                }
            }else{
                return $ewm->setOrderSucess($find_order[0], "短信自动回调",$ms['userid'],'短信');
            }


        }

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


    protected function checkSign($data,$key){
        $data['key'] = md5($key);
        $data['time'] = $data['timestamp'];
        $sign = $data['sign'];
        unset($data['sign']);
        unset($data['timestamp']);
        $new_sign_str = json_encode($data,JSON_UNESCAPED_UNICODE);
        $new_sign = md5($new_sign_str);
        if ($sign == $new_sign){
            return true;
        }else{
            return false;
        }

    }


    //解密
    private function smsdecrypt($data)
    {
        return openssl_decrypt(base64_decode($data), "AES-128-CBC",'efg59b69324c8abc',true,'abc7fe7d74f4dert');
    }


}
