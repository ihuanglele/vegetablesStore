<?php
/**
 * Author: huanglele
 * Date: 2016/7/5
 * Time: 下午 10:54
 * Description:
 */

namespace Home\Controller;


class UserController extends CommonController
{

    private $uid = null;

    /**
     * 初始化，判断是否登录了
     */
    public function _initialize(){
        parent::_initialize();
        $this->uid = session('uid');
        if(!$this->uid){
            if(strtolower(ACTION_NAME)!='login'){
                session('jump',$_SERVER['REQUEST_URI']);
            }
            $this->redict('common/login');die;
        }
    }

    //个人中心
    public function index(){
        $info = M('user')->find($this->uid);
        $this->assign('info',$info);
        //获取推荐商品
        $Tool = A('Tool');
        $goods = $Tool->getGoods(array('status'=>2),4,'sold_num desc');
        $this->assign('goods',$goods);

        //获取没有付款的订单数量
        $map['stauts'] = 1;$map['uid'] = session('uid');
        $count = M('orders')->where($map)->count('trade');
        $this->assign('noPayNum',$count);

        $this->display('index');
    }

    //确认订单
    public function cart(){
        if(IS_POST){
            layout(false);
            $gidArr = I('post.gid');
            $numArr = I('post.num');
            $gInfo = M('goods')->where(array('gid'=>array('in',$gidArr)))->getField('gid,name,buy_price as price,status,left_num,img',true);
            $data = array();
            $goodAmount = 0;
            $cart = array();
            foreach($gidArr as $k=>$v){
                if(array_key_exists($v,$gInfo)){
                    $cart[$v] = $numArr[$k];
                    $i['gid'] = $v;
                    $i['num'] = $numArr[$k];
                    $i['name'] = $gInfo[$v]['name'];
                    $i['price'] = $gInfo[$v]['price'];
                    $i['status'] = $gInfo[$v]['status'];
                    $i['left_num'] = $gInfo[$v]['left_num'];
                    $i['img'] = $gInfo[$v]['img'];
                    $data[] = $i;
                    $goodAmount += $i['num']*$i['price'];
                }
            }
            session('cart',$cart);  //更新购物车
            $this->assign('amount',$goodAmount);//商品价格
            $this->assign('yunfei',readConf('yunfei'));//商品价格
            $this->assign('data',$data);

            //查询收货地址
            $address = M('address')->where(array('uid'=>$this->uid))->field('id,address,phone,name')->order('`default` desc')->select();
            $this->assign('address',$address);

            $this->display('cart');
        }else{
            $this->redirect('index/cart');
        }

    }

    //管理收货地址
    public function address(){
        $Tool = A('Tool');
        $map['uid'] = $this->uid;
        $Tool->getList('address',$map,'id desc');
        $this->display();
    }

    //购买
    public function buy(){
        $Tool = A('Order');
        $order = $Tool->addOrder();
        if($order['status']){
            $data['uid'] = session('uid');
            $data['oid'] = $order['trade'];
            $data['amount'] = $order['money'];
            $data['body'] = '消费';
            $data['attach'] = '消费';
            $this->sendPayData($data);
        }else{
            $this->error($order['msg']);
        }
    }

    /**
     * 发起微信支付
     * @param $da
     * 包含oid的为订单付款，对应的orders表里面的trade字段。
     */
    private function sendPayData($da){
        layout(false);
        $body = $da['body'];
        $attach = $da['attach'];
        $tag = $da['uid'];
        $trade_no = createWxPayTrade();
        $Pay = A('Wechat');
        $order = $Pay->scanPay($body,$attach,$trade_no,intval($da['amount']*100),$tag);
        if($order['result_code']=='SUCCESS'){//生成订单信息成功
            $data['mytrade'] = $trade_no;
            $data['uid'] = $da['uid'];
            $data['oid'] = $da['oid'];
            $data['create_time'] = date('Y-m-d H:i:s');
            $data['amount'] = $da['amount'];
            $data['status'] = 1;
            $data['pay_time'] = 0;
            if(M('wxpay')->add($data)){
                $this->assign('order',$order);
                $this->assign('id',$da['oid']);
                $this->assign('money',$da['amount']);
                $this->display('User/paySub');die;
            }else{
                $this->error('操作失败请重试');die;
            }
        }else{
            $this->error('操作失败请重试');die;
        }
    }

