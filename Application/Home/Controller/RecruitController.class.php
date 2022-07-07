<?php
namespace Home\Controller;
use Think\Controller;
class RecruitController extends PublicController{
	public function _initialize(){
		$user = session('user');
		if(empty($user)){
			$this->error('请先登录',U('login/index'));
		}
	}
	
	//新闻中心首页
	
	function index(){
		if(!$this->checkSignDate()) {	//检查报名截止时间
			$this->error('报名已经截止');exit;
		}
		$info = D('Resume')->getBaseInfo(session('user.id'));
		if(!empty($info)) {
			$this->assign('base',$info);
		}		
		$where = ['id'=>1];
		$news = D('Article')->where($where)->find();

        $this->getCompany();//获取科室

        var_dump(F('_common/sex'));
        die;
        $this->assign('news',$news)
			->assign('education',F('_common/education'))
			->assign('policy',F('_common/policy'))
			->assign('sex',F('_common/sex'))
			->assign('degree',F('_common/degree'))
			->assign('config',D('Common/Cache')->getConfig()) //配置
			->assign('rnd',session_id().rand(100000,999999))
			->display();	
	}

    public function getCompany()
    {
        //获取科室
        $model = M('Recruit');
        $recruit = $model->field("tp_recruit.id,tp_recruit.company,tp_recruit.job,tp_recruit_job.name")->join('tp_recruit_job on tp_recruit_job.id=tp_recruit.job')->select();
        $recruitJson = [];
        foreach ($recruit as $value) {
            $recruitJson[$value['company']][$value['job']] = $value['name'];
//            if (isset($recruitJson[$value['company']])) {
//                $recruitJson[$value['company']] = array_unique(array_merge($recruitJson[$value['company']], [
//                    $value['job'] => $value['name']
//                ]));
//            } else {
//                $recruitJson[$value['company']] = [
//                    $value['job'] => $value['name'],
//                ];
//            }
        }
        $recruitCompany = D('RecruitCompany')->field("id,name")->select();
        foreach ($recruitCompany as $value) {
            $recruitJson['parent'][$value['id']] = $value['name'];
        }
        $str = "var recruitJson = JSON.parse('" . json_encode($recruitJson)."')";
        $file_name = "./Public/js/recruit_list.js";
        $res = file_put_contents($file_name, $str);
        if($res == false){
            //没有权限
        }
    }
	
