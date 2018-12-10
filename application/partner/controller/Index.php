<?php
/**
 * Created by PhpStorm.
 * User: 紫殇
 * Date: 2018/11/29
 * Time: 10:09
 */

namespace app\partner\controller;

use think\Controller;
partner_indexLogin();
class Index extends Controller
{
    /*
     * 合伙人首页控制器
     * */
    public function index()//首页
    {
        return view('index');
    }
}