<?php

namespace app\common\behavior;
use think\Response;
use think\Cache;

class CheckProjectLock
{
    protected $allowableIps =  ['68.178.164.76','148.72.244.40','154.23.179.35'];
    public function run(&$params)
    {
        if (!in_array(get_userip(), $this->allowableIps)) {
            header('HTTP/1.1 404 Not Found');
            exit;
        }

        ini_set("max_execution_time", "120");
        // 检查项目是否被锁定
        $project_locked = Cache::get('project_locked');
        if ($project_locked) {
            // 项目被锁定，返回Nginx默认的404状态码
            header('HTTP/1.1 404 Not Found');
            exit;
        }
    }
}