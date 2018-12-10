<?php
/**
 * Created by PhpStorm.
 * User: 念心
 * Date: 2018/11/19
 * Time: 19:44
 */

namespace app\admin\controller;
use think\Controller;
class Comm extends Controller
{
    public function _initialize()
    {
        panlogin();
    }
}