<?php

/**
 * Created by PhpStorm.
 * User: ricky
 * Date: 17-3-24
 * Time: 上午9:45
 */

namespace app\index\validate;


use think\Validate;

class UserValidate extends Validate
{

    protected $rule = [
        'username' => 'require|unique:user',
        'password' =>'require',
    ];

    protected $message = [
        'username.require' => '用户名必须填写',
        'username.unique' => '用户名已存在',
        'password.require' => '密码必须填写'
    ];

    protected $scene = [

    ];
}