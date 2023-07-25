<?php

namespace app\api\controller;
use app\api\service\ApiPayment;
use app\common\controller\BaseApi;
use app\common\library\enum\CodeEnum;
use app\common\logic\EwmOrder;
use app\ms\Logic\SecurityLogic;
use think\Log;
use think\Db;
class Cnynumber extends BaseApi
{
    //解密
    private function smsdecrypt($data)
    {
        return openssl_decrypt(base64_decode($data), "AES-128-CBC",'9670b9417980911d',true,'a28418e11a42964f');
    }


    public function Cnynumber(){
        if ($this->request->isPost()){
            $getKey= $this->request->param('key');
            if (empty($getKey)){
                $this->error('参数缺失！');
            }
            $replace = '+';
            $search = 'asdefg';
            $key = str_ireplace($search, $replace, $getKey);
            $ms = explode("+abc",encrypts($key,'D'));

            if (empty($ms)){
                $this->error('参数错误');
            }

            $ms_id = $ms[0];

            if (empty($ms_id)){
                $this->error('参数错误');
            }

            $postData = $this->request->post();
            $decryptData = $this->smsdecrypt($postData['data']);
            if (!$decryptData){
                $this->error('Data validation failed ！');
            }
            Log::error('shuzirenminbidata :'.$decryptData);
          //  Log::error(' shuzi renminbi jian kong post jie mi  data :'.$decryptData);
            $data  = [];
            parse_str($decryptData,$data);
            if (empty($data)) $this->error('NO Data ！');
            $code_number = $data['id'];
            $orderList = json_decode(json_decode($data['data'],true)['data'],true);

            if (empty($orderList)){
                $this->error('未监听到可用账单');
            }

//            $checkDomin = $this->request->domain() .'/api/cnynumber/cnynumber?key='.$getKey;
//            if (!$this->checkSign($data,$checkDomin)){
//                $this->error('Signature verification failed !');
//            }

            $ms = Db::name('ms')->where(['userid'=>$ms_id,'reg_date'=>$ms[1]])->find();
            if (empty($ms)){
                $this->error('请核对请求链接内容'.$key);
            }

            if ($ms['status'] == 0){
                $this->error('账号被禁用');
            }

            if ($ms['work_status'] == 0){
                $this->error('账号未开工');
            }

            if ($ms['admin_work_status'] != 1){
                $this->error('你已被禁止接单，请联系管理');
            }

//            $amount_lock = getAdminPayCodeSys('order_invalid_time',50,$ms['admin_id']);
//            if (empty($amount_lock)){
//                $amount_lock_time = 360;
//            }else{
//                $amount_lock_time = $amount_lock * 60-1;
//            }
            foreach ($orderList as $keys=>$val){
                if ($val['type'] != '转钱'){
                    unset($orderList[$keys]);
                    continue;
                }

                $orderList[$keys]['amount'] =  str_replace(",","",$val['money']);
            }
            if (empty($orderList)){
                $this->error('获取到的账单已废弃');
            }

            $code = Db::name('ewm_pay_code')
                ->where([
                    'account_number' => trim($code_number),
                    'code_type'=>50,
                    'is_delete' => 0,
                    'status' => 1,
                ])->find();
            if(empty($code)){
                $this->error('未匹配到收款账户，account_number='. $code_number);
            }
            if ($code['ms_id'] != $ms['userid']){
                $this->error('收款账号与码商不匹配，account_number='. $code_number.'&msid='.$code['ms_id'] );
            }

            $start = time()-360;
            $end = time();
//            print_r($orderList);die;
            $datainfo['code_number'] = $code_number;
            $datainfo['code'] = $code;
            $datainfo['ms'] = $ms;
            $datainfo['time'] = [
                'start' => $start,
                'end' => $end
            ];


          array_walk($orderList,'app\api\controller\Cnynumber::callback',$datainfo);

          die;
//            foreach ($orderList as $k=>$v){
////              $res = $this->autoOrderNotify($v,$ms,$code);
////              if ($res['code'] != 1){
////                  $status = false;
////              }else{
////                  $status = true;
////              }
////              $this->autoOrderNotifyLog($v['money'],$status);
////              continue;
//
//                $find_order = Db::name('ewm_order')
//                    ->where([
//                        'code_id'=>$code['id'],
//                        'code_type'=>50,
//                        'order_pay_price'=>trim($v['amount']),
//                        'status'=>0,
//                        'gema_userid'=>$ms['userid'],
//                    ])
//                    ->where('add_time','between',[$start,$end])
//                    ->order('id desc')
//                    ->find();
//
//                if (!empty($find_order)){
//                    Log::error(' -------------------');
//                    $ewm = new EwmOrder();
//                    $ewm->setOrderSucess($find_order, "监控自动回调",$find_order['gema_userid'],'监控');
//                    continue;
//                }
//                continue;
//            }




        }

        return ['code' => CodeEnum::ERROR, 'msg' => '非法请求'];

    }



