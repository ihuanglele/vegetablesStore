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
                record("更新了".$_POST['oid'].'号订单信息');
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

    /**
     * 导出订单
     */
    public function export(){
        $starTime = strtotime(date('Y-m-d'))-7*24*3600;
        $endTime = time();
        $this->assign('starTime',$starTime);
        $this->assign('endTime',$endTime);
        $this->display('export');
    }

    public function exportOrder(){
        C('SHOW_PAGE_TRACE',false);
        $t = I('get.t');
        if($t){
            //直接查询某个特定时间段
            $endTime = strtotime(date('Y-m-d 23:59:59'))+1;
            switch($t){
                case 'month':$starTime = strtotime(date("Y-m-1 00:00:00"));break;//本月
                case '15':$starTime = $endTime-15*24*3600;break;   //15天
                case '30':$starTime = $endTime-30*24*3600;break;   //30天
                default:$starTime = strtotime(date('Y-m-1 00:00:00'));break;//本月
            }
        }else{
            $t1 = I('get.t1',0);
            $starTime = $t1?strtotime($t1):0;
            if($starTime==0){
                $starTime = strtotime(date('Y-m-d'))-7*24*3600;
            }
            $t2 = I('get.t2',0);
            $endTime = $t1?strtotime($t2):0;
            if($endTime==0){
                $endTime = time();
            }
        }
        $map['time'] = array(array('egt',$starTime),array('elt',$endTime));
        $list = $this->getData(M('orders'),$map,'trade desc');
        $data = array();
        $Status = C('OrdersStatus');
        foreach($list as $v){
            $addr = json_decode($v['address_info'],true);
            $t[] = $v['trade'];
            $t[] = Mydate($v['create_time']);
            $t[] = Mydate($v['pay_time']);
            $t[] = $Status[$v['status']];
            $t[] = $addr['name'];
            $t[] = $addr['tel'];
            $t[] = $addr['address'];
            $goods = json_decode($v['goods_info']);
            foreach($goods as $g){
                $t[] = $g['gid'];
                $t[] = $g['pay_each_price'];
                $t[] = $g['buy_num'];
                $data[] = $t;
            }

        }
        $title = array('订单号','创建时间','支付时间','状态','收件人姓名','收件人电话','收件地址','商品ID','购买单价','购买数量');

        exportexcel($data,$title,date('m-d H:i').'导出订单');
    }

}