<?php
namespace app\admin\controller;
use think\paginator\driver\Bootstrap;
use think\Db;
use think\Session;

class Type	extends Comm
{
    public function index(){
        if(isset($_POST['sname']))
            session::set('t_sname',input('sname'));
        $name=session::get('t_sname');
        $question=Db::name('type')
            ->field('t_id,t_name,t_fid')
            ->where('t_fid=0')
            ->where(" t_name like '%$name%'")
            ->paginate('5');//分页显示两条数据
        $page=$question->render();
        return view('index',['model'=>$question,'page'=>$page]);
    }
    public function look(){
        if(isset($_GET['t_id']))
            session::set('t_id',$_GET['t_id']);
        if(isset($_POST['sname']))
            session::set('t_l_sname',input('sname'));
        $name=session::get('t_l_sname');
        $t_id=session::get('t_id');
        $question=Db::name('type')
            ->field('t_id,t_name,t_fid')
            ->where('t_fid='.$t_id)
            ->where(" t_name like '%$name%'")
            ->paginate('5');//分页显示两条数据
        $page=$question->render();
        return view('tow',['model'=>$question,'page'=>$page]);
    }
    public function xiu(){
        $t_id=$_GET['t_id'];
        $question=Db::name('type')->field('t_id,t_name,t_fid')->where('t_id='.$t_id)->find();
        return view('xiu',['model'=>$question]);
    }
    public function xiu_tow(){
        $t_id=$_GET['t_id'];
        $question=Db::name('type')->field('t_id,t_name,t_fid')->where('t_id='.$t_id)->find();
        return view('xiu',['model'=>$question]);
    }
    public function xiugai(){
        if(input('sub')){
            $t_name=input('t_name');
            $t_id=input('t_id');
            $validate=validate('Type');
            $date=[
                't_name'=>$t_name
            ];
            if($validate->check($date)){
                $query=Db::name('type')->where('t_id',$t_id)->update(['t_name'=>$t_name]);
                if($query)
                    $this->success('修改成功','index');
                else
                    $this->error('修改失败','index');
            }else{
                $validate->getError();
            }
        }
    }
    public function add(){
        if(isset($_POST['t_name'])){
            $t_name=input('t_name');
            $data=['t_name'=>$t_name];
            $validate=validate('Type');
            if($validate->check($date)){
                if(Db::name('type')->insert(['t_name'=>$t_name,'t_fid'=>0]))
                    $this->success('添加成功','index');
                else
                    $this->error('添加失败','index');
            }else{
                $validate->getError();
            }
        }
        return view('add');
    }
    public function add_tow(){
        if(isset($_POST['t_id'])){
            $t_fid=input('t_id');
            $t_name=input('t_name');
            $date=[
                't_name'=>$t_name,
                't_fid'=>$t_fid,
            ];
            $validate=validate('Type');
            if($validate->check($date)){
                if(Db::name('admin')->insert($date))
                    $this->success('添加成功','index');
                else
                    $this->error('添加失败','index');
            }else{
                $validate->getError();
            }
        }
        $model=Db::name('type')->where('t_fid',0)->select();
        return view('add_tow',['model'=>$model]);
    }
    public function dele(){
        $id=input('id/a');
        if(Db::name('type')->where("t_id",'in',$id)->delete())
            $this->success('删除成功','index');
        else
            $this->error('删除失败','index');
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