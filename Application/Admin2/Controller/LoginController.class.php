<?php
namespace Admin2\Controller;
use Think\Controller;
class LoginController extends Controller {
	
	public function login(){
		
		if(IS_POST){
			$this->username = I('post.username','','htmlspecialchars');//$_POST['username']
			if($_POST['password']!=$_COOKIE['password']){
				$this->password = setPassword(I('post.password','','htmlspecialchars'));
			}else{
				$this->password=I('post.password','','htmlspecialchars');
			}			
			//密码加密方式 在/Common模块/Common文件夹/function.php
			$result = $this->checkUser();
			if(!empty($result)){
				session('admin',$result); //thinkphp 的session用法
				$model = M('Admin');//实例化一个表模型
				//登录成功后保存登录IP，时间及登录次数
				$result['last_ip']=get_client_ip();//获取客户端IP
				$result['last_time']=time();
				$model->save($result);
				$model->where('id=1')->setInc('login_times',1); // 登录次数加1
				
				$yzm = new \Think\Verify();
				if(!$yzm->check($_POST['code'])){ //用户输入的验证码传进去
					$this->error('验证码错误');
				}	
				// $forward = $result['role_id']==1?U('Index/index'):U('Recruit/index');
				$forward = U('Recruit/index');
				$this->success('登陆成功',$forward,1);
			}else{
				$this->error('登陆失败',U('Login/login'),1);
			}
		}else{
			$this->display();
		}
	}
	
	//判断用户名密码是否正确，我们用私有方法提高安全性
	private function checkUser(){
		$model = M('Admin');//实例化一个表模型
		$user = $model->where(array('username'=>$this->username,'status'=>1))->find();
		if($user){
			if($this->password != $user['password']){
				return false;
			}else{
				// $region = F('_common/region');
				// $user['city_name'] = $region[$user['city']]['region_name'];

				$region = D('region')->getChild(10);				
				$user['city_name'] = $region[$user['city']]['name'];	
				if(isset($_POST['remember'])){
					setcookie('username',$user['username'],time()+3600);
					
					setcookie('password',$this->password,time()+3600);
					setcookie('remember',1,time()+3600);
					return $user;
				}else{
					setcookie('username','',time()-3600);
					setcookie('password','',time()-3600);
					setcookie('remember',0,time()-3600);
					return $user;
				}
				
			}
		}else{
			return false;
		}
	}

	public function logout(){

		//unset($_SESSION['admin']);
		session('admin',null);//清除session

		$this->redirect('Login/login', array(), 1, '亲，退出成功...');

	}
		function getCode(){
		$config =	array(
        'seKey'     =>  'ThinkPHP.CN',   // 验证码加密密钥
        'codeSet'   =>  '0123456789',             // 验证码字符集合
        'expire'    =>  1800,            // 验证码过期时间（s）
        'useZh'     =>  false,           // 使用中文验证码 
        'useImgBg'  =>  false,           // 使用背景图片 
        'fontSize'  =>  20,              // 验证码字体大小(px)
        'useCurve'  =>  false,            // 是否画混淆曲线
        'useNoise'  =>  false,            // 是否添加杂点	
        'imageH'    =>  0,               // 验证码图片高度
        'imageW'    =>  0,               // 验证码图片宽度
        'length'    =>  3,               // 验证码位数
       
        );
	
	$Verify = new \Think\Verify($config);
	$Verify->entry();
	
	}
	
	
	
}