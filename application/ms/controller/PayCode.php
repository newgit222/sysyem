<?php

namespace app\ms\controller;

use app\common\library\enum\CodeEnum;
use app\common\logic\CodeLogic;
use app\common\logic\EwmOrder;
use app\common\logic\Queuev1Logic;
use app\common\model\EwmPayCode;
use app\common\model\GemapayCodeModel;
use think\Db;
use think\Request;

/**
 *码商二维码管理
 * Class PayCode
 * @package app\agent\controller
 */
class PayCode extends Base
{


    private function encrypt($data)
    {
        return base64_encode(openssl_encrypt($data,"AES-128-CBC",config('secret_key'),true,config('iv')));

    }


    /**
     *修改限额
     */
    public function editXiane(){
        $code_id = $this->request->param('id');
        $edu = $this->request->param('limit__total');
        $maxMoney = $this->request->param('max_money');
        $minMoney = $this->request->param('min_money');

        if (empty($code_id)){
            $this->error('非法操作');
        }
        if (!$edu || $edu < 0 || $maxMoney < 0 || $minMoney < 0){
            $this->error('金额错误');
        }

        $res = Db::name('ewm_pay_code')->where('id',$code_id)->update(['limit__total'=>$edu, 'max_money' => $maxMoney, 'min_money' => $minMoney]);
        if ($res === false){
            $this->error('修改失败！');
        }
        $this->success('修改成功','');
    }


    /**
     *修改收款笔数
     */
    public function edit_order_num(){
        $code_id = $this->request->param('id');
        $success_order_num = $this->request->param('success_order_num');
        if (empty($code_id)){
            $this->error('非法操作');
        }
//        print_r($success_order_num);die;
        if ($success_order_num == NULL || $success_order_num < 0){
            $this->error('笔数有误');
        }

        $res = Db::name('ewm_pay_code')->where('id',$code_id)->update(['success_order_num'=>$success_order_num]);
        if ($res === false){
            $this->error('修改失败！');
        }
        $this->success('修改成功','');
    }

    /**
     * 二维码列表
     * @param Request $request
     * @return mixed
     */
    public function lists(Request $request)
    {
        $map = [];
//        $map['a.code_type'] = 30;
        $codetype = $request->param('pay_code');
        if (empty($codetype)){
            $this->error('当前链接无法访问！');
        }
        $codename = Db::name('pay_code')->where('id',$codetype)->value('code');
        $this->assign('pay_code',$codename);
        if ($codetype == 32){
            $map['b.code'] = ['in',['alipayUid','alipayUidSmall']];
            $where['b.code'] = ['in',['alipayUid','alipayUidSmall']];
        }else{
            $map['a.code_type'] = $codetype;

            $where['a.code_type'] = $codetype;
        }
        $account_name = $request->param('account_name', '', 'trim');
	if(strlen( $account_name<10)){
	    $account_name && $map['a.account_name'] = ['like', '%' . $account_name . '%'];
        }

        $map['a.ms_id'] = $this->agent_id;
        $map['a.is_delete'] = 0;
//            print_r($map);die;
        $gemapayCode = Db::name('ewm_pay_code');
        $listGemaPayCode = $gemapayCode->alias('a')
            ->join('pay_code b','a.code_type = b.id','left')
            ->field('a.*,b.name')
            ->where($map)
            ->order('a.id desc')
            ->paginate(10);

        $list = $listGemaPayCode->items();
//        print_r($list);die;
        $CodeLogic = new CodeLogic();

        foreach ($list as $k => $v) {
            $v['type'] = 3; //3是银行卡
            $v['add_admin_id'] = 1;//目前就当是支付系统admin超级管理员
            $position = $CodeLogic->getQueenPostion($v['id'], $v['type'], $v['add_admin_id']);
            $list[$k]['queen_postion'] = $position;
        }
        $positions = array_column($list, 'queen_postion');
        array_multisort($positions, SORT_ASC, SORT_REGULAR, $list);
        $count = $listGemaPayCode->total();
        // 获取分页显示
        $page = $listGemaPayCode->render();
        $reg_date = Db::name('ms')->where('userid',$this->agent_id)->value('reg_date');
        $jimidata = $this->agent_id.'_'.$reg_date;
        $apikey = encrypt($jimidata,'E');
        $replace = 'asdefg';
        $search = '+';
        $apikey = str_ireplace($search, $replace, $apikey);
        $apiurl = $this->request->domain() .'/api/bank/banksms?key='.$apikey;



        $codeSum = Db::name('ewm_pay_code')->alias('a')
                            ->join('pay_code b','a.code_type = b.id','left')
                            ->where($where)
                            ->where(['a.is_delete'=>0,'a.ms_id'=>$this->agent_id])
                            ->count();
        $codeOnSum = Db::name('ewm_pay_code')->alias('a')
            ->join('pay_code b','a.code_type = b.id','left')
            ->where($where)
            ->where(['a.is_delete'=>0,'a.ms_id'=>$this->agent_id,'a.status'=>1])->count();

        $this->assign('codeSum', $codeSum);
        $this->assign('codeOnSum', $codeOnSum);
        $this->assign('apiurl', $apiurl);
        $this->assign('count', $count);
        $this->assign('list', $list); // 賦值數據集
        $this->assign('count', $count);
        $this->assign('page', $page); // 賦值分頁輸出
        return $this->fetch();
    }








