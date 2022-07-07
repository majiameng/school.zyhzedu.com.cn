<?php 
namespace Home\Controller;
use Think\Controller;
class ReviewController extends PublicController{
	public function _initialize(){
		$user = session('user');		
		if(empty($user)){
			$this->error('请先登录',U('login/index'));
		}
	}
	//新闻中心首页
	function index(){
//		$data = D('Resume')->getBaseInfo(session('user.id'));
        $data = D('Resume')->where(array('userid'=>session('user.id')))->find();
        if(empty($data)||!isset($data['status'])) $this->error("您还没有报名",U('recruit/agreement'));
		//is_join 确认参加考试 1是;0否
		$template = $data['is_join']==0?'index':'confirm-join';
		$config = D('Common/Cache')->getConfig();
		if(!empty($config)) {
			$config['confirm_start_date'] = format_date($config['confirm_start_date']);
			$config['confirm_end_date'] = format_date($config['confirm_end_date']);
			$config['print_start_date'] = format_date($config['print_start_date']);
			$config['print_end_date'] = format_date($config['print_end_date']);
		}
      //$now = time();
     // var_export($now >= strtotime($config['print_start_date']) && $now <= strtotime($config['print_end_date']));
		$this->assign('data',$data)
			->assign('now',time())  //现在时间
			->assign('config',$config)  //更新配置缓存
			->display($template);
	
	}

	function notice(){
		$r = D('Resume')->getBaseInfo(session('user.id'));
		$notice = F('_common/audit_notice');
		$this->assign('notice',$notice[$r['city']])
			->display();
	
	}
	
    function ticket(){
		$data = D('Resume')->getBaseInfo(session('user.id'));
		if(empty($data)||!isset($data['status'])) redirect(U('recruit/agreement'));	
		if($data['status']!=1) redirect(U('review/index'));	
		
        //渲染模版
        $this->assign('base',$data)
			->assign('unit',D('Common/Recruit')->getCompany())
			->assign('job',D('Common/Recruit')->getJob())
			->assign('config',D('Common/Cache')->getConfig())
			->display();
    }
	
	public function pdf(){	
		$data = D('Resume')->getBaseInfo(session('user.id'));
		if(empty($data)||!isset($data['status'])) redirect(U('recruit/agreement'));	
		if($data['status']!=1) redirect(U('review/index'));	
		$html = $this->assign('base',$data)
			->assign('unit',D('Common/Recruit')->getCompany())
			->assign('job',D('Common/Recruit')->getJob())
			->assign('config',D('Common/Cache')->getConfig())
			->fetch(); 		
		D('Common/Recruit')->pdf($html);
	}

	//分数查询
	function score(){
		$uid = session('user.id');
		$is_on = L('SCORE_SEARCH_ON');
		if(!$is_on) {
			 $this->error("暂时不能查询成绩",U('index/index'));
		}
		$info = D('Admin2/Score')->where(array('userid'=>$uid))->find();		
        if(empty($info)) $this->error("您还没有成绩",U('index/index'));		
       	$base = D('Resume')->getBaseInfo($uid);
		$map['unit'] = $base['unit'];
		$map['job'] = $base['job'];
       	$grade =  D('Common/Grade')->where($map)->find();
        $this->assign('info',$info)
			->assign('company', D('Common/Recruit')->getCompany())  	
			->assign('job', D('Common/Recruit')->getJob())   	
			->assign('base', $base)   	
			->assign('grade', $grade)   	
            ->display();
    }

    /**
     * 记录打印日志
     */
    public function confirm(){
    	$uid = session('user.id');
    	$map = array('userid' =>$uid);
    	$base = D('Resume')->where($map)->field('name,is_join,is_pay')->find();
    	if(empty($base)) $this->error('请先报名');
        if($base['is_pay'] == 0){
            $this->error('请先支付考试服务费！');
        }
    	$data = array();
    	$data['user_id'] 		= $uid;
    	$data['user_name'] 		= session('user.username');
    	$data['name'] 	 		= $base['name'];
    	$data['create_time'] 	= time();
    	$res = D('PrintLog')->data($data)->add();
    	
    	if(!$base['is_join']) {		//更新报名信息
    		$data= array('is_join'=>1,'uptime'=>time());
    		$res = D('Resume')->where($map)->data($data)->save();
			D('Home/Resume')->setResumeBase($uid);//更新报名缓存
    	}    	
    	if($res) {
    		$this->success('操作成功',U('review/index'));
    	}
    	$this->error('网络繁忙请稍后再试！');
    }
}
?>