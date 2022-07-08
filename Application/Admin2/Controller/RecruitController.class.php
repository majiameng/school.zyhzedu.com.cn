<?php
namespace Admin2\Controller;
use Think\Controller;
class RecruitController extends AdminBaseController {
    public $api_url = "http://payment.zyhzedu.com.cn/index.php";

    protected $model;
	
	function _initialize() { 	
		$roleid = session('admin.role_id');
		$city = session('admin.city');  		
        if($roleid !=1) {
 			$company = D('Recruit')->city_company();
  			$this->assign('unit',$company[$city]);
			// $this->assign('unit',D('Common/Recruit')->getCompany());
		}else{
			$this->assign('unit',D('Common/Recruit')->getCompany());
		}
	}		
	
    public function index(){
		$tt = I('get.tt',1,'intval');
        $recuit_status = F('_common/recuit_status');
//        $recuit_status[5] = "免试通过";
    	$this->_list();
        $this->assign('sex',F('_common/sex'))
			->assign('education',F('_common/education'))
			->assign('recuit_status',$recuit_status)
			->assign('job',D('Common/Recruit')->getJob())
			->assign('is_huibi',F('_common/is_huibi'))			
			->assign('full_time',F('_common/full_time'))
			->assign('degree',F('_common/degree'))
			->assign('tt',$tt);
		if($tt==1) $this->display();
		else $this->display('index_score');
    } 
    //考虑到回收站和普通列表逻辑相同，故放在一个方法里
    private function _list($is_del = 0){
        $is_pay = [0=>'未支付',1=>'已支付'];
        $where  = $this->getWhere();
		$roleid = session('admin.role_id');
		if($roleid !=1) $where['city'] = session('admin.city');	
		$status		= I('get.status',2,'intval');
		$tt 		= I('get.tt',1,'intval');
		$model  = D('Resume');
		if(isset($_GET['do'])) {
			$num = $model->where($where)->count();
			$page = I('get.page',1,'intval');
			$size = 500;
			/*
				if($num >= $size) {
				$offset = ($page-1)*$size;
				$data = $model->order('no asc,id desc')->limit($offset,$size)->where($where)->field('id,userid')->select();
			}else{
				$data = $model->order('no asc,id desc')->where($where)->field('id,userid')->select(); 
			}
			 */
			$size = 1;
			$offset = ($page-1)*$size;
			$data = $model->order('no asc,id desc')->limit($offset,$size)->where($where)->field('id,userid')->select();
			if(empty($data)) {
				$msg = $page == 1?'没有查询到导出的信息':'报名信息导出成功';
				$this->error($msg,U('Recruit/index','status='.$status));
			}
			$sex        = F('_common/sex');  
			$education  = F('_common/education');
			$company  	= D('Common/Recruit')->getCompany();
			$job  		= D('Common/Recruit')->getJob();
			$policy  	= F('_common/policy');//政治面貌
			$full_time  = F('_common/full_time');
			$is_huibi   = F('_common/is_huibi');
			$degree     = F('_common/degree');
			$marriage   = F('_common/marriage');
			$model = D('Home/Resume');
			foreach($data as $k=>$val){				
				$r = $model->getBaseInfo($val['userid']);
				$r['resume_qualifications_data'] = $model->getResumeQualifications($val['userid']);
				$data[$k]				 = $r;	
			
				$data[$k]['sex']         = $sex[$r['sex']];
				$data[$k]['education']   = $education[$r['education']];
				$data[$k]['degree']   	 = $degree[$r['degree']];
				$data[$k]['unit']     	 = $company[$r['unit']]['name'];
				$data[$k]['job']      	 = $job[$r['job']];
				$data[$k]['full_time']   = $full_time[$r['full_time']];
				$data[$k]['marriage']   = $marriage[$r['marriage']];
				$data[$k]['political_status']= $policy[$r['political_status']];
				$data[$k]['is_huibi']    = $is_huibi[$r['is_huibi']];
				$data[$k]['remark']   	 = ' ';
				$data[$k]['resume_qualifications'] = $r['resume_qualifications_data'];
				$data[$k]['is_famous'] = (!empty($r['is_famous'])) ? '是' : '否';
//				$data[$k]['is_train'] = (!empty($r['is_train'])) ? '有' : '没有';
				$data[$k]['is_operation'] = (!empty($r['is_operation'])) ? '有' : '没有';
//                $data[$k]['is_free'] = (!empty($r['is_free'])) ? '符合' : '不符合';
                $data[$k]['is_pay'] = (!empty($r['is_pay'])) ? '已支付' : '未支付';
                $data[$k]['operation_img'] = !empty($r['resume_qualifications_data']['operation_img']) ? $r['resume_qualifications_data']['operation_img'] : '';
//                $data[$k]['train_img'] = !empty($r['resume_qualifications_data']['train_img']) ? $r['resume_qualifications_data']['train_img'] : '';
                $data[$k]['graduation_img'] = !empty($r['resume_qualifications_data']['graduation_img']) ? explode(',', $r['resume_qualifications_data']['graduation_img']) : [];
                $data[$k]['skill_img'] = !empty($r['resume_qualifications_data']['skill_img']) ? explode(',', $r['resume_qualifications_data']['skill_img']) : [];

				$more = $model->getMore($val['userid'], $val['id']);
				$edu = array();
				if(!empty($more['edu'])) {
					foreach($more['edu'] as $item){
						$arry = array();
						$arry[] = $item['start_time'].'-'.$item['end_time'];
						$arry[] = $item['school'];
						$arry[] = $item['profession'];
						$arry[] = $education[$item['education']];
						$arry[] = $full_time[$item['full_time']];
						$arry[] = $degree[$item['degree']];
						$edu[] = implode(',',$arry);
					}
					$data[$k]['edu']   	 = implode("\r",$edu);
				}
				$family = array();
				if(!empty($more['family'])) {
					foreach($more['family'] as $item){
						$arry = array();
						$arry[] = $item['relation'];
						$arry[] = $item['name'];
						$arry[] = $item['work_place'];
						$arry[] = $item['job'];
						$family[] = implode(',',$arry);
					}
					$data[$k]['family']   	 = implode("\r",$family);
				}

				$work = array();
				if(!empty($more['work'])) {
					foreach($more['work'] as $item){
						$arry = array();
						$arry[] = $item['start_time'].'-'.$item['end_time'];
						$arry[] = $item['company'];
						$arry[] = $item['job'];
						$work[] = implode(',',$arry);
					}
					$data[$k]['work']   	 = implode("\r",$work);
				}
				$skill = array();
				if(!empty($more['skill'])) {
					$skill = $more['skill'];
					$arry[] = $skill['certificate'];
					$arry[] = $skill['other'];
					$arry[] = $skill['rewards'];
					$data[$k]['skill'] = implode(',',$arry);
				}
				
			}
			unset($more,$edu,$family,$work,$skill);
			$cnTable = array(			
				'unit'		=>'系部名称',
				'job'		=>'报考岗位',
				// 'exam_no'	=>'考场号',	
				'name'		=>'姓名',	
				'sex'		=>'性别',	
				'card'		=>'身份证',				
				'birth'		=>'出生日期',				
				'house_address'		=>'户口所在地',				
				'political_status'	=>'政治面貌',
				'is_huibi'			=>'是否符合回避原则',	
				
				'health'	=>'健康状况',
				'marriage'	=>'婚姻状况',
				'height'	=>'身高/cm',
				'weight'	=>'体重/kg',
				'nation'	=>'民族',
				
				'education'	=>'学历',
				'full_time'	=>'是否全日制',
				'degree'	=>'学位',
				'profession'=>'专业',
				'school'	=>'毕业院校',
				
				'edu'		=> "教育情况",
				'work'		=> "工作经历",				
				'skill'		=> "专业技能",				
				'family'	=> "家庭成员",	
				
				'address'	=>'通讯地址',
				'mobile'	=>'手机',
				'tel'		=>'固定电话',
				'postcode'	=>'邮编',
				'email'		=>'邮箱',
				'avatar'	=>'照片',
				'reamark'	=>'备注',
				'is_pay'	=>'是否支付报名费',
//				'is_free'	=>'是否符合免试条件',
				'is_famous'	=>'是否985/211',
				'card_front'	=>'身份证正面',
				'card_behind'	=>'身份证反面',
				'graduation_img'	=>'毕业证书',
				'study_img'	=>'学信网证明',
				'is_operation'	=>'是否有执业资格',
				'operation_img'	=>'执业资格证',
//				'is_train'	=>'是否有规培证',
//				'train_img'	=>'规培证',
				'skill_img'	=>'其他',

			);
			$tpl =  APP_PATH . "../zpxx.xlsx";
			if($roleid ==1) {
				if(isset($_GET['random'])&&!empty($_GET['random'])) $random = trim($_GET['random']);
				else $random = time();
				$name = 'zpxx_'.date('Y-m-d').'_'.$random.'_'.$page;
				$name = iconv('utf-8','gbk',$name);
				$obj = new \Org\Util\ExcelReader($name);	
				$r = $obj->export($data,$cnTable,$tpl,'12','2007',false);
				$query = $_SERVER['QUERY_STRING']."&random=$random&page=".($page+1);
				$url = U('Admin2/Recruit/index',$query);
				if($page%10==0) {					
					$this->success('导出成功，继续下一卷',$url);
					exit;
				}else{
					redirect($url);
				}	
				exit;
			}
			$name = '报名信息'.date('Y-m-d');
			$obj = new \Org\Util\ExcelReader($name);			
			$r = $obj->export($data,$cnTable,$tpl);
			unset($data,$edu,$first,$cnTable);
			exit;
		
		}
        $count = $model->where($where)->count();//总页数
    	$page  = $this->page($count, 20);
		if($tt!=1) {
			$list = $model->where($where)
				->limit($page->firstRow.','.$page->listRows)
				->alias('r')->join("left join __SCORE__ AS s on s.userid=r.userid")
				->order('r.unit asc,s.score desc,cast(no as signed) asc')
				->field('s.score,r.name,r.id,r.userid,r.sex,r.no,r.exam_no,r.unit,r.job,r.school,r.is_join,r.is_pay')
				->select(); 
		}else{
			$list = $model->where($where)
				->limit($page->firstRow.','.$page->listRows)
				->order('cast(exam_no as signed) asc,cast(no as signed) asc')
				->field('name,id,userid,sex,no,exam_no,birth,unit,job,school,education,profession,full_time,is_huibi,profession,status,addtime,is_ok,is_lock,is_join,is_pay')
				->select(); 
		}

        $result = $this->getNum();

		$this->assign('pages', $page->show())
			->assign('list', $list)
			->assign('result', $result)
			->assign('is_join', L('is_join'))
			->assign("status",$status)
//			->assign("is_free", I('get.is_free'))
			->assign("is_pay_status", I('get.is_pay'))
            ->assign('is_pay',$is_pay);
    }

