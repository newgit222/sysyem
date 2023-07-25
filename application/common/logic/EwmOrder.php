<?php
namespace app\common\logic;


use app\admin\model\ShopOrderModel;
use app\agent\model\ShopAccountModel;
use app\common\library\enum\CodeEnum;
use app\common\library\enum\OrderStatusEnum;
use app\common\model\Admin;
use app\common\model\Config;
use app\common\model\EwmPayCode;
use app\common\model\GemapayOrderModel;
use app\common\model\OrdersNotify;
use app\ms\Logic\SecurityLogic;
use GuzzleHttp\Client;
use think\Cache;
use think\Db;
use think\Log;
use function GuzzleHttp\Psr7\uri_for;

/**
 * 二维码订单逻辑处理
 * Class EwmOrder
 * @package app\common\logic
 */
class EwmOrder extends BaseLogic
{
    /**
     * @param $array
     * @param $keys
     * @param int $sort
     * @return mixed
     * 二维数组排序
     */
    public function arraySort($array, $keys, $sort = SORT_DESC)
    {
        $keysValue = [];
        foreach ($array as $k => $v) {
            $keysValue[$k] = $v[$keys];
        }
        array_multisort($keysValue, $sort, $array);
        return $array;
    }


    /**
     * 获取可以使用的二维码
     * @param $money
     * @param $codeType
     * @param $ip
     * @param $admin_id
     * @return array|bool
     */
    public function getGoodCodeV5($money, $codeType, $ip, $member_id,$admin_id=0)
    {
        $EwmOrderModel = new \app\common\model\EwmOrder();
        $GemapayCode = new EwmPayCode();
//        $gemapayOrderLogic = new \app\common\logic\EwmOrder();
//            所有金额
            $codeInfos = $GemapayCode->getRandomCodeV3($codeType, $member_id,$money);
            if ($codeInfos === false) {
                $area = '没有可用二维码（未找到可用收款码）';
                return [$money, false,null, $area];
            }
            $codeInfos = array_unique($codeInfos);
//            去掉最大金额，最小金额，去掉日接单次数，单日总金额
            foreach ($codeInfos as $k=>$v){
                //过期时间
                if (!empty($v['expiration_time'] )&&  $v['expiration_time'] != 0){
                    if (time() >=  $v['expiration_time']){
                        unset($codeInfos[$k]);
                        continue;
                    }
                }
                //订单笔数限制
                if ($v['success_order_num'] != 0){
//                    $code_num = $EwmOrderModel->where('code_id',$v['id'])->whereTime('add_time', 'today')->count('id');
                    if ($v['today_receiving_number'] >= $v['success_order_num']){
                        unset($codeInfos[$k]);
                        continue;
                    }
                }
                //日限额
                if($v['limit__total'] != 0) {
//                    $money_today = $EwmOrderModel->where(['code_id'=>$v['id'],'status'=>1])->whereTime('add_time', 'today')->sum('order_pay_price');
                    if( $money+$v['today_receiving_amount'] > $v['limit__total'] ){
                        unset($codeInfos[$k]);
                        continue;
                    }
                }

            }
            if (empty($codeInfos)){
                $area = '没有可用二维码（超出收款限制）';
                return [$money, false,null, $area];
            }
            $amount_lock = getAdminPayCodeSys('order_invalid_time',$codeType,$admin_id);
            if (empty($amount_lock)){
                $amount_lock_time = 600;
            }else{
                $amount_lock_time = $amount_lock * 60 + 10;
            }
        $is_amount_lock = getAdminPayCodeSys('is_amount_lock',$codeType,$admin_id);
        if (empty($is_amount_lock)){
            $is_amount_lock = 1;
        }

        if ($is_amount_lock == 1){
            $OrderData = $EwmOrderModel
                ->where('gema_userid','in',$member_id)
                ->where('add_time','>',time() - $amount_lock_time)
                ->where('status',0)
                ->where('code_type',$codeType)
                ->field('code_id,order_price,add_time')
                ->select();


            foreach ($OrderData as $p){
                foreach ($codeInfos as $key=>$val){
                    if ($p['order_price']== $money && $p['code_id'] == $val['id']){
                        unset($codeInfos[$key]);
                        continue;
                    }
                }
            }
            if (empty($codeInfos)){
                $area = '没有可用二维码（无可用金额）';
                return [$money, false,null, $area];
            }
        }

//            $reallPayMoney = $money;
        $userIds = [];
        foreach ($codeInfos as $code) {
            $userIds[] = $code['ms_id'];
        }
        //去重
        $userIds = array_unique($userIds);
        $MsCaceQuQue = new MsCacheQueue($codeType.$admin_id."_last_userids");
//        $MsCaceQuQue->init();
        $mssWeight =  $this->modelMs->where('userid', 'in',$userIds)->field('userid,weight')->select();
        $weightUserIds = [];
        foreach ($mssWeight as $msWeight){
            if (!is_numeric($msWeight['weight'])){
                $msWeight['weight'] = 1;
            }
            $msWeight['weight'] = $msWeight['weight'] > 30 ? 30 : $msWeight['weight'];
            $weightUserIds = array_merge(array_fill(0, $msWeight['weight'], $msWeight['userid']), $weightUserIds);
        }

        $lastUserId = $MsCaceQuQue->get_data($weightUserIds);
        //这里按照正序排序
        $codeInfos = $this->arraySort($codeInfos, 'id', SORT_ASC);
        foreach ($codeInfos as $k=>$v){
            if ($v['ms_id'] != $lastUserId){
                unset($codeInfos[$k]);
            }
        }
        $codeInfos = array_column($codeInfos,'id');
        $PayCodeQueue = new PayCodeQueue($codeType.$admin_id."_last_userid_codeids_" . $lastUserId);
//        $PayCodeQueue->init();
        $codeInfo = $PayCodeQueue->get_data($codeInfos);

        $codeInfo  = Db::name('ewm_pay_code')->where('id',$codeInfo)->find();
        return [$codeInfo['api_pay_money'], $codeInfo, null,'匹配成功'];
    }



