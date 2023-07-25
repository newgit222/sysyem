<?php


namespace app\common\model;

use think\Db;
use think\Log;

/***
 * 码商二维码模型
 * Class EwmPayCode
 * @package app\common\model
 */
class EwmPayCode extends BaseModel
{

    protected $autoWriteTimestamp = false;


    //打开中
    const STATUS_ON = 0;

    //关闭中
    const STATUS_OFF = 1;

    //支付中
    const STATUS_PAYING = 1;

    //空闲中　
    const STATUS_NOPAYING = 0;

    //关闭中
    const STATUS_CLOSE = 2;

    //二维码生成订单最大个数
    const LIMIT_NUM = 200;

    //每个金额最大个数
    const MONEY_LIMIT_NUM = 10;

    //每个码最大收款额
    const CODE_MONEY_LIMIT = 10000;
    // 定义与订单表的一对多关联
    public function orders()
    {
        return $this->hasMany(EwmOrder::class, 'code_id');
    }

    function getAviableCodeV2($money = null, $type = 3, $admin_id = 1)
    {
        //二维码类型
//        $where["code.type"] = $type;

        //二维码激活
        $where["code.status"] = self::STATUS_YES;

        //二维码没有被锁定
        $where["code.is_lock"] = self::STATUS_NO;

        //二维码没被删除
        $where["code.is_delete"] = self::STATUS_NO;

        //余额足够
        if ($money) {
            $where["u.money"] = array('gt', $money);
        }

//        $where['u.add_admin_id'] = $admin_id;
        //用户正常开工
        $where["u.status"] = self::STATUS_YES;

        //用户工作状态
        $where["u.work_status"] = self::STATUS_YES;

        $order = "code.order_today_all ASC";
        //选内容
        $fileds = [
            "code.*",
        ];
        $this->join('cm_ms u', "u.userid=code.ms_id", "LEFT");
        $data = $this->alias('code')->field($fileds)->where($where)->order($order)->select();
         return $data;
    }
    /**
     * 获取一个最优使用的二维码
     * @param $money
     * @param null $type
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function getAviableCode($money, $type, $member_id)
    {

	$EwmOrderModel = new EwmOrder();
	//EwmOrder
	$ignore_money = 0;
	if(!$ignore_money)
	{
        if ($type == 30){
            $codes = $EwmOrderModel->where(' code_id > 0 and order_pay_price = "' . $money . '" and status = "' . $EwmOrderModel::WAITEPAY . '" and  add_time > "' . (time() - 600) . '" and  code_id > 0 and order_pay_price = "' . $money . '" and code_type = "'. $type .'" ')->select();
            if (!empty($codes)) {
                $ids = [];
                foreach ($codes as $code) {
                    if (!empty($code['code_id'])) {
                        $ids[] = $code['code_id'];
                    }
                }
                $ids = array_unique($ids);
                if (!empty($ids)) {
                    $where["code.id"] = array("not in", $ids);
                }
            }
        }else{
            $codes = $EwmOrderModel->where(' code_id > 0 and order_pay_price = "' . $money . '" and status = "' . $EwmOrderModel::WAITEPAY . '" and  add_time > "' . (time() - 600) . '" and  code_id > 0 and order_pay_price = "' . $money . '" and code_type = "'. $type .'" ')->select();
            if (!empty($codes)) {
                $ids = [];
                foreach ($codes as $code) {
                    if (!empty($code['code_id'])) {
                        $ids[] = $code['code_id'];
                    }
                }
                $ids = array_unique($ids);
                if (!empty($ids)) {
                    $where["code.id"] = array("not in", $ids);
                }
            }
        }

//	var_dump($where);die();
	}
//        Log::error('mashang:'.json_encode($member_id,true));
	$where['status'] = 1;
	$where["ms_id"]=['in',$member_id];
        $where["is_lock"] = self::STATUS_NO;
        $where["is_delete"] = self::STATUS_NO;

        $where["code_type"] = $type;
        $order = 'id asc';
//
//        $where['min_money'] = ['egt',$money];
//        $where['max_money'] = ['elt',$money];
        $fileds = [
            "code.*",
        ];

        $data = $this->alias('code')->field($fileds)->where($where)->order($order)->select();
//        Log::error('二维码:'.json_encode($data,true));

        $r_data = [];
        //检查日限额	
        foreach($data as $d)
        {
         if($d['limit__total'] != 0)
         {
            $money_today = $EwmOrderModel
            ->where('code_id='.$d['id'].' and status =1 and add_time>'.strtotime(date('Y-m-d')))
            ->sum('order_pay_price');
            if( $money+$money_today >$d['limit__total'] ){
                  continue;
            }

         }

      /*   if (!empty($d['min_money'] ) &&$d['min_money'] * 100 !=0){
             if ($money < $d['min_money']){
                 continue;
             }
	 }*/
         if (  !empty($d['max_money'] )&&  $d['max_money'] * 100 != 0){
             if ($money > $d['max_money']){
                 continue;
             }
         }


