<?php
/**
 * Created by PhpStorm.
 * User: 紫殇
 * Date: 2018/11/29
 * Time: 11:14
 */

namespace app\partner\controller;

use think\Controller;
use think\Db;
partner_indexLogin();
class Order extends Controller
{
    /*
     * 订单控制器
     * */
    public function index()//首页
    {
        $data=[];$start=0;$stop=9999999999;
        if (null!==input('ss'))
        {
            if (null!==input('ss_p_title'))
                session('p_title',input('ss_p_title'));
            if (null!==input('ss_d_id'))
                session('d_id',input('ss_d_id'));
            if (null!==input('ss_o_num'))
                session('o_num',input('ss_o_num'));
            if (null!==input('ss_start'))
                session('ss_start',input('ss_start'));
            if (null!==input('ss_stop'))
                session('ss_stop',input('ss_stop'));
            if (null!==input('ss_o_state'))
                session('o_state',input('ss_o_state'));
            if (session('?p_title'))
                $data['p_title']=['like','%'.session('p_title').'%'];
            if (session('?ss_start'))
                $start=strtotime(session('ss_start'));
            if (session('?ss_stop'))
                $stop=strtotime(session('ss_stop'));
            if (session('?d_id'))
                $data['d_id']=session('d_id');
            if (session('?o_num'))
                $data['o_num']=session('o_num');
            if (session('?o_state'))
                $data['o_state']=session('o_state');
        }
        if (null!==input("qingkong"))
        {
            session('p_title','');
            session('d_id','');
            session('ss_start','');
            session('ss_stop','');
            session('o_num','');
            session('o_start','');
            $data=[];
        }
        $rs=Db::name('order')
            ->join('product','p_id=o_p_id','left')
            ->join('daili','d_id=o_d_id','left')
            ->join('customer','c_id=o_c_id','left')
            ->field('o_id,d_user,c_user,o_market,p_title,p_commission,o_num,o_number,o_state,o_essh,o_use')
            ->where('o_pa_id',session('pa_id'))
            ->where($data)
            ->paginate(10);
        //echo Db::name('order')->getLastSql();die;
        $rs_daili=Db::name('daili')->where('d_pa_id',session('pa_id'))->select();
        return  view('index',['arr'=>$rs,'rs_daili'=>$rs_daili]);
    }
}