    /**
     * 支付宝uid
     */

    public function alipayUid(){
        $where['b.code'] = ['in',['alipayUid','alipayUidSmall']];
        $query = Db::name('ewm_pay_code');

        $res = $query->alias('a')->join('pay_code b','a.code_type=b.id')->where($where)->where(['a.ms_id'=>$this->agent_id,'a.is_delete'=>0])->field('a.*,b.name')->order('a.id','desc')->paginate(200);
        $page = $res->render();
        $codeSum = Db::name('ewm_pay_code')->alias('a')->join('pay_code b','a.code_type=b.id')->where($where)->where(['a.is_delete'=>0,'a.ms_id'=>$this->agent_id])->count('a.id');
        $codeOnSum = Db::name('ewm_pay_code')->alias('a')->join('pay_code b','a.code_type=b.id')->where($where)->where(['a.is_delete'=>0,'a.ms_id'=>$this->agent_id,'a.status'=>1])->count('a.id');
//        print_r($res);die;
        $this->assign('codeSum', $codeSum);
        $this->assign('codeOnSum', $codeOnSum);
        $this->assign('page',$page);
        $this->assign('res',$res);
        return $this->fetch();
    }






     public function qun_lists(Request $request)
    {
        $account_name = $request->param('account_name', '', 'trim');
	if(strlen( $account_name<10)){
	    $account_name && $map['a.account_name'] = ['like', '%' . $account_name . '%'];
        }
        $map = [];
        $map['a.ms_id'] = $this->agent_id;
        $map['a.is_delete'] = 0;

        $gemapayCode = Db::name('ewm_pay_code');
        $listGemaPayCode = $gemapayCode->alias('a')
            ->field('*')
            ->where($map)
            ->order('id desc')
            ->paginate(10);

        $list = $listGemaPayCode->items();
        $CodeLogic = new CodeLogic();

        foreach ($list as $k => $v) {
            $v['type'] = 1; //3是银行卡
            $v['add_admin_id'] = 1;//目前就当是支付系统admin超级管理员
            $position = $CodeLogic->getQueenPostion($v['id'], $v['type'], $v['add_admin_id']);
            $list[$k]['queen_postion'] = $position;
        }
        $positions = array_column($list, 'queen_postion');
        array_multisort($positions, SORT_ASC, SORT_REGULAR, $list);
        $count = $listGemaPayCode->total();
        // 获取分页显示
        $page = $listGemaPayCode->render();
        $this->assign('count', $count);
        $this->assign('list', $list); // 賦值數據集
        $this->assign('count', $count);
        $this->assign('page', $page); // 賦值分頁輸出
        return $this->fetch();
    }
    /**
     * 银行卡
     * @var string[]
     */
    protected $banks = [
	    '农业银行','山西省农村信用社',
'工商银行','恒丰银行','华夏银行','山东省农村信用社',
'招商银行','台州银行',
'平安银行','广发银行',
'吉林银行','广州（潮州）农商银行',
'武汉农商银行',
'吉林昌邑榆银村镇银行',
'河南省农村信用社',
'河北省农村信用社',
'海南省农村信用社',
'安徽省农村信用社','四川省农村信用社','浙江省农村信用社','广东省农村信用社','广西省农村信用社','黑龙江省农村信用社','湖北省农村信用社','南京银行','中原银行','浦发银行','光大银行','安徽当涂新华村镇银行','江苏长江商业银行','渤海银行','保定银行'
    ];

