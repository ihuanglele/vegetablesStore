<?php
/**
 * Created by PhpStorm.
 * User: huanglele
 * Date: 2015/12/20
 * Time: 16:59
 */

namespace Home\Controller;
use Think\Controller;
use Org\Wxpay;

class WechatController extends Controller {

    public function _initialize(){
        C('SHOW_PAGE_TRACE',FALSE);
    }

    //微信支付异步通知
    public function notify(){
        C('SHOW_PAGE_TRACE',false);
        $Handle = A('WechatNotify');
        $Handle->handle(false);
    }

    /**
     * 像微信发起一个支付
     * @param $body
     * @param $attach
     * @param $money
     * @param $tag
     * @return Wxpay\成功时返回
     * @throws Wxpay\WxPayException
     */
    public function pay($openId,$body,$attach,$trade_no,$money,$tag){
        //①、获取用户openid
        $tools = new \Org\Wxpay\JsApi();

        //②、统一下单
        $input = new \Org\Wxpay\WxPayUnifiedOrder();
        $input->SetBody($body);
        $input->SetAttach($attach);
        $input->SetOut_trade_no($trade_no);
        $input->SetTotal_fee($money);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag($tag);
        $input->SetNotify_url(C('Wx.notify_url'));
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = \Org\Wxpay\Wxpay::unifiedOrder($input);
        $jsApiParameters = $tools->GetJsApiParameters($order);
        $this->assign('jsApiParamerers',$jsApiParameters);
        //获取共享收货地址js函数参数
        //$editAddress = $tools->GetEditAddressParameters();
        //$this->assign('address',$editAddress);
        return $order;
    }

    /**
     * 扫描付款
     */
    public function scanPay($body,$attach,$trade_no,$money,$tag){
        $tools = new \Org\Wxpay\JsApi();
        //②、统一下单
        $input = new \Org\Wxpay\WxPayUnifiedOrder();
        $input->SetBody($body);
        $input->SetAttach($attach);
        $input->SetOut_trade_no($trade_no);
        $input->SetTotal_fee($money);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag($tag);
        $input->SetNotify_url(C('Wx.notify_url'));
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id($trade_no);
        $order = \Org\Wxpay\Wxpay::unifiedOrder($input);
        return $order;
    }


    public function refund($pid){
        $out_trade_no = $pid;
        $Pay = M('pay');
        $info = $Pay->where(array('pid'=>$pid))->field('money,status,from')->find();
        $total_fee =  $refund_fee = $info['money']*100;
        $res = $this->sendRefund($out_trade_no,$total_fee,$refund_fee);
        if($res['result_code']=='SUCCESS'){
            if($res['return_code']=='SUCCESS'){
                //退款成功 修改记录
                $Pay->where(array('pid'=>$pid))->setField('status',2);
                if($info['from']==1){
                    M('guess')->where(array('pid'=>$pid))->setField('result',4);
                }
                return true;
            }elseif($res['return_code']=='FAIL'){
                //订单出错
                if($info['status']==0)
                    $Pay->where(array('pid'=>$pid))->setField('status',3);
            }
            return false;
        }else{
            return false;
        }
    }

    /**
     * @param $out_trade_no
     * @param $total_fee
     * @param $refund_fee
     */
    private function sendRefund($out_trade_no,$total_fee,$refund_fee){
        require_once LIB_PATH."Org/Wxpay/Include.function.php";
        $input = new \Org\Wxpay\WxPayRefund();
        $input->SetOut_trade_no($out_trade_no);
        $input->SetTotal_fee($total_fee);
        $input->SetRefund_fee($refund_fee);
        $input->SetOut_refund_no(C('Wx.mch_id').date("YmdHis"));
        $input->SetOp_user_id(C('Wx.mch_id'));
        return (\Org\Wxpay\WxPayApi::refund($input));
    }


    /**
     * 查询订单
     */
    public function call(){
        $id = I('get.id');
        $Handle = A('WechatNotify');
        $res = $Handle->Queryorder($id);
        var_dump($res);
    }


    public function refundAll(){
        $pids = M('Pay')->where(array('status'=>1))->getField('pid',true);
        var_dump($pids);
        $i = 0;
        foreach($pids as $pid){
            if($this->refund($pid)){
                echo '<br/>退款成功'.$pid;
            }else{
                echo '<br/>退款失败'.$pid;
            }
        }

    }

    public function temp(){
        $this->display('index');
    }

    public function sendRedPack($money,$openId){
        $data['mch_billno'] = C('Wx.mch_id').date('YmdHis').rand(0,3).rand(0,3).rand(0,3);
        $data['mch_id'] = C('Wx.mch_id');
        $data['wxappid'] = C('Wx.AppID');
        $data['send_name'] = C('Wechat.name');
        $data['re_openid'] = $openId;
        $data['total_amount'] = $money*100;
        $data['wishing'] = 'wishing';
        $data['act_name'] = 'act_name';
        $data['remark'] = 'remark';

        $RedPack = new \Org\Wxpay\WxRedPack();
        $res = $RedPack->send($data);
        return $res;
    }

    public function getRedPack(){
        $id = I('get.id');
        $RedPack = new \Org\Wxpay\WxRedPack();
        $res = $RedPack->query($id);
        var_dump($res);
    }
}