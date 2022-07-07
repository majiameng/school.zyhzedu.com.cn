<?php
return array(	
    'DB_DEBUG'				=>  true, // 开启调试模式 记录SQL日志
			
	/* 错误设置 */
    'SHOW_PAGE_TRACE'       =>  false,	
    'SHOW_ERROR_MSG'        =>  true,    // 显示错误信息
    'ERROR_MESSAGE'         =>  '页面错误！请稍后再试～',//错误显示信息,非调试模式有效
    'ERROR_PAGE'            =>  '',	// 错误定向页面
    'TRACE_MAX_RECORD'      =>  100,    // 每个级别的错误信息 最大记录数
	
	/* 日志设置 */
	'LOG_RECORD'            =>  true,  // 进行日志记录
    'LOG_EXCEPTION_RECORD'  =>  true,    // 是否记录异常信息日志
    'LOG_LEVEL'             =>  'EMERG,ALERT,CRIT,ERR',  // 允许记录的日志级别 
    // 'LOG_LEVEL'             =>  'EMERG,ALERT,CRIT,ERR,WARN,NOTIC,DEBUG',  // 允许记录的日志级别 
 
    'DB_FIELDS_CACHE'   =>  true,        // 启用字段缓存
	
	'TMPL_CACHE_ON'         =>  false,        // 是否开启模板编译缓存,设为false则每次都会重新编译
	
    'URL_CASE_INSENSITIVE'  =>  false,  // URL区分大小写
	'TMPL_STRIP_SPACE'      =>  false,       // 是否去除模板文件里面的html空格与换行
);