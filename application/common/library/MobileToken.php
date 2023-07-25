<?php

namespace app\common\library;

class MobileToken
{
    private $hashalgo = 'ripemd160';

    private $hashKey = 'fUXpPelBaMH5bZY0YqPhy4C9K17FcAui';

    protected $handler = null;

    public static $instance = null;

    public function __construct()
    {
        $this->handler = \think\Db::name('user_token');
        $time = time();
        $tokentime = cache('tokentime', $time);
        if (!$tokentime || $tokentime < $time - 86400) {
            $this->handler->where('expiretime', '<', $time)->where('expiretime', '>', 0)->delete();
        }
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new MobileToken();
        }

        return self::$instance;
    }

    public function get($token)
    {
        $data = $this->handler->where('token', $this->getEncryptedToken($token))->find();

        if ($data) {
            if (!$data['expiretime'] || $data['expiretime'] > time()) {
                $data['token'] = $token;
                $data['expire_in'] = $this->getExpireIn($data['expiretime']);
                return $data;
            } else {
                $this->handler->where('token', $this->getEncryptedToken($token))->delete();
            }
        }

        return [];

    }

    public function set($token, $uid, $expire = null)
    {
        $expire = !is_null($expire) && $expire !== 0 ? time() + $expire : 0;

        $token = $this->getEncryptedToken($token);

        $this->handler->insert(['token' => $token, 'uid' => $uid, 'createtime' => time(), 'expiretime' => $expire]);

        return true;
    }

    protected function getEncryptedToken($token)
    {
        return hash_hmac($this->hashalgo, $token, $this->hashKey);
    }


    protected function getExpireIn($expiretime)
    {
        return $expiretime ? max(0, $expiretime - time()) : 1000 * 86400;
    }

    public function check($token, $uid)
    {
        $data = $this->get($token);
        return $data && $data['uid'] == $uid ? true : false;
    }

    public function clear($uid)
    {
        $this->handler->where('uid', $uid)->delete();
        return true;
    }
}