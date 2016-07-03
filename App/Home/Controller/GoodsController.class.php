<?php
/**
 * Author: huanglele
 * Date: 2016/6/28
 * Time: 下午 10:47
 * Description:
 */

namespace Home\Controller;


class GoodsController extends CommonController
{

    public function index(){
        $cid = I('get.type',0,'number_int');
        if($cid){
            $map['cid'] = $cid;
        }
        $this->assign('cid',$cid);
        $map['status'] = 2;
        $Tool = A('Tool');

        $order = 'gid desc';
        $field = 'gid,img,name,buy_price as price';
        $Tool->getList('goods',$map,$order,$field);

        $order = 'sold_num desc';
        $data = $Tool->getGoods($map,6,$order);
        $this->assign('hots',$data);

        $goodsType = json_decode(readConf('goodsType'),true);
        $this->assign('goodsType',$goodsType);
        $this->display('index');
    }

    public function item(){
        $gid = I('get.id');
        $info = M('goods')->find($gid);
        if($info){
            $goodsType = json_decode(readConf('goodsType'),true);
            $this->assign('goodsType',$goodsType);
            $info['type'] = $goodsType[$info['cid']];
            $this->assign('info',$info);

            $Tool = A('Tool');
            $map['status'] = 2;
            $map['cid'] = $info['cid'];
            $order = 'sold_num desc';
            $data = $Tool->getGoods($map,4,$order);
            $this->assign('hots',$data);
            $this->display('item');
        }else{
            $this->error('商品不存在',U('goods/index'));
        }
    }

}