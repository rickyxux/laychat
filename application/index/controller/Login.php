<?php

namespace app\index\controller;

use app\index\model\UserModel;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use think\cache\driver\Memcache;
use think\Controller;

class Login extends Controller
{


    /**
     * 用户登录
     *
     * @return \think\response\Json
     */
    public function login()
    {

        $user_id = input('post.user_id');
        $pwd = input('post.pwd');
        $us = new UserModel();
        $user = $us->where('id', $user_id)->find();
        if (!$user) {
            return json(['code' => 0, 'data' => '', 'msg' => '用户不存在！']);
        } else if (md5($pwd)!=$user['password']) {
            return json(['code' => 0, 'data' => '', 'msg' => '密码不正确！']);
        }

        $signer = new Sha256();
        $token = (string)(new Builder())
            ->setIssuer('admin')   //设置jwt签发人
            ->setAudience($user_id)   //设置jwt接受人
            ->set('user_id', $user_id)
            ->setExpiration(time()+3600)   //设置jwt过期时间
            ->sign($signer, '123456789')   //设置jwt密钥
            ->getToken();

        //将token放入缓存中
        $mem = new Memcache();
        $mem->set($token, time()+3600, time()+3600);   //缓存1个小时


        //更新用户登录信息
        $data['loginnum'] = $user['loginnum']+1;
        $data['last_login_ip'] = request()->ip();
        $data['last_login_time'] = time();
        $us->save($data, ['id' => $user_id]);

//        $token = (new Parser())->parse((string) $token); // Parses from a string
//        echo $token->getClaim('user_id').'<br>'; // will print "1"
//        echo $token->getClaim('pwd');
        return json(['code' => 1, 'data' => ['token' => $token], 'msg' => '登录成功！']);
    }


    public function loginout() {

        $token = request()->header('token');

        $mem = new Memcache();
        $find = $mem->get($token);
        if ($find) {
            $destroy = $mem->rm($token);
            if ($destroy) {
                return json(['code' => 1, 'data' => '', 'msg' => '退出成功！']);
            } else {
                return json(['code' => 1, 'data' => '', 'msg' => '退出失败！']);
            }
        } else {
            return json(['code' => 1, 'data' => '', 'msg' => '退出成功！']);
        }

    }

}