    /**
     * 获取可以使用的二维码
     * @param $money
     * @param $codeType
     * @param $ip
     * @param $admin_id
     * @return array|bool
     */
    public function getGoodCodeV3($money, $codeType, $ip, $member_id,$admin_id=0)
    {
        $area = '';
        $EwmOrderModel = new \app\common\model\EwmOrder();
        $GemapayCode = new EwmPayCode();
        $gemapayOrderLogic = new EwmOrder();
        //判断金额是否浮动
        $isAllowPointStatus = getAdminPayCodeSys('money_is_float',$codeType,$admin_id);
        if (empty($isAllowPointStatus)){
            $isAllowPoint = true;
        }else if ($isAllowPointStatus == 1){
            $isAllowPoint = true;
        }else{
            $isAllowPoint = false;
        }
//        $isAllowPoint = true;
        //如果匹配不到整数,去匹配小数点
        if ($isAllowPoint) {
//            $member_id 为所有可用码商
//            所有金额
            Log::error(date('Y-m-d H:i:s',time()).':查询所有金额开始');
            $payPrices = $gemapayOrderLogic->getAvaibleMoneys($money,$codeType,$admin_id);
            Log::error(date('Y-m-d H:i:s',time()).':查询所有金额结束');
            //判断通道是否为固定金额
            Log::error(date('Y-m-d H:i:s',time()).':查询可用码子开始');
            if ($codeType == 38){
                $codeInfos = $GemapayCode->getAlipayPinMoneyCodeV2($codeType, $member_id);
            }else{
                $codeInfos = $GemapayCode->getRandomCodeV2($codeType, $member_id);
            }

            Log::error(date('Y-m-d H:i:s',time()).':查询可用码子结束');
            if ($codeInfos === false) {
                $area = '没有可用的收款码（未找到可用收款码）';
                return [$money, false,null, $area];
            }
//            Log::error(date('Y-m-d H:i:s',time()).':去重二维码开始');
//            $codeInfos = array_unique($codeInfos);
//            $uniqueArray = [];
//            foreach ($codeInfos as $codeInfo) {
//                $uniqueArray[$codeInfo['id']] = $codeInfo;
//            }
//            $codeInfos = array_values($uniqueArray);
//            Log::error(date('Y-m-d H:i:s',time()).':去重二维码结束');
//            去掉最大金额，最小金额，去掉日接单次数，单日总金额
            Log::error(date('Y-m-d H:i:s',time()).':筛选限制开始');
            $codeInfos = array_filter($codeInfos, function ($code) use ($money) {
                $expirationTime = $code['expiration_time'];
                $minMoney = $code['min_money'];
                $maxMoney = $code['max_money'];
                $successOrderNum = $code['success_order_num'];
                $todayReceivingAmount = $code['today_receiving_amount'];

                if (!empty($expirationTime) && $expirationTime != 0 && time() >= $expirationTime) {
                    return false;
                }

                if (!empty($minMoney) && $minMoney * 100 != 0 && $money < $minMoney) {
                    return false;
                }

                if (!empty($maxMoney) && $maxMoney * 100 != 0 && $money > $maxMoney) {
                    return false;
                }

                if ($successOrderNum != 0 && $code['today_receiving_number'] >= $successOrderNum) {
                    return false;
                }

                if ($code['limit__total'] != 0 && $money + $todayReceivingAmount > $code['limit__total']) {
                    return false;
                }

                return true;
            });

            Log::error(date('Y-m-d H:i:s',time()).':筛选限制结束');
            if (empty($codeInfos)){
                $area = '没有可用二维码（超出收款限制）';
                return [$money, false,null, $area];
            }
            //卡转卡去除相同金额
//            $codeTypename = Db::name('pay_code')->where('id',$codeType)->value('code');
//            if ($codeType == 'kzk' || $codeType == 'alipayUidTransfer'){
            //订单过期时间和解码时间
            $amount_lock = getAdminPayCodeSys('order_invalid_time',$codeType,$admin_id);
            if (empty($amount_lock)){
                $amount_lock_time = 600;
            }else{
                $amount_lock_time = $amount_lock * 60 + 10;
            }
//            Log::error(date('Y-m-d H:i:s',time()).':检测重复订单开始');
//                $OrderData = $EwmOrderModel
//                    ->where('gema_userid','in',$member_id)
//                    ->where('add_time','>',time()-$amount_lock_time)
//                    ->where('status',0)
//                    ->where('code_type',$codeType)
//                    ->field('code_id,order_pay_price,add_time')
//                    ->select();
//                foreach ($payPrices as $k=>$price){
//                    foreach ($OrderData as $p){
//                        foreach ($codeInfos as $val){
//                            if ($price == $money){
//                                unset($payPrices[$k]);
//                                continue;
//                            }
//                            if ($p['order_pay_price']==$price && $p['code_id'] == $val['id']){
//                                unset($payPrices[$k]);
//                                continue;
//                            }
//                        }
//                    }
//
//                }
            Log::error(date('Y-m-d H:i:s',time()).':检测重复订单开始');
//            $OrderData = $EwmOrderModel
//                ->where('gema_userid', 'in', $member_id)
//                ->where('add_time', '>', time() - $amount_lock_time)
//                ->where('status', 0)
//                ->where('code_type', $codeType)
//                ->field('code_id,order_pay_price,add_time')
//                ->select();
//
//            $codeInfosMap = [];
//            foreach ($codeInfos as $val) {
//                $codeInfosMap[$val['id']] = true;
//            }
//            $priceByCodeId = [];
//            foreach ($OrderData as $p) {
//                if (isset($codeInfosMap[$p['code_id']])) {
//                    $priceByCodeId[$p['code_id']][] = $p['order_pay_price'];
//                }
//            }
//            $payPrices = array_filter($payPrices, function ($price) use ($money, $priceByCodeId) {
//                if ($price == $money) {
//                    return false;
//                }
//                foreach ($priceByCodeId as $prices) {
//                    if (in_array($price, $prices)) {
//                        return false;
//                    }
//                }
//
//                return true;
//            });
            $codeIds = array_column($codeInfos, 'id');

            $OrderData = $EwmOrderModel
                ->whereIn('code_id', $codeIds)
                ->where('gema_userid', 'in', $member_id)
                ->where('add_time', '>', time() - $amount_lock_time)
                ->where('status', 0)
                ->where('code_type', $codeType)
                ->field('code_id,order_pay_price,add_time')
                ->select();

            $codeIdsSet = array_flip($codeIds);

            $priceByCodeId = [];
            foreach ($OrderData as $p) {
                if (isset($codeIdsSet[$p['code_id']])) {
                    $priceByCodeId[$p['code_id']][] = $p['order_pay_price'];
                }
            }

            $payPrices = array_filter($payPrices, function ($price) use ($money, $priceByCodeId) {
                if ($price == $money) {
                    return false;
                }

                foreach ($priceByCodeId as $prices) {
                    if (in_array($price, $prices)) {
                        return false;
                    }
                }

                return true;
            });


            Log::error(date('Y-m-d H:i:s',time()).':检测重复订单结束');
                if (empty($payPrices)){
                    $area = '没有可用二维码（无可用金额）';
                    return [$money, false,null, $area];
//                    return false;
                }
                $payPrices = array_unique($payPrices);
                shuffle($payPrices);
                $reallPayMoney = reset($payPrices);
//            }else{
//                $reallPayMoney  = $money;
//            }
//            Log::error('排掉后的金额:'.json_encode($payPrices,true));

        } else {
            $codeInfos = $GemapayCode->getRandomCodeV2($codeType, $member_id);
            if ($codeInfos === false) {
                $area = '没有可用二维码(没有开启中的收款码)';
                return [$money, false,null, $area];
//                return false;
            }
//            $codeInfos = array_unique($codeInfos);
//            去掉最大金额，最小金额，去掉日接单次数，单日总金额
            foreach ($codeInfos as $k=>$v){
                //过期时间
                if (!empty($v['expiration_time'] )&&  $v['expiration_time'] != 0){
                    if (time() >=  $v['expiration_time']){
                        unset($codeInfos[$k]);
                        continue;
                    }
                }
                //最小限额
                if (!empty($v['min_money'] ) &&$v['min_money'] * 100 !=0){
                    if ($money < $v['min_money']){
                        unset($codeInfos[$k]);
                        continue;
                    }
                }
                //最大限额
                if (!empty($v['max_money'] )&&  $v['max_money'] * 100 != 0){
                    if ($money > $v['max_money']){
                        unset($codeInfos[$k]);
                        continue;
                    }
                }
                //订单笔数限制
                if ($v['success_order_num'] != 0){
//                    $code_num = $EwmOrderModel->where('code_id',$v['id'])->whereTime('add_time', 'today')->count('id');
                    if ($v['today_receiving_number'] >= $v['success_order_num']){
                        unset($codeInfos[$k]);
                        continue;
                    }
                }
                //日限额
                if($v['limit__total'] != 0) {
//                    $money_today = $EwmOrderModel->where(['code_id'=>$v['id'],'status'=>1])->whereTime('add_time', 'today')->sum('order_pay_price');
                    if( $money+$v['today_receiving_amount'] > $v['limit__total'] ){
                        unset($codeInfos[$k]);
                        continue;
                    }
                }

            }
            if (empty($codeInfos)){
                $area = '没有可用二维码（超出收款限制）';
                return [$money, false,null, $area];
            }
            $amount_lock = getAdminPayCodeSys('order_invalid_time',$codeType,$admin_id);
            if (empty($amount_lock)){
                $amount_lock_time = 600;
            }else{
                $amount_lock_time = $amount_lock * 60 + 10;
            }

            $is_amount_lock = getAdminPayCodeSys('is_amount_lock',$codeType,$admin_id);
            if (empty($is_amount_lock)){
                $is_amount_lock = 1;
            }

            if ($is_amount_lock == 1){
                $OrderData = $EwmOrderModel
                    ->where('gema_userid','in',$member_id)
                    ->where('add_time','>',time()-$amount_lock_time)
                    ->where('status',0)
                    ->where('code_type',$codeType)
                    ->field('code_id,order_pay_price,add_time')
                    ->select();

                // 首先，创建一个映射从 code_id 到它在 $codeInfos 中的键
                $codeIdToKey = [];
                foreach ($codeInfos as $key => $val) {
                    if (!isset($codeIdToKey[$val['id']])) {
                        $codeIdToKey[$val['id']] = [];
                    }
                    $codeIdToKey[$val['id']][] = $key;
                }

// 然后，遍历 $OrderData，如果找到一个匹配的元素，就删除 $codeInfos 中的元素
                foreach ($OrderData as $p) {
                    if ($p['order_pay_price'] == $money && isset($codeIdToKey[$p['code_id']])) {
                        foreach ($codeIdToKey[$p['code_id']] as $key) {
                            unset($codeInfos[$key]);
                        }
                        unset($codeIdToKey[$p['code_id']]);
                    }
                }


                if (empty($codeInfos)){
                    $area = '没有可用二维码（无可用金额）';
                    return [$money, false,null, $area];
                }
            }

            $reallPayMoney = $money;
        }
        //$reallPayMoney = $money;;
        $userIds = [];
        $sys_code_type = getAdminPayCodeSys('sys_code_type',256,$admin_id);
        if (empty($sys_code_type)){
            $sys_code_type = 0;
        }
        if ($sys_code_type == 1){
            foreach ($codeInfos as $code) {
//                $userIds[] = $code['ms_id'];
                $userIds[] = Db::name('ms')->where('userid',$code['ms_id'])->field('userid,weight')->find();
            }
            // 计算权重
            if (count($userIds) == 1) {
                $lastUserId = current($userIds)['userid'];
            } else {
//                $weightUserIds = [];
//                foreach ($userIds as $key=>$msWeight){
//                      if (!is_numeric($msWeight['weight'])){
//                              $msWeight['weight'] = 1;
//                          }
//                $msWeight['weight'] = $msWeight['weight'] > 99 ? 99 : $msWeight['weight'];
//
//
//
//                $weightUserIds = array_merge(array_fill(0, $msWeight['weight'], $msWeight['userid']), $weightUserIds);
                $userIds = $this->unique_2d_array_by_key($userIds,'userid');
                $weightUserIds = [];
//                Log::error('array :'.json_encode($userIds,true));
                foreach ($userIds as $item) {
                    if (!is_numeric($item['weight'])){
                        $item['weight'] = 1;
                    }

                    $item['weight'] = $item['weight'] > 99 ? 99 : $item['weight'];
                    $userId = $item['userid'];
                    $weight = $item['weight'];
                    $weightUserIds = array_merge($weightUserIds, array_fill(0, $weight, $userId));
                }

//            $lastUserId = $MsCaceQuQue->get_data($weightUserIds);
//                Log::error('weight :'.json_encode($weightUserIds,true));
                $MsWeightQueue = new MsWeightQueue($codeType.$admin_id."_newweight_last_userids");
                $lastUserId = $MsWeightQueue->get_data($weightUserIds);
//                $a= $MsWeightQueue->getQueue();
//                Log::error('quque :'.json_encode($a,true));
            }
        }else{
            foreach ($codeInfos as $code) {
                $userIds[] = $code['ms_id'];
            }
            //去重
//            $userIds = array_unique($userIds);
            $userIds = array_keys(array_flip($userIds));
//            sort($userIds);
//            $lastUserId = cache($codeType.$admin_id."_last_userid");
//            if (empty($lastUserId)) {
//                $lastUserId = $userIds[0];
//            } else {
//                $flag = false;
//                foreach ($userIds as $key => $userId) {
//                    if ($userId > $lastUserId) {
//                        $flag = true;
//                        $lastUserId = $userId;
//                        break;
//                    }
//                }
//                if ($flag == false) {
//                    $lastUserId = $userIds[0];
//                }
//            }
//            cache($codeType.$admin_id.'_last_userid', $lastUserId);
//            Log::error(date('Y-m-d H:i:s',time()).':队列找码商开始');
            $MsCaceQuQue = new MsCacheQueue($codeType.$admin_id."_last_userids");
//        $MsCaceQuQue->init();
            $lastUserId = $MsCaceQuQue->get_data($userIds);
//            Log::error(date('Y-m-d H:i:s',time()).':队列找码商结束');
        }

        $msinfo = Db::name('ms')->where('userid',$lastUserId)->field('cash_pledge,money')->find();
        if ($msinfo['cash_pledge'] + $money > $msinfo['money']){
            $area = '锁定后的码商金额不足';
            return [$money, false,null, $area];
        }
        //这里按照正序排序
        $codeInfos = $this->arraySort($codeInfos, 'id', SORT_ASC);
        foreach ($codeInfos as $k=>$v){
            if ($v['ms_id'] != $lastUserId){
                unset($codeInfos[$k]);
            }
        }
        $codeInfos = array_column($codeInfos,'id');
        $PayCodeQueue = new PayCodeQueue($codeType.$admin_id."_last_userid_codeids_" . $lastUserId);
//        $PayCodeQueue->init();
        $codeInfo = $PayCodeQueue->get_data($codeInfos);

        $codeInfo  = Db::name('ewm_pay_code')->where('id',$codeInfo)->find();
        return [$reallPayMoney, $codeInfo, null,'匹配成功'];
    }


