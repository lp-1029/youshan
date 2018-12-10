<?php
namespace app\admin\controller;
use think\paginator\driver\Bootstrap;
use think\Controller;
use think\Session;
use think\Db;

class Busicash	extends Comm
{
    public function index(){
        if(input('new_time')){
            session::set('bus_new_time',(" bus.bus_addtime >= ".strtotime(input('new_time'))));
            session::set('bus_new',input('new_time'));
        }
        if(input('new_time')<input('die_time')){
            session::set('bus_die_time',("bus.bus_addtime <= ".strtotime(input('die_time'))));
            session::set('bus_die',input('die_time'));
        }
        if(input('sname')){
            session::set('bus_sname',input('sname'));
        }
        if(input('state')){
            session::set('bus_state',input('state'));
        }
        $new=session::get('bus_new_time');
        $die=session::get('bus_die_time');
        $name=session::get('bus_sname');
        if(session::get('bus_state'))
            $state=session::get('bus_state');
        else
            $state="bus.bus_state";
        $question=Db::name('busicash')
            ->alias('bus')
            ->join('business b','b.b_id=bus.bus_id')
            ->join('busibank bu','bu.bu_id=bus.bus_bu_id')
            ->where(" bus_state=".$state)
            ->where($new)
            ->where($die)
             ->where(" b_user like '%$name%'")
            ->paginate('10');//分页显示数据条数
        $page=$question->render();
        return view('index',['model'=>$question,'page'=>$page]);
    }
    public function update(){
        $id=input('id/a');
        if(Db::name('busicash')->where('bus_id','in',$id)->update(['bus_state'=>2,'bus_turntime'=>time()])){
            return $this->success('提现成功','index');
        }else{
            Db::name('busicash')->where('bus_id','in',$id)->update(['bus_state'=>3,'bus_turntime'=>time()]);
            return $this->error('提现失败失败','index');
        }
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