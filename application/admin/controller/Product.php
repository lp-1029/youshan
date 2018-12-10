<?php
namespace app\admin\controller;
use think\paginator\driver\Bootstrap;
use think\Db;
use think\Session;

class Product extends Comm
{
    public function index()//商品的主页
    {
        if(input('new_time')){
            session::set('p_new_time',(" p.p_addtime >= ".strtotime(input('new_time'))));
            session::set('p_new',input('new_time'));
        }
        if(input('new_time') < input('die_time')){
            session::set('p_die_time',("p.p_addtime <= ".strtotime(input('die_time'))));
            session::set('p_die',input('die_time'));
        }
        if(input('sname')){
            session::set('p_sname',input('sname'));
        }
        if(input('state')){
            session::set('p_state',input('state'));
        }
        $new=session::get('p_new_time');
        $die=session::get('p_die_time');
        $name=session::get('p_sname');
        if(session::get('p_state'))
            $state=session::get('p_state');
        else
            $state="p_state";
        if(isset($_GET['p_id']))
            $p_id=$_GET['p_id'];
        else
            $p_id='p.p_id';
        $rs=Db::name("product")
            ->alias('p')
            ->where($new)
            ->where($die)
            ->where("p.p_state <> 4")
            ->where("p.p_state=".$state)
            ->where(" b.b_company like '%$name%'")
            ->field('b.b_company,t.*,p.*')
            ->where("p.p_id=".$p_id)
            ->join('type t','t.t_id=p.p_first')
            ->join('business b','b.b_id=p.p_b_id')
            ->paginate(10);
        $lm2=[];$time=[];$i=0;
        foreach ($rs as $k=>$v)
        {
            $lm2[$k]=Db::name('type')->where('t_id',$v['p_second'])->find();//获取二级栏目
            $time[$k]=Db::name("hair")->where("ha_p_id",$v['p_id'])->select();
            $i++;
        }
        if(isset($_GET['p_id'])){
            return view('update',['rs'=>$rs['0'],'lm2'=>$lm2['0'],"time"=>$time,"i"=>$i]);
        }else{
            return view('index',['arr'=>$rs,'lm2'=>$lm2,"time"=>$time]);
        }
    }
    public function update(){
        $p_id=input('p_id');
        if(isset($_POST['pass'])){
            $p_cost=(float)input('p_cost');
            $p_market=(float)input('p_market');
            $p_cost_child=(float)input('p_cost_child');
            $p_market_child=(float)input('p_market_child');
            if($p_cost>=$p_market||$p_cost_child>=$p_market_child){
                $this->error('价格添加不合理','index');
            }
            $date=[
                'p_market'=>$p_market,
                'p_market_child'=>$p_cost_child,
                'p_state'=>2
            ];
            $validate=validate('Product');
            if($validate->check($date)){
                if(db('product')->where('p_id',$p_id)->update($date))
                    return $this->index();
                else
                    return $this->index();
            }else{
                $this->error('验证失败','index');
            }
        }else{
            if(db('product')->where('p_id',$p_id)->update(['p_state'=>3]))
                return $this->index();
            else
                return $this->index();
        }
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