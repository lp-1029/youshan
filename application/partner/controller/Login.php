<?php
/**
 * Created by PhpStorm.
 * User: 紫殇
 * Date: 2018/11/29
 * Time: 14:49
 */

namespace app\partner\controller;

use think\Controller;
use think\Db;
class Login extends Controller
{
    /*
     * 合伙人登陆控制器
     * */
    public function index()
    {
        if (null!==input('sub'))
        {
            $data['pa_user']=input('user');
            $data['pa_pwd']=hash('sha256',input('pwd'));
            //print_r($data);die;
            $validate =validate('Login');
            if(!$validate->check($data)){
                $this->error(''.print_r($validate->getError()));
            }
            $rs=Db::name('partner')->where($data)->find();
            if ($rs)
            {
                \session('pa_user',$rs['pa_user']);
                \session('pa_id',$rs['pa_id']);
                $this->success('登陆成功',url("Index/index"));
            }
            else
            {
                $this->error('登陆失败');
            }
        }
        else
         return view('index');
    }
    public function outlogin()//退出登陆，清除session
    {
        session(null);
        header("Location:".url("Login/index"));
        die;
    }
}