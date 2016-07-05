<?php
/**
 * Created by PhpStorm.

 * author: huanglele

 * Date: 2016/6/16

 * Time: 17:10

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

    public function index(){
        $info = M('user')->find($this->uid);
        $this->assign('info',$info);
        //获取推荐商品
        $Tool = A('Tool');
        $goods = $Tool->getGoods(array('status'=>2),4,'sold_num desc');
        $this->assign('goods',$goods);
        $this->display('index');
    }


    /**

     * 微信登录

     */

    public function login(){

        $this->checkJump();

        $tools = new \Org\Wxpay\UserApi();

        $openId = $tools->GetOpenid();

        $wxInfo = $tools->getInfo();

        if(!$wxInfo || isset($wxInfo['errcode'])){

            $this->error('微信授权出错',U('index/index'));

        }

        $info = getWxUserInfo($openId);

        if(!$info || isset($info['errcode'])){

            var_dump($info);die;

            $this->error('登录出了点状况',U('index/index'));

        }



        //判断之前是否存储过用户资料

        $M = M('user');

        $data = array_merge($info,$wxInfo);



        session('openid',$openId);



        if(isset($data['headimgurl'])){

            $data['headimgurl'] = trim($data['headimgurl'],'0').'64';

        }

        $uInfo = $M->where(array('openid'=>$openId))->field('uid,agent')->find();

        $uid = $uInfo['uid'];

        $jump = session('jump');

        if(!$jump){

            $jump = U('user/index');

        }

        session('jump',null);

        if($uid){

            session('uid',$uid);

            session('agent',$uInfo['agent']);

            header("Location:$jump");

        }else{

            //第一次登录 添加到用户表里面

            $data['money'] = $data['vip'] = $data['leader'] = $data['agent'] = $data['up1'] = $data['up2'] = 0;

            $r = $M->add($data);

            if($r){

                session('uid',$r);

                session('agent',0);

                header("Location:$jump");

            }

        }

    }



    public function checkJump(){

        $referer = $_SERVER['HTTP_REFERER'];

        $host = $_SERVER['HTTP_HOST'];

        $patten = "/^http:\/\/$host(\/index.php)?(.*)$/i";

        if(preg_match($patten,$referer,$arr)){

            $uri = $arr[2];

            if(!preg_match("/user\/login/i",$uri)){

                session('jump',$referer);

            }

        }

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



    /**

     * 购买商品

     */

    public function buy(){

        $gid = I('post.gid');

        $uid = session('uid');

        $gInfo = M('goods')->field('gid,price,self,up1,up2,leader,status')->find($gid);

        if($gInfo && $gInfo['status']==1) {

            $data = $_POST;

            //查询账户里面的钱够不够

            $uInfo = M('user')->where(array('uid'=>$uid))->field('up1,up2,leader,agent,money,openid')->find();

            $data['uid'] = $uid;

            $data['time'] = time();

            $data['money'] = $gInfo['price'];



            //判断支付方式

            $type = I('post.type');

            if($type=='money'){     //余额支付

                if($uInfo['money']<$gInfo['price']){$this->error('账户余额不足',U('user/pay'));die;}

                //扣钱 添加订单记录  发送红包

                $da1['uid'] = $uid;

                $da1['money'] = $uInfo['money']-$gInfo['price'];

                if($da1['vip']<$gInfo['price']) $da1['vip'] = $gInfo['price'];

                M('user')->save($da1);

                $data['status'] = 2;

                $oid = M('order')->add($data);      //添加订单记录

                onBuyEvent($oid);   //发送红包

                sendOrderTempMsg($oid);

                $this->success('购买成功',U('user/index'));

            }else{      //微信支付

                $data['uid'] = $uid;

                $data['body'] = '充值';

                $data['attach'] = '充值';

                $data['money'] = $gInfo['price'];

                $data['status'] = 1;

                $oid = M('order')->add($data);

                $data['oid'] = $oid;

                $this->sendPayData($data);

            }

        }else{

            $this->error('商品不存在');

        }

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