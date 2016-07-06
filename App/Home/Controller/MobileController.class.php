<?php
/**
 * Author: huanglele
 * Date: 2016/7/6
 * Time: 下午 06:22
 * Description:
 */

namespace Home\Controller;
use Think\Controller;

class MobileController extends Controller{

    public function _initialize(){
        //parent::_initialize();
        C('LAYOUT_NAME','Public/mLayout');
    }

    //首页
    public function index(){
        $Tool = A('Tool');
        $map['status'] = 2;
        $order = 'gid desc';

        $goodsType = json_decode(readConf('goodsType'),true);
        foreach($goodsType as $k=>$v){
            $map['cid'] = $k;
            $data = $Tool->getGoods($map,4,$order);
            $lists[] = array('title'=>$v,'data'=>$data,'type'=>$k);
        }
        $this->assign('lists',$lists);
        $slides = readConf('carouselMJson');
        $this->assign('slides',json_decode($slides,true));

        $this->display('index');
    }

    //详情页
    public function item(){
        $gid = I('get.id');
        $info = M('goods')->find($gid);
        if($info){
            $this->assign('info',$info);
            $this->display('item');
        }else{
            $this->error('商品不存在',U('index'));
        }
    }

    //购物车页面
    public function cart(){
        $cart = session('cart');
        $gidArr = array(0);
        foreach($cart as $k=>$v){
            $gidArr[] = $k;
        }
        $gInfo = M('goods')->where(array('gid'=>array('in',$gidArr)))->getField('gid,name,buy_price as price,status,left_num,img',true);
        $data = array();
        foreach($cart as $k=>$v){
            $i['gid'] = $k;
            $i['num'] = $v;
            $i['name'] = $gInfo[$k]['name'];
            $i['price'] = $gInfo[$k]['price'];
            $i['status'] = $gInfo[$k]['status'];
            $i['left_num'] = $gInfo[$k]['left_num'];
            $i['img'] = $gInfo[$k]['img'];
            $data[] = $i;
        }
        $this->assign('data',$data);
        $this->display('cart');
    }

}