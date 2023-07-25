<?php

namespace app\member\controller;


class MsBalanceRecharge extends Base
{


    //列表方法
    public function index()
    {
        if ($this->request->isAjax()){
            $data = $this->request->param();
            $msBalanceRechargeLogic = new \app\common\logic\MsBalanceRecharge();

            $where = [];

            
            $startDate = strtotime(date('Y-m-d',time()));
            $endDate = strtotime(date('Y-m-d',time()).' 23:59:59');

            if (!empty($data['startDate'])){
                $startDate = strtotime($data['startDate']);
            }

            if (!empty($data['endDate'])){
                $endDate = strtotime($data['endDate']);
            }

            $where['create_time'] = ['between',[$startDate,$endDate]];

            if (!empty($data['amount'])){
                $where['amount'] = $data['amount'];
            }

            if (!empty($data['status']) && $data['status'] != -1){
                $where['status'] = $data['status'];
            }

            $where['ms_id'] = $this->agent_id;
            $MsBalanceRecharge = new \app\common\model\MsBalanceRecharge();

            $ret = $MsBalanceRecharge->where($where)->order('create_time desc')->paginate(input('limit/d', 10));

            $result = $ret->total()>0?[
                'code'=>0,
                'msg'=>'',
                'count'=>$ret->total(),
                'data'=>$ret->items()
            ]:[
                'code'=>0,
                'msg'=>'',
                'count'=>0,
                'data'=>[]
            ];
            return json($result);
        }

        return $this->fetch();
    }


    //添加方法
    public function add()
    {

        if ($this->request->isPost()){
            $data = $this->request->param();
            $msBalanceRechargeLogic = new \app\common\logic\MsBalanceRecharge();

            $validate = new \think\Validate([
                'amount'  => 'require|number',
                'reason' => 'require|chsDash',
                '__token__' => 'token',
            ]);

            if (!$validate->check($data)) {
                return json(['code'=>0,'msg'=>$validate->getError()]);
            }

            $data['ms_id'] = $this->agent_id;
            $data['admin_id'] = $this->agent->admin_id;

            $ret = $msBalanceRechargeLogic->add($data);
            return json($ret);
        }

        return $this->fetch();


    
    }

    public function add_index()
    {
        return $this->fetch();
    }

    /**
     * 取消审核
     */
    public function cancelReview()
    {
        $id = $this->request->param('id');

        $info = \app\common\model\MsBalanceRecharge::get($id);

        if (empty($info)){
            return json(['code'=>0,'msg'=>'充值记录不存在']);
        }

        if ($info->status != 1){
            return json(['code'=>0,'msg'=>'当前记录已变动，请刷新']);
        }

        $info->status = 4;

        $ret = $info->save();

        if ($ret){
            return json(['code'=>1,'msg'=>'操作成功']);
        }else{
            return json(['code'=>0,'msg'=>'操作失败']);
        }

    }

}