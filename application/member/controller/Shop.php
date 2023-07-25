<?php


namespace app\member\controller;


use app\common\library\enum\CodeEnum;
use think\Request;

class Shop extends Base
{
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 添加商铺
     */
    public function add_shop(){
        return $this->fetch();
    }

    /**
     * 编辑商铺
     */
    public function edit_shop(Request $request)
    {
        $id = $request->param('id');
        $where['uid'] = $this->agent_id;
        $where['id'] = $id;

        $row = $this->modelShop->where($where)->find();
        if (empty($row)){
            $this->error('操作不合法');
        }
        $this->assign('row', $row);
        return $this->fetch();
    }


    /**
     * 删除店铺
     */
    public function del_shop(Request $request){
        $id = $request->param('id');
        $where['uid'] = $this->agent_id;
        $where['id'] = $id;

        $row = $this->modelShop->where($where)->find();
        if (empty($row)){
            $this->error('操作不合法');
        }
        $this->modelShop->where($where)->update(['status' => -1]);
        $this->success('删除成功');
    }

    /**
     * 保存商铺数据
     */
    public function saveShop(Request $request)
    {
        $data = $request->param();
        $data['uid'] = $this->agent_id;
        $request->isPost() && $this->result($this->logicShop->saveShop($data));
        $this->error([ CodeEnum::ERROR,'未知错误']);

    }


    /**
     * 获取商户列表
     */
    public function getShopList(Request $request)
    {
         $type = $request->param('type');
        $name = $request->param('name');

        $where['uid'] = $this->agent_id;
        $where['status'] = ['neq' , -1];
        !empty($type) && $where['type'] = $type;
        !empty($name) && $where['name'] = ['like', '%'. $name .'%'];


        $lists = $this->modelShop->where($where)->paginate($request->post('limit', 15));


        $this->result($lists->total() ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg' => '',
                'count' => $lists->total(),
                'data' =>$lists->items()
            ] : [
                'code' => CodeEnum::ERROR,
                'msg' => '暂无数据',
                'count' => 0,
                'data' => []
            ]
        );

    }
}