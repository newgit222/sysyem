<?php

namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Log;
use think\Validate;

class Pay extends Controller
{
    /**
     * var string $secret_key 加解密的密钥
     */
    protected $secret_key  = 'f3a59b69324c831e';
    protected $new_secret_key  = '8e70f72bc3f53b12';

    /**
     * var string $iv 加解密的向量，有些方法需要设置比如CBC
     */
    protected $iv = '7fc7fe7d74f4da93';
    protected $new_iv = '99b538370c7729c7';

    public function orderQuery(){
        $order = $this->request->param('key');
        $order = $this->decrypt($order);
        if (empty($order)){
            return json(['code'=>0,'msg'=>'数据错误']);
	}
	$order = addslashes($order);
//	$order = mysql_real_escape_string($order);
        $vadata['trade_no'] = $order;
        $validate = new Validate([
            'trade_no' => 'alphaNum|max:50',
        ],[
            'trade_no.alphaNum' => '订单号只能为字母和数字',
        ]);

        if (!$validate->check($vadata)) {
//            $this->error($validate->getError());
            return json(['code'=>0,'msg'=>$validate->getError()]);
        }

        $ewm_order = Db::name('ewm_order')->where(['order_no'=>$order])->find();
        if (empty($ewm_order)){
            return json(['code'=>0,'msg'=>'数据错误']);
        }

        $orders = Db::name('orders')->where('trade_no',$order)->find();

        $admin_id = Db::name('user')->where('uid',$orders['uid'])->value('admin_id');

//        $orderTime = Db::name('config')->where(['name'=>'order_invalid_time','admin_id'=>$admin_id])->value('value');
//
//        if (!empty($orderTime)){
//            $invalid_time = $orderTime * 60;
//        }else{
//            $invalid_time = 360;
//        }

        $orderTime = getAdminPayCodeSys('order_invalid_time',$ewm_order['code_type'],$admin_id);
        if (empty($orderTime)){
            $orderTime = 600;
        }else{
            $orderTime = $orderTime * 60 ;
        }
//        $orderTime = 600;
        if (empty($orders)){
            return json(['code'=>0,'msg'=>'数据错误']);
        }
        if (time() > $ewm_order['add_time'] + $orderTime){
            return json(['code'=> -1,'data'=>['success_url'=>'http://www.baidu.com/']]);
        }

        if ($ewm_order['status'] == 1){
            return json(['code'=>200,'data'=>['status'=>"1",'order_status_msg'=>'订单付款完成','success_url'=>'http://www.baidu.com/']]);
        }

        return json(['code'=>1,'data'=>['deadline_time'=>$ewm_order['add_time'] + $orderTime - time()]]);

    }




    private function decrypt($data)
    {
        $a = openssl_decrypt(base64_decode($data), "AES-128-CBC", $this->secret_key, true, $this->iv);
        if (!$a){
            $a = openssl_decrypt(base64_decode($data), "AES-128-CBC", $this->new_secret_key, true, $this->new_iv);
        }
        return $a;
        //return openssl_decrypt(base64_decode($data), "AES-128-CBC",$this->secret_key,true,$this->iv);
    }






}
