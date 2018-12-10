<?php
/**
 * Created by PhpStorm.
 * User: 紫殇
 * Date: 2018/11/26
 * Time: 9:10
 */

namespace app\partner\controller;

use think\Controller;
use think\Db;
partner_indexLogin();
class Pbank  extends Controller
{
    /*
     * 合伙人银行卡
     * */
    public function index()//合伙人银行卡
    {
        $sheng=[];
        $rs=Db::name('pbank')->join('area','pb_sh_id=area.a_id')->where('pb_pa_id',session('pa_id'))->select();
        foreach ($rs as $k=>$v)
        {
            $sheng[$k]=Db::name('area')->where('a_id',$v['a_fid'])->find();
        }
        return view('pbank',['rs'=>$rs,'sheng'=>$sheng]);
    }
    public function bank_add()//添加银行卡
    {
        if (null!==input('sub'))
        {
            $data=[];
            $data['pb_pa_id']=session('pa_id');
            $data['pb_card']=input('pb_card');
            $data['pb_s_id']=intval(input('pb_sheng'));
            $data['pb_sh_id']=intval(input('pb_shi'));
            $data['pb_belong']=input('pb_belong');
            $data['pb_open']=input('pb_open');
            $data['pb_name']=input('pb_name');
            $data['pb_reserve']=input('pb_reserve');
            $data['pb_card_addtime']=time();
            if (Db::name('pbank')->insert($data))
            {
                $this->success('银行卡添加成功',url('pbank/index'));
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
            $pb_id=intval(input('pb_id'));
            $data=[];
            //$data['pb_pa_id']=session('pa_id');
            $data['pb_card']=input('pb_card');
            $data['pb_s_id']=intval(input('pb_sheng'));
            $data['pb_sh_id']=intval(input('pb_shi'));
            $data['pb_belong']=input('pb_belong');
            $data['pb_open']=input('pb_open');
            $data['pb_name']=input('pb_name');
            $data['pb_reserve']=input('pb_reserve');
            $rs=Db::name('pbank')->where('pb_id',$pb_id)->update($data);
            if ($rs==0)
            {
                $this->success('数据未修改',url('pbank/index'));
            }
            elseif($rs>0)
            {
                $this->success('数据修改成功',url('pbank/index'));
            }
            else
            {
                $this->error('修改失败');
            }
        }
        else
        {
            $pb_id=input('pb_id');
            $arr=Db::name('pbank')->where('pb_id',$pb_id)->find();
            $rs2=Db::name('area')->where('a_fid',$arr['pb_s_id'])->select();
            $rs=Db::name('area')->where('a_fid',0)->select();
            return view('bank_update',['arr'=>$arr,'rs'=>$rs,'rs2'=>$rs2]);
        }
    }

}