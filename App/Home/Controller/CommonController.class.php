<?php
/**
 * Author: huanglele
 * Date: 2016/7/3
 * Time: 下午 05:02
 * Description:
 */

namespace Home\Controller;
use Think\Controller;

class CommonController extends Controller{

    public function _initialize(){

        //处理一些菜单信息

        $goodsType = json_decode(readConf('goodsType'),true);

        $this->assign('goodsType',$goodsType);

        $this->assign('Cat',C('HelpCat'));

    }

    public function login(){
        $id = I('get.id');
        if($id!=1){
            $Tab = '#loginTab';
        }else{
            $Tab = '#regTab';
        }
        $this->assign('Tab',$Tab);
        $this->display('Public/login');
    }

    //处理登录
    public function loginPost(){
        if(IS_AJAX){
            $phone = I('post.phone');
            $password = I('post.password');
            $map['phone'] = $phone;
            $map['password'] = md5($password);
            $info = M('user')->where($map)->field('uid,nickname,favorite')->find();
            if($info){
                $openid = session('openid');
                if($openid){
                    $data['uid'] = $info['uid'];
                    $data['nickname'] = session('nickname');
                    $data['headimgurl'] = session('headimgurl');
                    $data['openid'] = session('openid');
                    session('nickname',null);
                    session('headimgurl',null);
                    if(M('user')->save($data)){
                        session('favorite',json_decode($info['favorite'],true));
                        session('uid', $info['uid']);
                        session('nickname', $data['nickname']);
                        $this->success('登录成功', U('user/index'));
                    }else{
                        $this->error('绑定失败');
                    }
                }else {
                    session('uid', $info['uid']);
                    session('nickname', $info['nickname']);
                    $this->success('登录成功', U('user/index'));
                }
            }else{
                $this->error('用户名或者密码不存在');
            }
        }
    }

    //用户注册
    public function regPost(){
        if(IS_AJAX){
            $phone = I('post.phone');
            $password = I('post.password');
            $openid = I('post.openid');
            $map['phone'] = $phone;
            if(M('user')->where($map)->find()){
                $this->error('该手机号已经注册');
            }else{
                if($openid){
                    $data['nickname'] = session('nickname');
                    $data['headimgurl'] = session('headimgurl');
                    $data['openid'] = session('openid');
                    session('nickname',null);
                    session('headimgurl',null);
                }else{
                    $data['nickname'] = $phone;
                    $data['openid'] = $openid;
                    $data['headimgurl'] = '';
                }
                $tg = session('tg');
                if($tg){
                    $data['invite_uid'] = $tg['uid'];
                    $data['leader'] = $tg['leader'];
                }else{
                    $data['invite_uid'] = 0;
                    $data['leader'] = 0;
                }
                $data['is_leader'] = 0;
                $data['phone'] = $phone;
                $data['password'] = md5($password);
                $data['status'] = '';
                $data['coin'] = $data['money'] = $data['use_money'] =  0;
                $uid = M('user')->add($data);
                if($uid){
                    session('uid',$uid);
                    session('nickname',$data['nickname']);
                    $this->success('注册成功',U('user/index'));
                }else{
                    $this->error('注册失败请重试');
                }
            }
        }
    }

    //添加到购物车里面，返回购物车里面商品的数量
    public function addCart(){
        $id = I('get.id', 0, 'number_int');
        $num = I('get.num', 0, 'number_int');
        if ($id == 0 || $num == 0) {
            $this->error('参数错误');
        }
        $cart = session('cart');
        if (is_array($cart)) {
            if (array_key_exists($id, $cart)) {
                $cart[$id] += $num;
            } else {
                $cart[$id] = $num;
            }
        } else {
            $cart[$id] = $num;
        }
        $count = count($cart);
        session('cart', $cart);
        $this->success($count);
    }

    //删除购物车里面的商品
    public function delCart(){
        $id = I('get.id', 0, 'number_int');
        if ($id == 0) {
            $this->error('参数错误');
        }
        $cart = session('cart');
        if (is_array($cart)) {
            if (array_key_exists($id, $cart)) {
                unset($cart[$id]);
            }
        }
        $count = count($cart);
        session('cart', $cart);
        $this->success($count);
    }

    //获取购物车里面商品的数量
    public function getCartNum(){
        $cart = session('cart');
        $count = count($cart);
        echo ($count);
    }

    //删除地址
    public function delAddress(){
        $id = I('id');
        $map['id'] = $id;
        $map['uid'] = session('uid');
        $res = M('address')->where($map)->delete();
        echo ($res);
    }

    //处理收藏
    public function doFav(){
        $uid = session('uid');
        if($uid){
            $favorite = M('user')->where(array('uid'=>session('uid')))->getField('favorite');
            $ac = I('get.ac');
            $id = I('get.id');
            $favArr = json_decode($favorite,true);

            if($ac=='un'){//删除
                foreach($favArr as $k=>$v){
                    if($v==$id){
                        unset($favArr[$k]);break;
                    }
                }
            }else{//添加
                $favArr[] = $id;
            }
            $favArr = array_unique($favArr);
            session('favorite',$favArr);
            $favJson = json_encode($favArr);
            M('user')->where(array('uid'=>session('uid')))->setField('favorite',$favJson);
            $this->success('操作成功');
        }else{
            $this->error('请先登录');
        }
    }

    public function logout(){
        session('uid',null);
        $this->success('安全退出');
    }

    public function getOpenId(){
        $openId = session('openid');
        if($openId){
            die(1);
        }else{
            $tools = new \Org\Wxpay\JsApi();
            $openId = $tools->GetOpenid();
            session('openid',$openId);
            if($openId){
                die(1);
            }else {
                die(0);
            }
        }
    }

    //查看订单状态
    public function checkOrder(){
        layout(false);C('SHOW_PAGE_TRACE',false);
        $map['trade'] = I('get.id');
        $map['uid'] = session('uid');
        $status = M('orders')->where($map)->getField('status');
        if(!$status){
            $status = 0;
        }
        echo $status;
    }

}