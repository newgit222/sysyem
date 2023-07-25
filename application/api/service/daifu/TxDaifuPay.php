<?php

namespace app\api\service\daifu;
use app\api\service\DaifuPayment;
use think\Db;
use think\Log;
class TxDaifuPay extends DaifuPayment
{
    private static $yh_code = [
        '工商银行' => 'ICBC',
        '招商银行' => 'CMB',
        '建设银行' => 'CCB',
        '中国银行' => 'BOC',
        '农业银行' => 'ABC',
        '交通银行' => 'BCM',
        '上海浦东发展银行' => 'SPDB',
        '广东发展银行' => 'CGB',
        '光大银行' => 'CEB',
        '兴业银行' => 'CIB',
        '平安银行' => 'PAB',
        '民生银行' => 'CMBC',
        '华夏银行' => 'PSBC',
        '中国邮政储蓄银行' => 'PSBC',
        '宁波银行' => 'NBBANK',
        '北京银行' => 'BJBANK',
        '浙商银行' => 'CZBANK',
        '广州银行' => 'GCB',
        '长沙银行' => 'CSCB',
        '广西北部湾银行' => 'BGB',
        '广东农信' => 'GDRC',
        '桂林银行' => 'GLB',
        '东莞银行' => 'BOD',
        '杭州银行' => 'HZCB',
        '江苏银行' => 'JSBC',
        '广西农信银行' => 'GXRCU',
        '吉林银行' => 'JLBANK',
        '南京银行' => 'NJCB',
        '恒丰银行' => 'EGBANK',
        '深圳农村商业银行' => 'SRCB',
        '中信银行' => 'CNCB',
        '武汉农商银行' => 'WHRCB',
        '威海银行' => 'WHCCB',
        '上海银行' => 'SHBANK',
        '哈尔滨银行' => 'HRBANK',
        '青岛银行' => 'QDCCB',
        '莱商银行' => 'LSBANK',
        '齐鲁银行' => 'QLBANK',
        '烟台银行' => 'YTBANK',
        '柳州银行' => 'LZCCB',
        '重庆农村商业银行' => 'CRCBANK',
        '北京商业银行' => 'BCCB',
        '甘肃省农村信用社' => 'GSNX',
        '湖北银行' => 'HBC',
        '东莞农村商业银行' => 'DRCB',
        '湖南省农村信用社' => 'HUNNX',
        '泰隆银行' => 'ZJTLCB',
        '稠州银行' => 'CZCB',
        '自贡银行' => 'ZGCCB',
        '民泰银行' => 'MTBANK',
        '银座村镇银行' => 'YZBANK',
        '营口银行' => 'BOYK',
        '兰州银行' => 'LZYH',
        '临商银行' => 'LSBC',
        '江西银行' => 'NCB',
        '江西省农村信用社' => 'JXRCU',
        '江苏省农村信用社联合社' => 'JSRCU',
        '华融湘江银行' => 'HRXJB',
        '湖北省农信社' => 'HURCB',
        '大连银行' => 'DLB',
        '沧州银行' => 'BOCZ',
        '重庆三峡银行' => 'CCQTGB',
        '东营银行' => 'DYCCB',
        '广东省农村信用社联合社' => 'GDRCC',
        '广州农商银行' => 'GRCB',
        '蒙商银行' => 'PERB',
        '长春经开融丰村镇银行' => 'CCRFCB',
        '保定银行' => 'BOB',
        '重庆银行' => 'CQBANK',
        '张家口银行' => 'ZJKCCB',
        '天津银行' => 'TCCB',
        '邯郸银行' => 'HDBANK',
        '富滇银行' => 'FDB',
        '承德银行' => 'CDBANK',
        '浙江农村信用合作社' => 'ZJNX',
        '安徽省农村信用社' => 'ARCU',
        '阳光村镇银行' => 'YGCZYH',
        '福建农村信用社' => 'FJNX',
        '阜新银行' => 'BOFX',
        '赣州银行' => 'GZB',
        '乐山商业银行' => 'LSCCB',
        '广东南粤银行' => 'GDNY',
        '顺德农村商业银行' => 'SDEBANK',
        '广东华兴银行' => 'GHB',
        '广东省农村信用社' => 'GDRCU',
        '珠海华润银行' => 'CRBANK',
        '南海农商银行' => 'NRCB',
        '常熟农商银行' => 'CSRCB',
        '成都银行' => 'CDCB',
        '汉口银行' => 'HKB',
        '杭州联合银行' => 'URCB',
        '江南农村商业银行' => 'JNRCB',
        '江苏省农村信用社' => 'JSNX',
        '九江银行' => 'JJCCB',
        '辽宁省农村信用社' => 'INRCC',
        '洛阳银行' => 'LYBANK',
        '齐商银行' => 'QSB',
        '青岛农商银行' => 'QRCB',
        '日照银行' => 'RZB',
        '厦门国际银行' => 'XMIB',
        '厦门银行' => 'BOXM',
        '山东省农村信用社' => 'SDRCU',
        '上海农商银行' => 'SRCBANK',
        '云南农村信用社' => 'YNRCC',
        '深圳前海微众银行' => 'WEBANK',
        '盛京银行' => 'SJBANK',
        '四川省农村信用社' => 'SCRCU',
        '四川天府银行' => 'PWEB',
        '苏州银行' => 'BOSZ',
        '台州银行' => 'TZCB',
        '微商银行' => 'SBANK',
        '温州银行' => 'WZCB',
        '萧山农商银行' => 'ZJXSBANK',
        '长沙农商银行' => 'CRCB',
        '郑州银行' => 'ZZBANK',
        '中原银行' => 'ZYBANK',
        '紫金农商银行' => 'ZJRCBANK',
        '河南省农村信用社' => 'HNRCU',
        '渤海银行' => 'CBHB',
        '龙江银行' => 'LJBANK',
        '黑龙江农信' => 'HLJRCU',
        '辽阳银行' => 'LYCB',
        '丹东银行' => 'BODD',
        '大连农商银行' => 'DLRCB',
        '甘肃银行' => 'GSBANK',
        '贵阳银行' => 'GYCB',
        '贵州银行' => 'ZYCBANK',
        '桂林国民银行' => 'GLGM',
        '徽商银行' => 'HSBANK',
        '济宁银行' => 'BOJN',
        '廊坊银行' => 'LANGFB',
        '长安银行' => 'CCAB',
        '西安银行' => 'XABANK',
        '包商银行' => 'BSB',
        '本溪银行' => 'BOBZ',
        '达州银行' => 'BODZ',
        '成都农商银行' => 'CDRCB',
        '东亚银行' => 'CDRCB',
        '抚顺银行' => 'FSCB',
        '贵州省农村信用社联合社' => 'GZRCU',
        '河北银行' => 'BHB',
        '吉林农信银行' => 'JLRCU',
        '江苏农商银行' => 'JRCB',
        '金华银行' => 'JHBANK',
        '锦州银行' => 'BOJZ',
        '昆仑银行' => 'KLB',
        '昆山农商银行' => 'KSRB',
        '青海省农村信用社' => 'QHRC',
        '上虞农商银行' => 'SYCB',
        '绍兴银行' => 'SXCB',
        '太仓农商银行' => 'TCRCB',
        '泰安银行' => 'TACCB',
        '天津农商银行' => 'TRCB',
        '邢台银行' => 'XTB',
        '张家港农商银行' => 'RCBOZ',
        '长江商业银行' => 'JSCCB',
        '绵阳商业银行' => 'MYCC',
        '山西农信' => 'SRCU',
        '阳泉商业银行' => 'YQCCB',
        '桦甸惠民村镇银行' => 'HDHMB',
        '海南农信' => 'HNB',
        '黄河农信银行' => 'HHNX',
        '江门农商银行' => 'JMRCB',
        '晋城银行' => 'JCCB',
        '宁夏银行' => 'NXBANK',
        '宁夏黄河农村商业银行' => 'NXRCU',
        '深圳福田银座村镇银行' => 'FTYZB',
        '石嘴山银行' => 'SZSCCB',
        '苏州农商银行' => 'WJRCB',
        '天津滨海农村商业银行' => 'TJBHB',
        '枣庄银行' => 'ZZB',
        '浙江网商银行' => 'MYBANK',
        '广东农商银行' => 'GZRCB',
        '海南银行' => 'HNBANK',
        '五常惠民村镇银行' => 'WCHMB',
        '唐山银行' => 'TSB',
        '北京农村商业银行' => 'BJRCB',
        '南阳村镇银行' => 'NYCBANK',
        '鞍山银行' => 'ASBANK',
        '中银富登村镇银行' => 'BOCFTB',
        '衡水银行' => 'HSB',
        '平顶山银行' => 'PDSB',
        '内蒙古农村信用社' => 'NMGNXS',
        '内蒙古银行' => 'BOIMC',
        '惠水恒升村镇银行' => 'HSHSB',
        '朝阳银行' => 'CYCB',
        '江西农商银行' => 'JXNXS',
        '贵阳农商银行' => 'GYNSH',
        '泉州银行' => 'QZCCB',
        '安图农商村镇银行' => 'ATCZB',
        '广西农村信用社' => 'GXNX',
        '融兴村镇银行' => 'RXVB',
        '宁波通商银行' => 'NCBANK',
        '天津宁河村镇银行' => 'NINGHEB',
        '广州增城长江村镇银行' => 'ZCCJB',
        '鄞州银行' => 'BEEB',
        '海丰农商银行' => 'HFRCB',
        '上饶银行' => 'SRBANK',
        '黄梅农村商业银行' => 'HMRCB',
        '陕西省农村信用社联合社' => 'SXRCCU',
        '密山农商银行' => 'MSRCB',
        '义乌联合村镇银行' => 'ZJYURB',
        '广东普宁汇成村镇银行' => 'GDPHCB',
        '海南省农村信用社联合社' => 'HAINANBANK',
        '浙江诸暨联合村镇银行' => 'ZJURB',
        '东阳农商银行' => 'ZJDYB',
        '日照沪农商村镇银行' => 'SRCBCZ',
        '浙江农商银行' => 'ZJRCB',
        '鄂尔多斯银行' => 'ORDOSB',
        '永州农村商业银行' => 'HNNXS',
        '福建省农村信用社联合社' => 'FJNXS',
        '潮州农商银行' => 'GRCBANK',
        '义乌农商银行' => 'YRCBANK',
        '海口联合农商银行' => 'UNITEDB',
        '瑞丰银行' => 'BORF',
        '南海农信' => 'NRCBANK',
        '山西银行' => 'SHXIBANK',
        '盘锦银行' => 'PJBANK',
        '海南省农村信用社' => 'HAINANNONGXIN',
        '青田农商银行' => 'QTRCB',
        '河北省农村信用社' => 'HEBNX',
        '海南农村信用社' => 'HNBB',
        '海峡银行' => 'FJHXBANK',
        '上海农村商业银行' => 'SHRCB',
        '四川银行' => 'SCB',
        '江南村镇银行' => 'JRCCB',
        '顺德农商银行' => 'SDNBANK',
        '安徽怀远农商行' => 'AHHYRCB',
        '新疆农村信用社' => 'XJRCU',
        '长春二道农商村镇银行' => 'CCEDCB',
        '山西省农村信用社' => 'SXRCU',
        '吉林省农村信用社' => 'JLRCUU',
        '昆山农信社' => 'KRCB',
        '东莞农村银行' => 'DRCBANK',
        '河北农信' => 'HBNCXYS',
        '广西农村信' => 'GXRCC',
        '梁山农商银行' => 'LSRCB',
        '吉林农村信用社' => 'JLNLS',
        '乌鲁木齐商业银行' => 'URMQCCB',
        '邮政银行' => 'PSBCB',
        '宁波鄞州农村合作银行' => 'NBNCYH',
        '垦利乐安村镇银行股份有限公司' => 'kllabank',
        '广州农信社' => 'JPTX',
        '德州银行' => 'DZBCHINA',
        '苏州农村商业银行' => 'SZRCB',
        '新疆银行' => 'XJBANK',
        '西藏银行' => 'XZB',
        '渣打银行' => 'SCHK',
        '文昌大众村镇银行' => 'WCRCB',
        '汇丰银行' => 'HSBC',
        '支付宝' => '支付宝',
        '微信' => 'WECHAT',
        '曲靖市商业银行' => 'QJCCB',
        '农业发展银行' => 'ADBC',
        '云南红塔银行' => 'YNHTBANK',
        '湖州银行' => 'HZCCB',
        '福州农商银行' => 'FZRCB',
        '山东农商银行' => 'SDNCB',
        '泸州银行' => 'LZBANK',
        '东营莱商村镇银行' => 'DYLSBANK',
        '孝义汇通村镇银行' => 'XYHTCB',
        '山西孝义农村商业银行' => 'SXXYRCU',
        '长治银行' => 'SHXIB',
        '晋商银行' => 'JSHB',
        '晋中银行' => 'JZBA',
        '湖北省农村信用社' => 'HBRCC',
        '陕西农村信用社' => 'SXNXS',
    ];
    public function pay($params,$dfChannel){
        $bank_code = '';
        foreach (self::$yh_code as  $k=>$v){
            if (trim($params['bank_name']) == $k){
                $bank_code = $v;
                continue;
            }
        }
        if ($bank_code == ''){
            return ['code'=>0,'msg'=>'不支持的银行名称'];
        }
        $data = [
            'merchant_id' => $dfChannel['merchant_id'],
            'merchant_order_id' => $params['out_trade_no'],
//            'user_level' => '0',
//            'user_credit_level' => '-9_9',
            'pay_type' => '912',
            'pay_amt' => sprintf("%.2f",$params['amount']),
            'notify_url' => $dfChannel['notify_url'],
            'return_url' => 'http://www.baidu.com',
            'bank_code' => $bank_code,
            'bank_num' => trim($params['bank_number']),
            'bank_owner' => trim($params['bank_owner']),
            'bank_address' => '北京,北京',
//            'user_id' => rand(10000000,99999999),
            'remark' => '转账',
//            'key' => $dfChannel['api_key']
        ];

        $data['sign'] = $this->getSign($data,$dfChannel['api_key']);
        $data['user_level'] = '0';
        $data['user_credit_level'] = '-9_9';
        $data['user_id'] = rand(10000000,99999999);
        $data['user_ip'] = '127.0.0.1';
        $data['member_account'] = rand(100000000,999999999);
        $apiurl = $dfChannel['api_url'];
        $result = self::curlPost($apiurl,$data);
        Log::error('TxDaifuPayReturnData :'.$result);
        $result = json_decode($result,true);
        if (!empty($result['data']) && !empty($result['status']) && $result['status'] == '-1'){
            return ['code'=>0,'msg'=>$result['data']];
        }
        if ($result['pay_message'] != 1){
            return ['code'=>0,'msg'=>$result['pay_result']];
        }
        return ['code'=>1,'msg'=>'请求成功'];
    }


