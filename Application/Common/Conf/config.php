<?php
include_once "site.php";

$config = array(
    //'配置项'=>'配置值'
    'DB_PORT'   => '3306', // 端口
    'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_DEBUG'  		=>  false, // 数据库调试模式 开启后可以记录SQL日志
    'DB_FIELDS_CACHE'   =>  true,        // 启用字段缓存

    'NAME'   =>  $name,
    'SITE_KEYWORDS'   =>  $name,
    'SITE_DESCRIPTION'   =>  $name,
    'FOOTER_NAME'   =>  $name,
    'SITE_NAME'   =>  $site_name,
    'SITE_TITLE'   =>  $site_title,

    /* URL设置 */
    'URL_CASE_INSENSITIVE'  =>  true,   // 默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'             =>  0,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
    // 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式

    // 'URL_PATHINFO_DEPR'     =>  '/',	// PATHINFO模式下，各参数之间的分割符号

    /* 数据缓存设置 */
    'DATA_CACHE_TIME'       =>  0,      // 数据缓存有效期 0表示永久缓存
    'DATA_CACHE_COMPRESS'   =>  true,   // 数据缓存是否压缩缓存
    'DATA_CACHE_CHECK'      =>  false,   // 数据缓存是否校验缓存
    'DATA_CACHE_PREFIX'     =>  'Bank_',     // 缓存前缀
    'DATA_CACHE_TYPE'       =>  'File',  // 数据缓存类型,支持:File|Db|Apc|Memcache|Shmop|Sqlite|Xcache|Apachenote|Eaccelerator
    'DATA_CACHE_PATH'       =>  TEMP_PATH,// 缓存路径设置 (仅对File方式缓存有效)
    'DATA_CACHE_KEY'        =>  '',	// 缓存文件KEY (仅对File方式缓存有效)
    'DATA_CACHE_SUBDIR'     =>  false,    // 使用子目录缓存 (自动根据缓存标识的哈希创建子目录)
    'DATA_PATH_LEVEL'       =>  1,        // 子目录缓存级别

    /* 错误设置 */
    'SHOW_PAGE_TRACE'       =>  true,
    'ERROR_MESSAGE'         =>  '页面错误！请稍后再试～',//错误显示信息,非调试模式有效
    'ERROR_PAGE'            =>  '',	// 错误定向页面
    'SHOW_ERROR_MSG'        =>  TRUE,    // 显示错误信息
    'TRACE_MAX_RECORD'      =>  100,    // 每个级别的错误信息 最大记录数

    /* 日志设置 */
    'LOG_RECORD'            =>  TRUE,   // 默认不记录日志
    'LOG_TYPE'              =>  'File', // 日志记录类型 默认为文件方式
    'LOG_LEVEL'             =>  'EMERG,ALERT,CRIT,ERR',// 允许记录的日志级别
    'LOG_FILE_SIZE'         =>  2097152,	// 日志文件大小限制
    'LOG_EXCEPTION_RECORD'  =>  TRUE,    // 是否记录异常信息日志

    /* 模板引擎设置 */
    'TMPL_CONTENT_TYPE'     =>  'text/html', // 默认模板输出类型
    'TMPL_ACTION_ERROR'     =>  THINK_PATH.'Tpl/dispatch_jump.tpl', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   =>  THINK_PATH.'Tpl/dispatch_jump.tpl', // 默认成功跳转对应的模板文件
    'TMPL_EXCEPTION_FILE'   =>  THINK_PATH.'Tpl/think_exception.tpl',// 异常页面的模板文件
    'TMPL_STRIP_SPACE'      =>  true,       // 是否去除模板文件里面的html空格与换行
    'TMPL_CACHE_ON'         =>  true,        // 是否开启模板编译缓存,设为false则每次都会重新编译

    /* 默认设定 */
    'DEFAULT_M_LAYER'       =>  'Model', // 默认的模型层名称
    'DEFAULT_C_LAYER'       =>  'Controller', // 默认的控制器层名称
    'DEFAULT_V_LAYER'       =>  'View', // 默认的视图层名称
    'DEFAULT_LANG'          =>  'zh-cn', // 默认语言
    'DEFAULT_THEME'         =>  '',	// 默认模板主题名称
    'DEFAULT_MODULE'        =>  'Home',  // 默认模块
    // 'DEFAULT_CONTROLLER'    =>  'Index', // 默认控制器名称
    // 'DEFAULT_ACTION'        =>  'index', // 默认操作名称

    /* Cookie设置 */
    'COOKIE_EXPIRE'         =>  3600*24,       // Cookie有效期
    'COOKIE_DOMAIN'         =>  '',      // Cookie有效域名
    'COOKIE_PATH'           =>  '/',     // Cookie路径
    'COOKIE_PREFIX'         =>  'rh',      // Cookie前缀 避免冲突
    'COOKIE_SECURE'         =>  true,   // Cookie安全传输
    'COOKIE_HTTPONLY'       =>  '',      // Cookie httponly设置

    // 邮件配置

    'MAIL_HOST'		 	=>'smtp.mxhichina.com',//smtp服务器的名称
    'MAIL_SMTPAUTH'  	=>TRUE, //启用smtp认证
    'MAIL_USERNAME' 	=>'hzgkfd@zyhzedu.com',//你的邮箱名
    'MAIL_FROM' 		=>'hzgkfd@zyhzedu.com',//发件人地址
    'MAIL_FROMNAME'		=>$NAME,//发件人姓名
    'MAIL_PASSWORD'		=>'Bohui123',//邮箱密码
    'MAIL_CHARSET'		=>'utf-8',//设置邮件编码
    'MAIL_ISHTML'		=>TRUE, // 是否HTML格式邮件

    'MAIL_NOTICE_BY_USERNAME' =>  "%s,你好:<br/> 您收到这封电子邮件是因为您申请了一个找回密码的请求。<br/> 
       假如这不是您本人所申请, 或者您曾持续收到这类的信件骚扰, 请您尽快联络管理员。<br/>        此邮件30分钟之内有效。您可以点击如下链接重新设置您的密码
		<a href='%s' target='_blank'>立即重置</a><br/>
		如果点击无效，请把下面的代码拷贝到浏览器的地址栏中：
       %s<br/> 
       在访问链接之后, 您可以重新设置新的密码。",

    'DB_BACKUP'				=> RUNTIME_PATH . 'Backup/',

);
return array_merge($config,$dbConfig);
