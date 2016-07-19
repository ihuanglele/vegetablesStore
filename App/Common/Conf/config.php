<?php
return array(
	//'配置项'=>'配置值'
    'DB_TYPE' => 'mysql',
    'DB_NAME' => 'vegetables',
    'DB_USER' => 'vegetables',
    'DB_PWD' => 'vegetables123',
    'DB_PREFIX' => 'vege_',

    'SHOW_PAGE_TRACE' => true,
    //定义模板信息
    'TMPL_L_DELIM' => '<{',
    'TMPL_R_DELIM' => '}>',

    //加载自定义配置文件
    'LOAD_EXT_CONFIG' => 'site',

    //定义模块
    'MODULE_ALLOW_LIST'    =>    array('Home','Admin'),
    'DEFAULT_MODULE'       =>    'Home',

    //上传配置
    'UploadConfig' => array(
        'maxSize'       =>  0, //上传的文件大小限制 (0-不做限制)
        'exts'          =>  array('jpg', 'gif', 'png', 'jpeg'),// 设置附件上传类型
        'autoSub'       =>  true, //自动子目录保存文件
        'subName'       =>  array('date', 'Ymd'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath'      =>  './upload/', //保存根路径
        'savePath'      =>  '', //保存路径
        'saveName'      =>  array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'       =>  '', //文件保存后缀，空则使用原后缀
        'replace'       =>  false, //存在同名是否覆盖
        'hash'          =>  true, //是否生成hash编码
        'callback'      =>  false, //检测文件是否存在回调，如果存在返回文件信息数组
        'driver'        =>  '', // 文件上传驱动
    ),

    //管理员角色
    'AdminRole' => array(
        '0' => '弃用',
        '1' => '管理员',
        '2' => '编辑',
        '3' => '财务',
        '4' => '销售'
    ),

    //用户状态
    'UserStatue' => array(
        '0' => '未申请',
        '1' => '申请中',
        '2' => '已通过',
        '3' => '已拒绝',
    ),

    //新闻栏目
    'HelpCat' => array(
        '1' => '网站通知',
        '2' => '使用帮助',
        '3' => '最新活动',
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
        '2' => '送人他人',
    ),
    //订单支付类型
    'OrderPayType' => array(
        '1' => '微信支付',
        '2' => '余额支付',
    ),

    //微信支付状态
    'wxPayStatus' => array(
        '0' => '全部',
        '1' => '待支付',
        '2' => '已支付',
    ),

    //用户财务记录类型
    'MoneyType' => array(
        '0' => '全部',
        '1' => '付款',
        '2' => '微信充值',
        '3' => '店铺收入',    //店铺收入
        '4' => '下属返利',    //下属返利
        '5' => '团队返利',    //总监返利
        '6' => '提现',        //money
        '9' => '提现中',       //money
        '7' => '现金充值',        //cash_money
        '8' => '扣除现金',        //cash_money
    ),

);