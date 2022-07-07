<?php
namespace Admin2\Controller;
use Think\Controller;
class AdminBaseController extends Controller {
	public function __construct()
	{
		parent::__construct();
		if(!session('?admin')){
			$this->redirect('Login/login');
		}else{
			/*
			$menu_list=C('MENU');
			$this->assign('menu_list',$menu_list);//左侧菜单数组
			*/
			$roleid = session('admin.role_id');
			$map['is_show'] = 1;
			$map['pid'] 	= 0;
			if($roleid!=1) {
				$ids = D('Role')->where(array('id'=>session('admin.role_id')))->getField('auth_ids');
				$map['id'] = array('in',$ids);
			}
			$auth_all = M('auth')->where($map)->order('listorder asc')->select();
			foreach($auth_all as $k=>&$v){
				$map['pid'] 	= $v['id'];
				$v['son']=M('auth')->where($map)->order('listorder asc')->select();
				
				//根据当前控制器名和当前方法名  给遍历过程中遇到的当前页加个标识
				foreach($v['son'] as $kk=>&$vv){
				    if(CONTROLLER_NAME == $vv['auth_c'] && ACTION_NAME == $vv['auth_a']){				        
				        $vv['current'] = 1;
				        $v['current'] = 1;
				    }
				    $map['pid'] 	= $vv['id'];
				    $vv['son']=M('auth')->field('auth_c,auth_a')->where($map)->select();
				    foreach($vv['son'] as $kkk=>&$vvv){
				        if(CONTROLLER_NAME == $vvv['auth_c'] && ACTION_NAME == $vvv['auth_a']){
				            $vv['current'] = 1;
				            $v['current'] = 1;
				        }
				    }
				}
				
				
			}
			$this->menu_list = $auth_all;
			
			$this->assign('admin',session('admin'));
			
			$this->quanxian();//检查当前管理员是否有权限访问其正在访问的页面，目的是阻止没权限的管理员非法访问
			
			//查询当前管理员拥有的权限,目的是判断左边菜单栏显示哪些菜单
			$role_id = session('admin.role_id');
			$admin_role = M('Role')->find($role_id);
			$admin_roles = explode(',',$admin_role['auth_ids']);			
			$this->assign('admin_roles',$admin_roles);
			//$this->display('Public:header');
		}
	}


	/**
	 * 检查当前管理员是否有权限访问其正在访问的页面
	 */
	private function quanxian(){
		//当前管理员拥有的角色id
		$role_id = session('admin.role_id');//角色id
		//根据角色id查询对应的权限id们
		$auth_ids = M('Role')->where('id='.$role_id)->getField('auth_ids');
		//根据权限id们找到对应的权限的控制器和方法们
		$admin_acs = M('Auth')->where('id in ('.$auth_ids.')')->select();
		$auth_ac='';
		foreach($admin_acs as $v){
			$auth_ac.=$v['auth_c'].'/'.$v['auth_a'].','; //得到允许的控制器和方法，类似这样的字符串 Admin/index,Admin/add,Admin/edit,
		}
		
		//当前控制器和方法
		$now_ac = CONTROLLER_NAME."/".ACTION_NAME;
		
		
		//判断$now_ac是否在$auth_ac字符串里边有出现过
		//strpos函数如果返回false是没有出现，返回0 1 2 3表示有出现
		//超级超级管理员不限制
		//默认以下权限没有限制
		//Index/index
		$allow_ac = array('Index/index');
		if(!in_array($now_ac,$allow_ac) && $_SESSION['admin']['id'] !=1 && strpos($auth_ac,$now_ac) === false){
			$this -> error('没有权限访问',U('Index/index'));
		}
		
	}
	
	
	protected function page($page_totle,$perpage = 5) {
        //import('Libaray/Page');
        $Page = new \Libaray\Page($page_totle,$perpage);//实例化分页类
		//$Page->setConfig('first','第一页');

        return $Page;
    }
	
	
	public function _empty(){

	}
	
	
}