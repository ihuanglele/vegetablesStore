<?php
/**
 * Author: huanglele
 * Date: 2016/1/28
 * Time: 20:43
 * Description:
 */

namespace Admin\Controller;
use Admin\Controller;

class MoneyController extends CommonController
{
    public function _initialize(){
        parent::_initialize();
        $this->checkRole(array(1,3));
    }

    /**
     * 微信账单
     */
    public function index(){
        $map = array();
        $uid = I('get.uid');
        $this->assign('uid',$uid);
        if($uid){
            $map['uid'] = $uid;
        }

        $mytrade = I('get.mytrade');
        $this->assign('mytrade',$mytrade);
        if($mytrade){
            $map['mytrade'] = $mytrade;
        }

        $wxtrade = I('get.wxtrade');
        $this->assign('wxtrade',$wxtrade);
        if($wxtrade){
            $map['wxtrade'] = $wxtrade;
        }

        $status = I('get.status',0,'number_int');
        $this->assign('status',$status);
        if($status>=0){
            $map['status'] = $status;
        }

        $this->getData(M('wxpay'),$map,'mytrade desc');
        $this->assign('PayStatus',C('wxPayStatus'));
        $this->display('wxpay');
    }

    public function shang(){
        $this->assign('MoneyType',C('SMoneyType'));
        $map = array();
        $id = I('get.id','','number_int');
        $this->assign('id',$id);
        if($id){
            $map['aid'] = $id;
        }
        $M = M('smoney');
        $order = 'mid desc';
        $this->getData($M,$map,$order);
        $this->display('shang');
    }


}