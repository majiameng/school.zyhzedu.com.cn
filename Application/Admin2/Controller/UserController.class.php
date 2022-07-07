<?php
namespace Admin2\Controller;
use Think\Controller;
class UserController extends AdminBaseController {

	protected $model;
	
	function _initialize() {
		$this->model = D("Admin");// 取得一个Admin表的对象

	}
	
	//管理员修改页
	public function index(){
		$admin = M('Admin');// 取得一个Admin表的对象   
		// var_export(IS_AJAX);exit;
		if (IS_POST) {
			if(empty($_POST['password'])){
				$this->error('请输入密码');
			}
			$data['password'] = setPassword(I('post.password','','trim'));
			
			$rs = $admin->where(array('id'=>session('admin.id')))->data($data)->save();
			
			if ($rs) {
				$this->success('修改成功');
			} else {
				$this->error('密码修改失败');
			}
			exit;
		} 
		$admin_info = $admin->where('id = '.session('admin.id'))->find();
		$this->assign($admin_info);
		$this->display();
	}	
	
	function excelList(){
		$path = '/upload/excel/';
		$list = glob(WEB_PATH . $path.'/*.xlsx');
		$info = array();
		if(!empty($list)) {
			rsort($list);
			foreach($list as $k=>$r){				
				$info[$k]['filesize'] = sizecount(filesize($r));
				$info[$k]['maketime'] = date('Y-m-d H:i:s',filemtime($r));
				$r = iconv('GBK','UTF-8',$r);					
				$name = substr($r,strrpos($r,'/')+1);
				$info[$k]['filename'] = $name;
				$info[$k]['url'] = $path.$name;
			}
		}
		$this->assign('list',$info)
			->display();
	}
	public function check(){
		$unit = D('Common/Recruit')->getCompany();
		foreach($unit as $r){
			$city = $r['city'];
			$map['unit'] = $r['id'];
			$map['city'] = array('neq',$city);		
			$arry = D('Resume')->where($map)->field('*')->select();
			if(empty($arry)) continue;
			foreach($arry as $k=>$v) {				
				D('Resume')->where(array('id'=>$v['id']))->save(array('city'=>$city));
				D('Home/Resume')->setResumeBase($v['userid']);
			}			
			$list[] = $arry;			
		}
		var_export($list);exit;
		
	}

	public function change(){
		$ids = array(684,3763,4626);
		foreach($ids as $r){
			$a[]=$arry = D('Resume')->where(array('id'=>$r))->find();
			D('Home/Resume')->setResumeBase($arry['userid']);
		}
		var_export($a);exit;
		
	}
	
}