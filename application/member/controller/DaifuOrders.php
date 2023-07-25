<?php
/**
 * Created by PhpStorm.
 * User: zhangxiaohei
 * Date: 2020/2/7
 * Time: 21:27
 */

namespace app\member\controller;


use app\common\library\enum\CodeEnum;
use app\common\library\RsaUtils;
use app\common\logic\MsMoneyType;
use think\Cache;
use think\Cookie;
use think\Db;
use think\Exception;
use think\Request;
use think\Log;

class DaifuOrders extends Base
{

    public function upload_https(Request $request)
    {
        // 请替换为您的 HTTP 上传接口 URL
//        $uploadUrl = '';
        // 请替换为您的 HTTP 上传接口 URL
        $df_img_service_address = getAdminPayCodeSys('df_img_service_address',256,$this->agent['admin_id']);
        if (empty($df_img_service_address)){
            $uploadUrl = 'http://dfimg1.niuniualicloudid001.xyz/upload.php';
        }else{
            $uploadUrl = $df_img_service_address;
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

    public function checkOrderInfo(){
        $orderId = $this->request->param('id');
        $order= Db::name('daifu_orders')->where('id',$orderId)->find();

        if ($order['ms_id'] != $this->agent['userid'] || $order['status'] != 3){
            return json(['code'=>1,'msg'=>'订单已被处理']);
        }else{
            return json(['code'=>0,'msg'=>'处理中']);
        }

    }





    public function alipayToCard(){
        $orderId = $this->request->param('id');
        $order= Db::name('daifu_orders')->where('id',$orderId)->find();
        if ($order['ms_id'] != $this->agent_id){
            $this->error('非法请求');
        }
        $urls = '&actionType=toCard&sourceId=bill&bankAccount='.$order['bank_owner'].'&cardNo='.$order['bank_number'].'&money='.$order['amount'].'&amount='.$order['amount'].'&bankMark=&cardIndex=&cardNoHidden=true&cardChannel=HISTORY_CARD&orderSource=from&buyId=auto';

        $this->assign('url','https://ds.alipay.com/?scheme=alipays://platformapi/startapp?appId=09999988'.urlencode($urls));
        $this->assign('order',$order);
        return $this->fetch('alipayToCard');
    }

    /**
     * @return mixed
     * 代付订单列表
     */
    public function index(){
        $daifu_refresh_isver = getAdminPayCodeSys('daifu_refresh_isver',255,$this->agent['admin_id']);
        if (empty($daifu_refresh_isver)){
            $daifu_refresh_isver = 1;
        }

        $this->assign('daifu_refresh_isver',$daifu_refresh_isver);
        $this->assign('status',$this->request->param('Orderstatus'));
        $rsa_public_key = $this->modelConfig->where('name', 'rsa_public_key')->value('value');
        $rsa_private_key = $this->modelConfig->where('name', 'rsa_private_key')->value('value');
        $this->assign('publicKeyString', $rsa_public_key);
        $this->assign('rsa_private_key', $rsa_private_key);
        $daifu_success_uplode = getAdminPayCodeSys('daifu_success_uplode',255,$this->agent['admin_id']);
        if (empty($daifu_success_uplode)){
            $this->assign('daifu_success_uplode', 1);
        }else{
            $this->assign('daifu_success_uplode', $daifu_success_uplode);
        }
        $daifu_orders_username = getAdminPayCodeSys('daifu_orders_username',255,$this->agent['admin_id']);
        if (empty($daifu_orders_username)){
            $daifu_orders_username = 2;
        }
        $this->assign('daifu_orders_username',$daifu_orders_username);

        $this->assign('member_id',$this->agent_id);
//        $where = [];
//        $where['create_time'] = ['elt',time()];
//        $admin_sonUser = Db::name('user')->where('admin_id',$this->agent['admin_id'])->column('uid');
//        $where['uid'] = ['in',$admin_sonUser];
//        $where['ms_id'] = $this->agent_id;
//        $feesDb = Db::name('daifu_orders');
//        $fees['amount'] = sprintf("%.2f",$feesDb->where($where)->sum('amount'));
//        $fees['success_amount'] = sprintf("%.2f",$feesDb->where($where)->where(function ($querys){
//            $querys->where('status',2);
//        })->sum('amount'));
//        $fees['count'] = $feesDb->where($where)->count();
//        $fees['success_count'] = $feesDb->where($where)->where(function ($querys){
//            $querys->where('status',2);
//        })->count();
//        if ($fees['count'] > 0){
//            $fees['success_rate'] = ($fees['success_count'] / $fees['count']) * 100;
//        }else{
//            $fees['success_rate'] = 0;
//        }
//        $this->assign('fees',$fees);
        if ($this->agent['is_daifu'] != 1){
            return json([
                'code'=>1,
                'msg'=>'你没有代付权限，请联系管理员！'
            ]);
        }
        return $this->fetch();
    }


    public function common_orders(){

//        print_r($this->agent['is_daifu']);die;
        if ($this->agent['is_daifu'] != 1){
            return json([
                'code'=>1,
                'msg'=>'你没有代付权限，请联系管理员！'
            ]);
        }
        $daifu_refresh_isver = getAdminPayCodeSys('daifu_refresh_isver',255,$this->agent['admin_id']);
        if (empty($daifu_refresh_isver)){
            $daifu_refresh_isver = 1;
        }

        $this->assign('daifu_refresh_isver',$daifu_refresh_isver);
        $this->assign('status',$this->request->param('Orderstatus'));
        $rsa_public_key = $this->modelConfig->where('name', 'rsa_public_key')->value('value');
        $rsa_private_key = $this->modelConfig->where('name', 'rsa_private_key')->value('value');
        $this->assign('publicKeyString', $rsa_public_key);
        $this->assign('rsa_private_key', $rsa_private_key);
        $daifu_success_uplode = getAdminPayCodeSys('daifu_success_uplode',255,$this->agent['admin_id']);
        if (empty($daifu_success_uplode)){
            $this->assign('daifu_success_uplode', 1);
        }else{
            $this->assign('daifu_success_uplode', $daifu_success_uplode);
        }
        $daifu_orders_username = getAdminPayCodeSys('daifu_orders_username',255,$this->agent['admin_id']);
        if (empty($daifu_orders_username)){
            $daifu_orders_username = 2;
        }
        $this->assign('daifu_orders_username',$daifu_orders_username);

        $this->assign('member_id',$this->agent_id);
//        $where = [];
//        $where['create_time'] = ['elt',time()];
//        $admin_sonUser = Db::name('user')->where('admin_id',$this->agent['admin_id'])->column('uid');
//        $where['uid'] = ['in',$admin_sonUser];
//        $where['ms_id'] = $this->agent_id;
//        $feesDb = Db::name('daifu_orders');
//        $fees['amount'] = sprintf("%.2f",$feesDb->where($where)->sum('amount'));
//        $fees['success_amount'] = sprintf("%.2f",$feesDb->where($where)->where(function ($querys){
//            $querys->where('status',2);
//        })->sum('amount'));
//        $fees['count'] = $feesDb->where($where)->count();
//        $fees['success_count'] = $feesDb->where($where)->where(function ($querys){
//            $querys->where('status',2);
//        })->count();
//        if ($fees['count'] > 0){
//            $fees['success_rate'] = ($fees['success_count'] / $fees['count']) * 100;
//        }else{
//            $fees['success_rate'] = 0;
//        }
//        $this->assign('fees',$fees);
        return $this->fetch();
}





    /**
     * 上传凭证
     */
    public function upload()
    {
        $orderinfo = Db::name('daifu_orders')->where('id',$this->request->param('orderid'))->field('transfer_chart,ms_id')->find();
        if ($orderinfo['ms_id'] != $this->agent_id){
            $this->error('非法操作');
        }
        if (!empty($orderinfo['transfer_chart'])){
            $this->error('此订单已经上传过了');
        }


        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('file');
//        print_r($file);die;
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->validate(['size'=>2097152,'ext'=>'jpg,png','image' => 'require|image'])->move(ROOT_PATH . 'public' . DS . 'uploads'. DS .'transfer');
            if($info){
                $path=str_replace('\\','/',$info->getSaveName());
//                E:\wwwroot\git\sanfang\public\uploads\transfer\20221215\340c1ec6a83cfc681539174994e5e3f8.jpg
                $res = Db::name('daifu_orders')->where('id',$this->request->param('orderid'))->update(['transfer_chart'=>'/public/uploads/transfer/'.$path]);
//                public\uploads\transfer\20221215\fca52f78e51ac9e3aeeb3579b8ddca5d.jpg
                if ($res === false){
                    return json(['code'=>1,'msg'=>'上传失败']);
                }
                return json(['code'=>0,'msg'=>'上传成功']);
            }else{
                // 上传失败获取错误信息
//                echo $file->getError();

                return json(['code'=>1,'msg'=>$file->getError()]);
            }
        }
    }

    /**
     * 上传凭证
     */
    public function upload1()
    {
        $number = $this->request->param('number',0);
        if (!in_array($number, [1,2,3])){
            $this->error('参数错误');
        }

        switch ($number){
            case 1:
                $field_name = 'transfer_chart';
                break;
            case 2:
                $field_name = 'transfer_chart2';
                break;
            case 3:
                $field_name = 'transfer_chart3';
                break;
        }

        $orderinfo = Db::name('daifu_orders')->where('id',$this->request->param('orderid'))->field('transfer_chart,transfer_chart2,transfer_chart3,ms_id')->find();
        if ($orderinfo['ms_id'] != $this->agent_id){
            $this->error('非法操作');
        }
//        if (!empty($orderinfo['transfer_chart'])){
//            $this->error('此订单已经上传过了');
//        }

        $image_path = $this->request->param('image_path');
        if (empty($image_path)){
            return json(['code'=>1,'msg'=>'上传凭据不能为空']);
        }

        $image_path = explode(',',$image_path);

        $new_iamge_path =  [];
        foreach ($image_path as $itme){
            $new_iamge_path[] =   openssl_decrypt(base64_decode($itme), "AES-128-CBC", '8e70f72bc3f53b12', true, '99b538370c7729c7');
        }
     
        $new_iamge_path = implode(',',$new_iamge_path);

        $res = Db::name('daifu_orders')->where('id',$this->request->param('orderid'))->update(["{$field_name}"=>$new_iamge_path]);
//                public\uploads\transfer\20221215\fca52f78e51ac9e3aeeb3579b8ddca5d.jpg
        if ($res === false){
            return json(['code'=>1,'msg'=>'上传失败']);
        }
        return json(['code'=>0,'msg'=>'上传成功']);


    }

//    public function searchStats(){
//        $where = [];
//        $startTime = '';
//        $endTime = '';
//        $where['create_time'] = ['elt',time()];
//        if (!empty($this->request->param('startDate'))){
//            $startTime = $this->request->param('startDate');
//            $where['create_time'] = ['egt', strtotime($startTime)];
//        }
//        if (!empty($this->request->param('endDate'))){
//            $endTime = $this->request->param('endDate');
//            $where['create_time'] = ['elt',strtotime($endTime)];
//        }
//        if ($startTime && $endTime) {
//            $where['create_time'] = ['between', [strtotime($startTime), strtotime($endTime)]];
//        }
//        $admin_sonUser = Db::name('user')->where('admin_id',$this->agent['admin_id'])->column('uid');
//        $where['uid'] = ['in',$admin_sonUser];
//        $where['ms_id'] = $this->agent_id;
//
//        if (!empty($this->request->param('trade_no'))){
//            unset($where['create_time']);
//            $where['trade_no'] = $this->request->param('trade_no');
//        }
//
//        if (!empty($this->request->param('out_trade_no'))){
//            unset($where['create_time']);
//            $where['out_trade_no'] = $this->request->param('out_trade_no');
//        }
//        $status = $this->request->param('status');
//        if ($status != -1){
//            $where['status'] = $status;
//        }
//
////        print_r($where);die;
//        $feesDb = Db::name('daifu_orders');
//        $fees['amount'] = sprintf("%.2f",$feesDb->where($where)->sum('amount'));
//        $fees['success_amount'] = sprintf("%.2f",$feesDb->where($where)->where(function ($querys){
//            $querys->where('status',2);
//        })->sum('amount'));
//        $fees['count'] = $feesDb->where($where)->count();
//        $fees['success_count'] = $feesDb->where($where)->where(function ($querys){
//            $querys->where('status',2);
//        })->count();
//        if ($fees['count'] > 0){
//            $fees['success_rate'] = sprintf("%.2f",($fees['success_count'] / $fees['count']) * 100);
//        }else{
//            $fees['success_rate'] = 0;
//        }
//
//        return json(['code'=>0,'msg'=>'请求成功','data'=>$fees]);
//    }

    public function query_cache()
    {
        $refreshs = getAdminPayCodeSys('daifu_refresh_frequency', 255, $this->agent['admin_id']);
        if (empty($refreshs) && $refreshs != 0) {
            $refreshs = 120;
        }

        if (!empty($refreshs) && $refreshs > 0) {
            $heimingdan = Cache::get('getDaifuOrderList_ver_copt' . $this->agent_id);
            // $heimingdan = 1;
            $code = ($heimingdan == 1) ? 1 : 0;
        } else {
            $code = 0;
        }

        return json([
            'code' => $code,
        ]);
    }


    public function ver_captcha(){
        $member = $this->request->get('member_id');
        if ($member != $this->agent_id){
            return json([
                'code'=>0,
            ]);
        }
        $captcha = $this->request->post('captcha');
        Log::error('码商：'.$this->agent['username'].'代付刷新超出限制，提交验证码：'.$captcha);
//        action_log('码商刷单','码商： '.$this->agent['username'].'代付刷新超出限制，提交验证码： '.$captcha);
        if(!captcha_check($captcha)){
            //验证失败
            return json([
                'code'=>0,
            ]);
        }else{
            Cache::set('getDaifuOrderList_ver_copt'.$this->agent_id,0);
            Cache::set('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date'],0);
            Cache::set('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date'].'time',time()+120);
//            $daojishi = getAdminPayCodeSys('ms_shuadan_daojishi',256,$this->agent['admin_id']);
//            if (empty($daojishi) || $daojishi < 0){
//                $daojishi = 90;
//            }
//            Cache::set('getDaifuOrderList_daojishi'.$this->agent_id.$this->agent['reg_date'],time()+$daojishi,$daojishi);
            return json([
                'code'=>1,
            ]);
        }
    }


    public function getDaifuOrderList(Request $request){
        if ($this->agent['is_daifu'] != 1){
//            return '你没有代付权限，请联系管理员！';
            return json([
                'code'=>1,
                'msg'=>'你没有代付权限，请联系管理员！'
            ]);
        }
//        $daojishi = Cache::get('getDaifuOrderList_daojishi'.$this->agent_id.$this->agent['reg_date']);
//        if ($daojishi){
//            if (($daojishi - time()) > 1){
//                return json([
//                    'code'=>1,
//                    'msg'=>' 存在刷单行为已经被限制，请'.($daojishi - time()).'秒后重试！'
//                ]);
//            }
//        }

        $daifu_refresh_isver = getAdminPayCodeSys('daifu_refresh_isver',255,$this->agent['admin_id']);
        if (empty($daifu_refresh_isver)){
            $daifu_refresh_isver = 1;
        }

        if ($daifu_refresh_isver == 1){
            $refreshs = getAdminPayCodeSys('daifu_refresh_frequency',255,$this->agent['admin_id']);
            if (empty($refreshs) && $refreshs != 0){
                $refreshs = 120;
            }
            if (!empty($refreshs) && $refreshs > 0){
                $heimingdan = Cache::get('getDaifuOrderList'.$this->agent_id);
                if ($heimingdan){
                    if (($heimingdan - time()) > 0){
                        return json([
                            'code'=>1,
//                    'msg'=>'请输入验证码！'
                            'msg'=>'监测到该用户存在刷单外挂行为，请减少页面刷新频率，'.($heimingdan - time()).'秒后解除限制,如果再次检测到会增加惩罚时间'
                        ]);
                    }
                    Cache::rm('getDaifuOrderList'.$this->agent_id);
                    Cache::set('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date'],0);
                    Cache::set('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date'].'time',time()+120);
                }

//                $lastcount = Cache::get('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date']);
//                if ($lastcount >= $refreshs){
////                Cache::set('getDaifuOrderList'.$this->agent_id,1);
//                    Cache::set('getDaifuOrderList'.$this->agent_id,time()+60);
//                    action_log('码商刷单','码商： '.$this->agent['username'].'刷新代付订单频率超出，存在使用外挂行为');
//                    return json([
//                        'code'=>1,
//                        'msg'=>'接口频繁，请刷新重试！'
//                    ]);
//                }
                $lastcount = Cache::get('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date']);
                $lastcounttime = Cache::get('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date'].'time');
                if (($lastcounttime - time()) > 0){
                    if ($lastcount >= $refreshs){
//                Cache::set('getDaifuOrderList'.$this->agent_id,1);
                        Cache::inc('getDaifuOrderList'.$this->agent_id.'count');
                        $jinzhicount = Cache::get('getDaifuOrderList'.$this->agent_id.'count');
                        if ($jinzhicount == 1){
                            Cache::set('getDaifuOrderList'.$this->agent_id,time()+60);
                        }else{
                            Cache::set('getDaifuOrderList'.$this->agent_id,time()+($jinzhicount-1)*5*60);
                        }
//                        Cache::set('getDaifuOrderList'.$this->agent_id,time()+60);
                        action_log('码商刷单','码商： '.$this->agent['username'].'刷新代付订单频率超出，存在使用外挂行为');
                        return json([
                            'code'=>1,
                            'msg'=>'接口频繁，请刷新重试！'
                        ]);
                    }
                }else{
                    Cache::set('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date'],0);
                    Cache::set('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date'].'time',time()+120);
                }

                if (!$lastcount){
                    Cache::set('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date'],$lastcount+1,120);
                }else{
                    Cache::inc('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date']);
                }
            }
        }else{
            $refreshs = getAdminPayCodeSys('daifu_refresh_frequency',255,$this->agent['admin_id']);
            if (empty($refreshs) && $refreshs != 0){
                $refreshs = 120;
            }
            if (!empty($refreshs) && $refreshs > 0){
                $heimingdan = Cache::get('getDaifuOrderList_ver_copt'.$this->agent_id);
                if ($heimingdan == 1){
                    return json([
                        'code'=>1,
                        'msg'=>'请输入验证码！'
//                            'msg'=>'监测到该用户存在刷单外挂行为，请减少页面刷新频率，'.($heimingdan - time()).'秒后解除限制,如果再次检测到会增加惩罚时间'
                    ]);
//                    if (($heimingdan - time()) > 0){
//                        return json([
//                            'code'=>1,
//                             'msg'=>'请输入验证码！'
////                            'msg'=>'监测到该用户存在刷单外挂行为，请减少页面刷新频率，'.($heimingdan - time()).'秒后解除限制,如果再次检测到会增加惩罚时间'
//                        ]);
//                    }
//                    Cache::rm('getDaifuOrderList'.$this->agent_id);
//                    Cache::set('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date'],0);
                }

                $lastcount = Cache::get('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date']);
                $lastcounttime = Cache::get('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date'].'time');
                if (($lastcounttime - time()) > 0){
                    if ($lastcount >= $refreshs){
//                Cache::set('getDaifuOrderList'.$this->agent_id,1);
                        Cache::set('getDaifuOrderList_ver_copt'.$this->agent_id,1);
                        action_log('码商刷单','码商： '.$this->agent['username'].'刷新代付订单频率超出，存在使用外挂行为');
                        return json([
                            'code'=>1,
                            'msg'=>'接口频繁，请刷新重试！'
                        ]);
                    }
                }else{
                    Cache::set('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date'],0);
                    Cache::set('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date'].'time',time()+120);
                }

                if (!$lastcount){
                    Cache::set('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date'],$lastcount+1,120);
                }else{
                    Cache::inc('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date']);
                }

            }
