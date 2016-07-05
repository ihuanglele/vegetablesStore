<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/7/31
 * Time: 下午 05:56
 */

namespace Admin\Controller;
use Admin\Controller;

class HelpController extends CommonController{

    public function _initialize(){
        parent::_initialize();
        $this->checkRole(array(1,2));
        $this->assign('cidArr',C('HelpCat'));
    }

    public function t(){
       $this->del();
    }

    public function index(){
        $map = array();
        $cid = I('get.cid','','number_int');
        if($cid != ''){
            $map['cid'] = $cid;
        }

        $word = I('get.word','');
        if($word != ''){
            $map['title'] = array('like','%'.$word.'%');
        }

        $id = I('get.id','');
        if($id != ''){
            $map['id'] = $id;
        }

        $M = M('Help');
        $count = $M->where($map)->count();
        $Page = new \Think\Page($count,25);
        $show = $Page->show();
        $list = $M->where($map)->order('id desc')->field('id,cid,title')->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->assign('page',$show);
        $this->assign('list',$list);
        $this->display('list');
    }

    public function del(){
        $id = I('get.id','','number_int');
        if($id == ''){
            $this->error('参数错误',U('Help/index'));
            die();
        }
        if(M('Help')->delete($id)){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    public function editor(){
        $id = I('get.id','','number_int');
        if($id == ''){
            $this->error('参数错误',U('Help/index'));
            die();
        }
        $info = M('Help')->find($id);
        if($info){
            $this->assign('info',$info);
            $this->display('add');
        }else{
            $this->error('该文章不存在',U('help/index'));
        }
    }

    public function add(){
        $this->display('add');
    }

    public function post(){
        $id = I('post.id','','number_int');
        $data['title'] = I('post.title');
        $data['cid'] = I('post.cid');
        $data['content'] = $_POST['content'];
        $data['time'] = time();
        $H = M('Help');
        if($id == ''){
            if($H->add($data)){
                $this->success('添加成功',U('help/index'));
            }else{
                $this->error('添加失败');
            }
        }else{
            $data['id'] = $id;
            if($H->save($data)){
                $this->success('操作成功',U('help/index'));
            }else{
                $this->error('操作失败');
            }
        }
    }

}