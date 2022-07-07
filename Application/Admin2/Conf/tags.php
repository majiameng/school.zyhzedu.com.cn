<?php
    return array(
        // 添加下面一行定义即可
        'app_begin'        => array('Common\Behavior\CheckLangBehavior'),
      
        'LANG_LIST'        => 'zh-cn',         // 允许切换的语言列表 用逗号分隔
        //'VAR_LANGUAGE'     => 'l',          // 默认语言切换变量，注意到上面发的链接了么，l=zh-cn，就是在这里定义l这个变量
    );