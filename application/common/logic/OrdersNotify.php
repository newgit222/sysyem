<?php

/**
 *  +----------------------------------------------------------------------
 *  | 狂神系统系统 [ WE CAN DO IT JUST THINK ]
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2018 http://www.iredcap.cn All rights reserved.
 *  +----------------------------------------------------------------------
 *  | Licensed ( https://www.apache.org/licenses/LICENSE-2.0 )
 *  +----------------------------------------------------------------------
 *  | Author: Brian Waring <BrianWaring98@gmail.com>
 *  +----------------------------------------------------------------------
 */

namespace app\common\logic;


use app\common\library\exception\OrderException;
use GuzzleHttp\Client;
use think\Db;
use think\Log;

class OrdersNotify extends BaseLogic
{

    /**
     *
     * 获取订单通知列表
     *
     * @author 勇敢的小笨羊
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int $paginate
     * @return mixed
     */
    public function getOrderList($where = [], $field = true, $order = 'create_time desc', $paginate = 15)
    {
        $this->modelOrdersNotify->limit = !$paginate;
        return $this->modelOrdersNotify->getList($where, $field, $order, $paginate);
    }

    /**
     * 获取订单通知信息
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @param bool $field
     *
     * @return mixed
     */
    public function getOrderInfo($where = [], $field = true){
        return $this->modelOrdersNotify->getInfo($where, $field);
    }

    /**
     * 获取订单通知总数
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $where
     * @return mixed
     */
    public function getOrdersCount($where = []){
        return $this->modelOrdersNotify->getCount($where);
    }

    /**
     * 新增或者修改通知信息 【命令行里无module】
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $order
     *
     */
    public function saveOrderNotify($order){

        //TODO 修改数据
        Db::startTrans();
        //数据提交
        try{

            $this->modelOrdersNotify->setInfo([ 'order_id'   => $order['id']]);

            Db::commit();

        }catch (\Exception $e) {
            Db::rollback();
            //记录日志
            Log::error("Creat Balance Change Error:[{$e->getMessage()}]");
        }
    }



    public function callBackOne($data){
        $data = json_decode(json_encode($data),true);
        $client = new Client();
        $where = array();
        $where['uid'] = $data['uid'];
        $LogicApi = new \app\common\logic\Api();
        $appKey = $LogicApi->getApiInfo($where, "key",($data['uid']==100063||$data['uid']==100068|| $data['uid']==100067));
        $to_sign_data =  $this->buildSignData($data, $appKey["key"],($data['uid']==100063||$data['uid']==100068|| $data['uid']==100067));
       // print_r($data);die;
//        $response = $client->request(
//            'POST', $data['notify_url'], ['form_params' => $to_sign_data,'timeout'=>3]
//        );
        if ($data['uid'] == '29'){
            $response = self::curlPost('http://68.178.164.187/zz.php?notify_url='.urlencode($data['notify_url']),$to_sign_data);
        }else{
            $response = self::curlPost($data['notify_url'],$to_sign_data);
        }

//        $statusCode = $response->getStatusCode();
//        $contents = $response->getBody()->getContents();
//        Log::notice("订单异步队列回调 notify url " . $data['notify_url'] . "data" . json_encode($to_sign_data).'返回内容:'.$contents);
//        Log::notice("response code: ".$statusCode." response contents: ".$contents);
      //  print("<info>response code: ".$statusCode." response contents: ".$contents."</info>\n");
//        if ( $statusCode == 200 && !is_null($contents)){
            //判断放回是否正确
//                    if ($contents == "SUCCESS"){
            //TODO 更新写入数据
        Log::error(date('Y-m-d H:i:s',time()).'完成订单回调：'. $data['out_trade_no'] . '第1次回调，返回内容：'.$response);
        if ($response == 'SUCCESS'){
            $result =  [
                'times' => 1,
                'result'   => $response,
                'is_status'   => 200
            ];
//            $order = (new \app\common\model\OrdersNotify())->where(['order_id' => $data['id']]);
//            $order->update($result);
        }else{
            $result =  [
                'times' => 1,
                'result'   => $response,
                'is_status'   => 404,
                'update_time' => time()
            ];
        }
        $order = (new \app\common\model\OrdersNotify())->where(['order_id' => $data['id']]);
        $order->update($result);
//            $result =  [
//                'times' => 1,
//                'result'   => $contents,
//                'is_status'   => $statusCode
//            ];


//        }

    }

    const SUSSCE = 1;
    /**
     * 构建返回数据对象
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $data
     * @return array
     */
    protected function buildSignData($data,$md5Key,$need_remark=false){
        //除去不需要字段
        unset($data['id']);
        unset($data['uid']);
        unset($data['cnl_id']);
        unset($data['puid']);
        unset($data['status']);
        unset($data['create_time']);
        unset($data['update_time']);
        unset($data['update_time']);
        unset($data['income']);
        unset($data['user_in']);
        unset($data['agent_in']);
        unset($data['platform_in']);
        unset($data['currency']);
        unset($data['client_ip']);
        unset($data['return_url']);
        unset($data['notify_url']);
        unset($data['extra']);
        unset($data['subject']);
        unset($data['bd_remarks']);
        unset($data['remark']);
        unset($data['visite_show_time']);
        unset($data['real_need_amount']);
        unset($data['image_url']);
        unset($data['request_log']);
        unset($data['visite_time']);
        unset($data['request_elapsed_time']);

        $data['amount'] = sprintf("%.2f", $data['amount']);
        $data['order_status'] = self::SUSSCE;
        ksort($data);

        $signData = "";
        foreach ($data as $key=>$value)
        {
            $signData = $signData.$key."=".$value;
            $signData = $signData . "&";
        }
        $str = $signData."key=".$md5Key;

        $sgin = md5($str);
        $data['sign'] = $sgin;
        //返回
        return $data;
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
    private static function curlPost($url = '', $postData = '', $options = array(),$timeOut=3)
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
}