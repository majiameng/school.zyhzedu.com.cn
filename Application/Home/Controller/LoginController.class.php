<?php
namespace Home\Controller;
class LoginController extends PublicController
{

    //登录首页
    public function index()
    {  
        $reg = preg_match('/login/i', $_SERVER['HTTP_REFERER']);
        $reg2 = preg_match('/register/i', $_SERVER['HTTP_REFERER']);
        
        if ($reg === 0 && $reg2 === 0) { //不是登录页面,所以存到session里
            session('url', $_SERVER['HTTP_REFERER']);
        }
        $this->display();
    }

    public function login() {
        if (IS_AJAX) {
			
            $username = I('post.username','','safe_replace');
            $userpwd  = I('post.userpwd','','safe_replace');
            $remember  = I('post.remember',0,'intval');
            $code  = I('post.code','','trim');
            if(empty($username)) $this->error('请填写账号');
            if(empty($userpwd)) $this->error('请输入密码');
			$yzm = new \Think\Verify();
			if(!$yzm->check($code)){
				$this->error(array('code'=>2));
			}				
            $password = password($userpwd);  		
            $model = D('User');
            $map['username'] = $username;
            $map['password'] = $password;
            $user = $model->where($map)->field('password,username,id,email,tel')->find();     
            if(empty($user)) $this->error('账号或密码错误');
           
            session('user',$user);            
            if($remember==1) {	
                cookie('_u', base64_encode($user['username'].'++'.$user['password'] ),3600 * 24 * 7);
            }			
			$url = !empty(session('url'))?session('url'):U('User/index');
            $this->success(' ',$url);
        }
        redirect(U('login/index'));  
    }
	
	function register(){		
		//添加到User 数据库
		if(IS_AJAX){
			// $register_time = L('REGISTER_END_TIME');
			if(!$this->checkSignDate()) {	//检查报名截止时间
				$this->error('报名已经截止');exit;
			}
			$username = I('post.username','','safe_replace');
			if(empty($username)) {$this->error('身份证不能为空');}
			$password = I('post.userpwd','','safe_replace');
			if(empty($password)) {$this->error('密码不能为空');}
			$email    = I('post.email','','safe_replace'); 
			if(empty($email)) {$this->error('邮箱不能为空');}
			$code   = I('post.verifycode','','trim');
			if(empty($code)) {$this->error('验证码不能为空');}
			$yzm = new \Think\Verify();
			if(!$yzm->check($code)){
				$msg = array('code'=>2); 
				$this->error($msg);exit;
			}	
			/*
			$mobile   = I('post.mobile','','safe_replace');
			if(empty($mobile)) {$this->error('手机不能为空');}
			 $data['tel']	=	$mobile;
			*/
				
			$model = D('User');			
			$data['username']= $username;		
			$data['password']= password($password);
			$data['email']	=	$email;
            $data['register_time']=time();		
			$model->create();
			if($model->add($data)){
				$this->success('注册成功 请登录',U('login/index'));
			}else{
				$this->error('注册失败 请重试');
			}
			exit;
		}
		$this->display();
	}
	
    /* 忘记密码*/
    function forget()
    {	
        $this->display();
    }

	
	
