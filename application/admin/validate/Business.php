<?php
namespace app\admin\Validate;

use think\Validate;

class Business extends Validate
{
    protected $rule = [
        'b_user'=>'unique:business',
        'b_user'=>'require',
        'b_pwd'=>'require',
        'b_comadd'=>'require',
        'b_company'=>'require',
        'b_legal'=>'require',
        'b_legal_id'=>'require',
        'b_phone'=>'require',
        'b_license'=>'require',
        'b_state'=>'number',
    ];
}