    /**
     * @return void
     * 上传收款码
     */
//    public function QRcode(){
//        // 获取表单上传文件 例如上传了001.jpg
//        $file = $this->request->file('file');
//
//        // 移动到框架应用根目录/public/uploads/ 目录下
//        if($file){
//            $info = $file->validate(['size'=>15678,'ext'=>'jpg,png,jpeg'])->move(ROOT_PATH . 'public' . DS . 'uploads'. DS . 'qr');
//            if($info){
////                echo $info->getFilename();
//                $path = 'public/uploads/qr/'.$info->getSaveName();
//                return json_encode(['code'=>0,'data'=>$path]);
//            }else{
//                // 上传失败获取错误信息
//                return json_encode(['code'=>1,'msg'=>$file->getError()]);
//            }
//        }
//    }


    /**
     * 添加二维码
     */
    public function add(CodeLogic $codeLogic)
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();


                $result = $this->validate($data, 'EwmPayCode');
                if (true !== $result) {
                    $this->error($result);
                }


                $code = Db::name('pay_code')->where('code',$data['code_type'])->value('id');
                if (empty($code)){
                    $this->error('不支持的通道');
                }


//        print_r($data);die;
            $result = $codeLogic->addQRcode($data,$code);
            if ($result['code'] = CodeEnum::ERROR) {
                $this->error("上传失败," . $result['msg']);
            }
            if ($data['code_type'] == 'alipayUidSmall'){
                $code = 32;
            }
            $this->success($result['msg'], url('lists?pay_code='.$code));
        }
//        print_r($this->request->param());die;
        $pay_code = $this->request->param('pay_code');
//        $code = Db::name('pay_code')->where('id',$pay_code)->value('code');
        $security = Db::name('ms')->where('userid',$this->agent_id)->value('security_pwd');
        if (!$security){
            $this->success('请先至用户中心配置安全码，正在为你跳转.....',url('index/editsafety'));
        }
        $smsTemplate = require_once('./data/conf/smsTemplate.php');
//        print_r($smsTemplate);die;

        $this->assign('banksList', $smsTemplate);
        $this->assign('code', $pay_code);
        return $this->fetch();
    }

     /**
     * 添加群二维码
     */