    //通过账户名找到邮箱
    function findName(){
        if(IS_AJAX){
            $username = I('post.username','','safe_replace');
			if(empty($username)) {
				$this->error('请填写账号');exit; //失败
			}
			
			if(empty($code)) {$this->error('验证码不能为空');}
			$yzm = new \Think\Verify();
			if(!$yzm->check($code)){
				$msg = array('code'=>2); 
				$this->error($msg);exit;
			}	
			
            $model = M('user');			
            $str = $model->where(array('username'=>$username))->find();
            if(empty($str)){
                $this->error('账号不存在');exit; //失败
            }else{
                $userid = $str['id'];
                $key = rand_code();				
                $username = $str['username'];
				$url = U('login/findpassword',array('key'=>$key,'id'=>$userid),true,true);
                $emailContent= sprintf(C('MAIL_NOTICE_BY_USERNAME'),$username,$url,$url);	
				$emailContent = htmlspecialchars_decode($emailContent);
				
				$rs = user_send_email($str['email'],$emailContent,1);
                
                if(!$rs){
                    $this->error('邮件发送失败 请联系客服');exit; //失败
                }else{					
					$data['time'] 	=  time();
					$data['code'] 	=  $key;
					$data['userid'] = $userid;
					S('findpassword',$data,3600*30);
                    $this->success('邮件发送成功 请注意查收');exit; //失败
                }

            }
        }
    }
    //通过邮箱直接发送
    function sendEmail(){
        if(IS_AJAX){
            $username 	= I('post.username','','safe_replace');			
            $email 		= I('post.email','','safe_replace');			
            $code 		= I('post.verifycode','','trim');
			if(empty($username)) $this->error("请输入账号");
			if(empty($email)) $this->error("请输入账号");					
			if(empty($code)) $this->error('请输入验证码');
			$yzm = new \Think\Verify();
			if(!$yzm->check($code)){
				$msg = array('code'=>2); 
				$this->error($msg);exit;
			}
			
            $where['username'] = $username;	
            $user = D('User')->where($where)->find();
            if(empty($user)){
                $this->error('账号不存在');exit; //失败
            }			
			if($user['email'] != $email){
                $this->error('邮箱错误');exit; //失败
            }
			
			$userid = $user['id'];
			$key = rand_code();				
			$username = $user['username'];
			$url = U('login/findpassword',array('key'=>$key,'id'=>$userid),true,true);
			$emailContent= sprintf(C('MAIL_NOTICE_BY_USERNAME'),$username,$url,$url);	
			$emailContent = htmlspecialchars_decode($emailContent);			
			$rs = user_send_email($user['email'],$emailContent,1);			
			if(!$rs){
				$this->error('邮件发送失败 请联系客服');exit; //失败
			}else{
				$data['time'] 	=  time();
				$data['code']	=  $key;
				$data['userid'] = $userid;
				S('findpassword',$data,3600*30);
				$this->success('发送成功');exit; //失败
			}
        }
		redirect(U('login/index'));
    }
    //重新写密码页面 
    function findPassword(){
		$forget = S('findpassword');
        if(IS_AJAX){
            $userid = I('post.id',0,'intval');
			if(empty($forget) || $userid != $forget['userid']){
				$this->error('您没有进行找回密码的操作',U('login/index'));
			}
            if( time() > 30*60*60 +$forget['time']) {
				$this->error('链接超时，请重新找回密码',U('login/index'));
            }
			$password = I('post.userpwd','','safe_replace');
			$code = I('post.verifycode','','safe_replace');
			if(empty($password)) $this->error('请输入密码');
			if(empty($code)) $this->error('请输入验证码');
			$yzm = new \Think\Verify();
			if(!$yzm->check($code)){
				$msg = array('code'=>2); 
				$this->error($msg);exit;
			}			
			$user =  D('User')->where(array('id'=>$userid))->getField('password');
			if(empty($user)) {
				$this->error('该账号不存在',U('login/index'));
			}
			$data['password'] = password($password);
			if($data['password'] == $user) {
				$this->error('新密码不能与之前密码相同');
			}	
			$st =  D('User')->where(array('id'=>$userid))->save($data);
			if($st){
				S('findpassword',null);
				$this->success('密码修改成功');
			}
			$this->error('密码修改失败 请重试');
        }
		
		if(isset($_GET['id']) && isset($_GET['key'])) {
			
			$userid = I('get.id',0,'intval');
			$code = I('get.key',0,'intval');
			
			if(empty($forget) || $userid != $forget['userid'] || empty($code) ){
				$this->error('您没有进行找回密码的操作',U('login/index'));
			}
			$user = D('User')->where(array('id'=>$userid))->getField('id');		
			if(empty($user)){				
				$this->error('您没有进行找回密码的操作',U('login/index'));
			}
			
			if(time() > 30*60*60 +$forget['time']  || $code != $forget['code']) {
				
				$this->error('链接已失效请重新找回密码',U('login/index'));
			}
			
			$this->assign('userid',$userid)->display();exit;
        }
		$this->error('您没有进行找回密码的操作',U('login/index'));        
    }
	
	function userCheck(){
	  	$model=M('user');
	  	if(IS_AJAX){
	  		$username = I('post.username','','safe_replace');
	  		if(empty($username)) {
	  			echo 'false';exit;
	  		}
	  		if($user=$model->where(array('username'=>$username))->getField('id')){
  				echo 'false';exit;
  			}
  			echo "true";exit;
	  	}
	}
	 
	 
    function emailCheck(){        
        if(IS_AJAX){
            if(!empty($_POST)){
            	$model=M('user');
            	$email = trim(I('post.email','','safe_replace'));
                if($user=$model->where(array('email'=>$email))->find()){  //有重复
                   echo 'false';exit;
                }else{
                     echo 'true';exit;
                }
            }else{
                echo 'false';exit;
            }
        }
    }
		
	public function checkMobile(){
        if(IS_AJAX){
        	$mobile = trim(I('post.mobile','','safe_replace'));
        	if(empty($mobile)) {
        		echo 'false';exit;
        	}
        	$user= D('User')->where(array('tel'=>$mobile))->getField('id');
        	if(empty($user)) {
        		echo 'true';exit;
        	}
        	echo 'false';exit;
        }
    }

	
	//打开验证码
	function code(){
		$config =    array(
				'imageW'=>100,
				'imageH'=>44,
				'length'=>3,
				'useNoise'=>false,
				'fontSize'=>17,
		);
		ob_end_clean();
		$Verify = new \Think\Verify($config);
		$Verify->entry();
	}
	
	function codeCheck(){
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
/*
	public function cc(){		
		$username = I('get.username','','safe_replace');
		$map['username'] = $username;
		
		$user = D('user')->where($map)->field('password,username,id,email,tel')->find();
		if(empty($user)) $this->error('账号或密码错误');
		session('user',$user); 
		echo 44;exit;
	}
*/
}
?>