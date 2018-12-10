<?php
namespace app\admin\controller;
use think\paginator\driver\Bootstrap;
use think\Db;
use think\Session;

class Business	extends Comm
{
    public function index(){
        if(input('new_time')){
            session::set('b_new_time',(" b_time >= ".strtotime(input('new_time'))));
            session::set('b_new',input('new_time'));
        }
        if(input('new_time') < input('die_time')){
            session::set('b_die_time',("b_time <= ".strtotime(input('die_time'))));
            session::set('b_die',input('die_time'));
        }
        if(input('sname')){
            session::set('b_sname',input('sname'));
        }
        if(input('state')){
            session::set('b_state',input('state'));
        }
        $new=session::get('b_new_time');
        $die=session::get('b_die_time');
        $name=session::get('b_sname');
        if(session::get('b_state'))
            $state=session::get('b_state');
        else
            $state="b_state";
        $question=Db::name('business')
            ->where($new)
            ->where($die)
            ->where(" b_state=".$state)
            ->where(" b_user like '%$name%'")
            ->paginate('10');//分页显示数据条数
        $page=$question->render();
        return view('index',['model'=>$question,'page'=>$page]);
    }
    public function add(){
        if(isset($_POST['sub'])){
            $user=input('b_user');
            $pwd=input('newpass');
            $b_company=input('b_company');
            $b_comadd=input('b_comadd');
            $b_legal=input('b_legal');
            $b_legal_id=input('v');
            $b_phone=input('b_phone');
            $b_license=input('b_license');
            $b_state=input('b_state');
            $date=[
                'b_user'=>$user,
                'b_pwd'=>$pwd,
                'b_comadd'=>$b_comadd,
                'b_company'=>$b_company,
                'b_legal'=>$b_legal,
                'b_legal_id'=>$b_legal_id,
                'b_phone'=>$b_phone,
                'b_license'=>$b_license,
                'b_state'=>$b_state,
            ];
            $validate=validate('Business');
            if($validate->check($date)){
                if(Db::name('business')->insert($date))
                    $this->success('添加成功','index');
                else
                    $this->error('添加失败','indesx');
            }else{
                return $this->index();
            }
        }
        return view('add');
    }
    public function update(){
        if(isset($_POST['out'])){
            $b_id=input('b_id');
            $b_state=2;
            if(Db::name('business')->where('b_id',$b_id)->update(['b_state'=>$b_state]))
                $this->success('修改成功','index');
            else
                $this->error('修改失败','index');
        }
        if(isset($_POST['sub'])){
            $b_id=input('b_id');
            $b_state=1;
            if(Db::name('business')->where('b_id',$b_id)->update(['b_state'=>$b_state]))
                $this->success('修改成功','index');
            else
                $this->error('修改失败','index');
        }
        $id=$_GET['b_id'];
        $model=Db::name('business')
            ->where('b_id',$id)
            ->find();
        return view('update',['model'=>$model]);
    }
    public function dele(){
        $id=input('id/a');
        if(Db::name('type')->where("b_id",'in',$id)->update(['b_state'=>2]))
            $this->success('操作成功','index');
        else
            $this->error('操作失败','index');
    }
    public function diesession()
    {
        $name=session::get('name');
        $pwd=session::get('pwd');
        session::clear();
        session::set('name',$name);
        session::set('pwd',$pwd);
        return $this->index();
    }
}