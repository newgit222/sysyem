<?php

/**
 *  +----------------------------------------------------------------------
 *  | 中通支付系统 [ WE CAN DO IT JUST THINK ]
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2018 http://www.iredcap.cn All rights reserved.
 *  +----------------------------------------------------------------------
 *  | Licensed ( https://www.apache.org/licenses/LICENSE-2.0 )
 *  +----------------------------------------------------------------------
 *  | Author: Brian Waring <BrianWaring98@gmail.com>
 *  +----------------------------------------------------------------------
 */

namespace app\api\controller;
use app\api\service\ApiRespose;
use app\common\controller\BaseApi;
use app\common\library\exception\ForbiddenException;
use app\common\model\EwmOrder;
use app\common\model\Orders;
use app\common\model\Balance;
use GuzzleHttp\Client;
use think\Db;
use think\Log;
use think\Request;
use think\Validate;

/**
 * 所有的支付操作，都需要对输入执行参数校验，避免接口受到攻击。
 * 　　● 验证输入参数中各字段的有效性验证，比如用户ID,商户ID,价格，返回地址等参数。
 * 　　● 验证账户状态。交易主体、交易对手等账户的状态是处于可交易的状态。
 * 　　● 验证订单：如果涉及到预单，还需要验证订单号的有效性，订单状态是未支付。为了避免用户缓存某个URL地址，还需要校验下单时间和支付时间是否超过预定的间隔。
 * 　　● 验证签名。签名也是为了防止支付接口被伪造。 一般签名是使用分发给商户的key来对输入参数拼接成的字符串做MD5 Hash或者RSA加密，然后作为一个参数随其他参数一起提交到服务器端。
 *
 */
class Pay extends BasePay
{
    //前置操作来验证  不适用tags了
    protected $beforeActionList = [
        'checkRequestParam' => ['only' => 'unifiedorder,query'],
    ];

    /**
     * 收银台跳转支付
     *
     * @throws ForbiddenException
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     *
     */
    public function cashier()
    {
        throw new ForbiddenException(['msg' => '等待开发...']);
    }

    /**
     * 统一扫码支付
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function unifiedorder()
    {
        $startTime = time();
        //传入预支付订单信息 => 支付对象返
        if (config('proxy_debug')) {
            //代理过去
            $hosts = config('real_host');
            $requestUrl = $hosts . "api/pay/unifiedorder";
            $res = httpRequest($requestUrl, 'post', $this->request->post());
        } else {
            //本host处理
            $res = $this->logicPrePay->orderPay($this->request->post());
        }

        $data = [
            'code' => 0,
            'msg' => '请求成功',
            'data' => $res,
        ];
        Log::error('订单号：'.$this->request->post('out_trade_no').'：返回：'.json_encode($data,true));
        $useTime = time() - $startTime;

//        $log = "本笔订单耗时{$useTime}秒,REQUEST_PARAMS:" . json_encode($this->request->post()) . "|RESPONSE_PARAMS:" . json_encode($data);
//        Log::notice($log);


        echo json_encode($data);
        die();
    }

    /**
     * 统一查询接口
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function query()
    {
        //传入预支付订单信息 => 支付对象返回
        //   $res = $this->logicPrePay->daifuOrderQuery($this->request->post());

        $res = $this->logicPrePay->orderQuery($this->request->post());
        $data = [
            'code' => 0,
            'msg' => '请求成功',
            'data' => $res,
        ];
        echo json_encode($data);
        die();
    }

    public function balance()
    {
	    if(!empty($_POST['uid']))
	    {
	      $uid = $_POST['uid'];
	    }
	    else
	    {
              if(!empty($_POST['mchid'])){
		      $uid = $_POST['mchid'];
	      }
	    }
          //   $uid= 100001;
	    if(empty($uid))
	    {
	      echo 'mchid params need';die();
	    }
        //传入预支付订单信息 => 支付对象返回
        //   $res = $this->logicPrePay->daifuOrderQuery($this->request->post());
	    $data = Db::name('balance')->where(['uid'=>$uid,'status'=>1])->find();;
            if(!empty($data)){
	        $res['balance']= $data['enable'];
	    }
	    else
	    {
	      $res['balance']= '0.0';
	    }
    ///   $res['balance']= ;

        $data = [
            'code' => 0,
            'msg' => '请求成功',
            'data' => $res,
        ];
        echo json_encode($data);
        die();
    }

    /**
     * 代付款统一下单接口
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function daifuorder()
    {

        $startTime = time();
        //传入预支付订单信息 => 支付对象返
        //本host处理
        $res = $this->logicPrePay->daifuOrderPay($this->request->post());
        $data = [
            'code' => 0,
            'msg' => '请求成功',
            'data' => $res,
        ];
        $useTime = time() - $startTime;

        $log = "本笔订单耗时{$useTime}秒,REQUEST_PARAMS:" . json_encode($this->request->post()) . "|RESPONSE_PARAMS:" . json_encode($data);
        Log::notice($log);


        echo json_encode($data);
        die();

//        ApiRespose::send($this->logicPrePay->daifuOrderPay($this->request->post()));
    }

    /**
     * 代付款统一查询接口
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function daifuquery()
    {
        //传入预支付订单信息 => 支付对象返回
        $res = $this->logicPrePay->daifuOrderQuery($this->request->post());
        $data = [
            'code' => 0,
            'msg' => '请求成功',
            'data' => $res,
        ];
        echo json_encode($data);die();
    }
//   public function updateOrderPayUsername2(Request $request)
//   {
//	   //  echo 3;die();
//	   $trade_no = '2203240224433781';
//	   $payusername = '关彬';
//       $data = (new EwmOrder())->where('order_no',$trade_no)->find();
//       $where['code_id'] = $data['code_id'];
//       $where['pay_username'] = $payusername;
//       $where['order_price'] = $data['order_price'];
//       $where['add_time'] = array(">",$data['add_time']-60*5);
//      $data =  (new EwmOrder())->where($where)->find();
//       var_dump($data);die();
//
//       $data['code_id'];
//   }


    /**
     * 更新订单支付用户名
     */
    public function updateOrderPayUsername(Request $request)
    {
        $trade_no = $request->param('trade_no');
        $payusername = $request->param('pay_username');
        $ip = $request->param('ips');
        if ($this->modelEwmBlockIp->where('block_visite_ip', $ip)){
         //  return $this->error('没有足够的卡转卡资源请重新拉取订单');
        }
      //  $trade_no = '9341498210926203216';
       // $payusername ='伟哥';
        if (empty($trade_no)) return json(['code'=>0,'msg'=>'订单号不能为空']);
        $aValid = array('-', '_');
        if (!ctype_alnum((str_replace($aValid, '', $trade_no)))) return json(['code'=>0,'msg'=>'订单号不合法']);
        if (empty($payusername)) return json(['code'=>0,'msg'=>'姓名不能为空']);
//        if (checkIsChinese($payusername) == false) return json(['code'=>0,'msg'=>'请输入中文姓名']);
//        if (strlen($payusername) > 36) return json(['code'=>0,'msg'=>'付款人姓名最大12位']);

        $vadata['trade_no'] = $trade_no;
        $vadata['pay_username'] = $payusername;
        $validate = new Validate([
            'pay_username'  => 'chs|max:10',
            'trade_no' => 'alphaDash|max:50',
        ]);

        if (!$validate->check($vadata)) {
//            $this->error($validate->getError());
            return json(['code'=>0,'msg'=>$validate->getError()]);
        }
	$data = (new EwmOrder())->where('order_no',$trade_no)->find();
        if (empty($data)){
            return json(['code'=>0,'msg'=>'非法请求']);
        }

//	$data = (new EwmOrder())->where('order_no',$trade_no)->find();
       $where2['code_id'] = $data['code_id'];
       $where2['pay_username'] = $payusername;
       $where2['order_price'] = $data['order_price'];
       $where2['add_time'] = array(">",$data['add_time']-60*5);
       $data2 =  (new EwmOrder())->where($where2)->find();
       if(!empty($data2))
       {
//	     return $this->success('ok', null, ['pay_username' =>$payusername]);
           return json(['code'=>1,'msg'=>'提交成功']);
           //return $this->error('请重新提交订单', null, ['pay_username' => $payusername.strlen($payusername)]);
       }

        if(!empty($data['pay_username']))
        {
            if($payusername==$data['pay_username'])
            {
                return json(['code'=>1,'msg'=>'提交成功']);
            }else
            {
                return json(['code'=>0,'msg'=>'请重新提交订单']);
            }
         }
        $ret = (new EwmOrder())->where('order_no', $trade_no)->setField('pay_username', $payusername);
        Log::error('----'.$trade_no.'---------newip'. $request->post('ips'). $payusername);
        return json(['code'=>1,'msg'=>'提交成功']);
//        return $this->success('ok', null, ['pay_username' => $payusername.strlen($payusername)]);

    }