    //AJAX提交表单
    function resume(){
		// $end = L('EDIT_END_TIME');
		if(!$this->checkSignDate()) {	//检查报名截止时间
			$this->error('报名已经截止');exit;
		}
        if(IS_AJAX) {			
			$userid 	= session('user.id');			
			$params 	= I('post.');
			$info 		= I('post.info');
			$edu 		= I('post.edu');
			$work 		= I('post.work');	
			$skill 		= I('post.skill');		
			$family 	= I('post.family');

			if(empty($info)) $this->error('请填写个人资料');
			if(empty($edu)) $this->error('请填写教育情况');
			if(empty($work)) $this->error('请填写工作经历');
			if(empty($family)) $this->error('请填写家庭成员');
			
			$base = D('Resume')->getBaseInfo($userid);
			if(empty($base['avatar']) && empty($_FILES['avatar'])) {
				$this->error('请上传个人照片');
			}

			if(!empty($_FILES)) {
				$config['exts'] = array('gif', 'jpg', 'jpeg', 'png', 'bmp');
				$config['maxSize'] = 1024*1024*500; //500KB
				$config['rootPath'] = 'avatar';
				$config['saveRule'] = '';
				$avatar = D('Common/upload')->upload($config);
				if(isset($avatar['msg'])) {
					$this->error($avatar['msg']);
					exit;
				}
//                $info['avatar'] = $avatar['avatar']['file'];
			}
            if(!empty($avatar['avatar'])) {
                $info['avatar'] = $avatar['avatar']['file'];
            }

            // 身份证正面
            $file_field = 'card_front';
            if(isset($params[$file_field.'1'])){
                $info[$file_field] = $params[$file_field.'1'];
            }
            if(!empty($_FILES[$file_field])) {
                $info[$file_field] = $avatar[$file_field]['file'];
            }
            if(empty($info[$file_field])){
                $this->error('请上传身份证正面照片');
            }

            // 身份证反面
            $file_field = 'card_behind';
            if(isset($params[$file_field.'1'])){
                $info[$file_field] = $params[$file_field.'1'];//修改时
            }
            if(!empty($_FILES[$file_field])) {
                $info[$file_field] = $avatar[$file_field]['file'];
            }
            if(empty($info[$file_field])){
                $this->error('请上传身份证反面照片');
            }

            // 学信网照片
            $file_field = 'study_img';
            if(isset($params[$file_field.'1'])){
                $info[$file_field] = $params[$file_field.'1'];//修改时
            }
            if(!empty($_FILES[$file_field])) {
                $info[$file_field] = $avatar[$file_field]['file'];
            }
            if(empty($info[$file_field])){
                $this->error('请上传学信网照片');
            }

            // 执业资格
            $file_field = 'operation_img';
            if(isset($params[$file_field.'1'])){
                $info[$file_field] = $params[$file_field.'1'];//修改时
            }
            if(!empty($_FILES[$file_field])) {
                $info[$file_field] = $avatar[$file_field]['file'];
            }
            if($info['is_operation'] == 1){
                if(empty($info[$file_field])){
                    $this->error('请上传执业资格照片');
                }
            }

//            // 规培证
//            $file_field = 'train_img';
//            if(isset($params[$file_field.'1'])){
//                $info[$file_field] = $params[$file_field.'1'];//修改时
//            }
//            if(!empty($_FILES[$file_field])) {
//                $info[$file_field] = $avatar[$file_field]['file'];//上传时
//            }
//            if($info['is_train'] == 1) {
//                if(empty($info[$file_field])){
//                    $this->error('请上传规培证照片');
//                }
//            }

            //学历
            $graduation_img_array = [];
            $graduation_img = 'graduation_img';
            if(isset($params[$graduation_img.'1'])){
                $graduation_img_array = $params[$graduation_img.'1'];//修改时
            }
            if(!empty($_FILES[$graduation_img])) {
                foreach ($_FILES[$graduation_img]['name'] as $k=>$v){
                    $name = explode(".", $_FILES[$graduation_img]['name'][$k]);
                    $tmp_name = $_FILES[$graduation_img]['tmp_name'][$k];

                    $name = $this->uploadOne($name,$tmp_name,$graduation_img);
                    if($name){
                        $graduation_img_array[] = $name;
                    }
                }
            }

            //技能照片
            $skill_img_array = [];
            $skill_img = 'skill_img';
            if(isset($params[$skill_img.'1'])){
                $skill_img_array = $params[$skill_img.'1'];//修改时
            }
            if(!empty($_FILES[$skill_img])) {
                foreach ($_FILES[$skill_img]['name'] as $k=>$v){
                    $name = explode(".", $_FILES[$skill_img]['name'][$k]);
                    $tmp_name = $_FILES[$skill_img]['tmp_name'][$k];
                    $name = $this->uploadOne($name,$tmp_name,$skill_img);
                    if($name){
                        $skill_img_array[] = $name;
                    }
                }
            }

			//保存 基本信息
			$info = array_map(function($value){
				return trim($value);
			},$info);
			$data = array(
				'base' 	=> empty($base)?$info:array_merge($base,$info),				
				'edu' => array(),
				'work' => array(),
				'family' => array(),
				'skill' => $skill,
				'graduation_img' => $graduation_img_array,
				'skill_img' => $skill_img_array,
			);
			//教育情况
			foreach($edu['school'] as $k=>$val){				
				$arry['school'] 	= trim($val);
				$arry['degree'] 	= intval($edu['degree'][$k]);
				$arry['education'] 	= intval($edu['education'][$k]);
				$arry['start_time'] = trim($edu['start_time'][$k]);
				$arry['end_time'] 	= trim($edu['end_time'][$k]);
				$arry['profession'] = trim($edu['profession'][$k]);
				$arry['full_time'] 	= !isset($edu['full_time'][$k])?'':trim($edu['full_time'][$k]);
				$arry = array_filter($arry);
				if(empty($arry)) continue;
				!isset($arry['full_time'])?$arry['full_time']=0:'';
				$data['edu'][$k] = $arry;
			}
			
			//工作经历			
			foreach($work['company'] as $k=>$val){
				$arry = array();
				$arry['company'] 	= trim($val);
				$arry['job'] 		= trim($work['job'][$k]);
				$arry['start_time'] = trim($work['start_time'][$k]);
				$arry['end_time'] 	= trim($work['end_time'][$k]);
				$arry = array_filter($arry);				
				if(empty($arry)) continue;
				$data['work'][$k] = $arry;
			}
			//家庭成员
			
			foreach($family['relation'] as $k=>$val){
				$arry = array();
				$arry['relation'] 	= trim($val);
				$arry['name'] 		= trim($family['name'][$k]);
				$arry['work_place'] = trim($family['work_place'][$k]);
				$arry['job'] 		= trim($family['job'][$k]);
				$arry = array_filter($arry);				
				if(empty($arry)) continue;
				$data['family'][] = $arry;
			}
			D('Resume')->setResumeCache($userid, $data); //保存到缓存	
			$this->success('保存成功',U('recruit/confirm'));			
        }
		redirect(U('user/index'));
    }

