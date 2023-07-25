<?php

namespace app\common\behavior;
use think\Response;
use think\Cache;

class CheckProjectLock
{
    public function run(&$params)
    {
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