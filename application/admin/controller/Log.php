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


use app\common\library\enum\CodeEnum;
use think\Db;
use think\helper\Time;

class Log extends BaseAdmin
{

    /**
     * 管理行为日志
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @return mixed
     */
    public function index(){


        return $this->fetch();
    }

    /**
     * 获取管理日志记录
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     */
    public function getList(){

        $where = [];
        //组合搜索
        !empty($this->request->param('uid')) && $where['uid']
            = ['eq', $this->request->param('uid')];

        !empty($this->request->param('action')) && $where['action']
            = ['like', '%' . $this->request->param('action') . '%'];

        !empty($this->request->param('describe')) && $where['describe']
            = ['like', '%' . $this->request->param('describe') . '%'];

        !empty($this->request->param('module')) && $where['module']
            = ['like', '%'.$this->request->param('module').'%'];

        !empty($this->request->param('ip')) && $where['ip']
            = ['eq', $this->request->param('ip')];

        //时间搜索  时间戳搜素
        $where['create_time'] = $this->parseRequestDate();
        if(is_admin_login() != 1){
            $where['admin_id'] = is_admin_login();
        }
        $data = $this->logicLog->getLogList($where, true, 'create_time desc', false);

        $count = $this->logicLog->getLogCount($where);

        $this->result($data || !empty($data) ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg'=> '',
                'count'=>$count,
                'data'=>$data
            ] : [
                'code' => CodeEnum::ERROR,
                'msg'=> '暂无数据',
                'count'=>$count,
                'data'=>$data
            ]
        );
    }

    /**
     * 日志删除
     */
//    public function logDel($id = 0)
//    {
//        $this->result($this->logicLog->logDel(['id' => $id]));
//    }

    /**
     * 日志清空
     */
//    public function logClean()
//    {
//        $this->result($this->logicLog->logDel(['status' => '1']));
//    }


    /**
     * 短信日志
     */
    public function smsLogIndex()
    {
        return $this->fetch();
    }

    public function getSmsLogLists()
    {

        if (is_admin_login() != '1'){
            $where['admin_id'] = is_admin_login();
        }

        list($start,$end) = Time::today();
        $where['a.create_time'] =  [
            'between',!empty($this->request->param('end'))
                ? [$this->request->param('start'), $this->request->param('end')]
                : [date('Y-m-d H:i:s' ,$start), date('Y-m-d H:i:s' ,$end)]
        ];

        $order_no = $this->request->param('order_no', '');
        $order_no && $where['e.order_no'] = $order_no;

        $username = $this->request->param('username', '');
        $username && $where['m.username'] = ['like', '%'. $username .'%'] ;


        $phone = $this->request->param('phone', '');
        $phone && $where['a.phone'] =  $phone;

        $logs = Db::name('banktobank_sms')
            ->alias('a')
            ->join('ms m', 'm.userid = a.ms_id', 'left')
            ->join('ewm_order e', 'e.id = a.order_id', 'left')
            ->join('ewm_pay_code c', 'c.id = a.code_id', 'left')
            ->where($where)
            ->field('a.*,m.username,e.order_no,e.order_pay_price,c.account_name,c.bank_name,account_number')
            ->order('create_time desc')
            ->paginate($this->request->param('limit') ?? 15);

        $this->result($logs->total() ?
            [
                'code' => CodeEnum::SUCCESS,
                'msg'=> '',
                'count'=>$logs->total(),
                'data'=>$logs->items()
            ] : [
                'code' => CodeEnum::ERROR,
                'msg'=> '暂无数据',
                'count'=>0,
                'data'=>[]
            ]
        );

    }

}