<?php

namespace app\manage\controller;


use app\admin\logic\AuthGroup;
use app\common\library\enum\CodeEnum;
use think\Db;

class Admin extends Base
{

    public function index(){
        return $this->fetch('index');
    }


    public function getAdminList(){
        $where = [];

        //组合搜索
        !empty($this->request->param('id')) && $where['id']
            = ['eq', $this->request->param('id')];

        !empty($this->request->param('username')) && $where['username']
            = ['like', '%'.$this->request->param('username').'%'];

        !empty($this->request->param('email')) && $where['email']
            = ['like', '%'.$this->request->param('email').'%'];

        !empty($this->request->param('role')) && $where['id']
            = ['eq', $this->request->param('role')];

        $data = $this->logicAdmin->getAdminList($where,true,'id asc',false);

        $count = $this->logicAdmin->getAdminCount($where);

        $this->result($data || !empty($data) ?
            [
                'code' => CodeEnum::ERROR,
                'msg'=> '',
                'count'=>$count,
                'data'=>$data
            ] : [
                'code' => CodeEnum::SUCCESS,
                'msg'=> '暂无数据',
                'count'=>$count,
                'data'=>$data
            ]
        );
    }

    /*
     * 添加管理员
     */
    public function add()
    {
        $data = $this->request->param();
        $adminLogic = new \app\admin\logic\Admin();


        $ret = $adminLogic->addAdmin($data);

        return json($ret);
    }

    /**
     * 设置管理员费率，字段是rate
     */
    public function setRate()
    {
        $data = $this->request->param();

        if (!isset($data['admin_id'])){
            return json(['code' => CodeEnum::ERROR, 'msg' => '管理员【admin_id】不能为空']);
        }

        if (!isset($data['rate'])){
            return json(['code' => CodeEnum::ERROR, 'msg' => '费率【rate】不能为空']);
        }

        $adminLogic = new \app\admin\logic\Admin();
        $ret = $adminLogic->setRate($data);
        return json($ret);
    }



    public function editadminstatus(){
        $userid = $this->request->param('userid');
        $status = $this->request->param('admin_status')==1?0:1;
        if($userid == 1){
            $this->error('无权操作');
        }
        try {
            $res = Db::name('admin')->where('id',$userid)->update(['status'=>$status]);
            if ($res === false){
                throw new \Exception('更新管理员状态失败');
            }
            action_log('客服接口操作管理员状态','客服接口编辑管理员'.$userid.'状态，客户端ip： '.get_userip());
            return json([
                'code' => 1,
                'msg' => '操作成功'
            ]);



        }catch (\Exception $exception){
            return json([
                'code' => 404,
                'msg' => $exception->getMessage()
            ]);
        }

    }

    public function userAdd()
    {
        $this->assign('auth',$this->logicAuthGroup->getAuthGroupList());
        $this->request->isPost() && $this->result($this->logicAdmin->seveAdminInfo($this->request->post()));
        //代理组写死为2
        $groupAgentId = 2;
        //代理角色下的所有人
        $agentUserList = $this->logicAuthGroupAccess->getUserGroupInfoByGroupId($groupAgentId);
        $this->assign('agents', $agentUserList);
        return $this->fetch('user_add');
    }

    public function changeBalance()
    {
        $adminInfo = $this->modelAdmin->where('id', $this->request->param('id'))->find();
        if ($this->request->isPost()){

            $data = $this->request->post();

            $result = $this->validate(
                [
                    '__token__' => $this->request->post('__token__'),
                ],
                [
                    '__token__' => 'require|token'
                ]);

            if (true !== $result) {
                $this->error($result);
            }

            if (bccomp(0.00, $data['money']) != -1) {
                $this->error('操作资金不可小于或等于0.00');
            }
            if ($data['op_type'] == 0 && bccomp($data['money'], $adminInfo['balance']) == 1) { //减少
                $this->error('减少资金不可小于用户本金');
            }
            Db::startTrans();
            $ret = $this->logicAdminBill->addBill($adminInfo['id'], 2, $data['op_type'], $data['money'], $data['opInfo']);
            if ($ret) {
                Db::commit();
                if ($data['op_type'] == 0){
                    $caozuo = '减少';
                }else{
                    $caozuo = '增加';
                }
                action_log('客服接口增加余额','客服接口给管理员'.$adminInfo['username'].$caozuo.'余额： '.$data['money'].'，客户端ip ：'.get_userip());
                $this->success('操作成功', url('index'));
            }

            Db::rollback();
            $this->error('操作失败');
        }
        $this->assign('adminInfo', $adminInfo);
        return $this->fetch();
    }

}