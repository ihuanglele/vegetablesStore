<?php

//网站配置文件
return array(

    'Wx' => array(
        'AppID' => 'wxf00aa21907c10b2f',
        'AppSecret' => '536877a49785e511d4f2dfb830ea0501',
        'Token' => 'Z60z6Z6Q1aavK30K0GVv460t30bnA606',       //微信Token(令牌)
        'EncodingAESKey' => 'HdJJKSjx0kqcheREd1zYqJnSy4OCcRHeKdJyj2hECSH',//微信消息加解密密钥
        'key' => 'WeixingTianxia043187956777wxtx00',
        'mch_id' => '1363898802', //商户号
        'notify_url' => 'http://'. $_SERVER['HTTP_HOST'].'/index.php/wechat/notify',
        'SSLCERT_PATH' => LIB_PATH . "Org/Wxpay/apiclient_cert.pem",
        'SSLKEY_PATH' => LIB_PATH . "Org/Wxpay/apiclient_key.pem",
        'CURL_PROXY_HOST' => "0.0.0.0",
        'CURL_PROXY_PORT' => 0,
        'REPORT_LEVENL' => 1,
    ),

    'Wechat' => array(
        'welcome' => '欢迎关注我们',
    ),

);