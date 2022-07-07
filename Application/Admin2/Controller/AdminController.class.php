<?php
namespace Admin2\Controller;
use Think\Controller;
class AdminController extends AdminBaseController {

	protected $model;
	
	function _initialize() {
		$this->model = D("Admin");// 取得一个Admin表的对象
		$this->role_model = M("Role");
	}

	
	//管理员列表页
	public function index(){
		$count=$this->model->count();
		$page = $this->page($count, 10);
		
		
		$admin_list = $this->model
		->order("id ASC")
		->limit($page->firstRow . ',' . $page->listRows)
		->select();	

		$this->roles = $this->role_model->getField('id,rolename');//不使用联表查询，查到这样的数组 Array ( [1] => 超级管理员 [2] => 普通管理员 )
		$this->assign('admin_list',$admin_list);
		$this->assign("page", $page->show());
		$this->display();
	}
	
	//管理员添加页
	public function add(){	
	    if(IS_POST){
			$admin = D('Admin');//
			if ($admin->create()) {
				// var_export($admin);exit;
				$rs=$admin->add();
				if($rs){
					$this->success("添加成功！", U("Admin/index"));
				}else{
					$this->error('添加失败！');
				}
			}else{
				$this->error($admin->getError());
			}
		}else{
			$roles = $this->role_model->select();//查询所有角色
			$this->assign('roles', $roles);
			$this->display();
		}
	}
	
	//管理员修改页
	public function edit(){
		$admin = M('Admin');// 取得一个Admin表的对象
		if (IS_POST) {
			if(empty($_POST['password'])){
				unset($_POST['password']);
			}else{
				$_POST['password'] = setPassword($_POST['password']);
			}
			if ($admin->create()) {
				$rs=$admin->save();
				if ($rs!==false) {
					$this->success("保存成功！");
				} else {
					$this->error("保存失败！");
				}
			} else {
				$this->error($admin->getError());
			}
		} else {
			$id = I('get.id',0,'int');  //获取id
			$admin_info = $admin->where('id = '.$id)->find();
			$roles = $this->role_model->select();//查询所有角色
			$this->assign('roles', $roles);
			$this->assign($admin_info);
			$this->display();
		}
		
	}
	
	
	public function del(){
		$id = I('get.id',0,'int'); 
		$admin = M('Admin');// 取得一个Admin表的对象
		$rs=$admin->where('id = '.$id)->delete();
		if($rs){
			$this->redirect('index', array(), 2, '删除成功...');
		}
	}
	
	
}