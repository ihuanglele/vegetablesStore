<?php
namespace Admin\Controller;

class IndexController extends CommonController {

    public function index(){
        $mapGoods = array();
        $mapGift = array();

        $goodsInfo = M('goods')->where($mapGoods)->order('status asc')->field('count(`gid`) as num,status')->group('status')->select();
        $giftInfo = M('orders')->where($mapGift)->order('status asc')->field('count(`trade`) as num,status')->group('status')->select();
        $this->assign('goods',$goodsInfo);
        $this->assign('gift',$giftInfo);
        $this->assign('GoodsStatus',C('GoodsStatus'));
        $this->assign('TaskStatus',C('TaskStatus'));
        $this->assign('GiftStatus',C('DuijiangStatus'));
        $this->display('index');
    }

    public function role(){
        var_dump(session());
    }

}