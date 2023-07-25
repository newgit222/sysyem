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

namespace app\admin\controller;


use think\Db;

class Site extends BaseAdmin
{

    /**
     * 站点基本信息修改
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function website(){
        $this->common();
        $this->assign('list', $this->logicConfig->getConfigList(['group'=> '0'],true,'sort ace',100));
        return $this->fetch();
    }

    /**
     * 邮件修改
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function email(){
        $this->common();
        $this->assign('list', $this->logicConfig->getConfigList(['group'=> '1' ],true,'sort ace'));
        return $this->fetch();
    }

    /**
     * 管理员信息修改
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function profile(){

        //POST 提交修改
        $this->request->isPost() && $this->result($this->logicAdmin->seveAdminInfo($this->request->post()));

        $this->assign('info',$this->logicAdmin->getAdminInfo(['id' =>is_admin_login()]));

        return $this->fetch();
    }

    /**
     * 管理员密码修改
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function changePwd(){

        //POST 提交修改
        $this->request->isPost() && $this->result($this->logicAdmin->changeAdminPwd($this->request->post()));

        return $this->fetch('changepwd');
    }

    /**
     * Common
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    private function common(){
        $this->request->isPost() && $this->result(
            $this->logicConfig->settingSave(
                $this->request->post()
            )
        );
    }


    /**
     * 上传
     */
/*    public function upload()
    {
        if($this->request->isPost()) {
           $this->result($this->logicFile->fileUpload('file','logo'));
        }
    }
 */


    public function sys_config()
    {

        $config = require_once('./data/conf/adminSysPayCode.php');
        if ($this->request->isPost()){
            $data = $this->request->param();
            if (empty($data)){
                return json(['code'=>2,'msg'=>'没有数据哦']);
            }
            foreach ($data as $k=>$v){

                $admin_sys_config = $config['admin_sys_config'];
                foreach ($admin_sys_config as $key=>$val){
                    if ($k == $val['name']){
                        $insertData = $admin_sys_config[$key];
                        unset($insertData['attr'],$insertData['option_type']);
                        $insertData['value'] = $v;
                        $insertData['admin_id'] = is_admin_login();
                        $insertData['create_time'] = time();
                        $insertData['update_time'] = time();
                        $where['admin_id'] = is_admin_login();
                        $where['name'] = $val['name'];
                        $where['type'] = $val['type'];
                        $is = $this->logicConfig->getConfigInfo($where,true);
                        if ($is){
                            //更新
                            unset($insertData['create_time']);
                            Db::name('config')->where($where)->update($insertData);
                        }else{
                            //添加
                            Db::name('config')->insert($insertData);
                        }
                    }
                }
            }
//            print_r($insertData);die;
            return json(['code'=>1,'msg'=>'设置成功']);
        }

        $list = $config['admin_sys_config'];
        foreach ($list as $k=>$v){
            if ($v['name'] == 'sys_operating_password'){
                unset($list[$k]);
                continue;
            }
            $where['admin_id'] = is_admin_login();
            $where['type'] = $v['type'];
            $where['name'] = $v['name'];
            $res = Db::name('config')->where($where)->find();
            if ($res){
                if ($v['attr'] == 'select'){
//                    unset($res['extra']);
                    $res['extra'] = $v['extra'];
                }
                if ($res['name'] == 'cashier_address'){
                    unset($res['extra']);
                    $res['extra'] = $v['extra'];
                }

                $list[$k] = $res;
                $list[$k]['attr'] = $v['attr'];
                $list[$k]['option_type'] = $v['option_type'];
            }
        }
// halt($list);
        $this->assign('list',$list);
        return $this->fetch();
    }

}
