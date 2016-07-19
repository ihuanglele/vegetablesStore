<?php
/**
 * Author: huanglele
 * Date: 2016/1/11
 * Time: 23:52
 * Description:
 * 微信支付回调处理逻辑
 */

namespace Home\Controller;
use Org\Wxpay;

require_once LIB_PATH."Org/Wxpay/Include.function.php";

class WechatNotifyController extends \Org\Wxpay\WxPayNotify
{
    //查询订单
    public function Queryorder($transaction_id)
    {
        $input = new \Org\Wxpay\WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = \Org\Wxpay\WxPayApi::orderQuery($input);
        if(array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS")
        {
            return true;
        }
        return false;
    }

    //重写回调处理函数
    public function NotifyProcess($data, &$msg)
    {
        $notfiyOutput = array();
        S('notify',$data);
        if(!array_key_exists("transaction_id", $data)){
            $msg = "输入参数不正确";
            return false;
        }
        //查询订单，判断订单真实性
        if(!$this->Queryorder($data["transaction_id"])){
            $msg = "订单查询失败";
            return false;
        }
        //查询订单状态
        $Pay = M('wxpay');
        $payInfo = $Pay->where(array('mytrade'=>$data['out_trade_no']))->field('mytrade,amount,status,uid,oid')->find();
        if($payInfo['status']==1){
            if($payInfo['oid']){    //直接购买
                //更新支付订单，修改订单，{商品减数，商家增钱，商家增钱记录 | }
                $Pay->startTrans();
                $da1['status'] = 2;
                $da1['pay_time'] = time();
                $r1 = $Pay->where(array('mytrade'=>$payInfo['mytrade']))->save($da1);
                $Order = A('Order');
                $r2 = $Order->onOrderPay($payInfo['oid']);
                if($r1 && $r2 ){
                    $Pay->commit();return true;
                }else{
                    $Pay->rollback();return false;
                }

            }else{  //充值
                //处理
                //pay(status),user(money),umoney
                $Pay->startTrans();
                $da1['status'] = 2;
                $da1['pay_time'] = time();
                $r1 = $Pay->where(array('pid'=>$payInfo['mytrade']))->save($da1);

                $r2 = M('user')->where(array('uid'=>$payInfo['uid']))->setInc('cash_money',$payInfo['amount']);

                $da3['amount'] = $payInfo['amount'];
                $da3['uid'] = $payInfo['uid'];
                $da3['time'] = time();
                $da3['type'] = 2;
                $da3['note'] = '微信充值，订单号：'.$payInfo['mytrade'];
                $r3 = M('money')->add($da3);

                if($r1 && $r2 && $r3){
                    $Pay->commit();
                    return true;
                }else{
                    $Pay->rollback();
                    $msg = '处理失败';
                    return false;
                }
            }
        }else{ //订单已经处理过
            $msg = "订单已处理";
            return false;
        }
        return true;
    }

}