    public function getGoodCodeV2($money, $codeType, $ip, $member_id,$admin_id=0)
    {
        $GemapayCode = new EwmPayCode();
        $gemapayOrderLogic = new EwmOrder();
        //获取可以使用二维码
        //    $codeInfos = $GemapayCode->getAviableCode($money, $codeType, $member_id);
        //  if (empty($codeInfos)) {
        //      return false;
//	}
//	var_dump($codeInfos);;
        $isAllowPoint = true;
        //如果匹配不到整数,去匹配小数点

        if (empty($codeInfos)) {
            if ($isAllowPoint) {
                $payPrices = $gemapayOrderLogic->getAvaibleMoneys($money);
                foreach ($payPrices as $price) {
                    $codeInfos = $GemapayCode->getAviableCode($price, $codeType, $member_id);
                    if (!empty($codeInfos)) {

                        $reallPayMoney = $price;
                        break;
                    }
                }
            } else {
                $reallPayMoney = $money;
            }
            if (empty($codeInfos)) {
                return false;
            }
        } else {
            $reallPayMoney = $money;
        }

        //$reallPayMoney = $money;;
        $userIds = [];
        foreach ($codeInfos as $code) {
            $userIds[] = $code['ms_id'];
        }
        //去重
//        $userIds = array_unique($userIds);
        $userIds = array_keys(array_flip($userIds));
        sort($userIds);
//        echo json_encode($userIds);
        $lastUserId = cache($admin_id."_last_userid");
        if (empty($lastUserId)) {
            $lastUserId = $userIds[0];
        } else {
            $flag = false;
            foreach ($userIds as $key => $userId) {
                if ($userId > $lastUserId) {
                    $flag = true;
                    $lastUserId = $userId;
                    break;
                }
            }
            if ($flag == false) {
                $lastUserId = $userIds[0];
            }
        }





//        Log::error('$lastUserId :'. $lastUserId);
        cache($admin_id.'_last_userid', $lastUserId);

        //这里按照正序排序
        $codeInfos = $this->arraySort($codeInfos, 'id', SORT_ASC);
        $codeInfo = [];
        //该用户上次使用的codeid
        $lastUserIdCodeId = cache($admin_id."_last_userid_codeid_" . $lastUserId);
        if ($lastUserIdCodeId) {
            foreach ($codeInfos as $code) {
                if ($code['ms_id'] == $lastUserId && $code['id'] > $lastUserIdCodeId) {
                    $codeInfo = $code;
                    break;
                }
            }
            if (!$codeInfo) {
                foreach ($codeInfos as $code) {
                    if ($code['ms_id'] == $lastUserId) {
                        $codeInfo = $code;
                        break;
                    }
                }
            }
        } else {
            foreach ($codeInfos as $code) {
                if ($code['ms_id'] == $lastUserId) {
                    $codeInfo = $code;
                    break;
                }
            }
        }

        cache($admin_id."_last_userid_codeid_" . $lastUserId, $codeInfo['id']);

//var_dump($reallPayMoney);die();
        return [$reallPayMoney, $codeInfo, null];
    }


    /**
     * 删除二维数组中相同项的数据
     *
     */

    private function unique_2d_array_by_key($array, $key) {

        $tempArray = [];
        $result = [];
        foreach ($array as $value) {
            if (!isset($tempArray[$value[$key]])) {
                $tempArray[$value[$key]] = true;
                $result[] = $value;
            }
        }
        return $result;
    }

   private function shouldRemoveMs($ms_channel, $money, $admin_sys_disable_ms_money, $val) {
        return ($ms_channel['status'] == 0)
            || ($ms_channel['min_money'] > 0 && $money < $ms_channel['min_money'])
            || ($ms_channel['max_money'] > 0 && $money > $ms_channel['max_money'])
            || ($admin_sys_disable_ms_money == 1 && $money > $val['money']);
    }
    public $son_id = array();

    public function getIds($parentid)
    {
        $list = Db::name("ms")->where(["pid" => $parentid])->field('userid')->select();
        foreach ($list as $key => $value) {
            $this->son_id[] = $value['userid'];
            $this->getIds($value['userid']);
        }
        return $this->son_id;
    }