    public function getNum(){
        $model  = D('Resume');
        $map = [];
//        $map['is_free'] = 0;
        if(isset($where['city'])) $map['city'] = $where['city'];
        $result = $model->where($map)->group('status')->field('status,count(*) as num')->select();
        $result = array_column($result,'num','status');
        for($i=1;$i<=5;$i++){
            !isset($result[$i])?$result[$i] = 0:'';
        }

        //面试待审核
        $mapFree = [
            'status'=>2,
            'is_free'=>1,
        ];
        $Freecount = $model->where($mapFree)->count();
        $result[6] = $Freecount ?? 0;

        //待审核
        $mapFree = [
            'status'=>2,
            'is_free'=>0,
        ];
        $Freecount = $model->where($mapFree)->count();
        $result[2] = $Freecount ?? 0;

        return $result;
    }
	
	/*
	** 准考证区间-导出
	*/	
	public function examclass(){
		$where['status'] = 1;
		$model = D('Resume');		
		$info = $model->where($where)
			->order('cast(exam_no as signed) asc')
			->field('exam_no,min(no) as min,max(no) as max')
			->group('exam_no')
			->select(); 
		$data = array();
		foreach($info as $k=>$r){
			$data[$k]['exam_no'] = "第".$r['exam_no'].'考场';
			$data[$k]['no'] 	=  $r['min'].'-'.$r['max'];
		}
		$cnTable = array(
			'exam_no'	=> '考场',
			'no'		=> '准考证区间',
		);
		$obj = new \Org\Util\ExcelReader('准考证区间');
		$obj->getExcel($data,$cnTable,20);			
		unset($data,$cnTable);
		exit;
	}
	
	/*
	** 标签
 	*/
	public function note(){
		$where = array(
			// 'is_lock' =>0,
			'is_join' =>1,
			'status'  =>1,
		);	
		$page = I('get.p',1,'intval');
		$model = D('Resume');$size = 510;		
		$count = $model->where($where)->count();//总页数
    	$page  = $this->page($count, $size);
		$list = $model->where($where)->limit($page->firstRow.','.$page->listRows)
			 ->order('cast(exam_no as signed) asc,cast(no as signed) asc')
			->field('name,no,exam_no,unit,job')
			->select();
		$this->assign('unit',D('Common/Recruit')->getCompany())
			->assign('job',D('Common/Recruit')->getJob())		
			->assign('list',array_chunk($list,30));
		$this->display();
	}

