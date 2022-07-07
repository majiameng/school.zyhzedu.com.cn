<?php 
namespace Home\Model;
use Think\Model;
class UnionPayModel extends Model{
	public $validate;
	public $auto;
	public $redis;
	public function _initialize(){
		$this->validate = false;
		$this->auto 	= false;
		$this->redis 	= D('Common/Redis');
	}
	public function getAccessToken(){
        $info = $this->redis->getCache('resume_base_');
        return $info;
	}
	
	public function getResumeBase($userid){
		if(empty($userid)) $userid = session('user.id');		
		$info = $this->redis->getCache('resume_base_'.$userid);
		if(empty($info)) {
			$info  = $this->where(array('userid'=>$userid))->find();
			$this->setResumeCache($userid, array('base'=>$info));
		}
		return $info;
	}

}
