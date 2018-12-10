<?php
namespace app\admin\controller;
use think\paginator\driver\Bootstrap;
use think\Db;
use think\Session;

class User	extends Comm
{
    /*
     *超级管理员的控制器
     *index方法中可以有查询、分页($page)
    */
    public function index(){
        /*
         * 我所用用的每个if判断里面都是每个检索的条件，用session进行包装，不然下一页时，条件丢失
         */
        if(input('new_time')){
            session::set('new_time',(" ad_addtime >= ".strtotime(input('new_time'))));
            session::set('new',input('new_time'));
        }
        if(input('new_time')< input('die_time')){
            session::set('die_time',("ad_addtime <= ".input('new_time')));
            session::set('die',input('die_time'));
        }
        if(input('sname')){
            session::set('sname',input('sname'));
        }
        $new=session::get('new_time');
        $die=session::get('die_time');
        $name=session::get('sname');
        //admin这是超级管理员表
        $question=Db::name('admin')
            ->field('ad_id,ad_user,ad_addtime')
            ->where($new)
            ->where($die)
            ->where(" ad_user like '%$name%'")
            ->paginate('2');//分页显示两条数据
        $page=$question->render();
        return view('lest',['model'=>$question,'page'=>$page]);
    }
    public function add(){
        if(isset($_POST['sub'])){
            $name=input('ad_user');
            //hash加密方法两个参数
            $pwd=hash('sha256',input('newpass'));
            $date=[
                'ad_user'=>$name,
                'ad_pwd'=>$pwd,
                'ad_addtime'=>time(),
            ];
            //验证，作用不是很大，但也不小
            if(validate('User')->check($date)){
                if(Db::name('admin')->insert($date))
                    $this->success('添加成功','user/index');
                else
                    $this->error('添加失败','user/index');
            }else{
                $this->error('验证失败','index');
            }
        }
        return view('add');
    }
    public function xiu($ad_id){
        $model=Db::name('admin')->where(['ad_id'=>$ad_id,"ad_user"=>session::get('name')])->find();
        if($model){
            return view('xiu',['model'=>$model]);
        }else{
            $this->error('只能修改自己的','user/index');
        }
    }
    public function xiugai(){
        $ad_id=input('ad_id');
        $name=input('ad_user');
        $pwd=hash('sha256',input('ad_pwd'));
        $newpwd=hash('sha256',input('newpass'));
        $date=[
            'ad_pwd'=>$newpwd,
        ];
        if($name!=session('name')){
            $date['ad_user']=$name;
        }
        if(validate('User')->check($date)){
            if(Db::name('admin')->where(['ad_id'=>$ad_id,'ad_pwd'=>$pwd])->find()){
                if(Db::name('admin')->where('ad_id',$ad_id)->update($date)){
                    $this->success('修改成功，下次登录生效','index');
                }else
                    $this->error('修改失败','index');
            }else{
                $this->error('老密码错误',"index");
            }
        }else{
            $this->error('验证错误',"index");
        }
    }
    public function xiupass(){
        if(isset($_POST['ad_pwd'])){
            $name=session::get('name');
            $oldpwd=hash('sha256',input('ad_pwd'));
            if($oldpwd!=session::get('pwd')){
                $this->error('老密码错误，请重新输入','xiupass');
            }
            $newpwd=input('newpass');
            $date=[
                'ad_user'=>$name,
                'ad_pwd'=>$oldpwd
            ];
            if($oldpwd==session::get('pwd')){
                if(Db::name('admin')->where(['ad_user'=>$name,'ad_pwd'=>$oldpwd])->update(['ad_pwd'=>$newpwd]))

                    $this->success('修改成功,下次登录生效',url('index'));
                else
                    $this->error('修改失败','index');
            }else{
                $this->error('老密码不正确','xiupass');
            }
        }else{
            return view('xiupass');
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