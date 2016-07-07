<?php
/**
 * Created by PhpStorm.
 * author: huanglele
 * Date: 2016/6/16
 * Time: 17:10
 * Description:
 */

namespace Home\Controller;
use Think\Controller;

class UserMController extends Controller
{
    private $uid = null;

    /**
     * 初始化，判断是否登录了
     */
    public function _initialize(){
//        parent::_initialize();
        $this->uid = session('uid');
        if(!$this->uid){
            $this->redirect('mobile/login');die;
        }else{
            C('LAYOUT_NAME','Public/mLayout');
        }

    }

    public function index(){
        $info = M('user')->find($this->uid);
        $this->assign('info',$info);
        //获取推荐商品
        $Tool = A('Tool');
        $goods = $Tool->getGoods(array('status'=>2),4,'sold_num desc');
        $this->assign('goods',$goods);
        $this->display('index');
    }


    //我的收货地址
    public function myAddress(){
        $Tool = A('Tool');
        $map['uid'] = $this->uid;
        $Tool->getList('address',$map,'id desc');
        $this->display('myAddress');
    }

    //添加收货地址
    public function addAddress(){
        if(isset($_POST['action'])){
            $id = I('post.id',0,'number_int');
            $action = I('post.action');
            if($action=='update' && !$id){
                $action = 'add';
            }
            $data['name'] = I('post.receiverName');
            $data['phone'] = I('post.receiverMobile');
            $data['address'] = I('post.address');
            $code = array('province'=>I('post.province'),'city'=>I('post.city'),'district'=>I('post.district'),'deliveryAddress'=>I('post.deliveryAddress'));
            $data['code'] = json_encode($code);
            $data['uid'] = $this->uid;
            $isDefault = I('post.isDefault');
            $isDefault = ($isDefault=='Y') ? 1:0;
            $data['default'] = $isDefault;
            $M = M('address');
            if($action=='update'){
                $data['id'] = $id;
                if($M->save($data)){
                    if($isDefault){
                        $this->setDefaultAddress($id);
                    }
                    $this->success('修改成功');
                }else{
                    $this->error('修改失败');
                }
            }else{
                $id = $M->add($data);
                if($id){
                    if($isDefault){
                        $this->setDefaultAddress($id);
                    }
                    $this->success('添加成功');
                }else{
                    $this->error('添加失败');
                }
            }
        }else{
            $ac = I('ac');
            if(!$ac){
                $ac = U('user/index');
            }
            $this->assign('ac',$ac);
            $this->display('addaddress');
        }
    }

    //设置默认地址
    private function setDefaultAddress($id){
        $map['uid'] = $this->uid;
        $map['id'] = array('neq',$id);
        M('address')->where($map)->setField('default',0);
    }

    /**
     * 我的订单
     */
    public function order(){
        $map['uid'] = session('uid');
        $list = $this->getData('order',$map,'oid desc');
        $gids[] = 0;
        if(count($list)){
            foreach($list as $v){
                $gids[] = $v['gid'];
            }

            $gInfo = M('goods')->where(array('gid'=>array('in',$gids)))->getField('gid,name,img');

            $this->assign('gInfo',$gInfo);

            $this->assign('CityCode',C('CityCode'));

        }

        $this->display('order');

    }


    //确认订单
    public function cart(){
        if(IS_POST){
//            layout(false);
            $gidArr = I('post.gid');
            $numArr = I('post.num');
            $gInfo = M('goods')->where(array('gid'=>array('in',$gidArr)))->getField('gid,name,buy_price as price,status,left_num,img',true);
            $data = array();
            $goodAmount = 0;
            $cart = array();
            foreach($gidArr as $k=>$v){
                if(array_key_exists($v,$gInfo)){
                    $cart[$k] = $v;
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
//            var_dump($gInfo,$data);die;

            //查询收货地址
            $address = M('address')->where(array('uid'=>$this->uid))->field('id,address,phone,name')->order('`default` desc')->select();
            $this->assign('address',$address);

            $this->display('cart');
        }else{
            $this->redirect('mobile/index');
        }

    }

    /**
     * 购买商品
     */
    public function buy(){
        $Tool = A('Tool');
        $order = $Tool->addOrder();
        if($order){
            var_dump($order);
        }else{
            var_dump($order);
        }
    }

    public function test(){
        echo createTrade();
    }

    /**

     * 微信支付

     */

    public function pay(){

        if(isset($_POST['money'])){

            $money = I('post.money',0);

            $uid = session('uid');

            if($money>0){

                $data['uid'] = $uid;

                $data['body'] = '充值';

                $data['attach'] = '充值';

                $data['money'] = $money;

                $data['oid'] = 0;

                $this->sendPayData($data);

            }else{

                $this->error('输入金额有误');

            }

        }else{

            $this->getData('pay',array('uid'=>session('uid'),'status'=>2),'pid desc');

            $this->display('pay');

        }

    }



    private function sendPayData($da){

        $body = $da['body'];

        $attach = $da['attach'];

        $tag = $da['uid'];

        $trade_no = createTradeNum();

        $openId = session('openid');

        $Pay = A('Wechat');

        $order = $Pay->pay($openId,$body,$attach,$trade_no,intval($da['money']*100),$tag);

        if($order['result_code']=='SUCCESS'){//生成订单信息成功

            $data['uid'] = $da['uid'];

            $data['oid'] = $da['oid'];

            $data['create_time'] = date('Y-m-d H:i:s');

            $data['money'] = $da['money'];

            $data['pid'] = $trade_no;

            $data['status'] = 1;

            $data['pay_time'] = 0;

            if(M('pay')->add($data)){

                $this->assign('money',$da['money']);

                $this->display('user/paySub');die;

            }else{

                $this->error('操作失败请重试');die;

            }

        }else{

            $this->error('操作失败请重试');die;

        }

    }



    /**

     *显示提现记录

     */

    public function getCash(){

        $uid = session('uid');

        $uMoney = M('user')->where(array('uid'=>$uid))->getField('money');

        $CashRate = S('getCashRate');

        if(!$CashRate) $CashRate = array('rate'=>10,'money'=>'100');

        if(isset($_POST['money'])){

            $money = I('post.money',0);

            if($money>0){

                if($uMoney<$CashRate['money']){$this->error('低于提现最低金额');die;}

                $Pay = new \Org\Wxpay\WxBizPay();

                $data['openid'] = session('openid');

                $data['amount'] = intval($money*(100-$CashRate['rate']));

                $data['partner_trade_no'] = createBizPayNum();

                $data['desc'] = '提现操作';

                $res = $Pay->send($data);

                if($res['result_code']=='SUCCESS'){//生成订单信息成功

                    $data['uid'] = $uid;

                    $data['time'] = date('Y-m-d H:i:s');

                    $data['money'] = $money;

                    $data['trade'] = $data['partner_trade_no'];

                    $data['status'] = 2;

                    if(M('pack')->add($data)){

                        M('user')->where(array('uid'=>$uid))->setDec('money',$money);

                        $this->success('提现成功',U('user'));

                    }else{

                        $this->error('操作失败请重试');die;

                    }

                }else{

                    $this->error('操作失败请重试');die;

                }

            }else{

                $this->error('输入金额有误');

            }

        }else{

            $this->assign('uMoney',$uMoney);

            $this->assign('CashRate',$CashRate);

            $this->getData('pack',array('uid'=>session('uid'),'status'=>2),'pid desc');

            $this->display('getCash');

        }

    }

    
}