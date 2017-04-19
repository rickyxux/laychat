<?php

namespace app\index\model;

use app\index\validate\UserValidate;
use Lcobucci\JWT\Parser;
use think\cache\driver\Memcache;
use think\exception\PDOException;
use think\Loader;
use think\Model;

class UserModel extends Model
{

    protected $table = 'tp5_user';


    /**
     * 根据条件获取用户列表
     *
     * @param $where
     * @param $offset
     * @param $limit
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getUserBywhere($where, $offset, $limit) {
        return $this->where($where)->limit($offset, $limit)->select();
    }


    /**
     * 检测新增用户的用户名和密码是否合法
     *
     * @param $username
     * @param $pwd
     * @return array
     */
    public function checkNewuser($username, $pwd) {
        //检测用户名长度是否合法
        $name_len = strlen(trim($username));
        if ($name_len<6||$name_len>20) {
            return ['code' => 0, 'data' => '', 'msg' => '用户名长度必须在6～20位！'];
        }

        //判断该用户名是否已存在
        $unique = $this->where('username', $username)->find();
        if ($unique) {
            return ['code' => 0, 'data' => '', 'msg' => '该用户名已存在！'];
        }

        $pwd_len = strlen($pwd);
        if ($pwd_len<6) {
            return ['code' => 0, 'data' => '', 'msg' => '密码长度不得小于6位！'];
        }


        return ['code' => 1, 'data' => '', 'msg' => '用户名和密码合法！'];
    }


    /**
     * 更新用户信息
     *
     * @param $param
     * @return array
     */
    public function updateUser($param) {
        try{

            $update = $this->save($param, ['id' => $param['id']]);

            if (false===$update) {
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            } else {
                return ['code' => 1, 'data' => '', 'msg' => '用户修改成功'];
            }

        }catch (PDOException $e) {

            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }




    /**
     * 新增用户
     *
     * @param $param
     * @return array
     */
    public function addUser($param) {

        try{

            $validate = $this->validate('UserValidate')->save($param);

            if (false===$validate) {
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            } else {
                return ['code' => 1, 'data' => '', 'msg' => '用户添加成功'];
            }

        }catch (PDOException $e) {

            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }




    }


    /**
     * 获取用户权限
     *
     * @param $id
     * @return array
     */
    public function getUserPower($id)
    {
        $user = $this->where('id', $id)->find();
        if (!$user) {
            return ['code' => 0, 'data' => '', 'msg' => '用户不存在'];
        } else if ($user['username']==='admin') {   //超级管理员拥有所有权限
            $map_no = '';
        } else {
            $role_ids = db('user_role')->where('user_id', $id)->column('role_id');

            $map_node['role_id'] = array('in', $role_ids);
            $node_ids = db('role_node')->where($map_node)->column('node_id');

            $map_no['id'] = array('in', $node_ids);
        }

        $nodes = db('node')->where($map_no)->select();

        $action = array();
        $method = array();
        foreach ($nodes as $key => $vo) {
            if ('#'!=$vo['action_name']) {
                $action[] = $vo['control_name'].'/'.$vo['action_name'];

                switch ($vo['type']) {
                    case 1:
                        $method[] = 'GET';
                        break;
                    case 2:
                        $method[] = 'POST';
                        break;
                    case 3:
                        $method[] = 'DELETE';
                        break;
                    case 4:
                        $method[] = 'PUT';
                        break;
                    default :
                        $method[] = 'GET';
                        break;
                }
            }
        }
        return ['code' => 1, 'data' => ['action' => $action, 'method' => $method], 'msg' => ''];
    }


    /**
     * 检测用户token是否有效
     *
     * @param $token
     * @return array
     */
    public function check_token($token) {

        $mem = new Memcache();

        $limit_time = $mem->get($token);
        if (!$limit_time) {
            return ['code' => 0, 'data' => '', 'msg' => 'token已失效,请重新登录！'];
        } else {
            if (($limit_time-time())<=300) {   //当token失效时间在当前时间的5分钟内时更新token过期时间
                $mem->set($token, time()+3600, time()+3600);
            }

            $token = (new Parser())->parse((string) $token); // Parses from a string
            $user_id = $token->getClaim('user_id');

            return ['code' => 1, 'data' => ['user_id' => $user_id], 'msg' => 'token有效'];
        }
        /*
        try {

            $token = (new Parser())->parse((string) $token);

        } catch (Exception $exception) {
            return false;
        }

        //验证token是否有效
        //$check = new ValidationData();
        //$re = $token->validate($check);





        if ($re) {

            return ['code' => 1, 'data' => '', 'msg' => 'token值有效'];
        } else {
            return ['code' => 0, 'data' => '', 'msg' => 'token值无效！'];
        }
        */
    }
}
