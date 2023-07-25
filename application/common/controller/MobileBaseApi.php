<?php

namespace app\common\controller;

use app\common\library\MobileToken;
use PhpOffice\PhpSpreadsheet\IOFactory;
use think\Controller;
use think\exception\HttpResponseException;
use think\Request;
use think\Response;

class MobileBaseApi extends Controller
{

    protected $request;

    protected $noNeedLogin = [];

    public $user;


    public function __construct(Request $request = null)
    {
        $this->request = is_null($request) ? Request::instance() : $request;

        $this->_initialize();
    }

    protected function _initialize()
    {
        check_cors_request();

        // 移除html标签
        $this->request->filter('trim,strip_tags,htmlspecialchars');

        if (!$this->match($this->noNeedLogin)) {
            // 检测登录
            $this->checkLogin();
        }


    }

    protected function match(Array $arr = [])
    {
        $arr = array_map('strtolower', $arr);

        if (in_array(strtolower($this->request->action()), $arr) || in_array('*', $arr)) {
            return true;
        }

        return false;
    }


    protected function checkLogin()
    {
        $token = $this->request->server('HTTP_TOKEN', $this->request->request('token', \think\Cookie::get('token')));

        if (!$token) {
            $this->error('请先登录', null, 401);
        }

        $userTokenInfo = MobileToken::getInstance()->get($token);

        if (!$userTokenInfo) {
            $this->error('登录已过期，请重新登录', null, 401);
        }

        $user =  \app\common\model\User::get($userTokenInfo['uid']);

        if (!$user) {
            $this->error('用户不存在', null, 401);
        }

        $this->user = $user;

        return true;

    }



    protected function success($msg = '', $data = null, $code = 1, $type = null, array $header = [])
    {
        $this->result($msg, $data, $code, $type, $header);
    }

    /**
     * 操作失败返回的数据
     * @param string $msg    提示信息
     * @param mixed  $data   要返回的数据
     * @param int    $code   错误码，默认为0
     * @param string $type   输出类型
     * @param array  $header 发送的 Header 信息
     */
    protected function error($msg = '', $data = null, $code = 0, $type = null, array $header = [])
    {
        $this->result($msg, $data, $code, $type, $header);
    }

    /**
     * 返回封装后的 API 数据到客户端
     * @access protected
     * @param mixed  $msg    提示信息
     * @param mixed  $data   要返回的数据
     * @param int    $code   错误码，默认为0
     * @param string $type   输出类型，支持json/xml/jsonp
     * @param array  $header 发送的 Header 信息
     * @return void
     * @throws HttpResponseException
     */
    protected function result($msg, $data = null, $code = 0, $type = null, array $header = [])
    {
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'time' => Request::instance()->server('REQUEST_TIME'),
            'data' => $data,
        ];
        // 如果未设置类型则自动判断
        $type = $type ? $type : ($this->request->param(config('var_jsonp_handler')) ? 'jsonp' : 'json');

        if (isset($header['statuscode'])) {
            $code = $header['statuscode'];
            unset($header['statuscode']);
        } else {
            //未设置状态码,根据code值判断
            $code = $code >= 1000 || $code < 200 ? 200 : $code;
        }
        $response = Response::create($result, $type, $code)->header($header);
        throw new HttpResponseException($response);
    }



    /**
     * 获取逻辑层实例  --魔术方法
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $logicName
     * @return \think\Model|\think\Validate
     */
    public function __get($logicName)
    {
        $layer = $this->getLayerPre($logicName);

        $model = sr($logicName, $layer);

        return VALIDATE_LAYER_NAME == $layer ? validate($model) : model($model, $layer);
    }

    /**
     * 获取层前缀
     *
     * @author 勇敢的小笨羊 <brianwaring98@gmail.com>
     *
     * @param $name
     * @return bool|mixed
     */
    public function getLayerPre($name)
    {

        $layer = false;

        $layer_array = [MODEL_LAYER_NAME, LOGIC_LAYER_NAME, VALIDATE_LAYER_NAME, SERVICE_LAYER_NAME];
        foreach ($layer_array as $v)
        {
            if (str_prefix($name, $v)) {

                $layer = $v;

                break;
            }
        }

        return $layer;
    }
}