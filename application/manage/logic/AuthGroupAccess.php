<?php

namespace app\manage\logic;

use app\common\logic\BaseLogic;

class AuthGroupAccess  extends BaseLogic
{
    /**
     * 根据groupId获取用户权限组
     * @param int $groupId
     * @return mixed
     */
    public function getUserGroupInfoByGroupId($groupId = 0)
    {

        $this->modelAuthGroupAccess->alias('g');

        is_array($groupId) ? $where['g.group_id'] = ['in', $groupId] : $where['g.group_id'] = $groupId;


        $field = 'g.uid, g.group_id, a.nickname';

        $join = [
            ['admin a', 'g.uid = a.id'],
        ];

        $this->modelAuthGroupAccess->join = $join;

        return $this->modelAuthGroupAccess->getList($where, $field, '', false);
    }

}