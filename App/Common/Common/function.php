<?php
/**
 * Author: huanglele
 * Date: 2016/4/25
 * Time: 下午 05:18
 * Description:
 */

/**
 * 生成一个订单号
 * @return string 订单号
 */
function createTrade() {
    list($tmp1) = explode(' ', microtime());
    return date('YmdHis').floatval($tmp1) * 1000000;
}