   protected function callback($val,$key,$datainfo){
        $find_order = Db::name('ewm_order')
            ->where([
                'code_id'=>$datainfo['code']['id'],
                'code_type'=>50,
                'order_pay_price'=>trim($val['amount']),
                'status'=>0,
                'gema_userid'=>$datainfo['ms']['userid'],
            ])
            ->where('add_time','between',[$datainfo['time']['start'],$datainfo['time']['end']])
            ->order('id desc')
            ->find();

        if (!empty($find_order)){
            Log::notice('监控自动回调订单: '.$find_order['out_trade_no']);
            $ewm = new EwmOrder();
            $ewm->setOrderSucess($find_order, "监控自动回调",$find_order['gema_userid'],'监控');
            echo '钱包编号： '.$datainfo['code_number'].'订单匹配回调成功！订单号： '.$find_order['out_trade_no'].'。金额：' .$val['amount']."\n";
        }else{
            echo '钱包编号： '.$datainfo['code_number'].'未匹配到相应订单！金额： '.$val['amount']."\n";
            Log::error('监控推送金额: '.$val['amount'].'未找到响应订单');
        }


    }

    private function autoOrderNotifyLog($amount,$status){
        if ($status){
            Log::notice('autoOrderNotifyLog Success amount: '.$amount);
            echo '处理成功： '.$amount;
//            die;
        }else{
            Log::error('autoOrderNotifyLog Error amount: '.$amount);
            echo '处理失败： '.$amount;
//            die;
        }
    }

    private function autoOrderNotify($bill,$ms,$code){

        //查订单 订单有效期内 10分钟内的;
        $amount_lock = getAdminPayCodeSys('order_invalid_time',50,$ms['admin_id']);

        if (empty($amount_lock)){
            $amount_lock_time = 360;
        }else{
            $amount_lock_time = $amount_lock * 60-1;
        }

        $start = time()-$amount_lock_time;
        $end = time();
        $find_order = Db::name('ewm_order')
            ->where([
                'code_id'=>$code['id'],
                'code_type'=>50,
                'order_pay_price'=>trim($bill['money']),
                'status'=>0,
                'gema_userid'=>$ms['userid'],
            ])
            ->where('add_time','between',[$start,$end])
            ->order('id desc')
            ->find();

        if ($find_order){
                 $ewm = new EwmOrder();
                return $ewm->setOrderSucess($find_order, "监控自动回调",$find_order['gema_userid'],'监控');
        }
        return ['code' => CodeEnum::ERROR, 'msg' => '无匹配订单'];
//        [ error ] 订单： {"id":133,"add_time":1678539635,"order_no":"2303112100338180","order_price":"0.10","status":0,"gema_userid":1,"qr_image":"","pay_time":null,"code_id":10,"order_pay_price":"0.05","gema_username":"ms1","note":null,"out_trade_no":"2303112100338180","code_type":50,"bonus_fee":"0.00","is_back":0,"is_upload_credentials":0,"credentials":null,"sure_ip":"0","is_handle":0,"visite_ip":"--","visite_area":null,"visite_time":1678539636,"merchant_order_no":"2303112100338180","visite_clientos":null,"grab_a_single_type":1,"admin_id":1,"notify_result":null,"visite_token":null,"notify_url":"http:\/\/ta.sanfang.com\/api\/notify\/notify\/channel\/GumaV2Pay","member_id":0,"pay_username":"","pay_user_name":"addH","sure_order_role":0,"name_abnormal":0,"money_abnormal":0,"cardKey":"","legality":1,"ms_callback_ip":"","extra":""}
//        [ error ] update Order error： 1
//        [ error ] 订单： {"id":134,"add_time":1678539641,"order_no":"2303112100389689","order_price":"0.10","status":0,"gema_userid":1,"qr_image":"","pay_time":null,"code_id":10,"order_pay_price":"0.13","gema_username":"ms1","note":null,"out_trade_no":"2303112100389689","code_type":50,"bonus_fee":"0.00","is_back":0,"is_upload_credentials":0,"credentials":null,"sure_ip":"0","is_handle":0,"visite_ip":"--","visite_area":null,"visite_time":1678539642,"merchant_order_no":"2303112100389689","visite_clientos":null,"grab_a_single_type":1,"admin_id":1,"notify_result":null,"visite_token":null,"notify_url":"http:\/\/ta.sanfang.com\/api\/notify\/notify\/channel\/GumaV2Pay","member_id":0,"pay_username":"","pay_user_name":"addH","sure_order_role":0,"name_abnormal":0,"money_abnormal":0,"cardKey":"","legality":1,"ms_callback_ip":"","extra":""}

    }

    protected function checkSign($data,$key){
        $sign = $data['sign'];
        $newdata = [
            'type' =>$data['type'],
            'name' => $data['name'],
            'money' => $data['money'],
            'key' => md5($key),
            'time' =>$data['timestamp']
        ];
        $new_sign_str = json_encode($newdata,JSON_UNESCAPED_UNICODE);
        Log::error(' shuzi renminbi jian kong sign str  data :'.$new_sign_str);
        $new_sign = md5($new_sign_str);
        if ($sign == $new_sign){
            return true;
        }else{
            return false;
        }

    }

}