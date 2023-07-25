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
use think\queue\command\Subscribe;

class Config extends BaseLogic
{

    /**
     * 获取配置信息
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @param bool $field
     *
     * @return mixed
     */
    public function getConfigInfo($where = [], $field = true){
        return $this->modelConfig->getInfo($where, $field);
    }

    /**
     * 获取配置列表
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $where
     * @param bool $field
     * @param string $order
     * @param int $paginate
     * @return mixed
     */
    public function getConfigList($where = [], $field = true, $order = '', $paginate = 0){
        return $this->modelConfig->getList($where, $field, $order, $paginate);
    }

    /**
     * 系统设置
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param array $data
     * @return array
     */
    public function settingSave($data = []){
        foreach ($data as $name => $value)
        {

            $where = array('name' => $name);

            $this->modelConfig->where($where)->update(['value' => $value]);
//            $this->modelConfig->updateInfo($where, ['value' => $value]);
        }

        return ['code'=>CodeEnum::SUCCESS, 'msg'=>'设置保存成功'];
    }

    public static function isSysEnableRecharge()
    {
        return Db::name('config')->where('name', 'sys_enable_recharge')->value('value') ?? 0;
    }
}