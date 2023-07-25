<?php


namespace app\index\controller;


use app\common\library\enum\OrderStatusEnum;

class Agent extends Base
{

    /**
     * 获取数据统计
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     *
     * @return mixed
     */
    public function index(){
        $where = ['puid'=> is_login()];
        $this->assign('list', $this->logicUser->getUserList($where, true, 'create_time desc', 10));
        return $this->fetch();
    }

    /**
     * 下级交易明细
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     *
     * @return mixed
     */
    public function orders()
    {
        $where['puid'] = is_login();
        $User = new \app\common\model\User();
        $userList = $User->where($where)->select();
        $userIds = [];
        foreach ($userList as $user) {
            $userIds[] = $user['uid'];
        }

        unset($where);

        $where['uid'] =  array('in', $userIds);
        //组合搜索
        !empty($this->request->get('trade_no')) && $where['trade_no']
            = ['like', '%' . $this->request->get('trade_no') . '%'];

        !empty($this->request->get('channel')) && $where['channel']
            = ['eq', $this->request->get('channel')];

//        //时间搜索  时间戳搜素
       $where['create_time'] = $this->parseRequestDate();

        //状态
        if (!empty($this->request->get('status')) || $this->request->get('status') === '0') {
            $where['status'] = $this->request->get('status');
        }
        if (empty($userIds))
        {
            $list = [];
        }
        else
        {
            $list = $this->logicOrders->getOrderList($where,true, 'create_time desc', 10);
        }
        //这里循环算了以免影响到公共逻辑获取数据

        foreach($list as $k=>$v)
        {
              $uinfo = $this->logicUser->getUserInfo(['uid'=>$v['uid']],'username');
              $list[$k]['username'] = $uinfo['username'];
        }

        //代理下面的所有商户

        $map['puid'] = session('user_auth.uid');
        $usersInagent = $this->logicUser->getUserList($map, 'uid,puid,username', 'create_time desc', false);
//dd($usersInagent);
        $this->assign('list',$list);
        $this->assign('usersInagent',$usersInagent);
        $this->assign('code',$this->logicPay->getCodeList([]));

        return $this->fetch();
    }



    public function daifuOrders(){
        $User = new \app\common\model\User();
        $userList = $User->where(['puid'=>is_login()])->column('uid');

        //组合搜索
        !empty($this->request->get('trade_no')) && $where['trade_no']
            = ['like', '%' . $this->request->get('trade_no') . '%'];



//        //时间搜索  时间戳搜素
        //时间搜索  时间戳搜素
        $date = $this->request->param('d/a');
        // print_r($this->request->get('type_reason'));die;

        $start = empty($date['start']) ? date('Y-m-d ')." 00:00:00" : $date['start'];
        $end = empty($date['end']) ? date('Y-m-d')." 23:59:59" : $date['end'];
        $where['create_time'] = ['between', [strtotime($start), strtotime($end)]];


        $where['uid'] = ['in',$userList];
//print_r($userList);die;
         if (!empty($this->request->get('uid'))){
             if (!in_array($this->request->get('uid'),$userList)){
                 $this->error('非法请求');
             }
             $where['uid'] = $this->request->get('uid');
         }




//        if (!in_array($where['uid'],$userList) ){
//            $this->error('非法请求');
//        }

        //状态
        if (!empty($this->request->get('status')) || $this->request->get('status') === '0') {
            $where['status'] = $this->request->get('status');
        }


        $orderLists = $this->logicDaifuOrders->getOrderList($where, true, 'create_time desc', 10);
        $cals = $this->logicDaifuOrders->calOrdersData($where);
        $this->assign('cal', $cals);
//        $list = $data->items();
//        print_r($list);die;
//        foreach ($data->items() as $k=>$v){
//            $uinfo = $this->logicUser->getUserInfo(['uid'=>$v['uid']],'username');
//            $data->items()[$k]['username'] = $uinfo['username'];
//        }
        $this->assign('start', $start);
        $this->assign('end', $end);
        $this->assign('list', $orderLists);
        $where1['puid'] = is_login();
        $usersInagent = $User->where($where1)->select();
        $this->assign('usersInagent',$usersInagent);
        return $this->fetch('daifuOrders');
    }

    /**
     * 新增下级商户
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     *
     * @return mixed
     */
    public function addUser(){

        if($this->request->isPost()){
            if ($this->request->post('u/a')['puid'] == is_login()){
                //创建商户
                $this->result($this->logicLogin->doregister($this->request->post('u/a')));
            }else{
                $this->result(0,'非法操作，请重试！');
            }
        }
        return $this->fetch();
    }

}
