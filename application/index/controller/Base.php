<?php

namespace app\index\controller;

use app\index\model\UserModel;
use think\Controller;

class Base extends Controller
{

    public function _initialize()
    {

        //检测token是否存在
        $token = request()->header('token');
        if (!$token) {
            exit(json_encode(['code' => 0, 'data' => '', 'msg' => '您尚未登录，请登录！']));
        }

        //判断token是否过期
        $us = new UserModel();
        $check = $us->check_token($token);
        if ($check['code']==0) {
            exit(json_encode($check));
        } else {
            $user_id = $check['data']['user_id'];
        }


        //获取控制器和方法
        $control = lcfirst(request()->controller());
        $action = lcfirst(request()->action());
        $method = (request()->method());

        $user_info = $us->getUserPower($user_id);

        if ($user_info['code']==0) {
            exit(json_encode($user_info['msg']));
        }
        $data = $user_info['data'];
        $action_user = $data['action'];
        $method_user = $data['method'];

        $search = array_search($control.'/'.$action, $action_user);
        if (!is_numeric($search)||$method!=$method_user[$search]) {
            exit(json_encode(['code' => 0, 'data' => '', 'msg' => '您没有权限访问！']));
        } else {
            //exit(json_encode(['code' => 0, 'data' => '', 'msg' => '您有权访问！']));
        }




    }

}
