<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

use think\Url;
function business_indexLogin()
{
    $b_user=session("b_user");
    $url=url("Login/index");
    if($b_user=="")
    {
        header("Location:".$url);
        die();
    }
}
function partner_indexLogin()
{
    $pa_user=session("pa_user");
    $url=url("Login/index");
    if($pa_user=="")
    {
        header("Location:".$url);
        die();
    }
}
function panlogin()
{
    $uname=Session('name');
    //$url="../application/admin/controller/Index/login";
    $url=Url::build('admin/Login/index');
    if($uname==''){
        header('location:'.$url);
    }
}
