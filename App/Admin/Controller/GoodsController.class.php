<?php
/**
 * Author: huanglele
 * Date: 2016/5/3
 * Time: 上午 10:51
 * Description:
 */

namespace Admin\Controller;


class GoodsController extends CommonController
{

    public function _initialize(){
        parent::_initialize();
        $this->checkRole(array(1,4));
    }

    /**
     * 列出所有商品
     */
    public function index(){
        $sort = I('get.sort','gid');
        $px = I('get.or');
        if($px=='1'){
            $this->assign('or',0);
            $this->assign('cls','glyphicon-sort-by-attributes');
            $px = 'asc';
        }else{
            $this->assign('or',1);
            $this->assign('cls','glyphicon-sort-by-attributes-alt');
            $px = 'desc';
        }
        $map = array();
        $gid = I('get.gid','','number_int');
        if($gid){
            $map['gid'] = $gid;
        }
        $this->assign('gid',$gid);

        $name = I('get.name');
        if($name){
            $map['name'] = array('like','%'.$name.'%');
        }
        $this->assign('name',$name);

        $status = I('get.status',0,'number_int');
        if($status){
            $map['status'] = $status;
        }
        $this->assign('status',$status);

        $M = M('goods');
        $count = $M->where($map)->count();
        $Page = new \Think\Page($count,25);
        $show = $Page->show();
        $list = $M->where($map)->field('gid,name,status,left_num,sold_num')->order($sort.' '.$px)->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('page',$show);
        $this->assign('GoodsStatus',C('GoodsStatus'));
        $this->assign('sort',$sort);
        $this->assign('list',$list);
        $this->display('index');
    }

    /**
     * 添加
     */
    public function add(){
        $this->assign('GoodsStatus',C('GoodsStatus'));
        $this->assign('GoodsType',json_decode(readConf('goodsType'),true));
        $this->display();
    }

    /**
     * 修改一个商品
     */
    public function editor(){
        $id = I('get.id');
        $info = M('goods')->find($id);
        if($info){
            $this->assign('info',$info);
            $this->assign('GoodsStatus',C('GoodsStatus'));
            $this->assign('GoodsType',json_decode(readConf('goodsType'),true));
            $this->assign('GoodsOnlineTime',C('GoodsOnlineTime'));
            $this->display();
        }else{
            $this->error('参数错误',U('index'));
        }
    }

    /**
     * 添加商品 或者修改商品 处理表单
     */
    public function update(){
        if(isset($_POST['submit'])){
            $ac = I('post.submit');
            $data = $_POST;
            $M = D('Goods');
            if(!$M->create($data))  $this->error($M->getError());

            //判断是否有文件上传
            if($_FILES['img']['error']==0){
                //处理图片
                $upload = new \Think\Upload(C('UploadConfig'));
                $info   =   $upload->upload();
                if($info) {
                    $data['img'] = $info['img']['savepath'].$info['img']['savename'];
                }else{
                    $this->error($upload->getError());
                }
            }

            if($ac == 'add'){
                $data['create_time'] = time();
                $gid = $M->add($data);
                if($gid){
                    record("添加了 $gid 号商品");
                    $this->success('添加成功',U('index'));
                }else{
                    $this->error('添加失败请重试');
                }
            }elseif($ac == 'update'){
                $gid = I('post.gid');
                if(!$gid)   $this->error('参数错误',U('index'));
                if($M->save($data)){
                    record("修改了 $gid 号商品");
                    $this->success('更新成功',U('index'));
                }else{
                    $this->error('更新失败请重试');
                }
            }
        }else{
            $this->error('页面不存在',U('index'));
        }
    }

    /**
     * 设置商品种类
     */
    public function setGoodsType(){
        //获取现在已经添加了的商品类别
        $useType = M('goods')->group('cid')->getField('cid',true);
        if(!$useType){
            $useType = array();
        }
        $useTypeStr = '';
        foreach($useType as $v){
            $useTypeStr .= $v.'、';
        }
        if(isset($_POST['submit'])){
            $ids = $_POST['ids'];
            $name = $_POST['name'];
            foreach($ids as $k=>$v){
                if($v || in_array($v,$useType)){
                    $type[$v] = $name[$k];
                }
            }
            arsort($type);
            $type = json_encode($type);
            writeConf('goodsType',$type);
        }else{
            $type = readConf('goodsType');
        }
        $this->assign('list',json_decode($type,true));
        $this->assign('useType',$useType);
        $this->assign('useTypeStr',$useTypeStr);
        $this->display('setGoodsType');
    }

    /**
     * 导出商品信息
     */
    public function export(){
        C('SHOW_PAGE_TRACE',false);
        $list = M('goods')->order('gid desc')->field('gid,name,cose_price,vip_price,buy_price,market_price,sold_num,left_num,cid,status')->select();
        $type = readConf('goodsType');
        $Cid = json_decode($type,true);
        $Status = C('GoodsStatus');
        $data = array();
        $title = array('编号','商品名','成本价','代理价','购买价','市场价','销量','库存','状态','类别');
        if(count($list)){
            foreach($list as $v){
                $t[] = $v['gid'];
                $t[] = $v['name'];
                $t[] = $v['cose_price'];
                $t[] = $v['vip_price'];
                $t[] = $v['buy_price'];
                $t[] = $v['market_price'];
                $t[] = $v['sold_num'];
                $t[] = $v['left_num'];
                $t[] = $Status[$v['status']];
                $t[] = $Cid[$v['cid']];
                $data[] = $t;
            }
        }
        exportexcel($data,$title,'商品信息');die;
    }

}