//                    dump(Cache::get('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date']));die;
        }
//        dump(Cache::get('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date']));die;
        if (empty($request->param('member_id'))){
            return json([
                'code'=>1,
                'msg'=>'error！'
            ]);
        }
        if ($request->param('member_id') != $this->agent_id){
            return json([
                'code'=>1,
                'msg'=>'非法请求！'
            ]);
        }

//        Cache::set('getDaifuOrderList'.$this->agent_id,$refresh_frequency+1,60);
        $rsa_private_key = $this->modelConfig->where('name', 'rsa_private_key')->value('value');
        $rsa_public_key = $this->modelConfig->where('name', 'rsa_public_key')->value('value');
        $rsa = new RsaUtils($rsa_public_key, $rsa_private_key);

        $searchParams = [];
        if ($request->param('search')){
            $rsaRet = $rsa->decrypt($request->param('search'));
            if (empty($rsaRet)){
                return json([
                    'code'=>1,
                    'msg'=>'请求错误！'
                ]);
            }
            $searchParams = array_filter(json_decode($rsaRet,true), function ($arr){
                if($arr === '' || $arr === null){
                    return false;
                }
                return true;
            });
        }
        $where = [];
//        $orWhere = [];
        $startTime = '';
        $endTime = '';
        $where['create_time'] = $this->parseRequestDate3();;
        if (isset($searchParams['startDate']) && $searchParams['startDate']){
            $startTime = $searchParams['startDate'];
            $where['create_time'] = ['egt', strtotime($startTime)];
        }
        if (isset($searchParams['endDate']) && $searchParams['endDate']){
            $endTime = $searchParams['endDate'];
            $where['create_time'] = ['elt',strtotime($endTime)];
        }
        if ($startTime && $endTime) {
            $where['create_time'] = ['between', [strtotime($startTime), strtotime($endTime)]];
        }
