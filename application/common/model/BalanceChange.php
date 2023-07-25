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

namespace app\common\model;


class BalanceChange extends BaseModel
{
   const DEPOSITE = 1;
   const CHANGE = 2;
   const DAIFUA_SUCCESS = 3;
   const CHONGZHENG = 4;
   public $type_reason=[
	   '1'=>'充值上分',
	   '2' => '调整余额',
	   '3'=>'代付成功',
	   '4'=>'冲正',
	   '5'=>'补单',
   ];
}
