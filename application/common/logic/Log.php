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

namespace app\common\logic;


use app\common\library\enum\CodeEnum;
use think\Db;

class Log extends BaseLogic
{

    /**
     * 增加一个操作日志
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param string $action 动作
     * @param string $describe
     * @return array
     */
    public function logAdd($action = '', $describe = '')
    {
        $request = request();$module = $request->module();

        if ($module == 'admin'){
            $uid = empty(session('admin_info')['id'])?'999999':session('admin_info')['id'];
//            $uid = session('admin_info')['id'];
            $adminid = is_admin_login();
        }else if ($module == 'index'){
            $uid = empty(session('user_info'))?'999999':session('user_info')['uid'];
            $adminid = Db::name('user')->where('uid',session('user_info')['uid'])->value('admin_id');
        }else if ($module == 'ms'){
            $uid = intval(session('agent_id'));
            $adminid = Db::name('ms')->where('userid',session('agent_id'))->value('admin_id');
	}else if ($module == 'member'){
            $uid = intval(session('agent_id'));
            $adminid = Db::name('ms')->where('userid',session('agent_id'))->value('admin_id');
        }else if ($module == 'manage'){

            $uid = '999998';
        }else if ($module == 'mobile'){
            $uid = '999999';
        }
	if(empty($adminid))
	{
	  $adminid = 1;
	}
        $data['uid']       = $uid;
        $data['module']    = $module;
        $data['ip']        = get_userip();
        $data['url']       = $request->url();
        $data['action']    = $action;
        $data['describe']  = $describe;
        $data['admin_id']  = $adminid;
        $res = $this->modelActionLog->setInfo($data);

        return $res || !empty($res) ? ['code' => CodeEnum::SUCCESS, 'msg' =>'日志添加成功', '']
            : ['code' => CodeEnum::ERROR, 'msg' => '加入操作日志失败'];
    }

    /**
     * 获取日志总数
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @return mixed
     */
    public function getLogCount($where = []){
        return $this->modelActionLog->getCount($where);
    }

    /**
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @param bool|string $field
     * @param string $order
     * @param int $paginate
     *
     * @return mixed
     */
    public function getLogList($where = [] , $field = true, $order = 'create_time desc',$paginate = 0){
        $this->modelActionLog->limit = !$paginate;
        return $this->modelActionLog->getList($where, $field, $order,$paginate);
    }

    /**
     *
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @return array
     */
    public function logDel($where = [])
    {
        return $this->modelActionLog->deleteInfo($where) ? ['code' => CodeEnum::SUCCESS, 'msg' =>'日志删除成功', '']
            : ['code' => CodeEnum::ERROR, 'msg' => '删除操作日志失败'];
    }
}
