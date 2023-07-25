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

namespace app\admin\logic;


use app\common\library\enum\CodeEnum;
use app\common\model\ActionLog;
use think\Cache;
use think\Cookie;
use think\Db;
use think\Exception;
use think\Log;
use think\Request;
use think\Validate;

class Admin extends BaseAdmin
{
    /**
     * 获取管理员列表
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int $paginate
     * @return mixed
     */
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
     * 获取管理员信息
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @param bool $field
     * @return mixed
     */
    public function getAdminInfo($where = [], $field = true)
    {
        return $this->modelAdmin->getInfo($where, $field);
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
        if (is_admin_login() != 1){
            if ($data['id'] != is_admin_login()){
                return [ 'code' => CodeEnum::ERROR, 'msg' => '非法操作'];
            }
        }

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

            if (empty($data['security_code'])){
                unset($data['security_code']);
            }else{
                $data['security_code'] = md5($data['security_code']);
            }

            $admin_id = $this->modelAdmin->setInfo($data);

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
            return [ 'code' => CodeEnum::SUCCESS,'msg' => $action . '管理员信息成功'];
        }catch (\Exception $ex){
            Db::rollback();
            Log::error($ex->getMessage());
            return [ 'code' => CodeEnum::ERROR, 'msg' => config('app_debug') ? $ex->getMessage() : '未知错误'];
        }

    }

    /**
     * 添加管理员
     */
    public function addAdmin($data)
    {
        Db::startTrans();
        try {
            $validate = new Validate([
                'username' => 'require|unique:admin',
                'password' => 'require',
                'nickname' => 'require',
                'email' => 'require|email|unique:admin',
            ], [
                'username.require' => '用户名不能为空',
                'username.unique' => '用户名已存在',
                'password.require' => '密码不能为空',
                'nickname.require' => '昵称不能为空',
                'email.require' => '邮箱不能为空',
                'email.email' => '邮箱格式不正确',
                'email.unique' => '邮箱已存在',
            ]);
            if (!$validate->check($data)) {
                return ['code' => CodeEnum::ERROR, 'msg' => $validate->getError()];
            }
            $data['password'] = data_md5_key($data['password']);

            $admin_id = $this->modelAdmin->setInfo($data);

            $this->logicAdminBill->addBill($admin_id, 5, 1, 20, '管理员开户赠送金额20');

            Db::commit();
            return [ 'code' => CodeEnum::SUCCESS,'msg' => '添加管理员信息成功'];
        }catch (\Exception $ex){
            Db::rollback();
            return [ 'code' => CodeEnum::ERROR, 'msg' => config('app_debug') ? $ex->getMessage() : '未知错误'];
        }

    }

    /**
     * 修改密码
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $data
     *
     * @return array
     */
    public function changeAdminPwd($data){
        //数据验证'repassword'=>'require|confirm:password'
        $rules  = [
            'oldPassword'   => 'require',
            'password'   => 'require',
            'repassword' => 'require|confirm:password',
        ];

        $oldPwd = data_md5_key($data['oldPassword']);
        $newPwd = data_md5_key($data['password']);
        $user = $this->logicAdmin->getAdminInfo(['id' => is_admin_login()]);

        //验证原密码
        if ( $oldPwd == $user['password']) {
            $validate = new Validate($rules);;
            if (!$validate->check($data)) {
                return ['code' => CodeEnum::ERROR, 'msg' => $validate->getError()];
            }

            $result = $this->setAdminValue(['id' => is_admin_login()], 'password', $newPwd);
            action_log('修改', '管理员ID'. is_admin_login() .'密码修改');

            return $result && !empty($result) ? ['code' => CodeEnum::SUCCESS, 'msg' => '修改密码成功']
                : ['code' => CodeEnum::ERROR, 'msg' => '修改失败'];
        }else{
            return ['code' => CodeEnum::ERROR, 'msg' => '原密码不正确'];
        }
    }

    /**
     * 设置管理员信息
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @param string $field
     * @param string $value
     * @return mixed
     */
    public function setAdminValue($where = [], $field = '', $value = '')
    {
        return $this->modelAdmin->setFieldValue($where, $field, $value);
    }

    /**
     * 授权用户组
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $data
     * @return array
     */
    public function userAuth($data){

        if ( $data['id'] == 1) {

            return ['code' => CodeEnum::ERROR, 'msg' => '天神不能授权哦~'];
        }

        $where = ['uid' => ['in', $data['id']]];

        $this->modelAuthGroupAccess->deleteInfo($where, true);

        if (empty($data['role_ids'])) {

            return ['code' => CodeEnum::SUCCESS, 'msg' => '授权成功'];
        }

        $add_data = [];

        foreach ($data['role_ids'] as $group_id) {

            $add_data[] = ['uid' => $data['id'], 'group_id' => $group_id];
        }

        Db::startTrans();
        try{

            $this->modelAuthGroupAccess->setList($add_data);

            action_log('授权', '管理员ID'. $data['id'] . '用户组权限');

            Db::commit();
            return ['code' => CodeEnum::SUCCESS, 'msg' => '授权成功'];
        }catch (\Exception $e){
            Db::rollback();
            Log::error($e->getMessage());
            return ['code' => CodeEnum::ERROR, $e->getMessage()];
        }
    }

    /**
     * 获取管理员的所有下级
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param int $id
     * @param array $data
     * @return array
     */
    public function getSubUserIds($id = 0, $data = [])
    {

        $member_list = $this->modelAdmin->getList(['leader_id' => $id], 'id', 'id asc', false);

        foreach ($member_list as $v)
        {

            if (!empty($v['id'])) {

                $data[] = $v['id'];

                $data = array_unique(array_merge($data, $this->getSubUserIds($v['id'], $data)));
            }

            continue;
        }

        return $data;
    }

    /**
     * 删除管理员
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $where
     *
     * @return array
     */
    public function userDel($where){

        $result = $this->modelAdmin->deleteInfo($where);

        $result && action_log('删除', '删除管理，where：' . http_build_query($where));

        return $result ? ['code' => CodeEnum::SUCCESS, 'msg' =>'删除管理员成功', ''] : ['code' => CodeEnum::ERROR, 'msg' =>$this->modelMember->getError(), ''];
    }

    /**
     * 网站列表统计
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int $paginate
     * @param $whereOrder
     * @return array|mixed
     * @throws \think\Exception
     */
    public function getAdminListStat($where = [], $field = true, $order = '', $paginate = 15, $whereOrder)
    {
        $whereOrder['status'] = 2;
        $data = $this->modelAdmin->getList($where, $field, $order, $paginate);
        $data = $data->toArray()['data'] ?? [];
        foreach ($data as &$admin) {
            $uids = $this->logicUser->getUserIdsByAdminId($admin['id']);
            $whereOrder['uid'] = ['in', $uids];
            $admin['success_number'] = $this->modelOrders->where($whereOrder)->count();
            $admin['success_amount'] = $this->modelOrders->where($whereOrder)->sum('amount');
        }
        return $data;
    }

    /**
     * 设置费率
     */
    public function setRate($data)
    {
        $where = ['id' => $data['admin_id']];
        $result = $this->modelAdmin->setFieldValue($where, 'rate', $data['rate']);
//        $result && action_log('设置', '接口设置费率，where：' . http_build_query($where));

        if ($result){
            $action_log_model = new ActionLog();
            $action_log_model->save([
                'uid' => 999999,
                'module' => 'manage',
                'action' => '设置管理员费率',
                'describe' => '接口设置费率，where：' . http_build_query($where),
                'url' =>Request::instance()->module() . '/' . Request::instance()->controller() . '/' . Request::instance()->action(),
                'ip' => get_userip(),
                'admin_id' => $data['admin_id']
            ]);


        }

        return $result ? ['code' => CodeEnum::SUCCESS, 'msg' => '设置费率成功'] : ['code' => CodeEnum::ERROR, 'msg' => $this->modelAdmin->getError() ?? '设置费率失败'];
    }


    public function check_security_code($security_code){
        $cookie = Cookie::get('security_code_success');
        if ($cookie){
            return true;
        }
        $admin_info =  $this->modelAdmin->find(is_admin_login());
        if (!$security_code) {
            return false;
        }
        if (md5($security_code) != $admin_info['security_code']){
//            Cache::inc('check_security_code_error'.is_admin_login());
//            $security_code_error_count = Cache::get('check_security_code_error'.is_admin_login());
//            if ($security_code_error_count >= 5){
//                action_log('管理账户异常', '管理员'. $admin_info['username'] .'安全码连续错误5次，已被封锁账户');
//                $this->modelAdmin->where(['id'=>is_admin_login()])->update(['status'=>5]);
//            }
            return false;
        }
//        Cache::rm('check_security_code_error'.is_admin_login());
        Cookie::set('security_code_success',1,3600*24);
        return true;
    }
}
