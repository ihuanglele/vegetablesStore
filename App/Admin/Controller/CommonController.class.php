<?php

namespace Admin\Controller;


use Think\Controller;

class CommonController extends Controller
{
    public function _initialize(){
        header("Content-type:text/html;charset=utf-8");
        $acFuns = array('login','register','bindwechat','bindwechatpost');
        $action = strtolower(ACTION_NAME);
        if(in_array($action,$acFuns)){
            //不需要登录的操作
        }else{
            $aid = session('aid');
            if(!$aid){
                layout(false);
                $this->display('Public/login');
                exit;
            }else{
                $this->aid = $aid;
                $this->role = session('role');
                $this->assign('role',$this->role);
            }
        }
    }

    public function _empty(){
        $this->index();
    }


    /**
     * 管理员登录
     */
    public final function login(){
        layout(false);
        if(isset($_POST['submit'])){
            $user = I('post.user');
            $pw = I('post.pw','');
            $map['name'] = $user;
            $map['password'] = md5($pw);
            $info = M('Admin')->field('name,aid')->where($map)->find();
            if($info){
                if($info['role']==0){
                    session('aid',$info['aid']);
                    session('name',$info['user']);
                    $this->success('欢迎回来管理员',U('index/index'));
                }else{
                    if($info['status']==0){
                        $this->error('用户等待审核');
                    }elseif($info['status']==1){
                        session('aid',$info['aid']);
                        session('name',$info['name']);
                        $this->success('登录成功',U('index/index'));
                    }elseif($info['status']==2){
                        $this->error('该账户已被封，请联系管理员',U('index/index'));
                    }
                }
            }else{
                $this->error('用户名不存在或者密码错误');
            }
        }else {
            $this->display('Public/login');
        }
    }

    /**
     * 管理员退出登录
     */
    public final function logout(){
        cookie('name',null);
        cookie('code',null);
        $this->success('成功退出登录',U('Index/login'));
    }

    /**
     * 查询数据库的数据
     * @param $M    数据库
     * @param $map  条件
     * @param $order 排序
     */
    protected function getData($M,$map,$order,$field=false){
        $count = $M->where($map)->count();
        $Page = new\Think\Page($count,25);
        $show = $Page->show();
        if($field){
            $list = $M->where($map)->field($field)->order($order)->limit($Page->firstRow,$Page->listRows)->select();
        }else{
            $list = $M->where($map)->order($order)->limit($Page->firstRow,$Page->listRows)->select();
        }
        $this->assign('list',$list);
        $this->assign('page',$show);
        return $list;
    }

    /**
     * @param $M    需要查询的数据库
     * @param $map  查询条件
     * @return mixed 返回一共数据
     */
    protected function getCount($table,$map){
        return M($table)->where($map)->count();
    }

}