<?php
$banner_image = '/Public/images/index/banner.png';
$name = '中青旅博汇人才招考系统';
$site_name = '石家庄工程职业学院';
$site_title = '石家庄工程职业学院2022年教师公开招聘';
$site_name_pref = 'gczy';
$examination_method = '笔试准考证';

$dbConfig = [
    'NAME'   =>  $name,
    'SITE_KEYWORDS'   =>  $name,
    'SITE_DESCRIPTION'   =>  $name,
    'FOOTER_NAME'   =>  $name,
    'SITE_NAME'   =>  $site_name,
    'SITE_TITLE'   =>  $site_title,
    'BANNER_IMAGE'   =>  $banner_image,
    'SITE_NAME_PREF'   =>  $site_name_pref,
    'EXAMINATION_METHOD'   =>  $examination_method,

    'DB_HOST'   => '127.0.0.1', // 服务器地址
    'DB_NAME'   => 'gczyxy_zyhzedu', // 数据库名
    'DB_USER'   => 'gczyxy_zyhzedu', // 用户名
    'DB_PWD'    => 'C3akSE6WiYtNXCnR', // 密码
    'DB_PREFIX' =>  'tp_',    // 数据库表前缀

    'REDIS_ON' 				=> true, //是否开启
    'REDIS_RW_SEPARATE' 	=> false, //Redis读写分离 true 开启
    'REDIS_HOST'			=>'127.0.0.1', //redis服务器ip，多台用逗号隔开；读写分离开启时，第一台负责写，其它[随机]负责读；
    // 'REDIS_PORT'			=>'6379',//端口号
    'REDIS_PORT'			=>'6177',//端口号
    'DATA_CACHE_TIMEOUT'	=>'3000',//超时时间
    'REDIS_PERSISTENT'		=>false,//是否长连接 false=短连接
    'REDIS_AUTH'			=>'wending123a@ABC',//AUTH认证密码
    'REDIS_PREF' 			=> $site_name_pref.':', //redis 前缀
];