//        $admin_sonUser = Db::name('user')->where('admin_id',$this->agent['admin_id'])->column('uid');
//        $where['uid'] = ['in',$admin_sonUser];


        if (isset($searchParams['trade_no']) && $searchParams['trade_no']){
            unset($where['create_time']);
            $where['trade_no'] = $searchParams['trade_no'];
        }

        if (isset($searchParams['out_trade_no']) && $searchParams['out_trade_no']){
            unset($where['create_time']);
            $where['out_trade_no'] = $searchParams['out_trade_no'];
        }
//        $status = $searchParams['status'] ??  -1;

//        if (empty($searchParams['out_trade_no'] ?? '')&&empty($searchParams['trade_no'] ?? '')&&empty($searchParams['startDate'] ?? '')
//            &&empty($searchParams['endDate'] ?? '')){
//            $where['status'] = array('in', '3,1');
//        }

//        if ($status != -1){
//            $where['status'] = $status;
//        }

//        if ($this->agent['min_money'] != 0){
//            $orWhere['amount'] = ['gt',$this->agent['min_money']];
//        }
//
//        if ($this->agent['max_money'] != 0){
//            $orWhere['amount'] = ['lt',$this->agent['max_money']];
//        }
//
//        if ($this->agent['min_money'] != 0 && $this->agent['max_money'] != 0){
//            $orWhere['amount'] = ['between',[$this->agent['min_money'],$this->agent['max_money']]];
//        }

       // $exp = new \think\Db\Expression('field(status,3,1,2,0)');
