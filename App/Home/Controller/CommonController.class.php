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
            $info = M('user')->where($map)->field('uid,nickname')->find();
            if($info){
                session('uid',$info['uid']);
                session('nickname',$info['nickname']);
                $this->success('登录成功',U('user/index'));
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
            $map['phone'] = $phone;
            if(M('user')->where($map)->find()){
                $this->error('该手机号已经注册');
            }else{
                $data['nickname'] = '匿名';
                $data['phone'] = $phone;
                $data['password'] = md5($password);
                $data['openid'] = $data['status'] = '';
                $data['coin'] = $data['money'] = $data['use_money'] = $data['invite_uid'] = 0;
                $uid = M('user')->add($data);
                if($uid){
                    session('uid',$uid);
                    session('nickname','匿名');
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

}