    /**
     * @param $money
     * @param $tradeNo
     * @param $codeType
     * @param $merchantOrderNo
     * @param $admin_id
     * @param $notify_url
     * @param int $memeber_id
     * @return array
     */
    public function createOrder($money, $tradeNo, $codeType, $merchantOrderNo, $admin_id, $notify_url, $member_ids = 0,$pay_username = '',$team_ids = 0,$uid = 0 )
    {
//        Log::error('订单：'.$tradeNo.'创建订单开始:'.date('Y-m-d H:i:s',time()));
        $GemapayCode = new EwmPayCode();
        $GemapayOrderModel = new \app\common\model\EwmOrder();
        $msModel = new \app\common\model\Ms();
        $adminModel = new \app\common\model\Admin();
        $GemapayCode->startTrans();
        //码商余额是否大于押金浮动金额
        //没有指定码商
        $msyajin = getAdminPayCodeSys('ms_order_min_money',256,$admin_id);
        if (empty($msyajin) && $msyajin <= 0){
            $msyajin = 2000;
        }
        $msyajin = $msyajin + $money;
        if(empty( $member_ids))
        {
            if (!empty($team_ids)){
                $team_ids = explode(',', $team_ids) ? explode(',', $team_ids) : [];
                $ms_ids = [];

                foreach ($team_ids as $v) {
                    $team_users = $this->getIds($v);

                    // 将 $v 添加到 $ms_ids 数组中，如果它尚未存在
                    if (!in_array($v, $ms_ids)) {
                        array_push($ms_ids, $v);
                    }

                    foreach ($team_users as $user) {
                        // 将 $user 添加到 $ms_ids 数组中，如果它尚未存在
                        if (!in_array($user, $ms_ids)) {
                            array_push($ms_ids, $user);
                        }
                    }
                }

//                Log::error('全部的团队成员 ：'.json_encode($ms_ids,true));
                $msList = $msModel->where(['status'=>1,'work_status'=>1,'money'=>['>=',$money],'admin_work_status' => 1,'userid'=>['in',$ms_ids],'admin_id'=>$admin_id])->select();
            }else{
                $msList = $msModel->where(['status'=>1,'work_status'=>1,'money'=>['>=',$money], 'admin_work_status' => 1,'admin_id'=>$admin_id])->select();
            }

        }
        else
        {
            $msList = $msModel->where(['status'=>1,'work_status'=>1,'money'=>['>=',$money],'admin_work_status' => 1,'userid'=>['in',$member_ids],'admin_id'=>$admin_id])->select();
        }
//        Log::error('lastsql :'.$msModel->getLastSql());
        if(empty($msList))
        {
            $GemapayCode->rollback();
            return ['code' => CodeEnum::ERROR, 'msg' => '没足够的码商可以接这笔订单（没有开启的码商！）'];
        }
        $msChannelList = Db::name('ms_channel')->where([
            'ms_id' => ['in', array_column($msList, 'userid')],
            'pay_code_id' => $codeType
        ])->select();
        $member_ids = [];
        $admin_sys_disable_ms_money = getAdminPayCodeSys('disable_ms_money_status',256,$admin_id);
        if (empty($admin_sys_disable_ms_money)){
            $admin_sys_disable_ms_money = 2;
        }
        $member_no_nullRate = getAdminPayCodeSys('member_no_nullRate',256,$admin_id);
        if (empty($member_no_nullRate)){
            $member_no_nullRate = 2;
        }
        Log::error(date('Y-m-d H:i:s',time()).'押金判断开始');
        foreach ($msList as $key => $val) {
//            $ms_channel = Db::name('ms_channel')->where(['ms_id' => $val['userid'], 'pay_code_id' => $codeType])->find();
            $ms_channel = $this->findMsChannel($msChannelList, $val['userid'], $codeType);
            $ms_channel['status'] = $ms_channel['status'] ?? 1;
            $ms_channel['min_money'] = $ms_channel['min_money'] ?? '0.00';
            $ms_channel['max_money'] = $ms_channel['max_money'] ?? '0.00';

            if ($this->shouldRemoveMs($ms_channel, $money, $admin_sys_disable_ms_money, $val)) {
//                unset($msList[$key]);
                continue;
            }



                if ($money + $val['cash_pledge'] > $val['money']){
//                    unset($msList[$key]);
                    continue;
                }


//            $filteredMsList[] = $val;
            $member_ids[] = $val['userid'];
//            $member_ids[] = $val['userid'];
        }
        Log::error(date('Y-m-d H:i:s',time()).'押金判断结束');
        if(empty($member_ids))
        {
            $GemapayCode->rollback();
            return ['code' => CodeEnum::ERROR, 'msg' => '没足够的码商可以接这笔订单（码商余额不足！）'];
        }
//Log::error(date('Y-m-d H:i:s',time()).'团队判断开始');
//        foreach($member_ids as $key=>$val){
////            $team_work_status = Db::name('ms')->where('userid',$v)->value('team_work_status');
//            $team = getTeamPid($val);
//            foreach ($team as $v){
//                $team_work_status = Db::name('ms')->where('userid',$v)->value('team_work_status');
//                if ($team_work_status == 0){
//                        unset($member_ids[$key]);
//                        continue;
//                }
//                continue;
//            }
//
//        }
//        Log::error(date('Y-m-d H:i:s',time()).'团队判断结束');
//        if(empty($member_ids))
//        {
//            $GemapayCode->rollback();
//            return ['code' => CodeEnum::ERROR, 'msg' => '没足够的码商可以接这笔订单（没有开启的团队！）'];
//        }

//        $msyajin
        $member_ids = implode(',', $member_ids);

        $insId = $GemapayOrderModel->addGemaPayOrder(0, $money, $tradeNo, 0, 0, "", "", $codeType, $tradeNo, $merchantOrderNo, $admin_id, $notify_url, 0, $pay_username,$uid);
        if (empty($insId)) {
            $GemapayCode->rollback();
//            $GemapayOrderModel->rollback();
            return ['code' => CodeEnum::ERROR, 'msg' => '更新订单数据失败'];
        }
        $where['order_no'] = $tradeNo;
//        $GemapayOrderModel->startTrans();
        $order = $GemapayOrderModel->where($where)->find();

//        $adminwhere['id'] =  $admin_id;
//        $admin =  $adminModel->lock(true)->where($adminwhere)->find();
//sleep(3);
        $ip = '--';
        //判断金额是否浮动
//        $orderAmount = getAdminPayCodeSys('money_is_float',$codeType,$admin_id);
//        if (empty($orderAmount)){
//            $orderAmount = $order['order_price']-0.01;
//        }else if ($orderAmount == 1){
//            $orderAmount = $order['order_price']-0.01;
//        }else{
//            $orderAmount = $order['order_price'];
//        }
        //二维码是否有固定收款金额
        $ewm_is_pay_money_status = getAdminPayCodeSys('ewm_is_pay_money',$codeType,$admin_id);
        if (empty($ewm_is_pay_money_status)){
//            $smsTemplate = require_once('./data/conf/adminSysPayCode.php');
            $ewm_is_pay_money = false;
        }elseif ($ewm_is_pay_money_status == 1){
            $ewm_is_pay_money = true;
        }else{
            $ewm_is_pay_money = false;
        }

        if ($ewm_is_pay_money || $order['code_type'] == 59){
//            Log::error('订单号：'.$tradeNo.'进入v5取码:'.date('Y-m-d H:i:s',time()));
            list($money, $code, $area,$msg) = $this->getGoodCodeV5($order['order_price'], $order['code_type'], $ip, $member_ids, $admin_id);
        }else{
            Log::error(date('Y-m-d H:i:s',time()).'：订单号：'.$tradeNo.'进入v3取码');
            list($money, $code, $area,$msg) = $this->getGoodCodeV3($order['order_price'], $order['code_type'], $ip, $member_ids, $admin_id);
        }
        Log::error(date('Y-m-d H:i:s',time()).'：订单号：'.$tradeNo.'取码结束');
        if ($code == false) {
            $GemapayOrderModel->isUpdate(true, ['id' => $order['id']])->save(['note' => $msg]);
//            $GemapayOrderModel->rollback();
            return ['code' => CodeEnum::ERROR, 'msg' => $msg];
        }
        $data['order_pay_price'] = $money;
        $data['gema_username'] = $code['user_name'];
        $data['gema_userid'] = $code['ms_id'];
        $data['code_id'] = $code['id'];
        $data['visite_area'] = $area;
        $data['visite_ip'] = $ip;
        $data['visite_time'] = time();
        if ($codeType == 53){
            $Config = new Config();
            $usdtRate = $Config->where(['name'=>'usdt_rate'])->value('value');
            $usdt_num = sprintf("%.3f",$money/$usdtRate);
            $data['extra'] =$usdt_num ;
        }
        if (false == $GemapayOrderModel->where(['id' => $order['id']])->update($data)) {
//            $GemapayOrderModel->rollback();
            Log::error('二维码订单报错id:'. $order['id'].' 数据:'.json_encode($data));
            return ['code' => CodeEnum::ERROR, 'msg' => '更新失败'];

        }
//        $GemapayOrderModel->commit();
        //扣除用户余额
//        $message = "抢单" . $tradeNo . "成功";


        /*        if (false == accountLog($code['ms_id'], MsMoneyType::ORDER_DEPOSIT, MsMoneyType::OP_SUB, $order['order_price'], $message)) {
                    $GemapayCode->where(true, ['id' => $order['id']])->update(['note' => 'error']);
                    $GemapayCode->commit();
                    return ['code' => CodeEnum::ERROR, 'msg' => 'error'];
                }*/
        Log::error(date('Y-m-d H:i:s',time()).'：订单号：'.$tradeNo.'冻结码商金额开始');
        if ($admin_sys_disable_ms_money == 1){
            if (false == accountLog($code['ms_id'], MsMoneyType::ORDER_DEPOSITS, MsMoneyType::OP_SUB, $order['order_price'], $tradeNo)) {
                    $GemapayCode->where(true, ['id' => $order['id']])->update(['note' => '冻结码商订单金额失败']);
                    $GemapayCode->commit();
                    return ['code' => CodeEnum::ERROR, 'msg' => '冻结码商订单金额失败'];
               }

            if (false == accountLog($code['ms_id'], MsMoneyType::ORDER_DEPOSITS,MsMoneyType::OP_ADD, $order['order_price'], $tradeNo, 'disable')){
                $GemapayCode->where(true, ['id' => $order['id']])->update(['note' => '冻结码商订单金额,冻结余额加入失败']);
                $GemapayCode->commit();
                return ['code' => CodeEnum::ERROR, 'msg' => '冻结码商订单金额失败'];
            }
        }

        Log::error(date('Y-m-d H:i:s',time()).'：订单号：'.$tradeNo.'冻结码商金额结束');
        $GemapayCode->incTodayOrder($code['id']);
//        $GemapayCode->commit();

        $GemapayCode->commit();
        return ['code' => CodeEnum::SUCCESS, 'data' => [
            'code' => $code,
            'money' => $money
        ]];

    }


   private function findMsChannel($msChannelList, $msId, $payCodeId)
    {
        foreach ($msChannelList as $msChannel) {
            if ($msChannel['ms_id'] == $msId && $msChannel['pay_code_id'] == $payCodeId) {
                return $msChannel;
            }
        }
        return null;
    }



    /**
     * 获取可用金额列表
     * @param $money
     * @return array
     */
    public function getAvaibleMoneys($money,$codeType,$admin_id)
    {
        $data = [];
        $limit = getAdminPayCodeSys('order_amount_float_range',$codeType,$admin_id);
        if (empty($limit)){
            $limit = 20;
        }
        $order_amount_float_no_top = getAdminPayCodeSys('order_amount_float_no_top',$codeType,$admin_id);
        if ($order_amount_float_no_top == '1'){
            $moneyStart = $money - $limit * 0.01+0.01;
        }else{
            $moneyStart = $money - $limit * 0.005+0.01;
        }

        for ($i = 0; $i <= $limit; $i++) {
            if ($moneyStart + $i * 0.01 != $money+0.01) {
                $data[] = sprintf("%.2f", floatval($moneyStart + $i * 0.01));
            }
        }

        return $data;
    }


    /**
     * 用户完成订单
     * @param $orderId
     * @param $note
     * @param $userid
     */
    public function setOrderSucessByUser($orderId, $userid, $security=0, $next_user_id = 0, $coerce = false)
    {
        //判断订单状态
        $GemaPayOrder = new \app\common\model\EwmOrder();
        $SecurityLogic = new SecurityLogic();

        //判断交易密码
        if (empty(cookie('member_check_command_ok'))){
            $result = $SecurityLogic->checkSecurityByUserId($userid, $security);

            //判断用收款ip是否和最近登录的ip是否一致
            if ($result['code'] == CodeEnum::ERROR) {
                return $result;
            }
        }


        $where['id'] = $orderId;
        $where['status'] = $GemaPayOrder::WAITEPAY;
        //判断是否强制补单
        if (1) {
            unset($where['status']);
        }
        $where['gema_userid'] = $userid;
        if ($next_user_id) {
            //  $where['gema_userid'] = $next_user_id;
        }

        Db::startTrans();
        $orderInfo = $GemaPayOrder->where($where)->lock(true)->find();
        if ($orderInfo['status'] == $GemaPayOrder::PAYED) {
            Db::rollback();
            return ['code' => CodeEnum::ERROR, 'msg' => '订单已完成'];
        }
        Db::commit();


        if (empty($orderInfo)) {
            return ['code' => CodeEnum::ERROR, 'msg' => '订单信息有误'];
        }

        if ($orderInfo['gema_userid'] != $userid) {
            return ['code' => CodeEnum::ERROR, 'msg' => '操作非法'];
        }

        //判断用户余额是否足够

        $UserModel = new \app\common\model\Ms();
        $userMoney = $UserModel->where(['userid' => $userid])->value('money');
        if ($userMoney < $orderInfo['order_price']) {
         //   return ['code' => CodeEnum::ERROR, 'msg' => '用户余额不足'];
        }

        return $this->setOrderSucess($orderInfo, "用户手动调单",$userid);
    }