         if ($d['success_order_num'] != 0){
             $code_num = $EwmOrderModel->where('code_id',$d['id'])->count();
             if ($code_num >= $d['success_order_num']){
                 continue;
             }
         }


          $r_data[]=$d;
        }
	
	return $r_data;
    }

    /**
     * 处理订单金额重复 --12、10/luomu
     */

    function getRandomCodeV2($type,$member_id){
        $msCodes =$this
            ->where('code_type',$type)
            ->where('is_delete',0)
            ->where('is_lock',0)
            ->where('status',1)
            ->where('ms_id','in',$member_id)
            ->select();
        if (empty($msCodes)){
            return false;
        }
        return $msCodes;
    }

//    function getRandomCodeV2($type, $member_id) {
//        $batchSize = 100; // 每个批次查询的数量
//        $allMsCodes = []; // 存储所有查询结果
//
//        $page = 1; // 初始页码
//        do {
//            $start = ($page - 1) * $batchSize;
//
//            $msCodes = $this
//                ->where('code_type', $type)
//                ->where('is_delete', 0)
//                ->where('is_lock', 0)
//                ->where('status', 1)
//                ->whereIn('ms_id', $member_id)
//                ->limit($start, $batchSize)
//                ->select();
//
//            foreach ($msCodes as $code) {
//                $allMsCodes[] = $code;
//            }
//
//            $page++;
//        } while (count($msCodes) === $batchSize); // 继续查询直到返回的结果数量不满一个批次
//
//        if (empty($allMsCodes)) {
//            return false;
//        }
//
//        return $allMsCodes;
//    }







    function getAlipayPinMoneyCodeV2($type,$member_id){
        $msCodes =$this
            ->where('code_type',$type)
            ->where('is_delete',0)
            ->where('is_lock',0)
            ->where('status',1)
            ->where('ms_id','in',$member_id)
            ->where('create_time','>',time()-420)
            ->select();
        if (empty($msCodes)){
            return false;
        }
        return $msCodes;
    }


    /**
     * 处理订单金额重复 --12、10/luomu
     */

    function getRandomCodeV3($type,$member_id,$apimoney){
        $msCodes =$this
            ->where('code_type',$type)
            ->where('is_delete',0)
            ->where('is_lock',0)
            ->where('status',1)
            ->where('ms_id','in',$member_id)
            ->where('api_pay_money','eq',$apimoney)
//            ->order('today_receiving_number')
            ->select();
        if (empty($msCodes)){
            return false;
        }
        return $msCodes;
    }




    /**
     * @user luomu
     * @return
     * @time
     * 处理订单金额重复
     */

    function getRandomCode($money,$type,$member_id,$admin_id){
        $EwmOrderModel = new EwmOrder();
        $where =[];
        $data = $EwmOrderModel->where('gema_userid','in',$member_id)
            ->where('order_pay_price',$money)
            ->where('add_time','>',time()-380)
            ->where('status',0)
            ->where('code_type',$type)
            ->field('code_id')
            ->select();

        if (!empty($data)){
            array_unique($data);
            foreach ($data as $v){
                $ids[]=$v['code_id'];
                $where['id'] = ['not in',$ids];
            }
        };
        Log::error('找不可用二维码：'.json_encode($data,true));

        $this->startTrans();
        try {
            $msCodes =$this->lock(true)
                ->where($where)
                ->where('code_type',$type)
                ->where('is_delete',0)
                ->where('is_lock',0)
                ->where('status',1)
                ->where('ms_id','in',$member_id)
                ->select();
            Log::error('所有可用二维码:'.json_encode($msCodes));
            if (empty($msCodes)){
                $this->rollback();
                return false;
            }
            $r_data = [];
            foreach($msCodes as $d){
                if($d['limit__total'] != 0) {
                    $money_today = $EwmOrderModel
                        ->where('code_id='.$d['id'].' and status =1 and add_time>'.strtotime(date('Y-m-d')))
                        ->sum('order_pay_price');
                    if( $money+$money_today >$d['limit__total'] ){
                        continue;
                    }

                }

                if (!empty($d['max_money'] )&&  $d['max_money'] * 100 != 0){
                    if ($money > $d['max_money']){
                        continue;
                    }
                }


                if ($d['success_order_num'] != 0){
                    $code_num = $EwmOrderModel->where('code_id',$d['id'])->count();
                    if ($code_num >= $d['success_order_num']){
                        continue;
                    }
                }
                $r_data[] = $d;
            }
            Log::error('取出的所有账号：'.json_encode($r_data,true));
            if (empty($r_data)){
                $this->rollback();
                return false;
            }

            //打乱返回的所有可用的账户
            shuffle($r_data);
            $id = reset($r_data);
            $this->commit();
        } catch (\Exception $e) {
            // 回滚事务
            $this->rollback();
        }


        return $id;
    }

    /**
     * 增加ｃｏｄｅ支付个数
     * @param $id
     * @return false|int
     */
    public function incTodayOrder($id)
    {
        $where = [
            'id' => $id
        ];
        $ret   = $this->where($where)->setInc("order_today_all");
        return $ret;
    }

}
