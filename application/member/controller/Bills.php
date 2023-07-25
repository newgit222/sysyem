<?php


namespace app\member\controller;


use app\common\logic\MsMoneyType;
use think\Db;
use think\Request;

/**
 * 码商资金日志
 * Class Bills
 * @package app\ms\controller
 */
class Bills  extends Base
{


    public function index(){
        $bill_types = MsMoneyType::getMoneyOrderTypes();
        $this->assign('billTypes', $bill_types);
        return $this->fetch();
    }



    public function getBalanceDetails(Request $request){

        $where['a.uid'] = $this->agent_id;
        if (!empty($request->param('bill_type'))){
            $where['a.jl_class'] = $request->param('bill_type');
        }
        if (!empty($request->param('jc_class'))){
            $where['a.jc_class'] = $request->param('jc_class');
        }

        if (!empty($request->param('type'))){
            if ($request->param('type') != 'all'){
                $where['a.type'] = $request->param('type');
            }
        }else{
            $where['a.type'] = 'enable';
        }

        $startTime = date('Y-m-d 00:00:00',time());
        $endTime = date('Y-m-d 23:59:59',time());
        $where['a.addtime'] = ['between', [$startTime, $endTime]];
        if (!empty($request->param('startDate'))){
            $startTime = $request->param('startDate');
            $where['a.addtime'] = ['egt', strtotime($startTime)];
        }

        if (!empty($request->param('endDate'))){
            $endTime = $request->param('endDate');
            $where['a.addtime'] = ['elt',strtotime($endTime)];
        }

        if ($startTime && $endTime) {
            $where['a.addtime'] = ['between', [strtotime($startTime), strtotime($endTime)]];
        }
        $limit = $request->param('limit');
        $page = $request->param('page');
        $start=$limit*($page-1);
        $bills = Db::name('ms_somebill')
            ->alias('a')
            ->join('ms m','a.uid = m.userid')
            ->field('a.*,m.username')
            ->where($where)
            ->limit($start,$limit)
            ->order('id desc')
            ->select();


        $count = Db::name('ms_somebill')->alias('a')->where($where)->count();
        if ($count <= 0){
            return json([
                'code'=>1,
                'msg'=>'暂无数据'
            ]);
        }
        $bill_types = MsMoneyType::getMoneyOrderTypes();
        foreach ($bills as $k=>$v){
            foreach ($bill_types as $key => $val){
                if ($v['jl_class'] == $key){
                    $bills[$k]['jl_class'] = $val;
                    continue;
                }
            }
        }
        return json([
            'code'=>0,
            'msg'=>'请求成功',
            'data'=>$bills,
            'count'=>$count
        ]);
    }

    /**
     * @user luomu
     * @return 余额划分
      * @time
     */
    public function adjustment(){
        return $this->fetch();
    }

}
