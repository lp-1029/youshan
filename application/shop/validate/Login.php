<?php
/**
 * Created by PhpStorm.
 * User: 紫殇
 * Date: 2018/11/22
 * Time: 9:14
 */

namespace app\shop\validate;

use think\Validate;
class Login extends Validate
{
    /*
     * 登陆验证器
     * */
    protected $rule =   [
        'b_user'  => 'require|max:10',
        'b_pwd'   => 'require',
    ];
    protected $message  =   [
        'b_user.require' => '用户名必须',
        'b_pwd.require' => '密码必须',
        'b_user.max'     => '用户名最多不能超过10个字符',
    ];


}