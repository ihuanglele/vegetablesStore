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

    //分类页
    public function lists(){
        $goodsType = json_decode(readConf('goodsType'),true);
        $this->assign('goodsType',$goodsType);
        $this->display('lists');
    }

    //列表页面
    public function goodsList(){
        $type = I('get.type');
        $this->assign('type',$type);
        $name = I('get.name');
        $this->assign('name',$name);
        $this->display('goodsList');
    }

    public function getGoodsList(){
        $p = I('p',1,'number_int');
        $sort = I('get.sort');
        if(!in_array($sort,array('gid','sold_num','buy_price'))){
            $sort = 'gid';
        }
        $type = I('get.type');
        if($type){
            $map['cid'] = $type;
        }
        $name = I('get.name');
        if($name){
            $map['name'] = array('like','%'.$name.'%');
        }
        $map['status'] = 2;
        $Tool = A('Tool');
        $list = $Tool->getList('goods',$map,$sort.' desc','gid,img,name,buy_price as price');
        $num = count($list);
        $ret['status'] = 'success';
        $ret['num'] = $num;
        $ret['list'] = $list;
        if($num==16)  $p++;
        $ret['page'] = $p;
        $this->ajaxReturn($ret);
    }

    //详情页
    public function item(){
        $gid = I('get.id');
        $info = M('goods')->find($gid);
        if($info){
            $fav = session('favorite');
            if($fav && in_array($gid,$fav)){
                $this->assign('fav',1);
            }else{
                $this->assign('fav',0);
            }
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
            if(array_key_exists($k,$gInfo)){
                $i['gid'] = $k;
                $i['num'] = $v;
                $i['name'] = $gInfo[$k]['name'];
                $i['price'] = $gInfo[$k]['price'];
                $i['status'] = $gInfo[$k]['status'];
                $i['left_num'] = $gInfo[$k]['left_num'];
                $i['img'] = $gInfo[$k]['img'];
                $data[] = $i;
            }
        }
        $this->assign('data',$data);
        $this->display('cart');
    }



    //登录页面
    public function login(){
        $this->display('login');
    }

    //注册页面
    public function reg(){
        $from_uid = I('get.from_uid');
        if($from_uid){
            $info = M('user')->field('uid,leader')->find($from_uid);
            if($info){
                session('tg',$info);
            }
        }
        $this->display('reg');
    }

    //微信登录
    public function wxlogin(){
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

        $uInfo = $M->where(array('openid'=>$openId))->field('uid')->find();
        $uid = $uInfo['uid'];

        if($uid){
            session('uid',$uid);
            $this->redirect('UserM/index');
        }else{
            //第一次登录 提示用户登录或者绑定
            session('openid',$openId);
            session('nickname',$data['nickname']);
            session('headimgurl',$data['headimgurl']);
            $this->assign('openid',$openId);
            $this->display('bindReg');
        }
    }

    //微信绑定注册
    public function bindReg(){
        $openid = session('openid');
        if($openid) {
            $this->assign('openid', $openid);
            $this->display('bindReg');
        }else{
            $this->reg();die;
        }
    }
    //微信绑定登录
    public function bindLogin(){
        $openid = session('openid');
        if($openid) {
            $this->assign('openid', $openid);
            $this->display('bindLogin');
        }else{
            $this->login();die;
        }
    }
}