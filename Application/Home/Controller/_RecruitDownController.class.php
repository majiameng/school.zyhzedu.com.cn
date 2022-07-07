<?php
namespace Home\Controller;
use Think\Controller;
class RecruitDownController extends PublicController{
	
	//新闻中心首页
	function index(){
		//轮播图
		$modelBanner=M('Banner');
		$condition['category'] = 'product';
		$banner=$modelBanner->where($condition)->find();
		$this->assign('banner',$banner);
		//print_r($banner['thumb']);
        $model = M('recruit');
        $rec=$model->where('is_del=0')->select();
        $this->assign('rec',$rec);
		
		//渲染模版
		$this->display();
	
	}

	public function upp(){


        if(IS_AJAX) {

            

	    $re_id=$_POST['recid'];//调取recruit 的id


        //保存文件
        /*$upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg', 'txt');// 设置附件上传类型
        $upload->savePath  =      ''; // 设置附件上传目录
        $upload->rootPath = './Public/Uploads/';
        $upload->saveName = 'myfun';
        $upload->saveName = $re_id.'_'.time().'_'.mt_rand(1000,9000);
        $info   =   $upload->upload();*/



            $filename  = '';
            if($_FILES['file']['name']!=='' && $_FILES['file']['error']==0){  //判断提交图片的名字不为 并且 错误是0
                $houzhui = strrchr($_FILES['file']['name'],'.'); //提取提交的图片的后缀
                //echo $houzhui;die;
                $filename = './Public/uploads/'.$re_id.'_'.date('Ymd').'_'.mt_rand(100,999).$houzhui; //保存图片的新路径
                //echo $filename;die;

                $info = move_uploaded_file($_FILES['file']['tmp_name'],$filename);  //存储图片

                }

       // print_r($_FILES);
        //print_r($_FILES['file']['tmp_name']);die;

        //保存到数据库
        $model = M('Recruit_down');
        $data['content'] = $filename;
		$data['recruit_id'] = $re_id;
		$data['is_del'] = 0;

		

		
        
		$User = $model->add($data);


        //回传信息
        if(!$info && !$User) {// 上传错误提示错误信息
            echo 1;
        }else {// 上传成功
            echo 2;
        }
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
		
		
		
		
		
		//渲染模版
		//$this->display();
	}
	
	
	
}



?>