    /**
     * 更新订单支付用户名
     */
    public function updateOrderPayUsernameNoLockCode(Request $request)
    {
        $trade_no = $request->param('trade_no');
        $payusername = $request->param('pay_username');
        $ip = $request->param('ips');
        if($ip=='119.91.88.109' || $ip=='42.194.142.113')
        {

            Log::error('----ipyouwenti------'. $trade_no.'---'. $payusername.'----'.$ip);
//            return $this->error('ok', null, ['pay_username' => $payusername.strlen($payusername)]);
            return json(['code'=>0,'msg'=>'请重新提交订单']);
        }

        if ($this->modelEwmBlockIp->where('block_visite_ip', $ip)){
            //  return $this->error('没有足够的卡转卡资源请重新拉取订单');
        }
        //  $trade_no = '9341498210926203216';
        // $payusername ='伟哥';
        if (empty($trade_no)) return json(['code'=>0,'msg'=>'订单号不能为空']);
        $aValid = array('-', '_');
        if (!ctype_alnum((str_replace($aValid, '', $trade_no)))) return json(['code'=>0,'msg'=>'订单号不合法']);
        if (empty($payusername)) return json(['code'=>0,'msg'=>'姓名不能为空']);
//        if (checkIsChinese($payusername) == false) return $this->error('请输入中文姓名');
//        if (strlen($payusername) > 12) return $this->error('付款人姓名最大五位长度');
        $vadata['trade_no'] = $trade_no;
        $vadata['pay_username'] = $payusername;
        $validate = new Validate([
            'pay_username'  => 'chs|max:10',
            'trade_no' => 'alphaDash|max:50',
        ]);

        if (!$validate->check($vadata)) {
//            $this->error($validate->getError());
            return json(['code'=>0,'msg'=>$validate->getError()]);
        }
        $data = (new EwmOrder())->where('order_no',$trade_no)->find();
        if (empty($data)){
            return json(['code'=>0,'msg'=>'非法请求']);
        }

//	$data = (new EwmOrder())->where('order_no',$trade_no)->find();
//        $where2['code_id'] = $data['code_id'];
//        $where2['pay_username'] = $payusername;
//        $where2['order_price'] = $data['order_price'];
//        $where2['add_time'] = array(">",$data['add_time']-60*5);
//        $data2 =  (new EwmOrder())->where($where2)->find();
//        if(!empty($data2))
//        {
//            return $this->error('请重新提交订单', null, ['pay_username' => $payusername.strlen($payusername)]);
//        }

        if(!empty($data['pay_username']))
        {
            Log::error('chongfu----'.$trade_no.'---------newip'. $request->post('ips'). $payusername);
            if($payusername==$data['pay_username'])
            {
//                return $this->success('ok', null, ['pay_username' => $payusername.strlen($payusername)]);
                return json(['code'=>1,'msg'=>'ok']);
            }else
            {
//                return $this->error('请重新提交订单', null, ['pay_username' => $payusername.strlen($payusername)]);
                return json(['code'=>0,'msg'=>'请重新提交订单']);
            }
        }
        $ret = (new EwmOrder())->where('order_no', $trade_no)->setField('pay_username', $payusername);
        Log::error('----'.$trade_no.'---------newip'. $request->post('ips'). $payusername);
//        return $this->success('ok', null, ['pay_username' => $payusername.strlen($payusername)]);
        return json(['code'=>1,'msg'=>'ok']);

    }

    public function updateOrderPayUsernameQq(Request $request)
    {
        $trade_no = $request->param('trade_no');
        $payusername = $request->param('pay_username');
        $ip = $request->param('ips');
        if ($this->modelEwmBlockIp->where('block_visite_ip', $ip)){
            //  return $this->error('没有足够的卡转卡资源请重新拉取订单');
        }
        //  $trade_no = '9341498210926203216';
        // $payusername ='伟哥';
        if (empty($trade_no)) return json(['code'=>0,'msg'=>'订单号不能为空']);
        $aValid = array('-', '_');
        if (!ctype_alnum((str_replace($aValid, '', $trade_no)))) return json(['code'=>0,'msg'=>'订单号不合法']);
        if (empty($payusername)) return json(['code'=>0,'msg'=>'姓名不能为空']);
//        if (checkIsChinese($payusername) == false) return json(['code'=>0,'msg'=>'请输入中文姓名']);
//        if (strlen($payusername) > 36) return json(['code'=>0,'msg'=>'付款人姓名最大12位']);

        $vadata['trade_no'] = $trade_no;
        $vadata['pay_username'] = $payusername;
        $validate = new Validate([
            'pay_username'  => 'number|max:10',
            'trade_no' => 'alphaDash|max:50',
        ]);

        if (!$validate->check($vadata)) {
//            $this->error($validate->getError());
            return json(['code'=>0,'msg'=>$validate->getError()]);
        }
        $data = (new EwmOrder())->where('order_no',$trade_no)->find();
        if (empty($data)){
            return json(['code'=>0,'msg'=>'非法请求']);
        }

//	$data = (new EwmOrder())->where('order_no',$trade_no)->find();
        $where2['code_id'] = $data['code_id'];
        $where2['pay_username'] = $payusername;
        $where2['order_price'] = $data['order_price'];
        $where2['add_time'] = array(">",$data['add_time']-60*5);
        $data2 =  (new EwmOrder())->where($where2)->find();
        if(!empty($data2))
        {
//	     return $this->success('ok', null, ['pay_username' =>$payusername]);
            return json(['code'=>1,'msg'=>'提交成功']);
            //return $this->error('请重新提交订单', null, ['pay_username' => $payusername.strlen($payusername)]);
        }

        if(!empty($data['pay_username']))
        {
            if($payusername==$data['pay_username'])
            {
                return json(['code'=>1,'msg'=>'提交成功']);
            }else
            {
                return json(['code'=>0,'msg'=>'请重新提交订单']);
            }
        }
        $ret = (new EwmOrder())->where('order_no', $trade_no)->setField('pay_username', $payusername);
        Log::error('----'.$trade_no.'---------newip'. $request->post('ips'). $payusername);
        return json(['code'=>1,'msg'=>'提交成功']);
//        return $this->success('ok', null, ['pay_username' => $payusername.strlen($payusername)]);

    }

