<?php
namespace app\admin\Validate;

use think\Validate;

class Partner extends Validate
{
    protected $rule = [
        'pa_user'=>'unique:business',
        'pa_user'=>'require',
        'pa_pwd'=>'require',
        'pa_name'=>'require',
        'pa_phone'=>'',
    ];
}
