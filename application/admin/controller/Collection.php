<?php
namespace app\admin\controller;
use think\Controller;
use think\Session;
use think\Db;

class Collection extends Comm
{
    public function index(){
        if(input('new_time')){
            session::set('co_new_time',(" co.co_addtime >= ".strtotime(input('new_time'))));
            session::set('co_new',input('new_time'));
        }
        if(input('new_time') < input('die_time')){
            session::set('co_die_time',("co.co_addtime <= ".strtotime(input('die_time'))));
            session::set('co_die',input('die_time'));
        }
        if(input('sname')){
            session::set('co_sname',input('sname'));
        }
        if(input('state')){
            session::set('co_state',input('state'));
        }
        $new=session::get('co_new_time');
        $die=session::get('co_die_time');
        $name=session::get('co_sname');
        $model=Db::name('collection')
            ->alias('co')
            ->where($new)
            ->where($die)
            ->where(" p.p_title like '%$name%'")
            ->join("product p",'p.p_id=co.co_p_id')
            ->join("customer c",'c.c_id=co.co_c_id')
            ->field("p.p_title,c.c_user")
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