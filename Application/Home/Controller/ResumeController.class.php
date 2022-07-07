<?php
namespace Home\Controller;
use Think\Controller;
class ResumeController extends PublicController{
	
	//新闻中心首页
	function index(){
		//轮播图
		$modelBanner=M('Banner');
		$condition['category'] = 'product';
		$banner=$modelBanner->where($condition)->find();
		$this->assign('banner',$banner);
		
		//print_r($banner['thumb']);
        $rec = M('Recruit')->where(array('is_del'=>0))->select();
		
        $this->assign('rec',$rec);
	
		//渲染模版
		$this->display();
	
	}
	

	public function upp(){
        if(IS_AJAX) {
			$re_id=$_POST['recid'];//调取recruit 的id

			$config = array('exts'=>'doc,docx,zip,rar','rootPath'=>'attachment');
			$info = D('Common/upload')->upload($config);		
			if(isset($info['msg'])) {				
				$this->error("请上传2M以内的zip,rar,doc,docx文件");exit;
			}	

			$model = M('Resume');
			$data['content'] = $info['file']['file'];
			$data['recruit_id'] = $re_id;
			$data['is_del'] = 0;
			$userid = $model->add($data);
			//回传信息
			if($userid > 0) {
				$this->success('文件上传成功');exit;
			}
			$this->error('上传失败 请重试');exit;
		}

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
	}
}
?>