    /**
     * 用户完成订单不需要安全码
     * @param $orderId
     * @param $note
     * @param $userid
     */
    public function setOrderSucessByUserNullPayPass($orderId, $userid,  $next_user_id = 0, $coerce = false,$note='用户手动调单')
    {
        //判断订单状态
        $GemaPayOrder = new \app\common\model\EwmOrder();
        $SecurityLogic = new SecurityLogic();
        $where['id'] = $orderId;
        $where['status'] = $GemaPayOrder::WAITEPAY;
        //判断是否强制补单
        if (1) {
            unset($where['status']);
        }
        $where['gema_userid'] = $userid;
        if ($next_user_id) {
            //  $where['gema_userid'] = $next_user_id;
        }

        Db::startTrans();
        $orderInfo = $GemaPayOrder->where($where)->lock(true)->find();
        if ($orderInfo['status'] == $GemaPayOrder::PAYED) {
            Db::rollback();
            return ['code' => CodeEnum::ERROR, 'msg' => '订单已完成'];
        }
        Db::commit();


        if (empty($orderInfo)) {
            return ['code' => CodeEnum::ERROR, 'msg' => '订单信息有误'];
        }

        if ($orderInfo['gema_userid'] != $userid) {
            return ['code' => CodeEnum::ERROR, 'msg' => '操作非法'];
        }

        //判断用户余额是否足够

        $UserModel = new \app\common\model\Ms();
        $userMoney = $UserModel->where(['userid' => $userid])->value('money');
        if ($userMoney < $orderInfo['order_price']) {
            //   return ['code' => CodeEnum::ERROR, 'msg' => '用户余额不足'];
        }

        return $this->setOrderSucess($orderInfo, $note,$userid);
    }

    protected function getTeamPidRate($id, $rate, $code_type)
    {
        $nav = Db::name('ms')->where('userid', $id)->where('status','neq','-1')->field('userid, pid')->find();
        if ($nav['pid'] > 0){
            $teamrate = Db::name('ms_rate')->where(['ms_id' => $nav['pid'], 'code_type_id' => $code_type])->value('rate');

            if (empty($teamrate)) {
                $result = self::getTeamPidRate($nav['pid'], 0.00, $code_type);
            } else {
                if ($rate > $teamrate) {
                    return false;
                }

                if ($nav['pid'] > 0) {
                    $result = self::getTeamPidRate($nav['pid'], $teamrate, $code_type);
                } else {
                    $result = true;
                }
            }

            return $result;
        }
        return true;
    }


    /**
     * @param $orderInfo
     * @param bool $notSend
     * @return array|bool
     */
    protected function orderProfit($orderInfo, $notSend = false)
    {
        $msrate = Db::name('ms_rate')->where('ms_id', $orderInfo['gema_userid'])->where('code_type_id',$orderInfo['code_type'])->value('rate');
        $ms= Db::name('ms')->where('userid',$orderInfo['gema_userid'])->find();
        if (!$msrate || $msrate == 0){
            if ($ms['level'] > 1){
                $team = $this->getTeamPidRate($ms['userid'],0.00,$orderInfo['code_type']);
                if ($team === false){
                    return  true;
                }
                $agent = $this->getTeamPidFunrun($ms['userid'],$orderInfo['code_type'],0.00);
                if (empty($agent)){
                    return true;
                }
                $fenrun = $this->fenrun($agent,$msrate,$orderInfo['order_pay_price'],$orderInfo['out_trade_no'],$ms['username'],$ms['userid'],$orderInfo['code_type']);
                if ($fenrun === false){
                    Log::error($orderInfo['out_trade_no'].'码商完成时利润异常！');
                    return true;
                }
            }
            return true;
        }
        $topms = getNavPid($orderInfo['gema_userid']);
        $topmsRate = Db::name('ms_rate')->where(['ms_id'=>$topms,'code_type_id'=>$orderInfo['code_type']])->value('rate');
        if ($msrate >$topmsRate){
//            Log::error($orderInfo['out_trade_no'].'码商完成时代理利润异常！');
//            return false;
            Log::error($orderInfo['out_trade_no'].'码商完成时利润异常！');
            return true;
        }



        $money = sprintf('%.2f',$orderInfo['order_pay_price'] * $msrate / 100);
            $max_lirun = $orderInfo['order_pay_price'] * 0.12;
            if ($money > sprintf("%.2f",$max_lirun)){
    //                $res = Db::name('ms')->where('userid',$ms['userid'])->update(['work_status'=>0,'admin_work_status'=>0]);
                Log::error($orderInfo['out_trade_no'].'码商完成时利润异常！');
                return true;
            }



            if ($ms['level'] > 1){
//                $team = $this->getTeamPidRate($ms['userid'],$msrate,$orderInfo['code_type']);
//                if ($team === false){
//                    return  false;
//                }
//                    $this->orderMsTidWithTransaction($orderInfo,$ms['pid'],$orderInfo['code_type']);
//                    $this->orderMsTid($orderInfo,$ms['pid'],$orderInfo['code_type']);
//                $agent = getTeamPidFunrun($ms['userid'],$orderInfo['code_type']);
                $team = $this->getTeamPidRate($ms['userid'],$msrate,$orderInfo['code_type']);
                if ($team === false){
                    return true;
                }
                $agent = $this->getTeamPidFunrun($ms['userid'],$orderInfo['code_type'],$msrate);
                if (empty($agent)){
                    return true;
                }
                $fenrun = $this->fenrun($agent,$msrate,$orderInfo['order_pay_price'],$orderInfo['out_trade_no'],$ms['username'],$ms['userid'],$orderInfo['code_type']);
                if ($fenrun === false){
                    Log::error($orderInfo['out_trade_no'].'码商完成时利润异常！');
                    return true;
                }
            }

//
//        $money = sprintf('%.2f', $user['bank_rate'] * $orderInfo['order_pay_price'] / 100);


        if ((bccomp($money, 0.00, 2) == 1)) {
            //$tip_message = "订单【{$orderInfo['order_no']}】中获得佣金{$money}元";
            $tip_message = $orderInfo['order_no'];
            if (!accountLog($orderInfo['gema_userid'], MsMoneyType::USER_BONUS, MsMoneyType::OP_ADD, $money, $tip_message)) {
                Log::error($orderInfo['out_trade_no'].'码商提成失败！');
                return true;
            }
        }
        $yejian_rate = $this->getAfterAddMsRate($orderInfo['gema_userid']);
        if ($yejian_rate > 0){
            $yejian_fenrun = sprintf('%.2f',$orderInfo['order_pay_price'] * $yejian_rate / 100);
            $tip_message = $orderInfo['order_no'].'夜间分润加成';
            if (accountLog($orderInfo['gema_userid'], MsMoneyType::YE_USER_BONUS, MsMoneyType::OP_ADD, $yejian_fenrun, $tip_message )=== false){
                Log::error($orderInfo['out_trade_no'].'夜间码商提成失败！');
                return true;
            }
        }
        //查询

        $today_tc  =  Cache::get(date('Ymd',time()).'toady_success_tc_msid_'.$orderInfo['gema_userid'].'_codeType_'.$orderInfo['code_type']);
        Cache::set(date('Ymd',time()).'toady_success_tc_msid_'.$orderInfo['gema_userid'].'_codeType_'.$orderInfo['code_type'],$today_tc + $money);
        return $money;
    }


    public function orderMsTidWithTransaction($order, $mtid, $cnlid)
    {
        // 使用事务处理
        Db::transaction(function () use ($order, $mtid, $cnlid) {
            $this->processOrderMsTid($order, $mtid, $cnlid);
        });
    }

    private function getTeamPidFunrun($id,$code_type,$msrate)
    {
        $nav = Db::name('ms')->where('userid', $id)->field('userid, pid')->find();
        $result = [];

        if ($nav['pid'] > 0) {
            $profit = Db::name('ms_rate')->where('ms_id', $nav['pid'])->where('code_type_id',$code_type)->value('rate'); // 获取当前上级用户的利润
            if ($msrate > $profit){
                return [];
            }
            $result = self::getTeamPidFunrun($nav['pid'],$code_type,$profit); // 递归获取上级用户

            $result[] = ['uid' => $nav['pid'], 'profit' => $profit]; // 将当前上级用户的uid和利润添加到结果数组中
        }

        return $result;
    }


