<?php
namespace app\admin\Validate;

use think\Validate;

class Type extends Validate
{
    protected $rule = [
        't_name'  =>  'require',
        't_fid'=>'number',
    ];
}
