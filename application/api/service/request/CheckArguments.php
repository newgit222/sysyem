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

/**
 * Author: 勇敢的小笨羊
 * Github: https://github.com/SingleSheep
 */

namespace app\api\service\request;
use app\common\library\exception\ParameterException;
use app\common\library\HttpHeader;
use think\Log;
use think\Request;


/**
 * 检验网关公共必传参数
 *
 * check Gateway's common arguments
 */
class CheckArguments extends ApiCheck
{
    /**
     * 网关参数
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @var array
     */
    private $commonArgus = [
        // 授权API KEY
        'mchid',
        // 数据签名
        'sign',
    ];


    /**
     * 校验公共头参数
     *
     * check Gateway's common arguments
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param Request $request
     * @return mixed|void
     * @throws ParameterException
     */
    public function doCheck(Request $request)
    {
        $params = $request->post();

        if (strtolower($request->action()) == 'balance'){
            if (!isset($params['mchid'])  && isset($params['uid']) ){
                $this->commonArgus[0] = 'uid';
            }
        }

        foreach ($this->commonArgus as $v) {
            if (! isset($params[$v]) || empty($params[$v])) {
                throw new ParameterException([
                    'msg'=>"缺少参数[{$v}]",
                    'errorCode'=>400000
                    ]);
            }
        }
    }
}
