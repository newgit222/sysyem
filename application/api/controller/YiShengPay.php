<?php

namespace app\api\controller;

use app\common\controller\BaseApi;
use app\common\logic\EwmOrder;
use think\Db;

class YiShengPay extends BaseApi
{

    public function callback(){
        $start = time()-360;
        $end = time();
        $codes = Db::name('ewm_pay_code')
            ->where([
                'code_type'=>55,
                'status'=>1,
            ])
            ->order('id desc')
            ->field('id,account_number,bank_name')
            ->select();
        if (empty($codes)){
            return 'null';
        }
        foreach ($codes as $key=>$val){
            $is_order = Db::name('ewm_order')
                ->where([
                    'code_type'=>55,
                    'status'=>0,
                    'code_id' => $val['id']
                ])
                ->where('add_time','between',[$start,$end])
                ->order('id desc')
                ->find();
            if (empty($is_order)){
                unset($codes[$key]);
            }
        }
        if (empty($codes)){
            return 'null orders';
        }

        foreach ($codes as $k=>$v){
//            $v['extra']
            $res = $this->getBills($v['account_number'],$v['id'],$v['bank_name'],'');
            if ($res['code'] == 1){
                foreach ($res['data'] as $key=>$val){
                    $find_order = Db::name('ewm_order')
                        ->where([
                            'code_id'=>$v['id'],
                            'code_type'=>55,
                            'order_pay_price'=>sprintf("%.2f",$val['amount']/100),
                            'status'=>0,
                        ])
                        ->where('add_time','between',[$start,$end])
                        ->order('id desc')
                        ->find();

                    if (!empty($find_order)){
                        $ewm = new EwmOrder();
                        $ewm->setOrderSucess($find_order, "协议自动回调",$find_order['gema_userid'],'协议');
                        continue;
                    }
                    continue;
                }
                continue;
            }
            continue;
        }
        echo  'SUCCESS';
    }


    private function getBills($Authorization,$code_id,$bank_name,$Cookie){
        $url = 'https://b2b.eycard.cn/api/system/trade';
        $start = date('Y-m-d H:i:s',time()-360);
        $end = date('Y-m-d H:i:s',time());
        $param = [
            'type' => '1,2',
            'tradeTime' => $start.','.$end,
            'sort' => 'id,desc',
            'page' => 0,
            'size' => 20
        ];
        // print_r(http_build_query($param));die;
        $header = [
            'Content-Type: application/json; charset=utf-8',
            'Authorization: '.$Authorization,
        //    'Cookie: '.$Cookie
        ];
        $res = $this->http($url,$param,'GET',$header);
        $res = json_decode($res,true);
        if (!empty($res['status']) && $res['status'] == 200){
                $bills = $res['data']['content'];
                foreach ($bills as $k=>$v){
                    if ($v['state'] != 00){
                        unset($bills[$k]);
                    }
                    if ($v['termName'] != $bank_name){
                        unset($bills[$k]);
                    }

                }
               if (empty($bills)){
                   return ['code'=>0,'msg'=>'没有账单'];
               } else{
                   return ['code'=>1,'msg'=>'ok','data'=>$bills];
               }
        }else{
            Db::name('ewm_pay_code')->where('id',$code_id)->update(['status'=>0]);
        }

        // print_r($res);die;

    }






    /**
     * 发送HTTP请求方法
     * @param  string $url    请求URL
     * @param  array  $params 请求参数
     * @param  string $method 请求方法GET/POST
     * @param  array $header 请求头
     * @param  bool $multi 是否传输文件
     * @return array  $data   响应数据
     */
    private function http($url, $params, $method = 'GET', $header = array(), $multi = false){
        $opts = array(
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER     => $header
        );
        /* 根据请求类型设置特定参数 */
        switch(strtoupper($method)){
            case 'GET':
                $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
                break;
            case 'POST':
                //判断是否传输文件
                $params = $multi ? $params : http_build_query($params);
                $opts[CURLOPT_URL] = $url;
                $opts[CURLOPT_POST] = 1;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            default:
                throw new Exception('不支持的请求方式！');
        }
        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data  = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if($error) throw new Exception('请求发生错误：' . $error);
        return  $data;
    }

}