    public function alipayNotify(){
        $pData =$_POST;
        if (!isset($pData) || empty($pData)) {
            die('err-post');
        }
        Log::error('alipayNotifyData : '.json_encode($pData,true));
        $pData['fund_bill_list'] = stripslashes($pData['fund_bill_list']);
        if ($pData['trade_status'] == 'TRADE_SUCCESS') {
           // Log::error('f2fPayNotifyDataTongzhi :'.'yes');
            //查找订单信息
            $orderInfo = Db::name('ewm_order')
                ->where('order_no',$pData['out_trade_no'])
                ->where('status',0)
                ->where('add_time','>', time() - 1800)
                ->order('id desc')
                ->find();
           // Log::error('f2fPayNotifyDataOrderInfo :'.json_encode($orderInfo,320));
            if (empty($orderInfo)) {
                Log::error('alipayNotifyDataSignError : 找不到订单 '.$pData['out_trade_no']);
                return json(['code' => 0, 'msg' => '找不到订单']);
            }

            // 获取通道的签名参数 start

            $apiData = Db::name('ewm_pay_code')->where('id',$orderInfo['code_id'])->where('account_number',$pData['app_id'])->find();
       //     Log::error('f2fPayNotifyDataCodeInfo :'.json_encode($apiData,320));
            if (empty($apiData)) {
                Log::error('alipayNotifyDataSignError : api参数错误 '.$pData['out_trade_no']);
                return json(['code' => 0, 'msg' => 'api参数错误']);
            }


            $public_key = $apiData['publicKey'];
            vendor('alipay.aop.AopClient');
            $aop = new \AopClient();
            $aop->alipayrsaPublicKey = $public_key;
            //此处验签方式必须与下单时的签名方式一致
            $flag = $aop->rsaCheckV1($pData, $public_key, "RSA2");
            if (!$flag) {
                Log::error('alipayNotifyDataSignError : '.$pData['out_trade_no'].'error签名错误');
                echo 'error签名错误';
                exit();
            }
            //修改订单为已支付
            $ewm = new \app\common\logic\EwmOrder();
            Log::error('alipayNotifyDataSuccess : 去执行成功操作 '.$pData['out_trade_no']);
            $ewm->setOrderSucess($orderInfo, '支付宝交易号：'.$pData['trade_no'],$orderInfo['gema_userid'],'官方');
            //处理你的逻辑，例如获取订单号$_POST['out_trade_no']，订单金额$_POST['total_amount']等
            //程序执行完后必须打印输出“success”（不包含引号）。如果商户反馈给支付宝的字符不是success这7个字符，支付宝服务器会不断重发通知，直到超过24小时22分钟。一般情况下，25小时以内完成8次通知（通知的间隔频率一般是：4m,10m,10m,1h,2h,6h,15h）；
            echo 'success';
            exit();


        }

    }


    /**
     * 上传卡密
     */
    public function saveCardPwd(Request $request){
        $trade_no = $request->param('sn');
        $cardKey = $request->param('cardKey');
        if (empty($trade_no)) return json(['code'=>0,'msg'=>'订单号不能为空']);
        if (empty($cardKey)) return json(['code'=>0,'msg'=>'卡密不能为空']);
       // $aValid = array('-', '_');
       // if (!ctype_alnum((str_replace($aValid, '', $trade_no)))) return json(['code'=>0,'msg'=>'订单号不合法']);
//        if (strlen($cardKey) < 16 || strlen($cardKey) > 20) return json(['code'=>0,'msg'=>'请输入正确卡密']);

        $vadata['trade_no'] = $trade_no;
        $vadata['cardKey'] = $cardKey;
        $validate = new Validate([
            'cardKey'  => 'alphaDash|max:30',
            'trade_no' => 'alphaDash|max:50',
        ]);

        if (!$validate->check($vadata)) {
//            $this->error($validate->getError());
            return json(['code'=>0,'msg'=>$validate->getError()]);
        }

        $data = (new EwmOrder())->where('order_no',$trade_no)->find();
        if (empty($data)) return json(['code'=>0,'msg'=>'非法请求']);

        if (time() - $data['add_time'] > 599){
            return json(['code'=>0,'msg'=>'订单超时，请重新提交订单']);
        }
        if (!empty($data['cardKey'])) return json(['code'=>0,'msg'=>'该订单已绑定卡密，请重新提交订单']);

        $where2['code_id'] = $data['code_id'];
        $where2['cardKey'] = $cardKey;
        $where2['order_price'] = $data['order_price'];

        $admin_id = Db::name('ms')->where('userid',$data['gema_userid'])->value('admin_id');
        $order_invalid_time = getAdminPayCodeSys('order_invalid_time',$data['code_type'],$admin_id);
        if (empty($order_invalid_time)){
            $order_invalid_time = 600;
        }else{
            $order_invalid_time = $order_invalid_time * 60;
        }

        $where2['add_time'] = array(">",time()-$order_invalid_time);
        $data2 =  (new EwmOrder())->where($where2)->find();
        if(!empty($data2))
        {
            return json(['code'=>0,'msg'=>'该卡密已存在，请重新核对后提交']);
        }

        $ret = (new EwmOrder())->where('order_no', $trade_no)->setField('cardKey', $cardKey);

        Log::error( 'ip :.'.get_userip().'给订单----'.$trade_no.'---------上传卡密 ：'. $cardKey);

        $callbackurl = Db::name('orders')->where(['trade_no'=>$trade_no])->value('return_url');
        return json(['code'=>1,'msg'=>'提交成功','data'=>'http://www.baidu.com/']);

    }



    /**
     * 上传卡号卡密
     */
    public function saveCardAccountPwd(Request $request){
        $trade_no = $request->param('sn');
        $cardAccount = $request->param('cardAccount');
        $cardKey = $request->param('cardKey');
        if (empty($trade_no)) return json(['code'=>0,'msg'=>'订单号不能为空']);
        if (empty($cardKey)) return json(['code'=>0,'msg'=>'卡密不能为空']);
        if (empty($cardAccount)) return json(['code'=>0,'msg'=>'卡号不能为空']);
//        $aValid = array('-', '_');
//        if (!ctype_alnum((str_replace($aValid, '', $trade_no)))) return json(['code'=>0,'msg'=>'订单号不合法']);
//        if (strlen($cardKey) < 16 || strlen($cardKey) > 20) return json(['code'=>0,'msg'=>'请输入正确卡密']);
        $vadata['trade_no'] = $trade_no;
        $vadata['cardKey'] = $cardKey;
        $vadata['cardAccount'] = $cardAccount;
        $validate = new Validate([
            'cardKey'  => 'require|alphaDash|max:30',
            'cardAccount'  => 'require|alphaDash|max:30',
            'trade_no' => 'require|alphaDash|max:50',
        ]);

        if (!$validate->check($vadata)) {
//            $this->error($validate->getError());
            return json(['code'=>0,'msg'=>$validate->getError()]);
        }
        $data = (new EwmOrder())->where('order_no',$trade_no)->find();
        if (empty($data)) return json(['code'=>0,'msg'=>'非法请求']);

        if (time() - $data['add_time'] > 599){
            return json(['code'=>0,'msg'=>'订单超时，请重新提交订单']);
        }
        if (!empty($data['cardKey']) || !empty($data['cardAccount'])) return json(['code'=>0,'msg'=>'该订单已被绑定，请重新提交订单']);

//        $where2['code_id'] = $data['code_id'];
//        $where2['cardAccount'] = $cardAccount;
//        $where2['cardKey'] = $cardKey;
//        $where2['order_price'] = $data['order_price'];

//        $admin_id = Db::name('ms')->where('userid',$data['gema_userid'])->value('admin_id');
//        $order_invalid_time = getAdminPayCodeSys('order_invalid_time',$data['code_type'],$admin_id);
//        if (empty($order_invalid_time)){
//            $order_invalid_time = 600;
//        }else{
//            $order_invalid_time = $order_invalid_time * 60;
//        }
//
//        $where2['add_time'] = array(">",time()-$order_invalid_time);
//        $data2 =  (new EwmOrder())->where($where2)->find();
//        if(!empty($data2))
//        {
//            return json(['code'=>0,'msg'=>'该卡密已存在，请重新核对后提交']);
//        }

        $ret = (new EwmOrder())->where('order_no', $trade_no)->setField(['cardKey'=>$cardKey,'cardAccount'=>$cardAccount]);

        Log::error( 'ip :.'.$request->ip().'给订单----'.$trade_no.'---------上传卡密 ：'. $cardKey);

        $callbackurl = Db::name('orders')->where(['trade_no'=>$trade_no])->value('return_url');
        return json(['code'=>1,'msg'=>'提交成功','data'=>'http://www.baidu.com/']);

    }


