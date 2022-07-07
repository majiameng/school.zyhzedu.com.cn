<?php
return array(
	//'配置项'=>'配置值'
	
	// 'DEFAULT_MODULE'        =>  'Home',  // 默认模块
    'DEFAULT_CONTROLLER'    =>  'Recruit', // 默认控制器名称
    'DEFAULT_ACTION'        =>  'index', // 默认操作名称
	//增加一个模板替换项
	'TMPL_PARSE_STRING'  =>array(
		'__COMMON__' => __ROOT__.'/Public/admin' // 设置模板css，js所在路径 
	),
	'LANG_SWITCH_ON'   		=>  true,    //开启语言包功能        
    'LANG_AUTO_DETECT'      =>  false, // 自动侦测语言


		
);