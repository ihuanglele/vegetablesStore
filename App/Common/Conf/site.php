<?php

//网站配置文件
return array(

    'Wx' => array(
        'AppID' => 'wxcf843d2e9c7d6623',
        'AppSecret' => '8cabd200b73c97753b67832d6674e903',
        'Token' => 'Z60z6Z6Q1aavK30K0GVv460t30bnA606',       //微信Token(令牌)
        'EncodingAESKey' => 'HdJJKSjx0kqcheREd1zYqJnSy4OCcRHeKdJyj2hECSH',//微信消息加解密密钥
        'key' => '123456789012345678901234567890rz',
        'mch_id' => '1267553601', //商户号
        'notify_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/index.php/wechat/notify',
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