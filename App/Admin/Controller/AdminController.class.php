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
        $map['role'] = 0;
        $M = M('Admin');
        $order = 'aid desc';
        $this->getData($M,$map,$order);
        $this->display('index');
    }

    /**
     * 删除一个用户，不能删除自己
     */
    public function deluser(){
        $id = I('get.id');
        if($id == $this->aid) $this->error('不能删除自己');
        $M = M('Admin');
        if($M->delete($id)){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }

    /**
     * 添加一个用户
     */
    public function adduser(){
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
            if($M->add($data)){
                $this->success('添加成功',U('index'));
            }else{
                $this->error('添加失败');
            }
        }else{
            $this->display('adduser');
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
                $this->success('修改成功',U('index'));
            }else{
                $this->error('修改失败');
            }
        }else{
            $this->display('pwd');
        }
    }

    /**
     * 帮助文档
     */
    public function help(){
        if(isset($_POST['submit'])){
            $help = $_POST['help'];
            writeConf('userHelpDoc',$help);
        }else{
            $help = readConf('userHelpDoc');
        }
        $this->assign('help',$help);
        $this->display('help');
    }

    /**
     * 联系客服
     */
    public function kefu(){
        if(isset($_POST['submit'])){
            $kefu = $_POST['kefu'];
            writeConf('userKeFuDoc',$kefu);
        }else{
            $kefu = readConf('userKeFuDoc');
        }
        $this->assign('kefu',$kefu);
        $this->display('kefu');
    }

    /**
     * 后台通知
     */
    public function note(){
        if(isset($_POST['submit'])){
            $note = $_POST['note'];
            writeConf('adminNoteDoc',$note);
        }else{
            $note = readConf('adminNoteDoc');
        }
        $this->assign('note',$note);
        $this->display('note');
    }

    /**
     * 设置商家默认密码
     */
    public function setDefaultPwd(){
        if(isset($_POST['submit'])){
            $pwd = $_POST['pwd'];
            writeConf('adminDefaultPwd',$pwd);
        }else{
            $pwd = readConf('adminDefaultPwd');
        }
        $this->assign('pwd',$pwd);
        $this->display('setPwd');

    }

    /**
     * 网站商家注册
     */
    public function openReg(){
        if(isset($_POST['submit'])){
            $s = $_POST['s'];
            writeConf('openshangRegister',$s);
        }else{
            $s = readConf('openshangRegister');
        }
        $this->assign('s',$s);
        $this->display('openReg');
    }

    /**
     * 设置邮箱
     */
    public function setAdminEmail(){
        if(isset($_POST['submit'])){
            $value = $_POST['value'];
            writeConf('adminEmail',$value);
        }else{
            $value = readConf('adminEmail');
        }
        $this->assign('value',$value);
        $this->display('setAdminEmail');
    }

}