    public function detail(){
		$info = array();
		$id = I('get.id',0,'intval');
		if(!empty($id)) $info = D('Resume')->where(array('id'=>$id))->find();		
		 //echo D('Recruit')->getCardNo($info['unit'],$info['job']);	
		if(IS_AJAX) {
			$roleid = session('admin.role_id');
			if($roleid!=1) {
				//$this->error('您没有审核权限');
			}
			if(empty($info)) {
				$this->error('报名信息不存在');
			}				
			$remark = I('post.remark','','safe_replace');
			$status = I('post.status',0,'intval');					
			if(empty($status)) {
				$this->error('请选择审核结果');
			}
			//审核通过 获取准考证号
			/*
			if($status==1&&$info['status']!=1) {
				$no = D('Recruit')->getCardNo($info['unit'],$info['job']);
				$map = array();
				$map['no'] = $no;
				$map['id'] = array('neq',$id);				
				$res = D('Resume')->where($map)->field('id')->find();
				if(!empty($res)) $no = D('Recruit')->getCardNo($info['unit'],$info['job']); //准考证号重复重新生成
				$data['no'] = $no;
			}	
			*/

            // 发送邮箱提醒
            // 1通过 3拒绝 4信息补全 5免试通过
            $desc = '';
            switch ($status) {
                case 1:
                    $desc = '您的报名申请已通过';
                    $sms_template = 'template1';
                    break;
                case 3:
                    $desc = '您的报名申请已被拒绝。拒绝原因是：' . $remark;
                    $sms_template = 'template2';
                    break;
                case 4:
                    $desc = '您的报名申请已被拒绝。拒绝原因是：信息不全。' . $remark;
                    $sms_template = 'template3';
                    break;
                case 5:
                    $desc = '您的报名申请已通过（免试）';
                    $sms_template = 'template1';
                    break;
            }
//            $this->sendEmail($info['userid'], $desc);
            $this->sendEmail($info['userid'],$info['email'], $desc);
            var_dump($status);
            if(!empty($info['mobile']) && !empty($sms_template)){
//                $this->sendSms($info['mobile'], $sms_template);
                $sms = [
                    'user_id'=>$info['userid'],
                    'mobile'=>$info['mobile'],
                    'template'=>$status,
                    'create_time'=>time(),
                    'send_time'=>time()+30*60,
                    'status'=>1,
                ];
                var_dump($sms);
                $this->addSms($sms);
            }
            die;

			$data['status'] = $status;
			if($status==4 || $status==3) {
				$data['remark'] = $remark;
				$data['is_ok']	= 1;
			}else{
				$data['remark'] = '';
			}
			$data['exam_no'] = $data['no'] = '';	//清空考场和准考证号		
			$data['audit_time'] = time();			
			$data['admin_id']  = session('admin.id');		
			$r = D('Resume')->where(array('id'=>$id))->save($data);			
			if($r) {
				D('Home/Resume')->setResumeBase($info['userid']);
				$this->success('操作成功');
			}
			else $this->error('保存失败 请重试');
			exit;
		}
		if(!empty($info)) {
			$data = D('Home/Resume')->getMore($info['userid'],$info['id']);
			$this->assign('edu',$data['edu'])
				->assign('work',$data['work'])
				->assign('family',$data['family'])
				->assign('skill',$data['skill'])
                ->assign('graduation_img',$data['graduation_img'])
                ->assign('skill_img',$data['skill_img'])
                ->assign('resume_qualifications',$data['resume_qualifications'])
				->assign('education',F('_common/education'))
				->assign('sex',F('_common/sex'))
				->assign('marriage',F('_common/marriage'))			
				->assign('is_huibi',F('_common/is_huibi'))			
				->assign('full_time',F('_common/full_time'))
				->assign('policy',F('_common/policy'))				
				->assign('degree',F('_common/degree'))		
				->assign('unit',D('Common/Recruit')->getCompany())
				->assign('job',D('Common/Recruit')->getJob());
		}		
		$this->assign('base',$info)
			->display();
	}