    private function processOrderMsTid($order, $mtid, $cnlid)
    {
        $agentList = [];
        $currentAgent = $mtid;

        // 获取代理链
        while ($currentAgent > 0) {
            $agentRate = Db::name('ms_rate')->where(['ms_id' => $currentAgent, 'code_type_id' => $cnlid])->value('rate');
            $agentList[] = [
                'agentId' => $currentAgent,
                'rate' => $agentRate
            ];
            $currentAgent = $this->modelMs->where(['userid' => $currentAgent])->value('pid');
        }

        // 计算盈利并分润
        for ($i = 0; $i < count($agentList) - 1; $i++) {
            $rateDifference = $agentList[$i + 1]['rate'] - $agentList[$i]['rate'];
            if ($rateDifference > 0) {
                $gain = floatval($order['order_pay_price']) * $rateDifference / 100;
                accountLog($agentList[$i]['agentId'], MsMoneyType::AGENT_BONUS, MsMoneyType::OP_ADD, $gain, '子码商（ID：' . $order['gema_userid'] . '）-【' . $order['gema_username'] . '】产生交易订单（' . $order['order_no'] . '），盈利' . $gain);
            } else {
                $prevAgentIndex = $i - 1;
                while ($prevAgentIndex >= 0 && $agentList[$prevAgentIndex]['rate'] == $agentList[$i]['rate']) {
                    $prevAgentIndex--;
                }
                if ($prevAgentIndex >= 0) {
                    $rateDifference = $agentList[$i + 1]['rate'] - $agentList[$prevAgentIndex]['rate'];
                    $gain = floatval($order['order_pay_price']) * $rateDifference / 100;
                    accountLog($agentList[$i]['agentId'], MsMoneyType::AGENT_BONUS, MsMoneyType::OP_ADD, $gain, '子码商（ID：' . $order['gema_userid'] . '）-【' . $order['gema_username'] . '】产生交易订单（' . $order['order_no'] . '），盈利' . $gain);
                    $i++;
                }
            }
        }

        // 顶级代理的分润
        $topAgentIndex = count($agentList) - 1;
        $topAgentRateDifference = $agentList[$topAgentIndex]['rate'] - $agentList[$topAgentIndex - 1]['rate'];
        $topAgentGain = floatval($order['order_pay_price']) * $topAgentRateDifference / 100;
        accountLog($agentList[$topAgentIndex]['agentId'], MsMoneyType::AGENT_BONUS, MsMoneyType::OP_ADD, $topAgentGain, '子码商（ID：' . $order['gema_userid'] . '）-【' . $order['gema_username'] . '】产生交易订单（' . $order['order_no'] . '），盈利' . $topAgentGain);
    }


    private function fenrun($agent,$my,$money,$order_no,$ms_name,$ms_id,$code_type){
        $team = $this->getTeamPidRate($ms_id,$my,$code_type);
        if ($team === false){
            return  false;
        }


        for($i=0;$i<count($agent);$i++)
        {
            if($i==count($agent)-1)
            {
                if(($agent[$i]['profit']-$my)>0){
                    $lirun= ($agent[$i]['profit']-$my)*$money/100;
//                    echo 'userid'.$agent[$i]['uid'].' linrun:'.$lirun.'<br>';
                    $max_lirun =$money* 0.12;
//                    if ($money > sprintf("%.2f",$max_lirun)){
                    if ($lirun > sprintf("%.2f",$max_lirun)){
                        Log::error($order_no.'码商完成时代理利润异常！');
                        return false;
                    }
                    accountLog($agent[$i]['uid'], MsMoneyType::AGENT_BONUS, MsMoneyType::OP_ADD, $lirun, '子码商（ID：' . $ms_id . '）-【' . $ms_name . '】产生交易订单（' . $order_no . '），盈利' . $lirun);
                }
            }
            else{
                if($agent[$i]['profit']-$agent[$i+1]['profit']>0.0)
                {
                    $max_lirun =$money* 0.12;
                    $lirun= ($agent[$i]['profit']-$agent[$i+1]['profit'])*$money/100;
                    if ($lirun > sprintf("%.2f",$max_lirun)){
                        Log::error($order_no.'码商完成时代理利润异常！');
                        return false;
                    }
//                    echo 'userid'.$agent[$i]['uid'].' linrun:'.$lirun.'<br>';
                    accountLog($agent[$i]['uid'], MsMoneyType::AGENT_BONUS, MsMoneyType::OP_ADD, $lirun, '子码商（ID：' . $ms_id . '）-【' . $ms_name . '】产生交易订单（' . $order_no . '），盈利' . $lirun);
                }
            }

        }
    }


    /** 计算码商代理费率
     * @author luomu 2022/10/30
     */
    public function orderMsTid($order,$mtid,$cnlid){
        //查找该码商跟代理的费率
        //码商费率
        $msRate = Db::name('ms_rate')->where(['ms_id'=>$order['gema_userid'],'code_type_id'=>$cnlid])->value('rate');
        //代理费率
        $agentRate = Db::name('ms_rate')->where(['ms_id'=>$mtid,'code_type_id'=>$cnlid])->value('rate');

        //判断一下如果码商费率大于上级码商费率，就不分润了
        if ($msRate >= $agentRate){
            return true;
        }

        $rate = (floatval($agentRate) - floatval($msRate) >0) ? floatval($agentRate) - floatval($msRate) : 0;//码商费率
        $ffid =  $this->modelMs->where(['userid'=>$mtid])->value('pid');
        if($rate<=0){
            if($ffid>0){
                //上级代理的父代理结算
                $mtid = $ffid;
                $this->orderAgentFid($order,$ffid,$agentRate,$mtid,$cnlid);
            }
            return true;
        }
        //计算代理盈利
        $gain = floatval($order['order_pay_price']) * $rate / 100;
        accountLog($mtid, MsMoneyType::AGENT_BONUS, MsMoneyType::OP_ADD, $gain, '子码商（ID：'.$order['gema_userid'].'）-【'.$order['gema_username'].'】产生交易订单（'.$order['order_no'].'），盈利'.$gain);

        if($ffid>0){
            //上级代理的父代理结算
            $this->orderAgentFid($order,$ffid,$agentRate,$mtid,$cnlid);
        }

    }


    /** 计算码商上级代理盈利
     * @author luomu 2022/10/30
     */
    private function orderAgentFid($order,$fid,$tidRate,$mtid,$cnlid){
        //查找父代理的费率
        $fidRate = Db::name('ms_rate')->where([ 'code_type_id' => $cnlid, 'ms_id' => $fid])->value('rate');

        $rate = (floatval($fidRate) - floatval($tidRate)>0) ? floatval($fidRate) - floatval($tidRate) : 0;//码商费率
        $ffid =  Db::name('ms')->where(['userid'=>$fid])->value('pid');
        if($rate<=0){
            if($ffid>0){
                //上级代理的父代理结算
                $mtid = $fid;
                self::orderAgentFid($order,$ffid,$fidRate,$mtid,$cnlid);
            }
            return true;
        }

        //计算代理盈利
        $gain = floatval($order['order_pay_price']) * $rate / 100;
        $msname = $this->modelMs->where('userid',$mtid)->value('username');
        accountLog($fid, MsMoneyType::AGENT_BONUS, MsMoneyType::OP_ADD, $gain, '子码商（ID：'.$mtid.'）-【'.$msname.'】产生交易订单（'.$order['order_no'].'），盈利'.$gain);

        //查找上级代理是否存在父代理
        if($ffid>0){
            //上级代理的父代理结算
            $mtid = $fid;
            self::orderAgentFid($order,$ffid,$fidRate,$mtid,$cnlid);
        }

    }





    /**
     * 记录失败次数
     * @param $code_id
     * @param $type
     * @param $admin_id
     */
    public function recordFailedNum($code_id, $type, $admin_id)
    {
        $GemapayCodeModel = new EwmPayCode();
        $code = $GemapayCodeModel->where(['id' => $code_id])->find();
        if ($code) {
            $code->failed_order_num++;
            $code->updated_at = time();
            $code->save();
            $code->commit();
        }
    }


