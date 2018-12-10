<?php
/**
 * Created by PhpStorm.
 * User: 紫殇
 * Date: 2018/11/26
 * Time: 9:10
 */

namespace app\shop\controller;

use think\Controller;
use think\Db;
business_indexLogin();
class Busibank  extends Controller
{
    /*
     * 商户银行卡
     * */
    public function index()//商户银行卡
    {
        $sheng=[];
        $rs=Db::name('busibank')->join('area','bu_sh_id=area.a_id')->where('bu_b_id',session('b_id'))->select();
        foreach ($rs as $k=>$v)
        {
            $sheng[$k]=Db::name('area')->where('a_id',$v['a_fid'])->find();
        }
        return view('index',['rs'=>$rs,'sheng'=>$sheng]);
    }
    public function bank_add()//添加银行卡
    {
        if (null!==input('sub'))
        {
            $data=[];
            $data['bu_b_id']=session('b_id');
            $data['bu_card']=input('bu_card');
            $data['bu_s_id']=intval(input('bu_sheng'));
            $data['bu_sh_id']=intval(input('bu_shi'));
            $data['bu_belong']=input('bu_belong');
            $data['bu_open']=input('bu_open');
            $data['bu_name']=input('bu_name');
            $data['bu_reserve']=input('bu_reserve');
            $data['bu_card_addtime']=time();
            if (Db::name('busibank')->insert($data))
            {
                $this->success('银行卡添加成功',url('business/bank'));
            }
            else
                $this->error('添加失败');
        }
        else
        {
            $rs=Db::name('area')->where('a_fid',0)->select();
            return view('bank_add',['rs'=>$rs]);
        }

    }
    public function bld()//省市联动
    {
        $xid=input('xid');
        $data=Db::name("area")->where('a_fid',$xid)->select();
        $str="<option value=''>请选择 &nbsp;&nbsp;</option>";
        foreach($data as $cla)
        {
            $str.="<option value=".$cla['a_id'].">".$cla['a_name']."</option>";
        }
        echo $str;
    }
    public function bank_update()
    {
        if (null!==input('sub'))
        {
            $bu_id=intval(input('bu_id'));
            $data=[];
            $data['bu_b_id']=session('b_id');
            $data['bu_card']=input('bu_card');
            $data['bu_s_id']=intval(input('bu_sheng'));
            $data['bu_sh_id']=intval(input('bu_shi'));
            $data['bu_belong']=input('bu_belong');
            $data['bu_open']=input('bu_open');
            $data['bu_name']=input('bu_name');
            $data['bu_reserve']=input('bu_reserve');
            $rs=Db::name('busibank')->where('bu_id',$bu_id)->update($data);
            if ($rs==0)
            {
                $this->success('数据未修改',url('busibank/index'));
            }
            elseif($rs>0)
            {
                $this->success('数据修改成功',url('busibank/index'));
            }
            else
            {
                $this->error('修改失败');
            }
        }
        else
        {
            $bu_id=input('bu_id');
            $arr=Db::name('busibank')->where('bu_id',$bu_id)->find();
            $rs2=Db::name('area')->where('a_fid',$arr['bu_s_id'])->select();
            $rs=Db::name('area')->where('a_fid',0)->select();
            return view('bank_update',['arr'=>$arr,'rs'=>$rs,'rs2'=>$rs2]);
        }
    }

}