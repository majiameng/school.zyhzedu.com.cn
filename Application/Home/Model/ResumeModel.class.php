<?php 
namespace Home\Model;
use Think\Model;
class ResumeModel extends Model{
	public $validate;
	public $auto;
	public $redis;
	public function _initialize(){
		$this->validate = false;
		$this->auto 	= false;
		$this->redis 	= D('Common/Redis');
	}
	public function getBaseInfo($userid){
		if(empty($userid)) $userid = session('user.id');		
		return $this->getResumeBase($userid);
	}
	
	public function getMore($userid,$pid=0){
		if(empty($userid)) $userid = session('user.id');
		$data = array();
		$data['edu']  	= $this->getResumeEdu($pid,$userid);		
		$data['family'] = $this->getResumeFamily($pid,$userid);		
		$data['skill']  = $this->getResumeSkill($pid,$userid);
		$data['work']   = $this->getResumeWork($pid,$userid);
        $data['graduation_img']   = $this->getGraduationImg($pid,$userid);
        $data['skill_img']   = $this->getSkillImg($pid,$userid);
        $data['resume_qualifications']   = $this->getQualifications($pid,$userid);
		return $data;
	}
	
	public function getResumeBase($userid){
		if(empty($userid)) $userid = session('user.id');
        //tinymeng  redis 获取缓存中报名信息
//		$info = $this->redis->getCache('resume_base_'.$userid);
		if(empty($info)) {
			$info  = $this->where(array('userid'=>$userid))->find();
			$this->setResumeCache($userid, array('base'=>$info));
		}
		return $info;
	}

    public function getResumeQualifications($userid){
        if(empty($userid)) $userid = session('user.id');
        $resume_qualifications_data = $this->redis->getCache('resume_qualifications_data'.$userid);
        if(empty($resume_qualifications_data)) {
            $info  = $this->where(array('userid'=>$userid))->find();
            if (!empty($info)) {
                $resume_qualifications_data = M('ResumeQualifications')->where(['pid' => $info['id']])->find();
            }
            $this->setResumeCache($userid, array('resume_qualifications_data' => $resume_qualifications_data));
        }
        return $resume_qualifications_data;
    }

	public function getResumeEdu($pid,$userid=0){
		if(empty($userid)) $userid = session('user.id');		
		$info = $this->redis->getCache('resume_edu_'.$userid);
	
		if($info===false) {
			$info  = D('ResumeEdu')->where(array('pid'=>$pid))->order('id asc')->select();
			$this->setResumeCache($userid, array('edu'=>$info));
		}		
		return $info;
	}
	
	public function getResumeFamily($pid,$userid=0){
		if(empty($userid)) $userid = session('user.id');		
		$info = $this->redis->getCache('resume_family_'.$userid);
		if($info===false) {
			$info  = D('ResumeFamily')->where(array('pid'=>$pid))->order('id asc')->select();
			$this->setResumeCache($userid, array('family'=>$info));
		}
		return $info;
	}
	
	public function getResumeWork($pid,$userid=0){
		if(empty($userid)) $userid = session('user.id');		
		$info = $this->redis->getCache('resume_work_'.$userid);
		if($info===false) {
			$info = D('ResumeWork')->where(array('pid'=>$pid))->order('id asc')->select();
			$this->setResumeCache($userid, array('work'=>$info));
		}
		return $info;
	}
	
	public function getResumeSkill($pid,$userid=0){
		if(empty($userid)) $userid = session('user.id');		
		$info = $this->redis->getCache('resume_skill_'.$userid);
		if($info===false) {
			$info = D('ResumeSkill')->where(array('pid'=>$pid))->order('id asc')->find();
			$this->setResumeCache($userid, array('skill'=>$info));
		}
		return $info;
	}

    public function getGraduationImg($pid,$userid=0){
        if(empty($userid)) $userid = session('user.id');
        $info = $this->redis->getCache('resume_graduation_img_'.$userid);
        if(empty($info)) {
			$info = D('ResumeQualifications')->where(array('pid'=>$pid))->order('id asc')->find();
            $info = empty($info['graduation_img']) ? [] : explode(',',$info['graduation_img']);
			$this->setResumeCache($userid, array('graduation_img'=>$info));
        }
        return $info;
    }
    public function getSkillImg($pid,$userid=0){
        if(empty($userid)) $userid = session('user.id');
        $info = $this->redis->getCache('resume_skill_img_'.$userid);
        if(empty($info)) {
            $info = D('ResumeQualifications')->where(array('pid'=>$pid))->order('id asc')->find();
            $info = empty($info['skill_img']) ? [] : explode(',',$info['skill_img']);
            $this->setResumeCache($userid, array('skill_img'=>$info));
        }
        return $info;
    }

    public function getQualifications($pid,$userid=0){
        if(empty($userid)) $userid = session('user.id');
        $info = $this->redis->getCache('resume_qualifications'.$userid);
        if(empty($info)) {
            $info = D('ResumeQualifications')->where(array('pid'=>$pid))->order('id asc')->find();
            $info = empty($info) ? [] : $info;
            $this->setResumeCache($userid, array('resume_qualifications'=>$info));
        }
        return $info;
    }
	
	public function setResumeBase($userid,$info=array()){		
		if(empty($userid)) $userid = session('user.id');		
		if(empty($info)) {
			$info = $this->where(array('userid'=>$userid))->find();
		}
		$this->setResumeCache($userid, array('base'=>$info));
		return true;
	}
	
	public function setResumeCache($userid,$info=array()){

		if(empty($userid)) $userid = session('user.id');
		if(isset($info['base'])) {
			$this->redis->setCache('resume_base_'.$userid, $info['base']);
		}			
		if(isset($info['edu'])) {
			$this->redis->setCache('resume_edu_'.$userid, $info['edu']);
		}		
		if(isset($info['work'])) {
			$this->redis->setCache('resume_work_'.$userid, $info['work']);
		}			
		if(isset($info['family'])) {
			$this->redis->setCache('resume_family_'.$userid, $info['family']);
		}			
		if(isset($info['skill'])) {
			$this->redis->setCache('resume_skill_'.$userid, $info['skill']);
		}
		if(isset($info['graduation_img'])) {
            $this->redis->setCache('resume_graduation_img_'.$userid, $info['graduation_img']);
		}
		if(isset($info['skill_img'])) {
			$this->redis->setCache('resume_skill_img_'.$userid, $info['skill_img']);
		}
        if(isset($info['resume_qualifications_data'])) {
            $this->redis->setCache('resume_qualifications_data'.$userid, $info['resume_qualifications_data']);
//            $resume = M('Resume')->where(['userid' => $userid])->find();
//            if (!empty($resume)) {
//                $data = M('ResumeQualifications')->where(['pid' => $resume['id']])->find();
//                $this->redis->setCache('resume_resume_qualifications_data'.$userid, $data);
//            }
        }
        if(isset($info['resume_qualifications'])) {
            $this->redis->setCache('resume_resume_qualifications_'.$userid, $info['resume_qualifications']);
        }
		return true;
	}
	
	public function clearCache($userid) {
		if(empty($userid)) $userid = session('user.id');
		S('resume_base_'.$userid,null);
		S('resume_edu_'.$userid,null);
		S('resume_work_'.$userid,null);
		S('resume_skill_'.$userid,null);
		S('resume_family_'.$userid,null);
		S('resume_graduation_img_'.$userid,null);
		S('resume_skill_img_'.$userid,null);
	}
	
}
