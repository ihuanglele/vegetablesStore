<?php
/**
 * Author: huanglele
 * Date: 2016/7/8
 * Time: 下午 03:06
 * Description:
 */

namespace Home\Controller;


class OrderController
{

    private $tradeId;
    private $tradeInfo;

    //下单
    public function addOrder(){
        $addressId = I('post.address');
        $address_info = M('address')->field('phone,name,address')->find($addressId);
        if($address_info){
            $addrArr = array('name'=>$address_info['name'],'phone'=>$address_info['phone'],'address'=>$address_info['address']);
            $data['address_info'] = json_encode($addrArr);

            //处理商品信息
            $gidArr = I('post.gid');
            $numArr = I('post.num');
            $gInfo = M('goods')->where(array('gid'=>array('in',$gidArr)))->getField('gid,buy_price as price,vip_price',true);
            $goods = array();
            $goodAmount = 0;
            $cart = session('cart');

            //判断是否来自推广
            $from_uid = session('from_uid');
            $rewardGidArr = array();
            if($from_uid){
                $userInfo = M('user')->field('is_store,goods')->find($from_uid);
                if($userInfo && $userInfo['is_store']){
                    $rewardGidArr = json_decode($userInfo['goods']);
                }
            }
            if(count($rewardGidArr)){
                $data['from_uid'] = $from_uid;
            }else{
                $data['from_uid'] = 0;
            }
            $reward = array();
            $rewardAmount = 0;
            foreach($gidArr as $k=>$v){
                unset($cart[$v]);
                $i['gid'] = $v;
                $i['buy_num'] = $numArr[$k];
                $i['pay_each_price'] = $gInfo[$v]['price'];
                $goods[] = $i;
                $goodAmount += $i['buy_num']*$i['pay_each_price'];
                if($data['from_uid'] && in_array($v,$rewardGidArr)){
                    $r['gid'] = $v;
                    $r['buy_num'] = $numArr[$k];
                    $r['pay_each_price'] = $gInfo[$v]['price'];
                    $r['each_earn'] = ($r['pay_each_price'] - $gInfo[$v]['vip_price']);
                    if($r['each_earn']<0){
                        $r['each_earn'] = 0;
                    }
                    $rewardAmount += $r['buy_num']*$r['each_earn'];
                    $reward[] = $r;
                }
            }

            session('cart',$cart);
            if(count($goods)<1){
                $ret['status'] = false;
                $ret['msg'] = '商品为空';
                return $ret;
            }
            $data['goods_amount'] = $goodAmount;
            $data['reward_amount'] = $rewardAmount;
            $data['goods_info'] = json_encode($goods);
            $data['reward_info'] = json_encode($reward);
            $data['create_time'] = time();
            $data['uid'] = session('uid');
            $yunfei = readConf('yunfei')?readConf('yunfei'):0;
            $data['yunfei'] = $yunfei;
            $data['pay_time'] = 0;
            $data['status'] = 1;
            $data['type'] = 1;
            $data['trade'] = createOrderTrade();
            if(M('orders')->add($data)){
                $ret['status'] = true;
                $ret['trade'] = $data['trade'];
                $ret['money'] = $yunfei + $goodAmount;
            }else{
                $ret['status'] = false;
                $ret['msg'] = '生成订单失败';
            }
            return $ret;
        }else{
            $ret['status'] = false;
            $ret['msg'] = '地址信息错误';
            return $ret;
        }
    }

