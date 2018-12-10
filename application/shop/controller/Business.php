<?php
/**
 * Created by PhpStorm.
 * User: 紫殇
 * Date: 2018/11/22
 * Time: 14:48
 */

namespace app\shop\controller;

use think\Controller;
use think\Db;
business_indexLogin();
class Business  extends Controller
{
    /*
     * 商户控制器
     * */
    public function index()//商户信息主页
    {
        $rs=Db::name('business')->where('b_id',session('b_id'))->find();
        return  view('index',['arr'=>$rs]);
    }
    public function update()//修改商家密码
    {
        $rs=Db::name('business')->where('b_id',\session('b_id'))->find();
        if (null!==input('sub'))
        {
            $b_user=input('b_user');
            if ($b_user!=session('b_user')&&Db::name('business')->where('b_user',$b_user)->select())
            {
                $this->error('用户名重复，请另起用户名');
            }
            $b_old_pwd=hash('sha256',input('b_old_pwd'));
            $b_new_pwd=hash('sha256',input('b_new_pwd'));
            $b_re_pwd=hash('sha256',input('b_re_pwd'));
            if ($rs['b_pwd']==$b_old_pwd)
            {
                if ($b_new_pwd==$b_re_pwd)
                {
                    if (Db::name('business')->where('b_id',\session('b_id'))->update(['b_pwd'=>$b_new_pwd,'b_user'=>$b_user]))
                    {
                        $this->success('密码修改成功,下次登陆时请使用新密码登陆',url("Product/index"));
                    }
                    else
                        $this->error('密码修改失败');
                }
                else
                {
                    $this->error('两次密码不一致');
                }
            }
        }
        return  view("update",['arr'=>$rs]);
    }
    public function wallet()//商户钱包
    {
        $rs=Db::name("business")->where('b_id',session('b_id'))->field('b_wallet')->find();
        $arr=Db::name("busicash")
            ->field('bus_id,bus_money,bus_num,bus_state,bus_bu_id,bus_addtime,bu_card,bu_belong')
            ->join('busibank','bus_bu_id=busibank.bu_id')
            ->select();
        return  view('wallet',['rs'=>$rs,'arr'=>$arr]);
    }
    public function busicash()//商家提现
    {
        $rs_business=Db::name('business')->where('b_id',session('b_id'))->find();
        $rs_bank=Db::name('busibank')->where('bu_b_id',session('b_id'))->select();
        if  (null!==input('sub'))
        {
            $yue=$rs_business['b_wallet']-input('bus_money');
            $data['bus_b_id']=session('b_id');
            $data['bus_bu_id']=input('bu_card');
            $data['bus_money']=input('bus_money');
            list($msec, $sec) = explode(' ', microtime());
            $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
            $data['bus_num']=date('YmdHis').rand(0,9).rand(0,9).rand(0,9).$msectime;
            if (Db::name('busicash')->field('bus_num')->where('bus_num',$data['bus_num'])->find())
                $data['bus_num']=date('YmdHis').rand(0,9).rand(0,9).rand(0,9).$msectime;
            $data['bus_state']=1;
            $data['bus_turntime']=0;
            $data['bus_addtime']=time();
            if (Db::name('busicash')->insert($data))
            {
                if (Db::name('business')->where('b_id',session('b_id'))->update(['b_wallet'=>$yue]))
                {
                    $this->success('提现申请提交成功',url('business/wallet'));
                }
                else
                    $this->error('提现申请提交失败');
            }
            else
                $this->error('提现申请提交失败');
        }
        else
        {
            return view('busicash',['rs_business'=>$rs_business,'rs_bank'=>$rs_bank]);
        }
    }
}