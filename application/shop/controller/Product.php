<?php
/**
 * Created by PhpStorm.
 * User: 紫殇
 * Date: 2018/11/19
 * Time: 17:29
 */

namespace app\shop\controller;

use think\Controller;
use think\Db;
business_indexLogin();
class Product extends Controller
{
    /*
     * 商品控制器
     * */
    public function index()//商品的主页
    {
        $data=[];//$start=0;$stop=9999999999;
        if (null!==input('ss'))
        {
            if (null!==input('ss_p_first'))
                session('p_first',input('ss_p_first'));
            if (null!==input('ss_p_second'))
                session('p_second',input('ss_p_second'));
            if (null!==input('ss_p_title'))
                session('p_title',input('ss_p_title'));
//            if (null!==input('ss_start'))
//                session('ss_start',input('ssstart'));
//            if (null!==input('ss_stop'))
//                session('ss_stop',input('ss_stop'));
            if (null!==input('ss_p_date'))
                session('p_date',input('ss_p_date'));
            if (null!==input('ss_p_state'))
                session('p_state',input('ss_p_state'));
            if (session('?p_first'))
                $data['p_first']=session('p_first');
            if (session('?p_second'))
                $data['p_second']=session('p_second');
            if (session('?p_title'))
                $data['p_title']=['like','%'.session('p_title').'%'];
//            if (session('?ss_start'))
//                $start=strtotime(session('ss_start'));
//            if (session('?ss_stop'))
//                $stop=strtotime(session('ss_stop'));
            if (session('?p_date'))
                $data['p_date']=session('p_date');
            if (session('?p_state'))
                $data['p_state']=session('p_state');
        }
        if (null!==input("qingkong"))
        {
            session('p_first','');
            session('p_second','');
//            session('ss_start','');
//            session('ss_stop','');
            session('p_date','');
            session('p_state','');
            $data=[];
        }
        $b_id=session('b_id');
        $arr=Db::name("product")->join('type','type.t_id=p_second','left')->where('p_b_id',$b_id)->where($data)->paginate(10);
        //echo Db::name('product')->getLastSql();die;
        $lm2=[];$start=[];
        foreach ($arr as $k=>$v)
        {
            $lm2[$k]=Db::name('type')->where('t_id',$v['p_first'])->find();//获取一级栏目
            $start[$k]=Db::name('hair')->field('ha_time')->where('ha_p_id',$v['p_id'])->select();

        }
//        echo '<pre>';
//        print_r($start);die;
        $rs_first=Db::name('type')->where('t_fid',0)->select();
        return view("index",['arr'=>$arr,'lm2'=>$lm2,'rs_first'=>$rs_first,'start'=>$start]);
    }
    public function sel()//商品查看
    {
        $p_id=str_split(input('p_id'))[2];
        $arr=Db::name("product")->join('type','type.t_id=p_second','left')->join('ys_hair','ha_p_id=p_id','left')->where('p_b_id',session('b_id'))->where('p_id',$p_id)->find();
        //print_r($arr);die;
        $lm2=Db::name('type')->where('t_id',$arr['p_first'])->find();
        $start=Db::name('hair')->field('ha_time')->where('ha_p_id',$arr['p_id'])->select();
        return view('sel',['arr'=>$arr,'lm2'=>$lm2,'start'=>$start]);
    }
    public function add()//商品添加
    {
        if(null!==input('sub'))
        {
            $data=[];
            $ha_time=input('ha_time/a');
            $data['p_b_id']=session('b_id');
            $data['p_first']=intval(input("p_first"));
            $data['p_second']=intval(input("p_second"));
            $data['p_title']=input('p_title');
            $data['p_rement']=input('p_rement');
            $data['p_chstic']=input('p_chstic');
            $data['p_date']=intval(input('p_date'));
            $data['p_cost']=round(input('p_cost'),2);
            $data['p_cost_child']=round(input('p_cost_child'),2);
            $data['p_commission']=round(input('p_commission'),2);
            $data['p_state']=1;
            $data['p_addtime']=time();
            $validate =validate('Product');
            if(!$validate->check($data)){
                $this->error(''.print_r($validate->getError()));
            }

            $image =request()->file('image');
            if ($image)
            {
                $info = $image->move(ROOT_PATH . 'public/static' . DS . 'upload');
                if ($info)
                {
                    $image = \think\Image::open('./static/upload/'.$info->getSaveName());
                    if($image->thumb(300, 300)->save('./static/upload/'.$info->getSaveName()))
                    {
                        $data['p_url']=$info->getSaveName();
                        $arr=Db::name('product')->insertGetId($data);
                        if($arr)
                        {
                            $files=request()->file('file');
                            foreach($files as $k=>$f)
                            {
                                $info2 = $f->move(ROOT_PATH . 'public/static' . DS . 'upload');
                                if ($info2)
                                {
                                    $image = \think\Image::open('./static/upload/'.$info2->getSaveName());
                                    $image->thumb(300, 300)->save('./static/upload/'.$info2->getSaveName());
                                    Db::name('proinfo')->insert([
                                        'pr_p_id'=>$arr,
                                        'pr_url'=>$info2->getSaveName(),
                                        'pr_addtime'=>time(),
                                        'pr_sort'=>$k+1
                                    ]);
                                }
                            }
                            foreach ($ha_time as $v)
                            {
                                Db::name('hair')->insert([
                                    'ha_p_id'=>$arr,
                                    'ha_time'=>$v,
                                ]);
                            }
                            $this->success('商品添加成功',url('Product/index'));
                        }
                        $this->error('商品添加失败');
                    }

                }
            }
        }
        else
        {

            $rs=Db::name('type')->where('t_fid',0)->select();
            return view("add",['arr'=>$rs]);
        }

    }
    public function proinfo()
    {
        if (null!==input('pr_id')&&null!==input('pr_sort'))
        {
            $pr_id=input('pr_id');
            $pr_sort=input('pr_sort');
            if(Db::name('proinfo')->where('pr_id',$pr_id)->update(['pr_sort'=>$pr_sort]))
                echo  111;
            else
                echo  222;
        }
        $p_id=str_split(input('p_id'))[2];
        $rs=Db::name('proinfo')->field('pr_id,pr_p_id,pr_url,pr_sort')->where('pr_p_id',$p_id)->order('pr_sort','asc')->select();
        return view('proinfo',['rs'=>$rs,'p_id'=>$p_id]);
    }
    public function proinfo_add()
    {
        if (null!==input('sub'))
        {
            $pr_p_id=input('pr_p_id');
            $files=request()->file('file');
            foreach($files as $k=>$f)
            {
                $info = $f->move(ROOT_PATH . 'public/static' . DS . 'upload');
                if ($info)
                {
                    $image = \think\Image::open('./static/upload/'.$info->getSaveName());
                    $image->thumb(300, 300)->save('./static/upload/'.$info->getSaveName());
                    $arr=Db::name('proinfo')->where('pr_p_id',$pr_p_id)->order('pr_sort','asc')->select();
                    //echo Db::name('proinfo')->getLastSql();die;
                    if ($arr)
                    {
                        foreach ($arr as $v)
                        {
                            $i=$v['pr_sort'];
                        }
                    }
                    else
                        $i=0;
                    Db::name('proinfo')->insert(['pr_p_id'=>$pr_p_id,'pr_url'=>$info->getSaveName(),'pr_addtime'=>time(),'pr_sort'=>$i+1]);
                    //echo Db::name('proinfo')->getLastSql();die;
                }
            }
            $this->success('',url('product/proinfo',['p_id'=>'00'.$pr_p_id.'000']));
        }
    }
    public function update()//商品修改
    {
        if (null!==input("sub"))
        {
            $p_url=input("p_url");
            $p_id=input("id");
            $data=[];
            $start=input('ha_time/a');
            $data['p_b_id']=session('b_id');
            $data['p_first']=intval(input("p_first"));
            $data['p_second']=intval(input("p_second"));
            $data['p_title']=input('p_title');
            $data['p_rement']=input('p_rement');
            $data['p_chstic']=input('p_chstic');
            $data['p_date']=intval(input('p_date'));
            $data['p_cost']=round(input('p_cost'),2);
            $data['p_cost_child']=round(input('p_cost_child'),2);
            $data['p_commission']=round(input('p_commission'),2);
            $data['p_state']=1;
            $validate =validate('Product');
            if(!$validate->check($data)){
                $this->error(''.print_r($validate->getError()));
            }
            $file=request()->file('file');
            if($file)
            {
                @unlink("./static/upload/".$p_url);
                $info = $file->move(ROOT_PATH . 'public/static' . DS . 'upload');
                if ($info)
                {
                    $image = \think\Image::open('./static/upload/' . $info->getSaveName());
                    if($image->thumb(150, 150)->save('./static/upload/'.$info->getSaveName()))
                    {
                        $data['p_url']=$info->getSaveName();
                        Db::name('hair')->where('ha_p_id',$p_id)->delete();
                        foreach ($start as $v) {
                            $arr[]=Db::name('hair')->insert([
                                'ha_p_id'=>$p_id,
                                'ha_time'=>$v,
                            ]);
                        }
                        if(Db::name('product')->where('p_id',$p_id)->update($data))
                        {
                            $this->success('商品修改成功',url('Product/index'));
                        }
                        else
                            $this->error('商品修改失败');
                    }
                }

            }
            else
            {
                Db::name('hair')->where('ha_p_id',$p_id)->delete();
                foreach ($start as $v) {
                    $arr[]=Db::name('hair')->insert([
                        'ha_p_id'=>$p_id,
                        'ha_time'=>$v,
                    ]);
                }
                if (Db::name('product')->where('p_id',$p_id)->update($data))
                {
                    $this->success('修改成功',url("product/index"));
                }
                else
                    $this->error("修改失败");
            }

        }
        else
        {
            $p_id=input('p_id');
            $rs=Db::name("product")
                ->join('type','type.t_id=p_first')
                ->where('p_b_id',session('b_id'))
                ->where('p_id',$p_id)
                ->find();
            $lm2=Db::name('type')->where('t_id',$rs['p_second'])->find();//获取二级栏目
            $start=Db::name('hair')->field('ha_time')->where('ha_p_id',$p_id)->select();
            $arr=Db::name('type')->where('t_fid',0)->select();
            return  view('update',['arr'=>$arr,'rs'=>$rs,'lm2'=>$lm2,'start'=>$start]);
        }

    }
    public function ld()//一级栏目和二级栏目的联动
    {
        $xid=input('xid');
        $data=Db::name("type")->where('t_fid',$xid)->select();
        $str="<option value=''>请选择 &nbsp;&nbsp;</option>";
        foreach($data as $cla)
        {
            $str.="<option value=".$cla['t_id'].">".$cla['t_name']."</option>";
        }
        echo $str;
    }
}