    private function getSign($data, $secret)
    {

        //签名步骤一：按字典序排序参数
//        ksort($data);
        $string_a = '';
        foreach ($data as $k => $v) {
            $string_a .= "{$k}={$v}&";
        }
//        $string_a = substr($string_a,0,strlen($string_a) - 1);
        //签名步骤三：MD5加密
        Log::error('TxDaifuPaySignStr :'.$string_a . 'key=' . $secret);
        $sign = md5($string_a . 'key=' . $secret);

        // 签名步骤四：所有字符转为大写
//        $result = strtoupper($sign);

        return $sign;
    }

    public function balance($channel){
        $data =  [
            'MerchantID' => $channel['merchant_id'],
            'MerchantType' => '0',
        ];
        $data['sign'] = md5('merchant_id='.$channel['merchant_id'].'&key='.$channel['api_key']);
        $result = self::curlPost($channel['query_balance_url'],$data);
        Log::error('TxDaifuPayBalanceReturn Data :'.$result);
        $result = json_decode($result,true);
        if (!array_key_exists('balance',$result)){

        }else{
            Db::name('daifu_orders_transfer')->where('id',$channel['id'])->update(['balance' => $result['balance']]);
        }
    }


    public function notify(){
        $input = file_get_contents("php://input");
        Log::notice("TxDaifuPayNotifyData: ".$input);
        $notifyData = json_decode($input,true);
        if ($notifyData['pay_message'] == 1){
            echo '{"code":200,"msg":"成功"}';
            $data['out_trade_no'] = $notifyData['merchant_order_id'];
            $data['error_reason'] = '';
            $data['status'] = 2;
            return $data;
        }else{
            echo '{"code":200,"msg":"成功"}';
            $data['out_trade_no'] = $notifyData['merchant_order_id'];
            $data['error_reason'] = $notifyData['pay_result'];
            $data['status'] =1;
            return $data;
        }
    }
}