    /**
     * 上传凭证
     */
    public function uploadMoneyImg(Request $request){
//        Log::error('来上传了：'.json_encode($request->param()));
        $trade_no = $request->param('sn');
        if (empty($trade_no)) return json(['code'=>0,'msg'=>'订单号不能为空']);
        $aValid = array('-', '_');
        if (!ctype_alnum((str_replace($aValid, '', $trade_no)))) return json(['code'=>0,'msg'=>'订单号不合法']);
        //现在直接传图片地址过来
        $image_path = $request->param('image_path');

        $image_path =   openssl_decrypt(base64_decode($image_path), "AES-128-CBC", '8e70f72bc3f53b12', true, '99b538370c7729c7');

        $vadata['trade_no'] = $trade_no;
        $vadata['image_path'] = $image_path;
        $validate = new Validate([
            'image_path'  => 'require|url',
            'trade_no' => 'require|alphaDash|max:50',
        ],[
            'image_path.url' => '图片格式错误',
            'trade_no.alphaDash' => '订单号只能为字母和数字',
        ]);

        if (!$validate->check($vadata)) {
//            $this->error($validate->getError());
            return json(['code'=>0,'msg'=>$validate->getError()]);
        }


        $data = (new EwmOrder())->where('order_no',$trade_no)->find();
        if (empty($data)) return json(['code'=>0,'msg'=>'非法请求']);
        if (!empty($data['cardKey'])) return json(['code'=>0,'msg'=>'请重新提交订单']);
        if (time() - $data['add_time'] > 599){
            return json(['code'=>0,'msg'=>'订单超时，请重新提交订单']);
        }


        if (empty($image_path)) return json(['code'=>0,'msg'=>'图片不能为空']);
        $res = Db::name('ewm_order')->where('order_no',$trade_no)->update(['cardKey'=>$image_path]);
        if ($res === false){
            return json(['code'=>0,'msg'=>'上传失败']);
        }
//        $callbackurl = Db::name('orders')->where(['trade_no'=>$trade_no])->value('return_url');
        return json(['code'=>1,'msg'=>'上传成功','data'=>'http://www.baidu.com/']);


        // 获取表单上传文件 例如上传了001.jpg
    }




    /**
     * 上报访问相干信息
     * @param Request $request
     */
    public function recordVisistInfo(Request $request)
    {
        $trade_no = $request->param('trade_no', '');
        if (empty($trade_no))  return json(['code'=>0,'msg'=>'订单号不能为空']);
        if (!ctype_alnum($trade_no))  return json(['code'=>0,'msg'=>'订单号不合法']);
        $visite_ip = $request->param('visite_ip');
        if (empty($visite_ip)) return json(['code'=>0,'msg'=>'上报访问设备IP不能为空']);
//        if (!filter_var($visite_ip, FILTER_VALIDATE_IP)) return $this->error('上报ip不合法');
        $visite_clientos = match_str($request->param('visite_clientos'));
        $vadata['ip'] = $visite_ip;
        $vadata['trade_no'] = $trade_no;
        $vadata['visite_clientos'] = $visite_clientos;
        $validate = new Validate([
            'ip'  => 'require|ip|max:15',
            'trade_no' => 'require|alphaDash|max:50',
            'visite_clientos' => 'require|alpha|max:15'
        ]);

        if (!$validate->check($vadata)) {
            return json(['code'=>0,'msg'=>$validate->getError()]);
        }
        $data = (new EwmOrder())->where('order_no',$trade_no)->find();
        if (empty($data)) return json(['code'=>0,'msg'=>'非法请求']);
        $ret = (new EwmOrder())->where('order_no', $trade_no)->update([
            'visite_ip' => $visite_ip,
            'visite_time' => time(),
            'visite_clientos' => $visite_clientos,
        ]);
        if (false !== $ret) {
            return json(['code'=>1,'msg'=>'上报成功']);
        }
        return json(['code'=>0,'msg'=>'上报信息有误']);
    }






    public function PuXiJiShouCard(){
//        if (serverIP() != get_userip()){
//            echo "IP ERROR";
//            die;
//        }
        $xkms = Db::name('jdek_sale')->where(['type'=>2,'status'=>1])->column('ms_id');
        if (empty($xkms)) return '暂无码商开启自动提交';
        $data = Db::name('ewm_order')
            ->where('code_type',43)
            ->where('cardKey','neq','')
//            ->whereNotNull('cardKey')
//            ->where(function($query) {
//                $query->where('code_type', 43)
//                    ->whereOr(function($query) {
//                        $query->where('pid', 61)
//                            ->where('cardAccount', '<>', '');
//                    });
//            })
//            ->where(function($query) {
//                $query->where(function($query) {
//                    $query->where('code_type', 43)
//                        ->whereNotNull('cardKey');
//                })
//                    ->whereOr(function($query) {
//                        $query->where('code_type', 61)
//                            ->where('cardKey', '<>', '')
//                            ->where('cardAccount', '<>', '');
//                    });
//            })
            ->where('status',0)
            ->where('legality',1)
            ->where('gema_userid','in',$xkms)
            ->where('add_time','>',time()-600)
            ->select();
        if (empty($data)) return '暂无可处理的订单';
        Log::error('PuXiJiShouCardSubmit Order Data :' . json_encode($data,true));

        foreach ($data as $k=>$v){
            $res = json_decode($this->PuXiJiShouCardSubmit($v),true);
            if ($res['code'] == 0){
                Db::name('ewm_order')->where('id',$v['id'])->update(['legality'=>2,'note'=>'已提交至蒲曦寄售平台']);
                $status = true;
            }else{
                Db::name('ewm_order')->where('id',$v['id'])->update(['legality'=>2,'note'=>'蒲曦寄售返回：'.$res['msg'],'status'=>2]);
                $status = false;
            }
            $this->JdECardLog($v['order_no'],$status);
        }
    }



