<?php
namespace Home\Controller;
use Think\Controller;
class ContactController extends PublicController{
	
	//新闻中心首页
	function index(){
		//轮播图
		$modelBanner=M('Banner');
		$condition['category'] = 'product';
		$banner=$modelBanner->where($condition)->find();
		$this->assign('banner',$banner);
		//print_r($banner['thumb']);
		$model=M('System');
		$system=$model->where('id=1')->find();
		$this->assign('system',$system);
		
		//渲染模版
		$this->display();
	
	}
	
	public function yzm(){
	
			if(IS_AJAX){
				//echo (123);
				$yzm = new \Think\Verify();
					
				if($yzm->check($_POST['yzm'])){
					if($yzm){
						echo 1;
					}else{
						echo 0;
					}
				
				}
		
			}
	}
	public function addMessage(){
		if(IS_AJAX){
			//echo(123);
			if(!empty($_POST)){
				$model=M('message');
				$model->create();
					$data['username']=$_POST['username'];
					$data['address']=$_POST['address'];
					$data['tel']=$_POST['tel'];
					$data['email']=$_POST['email'];
					$data['content']=$_POST['content'];
				if($model->add($data)){
					$this->ajaxReturn(1);	
				}else{
					$this->ajaxReturn(2);
				}
			}
		}
	}
	function code(){
		$config =    array(
				'imageW'=>120,
				'imageH'=>30,
				'length'=>2,
				'useNoise'=>false,
				'fontSize'=>16,
		);
		$Verify = new \Think\Verify($config);
		$Verify->entry();
		
		
		
		
		
		//渲染模版
		//$this->display();
	}
	
	
	
}



?>