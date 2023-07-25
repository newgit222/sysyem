<?php

namespace app\manage\logic;


use app\common\library\enum\CodeEnum;
use app\common\logic\BaseLogic;
use think\Db;
use think\Log;

class Admin  extends BaseLogic
{
    public function getAdminList($where = [], $field = true, $order = '', $paginate = 0)
    {
        return $this->modelAdmin->getList($where, $field, $order, $paginate);
    }

    /**
     * 获取管理员总数
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @return mixed
     */
    public function getAdminCount($where = []){
        return $this->modelAdmin->getCount($where);
    }


    /**
     * 管理信息存储
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $data
     * @return array
     */
    public function seveAdminInfo($data){

        if (!empty($data['google_status'])){
            unset($data['google_status']);
        }

        if (!empty($data['google_secret_key'])){
            unset($data['google_secret_key']);
        }

        $validate = $this->validateAdmin->scene($data['scene'])->check($data);

        if (!$validate) {

            return ['code' => CodeEnum::ERROR, 'msg' => $this->validateAdmin->getError()];
        }
        //TODO 修改数据
        Db::startTrans();
        try{
            if (empty($data['password'])){
                unset($data['password']);
            }else{
                $data['password'] = data_md5_key($data['password']);
            }

            $admin_id = $this->modelAdmin->setInfo($data);
            $uid = $this->modelAdmin->id;
            //授权
            $where = ['uid' => ['in', $uid]];
            $this->modelAuthGroupAccess->deleteInfo($where, true);
            $add_data = [];
            foreach ($data['role_ids'] as $group_id) {

                $add_data[] = ['uid' => $uid, 'group_id' => $group_id];
            }
            $this->modelAuthGroupAccess->setList($add_data);
            $action = isset($data['id']) ? '编辑' : '新增';

            action_log($action, $action . '管理员信息，' . $data['nickname']);
            //用用户开户赠送100余额
            if ( !isset($data['id']) ){
                $admin_info = $this->modelAdmin->where('id', $admin_id)->find();
                if ($admin_info && $admin_id != is_admin_login()){
                    $this->logicAdminBill->addBill($admin_id, 5, 1, 20, '管理员开户赠送金额20');
                }
            }
            Db::commit();
            action_log('客服接口添加管理员','客服接口添加管理员'.$data['nickname'].'，客户端ip： '.get_userip());
            return [ 'code' => CodeEnum::SUCCESS,'msg' => $action . '管理员信息成功'];
        }catch (\Exception $ex){
            Db::rollback();
            Log::error($ex->getMessage());
            return [ 'code' => CodeEnum::ERROR, 'msg' => config('app_debug') ? $ex->getMessage() : '未知错误'];
        }

    }

}