//    public function qun_add(CodeLogic $codeLogic)
//    {
//        if ($this->request->isPost()) {
//
//			if (( ($_FILES["file"]["type"] == "image/jpeg")
//                || ($_FILES["file"]["type"] == "image/pjpeg")
//                || ($_FILES["file"]["type"] == "image/png"))
//            && ($_FILES["file"]["size"] < 2000000))
//        {
//            if ($_FILES["file"]["error"] > 0)
//            {
//                $this->error("Return Code: " . $_FILES["file"]["error"],3);
//            }
//            else
//            {
//                if($_FILES["file"]["type"] == "image/jpeg"){
//                    $ext = '.jpg';
//                }
//                if($_FILES["file"]["type"] == "image/pjpeg"){
//                    $ext = '.jpg';
//                }
//                if($_FILES["file"]["type"] == "image/png"){
//                    $ext = '.png';
//                }
//                $name = md5(microtime()).$ext;
//                if (file_exists("public/uploads/" . $name))
//                {
//                    echo $_FILES["file"]["name"] . " already exists. ";
//                }
//                else
//                {
//                    move_uploaded_file($_FILES["file"]["tmp_name"],
//                        "public/uploads/" .$name);
//                  //  $this->success("Stored in: " . "upload/" . $_FILES["file"]["name"],'/ownpay/add',3);
//                }
//            }
//        }
//        else
//        {
//            $this->error('非法图片，请选择二维码图片','/ownpay/add',3);
//        }
//
//
//
//
//            $data = $this->request->param();
//           // $result = $this->validate($data, 'EwmPayCode');
//           // if (true !== $result) {
//             //   $this->error($result);
//           // }
//		    $data['file_url']= "public/uploads/" . $name;
//            $result = $codeLogic->addQunQRcode($data);
//            if ($result['code'] = CodeEnum::ERROR) {
//                $this->error("上传失败," . $result['msg']);
//            }
//            $this->success($result['msg'], url('qun_lists'));
//        }
//        $this->assign('banksList', $this->banks);
//        return $this->fetch();
//    }


    /**
     * 删除二维码
     * @param Request $request
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function del(Request $request)
    {
        $id =intval(trim($request->param('id')));

//        $codeInfo = $this->modelEwmPayCode->where(['id'=>$id])->find();

        $codeInfo =  Db::name('ewm_pay_code')->where(['id'=>$id])->find();
//                print_r($codeInfo);die;
        if ($codeInfo['ms_id'] != $this->agent_id) {
            $this->error('删除失败,信息错误');
        }

//        $re = EwmPayCode::destroy($id);
        $re = Db::name('ewm_pay_code')->where(['id'=>$id])->update(['is_delete'=>1,'status'=>0]);

        if ($re) {
            $QueueLogic = new Queuev1Logic();
            $codeInfo['type'] = 3;
            $QueueLogic->delete($id, $codeInfo['type'], 1);
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     *　激活二维码
     */
//    public function activeCode(CodeLogic $codeLogic)
//    {
//        $codeTypeId =intval( $this->request->param('code_id'));
//
//        if (!$codeTypeId) {
//            $this->error('非法操作');
//        }
//        $res = $codeLogic->activeCode($this->agent_id, $codeTypeId);
//        if ($res['code'] == \app\common\library\enum\CodeEnum::ERROR) {
//            $this->error($res['msg']);
//        }
//        $this->success($res['msg']);
//    }

    public function disactiveCode(CodeLogic $codeLogic){
        $coid = $this->request->param('coid');
        $status = $this->request->param('status');
        // print_r($userid);die;
//        $res = Db::name('ewm_pay_code')->where('id',$coid)->update(['status'=>$status]);
        $msid = Db::name('ewm_pay_code')->where('id',$coid)->value('ms_id');
        if ($msid != $this->agent_id){
            $this->error('非法操作');
        }

        if ($status == 1){
         $res = $codeLogic->disactiveCode($this->agent_id, $coid);
        }else{
            $res = $codeLogic->activeCode($this->agent_id, $coid);
        }
        if($res['code'] == \app\common\library\enum\CodeEnum::ERROR){
            return json([
                'code' => 404
            ]);
        }else{
            return json([
                'code' => 1
            ]);
        }
    }


    /**
     * 冻结二维码
     */
