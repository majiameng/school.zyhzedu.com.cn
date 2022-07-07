<?php
namespace Home\Controller;
use Think\Controller;
class PublicController extends Controller {
    public function __construct(){
    	parent::__construct();
    	//默认中国时区
		$allow_action = array('logout');	
		$action = strtolower(ACTION_NAME);		
		if(in_array($action,$allow_action)) return true;
    	
		$this->checkUserStatus();
		$this->checkResumeStatus(); //判断是否报名
		$default = array('login','register');
		$deny	 = array('user');
		$controller  = strtolower(CONTROLLER_NAME);
		$user = session('user');
		if(!empty($user) && in_array($controller,$default)) {
			redirect(U('User/index'));
		}
		if(empty($user) && in_array($controller,$deny)) {
			redirect(U('login/index'));
		}		
		$this->assign('version',D('Common/Recruit')->getVersion());
   }
   public function checkResumeStatus () {
	   if(empty(session('user'))) return false; 	   
	   $info = D('Resume')->getBaseInfo(session('user.id'));
	   if(empty($info)) session('user.state',0);
	   else session('user.state',1);
   }
   
   public function checkUserStatus() {
	   $user = session('user');
	   if(!empty($user)) return true;	   
	   $info = cookie('_u');	
	   if(empty($info)) return false;	
	   $info = explode('++',base64_decode($info));	
	   $user = $this->getUserInfo(array('username'=>$info[0],'pwd'=>$info[1]));
	   if(empty($user)) return false;
	   session('user',$user);
	   cookie('_u', base64_encode($user['username'].'++'.$user['password'] ),3600 * 24 * 7);
	   return true;
	   
   }
   public function getUserInfo($data) {
	   if(isset($data['username'])) $map['username'] = $data['username'];
	   if(isset($data['pwd'])) $map['password'] = $data['pwd'];
	   if(isset($data['id'])) $map['id'] = $data['id'];
	   if(empty($data)) return false;
	   $user = D('User')->where($map)->find();
	   if(empty($user)) return false;
	   return $user;
   }
   	
	public function checkResumeCard(){
		if(IS_AJAX){
            // echo 'true';exit;
        	$card = trim(I('post.idCard','','safe_replace'));			
        	if(empty($card)) {
        		echo 'false';exit;
        	}	
			$map['card'] = $card;
			$map['is_lock'] = 0; //未锁定账号
			$map['userid'] = array('neq',session('user.id'));
			$user= D('Resume')->where($map)->getField('id');	
        	if(empty($user)) {
        		echo 'true';exit;
        	}
        	echo 'false';exit;
        }		
	}

	public function checkValidCard(){
		if(IS_AJAX){
			echo 'true';exit;
        	$card = trim(I('post.username','','safe_replace'));		
        	if(empty($card)) {
        		echo 'false';exit;
        	}	
			$map['username'] = $card;
			$user= D('User')->where($map)->getField('id');
        	if(empty($user)) {
        		echo 'true';exit;
        	}
        	echo 'false';exit;
        }		
	}
	
	
	/**
	 * 判断报名时间
	 */
	public function checkSignDate(){
		$config = D('Common/Cache')->getConfig();  //获取配置
		$config['register_end_date'] = format_date($config['register_end_date']);
		if(time() >= strtotime($config['register_end_date'])) return false;		
		return true;
	}

  
}