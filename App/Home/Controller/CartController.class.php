<?php
/**
 * Author: huanglele
 * Date: 2016/6/28
 * Time: 下午 10:44
 * Description:
 */

namespace Home\Controller;


use Think\Controller;

class CartController extends Controller
{

    public function addCart(){
        $id = I('post.productId');
        $num = I('post.number');
        if ($id == 0 || $num == 0) {
            $this->error('参数错误');
        }
        $cart = session('cart');
        if (is_array($cart)) {
            if (array_key_exists($id, $cart)) {
                $cart[$id] += $num;
            } else {
                $cart[$id] = $num;
            }
        } else {
            $cart[$id] = $num;
        }
        session('cart', $cart);
        $ret['result'] = 0000;
        $ret['cmsProduct'] = '';

        $this->ajaxReturn($ret);
    }


}