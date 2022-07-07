<?php 
namespace Home\Controller;
use Think\Controller;
class UserController extends PublicController{
	function __construct(){
		parent::__construct();
		$user = session('user');
		if(empty($user)){
			$this->error('请先登录',U('login/index'));
		}
	}
	
	//个人中心
	function index(){
		$userid = session('user.id');
//        $userid = 224;

        $resume = D('Resume')->where(array('userid'=>$userid))->find();
		$achievement = D('ResumeAchievement')->where(array('user_id'=>$userid))->find();
//        if(empty($achievement)){
//            $achievement = [
//                'written_achievement'=>0,
//                'interview_achievement'=>0,
//                'achievement'=>0,
//            ];
//        }

		$this->assign('resume',$resume)
            ->assign('achievement',$achievement)
			->display();
	}


    //个人中心
    function user(){
        $userid = session('user.id');
        $user = D('User')->where(array('id'=>$userid))->find();
        $this->assign('user',$user)
            ->display();
    }

	//考试服务费
	function pay(){
		$userid = session('user.id');
		$user = D('User')->where(array('id'=>$userid))->find();
        $resume = D('Resume')->where(array('userid'=>$userid))->find();
        if(empty($resume)){
            $this->error("请您先提交报名信息！");
        }
        if($resume['status'] == 5){
            $this->success("您是免试的，不需要支付考试服务费！");exit();
        }
        if($resume['is_pay'] == 1){
            $this->success("您已支付成功，请及时打印准考证！","/index.php?m=Home&c=Review&a=index");exit();
        }

        if($resume['status'] == 2){
            $this->error("您报名信息正在审核，不需要支付考试服务费！");
        }
        if($resume['status'] != 1){
            $this->error("您报名信息正在审核，请先查看审核结果！");
        }
        if(strtotime('2022-07-31 12:00:00') < NOW_TIME){
            $this->error("报名时间已过，不能进行缴费（如有疑问请联系管理员）！");
        }

        $system = D('System')->where(array('id'=>1))->find();
        $total_amount = $system['pay_total_amount'] ?? 1;// 金额（元）
        $qrUrl = $this->getPay($userid,$total_amount*100);
        $result = [
            'qrUrl'=>$qrUrl,
            'total_amount'=>$total_amount,
        ];

        $this
            ->assign('user',$user)
            ->assign('result',$result)
			->display();
	}

    public $api_url = "http://pay.zyhzedu.com.cn/index.php";

    /**
     * @param $userid
     * @param int $total_amount 金额（角）
     * @return string
     * @throws \Exception
     */
    public function getPay($userid,$total_amount){
        $url = $this->api_url."/UnionPay/index";
        $params = [
            'user_id'=>$userid,
            'total_amount'=>$total_amount,
        ];
        $result = $this->httpPost($url,$params);
        $result = json_decode($result,true);
        if($result['code']!=200){
            $this->error("调用支付系统出错：".$result['msg']);
        }

        $qrUrl = $this->api_url."/UnionPay/qrCode?data=".urlencode($result['data']['qr_code']);
        return $qrUrl;
    }

    /**
     * 获取支付状态
     */
    public function getPayStatus(){
        $userid = session('user.id');
        $resume = D('Resume')->where(array('userid'=>$userid))->find();
        if(empty($resume)){
            $this->error("请您先提交报名信息！");
        }
        $result = [
            'is_pay'=>$resume['is_pay'],
        ];
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($result);exit();
    }


    /**
     * Description: POST 请求
     * Author: JiaMeng <666@majiameng.com>
     * @param string $url 请求链接
     * @param array $param 请求参数
     * @param array $httpHeaders 添加请求头
     * @param string $proxy 代理ip
     * @param int $http_code 相应正确的状态码
     * @return mixed
     * @throws \Exception
     */
    private function httpPost($url, $param = array(), $httpHeaders = array(),$proxy='', $http_code = 200)
    {
        /** 参数检测,object或者array进行http_build_query */
        if(!empty($param) && is_array($param)){
            $flag = false;
            foreach ($param as $value){
                //判断参数是否是一个对象 或者 是一个数组
                if(is_array($value) || (is_string($value) && is_object($value))){
                    $flag = true;
                    break;
                }
            }
            if($flag == true){
                $param = http_build_query($param);
            }
        }

        $curl = curl_init();

        /** 设置请求链接 */
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        /** 设置请求参数 */
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $param);