//    public function disactiveCode(CodeLogic $codeLogic)
//    {
//        $codeTypeId = intval($this->request->param('code_id'));
//        if (!$codeTypeId) {
//            $this->error('非法操作');
//        }
//        $res = $codeLogic->disactiveCode($this->agent_id, $codeTypeId);
//        if ($res['code'] == \app\common\library\enum\CodeEnum::ERROR) {
//            $this->error($res['msg']);
//        }
//        $this->success($res['msg']);
//    }


    /**
     * 二维码统计信息
     * @param Request $request
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function statistics(Request $request)
    {
      //$type = $request->param('type', 0, 'intval');
        //eyword = $request->param('keyword', 0, 'intval');

       //mobile = $request->param('mobile', 0, 'intval');
       $account = $request->param('account', 0, 'vlidateError');
      $account_name = $request->param('account_name', 0, 'intval');

        $map = [];

       //type && $map['type'] = $type;
      //$keyword && $map['a.id'] = $keyword;

//      $mobile && $map['b.mobile'] = ['like', '%' . $mobile . '%'];
  //    $account && $map['b.account'] = ['like', '%' . $account . '%'];
    //  $account_name && $map['a.account_name'] = ['like', '%' . $account_name . '%'];


        $add_time = [];
        //时间
        $startTime = $request->param('start_time');
        empty($startTime) && $startTime = date("Y-m-d 00:00:00", time());
        $endTime = $request->param('end_time');
        empty($endTime) && $endTime = date("Y-m-d 23:59:59", time());

        $this->assign('startTime', $startTime);
        $this->assign('endTime', $endTime);


        $add_time = ['between', [strtotime($startTime), strtotime($endTime)]];
        $map['a.ms_id'] = $this->agent_id;
        $GemapayOrderLogic = new EwmOrder();
        $GemapayOrderModel = new \app\common\model\EwmOrder();
        $where = [
            'add_time' => $add_time
        ];

        $orders = $GemapayOrderModel->field('code_id')->where($where)->select();

        $array = [];
        foreach ($orders as $k => $v) {
            $array[] = $v['code_id'];
        }
        $map['a.id'] = ['in', $array];


        $gemapayCode = Db::name('ewm_pay_code');
        $listGemaPayCode = $gemapayCode->alias('a')
            ->field('a.*,b.account,b.username')
            ->join('cm_ms b', 'a.ms_id=b.userid', "LEFT")
            ->where($map)
            ->order('id desc')->paginate(50);
        $list = $listGemaPayCode->items();

        foreach ($list as $k => $v) {
            $where = [
                'code_id' => $v['id'],
            ];
            $where['add_time'] = $add_time;
            $list[$k]['orders'] = $GemapayOrderLogic->getTotalPrice($where);
        }
        $count = $listGemaPayCode->total();
        // 获取分页显示
        $page = $listGemaPayCode->render();
        $this->assign('count', $count);

        $this->assign('list', $list); // 賦值數據集
        $this->assign('count', $count);
        $this->assign('page', $page); // 賦值分頁輸出


//        //分组列表
//        $GemaPayGroup = new GemaPayGroupModel();
//        $this->assign('groupList', $GemaPayGroup->getGroupList($this->agent->userid));
        return $this->fetch();
    }




    /**
     * 测试uid
     */

    public function testpay(){
        if ($this->request->param('amount') < 0){
            return json(['msg'=>'金额最小为1']);
        }

        $code = Db::name('ewm_pay_code')->where('id',$this->request->param('code_id'))->find();
        $data['uid'] = $code['account_number'];

        $data['amount'] = sprintf("%.2f",$this->request->param('amount'));

        $data['orderNo'] = rand(2000000,2999999);

        $url = 'https://www.alipay.com/?appId=20000123&actionType=scan&biz_data={"s":"money","u":"'.$data['uid'].'","a":"'.$data['amount'].'","m":"商城购物'.$data['orderNo'].'"}';
        $encode_url = 'alipays://platformapi/startapp?appId=68687093&url='.urlencode($url);
        $gourl = str_replace('/&amp;/g','&',$encode_url);
        $this->assign('url',$gourl);
        return $this->fetch();
    }


    /**
     * 支付宝uid_add
     */

    public function alipayuidadd(CodeLogic $codeLogic){
        if ($this->request->isPost()){
            $data = $this->request->param();

            $result = $this->validate($data, 'EwmPayCode');
            if (true !== $result) {
                $this->error($result);
            }

            $codetype = $data['code_type'];
            $code = Db::name('pay_code')->where('code',$codetype)->find();
            if (!$code){
                $this->error('不支持的通道');
            }

//        print_r($data);die;
            $result = $codeLogic->addQRcode($data,$code['id']);
            if ($result['code'] = CodeEnum::ERROR) {
                $this->error("上传失败," . $result['msg']);
            }
            $this->success($result['msg'], url('alipayUid'));
        }
        $security = Db::name('ms')->where('userid',$this->agent_id)->value('security_pwd');
        if (!$security){
            $this->success('请先至用户中心配置安全码，正在为你跳转.....',url('index/editsafety'));
        }
        return $this->fetch();
    }

}
