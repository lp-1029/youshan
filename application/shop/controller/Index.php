<?php
/**
 * Created by PhpStorm.
 * User: 紫殇
 * Date: 2018/11/20
 * Time: 15:51
 */

namespace app\shop\controller;

use think\Controller;
business_indexLogin();
class Index extends Controller
{
    /*
     * 主页控制器
     * */
    public function index()
    {
        return view("index");
    }
}