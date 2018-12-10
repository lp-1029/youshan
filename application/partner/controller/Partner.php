<?php
/**
 * Created by PhpStorm.
 * User: 紫殇
 * Date: 2018/11/29
 * Time: 15:22
 */

namespace app\partner\controller;

use think\Controller;
use think\Db;
class Partner   extends Controller
{
    /*
     * 合伙人控制器
     * */
    public function index()
    {
        $arr=Db::name('partner')
            ->field('pa_id,pa_user,pa_name,pa_phone,pa_state,pa_addtime')
            ->where('pa_id',session('pa_id'))
            ->find();
        return  view('index',['arr'=>$arr]);
    }
    public function daili()//代理
    {
        $rs=Db::name('daili')
            ->field('d_id,d_user,d_name,d_card,d_phone,d_addtime')
            ->where('d_pa_id',session('pa_id'))
            ->paginate(10);
        return view('daili',['arr'=>$rs]);
    }
    public function update()//修改合伙人密码
    {
        $rs=Db::name('partner')->where('pa_id',\session('pa_id'))->find();
        if (null!==input('sub'))
        {
            $pa_user=input('pa_user');
            if ($pa_user!=session('pa_user')&&Db::name('partner')->where('pa_user',$pa_user)->select())
            {
                $this->error('用户名重复，请另起用户名');
            }
            $pa_old_pwd=hash('sha256',input('pa_old_pwd'));
            $pa_new_pwd=hash('sha256',input('pa_new_pwd'));
            $pa_re_pwd=hash('sha256',input('pa_re_pwd'));
            if ($rs['pa_pwd']==$pa_old_pwd)
            {
                if ($pa_new_pwd==$pa_re_pwd)
                {
                    if (Db::name('partner')->where('pa_id',\session('pa_id'))->update(['pa_pwd'=>$pa_new_pwd,'pa_user'=>$pa_user]))
                    {
                        $this->success('密码修改成功,下次登陆时请使用新密码登陆',url("partner/index"));
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
    public function dlmoneyyi()//设置代理佣金
    {
        if (null!==input('p_id'))
        {
            $data=[];
            $p_id=input('p_id');
            $data['dm_money']=input('dm_money');
            $data['dm_addtime']=time();
            if (Db::name('dlmoney')->where('dm_p_id',$p_id)->update($data))
            {
                echo 111;
            }
            else
                echo 222;
        }
        else
        {
            $rs=Db::name('product')
                ->join('dlmoney','dm_p_id=p_id')
                ->field('p_id,p_title,p_commission,dm_money')
                ->field('p_id,p_title,p_commission')
                ->where('p_state',2)
                ->select();
            return  view('dlmoneyyi',['rs'=>$rs]);
        }

    }
    public function dlmoneywei()//设置代理佣金
    {
        if (null!==input('p_id'))
        {
            $data=[];
            $data['dm_p_id']=input('p_id');
            $data['dm_pa_id']=session('pa_id');
            $data['dm_money']=input('dm_money');
            $data['dm_addtime']=time();
            if (Db::name('dlmoney')->insert($data))
            {
                echo 111;
            }
            else
                echo 222;
        }
        else
        {
            $rs=Db::name('product')
                ->join('dlmoney','dm_p_id=p_id','left')
                ->field('p_id,p_title,p_commission,dm_money')
                ->where('dm_money',null)
                ->where('p_state',2)
                ->select();
            return  view('dlmoneywei',['rs'=>$rs]);
        }

    }
    public function wallet()//钱包
    {
        $rs=Db::name('partner')
            ->where('pa_id',session('pa_id'))
            ->field('pa_wallet')
            ->find();
        $arr=Db::name("partnercash")
            ->field('par_id,par_money,par_num,par_state,par_pb_id,par_addtime,pb_card,pb_belong')
            ->join('pbank','par_pb_id=pb_id')
            ->select();
        return view('wallet',['rs'=>$rs,'arr'=>$arr]);
    }
    public function partnercash()//合伙人提现
    {
        $rs_partner=Db::name('partner')->where('pa_id',session('pa_id'))->find();
        $rs_bank=Db::name('pbank')->where('pb_pa_id',session('pa_id'))->select();
        if  (null!==input('sub'))
        {
            $yue=$rs_partner['pa_wallet']-input('par_money');
            $data['par_pa_id']=session('pa_id');
            $data['par_pb_id']=input('pb_card');
            $data['par_money']=input('par_money');
            list($msec, $sec) = explode(' ', microtime());
            $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
            $data['par_num']=date('YmdHis').rand(0,9).rand(0,9).rand(0,9).$msectime;
            if (Db::name('partnercash')->field('par_num')->where('par_num',$data['par_num'])->find())
                $data['par_num']=date('YmdHis').rand(0,9).rand(0,9).rand(0,9).$msectime;
            $data['par_state']=1;
            $data['par_turntime']=0;
            $data['par_addtime']=time();
            if (Db::name('partnercash')->insert($data))
            {
                if (Db::name('partner')->where('pa_id',session('pa_id'))->update(['pa_wallet'=>$yue]))
                {
                    $this->success('提现申请提交成功',url('partner/wallet'));
                }
                else
                    $this->error('提现申请提交失败');
            }
            else
                $this->error('提现申请提交失败');
        }
        else
        {
            return view('partnercash',['rs_partner'=>$rs_partner,'rs_bank'=>$rs_bank]);
        }
    }
}