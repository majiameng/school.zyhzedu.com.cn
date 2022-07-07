<?php 
namespace Home\Controller;
use Think\Controller;
class RegisterController extends PublicController{
	public $modelCity = NULL;
	function __construct(){
		parent::__construct();
		$this->modelCity = M('Region');
	}
	
	//注册首页
	function index(){
		
	
		$jiaModel = D('Register'); 
		
		//添加到User 数据库
		if(IS_POST){
			$username = I('post.username','','safe_replace');
			if(empty($username)) {$this->error('账号不能为空');}
			$password = I('post.userpwd','','safe_replace');
			if(empty($password)) {$this->error('密码不能为空');}
			$email    = I('post.email','','safe_replace'); 
			if(empty($email)) {$this->error('邮箱不能为空');}
			$mobile   = I('post.mobile','','safe_replace');
			if(empty($mobile)) {$this->error('手机不能为空');}
			//正则检证密码强度
			$score =0;
			if(preg_match("/[A-Z]+/", $password)){
				$score ++;
			}
			if(preg_match("/[0-9]+/",$password)){
              $score ++; 
            }
            if(preg_match("/[a-z]+/",$password)){
              $score ++; 
            }
		
			//写入数据库
			$model=M('User');
			$model->create();
			$data['username']= $username;
			//$data['password']=md5(md5($_POST['password']).'*&^');
			$data['password']=$jiaModel->jiami($password);
			
			$data['tel']	=	$mobile;
			$data['email']	=	$email;
			$data['safe_password']=$score;
            $data['register_time']=time();
			if($model->add($data)){
				$this->success('注册成功 请登录',U('login/index'));exit;
			}else{
				$this->error('注册失败请重试',U('login/index'));exit;
			}
		}
		redirect(U('Login/index'));	
	}

	//注册首页
	function index2(){
		
	
		$jiaModel = D('Register'); 
		
		//添加到User 数据库
		if(IS_AJAX){
			//正则检证密码强度
			$password=$_POST['password'];
			$score =0;
			if(preg_match("/[A-Z]+/", $password)){
				$score ++;
			}
			if(preg_match("/[0-9]+/",$password)){
              $score ++; 
            }
            if(preg_match("/[a-z]+/",$password)){
              $score ++; 
            }
		
		
			//写入数据库
			$model=M('User');
			$model->create();
			$data['username']=$_POST['username'];
			//$data['password']=md5(md5($_POST['password']).'*&^');
			$data['password']=$jiaModel->jiami($password);
			$data['primary_password']=$password;
			$data['tel']=$_POST['tel'];
			$data['email']=$_POST['email'];
			/*$data['province']=$_POST['sheng'];
			$data['city']=$_POST['city'];
			$data['district']=$_POST['area'];*/
			$data['safe_password']=$score;
            $data['rem_password']=0;
			if($model->add($data)){
				$this->ajaxReturn(1);die;
			}else{
				$this->ajaxReturn(0);die;
			}
		}
		
		//渲染模版
		$this->display();
	
	}
	//刷新验证码
	function yzm(){
	    if(IS_AJAX){
            $yzm = new\Think\Verify();
            $code = I('request.verifycode','','trim');
            if($yzm->check($code)){
                echo 'true';
            }
            else{
                echo 'false';
            }
        }
	}
	//打开验证码
	function code(){
		$config =    array(
				'imageW'=>100,
				'imageH'=>30,
				'length'=>3,
				'useNoise'=>false,
				'fontSize'=>16,
		);
		ob_end_clean();
		$Verify = new \Think\Verify($config);
		$Verify->entry();
	}
	
    
	//市区js传值
	function cityCheck(){
		if(IS_AJAX){
			$modelCity=M('Region');
			$regSheng=$modelCity->where('parent_id ='.$_POST['sheng'])->select();
			$regArea=$modelCity->where('parent_id='.$regSheng[0]['region_id'])->select();
			$data=array();
			$data['regSheng']=$regSheng;
			$data['regArea']=$regArea;
			echo json_encode($data);die; 
			//print_r($regSheng);	
			//print_r($regArea);die;
			
		}		
	}
	/* function areaCheck(){
		if(IS_AJAX){
			$modelCity=M('Region');
			$regCity=$modelCity->where('parent_id ='.$_POST['city'])->select();
			echo json_encode($regCity);die;
			//print_r($regCity);
		}
	} */
	

		
	
	
}