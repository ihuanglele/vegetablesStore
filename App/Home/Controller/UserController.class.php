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
            foreach($gidArr as $k=>$v){
                $i['gid'] = $v;
                $i['num'] = $numArr[$k];
                $i['name'] = $gInfo[$v]['name'];
                $i['price'] = $gInfo[$v]['price'];
                $i['status'] = $gInfo[$v]['status'];
                $i['left_num'] = $gInfo[$v]['left_num'];
                $i['img'] = $gInfo[$k]['img'];
                $data[] = $i;
            }
            $this->assign('data',$data);
            $this->display('cart');
        }else{
            $this->redirect('index/index');
        }

    }


}