//        $query = $this->modelDaifuOrders
//                        ->where($where)
//                        ->orderRaw("FIELD(status, 3, 1, 2, 0), create_time asc, id asc");


        $where['ms_id'] = $this->agent_id;
        $where['is_lock'] = 0;
        $where['status'] = ['in',[0,2,3]];
        $status = $searchParams['status'] ??  -1;

        if ($status != -1){
            if (!in_array($status,[0,2,3])){
                $retObj = json_encode([
                    'code'=>1,
                    'msg'=>'状态异常'
                ]);
                $this->result(array(
                    'code' => 1,
                    'data' => $rsa->PublicEncrypt( $retObj)
                ));
            }
            $where['status'] = $status;
        }

//print_r($where);die;
        $query = $this->modelDaifuOrders->where($where)
                    ->orderRaw("FIELD(status, 3,2,0), create_time asc, id asc")
                    ->paginate($this->request->param('limit', 10));



//        $query = $query->paginate($this->request->param('limit', 10));
        

        if ($query->total() > 0){
            $daifu_orders_username = getAdminPayCodeSys('daifu_orders_username',255,$this->agent['admin_id']);
            if (empty($daifu_orders_username)){
                $daifu_orders_username = 2;
            }
            if ($daifu_orders_username == 1) {
                $userIds = array_column($query->items(), 'uid');
                $usernames = [];

                Db::name('user')->whereIn('uid', $userIds)->chunk(100, function ($users) use (&$usernames) {
                    foreach ($users as $user) {
                        $usernames[$user['uid']] = $user['username'];
                    }
                });

                foreach ($query->items() as $key => $val) {
                    $query->items()[$key]['username'] = $usernames[$val['uid']] ?? '';
                }
            }



            $retObj = json_encode([
                'code'=>0,
                'msg'=>'请求成功',
                'data'=>$query->items(),
                'count'=>$query->total(),
            ]);
        }else{
            $retObj = json_encode([
                'code'=>1,
                'msg'=>'暂无数据'
            ]);
        }


        return json(json_decode($retObj));

       /* $this->result(array(
            'code' => 1,
            'data' => $rsa->PublicEncrypt( $retObj)
        ));*/

    }


    public function getDaifuCommonOrderList(Request $request){
//        print_r($this->agent['is_daifu']);die;
        if ($this->agent['is_daifu'] != 1){
            return json([
                'code'=>1,
                'msg'=>'你没有代付权限，请联系管理员！'
            ]);
        }
//        $daojishi = Cache::get('getDaifuOrderList_daojishi'.$this->agent_id.$this->agent['reg_date']);
//        if ($daojishi){
//            if (($daojishi - time()) > 1){
//                return json([
//                    'code'=>1,
//                    'msg'=>' 存在刷单行为已经被限制，请'.($daojishi - time()).'秒后重试！'
//                ]);
//            }
//        }
        $daifu_refresh_isver = getAdminPayCodeSys('daifu_refresh_isver',255,$this->agent['admin_id']);
        if (empty($daifu_refresh_isver)){
            $daifu_refresh_isver = 1;
        }

        if ($daifu_refresh_isver == 1){
            $refreshs = getAdminPayCodeSys('daifu_refresh_frequency',255,$this->agent['admin_id']);
            if (empty($refreshs) && $refreshs != 0){
                $refreshs = 120;
            }
            if (!empty($refreshs) && $refreshs > 0){
                $heimingdan = Cache::get('getDaifuOrderList'.$this->agent_id);
                if ($heimingdan){
                    if (($heimingdan - time()) > 0){
                        return json([
                            'code'=>1,
//                    'msg'=>'请输入验证码！'
                            'msg'=>'监测到该用户存在刷单外挂行为，请减少页面刷新频率，'.($heimingdan - time()).'秒后解除限制,如果再次检测到会增加惩罚时间'
                        ]);
                    }
                    Cache::rm('getDaifuOrderList'.$this->agent_id);
                    Cache::set('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date'],0);
                    Cache::set('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date'].'time',time()+120);
                }

//                $lastcount = Cache::get('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date']);
//                if ($lastcount >= $refreshs){
////                Cache::set('getDaifuOrderList'.$this->agent_id,1);
//                    Cache::set('getDaifuOrderList'.$this->agent_id,time()+60);
//                    action_log('码商刷单','码商： '.$this->agent['username'].'刷新代付订单频率超出，存在使用外挂行为');
//                    return json([
//                        'code'=>1,
//                        'msg'=>'接口频繁，请刷新重试！'
//                    ]);
//                }
                $lastcount = Cache::get('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date']);
                $lastcounttime = Cache::get('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date'].'time');
                if (($lastcounttime - time()) > 0){
                    if ($lastcount >= $refreshs){
//                Cache::set('getDaifuOrderList'.$this->agent_id,1);
//                        $jinzhicount = Cache::get('getDaifuOrderList'.$this->agent_id.'count');
                        Cache::inc('getDaifuOrderList'.$this->agent_id.'count');
                        $jinzhicount = Cache::get('getDaifuOrderList'.$this->agent_id.'count');
                        if ($jinzhicount == 1){
                            Cache::set('getDaifuOrderList'.$this->agent_id,time()+60);
                        }else{
                            Cache::set('getDaifuOrderList'.$this->agent_id,time()+($jinzhicount-1)*5*60);
                        }

                        action_log('码商刷单','码商： '.$this->agent['username'].'刷新代付订单频率超出，存在使用外挂行为');
                        return json([
                            'code'=>1,
                            'msg'=>'接口频繁，请刷新重试！'
                        ]);
                    }
                }else{
                    Cache::set('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date'],0);
                    Cache::set('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date'].'time',time()+120);
                }

                if (!$lastcount){
                    Cache::set('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date'],$lastcount+1,120);
                }else{
                    Cache::inc('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date']);
                }
            }
        }else{
            $refreshs = getAdminPayCodeSys('daifu_refresh_frequency',255,$this->agent['admin_id']);
            if (empty($refreshs) && $refreshs != 0){
                $refreshs = 120;
            }
            if (!empty($refreshs) && $refreshs > 0){
                $heimingdan = Cache::get('getDaifuOrderList_ver_copt'.$this->agent_id);
                if ($heimingdan == 1){
                    return json([
                        'code'=>1,
                        'msg'=>'请输入验证码！'
//                            'msg'=>'监测到该用户存在刷单外挂行为，请减少页面刷新频率，'.($heimingdan - time()).'秒后解除限制,如果再次检测到会增加惩罚时间'
                    ]);
//                    if (($heimingdan - time()) > 0){
//                        return json([
//                            'code'=>1,
//                             'msg'=>'请输入验证码！'
////                            'msg'=>'监测到该用户存在刷单外挂行为，请减少页面刷新频率，'.($heimingdan - time()).'秒后解除限制,如果再次检测到会增加惩罚时间'
//                        ]);
//                    }
//                    Cache::rm('getDaifuOrderList'.$this->agent_id);
//                    Cache::set('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date'],0);
                }

                $lastcount = Cache::get('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date']);
                $lastcounttime = Cache::get('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date'].'time');
                if (($lastcounttime - time()) > 0){
                    if ($lastcount >= $refreshs){
//                Cache::set('getDaifuOrderList'.$this->agent_id,1);
                        Cache::set('getDaifuOrderList_ver_copt'.$this->agent_id,1);
                        action_log('码商刷单','码商： '.$this->agent['username'].'刷新代付订单频率超出，存在使用外挂行为');
                        return json([
                            'code'=>1,
                            'msg'=>'接口频繁，请刷新重试！'
                        ]);
                    }
                }else{
                    Cache::set('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date'],0);
                    Cache::set('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date'].'time',time()+120);
                }

                if (!$lastcount){
                    Cache::set('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date'],$lastcount+1,120);
                }else{
                    Cache::inc('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date']);
                }

            }