        /** 设置请求headers */
        if(empty($httpHeaders)){
            $httpHeaders = array(
                "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.66 Safari/537.36"
            );
        }
        if (is_array($httpHeaders)){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $httpHeaders);
        }
        /** gzip压缩 */
        curl_setopt($curl, CURLOPT_ACCEPT_ENCODING, "gzip,deflate");

        /** 不验证https证书和hosts */
        if (stripos($url, "https://") !== FALSE) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        }

        /** http代理 */
        if(!empty($proxy)){
            $proxy = explode(':',$proxy);
            curl_setopt($curl, CURLOPT_PROXYAUTH, CURLAUTH_BASIC); //代理认证模式
            curl_setopt($curl, CURLOPT_PROXY, "$proxy[0]"); //代理服务器地址
            curl_setopt($curl, CURLOPT_PROXYPORT,$proxy[1]); //代理服务器端口
        }

        /** 请求 */
        $content = curl_exec($curl);
        /** 获取请求信息 */
        $info = curl_getinfo($curl);
        /** 关闭请求资源 */
        curl_close($curl);

        /** 验证网络请求状态 */
        return $content;
    }

    function editper(){
	    if(!empty($_POST)){
            // $data['nickname'] = I('post.nickname','','safe_replace');
            $data['tel'] = I('post.tel','','safe_replace');
            $data['email'] = I('post.email','','safe_replace');
	        $model =D('User');
            $userid = session('user.id');
			$r =$model->where(array('id'=>$userid))->save($data);
            if($r){
                $this->success('修改成功',U('user/index'));
            }else{
                $this->success('修改成功',U('user/index'));
            }

        }else{
            $this->error('内容不能为空',U('user/index'));
        }

    }
	
	public function pwdManage(){
		if(IS_AJAX){
			$userpwd = I('post.userpwd','','safe_replace');
			$newpwd = I('post.newpwd','','safe_replace');
			if(empty($userpwd) || strlen($userpwd)<6) {
				$this->error('原密码填写有误');
			}
			if(empty($newpwd) || strlen($newpwd)<6) {
				$this->error('新密码填写有误');
			}
			$userid = session('user.id');
			$user = D('User');
			$password = $user->where(array('id'=>$userid))->getField('password');    
			$userpwd = password($userpwd);
			if($password != $userpwd) {
				$this->error('原密码错误');
			}		
			$r = $user->where(array('id'=>$userid))->save(array('password'=>password($newpwd)));
			$this->success('密码修改成功');
		}
		redirect(U('user/index'));
	}
	
	public function avatar(){
		$userid = session('user.id');
		$avatar = D('User')->where(array('id'=>$userid))->getField('avatar');
		$this->assign('avatar',$avatar)
			->display();
	}
	
	function avatarChange(){
		$userid = session('user.id');
		if(empty($userid)) redirect('Login/index');
		$config['exts'] = array('gif', 'jpg', 'jpeg', 'png', 'bmp');
		$config['maxSize'] = 1024*1024*1; //1M
		$config['rootPath'] = 'avatar'; 
		$info = D('Common/upload')->upload($config);
		$data['status'] = 1;
		if(isset($info['msg'])) {
			$data['status'] = 0;
			$data['msg']	= '请上传1M以内图片';
			$this->ajaxReturn($data);exit;
		}	
		$data['url'] = $info['file']['file'];
		D('User')->where(array('id'=>$userid))->save(array('avatar'=>$data['url']));
	
		$this->ajaxReturn($data);exit;
	}
		
	/* 忘记密码*/
    public function logout()
    {	
       session('user',null);
       cookie('_u',null);
	   redirect(U('index/index'));
    }

	function del(){
		//print_r($_POST);
		if(!empty($_POST)){
			$id=$_POST['id'];
			$model=M('Order_goods');
			$del=$model->where('id='.$id)->delete();
			if($del){
				echo 1;
			}else{
				echo 0;
			}
		}
		
	}
	function change(){

	    $this->display();
    }
    function schedule(){
        if(IS_AJAX){
            $moder = M('Entry');
            $userid= session('user.id');
            $str = $moder ->where('userID ='.$userid)->find();

            if($str){
                if($str['accepted'] == 1){
                    $this->ajaxReturn(1);//录取
                }elseif($str['accepted'] == 0){
                    $this->ajaxReturn(0);//审核中
                }else{
                    $this->ajaxReturn(2);//不录取
                }
            }else{
                $this->ajaxReturn(3);//无此人信息
            }

        }
        $this->display();
    }

    function modify(){
        if(IS_AJAX){
            if(!empty($_POST)){
                $userID = session('user.id');
                $data['birth']  = $_POST['birth'];
                $data['address']  = $_POST['address'];
                $data['school'] = $_POST['school'];
                $data['class'] = $_POST['classMe'];
                $data['major'] = $_POST['major'];

                $data['score']  = $_POST['score'];
                $data['teacher']  = $_POST['teacher'];
                $data['teacherTel']  = $_POST['teacherTel'];
                $data['father']  = $_POST['father'];
                $data['fatherTel']  = $_POST['fatherTel'];
                $data['mather'] = $_POST['mather'];
                $data['matherTel']  = $_POST['matherTel'];
                $data['personalTel']  = $_POST['personalTel'];
                $model = M('Entry');
                $str = $model->where('userID='.$userID)->save($data);
                if($str){
                    $this->ajaxReturn(1); //修改
                }else{
                    $this->ajaxReturn(0); //修改失败
                }
            }else{
                $this->ajaxReturn(2); //提交为空
            }

        }
    }
    //判断是否提交
    function isSubmit(){
        if(IS_AJAX){
            $userID = session('user.id');
            $User= M('Entry');
            $where['userID'] = $userID;
            $str = $User->where($where)->find();

            if($str){
                $this->ajaxReturn(1); //已提交过报名表
            }else{
                $this->ajaxReturn(0); //没有提交过报名表
            }
        }
    }
	
	

}



?>