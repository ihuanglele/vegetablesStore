<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {

    public function index(){
        $arr[] = array('gid'=>1,'pay_each_price'=>3,'discount'=>'9');
        $arr[] = array('gid'=>223423423,'pay_each_price'=>3234323,'discount'=>'9');
        $arr[] = array('gid'=>223423423,'pay_each_price'=>3234323,'discount'=>'9');
        $arr[] = array('gid'=>223423423,'pay_each_price'=>3234323,'discount'=>'9');
        $arr[] = array('gid'=>223423423,'pay_each_price'=>3234323,'discount'=>'9');
        $str = json_encode($arr);
        echo strlen($str);
    }

}