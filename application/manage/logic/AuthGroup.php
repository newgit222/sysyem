<?php

namespace app\manage\logic;

use app\common\logic\BaseLogic;

class AuthGroup extends BaseLogic
{
    public function getAuthGroupList($where = [], $field = true, $order = '', $paginate = false)
    {
        return $this->modelAuthGroup->getList($where, $field, $order, $paginate);
    }

}