    public function PuXiJiShouCardHuiyuan(){
//        if (serverIP() != get_userip()){
//            echo "IP ERROR";
//            die;
//        }
        $xkms = Db::name('jdek_sale')->where(['type'=>5,'status'=>1])->column('ms_id');
        if (empty($xkms)) return '暂无码商开启自动提交';
        $data = Db::name('ewm_order')
            ->where('code_type','in',['61','60','62'])
            ->where('cardKey','neq','')
            ->where('cardAccount','neq','')
            ->where('status',0)
            ->where('legality',1)
            ->where('gema_userid','in',$xkms)
            ->where('add_time','>',time()-600)
            ->select();
        if (empty($data)) return '暂无可处理的订单';
//        Log::error('PuXiJiShouCardSubmitOrderData :' . json_encode($data,true));

        foreach ($data as $k=>$v){
            $res = json_decode($this->PuXiJiShouCardSubmit($v),true);
            if ($res['code'] == 0){
                Db::name('ewm_order')->where('id',$v['id'])->update(['legality'=>2,'note'=>'已提交至蒲曦寄售平台']);
                $status = true;
            }else{
                Db::name('ewm_order')->where('id',$v['id'])->update(['legality'=>2,'note'=>'蒲曦寄售返回：'.$res['msg'],'status'=>2]);
                $status = false;
            }
            $this->JdECardLog($v['order_no'],$status);
        }
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

    protected $PuXiJiShouCardIpWrite = ['120.48.145.187'];

    public function PuXiJiShouCardNotify(){
        if (!in_array(get_userip(),$this->PuXiJiShouCardIpWrite)){
            Log::error('PuXiJiShouCardSubmit Notify IP Error :'. $_SERVER['REMOTE_ADDR']);
            return json(['code'=>'404','msg'=>'IP不合法']);
        }

//
//        $NotifyDatas = $_GET;
//        Log::notice("PuXiJiShouCardNotify notify GET data".json_encode($NotifyDatas,true));
        $NotifyData = $_POST;
        Log::notice("PuXiJiShouCardNotify notify POST data".json_encode($NotifyData,true));
        if ($NotifyData['state'] == 2){
            $order = Db::name('ewm_order')->where('order_no',$NotifyData['orderId'])->find();
            if (empty($order)){
                echo "Error";
                die;
            }
            if ($NotifyData['settleFace'] != $order['order_price']){
                Db::name('ewm_order')->where('id',$order['id'])->update(['note'=>['销卡金额不匹配，实际金额：'.$NotifyData['settleFace']]]);
                echo "Error";
                die;
            }
            $GemaOrder = new \app\common\logic\EwmOrder();
            $res = $GemaOrder->setOrderSucessByUserNullPayPass($order['id'], $order['gema_userid'], 0, 0,'蒲曦寄售平台自动回调');
            if($res['code'] == 1){
                Db::name('ewm_order')->where('id',$order['id'])->update(['legality'=>1]);
                echo "OK";
                die;
            }else{
                echo "订单处理失败";
                die;
            }
        }else{
            Db::name('ewm_order')
                ->where('order_no',$NotifyData['orderId'])
                ->update(['note'=>$NotifyData['failReason'],'status'=>2]);
            echo "Error";
            die;
        }
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


    public function Echaka(){
        $xkms = Db::name('jdek_sale')->where(['type'=>1,'status'=>1])->column('ms_id');
        if (empty($xkms)) return '暂无码商开启自动提交';
        $data = Db::name('ewm_order')
            ->where('code_type','in',[43,52])
            ->where('cardKey','neq','')
            ->where('status',0)
            ->where('legality',1)
            ->where('gema_userid','in',$xkms)
            ->where('add_time','>',time()-600)
            ->select();
        if (empty($data)) return '暂无可处理的订单';

        foreach ($data as $k=>$v){
            $res = json_decode($this->EchakaSubmit($v),true);
            if ($res['code'] == 200){
                Db::name('ewm_order')->where('id',$v['id'])->update(['legality'=>2,'note'=>'已提交至E查卡平台']);
                $status = true;
            }else{
                Db::name('ewm_order')->where('id',$v['id'])->update(['legality'=>2,'note'=>'E查卡返回：'.$res['extend']['message']]);
                $status = false;
            }
            $this->JdECardLog($v['order_no'],$status);
        }


    }

    private function JdECardLog($order,$status){
        if ($status){
            Log::notice('JdECardSubmit Submit Success Order: '.$order);
            echo '处理成功： '.$order.'<br>';
//            die;
        }else{
            Log::error('JdECardSubmit Submit Error Order: '.$order);
            echo '处理失败： '.$order.'<br>';
//            die;
        }
    }


    private function EchakaSubmit($order){
        $apiInfo = Db::name('jdek_sale')->where(['ms_id'=>$order['gema_userid'],'type'=>1])->find();
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




    protected $ip_write = ['123.60.172.95','43.143.121.88','101.35.215.196','124.221.0.115','8.210.16.62'];

    public function EchakaNotify(){
        if (!in_array(get_userip(), $this->ip_write)){
            Log::error('JdECardSubmitNotifyIPError :'. $_SERVER['REMOTE_ADDR']);
            return json(['code'=>'404','msg'=>'IP不合法']);
        }

        $JdECardPostNotifyData = $_POST;
        Log::notice("EchakaNotify notify POST data".json_encode($JdECardPostNotifyData,true));


        $input = file_get_contents("php://input");
        Log::notice("EchakaNotify notify post json data".$input);
        $JdECardNotifyData = json_decode($input,true);
        if (empty($JdECardNotifyData)) return json(['code'=>'404','msg'=>'接收不到任何数据']);
        $order = Db::name('ewm_order')->where('order_no',$JdECardNotifyData['merOrderId'])->find();
        if (empty($order)){
            echo "无此订单信息";
            die;
        }
        if ($JdECardNotifyData['bindStatus'] == 1){
            if ($JdECardNotifyData['realFaceValue'] != $order['order_pay_price']){
                Db::name('ewm_order')
                    ->where('order_no',$JdECardNotifyData['merOrderId'])
                    ->update(['note'=>'金额不匹配,实付金额：'.$JdECardNotifyData['realFaceValue']]);
                echo "Y";
                die;
            }
            $GemaOrder = new \app\common\logic\EwmOrder();
            $res = $GemaOrder->setOrderSucessByUserNullPayPass($order['id'], $order['gema_userid'], 0, 0,'E查卡平台自动回调');
            Db::name('ewm_order')->where('order_no',$JdECardNotifyData['merOrderId'])->update(['legality'=>1]);
            echo "Y";
            die;
        }else{
            Db::name('ewm_order')
                ->where('order_no',$JdECardNotifyData['merOrderId'])
                ->update(['note'=>$JdECardNotifyData['retmsg']]);
            echo "Y";
            die;
        }

    }


//酷氪销卡 -- 土豆
    public function kuke(){
//        if (serverIP() != get_userip()){
//            echo "IP ERROR";
//            die;
//        }
        $xkms = Db::name('jdek_sale')->where(['type'=>3,'status'=>1])->column('ms_id');
        if (empty($xkms)) return '暂无码商开启自动提交';
        $data = Db::name('ewm_order')
            ->where('code_type','in',[43,52])
            ->where('cardKey','neq','')
            ->where('status',0)
            ->where('legality',1)
            ->where('gema_userid','in',$xkms)
            ->where('add_time','>',time()-600)
            ->select();
        if (empty($data)) return '暂无可处理的订单';

        foreach ($data as $k=>$v){
            $res = json_decode($this->KuKeSubmit($v),true);
            if ($res['code'] == 200){
                Db::name('ewm_order')->where('id',$v['id'])->update(['legality'=>2,'note'=>'已提交至酷氪销卡平台']);
                $status = true;
            }else{
                Db::name('ewm_order')->where('id',$v['id'])->update(['legality'=>2,'note'=>'酷氪销卡返回：'.$res['message']]);
                $status = false;
            }
            $this->KukeLog($v['order_no'],$status);
        }

    }


    private function KukeLog($order,$status){
        if ($status){
            Log::notice('Kuke Submit Success Order: '.$order);
            echo '处理成功： '.$order.'<br>';
//            die;
        }else{
            Log::error('Kuke Submit Error Order: '.$order);
            echo '处理失败： '.$order.'<br>';
//            die;
        }
    }

    private function KuKeSubmit($order){
        $apiInfo = Db::name('jdek_sale')->where(['ms_id'=>$order['gema_userid'],'type'=>3])->find();
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
    protected $kuke_ip_write = ['182.160.3.187'];
    public function KuKeNotify(){
        if (!in_array(get_userip(), $this->kuke_ip_write)){
            Log::error('KuKeNotifyIPError :'. $_SERVER['REMOTE_ADDR']);
            return json(['code'=>'404','msg'=>'IP不合法']);
        }

        $input = file_get_contents("php://input");
        Log::notice("KuKeNotifyNotify data".$input);

        $JdECardNotifyData = json_decode($input,true);
        if (empty($JdECardNotifyData)) return json(['code'=>'404','msg'=>'接收不到任何数据']);
        $order = Db::name('ewm_order')->where('order_no',$JdECardNotifyData['data']['bizOrderNo'])->find();
        if (empty($order)){
            echo "无此订单信息";
            die;
        }
        if ($JdECardNotifyData['data']['bindState'] == 200){
            if ($JdECardNotifyData['data']['cardAmount'] / 100 != $order['order_pay_price']){
                Db::name('ewm_order')
                    ->where('order_no',$JdECardNotifyData['data']['bizOrderNo'])
                    ->update(['note'=>'金额不匹配,实付金额：'.$JdECardNotifyData['data']['cardAmount']]);
                echo "success";
                die;
            }
            $GemaOrder = new \app\common\logic\EwmOrder();
            $res = $GemaOrder->setOrderSucessByUserNullPayPass($order['id'], $order['gema_userid'], 0, 0,'酷氪销卡平台自动回调');
            Db::name('ewm_order')->where('order_no',$JdECardNotifyData['data']['bizOrderNo'])->update(['legality'=>1]);
            echo "success";
            die;
        }else{
            if ($JdECardNotifyData['data']['bindState'] == 400){
                $msg = '重复绑定';
            }elseif ($JdECardNotifyData['data']['bindState'] == 500){
                $msg = '未知状态';
            }elseif ($JdECardNotifyData['data']['bindState'] == 506 || $JdECardNotifyData['data']['bindState'] == 508){
                $msg = '绑定失败';
            }elseif ($JdECardNotifyData['data']['bindState'] == 510){
                $msg = '卡密错误';
            }elseif ($JdECardNotifyData['data']['bindState'] == 511){
                $msg = '已被绑定过的卡密';
            }elseif ($JdECardNotifyData['data']['bindState'] == 550){
                $msg = '卡被冻结或卡被删除';
            }
            Db::name('ewm_order')
                ->where('order_no',$JdECardNotifyData['data']['bizOrderNo'])
                ->update(['note'=>$msg]);
            echo "success";
            die;
        }

    }


    public function MayiHuiYuanYiFu(){
        $xkms = Db::name('jdek_sale')->where(['type'=>4,'status'=>1])->column('ms_id');
        if (empty($xkms)) return '暂无码商开启自动提交';
        $data = Db::name('ewm_order')
            ->where('code_type',61)
            ->where('cardKey','neq','')
            ->where('cardAccount','neq','')
            ->where('status',0)
            ->where('legality',1)
            ->where('gema_userid','in',$xkms)
            ->where('add_time','>',time()-600)
            ->select();
        if (empty($data)) return '暂无可处理的订单';

        foreach ($data as $k=>$v){
            $res = json_decode($this->MayiHuiYuanYiFuSubmit($v),true);
            if ($res['code'] == 200){
                Db::name('ewm_order')->where('id',$v['id'])->update(['legality'=>2,'note'=>'已提交至蚂蚁销卡平台']);
                $status = true;
            }else{
                Db::name('ewm_order')->where('id',$v['id'])->update(['legality'=>2,'note'=>'蚂蚁销卡返回：'.$res['datas']['error']]);
                $status = false;
            }
//            $this->KukeLog($v['order_no'],$status);
            echo '---'.$v['order_no'].'---'."\n";
        }

    }

    protected function MayiHuiYuanYiFuSubmit($order){
        $apiInfo = Db::name('jdek_sale')->where(['ms_id'=>$order['gema_userid'],'type'=>3,'pay_code'=>61])->find();
        $merchantId = $apiInfo['merchantId'];
        $signKey = $apiInfo['signkey'];
        $encryptionkey = $apiInfo['encryptionkey'];
        $api_url = 'https://www.mayijishou.com/card/index.php?action=card_submit&mo=submit';
        $data = [
            'client_id' => $merchantId,
            'card_no' => $order['cardAccount'],
            'card_key' => $this->encryptDesEcbPKCS5($order['cardKey'],$encryptionkey),
            'face_value' => $order['order_pay_price'],
            'out_order_id' => $order['order_no'],
            'getway' => 620,
            'nonce' => rand(100000,999999),
            'timestamp' => time(),
            'notify_url' => $this->request->domain().'/api/pay/MayiHuiYuanYiFuNotify'
        ];

        ksort($data);
        $str='';
        foreach ($data as $key=>$value){
            $str.=$value;
        }
        $sign=strtolower(md5($str.$signKey));//签名

        $data['sign'] = $sign;

        $result = $this->send_post_json($api_url,json_encode($data,true));
        Log::error('KuKeSubmit Return Data :'.$result);
        Db::name('ewm_order')->where('id',$order['id'])->update(['updateTime'=>time(),'xiaoka_id'=>$apiInfo['id']]);
        return $result;



    }

    protected $MayiHuiYuanYiFu_ip_write = [''];
    public function MayiHuiYuanYiFuNotify(){
        if (!in_array($_SERVER['REMOTE_ADDR'], $this->MayiHuiYuanYiFu_ip_write)){
            Log::error('KuKeNotifyIPError :'. $_SERVER['REMOTE_ADDR']);
            return json(['code'=>'404','msg'=>'IP不合法']);
        }
        $input = file_get_contents("php://input");
        Log::notice("MayiHuiYuanYiFuNotifyData".$input);

        $notifyData = json_decode($input,true);
        if ($notifyData['code'] == 200){
            $order = Db::name('ewm_order')->where('order_no',$notifyData['out_order_id'])->find();
            if (empty($order)){
                echo "无此订单信息";
                die;
            }

            if ($notifyData['success_amount'] != $order['order_pay_price']){
                Db::name('ewm_order')
                    ->where('order_no',$notifyData['out_order_id'])
                    ->update(['note'=>'金额不匹配,实付金额：'.$notifyData['success_amount']]);
                echo "success";
                die;
            }

            $GemaOrder = new \app\common\logic\EwmOrder();
            $res = $GemaOrder->setOrderSucessByUserNullPayPass($order['id'], $order['gema_userid'], 0, 0,'蚂蚁销卡平台自动回调');
            Db::name('ewm_order')->where('order_no',$notifyData['out_order_id'])->update(['legality'=>1]);
            echo "success";
            die;


        }else{
            Db::name('ewm_order')
                ->where('order_no',$notifyData['out_order_id'])
                ->update(['note'=>$notifyData['message']]);
            echo "success";
            die;
        }

    }


    public function estJunWeb(){
        $xkms = Db::name('jdek_sale')->where(['type'=>6,'status'=>1])->column('ms_id');
        if (empty($xkms)) return '暂无码商开启自动提交';
        $data = Db::name('ewm_order')
            ->where('code_type',60)
            ->where('cardKey','neq','')
            ->where('cardAccount','neq','')
            ->where('status',0)
            ->where('legality',1)
            ->where('gema_userid','in',$xkms)
            ->where('add_time','>',time()-600)
            ->select();
        if (empty($data)) return '暂无可处理的订单';

        foreach ($data as $k=>$v){
            $res = json_decode($this->estJunWebSubmit($v),true);
            if ($res['code'] == 200){
                Db::name('ewm_order')->where('id',$v['id'])->update(['legality'=>2,'note'=>'已提交至e售通平台']);
                $status = true;
            }else{
                Db::name('ewm_order')->where('id',$v['id'])->update(['legality'=>2,'note'=>'e售通返回：'.$res['message']]);
                $status = false;
            }
//            $this->KukeLog($v['order_no'],$status);
            echo '---'.$v['order_no'].'---'."\n";
        }
    }


    private function estJunWebSubmit($order){
        $apiInfo = Db::name('jdek_sale')->where(['ms_id'=>$order['gema_userid'],'type'=>6,'pay_code'=>60])->find();
        $merchantId = $apiInfo['merchantId'];
        $signKey = $apiInfo['signkey'];
//        $encryptionkey = $apiInfo['encryptionkey'];
        $api_url = 'http://api.eshou.xyz/open/entrance/cardUpload';

        $data = [
            'app_key' => $merchantId,
            'card_type' => '10026',
            'face_type' => floor($order['order_price']),
            'callback_url' => $this->request->domain().'/api/pay/estJunWebNotify',
            'data' => $order['cardAccount'].','.$order['cardKey'].','.$order['order_no']
        ];
        $data['sign'] = $this->estJunWebgetSign($data,$signKey);
        Log::error('estJunWebSubmitApiData:'.json_encode($data,true));

        $result = $this->curlPost($api_url,$data);
        Log::error('estJunWebSubmitReturnData :'.$result);
        Db::name('ewm_order')->where('id',$order['id'])->update(['updateTime'=>time(),'xiaoka_id'=>$apiInfo['id']]);
        return $result;
    }
    protected $estJunWebNotify_ip_write = ['47.104.94.187'];
    public function estJunWebNotify(Request $request){
        if (!in_array($_SERVER['REMOTE_ADDR'], $this->estJunWebNotify_ip_write)){
            Log::error('estJunWebNotifyIPError :'. $_SERVER['REMOTE_ADDR']);
            return json(['code'=>'404','msg'=>'IP不合法']);
        }
//        $input = file_get_contents("php://input");
//        Log::notice("MayiHuiYuanYiFuNotifyData".$input);

        $notifyData = $request->post();
        Log::notice("estJunWebNotifyData".json_encode($notifyData,true));
        if ($notifyData['state'] == 102){
            $order = Db::name('ewm_order')->where('order_no',$notifyData['order_id'])->find();
            if (empty($order)){
                echo "无此订单信息";
                die;
            }

            if ($notifyData['settle_value'] != $order['order_pay_price']){
                Db::name('ewm_order')
                    ->where('order_no',$order['id'])
                    ->update(['note'=>'金额不匹配,实付金额：'.$notifyData['settle_value']]);
                echo "SUCCESS";
                die;
            }

            $GemaOrder = new \app\common\logic\EwmOrder();
            $res = $GemaOrder->setOrderSucessByUserNullPayPass($order['id'], $order['gema_userid'], 0, 0,'e售通平台自动回调');
            Db::name('ewm_order')->where('order_no',$notifyData['order_id'])->update(['legality'=>1]);
            echo "SUCCESS";
            die;


        }else{
            Db::name('ewm_order')
                ->where('order_no',$notifyData['order_id'])
                ->update(['note'=>$notifyData['state_info']]);
            echo "SUCCESS";
            die;
        }

    }


    private function estJunWebgetSign($data, $secret)
    {

        //签名步骤一：按字典序排序参数
        ksort($data);
        $string_a = '';
        foreach ($data as $k => $v) {
            $string_a .= "{$k}{$v}";
        }
//        $string_a = substr($string_a,0,strlen($string_a) - 1);
        //签名步骤三：MD5加密
        $sign = md5($string_a . $secret);

        // 签名步骤四：所有字符转为大写
//        $result = strtoupper($sign);

        return $sign;
    }


    public function taobao188(){
        $xkms = Db::name('jdek_sale')->where(['type'=>7,'status'=>1])->column('ms_id');
        if (empty($xkms)) return '暂无码商开启自动提交';
        $data = Db::name('ewm_order')
            ->where('code_type',60)
            ->where('cardKey','neq','')
            ->where('cardAccount','neq','')
            ->where('status',0)
            ->where('legality',1)
            ->where('gema_userid','in',$xkms)
            ->where('add_time','>',time()-600)
            ->select();
        if (empty($data)) return '暂无可处理的订单';

        foreach ($data as $k=>$v){
            $res = json_decode($this->taobao188Submit($v),true);
            if ($res['code'] == 1){
                Db::name('ewm_order')->where('id',$v['id'])->update(['legality'=>2,'note'=>'已提交至淘宝188平台']);
//                $status = true;
            }else{
                Db::name('ewm_order')->where('id',$v['id'])->update(['legality'=>2,'note'=>'淘宝188返回：'.$res['message']]);
//                $status = false;
            }
//            $this->KukeLog($v['order_no'],$status);
//            echo '---'.$v['order_no'].'---'."\n";
        }
    }


    public function taobao188Submit($order){
        $apiInfo = Db::name('jdek_sale')->where(['ms_id'=>$order['gema_userid'],'type'=>8,'pay_code'=>60])->find();
        $merchantId = $apiInfo['merchantId'];
        $signKey = $apiInfo['signkey'];
        $encryptionkey = $apiInfo['encryptionkey'];
        $api_url = 'http://www.taojin188.cn/api.php/tocard.html';
        $data = [
            'customerId' => $merchantId,
            'timestamp' => time(),
            'orderId' => $order['out_trade_no'],
            'productCode' => '1033',
            'cardNumber' => $this->encrypt_taobao188($order['cardAccount'],$encryptionkey),
            'cardPassword' => $this->encrypt_taobao188($order['cardKey'],$encryptionkey),
            'amount' => sprintf("%.2f",$order['order_pay_price']),
            'notify' => $this->request->domain().'/api/pay/taobao188Notify',
        ];

        $data['sign'] = $this->taobao188Sign($data,$signKey);
        Log::error('taobao188SubmitApiData :'.json_encode($data,true));
        $result = $this->send_post_json($api_url,json_encode($data,true));
        Log::error('taobao188SubmitReturnData :'.$result);
        Db::name('ewm_order')->where('id',$order['id'])->update(['updateTime'=>time(),'xiaoka_id'=>$apiInfo['id']]);
        return $result;

    }

    protected $taobao188Notify_ip_write = [''];
    public function taobao188Notify(Request $request){
        if (!in_array($_SERVER['REMOTE_ADDR'], $this->taobao188Notify_ip_write)){
            Log::error('taobao188NotifyIPError :'. $_SERVER['REMOTE_ADDR']);
            return json(['code'=>'404','msg'=>'IP不合法']);
        }

        $notifyData = $request->post();
        Log::notice("taobao188NotifyData".json_encode($notifyData,true));
        if ($notifyData['status'] == 2){
            $order = Db::name('ewm_order')->where('order_no',$notifyData['orderId'])->find();
            if (empty($order)){
                echo "无此订单信息";
                die;
            }

            if ($notifyData['realPrice'] != $order['order_pay_price']){
                Db::name('ewm_order')
                    ->where('order_no',$order['id'])
                    ->update(['note'=>'金额不匹配,实付金额：'.$notifyData['realPrice']]);
                echo "SUCCESS";
                die;
            }

            $GemaOrder = new \app\common\logic\EwmOrder();
            $res = $GemaOrder->setOrderSucessByUserNullPayPass($order['id'], $order['gema_userid'], 0, 0,'淘宝188自动回调');
            Db::name('ewm_order')->where('order_no',$notifyData['orderId'])->update(['legality'=>1]);
            echo "SUCCESS";
            die;


        }else{
            Db::name('ewm_order')
                ->where('order_no',$notifyData['orderId'])
                ->update(['note'=>$notifyData['message']]);
            echo "SUCCESS";
            die;
        }
    }



    private function taobao188Sign($data, $secret)
    {

        //签名步骤一：按字典序排序参数
        ksort($data);
        $string_a = '';
        foreach ($data as $k => $v) {
            $string_a .= "{$k}={$v}&";
        }
//        $string_a = substr($string_a,0,strlen($string_a) - 1);
        //签名步骤三：MD5加密
        Log::error('daMoXiaoKaSubmitSignStr :'.$string_a . 'Key=' . $secret);
        $sign = md5($string_a . 'key=' . $secret);

        // 签名步骤四：所有字符转为大写
//        $result = strtoupper($sign);

        return $sign;
    }


    private function encrypt_taobao188($data, $key) {
        // 设置加密算法、模式和填充方式
        $cipher = "AES-128-ECB";
        $iv = ""; // ECB模式不需要IV
        $options = OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING;

        // 执行加密操作
        $encryptedData = base64_decode(openssl_encrypt($data, $cipher, $key, 0));

        // 将加密后的数据转换为十六进制
        $hexData = bin2hex($encryptedData);

        return $hexData;
    }

//    private function encrypt_taobao188($data,$key) {
//        $encrypted = openssl_encrypt($data, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
//        $encrypted = $this->pkcs5Padding($encrypted);
//        return bin2hex($encrypted);
//    }

    private function pkcs5Padding($data) {
        $blockSize = 16;
        $padding = $blockSize - (strlen($data) % $blockSize);
        $data .= str_repeat(chr($padding), $padding);
        return $data;
    }



    public function daMoXiaoKa(){
        $xkms = Db::name('jdek_sale')->where(['type'=>8,'status'=>1])->column('ms_id');
        if (empty($xkms)) return '暂无码商开启自动提交';
        $data = Db::name('ewm_order')
            ->where('code_type',60)
            ->where('cardKey','neq','')
            ->where('cardAccount','neq','')
            ->where('status',0)
            ->where('legality',1)
            ->where('gema_userid','in',$xkms)
            ->where('add_time','>',time()-600)
            ->select();
        if (empty($data)) return '暂无可处理的订单';

        foreach ($data as $k=>$v){
            $res = $this->daMoXiaoKaSubmit($v);
//            $res = $res['think'];
            if ($res['code'] == 1){
                Db::name('ewm_order')->where('id',$v['id'])->update(['legality'=>2,'note'=>'已提交至达沫销卡平台']);
//                $status = true;
            }else{
                Db::name('ewm_order')->where('id',$v['id'])->update(['legality'=>2,'note'=>'达沫销卡返回：'.$res['message']]);
//                $status = false;
            }
//            $this->KukeLog($v['order_no'],$status);
//            echo '---'.$v['order_no'].'---'."\n";
        }
    }


    public function daMoXiaoKaSubmit($order){
        $apiInfo = Db::name('jdek_sale')->where(['ms_id'=>$order['gema_userid'],'type'=>8,'pay_code'=>60])->find();
        $merchantId = trim($apiInfo['merchantId']);
        $signKey = trim($apiInfo['signkey']);
        $encryptionkey = trim($apiInfo['encryptionkey']);
        $api_url = 'http://api.damoshuju.cn/tocard.html';
        $data = [
            'customerId' => $merchantId,
            'timestamp' => time(),
            'orderId' => $order['out_trade_no'],
            'productCode' => '1033',
            'cardNumber' => $this->encrypt_taobao188($order['cardAccount'],$encryptionkey),
            'cardPassword' => $this->encrypt_taobao188($order['cardKey'],$encryptionkey),
            'amount' => floor($order['order_pay_price']),
            'notify_url' => $this->request->domain().'/api/pay/daMoXiaoKaNotify',
            'batchno' => $order['out_trade_no']
        ];

        $data['sign'] = $this->taobao188Sign($data,$signKey);
        Log::error('daMoXiaoKaSubmitApiData :'.json_encode($data,true));
        $xmlString = $this->curlPost($api_url,$data);
        Log::error('daMoXiaoKaSubmitReturnData :'.$xmlString);

        $xml = simplexml_load_string($xmlString);
        $think = [];

        foreach ($xml as $key => $value) {
            $think[$key] = (string) $value;
        }

//        print_r($think);
        return $think;
    }

    protected $daMoXiaoKaNotify_ip_write = ['114.132.231.126'];
    public function daMoXiaoKaNotify(Request $request){
        if (!in_array(get_userip(), $this->daMoXiaoKaNotify_ip_write)){
            Log::error('daMoXiaoKaNotifyIPError :'. $_SERVER['REMOTE_ADDR']);
            return json(['code'=>'404','msg'=>'IP不合法']);
        }

        $notifyData = $request->post();
        Log::notice("daMoXiaoKaNotifyData".json_encode($notifyData,true));
        if ($notifyData['status'] == 2){
            $order = Db::name('ewm_order')->where('order_no',$notifyData['orderId'])->find();
            if (empty($order)){
                echo "无此订单信息";
                die;
            }

            if ($notifyData['realPrice'] != $order['order_pay_price']){
                Db::name('ewm_order')
                    ->where('order_no',$order['id'])
                    ->update(['note'=>'金额不匹配,实付金额：'.$notifyData['realPrice']]);
                echo "SUCCESS";
                die;
            }

            $GemaOrder = new \app\common\logic\EwmOrder();
            $res = $GemaOrder->setOrderSucessByUserNullPayPass($order['id'], $order['gema_userid'], 0, 0,'达沫销卡自动回调');
            Db::name('ewm_order')->where('order_no',$notifyData['orderId'])->update(['legality'=>1]);
            echo "SUCCESS";
            die;


        }else{
            Db::name('ewm_order')
                ->where('order_no',$notifyData['orderId'])
                ->update(['note'=>$notifyData['message']]);
            echo "SUCCESS";
            die;
        }
    }

    /**
     *
     *收卡云
     */

    public function shouKaCloud(){
        $xkms = Db::name('jdek_sale')->where(['type'=>9,'status'=>1])->column('ms_id');
        if (empty($xkms)) return '暂无码商开启自动提交';
        $data = Db::name('ewm_order')
            ->where('code_type',60)
            ->where('cardKey','neq','')
            ->where('cardAccount','neq','')
            ->where('status',0)
            ->where('legality',1)
            ->where('gema_userid','in',$xkms)
            ->where('add_time','>',time()-600)
            ->select();
        if (empty($data)) return '暂无可处理的订单';

        foreach ($data as $k=>$v){
            $res = json_decode($this->shouKaCloudSubmit($v),true);
//            $res = $res['think'];
            if ($res['code'] == 0){
                Db::name('ewm_order')->where('id',$v['id'])->update(['legality'=>2,'note'=>'已提交至收卡云平台']);
//                $status = true;
            }else{
                Db::name('ewm_order')->where('id',$v['id'])->update(['legality'=>2,'note'=>'收卡云返回：'.$res['msg']]);
//                $status = false;
            }
//            $this->KukeLog($v['order_no'],$status);
//            echo '---'.$v['order_no'].'---'."\n";
        }
    }


    private function shouKaCloudSubmit($order){
        $apiInfo = Db::name('jdek_sale')->where(['ms_id'=>$order['gema_userid'],'type'=>9,'pay_code'=>60])->find();
        $merchantId = trim($apiInfo['merchantId']);
        $signKey = trim($apiInfo['signkey']);
        $encryptionkey = trim($apiInfo['encryptionkey']);
        $api_url = 'https://www.shoukayun.com/open/card';
        if (floor($order['order_pay_price']) < 50){
            $product_no = 'JWZCK_'.floor($order['order_pay_price']);
        }else{
            $product_no = 'JWZC_'.floor($order['order_pay_price']);
        }
        $data = [
            'account' => $merchantId,
            'order_no' => $order['out_trade_no'],
            'product_no' => $product_no,
            'card_no' => $this->shouKaCloudEncrypt($order['cardAccount'],$encryptionkey),
            'card_password' => $this->shouKaCloudEncrypt($order['cardKey'],$encryptionkey),
            'notify_url' => $this->request->domain().'/api/pay/shouKaCloudNotify',
        ];

        $data['sign'] = $this->shouKaCloudGetSign($data,$signKey);


        Log::error('shouKaCloudSubmitApiData :'.json_encode($data,true));
        $xmlString = $this->curlPost($api_url,$data);
        Log::error('shouKaCloudSubmitReturnData :'.$xmlString);

        return $xmlString;
    }

    protected $shouKaCloudNotify_ip_write = ['47.96.8.174'];
    public function shouKaCloudNotify(Request $request){
        if (!in_array(get_userip(), $this->shouKaCloudNotify_ip_write)){
            Log::error('shouKaCloudNotifyIPError :'. $_SERVER['REMOTE_ADDR']);
            return json(['code'=>'404','msg'=>'IP不合法']);
        }

        $notifyData = $request->post();
        Log::notice("shouKaCloudNotifyData".json_encode($notifyData,true));
        if ($notifyData['status'] == 3){
            $order = Db::name('ewm_order')->where('order_no',$notifyData['order_no'])->find();
            if (empty($order)){
                echo "无此订单信息";
                die;
            }

            if ($notifyData['fave_value'] != $order['order_pay_price']){
                Db::name('ewm_order')
                    ->where('order_no',$order['id'])
                    ->update(['note'=>'金额不匹配,实付金额：'.$notifyData['fave_value']]);
                echo "success";
                die;
            }

            $GemaOrder = new \app\common\logic\EwmOrder();
            $res = $GemaOrder->setOrderSucessByUserNullPayPass($order['id'], $order['gema_userid'], 0, 0,'收卡云自动回调');
            Db::name('ewm_order')->where('order_no',$notifyData['order_no'])->update(['legality'=>1]);
            echo "success";
            die;


        }else{
            Db::name('ewm_order')
                ->where('order_no',$notifyData['order_no'])
                ->update(['note'=>$notifyData['status_text']]);
            echo "success";
            die;
        }

    }


    private function shouKaCloudGetSign($data,$signKey) {
        ksort($data);
        $str = '';
        foreach($data as $k => $v) {
            if((!empty($v) || is_numeric($v)) && !in_array($k,['sign','account'])) {
                $str .= "{$k}={$v}&";
            }
        }
        return strtoupper(md5($str . "key={$signKey}"));
    }


    private function shouKaCloudEncrypt($data,$apiKey){
        $iv = substr(md5($apiKey),8,16);
        $aesdata =  openssl_encrypt($data,'AES-128-CBC', $apiKey, OPENSSL_RAW_DATA,$iv);

        $data = base64_encode($aesdata);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }














    /**
     *
     * 加密函数
     * 算法：des
     *
     * @param unknown_type $input
     */
    private  function encryptDesEcbPKCS5($input, $key)
    {

        $data =openssl_encrypt($input,'des-ecb',$key);// mcrypt_get_block_size('des', 'ecb');

        return $data;
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



}