//                    dump(Cache::get('getDaifuOrderList'.$this->agent_id.$this->agent['reg_date']));die;
        }
        if (empty($request->param('member_id'))){
            return json([
                'code'=>1,
                'msg'=>'error！'
            ]);
        }
        if ($request->param('member_id') != $this->agent_id){
            return json([
                'code'=>1,
                'msg'=>'非法请求！'
            ]);
        }

//        Cache::set('getDaifuOrderList'.$this->agent_id,$refresh_frequency+1,60);
        $rsa_private_key = $this->modelConfig->where('name', 'rsa_private_key')->value('value');
        $rsa_public_key = $this->modelConfig->where('name', 'rsa_public_key')->value('value');
        $rsa = new RsaUtils($rsa_public_key, $rsa_private_key);

        $searchParams = [];
        if ($request->param('search')){
            $rsaRet = $rsa->decrypt($request->param('search'));
            if (empty($rsaRet)){
                return json([
                    'code'=>1,
                    'msg'=>'请求错误！'
                ]);
            }
            $searchParams = array_filter(json_decode($rsaRet,true), function ($arr){
                if($arr === '' || $arr === null){
                    return false;
                }
                return true;
            });
        }
        $where = [];
        $orWhere = [];

        // $startTime = strtotime(date('Y-m-d 00:00:00',time()));
        // $endTime =  strtotime(date('Y-m-d H:i:s',time()));
        $startTime = date('Y-m-d 00:00:00',time());
        $endTime =  date('Y-m-d H:i:s',time());
        $where['create_time'] = ['elt',time()];

        if (isset($searchParams['startDate']) && $searchParams['startDate']){
            $startTime = $searchParams['startDate'];
            $where['create_time'] = ['egt', strtotime($startTime)];
        }
        if (isset($searchParams['endDate']) && $searchParams['endDate']){
            $endTime = $searchParams['endDate'];
            $where['create_time'] = ['elt',strtotime($endTime)];
        }
        if ($startTime && $endTime) {
            $where['create_time'] = ['between', [strtotime($startTime), strtotime($endTime)]];
        }

        if (isset($searchParams['trade_no']) && $searchParams['trade_no']){
            unset($where['create_time']);
            $where['trade_no'] = $searchParams['trade_no'];
        }

        if (isset($searchParams['out_trade_no']) && $searchParams['out_trade_no']){
            unset($where['create_time']);
            $where['out_trade_no'] = $searchParams['out_trade_no'];
        }

//        $admin_sonUser = Db::name('user')->where('admin_id',$this->agent['admin_id'])->column('uid');
        $where['admin_id'] = $this->agent['admin_id'];
//        $where['ms_id'] = $this->agent_id;
        $where['is_lock'] = 0;
        $where['status'] = 1;

        $ms_channel = Db::name('ms_channel')->where(['ms_id' => $this->agent_id, 'pay_code_id' => 255])->find();
        $ms_channel['status'] = $ms_channel['status'] ?? 1;
        $ms_channel['min_money'] = $ms_channel['min_money'] ?? '0.00';
        $ms_channel['max_money'] = $ms_channel['max_money'] ?? '0.00';

        if ($ms_channel['status'] == 0){
            $retObj = json_encode([
                'code'=>1,
                'msg'=>'暂无数据'
            ]);

            $this->result(array(
                'code' => 1,
                'data' => $rsa->PublicEncrypt($retObj)
            ));
        }

        if ($ms_channel['min_money'] != 0 && $ms_channel['max_money'] != 0) {
            // 添加金额区间查询条件
            $where['amount'] = ['between', [$ms_channel['min_money'], $ms_channel['max_money']]];
        } elseif ($ms_channel['min_money'] != 0) {
            // 添加最小金额条件
            $where['amount'] = ['egt', $ms_channel['min_money']];
        } elseif ($ms_channel['max_money'] != 0) {
            // 添加最大金额条件
            $where['amount'] = ['elt', $ms_channel['max_money']];
        }

