<?php

namespace app\index\model;

use think\Model;

class UserRoleModel extends Model
{
    protected $table = 'tp5_user_role';


    /**
     * 获取用户角色
     *
     * @param $id
     * @return array
     */
    public function getUserRole($id) {

        $role_id = $this->where('user_id', $id)->column('role_id');

        $role = db('role')->where(['id' => ['in', $role_id]])->column('rolename');

        return $role;

    }

}