    //订单付款后处理业务
    public function onOrderPay($oid){
        $this->tradeId = $oid;
        //需要处理业务
        /**
         * 1.更新订单状态
         * 2.减少商品数量、增加销售数量
         * 3.修改user表里面的信息（coin，use_money）
         * 3.发佣金（如果存在）
         */
        $res = true;
        $oInfo = M('orders')->field('trade,goods_info,uid,status,goods_amount,reward_amount,reward_info,from_uid,yunfei')->find($oid);   //订单信息
        $this->tradeInfo = $oInfo;

        if($oInfo['status']!=1) return false;   //确保订单没有处理

        //修改订单信息
        $orderData['trade'] = $oid;
        $orderData['pay_time'] = time();
        $orderData['status'] = 2;
        $res = M('orders')->save($orderData);
        if(!$res) return $res;

        //修改商品库存、销量信息
        $goodsInfo = json_decode($oInfo['goods_info'],true);
        $GoodsM = M('goods');
        foreach($goodsInfo as $v){
            $goodsData['gid'] = $v['gid'];
            $goodsData['sold_num'] = array('exp','`sold_num`+'.intval($v['buy_num']));
            $goodsData['left_num'] = array('exp','`left_num`+'.intval($v['buy_num']));
            $res = $GoodsM->save($goodsData);
            if(!$res) return $res;
        }

        //修改用户积分信息
        $userData['uid'] = $oInfo['uid'];
        $userData['coin'] = array('exp','`coin`+'.$oInfo['goods_amount']);
        $userData['use_money'] = array('exp','`use_money`+'.$oInfo['goods_amount']);
        $res = M('user')->save($userData);
        if(!$res) return $res;

        //添加用户财务记录
        $moneyData['uid'] = $oInfo['uid'];
        $moneyData['amount'] = $oInfo['goods_amount']+$oInfo['yunfei'];
        $moneyData['time'] = time();
        $moneyData['note'] = '订单号'.$oid;
        $moneyData['type'] = 1;
        $res = M('money')->add($moneyData);
        if(!$res) return $res;

        $uInfo = M('user')->field('nickname,invite_uid,leader')->find($oInfo['uid']);
        //判断佣金信息
        /**
         * 1.商城自己买出去的会返5%给上级
         * 2.来自推广的直接返佣金给推广的人
         */
        if($oInfo['from_uid']==0){  //给上级饭钱
            if($uInfo['invite_uid']){
                $reward = $oInfo['goods_amount']*0.05;
                if($reward>=0.01){       //小于一分钱不记录
                    $r1 = M('user')->where(array('uid'=>$uInfo['invite_uid']))->setInc('money',$reward);

                    //添加用户财务记录
                    $moneyData['uid'] = $uInfo['invite_uid'];
                    $moneyData['amount'] = $reward;
                    $moneyData['time'] = time();
                    $moneyData['note'] = $uInfo['nickname'].'购物返利';
                    $moneyData['type'] = 4;
                    $r2 = M('money')->add($moneyData);
                    if($r1 && $r2){
                        $res = true;
                    }else{
                        $res = false;return $res;
                    }
                }
            }
        }else if($oInfo['reward_amount']>0){    //来自店家 直接给店家钱
            $reward = $oInfo['reward_amount'];
            if($reward>=0.01) {       //小于一分钱不记录
                $r1 = M('user')->where(array('uid' => $oInfo['from_uid']))->setInc('money', $reward);

                //添加用户财务记录
                $moneyData['uid'] = $oInfo['from_uid'];
                $moneyData['amount'] = $reward;
                $moneyData['time'] = time();
                $moneyData['note'] = $uInfo['nickname'] . '购物返利';
                $moneyData['type'] = 3;
                $r2 = M('money')->add($moneyData);
                if ($r1 && $r2) {
                    $res = true;
                } else {
                    $res = false;
                    return $res;
                }
            }
        }

        //给leader返利
        if($uInfo['leader']){
            $reward = $oInfo['goods_amount']*0.01;
            if($reward>=0.01) {       //小于一分钱不记录
                $r1 = M('user')->where(array('uid' => $uInfo['leader']))->setInc('money', $reward);

                //添加用户财务记录
                $moneyData['uid'] = $uInfo['leader'];
                $moneyData['amount'] = $reward;
                $moneyData['time'] = time();
                $moneyData['note'] = $uInfo['nickname'] . '购物返利';
                $moneyData['type'] = 5;
                $r2 = M('money')->add($moneyData);
                if ($r1 && $r2) {
                    $res = true;
                } else {
                    $res = false;
                    return $res;
                }
            }
        }

        return $res;
    }


}