<?php
return array(
	//'配置项'=>'配置值'
    'DB_TYPE' => 'mysql',
    'DB_NAME' => 'vegetables',
    'DB_USER' => 'vegetables',
    'DB_PWD' => 'vegetables123',

    'SHOW_PAGE_TRACE' => true,
    //定义模板信息
    'TMPL_L_DELIM' => '<{',
    'TMPL_R_DELIM' => '}>',

    //定义模块
    'MODULE_ALLOW_LIST'    =>    array('Home','Admin'),
    'DEFAULT_MODULE'       =>    'Home',

    //用户状态
    'UserStatue' => array(
        '1' => '普通会员',
        '2' => '代理会员',
        '3' => '限制账户'
    ),

    //商品状态
    'GoodsStatus' => array(
        '1' => '编辑',
        '2' => '上架',
        '3' => '下架',
    ),

    //订单状态
    'OrderStatus' => array(
        '1' => '待付款',
        '2' => '待发货',
        '3' => '已发货',
    ),
    //订单类型
    'OrderType' => array(
        '1' => '自己购买',
        '2' => '送人',
    ),

    //微信支付状态
    'wxPayStatus' => array(
        '1' => '待支付',
        '2' => '已支付',
    ),

    //用户财务记录类型
    'MoneyType' => array(
        '1' => '付款',
        '2' => '充值',
    ),

);