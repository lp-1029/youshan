<?php
namespace app\admin\controller;
use think\Controller;
use think\Session;
use think\Db;

class Contacts extends Comm
{
    public function index(){
        if(input('sname')){
            session::set('con_sname',input('sname'));
        }
        $name=session::get('con_sname');
        $model=Db::name('contacts')
            ->alias('con')
            ->where(" c.c_user like '%$name%'")
            ->join("customer c",'c.c_id=con.con_c_id')
            ->field("con.*,c.c_user")
            ->paginate(10);
        $page=$model->render();
        return view('index',['model'=>$model,"page"=>$page]);
    }
    public function diesession(){
        $name=session::get('name');
        $pwd=session::get('pwd');
        session::clear();
        session::set('name',$name);
        session::set('pwd',$pwd);
        return $this->index();
    }
}