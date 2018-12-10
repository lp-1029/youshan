<?php
namespace app\admin\controller;
use think\paginator\driver\Bootstrap;
use think\Db;
use think\Session;

class Partner	extends Comm
{
    public function index(){
        if(input('new_time')){
            session::set('pa_new_time',(" pa_addtime >= ".strtotime(input('new_time'))));
            session::set('pa_new',input('new_time'));
        }
        if(input('new_time')<input('die_time')){
            session::set('pa_die_time',("pa_addtime <= ".strtotime(input('die_time'))));
            session::set('pa_die',input('die_time'));
        }
        if(input('sname')){
            session::set('pa_sname',input('sname'));
        }
        if(input('state')){
            session::set('pa_state',input('state'));
        }
        $new=session::get('pa_new_time');
        $die=session::get('pa_die_time');
        $name=session::get('pa_sname');
        if(session::get('pa_state'))
            $state=session::get('pa_state');
        else
            $state="pa_state";
        $model=Db::name('partner')
            ->where($new)
            ->where($die)
            ->where(" pa_state=".$state)
            ->where(" pa_user like '%$name%'")
            ->paginate('10');//分页显示数据条数
        //print_r($model);
        $page=$model->render();
        return view('index',['model'=>$model,'page'=>$page]);
    }
    public function add(){
        if(isset($_POST['sub'])){
            $pa_user=input('pa_user');
            $pwd=input('newpass');
            $pa_name=input('pa_name');
            $pa_phone=input('pa_phone');
            $date=[
                'pa_user'=>$pa_user,
                'pa_pwd'=>$pwd,
                'pa_name'=>$pa_name,
                'pa_phone'=>$pa_phone,
            ];
            $validate=validate('Partner');
            if($validate->check($date)){
                if(Db::name('partner')->insert($date))
                    $this->success('添加成功','index');
                else
                    $this->error('添加失败','index');
            }

        }
        return view('add');
    }
    public function update(){
        if(isset($_POST['sub'])){
            $pa_id=input('pa_id');
            if(Db::name('partner')->where('pa_id',$pa_id)->update(['pa_state'=>1]))
                $this->success('修改成功','index');
            else
                $this->error('修改失败','index');
        };
        if(isset($_POST['out'])){
            $pa_id=input('pa_id');
            if(Db::name('partner')->where('pa_id',$pa_id)->update(['pa_state'=>2]))
                $this->success('修改成功','index');
            else
                $this->error('修改失败','index');
        }
        $pa_id=$_GET['pa_id'];
        $model=Db::name('partner')->where('pa_id',$pa_id)->find();
        return view('update',['model'=>$model]);
    }
    public function dele(){
        $id=input('id/a');
        if(Db::name('partner')->where("pa_id",'in',$id)->update(['pa_state'=>2]))
            $this->success('操作成功','index');
        else
            $this->error('操作失败','index');
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