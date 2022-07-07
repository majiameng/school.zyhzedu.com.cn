<?php
namespace Admin2\Controller;
use Think\Controller;
class SystemController extends AdminBaseController {	
	function index(){
		$model=M('System');
		if(IS_AJAX){
			/*
			$data['name'] 		= I('post.name','', 'trim');
			$data['address'] 	= I('post.address','','trim');//公司地址	
			$data['email'] 		= I('post.email','','trim');//邮件	
			$data['logo'] 		= I('post.logo','','trim');//logo	
			$data['erwei'] 		= I('post.erwei','','trim');//二维码	
			$data['tel'] 		= I('post.tel','','trim');//电话	
			*/			
			$data['exam_year'] 			= I('post.exam_year','','trim');//报考年份	
			$data['confirm_start_date'] = I('post.confirm_start_date','','trim');//确认考试开始时间	
			$data['confirm_end_date'] 	= I('post.confirm_end_date','','trim');//确认考试截止时间	
			$data['print_start_date'] 	= I('post.print_start_date','','trim');//打印开始时间	
			$data['print_end_date'] 	= I('post.print_end_date','','trim');//打印截止时间	
			$data['register_end_date'] 	= I('post.register_end_date','','trim');//报名截止时间	
			$data['exam_date'] 	= I('post.exam_date','','trim');//考试时间	
			
			$data['exam_address']= I('post.exam_address','','trim');//考试地点
			$id = I('post.id',0,'intval');
			$map = array();
			$map['id'] = $id;
			$info = $model->where($map)->field('id')->find();
			if(!empty($info)) {
				$res = $model->where($map)->save($data);
			}else{
				$res = $model->data($data)->add();	
			}	
			D('Common/Cache')->saveConfig();  //更新配置缓存
    		if($res!==false) $this->success('操作成功！');
    		$this->error('操作失败！');
			exit;
		}		
		$sys=$model->find();
		$this->assign('sys',$sys);
		$this->display();
	}
	
	
}