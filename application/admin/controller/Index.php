<?php
namespace app\admin\controller;
use think\Controller;
use think\Session;

class Index	extends Comm
{
    public function index(){
        return view("index");
    }
    public function diesession(){
        $name=session::get('name');
        $pwd=session::get('pwd');
        session::clear();
        session::set('name',$name);
        session::set('pwd',$pwd);
        return $this->index();
    }
    public function dielogin(){
        session::clear();
        header('Location:'.url('login/index'));
    }
}
