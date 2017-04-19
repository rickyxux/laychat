<?php

namespace app\index\controller;

use app\index\model\UserModel;
use app\index\model\UserRoleModel;
use think\Controller;
use think\Request;

class User extends Base
{



    /**
     * 获取用户列表
     *
     * @return \think\Response
     */
    public function index()
    {

        $return = array();

        $limit = input('pageSize');   //每页显示多少条
        $pageNumber = (input('pageNumber')>0)? input('pageNumber') : 1;
        $offset = ($pageNumber-1)*$limit;   //开始数
        //查询条件
        $searchText = input('searchText');
        if (isset($searchText) &&!empty($searchText)) {
            $where['username'] = array('like', '%'.$searchText.'%');
        } else {
            $where = '';
        }

        $us = new UserModel();
        $return['total_num'] = $us->where($where)->count();

        if ($return['total_num']==0) {
            return json(['code' => 0, 'data' => '', 'msg' => '暂无数据！']);
        }

        $return['total_page'] = ceil($return['total_num']/$limit);
        $return['list'] = $us->getUserBywhere($where, $offset, $limit);

        if (count($return['list'])==0) {
            return json(['code' => 0, 'data' => '', 'msg' => '本页没有请求的数据！']);
        }

        if ($return['total_num']-count($return['list'])-($pageNumber-1)*$limit>0) {
            $return['is_has'] = true;
        } else {
            $return['is_has'] = false;
        }


        return json(['code' => 1, 'data' => $return, 'msg' => '请求数据成功！']);

    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 保存新建的用户
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $param = input('post.');
        $username = $param['username'];
        $pwd = $param['password'];

        $us = new UserModel();
        //检测
        $check_up = $us->checkNewuser($username, $pwd);
        if ($check_up['code']==0) {
            return json($check_up);
        }
        $param['password'] = md5($pwd);
        $add = $us->addUser($param);
        return json($add);
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $us = new UserModel();
        $user = $us->where('id', $id)->find();

        if (!$user) {
            return json(['code' => 0, 'data' => '', 'msg' => '用户不存在！']);
        }


        $ro = new UserRoleModel();
        $role = $ro->getUserRole($id);

        $user['role_name'] = $role;

        return json(['code' => 1, 'data' => $user, 'msg' => '获取用户成功！']);

    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @return \think\Response
     */
    public function update($id)
    {
        $param = input('put.');

        $us = new UserModel();
        $param['id'] = $id;
        //判断用户是否存在
        $isset = $us->where('id', $id)->find();
        if (!$isset) {
            return json(['code' => 0, 'data' => '', 'msg' => '用户不存在！']);
        }

        $username = trim($param['username']);
        $pwd = trim($param['password']);

        if ($username!=$isset['username']) {
            $check = $us->where('username', $username)->find();
            if ($check) {
                return json(['code' => 0, 'data' => '', 'msg' => '用户名已存在！']);
            }

            $name_len = strlen($username);
            if ($name_len<6||$name_len>20) {
                return json(['code' => 0, 'data' => '', 'msg' => '用户名长度必须在6～20位！']);
            }
        }

        if (!empty($pwd)) {
            if (strlen($pwd)<6) {

                return json(['code' => 0, 'data' => '', 'msg' => '密码不得小于6位！']);
            }
            $param['password'] = md5($pwd);
        } else {
            unset($param['password']);
        }


        //更新用户信息
        $update = $us->updateUser($param);
        return json($update);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $us = new UserModel();
        $isset = $us->where('id', $id)->find();
        if (!$isset) {
            return json(['code' => 0, 'data' => '', 'msg' => '删除的用户不存在！']);
        }

        $delete = $us->where('id', $id)->delete();
        if ($delete) {
            return json(['code' => 1, 'data' => '', 'msg' => '用户删除成功！']);
        } else {
            return json(['code' => 0, 'data' => '', 'msg' => '用户删除失败！']);
        }

    }
}