    /**
     * Notes:发送邮箱
     * Date: 2022/3/27 上午2:16
     * @param $uid
     * @param $desc
     */
//	public function sendEmail($uid, $desc)
	public function sendEmail($uid,$email, $desc)
    {
        // 官网url
        $url = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].'/index.php?m=Home';
        $model = M('User');
        $userInfo = $model->where(['id' => $uid])->find();
        if(empty($userInfo)){
            $this->error('账号不存在');exit; //失败
        }else{
            $content = " '".$userInfo['username']."',你好:<br>"
                .$desc."<br>您可以点击如下链接查看申请进度。<br>如果点击无效，请把下面的代码拷贝到浏览器的地址栏中： '".$url."'在访问链接之后, 您可查看。<br>";

            $emailContent = htmlspecialchars_decode($content);

//            if (!empty($userInfo['email'])) {
//                user_send_email($userInfo['email'], $emailContent,2);
//            }
            if (!empty($email)) {
                user_send_email($email, $emailContent,2);
            }
        }
    }

    /**
     * Notes:发送短信
     * Date: 2022/3/27 上午2:16
     * @param $uid
     * @param $desc
     */
	public function sendSms($mobile, $sms_template){
        //短信发送
        $url = $this->api_url."/Sms/index";
        $params = [
            'mobile'=>$mobile,
            'type'=>$sms_template,//定义的模版名字
        ];
        $result = $this->httpPost($url,$params);//{"code":200,"msg":"success","data":[]}
        $result = json_decode($result,true);
        if($result['code'] != 200){
            $this->error("短信发送失败：".$result['msg']);
        }
    }

    /**
     * Notes:添加短信
     * Date: 2022/3/27 上午2:16
     * @param $info
     */
	public function addSms($info){
        $where = [
            'user_id'=>$info['user_id'],
            'status'=>1,
        ];
        $sms = D('Sms')->where($where)->count();
        if($sms>0){
            //删除未发送的短信
            D('Sms')->where($where)->delete();
        }
        //添加短信任务
        D('Sms')->create($info);
        D('Sms')->add();
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

	//编辑
	public function edit(){	
		$info = array();
		$id = I('get.id',0,'intval');
		if(!empty($id)) $info = D('Resume')->where(array('id'=>$id))->find();		
		 //echo D('Recruit')->getCardNo($info['unit'],$info['job']);	
		if(IS_AJAX) {
			$roleid = session('admin.role_id');
			if($roleid!=1) {
				//$this->error('您没有审核权限');
			}
			if(empty($info)) {
				$this->error('报名信息不存在');
			}				
			$exam_no = I('post.exam_no','','trim');
			$data['exam_no'] = $exam_no;
			$r = D('Resume')->where(array('id'=>$id))->save($data);			
			if($r) {
				D('Home/Resume')->setResumeBase($info['userid']);
				$this->success('操作成功');
			}
			else $this->error('保存失败 请重试');
			exit;
		}
		if(!empty($info)) {
			$data = D('Home/Resume')->getMore($info['userid'],$info['id']);
			$this->assign('edu',$data['edu'])
				->assign('work',$data['work'])
				->assign('family',$data['family'])
				->assign('skill',$data['skill'])
                ->assign('graduation_img',$data['graduation_img'])
                ->assign('skill_img',$data['skill_img'])
				->assign('education',F('_common/education'))
				->assign('sex',F('_common/sex'))
				->assign('marriage',F('_common/marriage'))			
				->assign('is_huibi',F('_common/is_huibi'))			
				->assign('full_time',F('_common/full_time'))
				->assign('policy',F('_common/policy'))				
				->assign('degree',F('_common/degree'))		
				->assign('unit',D('Common/Recruit')->getCompany())
				->assign('job',D('Common/Recruit')->getJob());
		}		
		$this->assign('base',$info)
			->display('edit_detail');
	}
	
	/*
	** 重新分配考场
	*/	
	public function exam(){
		$where = array(
			'is_lock' =>0,
			'is_join' =>1,
			'status'  =>1,
		);	
		$data = array('exam_no' => '');
		D('Resume')->where($where)->save($data);		
		$list = D('Resume')->where($where)->order('cast(no AS signed) asc')->field('id,no,userid')->select();	
		if(empty($list)) {
			$this->error('没有需要分配考场的考生');			
		}
		// $list = array_column($list,'userid','id');		
		$list = array_chunk($list,30);
		$map = array();
		foreach($list as $key=>$val){			
			foreach($val as $k=>$r){
				$map['exam_no'] = str_pad($key+1,3,0,STR_PAD_LEFT);
				D('Resume')->where(array('id'=>$r['id']))->save($map);
				D('Home/Resume')->setResumeBase($r['userid']);
			}
		}
		$this->success('考场分配完成');	
		// var_export($list);exit;
	}
	
	/*
	** 批量审核
	*/	
	public function verify(){				
		$ids = I('get.ids','','trim');$where = array();		
		if(empty($ids)) $this->error('请选择报名信息');		
		$where['id'] = array('in',$ids); 
		$status = I('get.status',3,'intval'); 
		$data = array(
			'remark' 	=>	'',
			'status' 	=>	$status,
			'audit_time'=>	time(),
		);
		if($status==4 || $status==3) $data['is_ok']	= 1;
		$r = D('Resume')->where($where)->save($data);			
		if($r) {
			$list = D('Resume')->where($where)->field('id,userid')->select();	
			foreach($list as $k=>$r){
				D('Home/Resume')->setResumeBase($r['userid']);
			}						
			$this->success('操作成功');exit;
		}
		$this->error('审核失败，请重试');			
	}
	
	
	/*
	** 统计
	*/
	public function statNum(){	
		$unit = D('Common/Recruit')->getCompany();
		$job = D('Common/Recruit')->getJob();
		$list = D('Recruit')->order('id asc')->select();
		$data = array();$total = 0;	
		foreach($list as $k=>$r){
			$data[$k]['unit'] = $unit[$r['company']]['name'];
			$data[$k]['job']  = $job[$r['job']];
			
			$map['unit'] = $r['company'];
			$map['job']	 = $r['job'];
			$info = array();		
			for($i=1;$i<5;$i++){
				$map['status']	 = $i;
				$num = D('Resume')->where($map)->count();
				$num = $num > 0? $num:0;
				$data[$k]['status'.$i] = $num;
				$info[$i] = $num;$total+=$num;
			}
			$data[$k]['total'] = array_sum($info);
		}
		$arry = array(
			'unit'		=> '',
			'job'		=> '',
			'status1'	=> '',
			'status2'	=> '',
			'status3'	=> '',
			'status4'	=> '总人数：',
			'total'		=> $total,
		);
		$data[] = $arry;
		$cnTable = array(
			'unit'		=> '系部名称',
			'job'		=> '报考岗位',
			'status1'	=> '审核通过',
			'status2'	=> '待审核',
			'status3'	=> '未通过',
			'status4'	=> '资料待完善',
			'total'		=> '总人数',
		);
		$obj = new \Org\Util\ExcelReader('报名统计');
		$obj->getExcel($data,$cnTable,20);			
		unset($data,$cnTable);
		exit;
	}
	/*
	** 统计
	*/
	public function statEdu(){	
		$education = F('_common/education');$data = array();
		$map = array(
			// 'is_lock' =>0,
			'is_join' =>1,
			'status'  =>1,
		);	
		foreach($education as $k=>$r){			
			$map['education'] = $k;
			$num = D('Resume')->where($map)->count();
			$num = $num > 0 ? $num:0;
			$data[$k] = $num;
		}		
		$education[] = '总人数';	
		$data[] = array_sum($data);
		$data 	= array($data);		
		$obj = new \Org\Util\ExcelReader('学历分布统计');
		$obj->getExcel($data,$education,20);			
		unset($data,$cnTable);
		exit;
	}
	
	/*
	** 准考证 批量打印
	*/
	public function printlist(){				
		$ids = I('get.ids','','trim');
		$type = I('get.type',0,'intval');
		$roleid = session('admin.role_id');
		$where['status'] = 1;
		if($roleid !=1) $where['city'] = session('admin.city');	
		if(empty($type) && !empty($ids)) {
			$where['id'] = array('in',$ids);
		}                 
        $list = M('Resume')->where($where)->field('name,unit,job,no,card,avatar,exam_no')->select();	
        if(empty($list)) $this->error('没有相关信息');	
        $this->assign('list',$list)
			->assign('unit',D('Common/Recruit')->getCompany())
			->assign('job',D('Common/Recruit')->getJob())
			->assign('config',D('Common/Cache')->getConfig())
            ->display();
	}
   
	/*
	** 打印报名信息
	*/
    public function printDetail()
    {
		$info = array();$where['status'] = 1;
		$ids = I('get.ids','','trim');
		$type = I('get.type',0,'intval');
		$roleid = session('admin.role_id');
		if($roleid !=1) $where['city'] = session('admin.city');	
		if(empty($type) && !empty($ids)) {
			$where['id'] = array('in',$ids);
		}  
        $list = M('Resume')->where($where)->field('id,userid')->select();  
        if(empty($list)) $this->error('没有相关信息'); 
		if(!empty($list)) {
			foreach($list as $k=>$r){
				$info[$k] = D('Home/Resume')->getMore($r['userid'],$r['id']);
				$info[$k]['base'] = D('Home/Resume')->getBaseInfo($r['userid']);
			}
		}
		unset($list);		
		$this->assign('list',$info);
		$this->assign('education',F('_common/education'))
				->assign('sex',F('_common/sex'))
				->assign('marriage',F('_common/marriage'))			
				->assign('is_huibi',F('_common/is_huibi'))			
				->assign('full_time',F('_common/full_time'))
				->assign('policy',F('_common/policy'))
				->assign('degree',F('_common/degree'))				
				->assign('unit',D('Common/Recruit')->getCompany())
				->assign('job',D('Common/Recruit')->getJob())
				->assign('config',D('Common/Cache')->getConfig())
				->display();
    }
	
	/*
	** 报名表 导出Excel
	*/
    public function excel_beifen()
    {
		$info = array();
		// $where['status'] = 1;
		$id = I('get.id','','trim');		
		// $roleid = session('admin.role_id');
		$where['id'] = $id;	 
        $r = M('Resume')->where($where)->field('id,userid')->find();

        if(empty($r)) $this->error('没有相关信息'); 
		$info = D('Home/Resume')->getMore($r['userid'],$r['id']);
		$info['base'] = D('Home/Resume')->getBaseInfo($r['userid']);
				
		$str = $this->assign('info',$info)
				->assign('education',F('_common/education'))
				->assign('sex',F('_common/sex'))
				->assign('marriage',F('_common/marriage'))			
				->assign('is_huibi',F('_common/is_huibi'))			
				->assign('full_time',F('_common/full_time'))
				->assign('policy',F('_common/policy'))
				->assign('degree',F('_common/degree'))				
				->assign('unit',D('Common/Recruit')->getCompany())
				->assign('job',D('Common/Recruit')->getJob())
				->fetch();
		$filename = "个人报名表.xls"; 
		
		header("Content-Type: application/vnd.ms-excel;charset=utf-8");
        // header( "Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" );     #告诉浏览器响应的对象的类型（字节流、浏览器默认使用下载方式处理）
        header( "Content-Disposition: attachment; filename=".$filename );   
		#不打开此文件，刺激浏览器弹出下载窗口、下载文件默认命名

        header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" ); 
        header( "Pragma: no-cache" );   #保证不被缓存或者说保证获取的是最新的数据
        header( "Expires: 0" ); 
        exit( $str ); 
    }
	
	/*
	** 成绩导入
	*/
    public function export_bao(){
        if(IS_POST){
            $info = $this->upload();
            if(!is_array($info)) $this->error($info);
            $list = $this->getExcelList($info['file'],$info['ext']); 
            if(empty($list)) $this->error('文件数据为空');
			foreach($list as $k=>$r) {				
				$no[] = trim($r[2]);	
			}	
			F('_common/export_bao',$no);			
		}else{
			$no = F('_common/export_bao');
		}
		$list = array();$model = D('resume');
		$where['status'] = 1;
		if(!empty($no)) $where['no']	 = array('in',implode(',',$no));
		$count = $model->where($where)->count();//总页数		
    	$page  = $this->page($count, 20);				
		$list = D('resume')->where($where)->limit($page->firstRow.','.$page->listRows)->select();		
		$this->assign('sex',F('_common/sex'))
			->assign('education',F('_common/education'))
			->assign('recuit_status',F('_common/recuit_status'))
			->assign('job',D('Common/Recruit')->getJob())
			->assign('is_huibi',F('_common/is_huibi'))			
			->assign('full_time',F('_common/full_time'))	
			->assign('degree',F('_common/degree'));
			
		$this->assign('list', $list)
			->assign('total', $count)
			->assign('pages', $page->show())
			->display();
	}

	/*
	** 导出报名表
	 */
	public function export_bao_xls(){
		$row 	= 150;
		$p 		= I('get.p',1,'intval');		
		$status	= I('get.status',0,'intval');
		$where  = $this->getWhere(); 
		$where['status'] = $status;
		$count  = D('resume')->where($where)->count();//总页数		
    	$page   = $this->page($count, $row);						
		$r = D('resume')->where($where)
			->limit($page->firstRow.','.$page->listRows)
			->field('id')->select();
        $num = count($r);
		$size = $num==$row ? $p*$row:($p-1)*$row+$num;
		$config = D('Common/Cache')->getConfig();
		if(!empty($r)) {
			foreach($r as $val) {
				$obj = new \Org\Util\ExcelReader('报名表',$config['exam_year']);
				$info = $this->excel_data($val['id']);
				$rr = $obj->getExcelBao($info,'12',"other",false);	
			}
			if($rr) {
				$where['p'] = $p+1;
				// $url = U('Recruit/export_bao_xls',array('status'=>$status,'p'=>$p+1));
				$url = U('Recruit/export_bao_xls',$where);
				$this->success("共{$count}条，成功导出{$size}条,正在进行下一批",$url);exit;
			}
			$user = session('export_user');
			empty($user)?$user = []:'';
			$user[] = $info['base']['name'];
			session('export_user',$user);
		}
		else{
			$user = session('export_user');
			if(!empty($user)) {
				echo "导出失败记录： \n\r<br/>";
				echo implode(',',$user);
				exit;	
			}
			$this->success('全部导出成功',U('Recruit/index',array('status'=>$status)));exit;
		}
		/*
		$r = array_shift($r);
		if(!empty($r)) {
			$obj = new \Org\Util\ExcelReader('报名表');
			$info = $this->excel_data($r['id']);
			$rr = $obj->getExcelBao($info,'12',"other",false);			
			if($rr) {
				$url = U('Recruit/export_bao_xls',array('status'=>$status,'p'=>$p+1));
				$this->success("共{$count}条，成功导出{$p}条,正在进行下一条",$url);exit;
			}
			$user = session('export_user');
			empty($user)?$user = []:'';
			$user[] = $info['base']['name'];
			session('export_user',$user);
		}
		else{
			$user = session('export_user');
			if(!empty($user)) {
				echo "导出失败记录： \n\r<br/>";
				echo implode(',',$user);
				exit;	
			}
			$this->success('全部导出成功',U('Recruit/index'));exit;
		}
		*/
		
		
		
	}

	/*
	** 报名表 导出Excel
	*/
    public function excel_data($id)
    {
		$info = array();		
		$where['id'] = $id;	 
        $r = M('Resume')->where($where)->field('id,userid')->find();

        if(empty($r)) $this->error('没有相关信息');
		
		$education 		= F('_common/education');
		$sex			= F('_common/sex');
		$marriage 		= F('_common/marriage');
		$is_huibi 		= F('_common/is_huibi');
		$full_time 		= F('_common/full_time');
		$policy 		= F('_common/policy');
		$degree 		= F('_common/degree');
		$unit 			= D('Common/Recruit')->getCompany();
		$job 			= D('Common/Recruit')->getJob();
	
		$info = D('Home/Resume')->getMore($r['userid'],$r['id']);
		if(!empty($info['edu'])) {
			foreach($info['edu'] as $k=>$v){
				$info['edu'][$k]['education']   = $education[$v['education']]; 
				$info['edu'][$k]['degree'] 		= $degree[$v['degree']]; 
				$info['edu'][$k]['full_time']   = $full_time[$v['full_time']]; 
			}
		}
		$base = D('Home/Resume')->getBaseInfo($r['userid']);
		$dir = $_SERVER['DOCUMENT_ROOT'];
		$base['avatar'] = $dir.substr($base['avatar'],1);	
		$base['sex'] 		= $sex[$base['sex']];
		$base['education'] 	= $education[$base['education']];
		$base['degree'] 	= $degree[$base['degree']];
		$base['marriage'] 	= $marriage[$base['marriage']];
		$base['is_huibi'] 	= $is_huibi[$base['is_huibi']];
        if(!empty($base['is_famous']) && $base['is_famous']==1){
            $base['is_famous'] 	= '是';
        }else{
            $base['is_famous'] 	= '否';
        }
		$base['full_time'] 	= $full_time[$base['full_time']];
		$base['unit'] 		= $unit[$base['unit']]['name'];
		$base['job'] 		= $job[$base['job']];
		$base['political_status']= $policy[$base['political_status']];
		$info['base'] = $base;
		return $info;
    }
	
	/*
	** 报名表 导出Excel
	*/
    public function excel()
    {
		$info = array();
		// $where['status'] = 1;
		$id = I('get.id','','trim');			
		// $roleid = session('admin.role_id');
		$where['id'] = $id;	 
        $r = M('Resume')->where($where)->field('id,userid')->find();

        if(empty($r)) $this->error('没有相关信息');
		
		$education 		= F('_common/education');
		$sex			= F('_common/sex');
		$marriage 		= F('_common/marriage');
		$is_huibi 		= F('_common/is_huibi');
		$full_time 		= F('_common/full_time');
		$policy 		= F('_common/policy');
		$degree 		= F('_common/degree');
		$unit 			= D('Common/Recruit')->getCompany();
		$job 			= D('Common/Recruit')->getJob();
	
		$info = D('Home/Resume')->getMore($r['userid'],$r['id']);
		if(!empty($info['edu'])) {
			foreach($info['edu'] as $k=>$v){
				$info['edu'][$k]['education']   = $education[$v['education']]; 
				$info['edu'][$k]['degree'] 		= $degree[$v['degree']]; 
				$info['edu'][$k]['full_time']   = $full_time[$v['full_time']]; 
			}
		}
		$base = D('Home/Resume')->getBaseInfo($r['userid']);
		$dir = $_SERVER['DOCUMENT_ROOT'];
		$base['avatar'] = $dir.substr($base['avatar'],1);	
		$base['sex'] 		= $sex[$base['sex']];
		$base['education'] 	= $education[$base['education']];
		$base['degree'] 	= $degree[$base['degree']];
		$base['marriage'] 	= $marriage[$base['marriage']];
		$base['is_huibi'] 	= $is_huibi[$base['is_huibi']];
        if(!empty($base['is_famous']) && $base['is_famous']==1){
            $base['is_famous'] 	= '是';
        }else{
            $base['is_famous'] 	= '否';
        }
//        if(!empty($base['is_free']) && $base['is_free']==1){
//            $base['is_free'] 	= '是';
//        }else{
//            $base['is_free'] 	= '否';
//        }
        if(!empty($base['is_operation']) && $base['is_famous']==1){
            $base['is_operation'] 	= '有';
        }else{
            $base['is_operation'] 	= '没有';
        }
//        if(!empty($base['is_train']) && $base['is_train']==1){
//            $base['is_train'] 	= '有';
//        }else{
//            $base['is_train'] 	= '没有';
//        }
		$base['full_time'] 	= $full_time[$base['full_time']];
		$base['unit'] 		= $unit[$base['unit']]['name'];
		$base['job'] 		= $job[$base['job']];
		$base['political_status']= $policy[$base['political_status']];
		$info['base'] = $base;
		$config = D('Common/Cache')->getConfig();
		$obj = new \Org\Util\ExcelReader('报名表',$config['exam_year']);
        $obj->getExcelBao($info,'12');
		exit;
    }
   
	/*
	** pdf下载
	*/
	public function printcard(){		
		// $info = D('Resume')->where(array('id'=>$id))->find();
		
		$ids = I('get.ids','','trim');
        $ids = explode(',',$ids);
        $ids = array_unique($ids);        
        if(empty($ids)) $this->error('请选择信息');
        $where['id'] = array('in',$ids);  
        $list = M('Resume')->where($where)->select();
	
        if(empty($list)) $this->error('没有相关信息');
		
        $html = $this->assign('list',$list)
			->assign('unit',D('Common/Recruit')->getCompany())
			->assign('job',D('Common/Recruit')->getJob())
			->assign('config',D('Common/Cache')->getConfig())
			->fetch();    
		D('Common/Recruit')->pdf($html);
	}
	
	/*
	** 批量审核
	*/	
	public function lock(){				
		$ids = I('get.ids','','trim');$where = array();		
		if(empty($ids)) $this->error('请选择报名信息');		
		$where['id'] = array('in',$ids);  
		$data = array(
			'is_lock' 	=>	1,
			'uptime'	=>	time(),
		);
		$r = D('Resume')->where($where)->save($data);			
		if($r){
			$list = D('Resume')->where($where)->field('id,userid')->select();	
			foreach($list as $k=>$r){
				D('Home/Resume')->setResumeBase($r['userid']);
			}						
			$this->success('锁定成功');exit;
		}
		$this->error('锁定失败，请重试');			
	}
	
	/*
	** 批量审核
	*/	
	public function unlock(){				
		$id = I('get.id',0,'intval');$where = array();		
		if(empty($id)) $this->error('请选择报名信息');			
		$where['id'] = $id; 
		$info = D('Resume')->where($where)->field('id,userid,card,is_lock')->find();

		if(empty($info)) $this->error('报名信息不存在或已删除');
		if($info['is_lock']==0) $this->error('该记录没有被锁定');		
		$data = array(
			'is_lock' 	=>	0,
			'uptime'	=>	time(),
		);
		$r = D('Resume')->where($where)->save($data);  //解除锁定
		D('Home/Resume')->setResumeBase($info['userid']);
		
		
		//其他重复的身份证 锁定		
		if($r){
			$map = array('card'=>$info['card'],'id'	=>array('neq',$id));
			$data = array(
				'is_lock' 	=>	1,
				'uptime'	=>	time(),
			);
			D('Resume')->where($map)->save($data);
			$list = D('Resume')->where($map)->field('id,userid')->select();	
	
			foreach($list as $k=>$v){
				D('Home/Resume')->setResumeBase($v['userid']);
			}						
			$this->success('解除成功');exit;
		}
		$this->error('解除失败，请重试');
					
	}
			
	/*
	** 花名册
	*/
    public function nameList($is_del = 0){
		$roleid = session('admin.role_id');$city = session('admin.city');
		$where = $this->getWhere();//获取搜索条件
		if($roleid !=1) $where['city'] = $city;		
		$model  = D('Resume');
		if(isset($_GET['do'])) {
			/*
            $where = array(
				// 'is_lock' =>0,
				'is_join' =>1,
				'status'  =>1,
			);
            */
			 // $data=$model->order('id DESC')->where($where)->field('name,id,sex,no,exam_no,birth,unit,job,school,education,profession,full_time,card,address,is_huibi,profession,status,mobile,tel,email')->select();
			 
			// $data=$model->order('id DESC')->where($where)->select();
			$data=$model->order('cast(exam_no as signed) asc,cast(no AS signed) asc')->where($where)->field('id,userid,exam_no,no,unit')->select();
			if(empty($data)) $this->error('没有可导出的信息');
			// $data=$model->order('exam_no asc,no asc')->where($where)->field('id,userid')->select();
			$sex        = F('_common/sex');  
			$education  = F('_common/education');
			$company  	= D('Common/Recruit')->getCompany();
			$job  		= D('Common/Recruit')->getJob();
			$policy  	= F('_common/policy');//政治面貌
			$full_time  = F('_common/full_time');
			$is_huibi   = F('_common/is_huibi');
			$degree     = F('_common/degree');
			$model = D('Home/Resume');$dir = $_SERVER['DOCUMENT_ROOT'];
			foreach($data as $k=>$r){				
				$r = $model->getBaseInfo($r['userid']);
				$data[$k]				 = $r;			
				$data[$k]['sex']         = $sex[$r['sex']];
				$data[$k]['education']   = $education[$r['education']];
				$data[$k]['degree']   	 = $degree[$r['degree']];
				$data[$k]['unit']     	 = $company[$r['unit']]['name'];
				$data[$k]['job']      	 = $job[$r['job']];
				$data[$k]['age']  		 = birthday($r['birth']);
				$data[$k]['avatar']  	 = $dir.substr($r['avatar'],1);
				$data[$k]['full_time']   = $full_time[$r['full_time']];
				$data[$k]['political_status']= $policy[$r['political_status']];
				$data[$k]['is_huibi']    = $is_huibi[$r['is_huibi']];
				$data[$k]['renhang']   	 = '';
				$data[$k]['remark']   	 = '';
                $data[$k]['edu_list']	 = '';
				
				$edu = $edu_data = $model->getResumeEdu($r['id'], $r['userid']);
				array_multisort(array_column($edu,'education'),SORT_DESC,$edu);
				$first = $edu[0];
				$data[$k]['max_education']   = $education[$first['education']];
				$data[$k]['max_full_time']   = $full_time[$first['full_time']];
				$data[$k]['max_profession']  = $first['profession'];
				$data[$k]['max_school'] 	 = $first['school'];
              	
              if(!empty($edu_data)) {
                	$edu_tmp = array();
					foreach($edu_data as $item){
						$arry = array();
						$arry[] = $item['start_time'].'-'.$item['end_time'];
						$arry[] = $item['school'];
						$arry[] = $item['profession'];
						$arry[] = $education[$item['education']];
						$arry[] = $full_time[$item['full_time']];
						$arry[] = $degree[$item['degree']];
						$edu_tmp[] = implode(',',$arry);
					}
					$data[$k]['edu_list']    = implode("；",$edu_tmp);
				}
			}  
   
			$cnTable = array(
				'unit'		=>'系部名称',
				'job'		=>'报考岗位',				
				'no'		=>'准考证号',
				'exam_no'	=>'考场',
				'name'		=>'姓名',
				'sex'		=>'性别',				
				'birth'		=>'生日',				
				'age'		=>'年龄',
				'avatar'	=>'照片',							
				'political_status'	=>'政治面貌',				
				'education'	=>'学历',
				'full_time'	=>'是否全日制',
				'degree'	=>'学位',
				'profession'=>'专业',
				'school'	=>'毕业院校',
				
				'max_education'	=>'最高学历',
				'max_full_time'	=>'是否全日制',
				'max_profession'=>'(最高)专业',
				'max_school'	=>'(最高)毕业院校',
				'edu_list'		=>'教育情况',
				'house_address'	=>'生源所在地',
				'card'		=>'身份证',
				'is_huibi'	=>'是否符合回避原则',
				'renhang'	=>'是否人行子女',
				'mobile'	=>'手机',
				'tel'		=>'固定电话',
				'email'		=>'邮箱',
				'reamark'	=>'备注',
			
			);
			$name = '花名册';
			if($roleid !=1) {
				$region = F('_common/region');
				$name = $region[$city]['region_name'].'花名册';
			}	
			$obj = new \Org\Util\ExcelReader($name);
			$tpl =  APP_PATH . "../hmc.xlsx";
			$obj->export($data,$cnTable,$tpl,'12');
			unset($data,$edu,$first,$cnTable,$region,$edu_tmp);
			exit;
		}
		$this->error('非法操作');
    }
	
	
	/*
	** 成绩导入
	*/
    public function import(){
        if(IS_POST){
            $info = $this->upload();
            if(!is_array($info)) $this->error($info);
            $list = $this->getExcelList($info['file'],$info['ext']); 			
            if(empty($list)) $this->error('文件数据为空');         
			$fail_list = $log = array();$i=0;			
			foreach($list as $k=>$r) {
				$data = $arry = array();
				$no = trim($r[0]); 
				if(empty($no)||strlen($no)==0){
					unset($list[$k]);
					continue;
				}
				$arry['no']		 = $no;
				$arry['is_ok']   = 0;				
				$arry['score']	 = $data['score']  = floatval($r[5]); 
				$arry['addtime'] = $data['uptime'] = time();
				if(empty($no)){
					$arry['msg']  = '准考证号为空';
					$fail_list[] = $data;
					continue;
				}					
				$where['status'] = 1;
				$where['no']	 = $no;
				$userid = D('resume')->where($where)->getField('userid');			
				if(empty($userid)) {
					$arry['msg']  = '准考证号不存在';
					$fail_list[] = $data;
					continue;
				}
				// D('score')->where('1')->delete();
				$data['userid'] = $userid; 
				$rs = D('score')->add($data);
				$arry['is_ok'] = $rs?1:0;
				$arry['msg']   = $rs?'成功':'导入失败';
				$log[] = $arry;
				$i++;             
			}	
			D('importlog')->addAll(array_values($log));
			$total = count($list);
			$str = '总条数：%s,成功导入：%s条,失败：%s条';
			$str = sprintf($str,$total,$i,count($fail_list));
			$this->success($str);
		}  
		exit;
	}  
	/**
	  * 重新生成准考证号
	  *@param status 审核状态1通过;0待审核;2审核中;3审核失败;4资料不全
	  *@param is_lock 锁定 1是;0否
	  */
	public function resetExamNo(){
		$row 	= 400;
		$p 		= I('get.p',1,'intval');	
		$map = array();$resume_obj = D('Resume');
		$map['is_lock'] = 0;	//锁定1是0否
		$map['is_join'] = 1;	
		$map['status'] = 1;		//审核状态1通过0否
		if($p == 1) {
			$data = array('no' => '');
			$resume_obj->where($map)->save($data);
		}
		$count  = $resume_obj->where($map)->count();
		$page = $this->page($count, $row);						
		$list = $resume_obj->where($map)
			->limit($page->firstRow.','.$page->listRows)
			->order('id asc')
			->field('id,userid,unit,job')->select();
		$num = count($list);
		$size = $num==$row ? $p*$row:($p-1)*$row+$num;
		// var_export($list);exit;
		if(!empty($list)) {
			$obj = D('Recruit');
			$data = array();
			$data['admin_id']  = session('admin.id');	
			foreach($list as $r) {
				$no   = $obj->getCardNo($r['unit'],$r['job']);
				$map = array();
				$map['no'] = $no;
				$map['id'] = array('neq',$id);				
				$res = $resume_obj->where($map)->field('id')->find();
				if(!empty($res)) $no = $obj->getCardNo($r['unit'],$r['job']); //准考证号重复重新生成
				
				$data['no']  = $no;	
				$res = $resume_obj->where(array('id'=>$r['id']))->save($data);
				if(empty($res)) continue;
				D('Home/Resume')->setResumeBase($r['userid']);		
			}
			$url = U('Recruit/resetExamNo',array('p'=>$p+1));
			$this->success("共{$count}条，成功操作{$size}条,正在进行下一批",$url,2);
			exit;
		}
		$this->success('全部生成成功',U('Recruit/index','status=1'));exit;
	}
	public function upload(){
		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize   =     3145728 ;// 设置附件上传大小
		$upload->exts      =     array('xls', 'xlsx');// 设置附件上传类型
		$upload->rootPath  =     WEB_PATH.'/upload/file/'; // 设置附件上传根目录
		
		$upload->savePath  =     ''; // 设置附件上传（子）目录
		$dir = $upload->rootPath.$upload->savePath;
		if(!file_exists($dir)) {
			@mkdir($dir,0777,true);
		}
		if(!is_writable($dir)){
			@chmod($dir,0777);
		}
		
		// 上传文件 
		$info   =   $upload->upload();
		if(!$info) {// 上传错误提示错误信息
			return $upload->getError();
		}
        $data = array();
        foreach($info as $k=>$r) {
            $data['file'] = $upload->rootPath.$r['savepath'].$r['savename'];
            $data['ext']  = $r['ext'];
        }
        unset($info);
        return $data;	
	}

    /*
    **  gmdate("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($val)) 将数字转为日期
    **
     */
    public function getExcelList ($filename,$ext='xlsx'){
        import("Org.Util.PHPExcel");
        import("Org.Util.PHPExcel.IOFactory",'','.php');
		if($ext=='xls') {
			 $reader     = \PHPExcel_IOFactory::createReader('Excel5'); 
		}else{
			 $reader     = \PHPExcel_IOFactory::createReader('Excel2007'); 
		}       
        $PHPExcel = $reader->load($filename,$encode='utf-8');       //载入文件
        //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
        $currentSheet = $PHPExcel->getSheet();
        $highestRow   = $currentSheet->getHighestRow();    //取得总行数
        $allColumn = $currentSheet->getHighestColumn(); // 取得总列数		
        $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($allColumn);      
        $infos = array();
        for($row=2;$row<=$highestRow;$row++){
             for($col= 0;$col<=$highestColumnIndex;$col++){   
                $val = trim($currentSheet->getCellByColumnAndRow($col, $row)->getValue());               
                $infos[$row][$col] = $val===null?'':$val;
            }
            
        }
        return $infos;
    }
	
	//排序
    public function listorder() {
    	//dump($_POST);die;
    	$ids = $_POST['listorders'];
    	foreach ($ids as $key => $v) {
    		$data['listorder'] = $v;
    		$this->model->where(array('id' => $key))->save($data);
    	}
    	$this->success('排序更新成功！');    	
    }
	
    //删除，并非真正删除，只是把is_del改为1
    public function del(){
    	
    	//删除单个
    	if(isset($_GET['id'])){
    		$id = I('get.id',0,'int');
    		$data['is_del']=1;
    		$rs = $this->model->where('id='.$id)->save($data);
    		if ($rs !== false) {
    			$this->success('删除成功！');
    		} else {
    			$this->error('删除失败！');
    		}
    	}
    	//批量删除
    	if(isset($_POST['ids'])){
    		$ids=implode(',',$_POST['ids']);
    		$data['is_del']=1;
			$rs = $this->model->where("id in ($ids)")->save($data);
    		if ($rs !== false) {
    			$this->success('批量删除成功！');
    		} else {
    			$this->error('删除失败！');
    		}
    	}    	
    }

    /**
     * 准考证 打印日志
     */
    public function printLog(){
    	$model 		= D('PrintLog');
    	$where 		= array();
    	$name  		= I('name','','trim');	//姓名
    	$username   = I('username','','trim');	//用户名
    	if(strlen($name) > 0) $where['name'] = array('like',"%{$name}%");
    	if(strlen($username) > 0) $where['user_name'] = array('like',"%{$username}%");

		$count = $model->where($where)->count();//总页数		
    	$page  = $this->page($count, 15);
		$list  = $model->where($where)
				->limit($page->firstRow.','.$page->listRows)
				->order('id desc')
				->select();
		// echo M()->getLastSql();
        $this->assign('list',$list)
			->assign('pages', $page->show())
			->display('printlog');
    }

    /**
     * 获取搜索条件
     */
    public function getWhere(){
    	$where = array();
    	$status		= I('get.status',2,'intval');
    	$isFree		= I('get.is_free', 0, 'intval');
		$sex 		= I('get.sex',0,'intval');
		$isPay = I('get.is_pay', 3, 'intval');
		$isPayStatus = I('get.is_pay_status', 3, 'intval');
		$education  = I('get.education',0,'intval');
		$unit 		= I('get.unit',0,'intval');
		$job  		= I('get.job',0,'intval');
		$no 		= I('get.no','','trim');
		$tt 		= I('get.tt',1,'intval');
		$is_join	= I('get.is_join',3,'intval');
		if ($status > 0) {
		    if ($status == 6) {
                $where['status'] = 2;
            } elseif ($status == 2){
                $where['is_free'] = 0;
                $where['status'] = 2;
            } else {
                $where['status'] = $status;
            }
        }
        if($isFree > 0) $where['is_free'] = $isFree;

		if($tt!=1) $where['status'] = 1;		
		if($sex > 0) $where['sex'] = $sex;	
		if($education > 0) $where['education'] = $education;	
		if($unit > 0) $where['unit'] = $unit;	
		if($job > 0) $where['job'] = $job;	
    	if(!empty($_GET['start_time'])){
			$where['birth'][] = array('egt',trim($_GET['start_time']));
    	}
    	if(!empty($_GET['end_time'])){
			$where['birth'][] = array('elt',trim($_GET['end_time'].' 23:59:59'));
    	}
		$keyword = I('get.keyword','','safe_replace');
    	if(!empty($keyword)) $where['name|card'] = array('like',"%".$keyword."%");
    	if(!empty($no)) $where['no'] = array('like',"%".$no."%");
    	if($is_join!=3) $where['is_join'] = $is_join;
        if($isPay!=3) $where['is_pay'] = $isPay;
        if($isPayStatus!=3) $where['is_pay'] = $isPayStatus;
    	return $where;
    }
	
	
}