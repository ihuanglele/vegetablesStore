<?php
namespace Home\Controller;

class IndexController extends CommonController {

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
        $this->assign('lists',$lists);

        //获取新闻列表
        $news = M('Help')->field('id,cid,title,time')->order('id desc')->limit(5)->select();
        $this->assign('news',$news);

        $this->display('index');
    }

    //新闻列表
    public function newsList(){
        $map = array();
        $cid = I('get.cid');
        if($cid){
            $map['cid'] = $cid;
        }
        $this->assign('cid',$cid);

        $count = M('help')->where($map)->count();
        $Page = new \Think\Page($count);
        $show = $Page->show();
        //获取新闻列表
        $news = M('Help')->where($map)->field('id,cid,title,time')->order('id desc')->limit($Page->firstRow,$Page->listRows)->select();
        $this->assign('list',$news);
        $this->assign('page',$show);

        $this->display('newsList');
    }

    //新闻详情页面
    public function news(){
        $id = I('get.id');
        $info = M('help')->find($id);
        $this->assign('info',$info);
        $this->display('news');
    }
}