<?php
/**
 * Created by PhpStorm.
 * User: huanglele
 * Date: 2015/12/24
 * Time: 16:10
 */

namespace Admin\Model;
use Think\Model;

class GoodsModel extends Model
{
    protected $_validate = array(
        array('name','require','填写商品名字',),   //商品名字
        array('cose_price','checkPrice','成本价格式不对',0,'function'),
        array('vip_price','checkPrice','代理价格式不对',0,'function'),
        array('buy_price','checkPrice','购买价格式不对',0,'function'),
        array('left_num','checkLeftNum','库存数量格式不对',0,'function'),

    );

    public function checkPrice($num){
        if(!preg_match('/^[0-9]+(.[0-9]{1,3})?$/', $num)){
            return false;
        }else{
            return true;
        }
    }

    public function checkLeftNum($num){
        if(!preg_match('/^[0-9]+)?$/', $num)){
            return false;
        }else{
            return true;
        }
    }
}