    /**
     * 设置订单为成功状态
     * @param $orderId
     * @param string $note
     * @return array
     */
    public function setOrderSucess1($orderInfo,$note,$userid,$RemarkNmae='码商')
    {
//        $GemapayCode = new  \app\common\model\EwmOrder();
        $GemapayOrderModel = new \app\common\model\EwmOrder();
        Db::startTrans();
        $orderInfo = $GemapayOrderModel->lock(true)->find($orderInfo['id']);
        //如果订单为关闭状态则手动强制完成需要扣除
        /* if ($orderInfo['status'] == $GemapayOrderModel::CLOSED) {
             $message = "后台强制完成订单" . $orderInfo['out_trade_no'] . ",扣除订单金额";
             $res = accountLog($orderInfo['gema_userid'], MsMoneyType::ORDER_FORCE_FINISH,
                 MsMoneyType::OP_SUB, $orderInfo["order_price"], $message);
             if ($res == false) {
                 Db::rollback();
                 return ['code' => CodeEnum::ERROR, 'msg' => '!更新数据失败'];
             }
         }*/

        //$message = "订单" . $orderInfo['out_trade_no'] . "完成";

        //判断码商余额是否充足

        $message = $orderInfo['out_trade_no'];
        $admin_sys_disable_ms_money = getAdminPayCodeSys('disable_ms_money_status',256,$orderInfo['admin_id']);
        if (empty($admin_sys_disable_ms_money)){
            $admin_sys_disable_ms_money = 2;
        }

        if ($admin_sys_disable_ms_money != 1){
            $ms_money = $this->modelMs->where('userid', $orderInfo['gema_userid'])->value('money');
            if ($ms_money < $orderInfo['order_price']){
                return ['code' => CodeEnum::ERROR, 'msg' => '码商余额不足'];
            }
            if (false === accountLog($orderInfo['gema_userid'], MsMoneyType::ORDER_DEPOSIT, MsMoneyType::OP_SUB, $orderInfo['order_price'], $message)) {
                $GemapayOrderModel->where('id',$orderInfo['id'])->update(['note' => 'error']);
                $GemapayOrderModel->rollback();
                return ['code' => CodeEnum::ERROR, 'msg' => 'error'];
            }
        }else{
//            $admin_sys_time = getAdminPayCodeSys('order_invalid_time',$orderInfo['code_type'],$orderInfo['admin_id']);
//            if (empty($admin_sys_time)){
//                $admin_sys_time = 600;
//            }else{
//                $admin_sys_time = $admin_sys_time * 60;
//            }
//
//            if ($orderInfo['add_time'] + $admin_sys_time < time()){
//                //已过期
//
//            }
            if ($orderInfo['status'] == 3){
                if (false === accountLog($orderInfo['gema_userid'], MsMoneyType::ORDER_DEPOSIT, MsMoneyType::OP_SUB, $orderInfo['order_price'], $message)) {
                    $GemapayOrderModel->where('id',$orderInfo['id'])->update(['note' => '扣减码订单商金额失败']);
                    $GemapayOrderModel->rollback();
                    return ['code' => CodeEnum::ERROR, 'msg' => '扣减码订单商金额失败'];
                }
            }
        }

        //给码商返佣金
        $bonus = $this->orderProfit($orderInfo);
        if ($bonus === false) {
        //    Db::rollback();
          //  return ['code' => CodeEnum::ERROR, 'msg' => '!发放佣金失败'];
        }

        //扣除代理用户余额
        if ($this->logicConfig->isSysEnableRecharge()){
            $rate_amount = Admin::getPayAdminRateMoney($orderInfo['admin_id'], $orderInfo['order_price'], $orderInfo['code_type']);
            if ($rate_amount){
                $admin = $this->modelAdmin->lock(true)->where('id',  $orderInfo['admin_id'])->find();
                if ($admin['balance'] < $rate_amount){
                    Db::rollback();
                    return ['code' => CodeEnum::ERROR, 'msg' => '代理商费用不足！'];
                }
                $this->logicAdminBill->addBill($orderInfo['admin_id'], 3, 0, $rate_amount, $orderInfo['order_no']);
            }
        }

        $res = $GemapayOrderModel->setOrderSucess($orderInfo['id'], $note, $bonus);


        if ($res == false) {
            Db::rollback();
            return ['code' => CodeEnum::ERROR, 'msg' => '!更新数据失败!'];
        }

        $orderModel = model('orders');
        $result = $orderModel->where('trade_no', $orderInfo['order_no'])->find();
        if (!empty($result)){
            if ($result['status'] == 1 || $result['status'] == 0){
                $OrdersNotify = new  \app\common\logic\OrdersNotify();
                $logicQueue = new  \app\common\logic\Queue();
                $bdRemarks = '后台确认收款通知支付系统单号'.$orderInfo['order_no'];
                if ($userid != 0){
                    $bdRemarks = $RemarkNmae.'用户'.$userid.'确认收款通知支付系统单号'.$orderInfo['order_no'];
                }

                if(empty($bdRemarks))
                {
                    return ['code' => CodeEnum::ERROR, 'msg' => '必须填写备注' ];
                }
                $model = new \app\api\logic\Notify();
                $res = $model->updateOrderInfo($result, 2);
                $OrdersNotify->saveOrderNotify($result);
                if ($res === true){

                    //  $OrdersNotify->saveOrderNotify($result);
                    //  $logicQueue->pushJobDataToQueue('AutoOrderNotify' , $result , 'AutoOrderNotify');
                    //单独修改补单备注(编辑封闭新增放开原则)todo 此处后期事务处理最好
                    $orderModel->where('trade_no', $orderInfo['order_no'])->setField('bd_remarks', $bdRemarks);
                    Db::commit();
                    // Process order notification
                    try {
                        $OrdersNotify->callBackOne($result);
                    } catch (Exception $e) {
                        // Log the exception or handle it as needed
                        // error_log($e->getMessage());
                    }
                    //添加今日成功订单数、今日成功金额
                    $this->modelEwmPayCode->where('id', $orderInfo['code_id'])->setInc('today_receiving_number');
                    $this->modelEwmPayCode->where('id', $orderInfo['code_id'])->setInc('today_receiving_amount', $orderInfo['order_price']);
                    $toady_success_count = Cache::get(date('Ymd',time()).'toady_success_count_msid_'.$orderInfo['gema_userid'].'_codeType_'.$orderInfo['code_type']);
                    Cache::set(date('Ymd',time()).'toady_success_count_msid_'.$orderInfo['gema_userid'].'_codeType_'.$orderInfo['code_type'],$toady_success_count+1);
                    $toady_success_money = Cache::get(date('Ymd',time()).'toady_success_money_msid_'.$orderInfo['gema_userid'].'_codeType_'.$orderInfo['code_type']);
                    Cache::set(date('Ymd',time()).'toady_success_money_msid_'.$orderInfo['gema_userid'].'_codeType_'.$orderInfo['code_type'],$toady_success_money+$orderInfo['order_price']);
                    return ['code' => CodeEnum::SUCCESS, 'msg' => '数据更新成功'];
                }
            }
        }



    }


    public function setOrderSucess($orderInfo, $note, $userid, $RemarkNmae = '码商')
    {
        $GemapayOrderModel = new \app\common\model\EwmOrder();
        $orderInfo = $GemapayOrderModel->lock(true)->find($orderInfo['id']);

        // Check if the order is locked
        if (!$orderInfo) {
            return ['code' => CodeEnum::ERROR, 'msg' => '订单已被锁定'];
        }

        // Check ms_money before the transaction


        $admin_sys_disable_ms_money = getAdminPayCodeSys('disable_ms_money_status',256,$orderInfo['admin_id']);
        if (empty($admin_sys_disable_ms_money)){
            $admin_sys_disable_ms_money = 2;
        }


        $ms_success_ver_money = getAdminPayCodeSys('ms_success_ver_money',256,$orderInfo['admin_id']);
        if (empty($ms_success_ver_money)){
            $ms_success_ver_money = 1;
        }

        $ms_djje_budan = getAdminPayCodeSys('ms_djje_budan',256,$orderInfo['admin_id']);
        if (empty($ms_djje_budan)){
            $ms_djje_budan = 2;
        }


        if ($ms_success_ver_money ==  1){
            $ms_money = $this->modelMs->where('userid', $orderInfo['gema_userid'])->value('money');
            if ($ms_money < $orderInfo['order_price']) {
                //判断是否开启冻结金额补单
                if ($ms_djje_budan == 1){
                    if ($admin_sys_disable_ms_money != 1){
                        return ['code' => CodeEnum::ERROR, 'msg' => '未开启码商进单冻结余额！'];
                    }
                    $ms_disable_money = $this->modelMs->where('userid', $orderInfo['gema_userid'])->value('disable_money');
                    if($ms_disable_money < $orderInfo['order_price']){
                        return ['code' => CodeEnum::ERROR, 'msg' => '码商余额和冻结余额不足'];
                    }
                }else{
                    return ['code' => CodeEnum::ERROR, 'msg' => '码商余额不足'];
                }

            }
        }



        $message = $orderInfo['out_trade_no'];
        // 开始事务
        $GemapayOrderModel->startTrans();
        try {
            // Your original database operation code
            //扣减码商余额
            if ($admin_sys_disable_ms_money != 1){
                if ($ms_success_ver_money == 1){
                    $ms_money = $this->modelMs->where('userid', $orderInfo['gema_userid'])->value('money');
                    if ($ms_money < $orderInfo['order_price']){
                        throw new \Exception('码商余额不足');
                    }
                }
                if (false == accountLog($orderInfo['gema_userid'], MsMoneyType::ORDER_DEPOSIT, MsMoneyType::OP_SUB, $orderInfo['order_price'], $message)) {
                    $GemapayOrderModel->where('id',$orderInfo['id'])->update(['note' => '扣减码订单商金额失败']);
                    throw new \Exception('扣减码订单商金额失败');
                }
            }else{

                if ($orderInfo['status'] ==3){
                    if (false == accountLog($orderInfo['gema_userid'], MsMoneyType::ORDER_DEPOSIT, MsMoneyType::OP_SUB, $orderInfo['order_price'], $message)) {
                        $GemapayOrderModel->where('id',$orderInfo['id'])->update(['note' => '扣减码订单商金额失败']);
                        throw new \Exception('扣减码订单商金额失败');
                    }

                }else{
                    if (false == accountLog($orderInfo['gema_userid'], MsMoneyType::ORDER_DEPOSIT, MsMoneyType::OP_SUB, $orderInfo['order_price'], $message, 'disable')) {
                        $GemapayOrderModel->where('id',$orderInfo['id'])->update(['note' => '扣减码订单商金额失败']);
                        throw new \Exception('扣减码订单商金额失败');
                    }
                }
            }

            //给码商返佣金
            $bonus = $this->orderProfit($orderInfo);
            if ($bonus === false) {
                throw new \Exception('发放佣金失败');
            }

            //扣除代理用户余额
            if ($this->logicConfig->isSysEnableRecharge()) {
                $rate_amount = Admin::getPayAdminRateMoney($orderInfo['admin_id'], $orderInfo['order_price'], $orderInfo['code_type']);
                if ($rate_amount) {
                    $admin = $this->modelAdmin->lock(true)->where('id', $orderInfo['admin_id'])->find();
                    if ($admin['balance'] < $rate_amount) {
                        throw new \Exception('代理商费用不足！');
                    }
                    $this->logicAdminBill->addBill($orderInfo['admin_id'], 3, 0, $rate_amount, $orderInfo['order_no']);
                }
            }

            $res = $GemapayOrderModel->setOrderSucess($orderInfo['id'], $note, $bonus);

            if ($res == false) {
                throw new \Exception('更新数据失败!');
            }
            $orderModel = model('orders');
            $result = $orderModel->where('trade_no', $orderInfo['order_no'])->find();
            if (!empty($result)){
                if ($result['status'] == 1 || $result['status'] == 0){
                    $OrdersNotify = new  \app\common\logic\OrdersNotify();
                    $bdRemarks = '后台确认收款通知支付系统单号'.$orderInfo['order_no'];
                    if ($userid != 0){
                        $bdRemarks = $RemarkNmae.'用户'.$userid.'确认收款通知支付系统单号'.$orderInfo['order_no'];
                    }

                    if(empty($bdRemarks))
                    {
                        throw new \Exception('必须填写备注');
                    }
                    $model = new \app\api\logic\Notify();
                    $res = $model->updateOrderInfo($result, 2);
                    $OrdersNotify->saveOrderNotify($result);
                    if ($res === true){
                        //单独修改补单备注(编辑封闭新增放开原则)todo 此处后期事务处理最好
                        $orderModel->where('trade_no', $orderInfo['order_no'])->setField('bd_remarks', $bdRemarks);
                        // Process order notification
//                        if($orderInfo['code_type'] == '56' || $orderInfo['code_type'] == '57'){
//                            try {
//                                $OrdersNotify->callBackOne($result);
//                            } catch (\Exception $e) {
//                                // Log the exception or handle it as needed
//                                // error_log($e->getMessage());
//                            }
//                        }
                        //添加今日成功订单数、今日成功金额
                        $this->modelEwmPayCode->where('id', $orderInfo['code_id'])->setInc('today_receiving_number');
                        $this->modelEwmPayCode->where('id', $orderInfo['code_id'])->setInc('today_receiving_amount', $orderInfo['order_price']);
                        $toady_success_count = Cache::get('toady_success_count_msid_'.$orderInfo['gema_userid'].'_codeType_'.$orderInfo['code_type']);
                        Cache::set('toady_success_count_msid_'.$orderInfo['gema_userid'].'_codeType_'.$orderInfo['code_type'],$toady_success_count+1);
                        $toady_success_money = Cache::get('toady_success_money_msid_'.$orderInfo['gema_userid'].'_codeType_'.$orderInfo['code_type']);
                        Cache::set('toady_success_money_msid_'.$orderInfo['gema_userid'].'_codeType_'.$orderInfo['code_type'],$toady_success_money+$orderInfo['order_price']);

                    }
                }
            }
            $GemapayOrderModel->commit(); // 提交事务
        } catch (\Exception $e) {
            $GemapayOrderModel->rollback(); // 回滚事务
            Log::error($orderInfo['out_trade_no'].'订单成功操作执行失败，原因： '.$e->getMessage() );
            return ['code' => CodeEnum::ERROR, 'msg' => '数据更新失败: ' . $e->getMessage()];
        }

        return ['code' => CodeEnum::SUCCESS, 'msg' => '数据更新成功'];
    }



