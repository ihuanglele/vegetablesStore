<?php
/**
 * Author: huanglele
 * Date: 2016/7/3
 * Time: 下午 05:02
 * Description:
 */

namespace Home\Controller;


use Think\Controller;

class CommonController extends Controller
{

    public function _initialize(){
        //处理一些菜单信息
        $goodsType = json_decode(readConf('goodsType'),true);
        $this->assign('goodsType',$goodsType);

        $this->assign('Cat',C('HelpCat'));
    }

    public function login(){
        $id = I('get.id');
        if($id!=1){
            $Tab = '#loginTab';
        }else{
            $Tab = '#regTab';
        }
        $this->assign('Tab',$Tab);
        $this->display('Public/login');
    }


}