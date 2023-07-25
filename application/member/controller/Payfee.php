<?php
/**
 * 囤福家园
 *
 * @copyright © 2022 by 技术先锋 All rights reserved。
 * @Created   by PhpStorm.
 * @author    StarsPhp
 * @date      2022/12/7
 */
declare (strict_types=1);

namespace app\member\controller;

use think\Db;

class Payfee extends Base
{
    public function index()
    {
        return $this->fetch();
    }

    public function getfee(){
        $where = [];
        if (!empty($this->request->param('name'))){
            $where['c.name'] = trim($this->request->param('name'));
        }
        if (!empty($this->request->param('code'))){
            $where['c.code'] = trim($this->request->param('code'));
        }
        $codes = Db::name('ms_rate')
                    ->alias('r')
                    ->join('pay_code c','r.code_type_id = c.id')
                    ->where($where)
                    ->where('r.ms_id',$this->agent_id)
                    ->where('c.status',1)
                    ->field('r.rate,c.name,c.code,r.create_time')
                    ->select();
        if (empty($codes)){
            $this->result(['code'=>1,'msg'=>'暂无数据']);
        }
        $count = count($codes);
        $this->result(['code'=>0,'msg'=>'请求成功','data'=>$codes,'count'=>$count]);
    }
}
