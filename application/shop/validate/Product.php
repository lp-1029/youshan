<?php
/**
 * Created by PhpStorm.
 * User: 紫殇
 * Date: 2018/11/22
 * Time: 8:31
 */

namespace app\shop\validate;

use think\Validate;
class Product extends Validate
{
    /*
     * 商品验证器
     * */
    protected $rule = [
        'p_first'  =>  'require|number',
        'p_rement'=>'require|max:200',
        'p_title'=>'require|max:100',
        'p_cost'=>'require|number',
        'p_date'=>'require|number',
        'p_commission'=>'require|number',
        'file'=>'file|fileExt:jpg,png,jpeg',
    ];
    protected $message = [
        'p_first.require'  =>  '一级栏目必须',
        'p_rement.require' =>  '限制要求必须',
        'p_rement.max' =>  '限制要求不能超过200个字符',
        'p_title.require' =>  '标题必须',
        'p_title.max' =>  '标题不能超过100个字符',
        'p_cost.require' =>  '成本价必须',
        'file.fileExt' =>  '文件必须为jpg,png,jpeg格式',
        'p_date.require' =>  '行程天数必须',
        'p_commission.require' =>  '佣金必须',
    ];


}