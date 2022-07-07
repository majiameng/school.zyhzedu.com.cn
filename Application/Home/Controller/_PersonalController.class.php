<?php 
namespace Home\Controller;
use Think\Controller;
class PersonalController extends PublicController{
	/*function __construct(){
		parent::__construct();
		if(!isset($_SESSION['user'])){
			$this->error('请先登录',U('Login/index'));		}
	}*/
	
	//新闻中心首页
	function index(){
		
		$user_id=$_SESSION['user']['id'];
		$model=M('User');
		$diqu=D('Order');
		$user=$model->where('id ='.$user_id)->find();
		$this->assign('user',$user);
		$sheng=$diqu->get_region($user['province']);
		$city=$diqu->get_region($user['city']);
		$district=$diqu->get_region($user['district']);
		$this->assign('sheng',$sheng);
		$this->assign('city',$city);
		$this->assign('district',$district);
		
		//渲染模版
		$this->display();
	
	}
	function save(){
		if(IS_AJAX){
			//print_r($_POST);die;
			
			$model=M('User');
			$jiaModel=D('Register');
			$diqu=D('Order');
				//print_r($_POST);
			$username= $_POST['username'];
			$password= $_POST['password'];
			$jiamipassword=$jiaModel->jiami($password);
			$tel= $_POST['tel'];
			$email= $_POST['email'];
			$nickname= $_POST['nickname'];
			$sex= $_POST['sex'];
			$user_id=$_POST['user_id'];
			$province=$_POST['province'];
			$city=$_POST['city'];
			$district=$_POST['district'];
			
			
			
			
			if($password!=''){
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
				$data['safe_password']=$score;
				$data['password']=$jiamipassword;
				//print_r($score);
				
				$pa=$model->where('id ='.$user_id)->data($data)->save();
				if($pa){
					if($username || $tel || $email || $nickname || $sex != ''){
						$data['username']=$username;
						$data['tel']=$tel;
						$data['email']=$email;
						$data['nickname']=$nickname;
						$data['sex']=$sex;
						$data['province']=$province;
						$data['city']=$city;
						$data['district']=$district;
						$userEdit = $model->where("id = $user_id")->save($data);
						if($userEdit){
							$this->ajaxReturn(array('error'=>1,'mes'=>$score));die;
						}else{
							$this->ajaxReturn(array('error'=>2));die;
						}
					}else{
						$this->ajaxReturn(array('error'=>1,'mes'=>$score));die;
					}
					
				}else{
					$this->ajaxReturn(array('error'=>0,'mes'=>$score));die;
				}
			}else{
				$data['username']=$username;
				$data['tel']=$tel;
				$data['email']=$email;
				$data['nickname']=$nickname;
				$data['sex']=$sex;
				$data['province']=$province;
				$data['city']=$city;
				$data['district']=$district;
				$userEdit = $model->where("id = $user_id")->save($data);
				if($userEdit){
					$this->ajaxReturn(array('error'=>1));die;
				}else{
					$this->ajaxReturn(array('error'=>2));die;
				}
				
			}
		}
	}
	
	

}



?>