    // 上传多张照片
    public function uploadOne($name,$tmp_name,$file_name)
    {
        $dir = "./upload/".$file_name."/".date("Y-m-d")."/";
        $name = $dir. md5(NOW_TIME).rand(11111,99999).'.'.end($name);
        if(!file_exists($dir)) {
            @mkdir($dir,0777,true);
        }
        if(!is_writable($dir)){
            @chmod($dir,0777);
        }

        /* 移动文件 */
        if (!move_uploaded_file($tmp_name, $name)) {
            $this->error('图片上传失败！');
        }
        return $name;
    }
		
	/*
	** status 1成功 3失败
	*/
	public function confirm(){
		// $end = L('EDIT_END_TIME');
		if(!$this->checkSignDate()) {	//检查报名截止时间
			$this->error('报名已经截止');exit;
		}
		$userid = session('user.id');
		$info = D('Resume')->getBaseInfo($userid);
		if(empty($info)) {
			$this->error('您还没有报名',U('recruit/agreement'));exit;
		}
		if(isset($info['status']) && in_array($info['status'], array(1,5))) {
			redirect(U('review/index'));exit;
		}
		$id = isset($info['id'])?$info['id']:0;
		$data = D('Resume')->getMore($userid,$id);
		if(IS_AJAX){ 	//审核中

			$is_insert = true;
			if($id) {
				$id = $info['id'];
				$is_insert = false;
			}
			$info['status']  = 2;
			$unit = D('Common/Recruit')->getCompany();
			$info['city']    = $unit[$info['unit']]['city'];
			if($is_insert) {
				$info['addtime'] = time();
				$info['userid']  = $userid;
				D('Resume')->create($info);
				$info['id'] = $id = D('Resume')->add();
			}else{
				$info['uptime'] = time();
				D('Resume')->create($info);
				D('Resume')->where(array('id'=>$id))->save();
			}
			if(empty($id)) $this->error('保存失败，请稍后重试！');
			$list['base'] = $info;
			//教育情况 edu
			$edu = $data['edu'];
			D('ResumeEdu')->where(array('pid'=>$id))->delete();
			foreach($edu as $k=>$val){					
				$val['pid'] = $id;				
				D('ResumeEdu')->create($val);
				D('ResumeEdu')->add();
				$list['edu'][$k] = $val;
			}			
			
			//工作经历
			$work = $data['work'];
			D('ResumeWork')->where(array('pid'=>$id))->delete();
			foreach($work as $k=>$val){
				$val['pid'] = $id;
				D('ResumeWork')->create($val);
				D('ResumeWork')->add();
				$list['work'][$k] = $val;
			}
			
			//家庭成员
			$family = $data['family'];
			D('ResumeFamily')->where(array('pid'=>$id))->delete();
			foreach($family as $k=>$val){
				$val['pid'] = $id;
				D('ResumeFamily')->create($val);
				D('ResumeFamily')->add();
				$list['family'][$k] = $val;
			}

			//专业技能
			$skill = $data['skill'];
			$skill['pid'] = $id;
			if($is_insert) {
				D('ResumeSkill')->create($skill);
				D('ResumeSkill')->add();
			}else{
				D('ResumeSkill')->create($skill);
				D('ResumeSkill')->where(array('pid'=>$id))->save();
			}
			$list['skill'] = $skill;

            //资质上传
            $val = [
                'pid'=>$id,
                'graduation_img'=>empty($data['graduation_img']) ? '' : implode(',',$data['graduation_img']),
                'skill_img'=>empty($data['skill_img']) ? '' : implode(',',$data['skill_img']),
                'operation_img' => empty($info['operation_img']) ? '' : $info['operation_img'],
//                'train_img' => empty($info['train_img']) ? '' : $info['train_img'],
            ];
            D('ResumeQualifications')->where(array('pid'=>$id))->delete();
            D('ResumeQualifications')->create($val);
            D('ResumeQualifications')->add();

            unset($data);

			D('Resume')->setResumeCache($userid,$list);
			$this->success('报名成功');	
			exit;
		}	
		
		//echo "输出一下单位内容:";
		//print_r(D('Common/Recruit')->getCompany());
			
		$this->assign('base',$info)
			->assign('edu',$data['edu'])
			->assign('work',$data['work'])
			->assign('family',$data['family'])
			->assign('skill',$data['skill'])
			->assign('graduation_img',$data['graduation_img'])
			->assign('skill_img',$data['skill_img'])
			->assign('policy',F('_common/policy'))
			->assign('education',F('_common/education'))
			->assign('sex',F('_common/sex'))
			->assign('marriage',F('_common/marriage'))			
			->assign('is_huibi',F('_common/is_huibi'))			
			->assign('full_time',F('_common/full_time'))			
			->assign('degree',F('_common/degree'))			
			->assign('unit',D('Common/Recruit')->getCompany())
			->assign('job',D('Common/Recruit')->getJob())
			->assign('config',D('Common/Cache')->getConfig()) //配置
			->display();
	}