//        print_r($ms_channel);die;
        $ms_daifu_show10 = getAdminPayCodeSys('ms_daifu_show10',256,$this->agent['admin_id']);
        if (empty($ms_daifu_show10)){
            $ms_daifu_show10 = 2;
        }
        if ($ms_daifu_show10 == 2){
            $query = $this->modelDaifuOrders
                ->where($where)
                ->order('id asc')
//                ->whereTime('create_time', 'today')
//                ->cache(true)
                ->paginate($this->request->param('limit', 10));



            if ($query->total() > 0){
                $daifu_orders_username = getAdminPayCodeSys('daifu_orders_username',255,$this->agent['admin_id']);
                if (empty($daifu_orders_username)){
                    $daifu_orders_username = 2;
                }
                if ($daifu_orders_username == 1) {
                    $userIds = array_column($query->items(), 'uid');
                    $usernames = [];

                    Db::name('user')->whereIn('uid', $userIds)->chunk(100, function ($users) use (&$usernames) {
                        foreach ($users as $user) {
                            $usernames[$user['uid']] = $user['username'];
                        }
                    });

                    foreach ($query->items() as $key => $val) {
                        $query->items()[$key]['username'] = $usernames[$val['uid']] ?? '';
                    }
                }



                $retObj = json_encode([
                    'code'=>0,
                    'msg'=>'请求成功',
                    'data'=>$query->items(),
                    'count'=>$query->total(),
                ]);
            }else{
                $retObj = json_encode([
                    'code'=>1,
                    'msg'=>'暂无数据'
                ]);
            }
        }else{
            $query = $this->modelDaifuOrders->where($where)
                ->order("id asc")
                ->limit(10)
                ->select();

            if (count($query) > 0){
                $daifu_orders_username = getAdminPayCodeSys('daifu_orders_username',255,$this->agent['admin_id']);
                if (empty($daifu_orders_username)){
                    $daifu_orders_username = 2;
                }
                if ($daifu_orders_username == 1) {
                    $userIds = array_column($query, 'uid');
                    $usernames = [];

                    Db::name('user')->whereIn('uid', $userIds)->chunk(100, function ($users) use (&$usernames) {
                        foreach ($users as $user) {
                            $usernames[$user['uid']] = $user['username'];
                        }
                    });

                    foreach ($query as $key => $val) {
                        $query[$key]['username'] = $usernames[$val['uid']] ?? '';
                    }
                }



                $retObj = json_encode([
                    'code'=>0,
                    'msg'=>'请求成功',
                    'data'=>$query,
                    'count'=>count($query),
                ]);
            }else{
                $retObj = json_encode([
                    'code'=>1,
                    'msg'=>'暂无数据'
                ]);
            }
        }


        return json(json_decode($retObj,true));

        /*$this->result(array(
            'code' => 1,
            'data' => $rsa->PublicEncrypt( $retObj)
        ));*/
    }










    /**
     * 获取错误注释
     */

    public function getErrorReson(){
        $daifu_logic = new \app\common\logic\Config();
        $daifu_err_reason = $daifu_logic->getConfigInfo(['name'=> 'daifu_err_reason']);
        $res = explode(",",$daifu_err_reason['value']);
        return json(['code'=>0,'data'=>$res]);
    }



    /**
     * 弃单
     */

    public function discard_df(){
        if ($this->request->isPost()){
            $id = $this->request->param('id');
            if (!$id) {
                return json(['code'=>0,'msg'=>'非法操作']);
            }

            $uid = Db::name('daifu_orders')->where('id',$id)->value('uid');
            $u_admin_id = Db::name('user')->where('uid',$uid)->value('admin_id');
            if ($this->agent['admin_id'] != $u_admin_id){
                return json(['code'=>0,'msg'=>'非法操作1']);
            }
//            $daifu_success_uplode = getAdminPayCodeSys('daifu_success_uplode',255,$this->agent['admin_id']);
//            if (empty($daifu_success_uplode)){
//                $is_uplode = false;
//            }elseif($daifu_success_uplode == 2){
//                $is_uplode = true;
//            }else{
//                $is_uplode = false;
//            }
//            if ($is_uplode) {
//                $transfer_chart = Db::name('daifu_orders')->where('id', $id)->value('transfer_chart');
//                if (!empty($transfer_chart)) {
//                    return json(['code'=>0,'msg'=>'您已经上传过支付凭证，不可弃单']);
//                }
//            }
            $this->modelDaifuOrders->startTrans();
            try {
                $transfer = $this->modelDaifuOrders->lock(true)->where(['id' => $id, 'status'=>3,'ms_id'=>$this->agent_id])->find();
                if (!$transfer) {
                    throw new Exception('订单不存在或者订单已处理，请刷新！');
                }
                $ms_3min_qidan = getAdminPayCodeSys('ms_3min_qidan',256,$this->agent['admin_id']);
                if (empty($ms_3min_qidan)){
                    $ms_3min_qidan = 2;
                }
                if ($ms_3min_qidan == 1){
                    $is_qidan = Cache::get($transfer['out_trade_no'].'_not_qidan');
                    if ($is_qidan){
                        throw new Exception('抢单后3分钟内不允许弃单！');
                    }
                }

                $transfer->status = 1;
                $transfer->ms_id = 0;
                $transfer->transfer_chart = '';
                $transfer->save();
                $this->modelDaifuOrders->commit();
                Log::notice('码商'.$this->agent['username'].'丢弃代付订单：'.$transfer['out_trade_no']);
                action_log('代付弃单','码商'.$this->agent['username'].'丢弃代付订单：'.$transfer['out_trade_no']);
                return json(['code'=>1,'msg'=>'成功弃单']);
            } catch (Exception $e) {
                $this->modelDaifuOrders->rollback();
                return json(['code'=>0,'msg'=>$e->getMessage()]);
            }
        }

        return json(['code'=>0,'msg'=>'非法操作']);
    }

    /**
     * 匹配订单
     */
    public function matching()
    {
        if ($this->agent['is_daifu'] != 1){
            return json([
                'code'=>0,
                'msg'=>'你没有代付权限，请联系管理员！'
            ]);
        }
        if ($this->request->isPost()){
            $id = $this->request->post('id');
            if (!$id) {
                return json(['code'=>0,'msg'=>'非法操作']);
            }

            $cache = Cache::get('matching'.$this->agent_id);
            if (!empty($cache) && (time()-$cache) < 15){
//            $this->error('频率过高，请稍后再来');
            }

            Cache::set('matching'.$this->agent_id,time());

//            $uid = Db::name('daifu_orders')->where('id',$id)->find();
//            $u_admin_id = Db::name('user')->where('uid',$uid['uid'])->value('admin_id');
//            if ($this->agent['admin_id'] != $u_admin_id){
//                return json(['code'=>0,'msg'=>'非法操作1']);
//            }
            Db::startTrans();
            try {
                $team = getTeamPid($this->agent_id);
                foreach ($team as $v){
                    $team_work_status = Db::name('ms')->where('userid',$v)->value('team_work_status');
                    if ($team_work_status == 0){
                        Db::rollback();
                        throw new Exception('所属团队未开启接单！');
                    }
                    continue;
                }

                $ms = Db::name('ms')->where('userid',$this->agent_id)->find();

                if ($ms['status'] == 0){
                    Db::rollback();
                    throw new Exception('你的状态已被禁用，请联系管理员处理');
                }
                /*if ($ms['work_status'] == 0){
                    Db::rollback();
                    throw new Exception('你正处于未开工状态，如需抢单请打开开工按钮');
                }*/

                if ($ms['is_daifu'] != 1){
                    Db::rollback();
                    throw new Exception('你已被禁止接单，请联系管理员！');
                }

                $transfer = $this->modelDaifuOrders->lock(true)->where(['id' => $id,'admin_id'=>$this->agent['admin_id']])->find();

                $ms_channel = Db::name('ms_channel')->where(['ms_id' => $this->agent_id, 'pay_code_id' => '255'])->find();
                $ms_channel['status'] = $ms_channel['status'] ?? 1;
                $ms_channel['min_money'] = $ms_channel['min_money'] ?? '0.00';
                $ms_channel['max_money'] = $ms_channel['max_money'] ?? '0.00';

                $amountCheckRet = true;
                if ($this->shouldRemoveMsId($ms, $ms_channel, $transfer)) {
                    $amountCheckRet = false;
                }



                if (!$amountCheckRet){
                    Db::rollback();
                    return json(['code'=>0,'msg'=>'该订单金额不允许！']);
                }

                if (!$transfer|| $transfer['status']!=1) {
                    Db::rollback();
                    throw new Exception('订单不存在或者订单已处理，请刷新！');
                }
                if ($transfer['is_lock']==1) {
                    Db::rollback();
                    throw new Exception('该订单已被锁定，请刷新！');
                }
                $cache = Cache::get('matching'.$this->agent_id);
                if (!empty($cache) && (time()-$cache) < 2){
                    //   $this->error('▒~Q▒~N~G▒~G▒~X▒~L请▒~M▒~P~N▒~F~M▒~]▒');
                }
                Cache::set('matching'.$this->agent_id,time());

                $status3 = $this->modelDaifuOrders->where(['status'=>3,'ms_id'=>$this->agent_id,'admin_id'=>$this->agent['admin_id']])->count('id');
                $admin_daifu_number = Db::name('config')->where(['name'=>'admin_daifu_number','admin_id'=>$this->agent['admin_id']])->value('value');
                if (empty($admin_daifu_number)){
                    if ($status3 > 2){
                        Db::rollback();
                        throw new Exception('正在处理订单已到最大值，请处理后再抢单！');
                    }
                }else{
                    if ($status3 >= $admin_daifu_number){
                        Db::rollback();
                        throw new Exception('正在处理订单已到最大值，请处理后再抢单！');
                    }
                }
                $transfer->status = 3;
                $transfer->ms_id = $this->agent_id;
                $res = $transfer->save();
                if ($res){
                    Db::commit();
                    Log::notice('码商'.$this->agent['username'].'抢到代付订单：'.$transfer['out_trade_no']);
                    action_log('代付抢单','码商'.$this->agent['username'].'抢到代付订单：'.$transfer['out_trade_no']);
                    $ms_3min_qidan = getAdminPayCodeSys('ms_3min_qidan',256,$this->agent['admin_id']);
                    if (empty($ms_3min_qidan)){
                        $ms_3min_qidan = 2;
                    }
                    if ($ms_3min_qidan == 1){
                        Cache::set($transfer['out_trade_no'].'_not_qidan',1,180);
                    }
                    return json(['code'=>1,'msg'=>'请求成功']);
                }
            } catch (Exception $e) {
                Db::rollback();
                return json(['code'=>0,'msg'=>$e->getMessage()]);
            }
        }
        return json(['code'=>0,'msg'=>'非法操作']);
    }


    private function shouldRemoveMsId($ms, $ms_channel, $data) {
        return ($ms_channel['min_money'] > 0 && $data['amount'] < $ms_channel['min_money'])
            || ($ms_channel['max_money'] > 0 && $data['amount'] > $ms_channel['max_money'])
            || ($ms_channel['status'] == 0);
    }
    /**
     * 码商修改代付结果
     * @return mixed
     */
    public function sendDfResult(Request $request)
    {
        if ($this->agent['is_daifu'] != 1){
            return json([
                'code'=>0,
                'msg'=>'你没有代付权限，请联系管理员！'
            ]);
        }
        if ($request->isPost()){
            $id = $this->request->param('id');
//            $uid = Db::name('daifu_orders')->where('id',$id)->value('uid');
//            $ms_id = Db::name('daifu_orders')->where('id',$id)->value('ms_id');
//            if ($ms_id != $this->agent_id){
//                return json(['code'=>0,'msg'=>'该订单已被处理,请刷新']);
//            }
//            $u_admin_id = Db::name('user')->where('uid',$uid)->value('admin_id');
//            $admin_id = Db::name('ms')->where('userid',$this->agent_id)->value('admin_id');
//            if ($admin_id != $u_admin_id){
//                return json(['code'=>0,'msg'=>'非法操作']);
//            }
            //代付银行卡
//            $df_bank_id = $this->request->param('df_bank_id');
            $status = $this->request->param('status');
            $remark = $this->request->param('remark');
            $error_reason = $this->request->param('error_reason', '');
//            print_r($error_reason);die;
            if (!$id || !in_array($status, [0, 2])) {
                return json(['code'=>0,'msg'=>'非法操作']);
            }
            Db::startTrans();
            $DaifuOrdersLogic = new \app\common\logic\DaifuOrders();
            try {
                $orders = $this->modelDaifuOrders->lock(true)->where(['id' => $id,'ms_id'=>$this->agent_id,'admin_id'=>$this->agent['admin_id']])->find();
                if (!$orders) {
                    throw new Exception('订单不存在');
                }
                if ($orders['ms_id'] != $this->agent_id) {
                    throw new Exception('该订单已被处理，请刷新');
                }
                //更新订单的代付银行卡
                $up['finish_time'] = time();
//            $up['df_bank_id'] = $df_bank_id;
                $up['error_reason'] = $error_reason;

                $res = $this->modelDaifuOrders->where(['id' => $id,'ms_id'=>$this->agent_id,'admin_id'=>$this->agent['admin_id']])->update($up);


                if ($status == 2) {
                    $daifu_success_uplode = getAdminPayCodeSys('daifu_success_uplode',255,$this->agent['admin_id']);
                    if (empty($daifu_success_uplode)){
                        $daifu_success_uplode = 1;
                    }
                    if ($daifu_success_uplode == 2){
                        $daifu_auto_uplode = getAdminPayCodeSys('daifu_auto_uplode',255,$this->agent['admin_id']);
                        if (empty($daifu_auto_uplode)){
                            $daifu_auto_uplode = 1;
                        }
                        if ($daifu_auto_uplode == 2){
                            $transfer_chart = Db::name('daifu_orders')->where('id',$id)->value('transfer_chart');
                            if (empty($transfer_chart)){
                                throw new Exception('请先上传转账截图');
                            }
                        }

                    }
                    Db::name('daifu_orders')->where('id',$id)->update(['remark'=>$remark]);
                    Log::notice('码商：'.$this->agent_id.' 成功完成代付订单：'.$orders['out_trade_no']);
                    action_log('代付成功','码商'.$this->agent['username'].'成功完成代付订单：'.$orders['out_trade_no']);
                    $result = $DaifuOrdersLogic->successOrder($orders['id']);
                } else {
                    Log::notice('码商：'.$this->agent_id.' 进行代付失败操作，订单：'.$orders['out_trade_no']);
                    action_log('代付失败','码商'.$this->agent['username'].'代付失败订单：'.$orders['out_trade_no']);
                    $result = $DaifuOrdersLogic->errorOrder($orders['id']);
                }

                if ($res === false) {
                    throw new Exception('代付失败');
                }

                Db::commit();
                $notify_status= Db::name('api')->where('uid',$orders['uid'])->value('is_notify_status');
                if ($notify_status != 1){
                    $this->modelDaifuOrders->where('id',$orders['id'])->update(['notify_result'=>'SUCCESS']);
                }else{
                    $status = ($status == 2) ? true : false;
                    $DaifuOrdersLogic->retryNotify($id,$status);
                }

                return $result;
            } catch (Exception $e) {
                Db::rollback();
                return json(['code'=>0,'msg'=>$e->getMessage()]);
            }
        }
        return json(['code'=>0,'msg'=>'非法操作']);

    }


    public function daifu_order_refresh(){
        $status = $this->request->param('status');
        if ($status == 1){
            Cookie::set('daifu_orders_refresh',1);
        }else{
            Cookie::set('daifu_orders_refresh',0);
        }
    }


    /**
     * 获取当前码商对应的商户的最新订单
     */
    public function lastOrder(\app\common\logic\User $user)
    {
        $uids = $user->getUsersByMsId($this->agent_id);
        $uids = collection($uids)->column('uid');
        $lastOrderId = \app\common\model\DaifuOrders::where(['uid' => ['in', $uids]])->order(['id' => 'desc'])->value('id');
        echo $lastOrderId;
    }


    /**
     * 导出代付订单
     * @param Request $request
     * @return mixed
     * @throws Exception
     * @throws \think\exception\DbException
     */
    public function exportDfOrder(Request $request)
    {
        $where = [];
        //当前时间段统计

        $startTime = $request->param('start_time');
        //dd($startTime);
        $endTime = $request->param('end_time');
        if ($startTime && empty($endTime)) {
            $where['a.create_time'] = ['egt', strtotime($startTime)];
        }
        if (empty($startTime) && $endTime) {
            $where['a.create_time'] = ['elt', strtotime($endTime)];
        }
        if ($startTime && $endTime) {
            $where['a.create_time'] = ['between', [strtotime($startTime), strtotime($endTime)]];
        }

        $status = $this->request->param('status', 1);
        $status != -1 && $where['a.status'] = ['eq', $status];

        !empty($this->request->param('trade_no')) && $where['a.trade_no']
            = ['eq', $this->request->param('trade_no')];
        !empty($this->request->param('out_trade_no')) && $where['a.out_trade_no']
            = ['eq', $this->request->param('out_trade_no')];
        //组合搜索
        !empty($this->request->param('uid')) && $where['a.uid']
            = ['eq', $this->request->param('uid')];
        $admin_id = Db::name('ms')->where('userid',$this->agent_id)->value('admin_id');

        $adminSonUser = Db::name('user')->where('admin_id',$admin_id)->column('uid');
        $where['a.uid'] = ['in',$adminSonUser];

        $fields = ['a.*', 'b.pao_ms_ids', 'c.username', 'bank_account_username', 'bank_account_number', 'e.enable'];
        $query = $this->modelDaifuOrders->alias('a')
            ->join('user b', 'a.uid=b.uid', 'left')
            ->join('ms c', 'a.ms_id=c.userid', 'left')
            ->join('deposite_card d', 'a.df_bank_id=d.id', 'left')
            ->join('cm_balance e', 'a.uid=e.uid', 'left')
            ->field($fields)
            ->order('id desc')
            ->where($where)
            ->where(function ($query) {
                $query->whereOr("IF (a.ms_id!=0,a.ms_id = {$this->agent->userid},(find_in_set( {$this->agent->userid}, pao_ms_ids )  or pao_ms_ids=''))");
            });

        $listData = $query->select();

        //组装header 响应html为execl 感觉比PHPExcel类更快
        $orderStatus = ['处理失败', '待处理', '已完成'];


        $strTable = '<table width="500" border="1">';
        $strTable .= '<tr>';
        $strTable .= '<td style="text-align:center;font-size:12px;width:120px;">ID标识</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="100">订单编号(商户)</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">商户UID</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">商户余额</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">金额</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">收款信息</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">付款信息</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">状态</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">失败原因</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">创建时间</td>';
        $strTable .= '<td style="text-align:center;font-size:12px;" width="*">完成时间</td>';
        $strTable .= '</tr>';

        if ($listData) {
            foreach ($listData as $k => $val) {
                $skAccountInfo = '姓名:' . $val['bank_owner'] . ' 银行:' . $val['bank_name'] . ' 卡号:' . $val['bank_number'];
                $payAccountInfo = '---';
                if ($val['bank_id']) {
                    $payAccountInfo = '转账银行卡ID:' . $val['bank_id'] . ' 姓名:' . $val['bank_account_username'] . ' 卡号:' . $val['bank_account_number'];
                }
                $val['finish_time'] = $val['finish_time']?date("Y-m-d H:i:s",$val['finish_time']):'---';
                $strTable .= '<tr>';
                $strTable .= '<td style="text-align:center;font-size:12px;">&nbsp;' . $val['id'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['out_trade_no'] . ' </td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['uid'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['enable'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['amount'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $skAccountInfo . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $payAccountInfo . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $orderStatus[$val['status']] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['error_reason'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['create_time'] . '</td>';
                $strTable .= '<td style="text-align:left;font-size:12px;">' . $val['finish_time'] . '</td>';
                $strTable .= '</tr>';
                unset($listData[$k]);
            }
        }
        $strTable .= '</table>';
        downloadExcel($strTable, 'daifu_orders_execl');
    }

    public function decodeImagePath()
    {
        $imagePath = $this->request->param('image_path');

        if (empty($imagePath)) {
            $this->error('图片路径不能为空');
        }

        $imagePath = openssl_decrypt(base64_decode($imagePath), "AES-128-CBC", '8e70f72bc3f53b12', true, '99b538370c7729c7');

        if (empty($imagePath)) {
            $this->error('图片路径解密失败');
        }

        $this->success('图片路径解密成功', '', $imagePath);

    }
}