    //我的订单
    public function myOrder(){
        $status = I('get.status');
        if($status){
            $map['status'] = $status;
        }
        $this->assign('status',$status);
        $trade = I('get.trade','','number_int');
        if($trade){
            $map['trade'] = $trade;
        }
        $this->assign('trade',$trade);

        $map['uid'] = $this->uid;
        $Tool = A('Tool');
        $list = $Tool->getList('orders',$map,'trade desc','trade,goods_info,status,goods_amount,create_time,address_info');

        if($list){
            $gidArr = array();
            foreach($list as $v){
                $goods = json_decode($v['goods_info'],true);
                foreach($goods as $vo){
                    if(!in_array($vo['gid'],$gidArr)){
                        $gidArr[] = $vo['gid'];
                    }
                }
            }
            $goodInfo = M('goods')->where(array('gid'=>array('in',$gidArr)))->getField('gid,name,img',true);
            $OrderStatus = C('OrderStatus');
            //合成数据
            foreach($list as $o){
                $i['trade'] = $o['trade'];
                $i['status'] = $OrderStatus[$o['status']];
                $i['time'] = Mydate($o['create_time']);
                $i['goods_amount'] = $o['goods_amount'];
                $goods = json_decode($v['goods_info'],true);
                $i['address'] = json_decode($o['address_info'],true);
                $goodArr = array();
                foreach($goods as $vo){
                    $t['name'] = $goodInfo[$vo['gid']]['name'];
                    $t['img'] = $goodInfo[$vo['gid']]['img'];
                    $goodArr[] = $t;
                }
                $i['goods'] = $goodArr;
                $data[] = $i;
            }
        }else{
            $data = array();
        }
        $this->assign('data',$data);
        $this->display('myOrder');
    }

    /**
     * 我的订单详情
     */
    public function order(){
        $id = I('get.id');
        $info = M('orders')->find($id);
        if($info && $info['uid']==session('uid')){
            $goods = json_decode($info['goods_info'],true);
            foreach($goods as $vo){
                $gidArr[] = $vo['gid'];
            }
            $goodInfo = M('goods')->where(array('gid'=>array('in',$gidArr)))->getField('gid,name,img',true);
            $goodArr = array();
            foreach($goods as $vo){
                $t['gid'] = $vo['gid'];
                $t['name'] = $goodInfo[$vo['gid']]['name'];
                $t['img'] = $goodInfo[$vo['gid']]['img'];
                $t['buy_price'] = $vo['pay_each_price'];
                $t['buy_num'] = $vo['buy_num'];
                $goodArr[] = $t;
            }
            $info['goods'] = $goodArr;
            $info['address'] = json_decode($info['address_info'],true);
            $info['express'] = json_decode($info['express_info'],true);
            $this->assign('info',$info);
            $this->assign('OrderStatus',C('OrderStatus'));
            $this->display('order');
        }else{
            $this->error('订单不存在',U('myOrder'));
        }
    }

    public function payOrder(){
        $order = I('get.order');
        $info = M('orders')->field('trade,goods_amount,yunfei,uid,status,uid')->find($order);
        if($info && $info['status']==1 && $info['uid']==session('uid')){
            $data['uid'] = session('uid');
            $data['oid'] = $order;
            $data['amount'] = $info['goods_amount']+$info['yunfei'];
            $data['body'] = '消费';
            $data['attach'] = '消费';
            $this->sendPayData($data);
        }else{
            $this->error('订单不存在');
        }
    }

}