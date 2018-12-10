<?php
namespace app\admin\controller;
use think\Controller;
use think\Session;
use think\Db;

class Login	extends Controller
{
    public function index(){
        //判断是否登录页面传值如果没传值将映射到登录页面
        if(isset($_POST['sub'])){
            $name=input('name');
            $pwd=hash('sha256',input('password'));
            //上两个接受登录页面的值
            $ip=request()->ip();
            //获取登录的IP地址
            $time=time();
            //当前时间戳
            $date=[
                'ad_user'=>$name,
                'ad_pwd'=>$pwd,
            ];
            if(Db::name("admin")->where($date)->find()){
                session::set('name',$name);
                session::set('pwd',$pwd);
                Db::name('history')->insert(['h_user'=>$name,'h_pwd'=>$pwd,'h_type'=>'超级管理员','h_ip'=>$ip,'h_state'=>'1','h_logintime'=>$time]);
                //查询成功后创建session并将登录的名密码IP时间戳全部存到history表中并跳转
                $this->success('登录成功',url('index/index'));
            }else{
                Db::name('history')->insert(['h_user'=>$name,'h_pwd'=>$pwd,'h_type'=>'超级管理员','h_ip'=>$ip,'h_state'=>'2','h_logintime'=>$time]);
                //即使失败也要添加到history表中
                $this->error("登陆失败",'index');
            }
        }else{
            return view("login");
        }

    }
}