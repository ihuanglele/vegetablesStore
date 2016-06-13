<?php
/**
 * Created by PhpStorm.
 * User: huanglele
 * Date: 2015/12/23
 * Time: 15:40
 */

namespace Admin\Controller;
use Admin\Controller;

class AdminController extends CommonController
{

    /**
     * 查看所有管理员
     */
    public function index(){
        $map = array();
        $user = I('get.user');
        if($user){
            $map['user'] = array('like','%'.$user.'%');
        }
        $this->assign('user',$user);
        $M = M('Admin');
        $order = 'aid desc';
        $this->assign('AdminRole',C('AdminRole'));
        $this->getData($M,$map,$order);
        $this->display('index');
    }

    /**
     * 删除一个用户，不能删除自己
     */
    public function deluser(){
        $this->checkRole(1);
        $id = I('get.id');
        if($id == $this->aid) $this->error('不能删除自己');
        $M = M('Admin');
        if($M->delete($id)){
            record('删除管理员ID'.$id);
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    /**
     * 添加一个用户
     */
    public function adduser(){
        $this->checkRole(1);
        if(isset($_POST['submit'])){
            $name = I('post.name');
            $pwd = I('post.pwd');
            if((!$name || !$pwd)) $this->error('请把表单填写完整');
            $M = M('Admin');
            if($M->where(array('name'=>$name))->find()) $this->error('用户名已存在');
            $data['name'] = $name;
            $data['password'] = md5($pwd);
            $data['time'] = time();
            $data['role'] = 0;
            $data['status'] = 0;
            $id = $M->add($data);
            if($id){
                record('添加管理员ID'.$id);
                $this->success('添加成功',U('index'));
            }else{
                $this->error('添加失败');
            }
        }else{
            $this->assign('role',C('AdminRole'));
            $this->display('adduser');
        }
    }

    /**
     * 查看一个管理员的详细信息
     */
    public function detail(){
        $this->checkRole(1);
        $id = I('get.id');
        $info = M('admin')->find($id);
        $this->assign('info',$info);
        $this->assign('role',C('AdminRole'));
        $this->display('detail');
    }

    /**
     * 修改管理员信息
     */
    public function update(){
        $this->checkRole(1);
        if(isset($_POST['submit'])){
            $aid = I('post.aid');
            $data['aid'] = $aid;
            $pwd = I('post.pwd');
            if($pwd)    $data['password'] = md5($pwd);
            $role = I('post.role');
            $data['role'] = $role;
            M('admin')->save($data);
            record('修改管理员信息,ID'.$aid);
            $this->success('操作成功');
        }else{
            $this->error('页面不存在');
        }
    }

    /**
     * 修改自己的密码
     */
    public function pwd(){
        if(isset($_POST['submit'])){
            $pwd = I('post.pwd');
            $newpwd = I('post.newpwd');
            $repwd = I('post.repwd');
            if((!$pwd || !$newpwd || !$repwd)) $this->error('请把表单填写完整');
            if($pwd == $newpwd) $this->error('新旧密码不能相同');
            if($newpwd != $repwd)   $this->error('两次新密码不同');
            $M = M('Admin');
            $map['aid'] = $this->aid;
            $map['password'] = md5($pwd);
            $id = $M->where($map)->getField('aid');
            if(!$id) $this->error('原密码错误');
            $data['aid'] = $this->aid;
            $data['password'] = md5($newpwd);
            if($M->save($data)){
                record('修改了自己的密码');
                $this->success('修改成功',U('index'));
            }else{
                $this->error('修改失败');
            }
        }else{
            $this->display('pwd');
        }
    }

    /**
     * 查看日志
     */
    public function record(){
        $map = array();
        $aid = I('get.aid');
        if($aid){
            $map['aid'] = $aid;
        }
        $this->assign('aid',$aid);

        $this->getData(M('admin_action'),$map,'id desc');
        $this->display();
    }


    /**
     * 公司信息
     */
    public function info(){
        $this->checkRole(array(1,2));
        if(isset($_POST['submit'])){
            $data = $_POST;
            record('修改了公司信息');
            writeConf('CompanyInfo',json_encode($data));
        }
        $info = readConf('CompanyInfo');
        $this->assign('info',json_decode($info,true));
        $this->display('info');
    }

}