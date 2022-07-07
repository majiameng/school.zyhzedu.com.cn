<?php 
namespace Common\Model;
use Think\Model;
class CacheModel extends Model{
	// protected $tableName = 'User_address';
	public function __construct() {
       $this->autoCheckFields = false;
        parent::__construct();
    }
	
	/*
	** 招生途径
	*/ 
	function setApplyTypeCache(){
		
		$list = D('Projects_from')->order('id desc')->select();
		$data = array();
		array_walk($list,function($item,$key) use(&$data){
			$data[$item['id']] = $item;
		});
		unset($list,$data);
		F('_common/applyType',$data);
		return $data;
	}			
	
	/*
	** 招生班级
	*/ 
	function saveClassCache(){		
		$list = D('Projects')->where('is_del=0')->order('id desc')->select();		
		$data = array();
		array_walk($list,function($item,$key) use(&$data){
			unset($item['content']);
			$data[$item['id']] = $item;
		});		
		F('_common/class',$data);
		unset($list,$data);
		return $data;
	}
	
	/**
	** 获取网站配置缓存
	*/ 
	function getConfig(){		
		$config = F('_common/web_config');
		if(empty($config)) $config = $this->saveConfig();
		return $config;
	}

	/**
	** 保存网站配置缓存
	*/ 
	function saveConfig(){		
		$config = D('System')->order('id desc')->find();
		F('_common/web_config',$config);
		return $config;
	}
	
}

?>