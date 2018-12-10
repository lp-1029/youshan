<?php
/**
 * Created by PhpStorm.
 * User: 紫殇
 * Date: 2018/11/21
 * Time: 9:22
 */

namespace app\shop\controller;

use think\Controller;
use think\Db;
use think\Session;
class Login extends Controller
{
    /*
     * 登陆控制器
     * */
    public function index()//登陆主页
    {
        if (null!==input('sub'))
        {
            $data['b_user']=input('user');
            $data['b_pwd']=hash('sha256',input('pwd'));
            $validate =validate('Login');
            if(!$validate->check($data)){
                $this->error(''.print_r($validate->getError()));
            }
            $rs=Db::name('business')->where($data)->find();
            if ($rs)
            {
                \session('b_user',$rs['b_user']);
                \session('b_id',$rs['b_id']);
                $this->success('登陆成功',url("Index/index"));
            }
            else
            {
                $this->error('登陆失败');
            }
        }
        else
        {
            return view("index");
        }
    }

    public function outlogin()//退出登陆，清除session
    {
        session(null);
        header("Location:".url("Login/index"));
        die;
    }
}