    public function modify(){
		// $end = L('EDIT_END_TIME');		
		if(!$this->checkSignDate()) {	//检查报名截止时间
			$this->error('报名已经截止');exit;
		}
        //渲染模版
        $info = D('Resume')->getBaseInfo(session('user.id'));
		if(empty($info)) {
			redirect(U('recruit/agreement'));
		}
		if(in_array($info['status'],array(1,5))) {
			redirect(U('review/index'));exit;
		}
		$id = isset($info['id'])?$info['id']:0;
		$data = D('Resume')->getMore(session('user.id'),$info['id']);

        $this->getCompany();//获取科室

		$this->assign('base',$info)
			->assign('edu',$data['edu'])
			->assign('work',$data['work'])
			->assign('family',$data['family'])
			->assign('skill',$data['skill'])
            ->assign('resume_qualifications',$data['resume_qualifications'])
            ->assign('graduation_img',$data['graduation_img'])
            ->assign('skill_img',$data['skill_img'])
			->assign('sex',F('_common/sex'))
			->assign('education',F('_common/education'))
			->assign('policy',F('_common/policy'))
			->assign('degree',F('_common/degree'))
            ->display();
    }
	
	/*
	** 
	*/
	public function detail(){	
		$userid = session('user.id');
		$info = D('Resume')->getBaseInfo($userid);	
		if(empty($info)|| !isset($info['status'])) {
			$this->error('您还没有报名',U('recruit/agreement'));exit;
		}		
		$id = isset($info['id'])?$info['id']:0;
		$data = D('Resume')->getMore($userid,$id);			
		$this->assign('base',$info)
			->assign('edu',$data['edu'])
			->assign('work',$data['work'])
			->assign('family',$data['family'])
			->assign('skill',$data['skill'])
            ->assign('graduation_img',$data['graduation_img'])
            ->assign('skill_img',$data['skill_img'])
			->assign('policy',F('_common/policy'))
			->assign('education',F('_common/education'))
			->assign('sex',F('_common/sex'))
			->assign('marriage',F('_common/marriage'))			
			->assign('is_huibi',F('_common/is_huibi'))			
			->assign('full_time',F('_common/full_time'))			
			->assign('degree',F('_common/degree'))			
			->assign('unit',D('Common/Recruit')->getCompany())
			->assign('job',D('Common/Recruit')->getJob())
			->assign('config',D('Common/Cache')->getConfig()) //配置
			->display();
	}
		
	/*
	** status 1 通过;2审核中;3失败;4信息不全;
	*/
	public function agreement() {
		$userid = session('user.id');
		$info = D('Resume')->getBaseInfo($userid);		
		if(empty($info) || !isset($info['status'])) {	
			$where = [];
			$where = ['id'=>2];
			$info = D('Article')->where($where)->find();		
			$more = D('ArticleData')->where($where)->find();		
			$this->assign('info',$info)->assign('more',$more)->display();
			exit;
		}		
		// 有审核结果
		
		redirect(U('Recruit/index'));
		exit;
	}	
	/*
	** 
	*/
	public function explain() {
		$where = [];
		$where = ['id'=>1];
		$info = D('Article')->where($where)->find();		
		$more = D('ArticleData')->where($where)->find();		
		$this->assign('info',$info)
			->assign('more',$more)
			->display();
	}
}
?>