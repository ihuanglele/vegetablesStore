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

/**
 * @param $k
 * @param bool|false $nocache
 * @return mixed
 */
function readConf($k,$nocache = false){
    $r = S($k);
    if(!($r) || $nocache){
        $r =  M('config')->where(array('key'=>$k))->getField('value');
        S($k,$r);
    }
    return $r;
}

function writeConf($k,$v){
    $M = M('config');
    S($k,$v);
    if($M->where(array('key'=>$k))->find()){
        $M->where(array('key'=>$k))->setField('value',$v);
    }else{
        $data['key'] = $k;
        $data['value'] = $v;
        $M->add($data);
    }
}

/**
 * @param string $timestr 需要格式化的时间戳
 * @return bool|string 格式化后时间字符串
 */
function Mydate($timestr=''){
    if(''==$timestr){
        $timestr = time();
    }
    if($timestr==0){
        return '';
    }else {
        return date('Y-m-d H:i', $timestr);
    }
}

/**
 * @param $openId
 */
function getWxUserInfo($openId){
    $access = getWxAccessToken();
    $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access&openid=$openId&lang=zh_CN";
    $res = myCurl($url);
    $info = json_decode($res,true);
    return $info;
}


/**
 * @return mixed 微信凭证
 */
function getWxAccessToken(){
    $token = S('Wx-access_token');
    if(!$token){
        $Wx = C('Wx');
        $appId = $Wx['AppID'];
        $appSec = $Wx['AppSecret'];
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appId&secret=$appSec";
        $res = myCurl($url);
        $data = json_decode($res,true);
        $token = $data['access_token'];
        S('Wx-access_token',$token,$data['expires_in']-1000);
    }
    return $token;
}


function myCurl($url,$data=false){
    $ch = curl_init();
    //设置超时
    curl_setopt($ch, CURLOPT_TIMEOUT, 6);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
    if($data){
        curl_setopt_array($ch,$data);
    }
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    //运行curl，结果以jason形式返回
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}


function creatTradeNum(){
    $num = date('YmdHis').rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);
    $M = M('pay');
    if($M->find($num)){
        $num = creatTradeNum();
    }
    return $num;
}

function getNonceStr($length = 32)
{
    $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
    $str ="";
    for ( $i = 0; $i < $length; $i++ )  {
        $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
    }
    return $str;
}


function sendAdminEmail($type){
    $to = readConf('adminEmail');
    if($to){
        $AdminNotifyType = C('AdminNotifyType');
        $name = C('SITE_NAME').'管理员';
        $subject = C('SITE_NAME').'网站通知';
        $body = $AdminNotifyType[$type];
        send_mail($to, $name, $subject, $body);
    }
}

/**
 * 系统邮件发送函数
 * @param string $to    接收邮件者邮箱
 * @param string $name  接收邮件者名称
 * @param string $subject 邮件主题
 * @param string $body    邮件内容
 * @param string $attachment 附件列表
 * @return boolean
 */
function send_mail($to, $name, $subject = '', $body = '', $attachment = null){
    $config = C('THINK_EMAIL');
    vendor('PHPMailer.class#phpmailer'); //从PHPMailer目录导class.phpmailer.php类文件
    $mail             = new PHPMailer(); //PHPMailer对象
    $mail->CharSet    = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP();  // 设定使用SMTP服务
    $mail->SMTPDebug  = 1;                     // 关闭SMTP调试功能
    // 1 = errors and messages
    // 2 = messages only
    $mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
    $mail->SMTPSecure = 'ssl';                 // 使用安全协议
    $mail->Host       = $config['SMTP_HOST'];  // SMTP 服务器
    $mail->Port       = $config['SMTP_PORT'];  // SMTP服务器的端口号
    $mail->Username   = $config['SMTP_USER'];  // SMTP服务器用户名
    $mail->Password   = $config['SMTP_PASS'];  // SMTP服务器密码
    $mail->SetFrom($config['FROM_EMAIL'], $config['FROM_NAME']);
    $replyEmail       = $config['REPLY_EMAIL']?$config['REPLY_EMAIL']:$config['FROM_EMAIL'];
    $replyName        = $config['REPLY_NAME']?$config['REPLY_NAME']:$config['FROM_NAME'];
    $mail->AddReplyTo($replyEmail, $replyName);
    $mail->Subject    = $subject;
    $mail->MsgHTML($body);
    $mail->AddAddress($to, $name);
    if(is_array($attachment)){ // 添加附件
        foreach ($attachment as $file){
            is_file($file) && $mail->AddAttachment($file);
        }
    }
    return $mail->Send() ? true : $mail->ErrorInfo;
}

/**
 * 微信扫码登录
 */
function wxScanLogin(){
    $ticket = $this->getTicket();
    if($ticket){
        $qrUrl = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urldecode($ticket);
        return $qrUrl;
    }else{
        return false;
    }
}

/**
 * http请求方式: POST
 *   URL: https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=TOKENPOST数据格式：json
 *   POST数据例子：{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 123}}}
 * 或者也可以使用以下POST数据创建字符串形式的二维码参数：
 * {"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "123"}}}
 */
function getTicket($data){
    $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.getWxAccessToken();
    $data = '{"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "'.session('uid').'"}}}';
    $curlArr = array(CURLOPT_POSTFIELDS=>$data);
    $res = json_decode(myCurl($url,$curlArr),true);
    if(isset($res['ticket'])){
        return $res['ticket'];
    }else{
        return false;
    }
}