    /**
     * 获取订单总金额
     */
    public function getTotalPrice($where)
    {
        $GemapayOrderModel = new \app\common\model\EwmOrder();
        //总订单金额 总订单数量
        $total_order = $GemapayOrderModel->where($where)
            ->field('cast(sum(order_price) AS decimal(15,2)) as  total_price, count(id) as total_number ')
            ->find()->toArray();
        //成功的订单
        $success_order = $GemapayOrderModel->where($where)->where(['status' => $GemapayOrderModel::PAYED])
            ->field('cast(sum(order_price) AS decimal(15,2)) as  success_price, count(id) as success_number ')
            ->find()->toArray();

        $result = [
            'total_price' => $total_order['total_price'] ? $total_order['total_price'] : 0,
            'total_number' => $total_order['total_number'] ? $total_order['total_number'] : 0,
            'success_price' => $success_order['success_price'] ? $success_order['success_price'] : 0,
            'success_number' => $success_order['success_number'] ? $success_order['success_number'] : 0,
        ];
        return $result;
    }


    /**
     *
     * 获取订单列表
     *
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int $paginate
     * @return mixed
     * @author 勇敢的小笨羊
     */
    public function getOrderList($where = [], $field = true, $order = 'add_time desc', $paginate = 1)
    {
        $this->modelEwmOrder->alias('a');
        $this->modelEwmOrder->limit = !$paginate;
        $this->modelEwmOrder->append = ['add_time', 'visite_time', 'pay_time'];
        $join = [
            ['ewm_pay_code b', 'a.code_id = b.id', 'left'],
            ['ms c', 'a.gema_userid = c.userid', 'left'],
            ['ewm_block_ip eo', 'eo.block_visite_ip = a.visite_ip', 'left'],
            ['orders o','a.order_no = o.trade_no','left'],
            ['user u','o.uid = u.uid','left'],
            ['orders_notify n','o.id = n.order_id','left'],
            ['pay_code p','a.code_type = p.id','left'],
            ['admin m','m.id = a.admin_id','left'],
//            ['banktobank_sms s','a.id = s.order_id','left'],
//            ['ms m', 'c.pid = m.userid', 'left'],
        ];
        $this->modelEwmOrder->join = $join;
        return $this->modelEwmOrder->getList($where, $field, $order, $paginate);
    }

    /**
     * 获取单总数
     *
     * @param $where
     * @return mixed
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function getOrdersCount($where = [])
    {
        return $this->modelEwmOrder->alias('a')
            ->where($where)
            ->join('ewm_pay_code b', 'a.code_id = b.id', 'left')
            ->join('ms c', 'a.gema_userid = c.userid', 'left')
            ->join('orders o','a.order_no = o.trade_no','left')
            ->join('pay_code p','b.code_type = p.id','left')
            ->join('admin m','m.id = a.admin_id','left')
            ->join('orders_notify n','o.id = n.order_id','left')
            ->count();

    }


    /**
     * 管理员完成订单+补单
     * @param $orderId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function setOrderSucessByAdmin($orderId, $coerce = 0)
    {
        //判断订单状态
        $GemaPayOrder = new \app\common\model\EwmOrder();

        $where['id'] = $orderId;
        $where['status'] = $GemaPayOrder::WAITEPAY;
        //判断是否强制补单
        if (1) {
            unset($where['status']);
        }

        Db::startTrans();
        $orderInfo = $GemaPayOrder->where($where)->lock(true)->find();
        if ($orderInfo['status'] == $GemaPayOrder::PAYED) {
            Db::rollback();
            return ['code' => CodeEnum::ERROR, 'msg' => '订单已完成'];
        }
        Db::commit();



        if (empty($orderInfo)) {
            return ['code' => CodeEnum::ERROR, 'msg' => '订单信息有误'];
        }

        //判断用户余额是否足够
        return $this->setOrderSucess($orderInfo, "用户手动调单",0);
    }


    /**
     * 取消订单
     * @param $order
     */
    public function cancleOrder($order)
    {
        Db::startTrans();
        $GemapayOrderModel = new \app\common\model\EwmOrder();
        $where["order_no"] = $order;
        $order = $GemapayOrderModel->where($where)->lock(true)->find();

        if (empty($order) || $order['status'] != $GemapayOrderModel::WAITEPAY) {
            Db::rollback();
            return false;
        }
        //取消订单
        $statusRet = $GemapayOrderModel->where(['id' => $order['id']])->setField('status', 2);

        if ($statusRet != false) {
            //如果为二维码订单 记录失败次数
            if (in_array($order['code_type'], ["1", "2", "3","4"])) {
                $this->recordFailedNum($order['code_id'], false, $order['admin_id']);
            }

            //记录日志
            /*   $message = "关闭订单：" . $order['order_no'];
               if ($order['gema_userid']) {
                   //如果没有配置或者配置为1 的话 抢单扣除余额 那关闭订单需要返回余额
                   $ret = accountLog($order['gema_userid'], MsMoneyType::ORDER_DEPOSIT_BACK,
                       MsMoneyType::OP_ADD, $order['order_price'], $message);
                   if ($ret != false) {
                       Db::commit();
                       return true;
                   }
               }*/
        }
        Db::rollback();
        return false;
    }


    /**
     * 10分钟关闭码商二维码订单
     */
    public function closeOrder()
    {

        $indate = 10;
        $where = [];
        $GemapayOrderModel = new \app\common\model\EwmOrder();
        $where['code_type'] = 3;
        $where['add_time'] = ['lt', time() - (60 * $indate)];
        $where['status'] = $GemapayOrderModel::WAITEPAY;
        $orderList = $GemapayOrderModel->where($where)->select();
        if ($orderList) {
            foreach ($orderList as $k => $v) {
                $res = $this->cancleOrder($v['order_no']);
            }
        }
echo 3;
        $GemapayOrderModel = new \app\common\model\EwmOrder();
        $where['code_type'] = 4;
        $where['add_time'] = ['lt', time() - (60 * 5)];
        $where['status'] = $GemapayOrderModel::WAITEPAY;
        $orderList = $GemapayOrderModel->where($where)->select();
        if ($orderList) {
            foreach ($orderList as $k => $v) {
                $res = $this->cancleOrder($v['order_no']);
            }
        }
    }


    /**
     * 获取夜间加成后的码商费率
     * @param $ms_id
     * @param $code_type_id
     * @return false|float|mixed|string
     */
    public function getAfterAddMsRate($ms_id){
        $rate = 0;
            $admin_id  = $this->modelMs->where('userid', $ms_id)->value('admin_id');
            $start_time_h = getAdminPayCodeSys('ms_rate_night_add_start_time_h', 256, $admin_id);
            $end_time_h = getAdminPayCodeSys('ms_rate_night_add_end_time_h', 256, $admin_id);

            if ($start_time_h && $end_time_h && is_numeric($start_time_h)
                && is_numeric($end_time_h)
            ){
                $Date = date('Y-m-d ',time());
                $start_time_h = strtotime($Date.$start_time_h. '00');
                $end_time_h = $end_time_h == '00' ?strtotime($Date.$end_time_h.'00')+86400 : strtotime($Date.$end_time_h.'00');
                $curr_time = strtotime(date('Y-m-d H:i',time()));
                if ($curr_time>=$start_time_h && $curr_time<=$end_time_h){
                    $ms_rate_night_add = getAdminPayCodeSys('ms_rate_night_add', 256, $admin_id);
                    $ms_rate_night_add && $rate+=  $ms_rate_night_add;
                }
            }

        return $rate;
    }
}
