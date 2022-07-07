<?php
namespace Admin2\Controller;
use Think\Controller;
class IndexController extends AdminBaseController {
    public function index(){		
		redirect(U('Recruit/index'));exit;
		$this->display();
    }
	
	/*
	** 更新缓存
	*/
	public function clearCache(){ 
		D('Common/Cache')->saveConfig();  //更新网站配置
		D('Admin2/Region')->cacheAll();  //更新地区缓存及地区js
		$recruit = D('Common/Recruit');
		$recruit->saveJobCache();  	  //更新岗位缓存
		$recruit->saveCompanyCache();  //更新招聘科室缓存
		$recruit->saveRecuitCache();  //更新招聘信息及js缓存
		$recruit->updateVersion();   //更新版本信息
		D('Common/Redis')->clearCache();   //清除redis缓存	
		$this->success('更新成功');
	}
	
	/*
	** 清空数据 user及resume相关表
	*/
	public function clearData(){ 	
		D('Common/Backup')->backall();  
		D('Common/Redis')->clearData();		//清空数据表
		D('Common/Redis')->clearCache();   //清除redis缓存	
		$this->success('清除成功');
	}
}