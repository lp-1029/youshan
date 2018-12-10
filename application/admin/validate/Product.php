<?php
namespace app\admin\Validate;

use think\Validate;

class Product extends Validate
{
    protected $rule = [
        'p_market'=>'float',
        'p_market'=>'require',
        'p_state'=>'number',
    ];
}