<?php 
namespace Home\Controller;
use Think\Controller;
class BrandController extends PublicController{
	
	//品牌介绍首页
	function index(){
		$brand=M('About');
		$about=$brand->where('id=5')->find();
		$this->assign('about',$about);
		$about2=$brand->where('id=6')->find();
		$this->assign('about2',$about2);
		$about3=$brand->where('id=1')->find();
		$this->assign('about3',$about3);
		
		
		//轮播图
        $modelBanner=M('Banner');
        $condition['category'] = 'brand';
        $banner=$modelBanner->where($condition)->find();
        $this->assign('banner',$banner);
        //print_r($banner['thumb']);
     
        
        
        //轮播图页内
        $modelBanner=M('Banner');
        $condition['category'] = 'brandMid';
        $banner2=$modelBanner->where($condition)->select();
        $this->assign('banner2',$banner2);
		
		//渲染模版
		$this->display();
	
	}
	
	
	
	

}



?>