<?php
namespace Admin2\Controller;
use Think\Controller;
class RecruitController extends AdminBaseController {
	protected $model;
	
	function _initialize() {
		set_time_limit(0); 
		$roleid = session('admin.role_id');
		$city = session('admin.city');
		if($roleid !=1) {
			$company = D('Recruit')->city_company();			
			$this->assign('unit',$company[$city]);
		}else{
			$this->assign('unit',D('Common/Recruit')->getCompany());
		}
	}
	
    public function index(){
		$tt = I('get.tt',1,'intval');
    	$this->_list();
        $this->assign('sex',F('_common/sex'))
			->assign('education',F('_common/education'))
			->assign('recuit_status',F('_common/recuit_status'))
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
		$roleid = session('admin.role_id');
		if($roleid !=1) $where['city'] = session('admin.city');	
		$status		= I('get.status',2,'intval');
		$sex 		= I('get.sex',0,'intval');
		$education  = I('get.education',0,'intval');
		$unit 		= I('get.unit',0,'intval');
		$job  		= I('get.job',0,'intval');
		$no 		= I('get.no','','trim');
		$tt 		= I('get.tt',1,'intval');
		if($status > 0) $where['status'] = $status;
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
    	if(!empty($keyword)){
			$where['name|card'] = array('like',"%".$keyword."%");
    	}  
    	if(!empty($no)){
			$where['no'] = array('like',"%".$no."%");
    	}  	
		$model  = D('Resume');
		if(isset($_GET['do'])) {	
			$num = $model->where($where)->count();
			$page = I('get.page',1,'intval');
			$size = 500;
			if($num >= $size) {
				$offset = ($page-1)*$size;
				$data = $model->order('no asc,id desc')->limit($offset,$size)->where($where)->field('id,userid')->select();
			}else{
				$data = $model->order('no asc,id desc')->where($where)->field('id,userid')->select(); 
			}
			
			if(empty($data)) $this->error('没有查询到导出的信息',U('Recruit/index','status=0'));
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
				'unit'		=>'报考科室',
				'job'		=>'报考岗位',
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
			
			);
			$tpl =  APP_PATH . "../zpxx.xlsx";	
			if($roleid ==1) {
				if(isset($_GET['random'])&&!empty($_GET['random'])) $random = trim($_GET['random']);
				else $random = time();
				$name = 'zpxx_'.date('Y-m-d').'_'.$random.'_'.$page;
				$name = iconv('utf-8','gbk',$name);
				$obj = new \Org\Util\ExcelReader($name);	
				$r = $obj->export($data,$cnTable,$tpl,'12','2007',false);
				if($r) {
					$query = $_SERVER['QUERY_STRING']."&random=$random&page=".($page+1);
					$url = U('Recruit/index',$query);
					echo '导出成功，继续下一卷';
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
				->field('s.score,r.name,r.id,r.userid,r.sex,r.no,r.exam_no,r.unit,r.job,r.school')
				->select(); 
		}else{
			$list = $model->where($where)
				->limit($page->firstRow.','.$page->listRows)
				->order('cast(exam_no as signed) asc,cast(no as signed) asc')
				->field('name,id,userid,sex,no,exam_no,birth,unit,job,school,education,profession,full_time,is_huibi,profession,status,addtime,is_ok,is_lock')
				->select(); 
		}
		
		$map = [];
		if(isset($where['city'])) $map['city'] = $where['city'];
		$result = $model->where($map)->group('status')->field('status,count(*) as num')->select();
		$result = array_column($result,'num','status');	
		for($i=1;$i<=4;$i++){
			!isset($result[$i])?$result[$i] = 0:'';
		}
		$this->assign('pages', $page->show())
			->assign('list', $list)
			->assign('result', $result)
			->assign("status",$status);   	
    }
	
	/*
	** 准考证区间
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
		$where['status'] = 1;$page = I('get.p',1,'intval');
		$model = D('Resume');$size = 500;
		
		$count = $model->where($where)->count();//总页数
    	$page  = $this->page($count, $size);
		$list = $model->where($where)
			->limit($page->firstRow.','.$page->listRows)
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
		if(IS_AJAX) {
			$roleid = session('admin.role_id');
			if($roleid!=1) {
				$this->error('您没有审核权限');
			}
			if(empty($info)) {
				$this->error('报名信息不存在');
			}				
			$remark = I('post.remark','','safe_replace');
			$status = I('post.status',0,'intval');					
			if(empty($status)) {
				$this->error('请选择审核结果');
			}
			if($status==1&&$info['status']!=1) $data['no']=D('Recruit')->getCardNo($info['unit'],$info['job']);
			 //&& empty($info['no'])
			$data['status'] = $status;
			if($status==4 || $status==3) {
				$data['remark'] = $remark;
				$data['is_ok']	= 1;
				
			}else{
				$data['remark'] = '';
			}
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
	
	/*
	** 分配考场
	*/	
	public function exam(){
		$where = array(
			'is_lock' =>0,
			'status'  =>1,
			// 'exam_no' =>'',
		);	
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
			'unit'		=> '报考科室',
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
		$map['status'] = 1;	
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
        $list = M('Resume')->where($where)->field('name,unit,job,no,card,avatar')->select();
        if(empty($list)) $this->error('没有相关信息');	
        $this->assign('list',$list)
			->assign('unit',D('Common/Recruit')->getCompany())
			->assign('job',D('Common/Recruit')->getJob())
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
	public function export_bao_xls(){
		$no = F('_common/export_bao');
	
		if(empty($no)) $this->error('导出数据为空');
		$where['status'] = 1;
		$where['no']	 = array('in',implode(',',$no));
		$p = I('get.p',1,'intval');
		
		$count = D('resume')->where($where)->count();//总页数		
    	$page  = $this->page($count, 1);
						
		$r = D('resume')->where($where)
			->limit($page->firstRow.','.$page->listRows)
			->field('id')->select();
		$r = array_shift($r);
		if(!empty($r)) {
			$obj = new \Org\Util\ExcelReader('报名表');
			$info = $this->excel_data($r['id']);
			// var_export($info);exit;
			$rr = $obj->getExcelBao($info,'12',"other",false);
			
			if($rr) {
				$url = U('Recruit/export_bao_xls',array('p'=>$p+1));
				$this->success('成功 下一条：'.$p+1,$url);exit;
			}
		}
		else{
			$this->success('全部导出成功',U('Recruit/export_bao'));exit;
		}
		
		
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
		$base['full_time'] 	= $full_time[$base['full_time']];
		$base['unit'] 		= $unit[$base['unit']]['name'];
		$base['job'] 		= $job[$base['job']];
		$base['political_status']= $policy[$base['political_status']];
		$info['base'] = $base;	
		$obj = new \Org\Util\ExcelReader('报名表');
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
		if($roleid !=1) $where['city'] = $city;		
		$model  = D('Resume');
		if(isset($_GET['do'])) {
			$where['status'] = 1;
			 // $data=$model->order('id DESC')->where($where)->field('name,id,sex,no,exam_no,birth,unit,job,school,education,profession,full_time,card,address,is_huibi,profession,status,mobile,tel,email')->select();
			 
			// $data=$model->order('id DESC')->where($where)->select();
			$data=$model->order('cast(exam_no as signed) asc,cast(no AS signed) asc')->where($where)->field('id,userid,exam_no,no,unit')->select();
			// $data=$model->order('exam_no asc,no asc')->where($where)->field('id,userid')->select();
			$sex        = F('_common/sex');  
			$education  = F('_common/education');
			$company  	= D('Common/Recruit')->getCompany();
			$job  		= D('Common/Recruit')->getJob();
			$policy  	= F('_common/policy');//政治面貌
			$full_time  = F('_common/full_time');
			$is_huibi   = F('_common/is_huibi');
			$degree     = F('_common/degree');
			$model = D('Home/Resume');
			foreach($data as $k=>$r){				
				$r = $model->getBaseInfo($r['userid']);
				$data[$k]				 = $r;			
				$data[$k]['sex']         = $sex[$r['sex']];
				$data[$k]['education']   = $education[$r['education']];
				$data[$k]['degree']   	 = $degree[$r['degree']];
				$data[$k]['unit']     	 = $company[$r['unit']]['name'];
				$data[$k]['job']      	 = $job[$r['job']];
				$data[$k]['age']  		 = birthday($r['birth']);
				$data[$k]['full_time']   = $full_time[$r['full_time']];
				$data[$k]['political_status']= $policy[$r['political_status']];
				$data[$k]['is_huibi']    = $is_huibi[$r['is_huibi']];
				$data[$k]['renhang']   	 = '';
				$data[$k]['remark']   	 = '';
				
				$edu = $model->getResumeEdu($r['id'], $r['userid']);
				array_multisort(array_column($edu,'education'),SORT_DESC,$edu);
				$first = $edu[0];
				$data[$k]['max_education']   = $education[$first['education']];
				$data[$k]['max_full_time']   = $full_time[$first['full_time']];
				$data[$k]['max_profession']  = $first['profession'];
				$data[$k]['max_school'] 	 = $first['school'];
			}    		
			$cnTable = array(
				'unit'		=>'报考科室',
				'job'		=>'报考岗位',				
				'no'		=>'准考证号',
				'exam_no'	=>'考场',
				'name'		=>'姓名',
				'sex'		=>'性别',				
				'birth'		=>'生日',				
				'age'		=>'年龄',				
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
			unset($data,$edu,$first,$cnTable,$region);
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
				$data = array();
				$log[$k]['is_ok']   = 0;
				$log[$k]['no']		= $no = trim($r[0]); 
				$log[$k]['score']	= $data['score']  = floatval($r[5]); 
				$log[$k]['addtime'] = $data['uptime'] = time();
				if(empty($no)){
					$log[$k]['msg']  = '准考证号为空';
					$fail_list[] = $data;
					continue;
				}					
				$where['status'] = 1;
				$where['no']	 = $no;
				$userid = D('resume')->where($where)->getField('userid');			
				if(empty($userid)) {
					$log[$k]['msg']  = '准考证号不存在';
					$fail_list[] = $data;
					continue;
				}
				D('score')->where('1')->delete();
				$data['userid'] = $userid; 
				$rs = D('score')->add($data);
				$log[$k]['is_ok'] = $rs?1:0;
				$log[$k]['msg']   = $rs?'成功':'导入失败';
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
	
	
	
}