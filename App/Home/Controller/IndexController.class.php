<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {

    public function index(){
        $Tool = A('Tool');
        $map['status'] = 2;
        $order = 'sold_num desc';
        $data = $Tool->getGoods($map,4,$order);
        $this->assign('hots',$data);

        $goodsType = json_decode(readConf('goodsType'),true);
        foreach($goodsType as $k=>$v){
            $map['cid'] = $k;
            $data = $Tool->getGoods($map,8,$order);
            $lists[] = array('title'=>$v,'data'=>$data,'type'=>$k);
        }
        $this->assign('goodsType',$goodsType);
        $this->assign('lists',$lists);

        $this->display('index');
    }


}