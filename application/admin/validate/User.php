<?php
namespace app\admin\Validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        'ad_user'  =>  'require|max:25',
        'ad_user'=>'unique:admin',
        'ad_pwd' =>  'require',
    ];
}
