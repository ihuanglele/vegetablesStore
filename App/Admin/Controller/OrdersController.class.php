<?php
/**
 * Author: huanglele
 * Date: 2016/4/1
 * Time: 下午 02:00
 * Description:
 */

namespace Admin\Controller;


class OrdersController extends CommonController
{

    public function _initialize(){
        parent::_initialize();
        $this->checkRole(array(1,4));
    }

    /**
     * 订单列表
     */
    public function index(){
        $map = array();
        if($this->role==2){
            $map['aid'] = $this->aid;
        }
        $trade = I('get.trade');
        if($trade){
            $map['trade'] = $trade;
        }
        $this->assign('trade',$trade);

        $status = I('get.status',0,'number_int');
        if($status){
            $map['status'] = $status;
        }
        $this->assign('status',$status);
        

        $M = M('orders');
        $field = 'trade,create_time,status,uid,goods_info';
        $list = $this->getData($M,$map,'trade desc',$field);
        $uidArr = array('0');
        $gidArr = array('0');
        foreach($list as $k=>$v){
            $uidArr[] = $v['uid'];
            $list[$k]['goodsInfo'] = json_decode($v['goods_info'],true);
            foreach($list[$k]['goodsInfo'] as $vo){
                $gidArr[] = $vo['gid'];
            }
        }

        $uidInfo = M('user')->where(array('uid'=>array('in',$uidArr)))->getField('uid,nickname,headimgurl');
        $goodsInfo = M('goods')->where(array('gid'=>array('in',$gidArr)))->getField('gid,name,status as gstatus');

        $data = array();
        foreach($list as $v){
            $data[] = array_merge($v,$uidInfo[$v['uid']],$goodsInfo[$v['gid']]);
        }

        $this->assign('list',$data);
        $this->assign('OrderStatus',C('OrderStatus'));
        $this->assign('GoodsStatus',C('GoodsStatus'));
        $this->display('index');
    }


    /**
     * 订单详情
     */
    public function detail(){
        $id = I('get.id');
        $M = M('orders');
        $info = $M->find($id);
        if(!$info) $this->error('页面不存在',U('index'));
        $user = M('user')->field('nickname')->find($info['uid']);
        $goods = M('goods')->field('name as tname')->find($info['gid']);

        $this->assign('info',array_merge($user,$goods,$info));
        $this->assign('OrderStatus',C('OrderStatus'));
        $this->display('detail');
    }

    /**
     * 处理兑奖
     */
    public function update(){
        if(isset($_POST['submit'])){
            $data = $_POST;
            $data['ex_time'] = time();
            $M = M('orders');
            if($M->save($data)){
                $this->success('处理成功');
            }else{
                $this->error('修改失败');
            }
        }else{
            $this->error('参数错误',U('index'));
        }
    }

    /**
     * 取消订单操作
     */
    public function undo(){
        $this->checkRole(1);
        $trade = I('get.id');
        $Orders = M('orders');
        $info = $Orders->field('trade,gid,uid,aid,status,buy_price,money')->find($trade);
        if($info['status']==1){
            //修改商品信息[库存、销量]，从商家账户扣钱，退款到买家账户

        }else{
            $this->error('订单当前状态不可取消');
        }
    }

}