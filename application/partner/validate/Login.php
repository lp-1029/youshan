<?php
/**
 * Created by PhpStorm.
 * User: 紫殇
 * Date: 2018/11/29
 * Time: 15:10
 */

namespace app\partner\validate;

use think\Validate;
class Login extends Validate
{
    /*
     * 登陆验证器
     * */
    protected $rule =   [
        'pa_user'  => 'require|max:10',
        'pa_pwd'   => 'require',
    ];
    protected $message  =   [
        'pa_user.require' => '用户名必须',
        'pa_pwd.require' => '密码必须',
        'pa_user.max'     => '用户名最多不能超过10个字符',
    ];


}