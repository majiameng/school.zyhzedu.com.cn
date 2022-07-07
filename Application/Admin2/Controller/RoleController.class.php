<?php
/**
 * 角色控制器
 */
namespace Admin2\Controller;
use Think\Controller;
class RoleController extends AdminBaseController {

	protected $model;
	
	function _initialize() {
		//获取权限表所有信息
		$auth_all = M('auth')->where('pid=0')->select();
		foreach($auth_all as $k=>&$v){
			$v['son']=M('auth')->where('pid='.$v['id'])->select();
			foreach($auth_all[$k]['son'] as $kk=>&$vv){
				$vv['sonson']=M('auth')->where('pid='.$vv['id'])->select();
			}
		}
		$this->auth_all = $auth_all;
	}

	
	//角色列表页
	public function index(){
		$rolelist = M('Role')->select(); //实例化角色
		$this->assign('rolelist',$rolelist);
		$this->display();
	}
	
	//角色添加
    public function add(){
        if(IS_POST){
            $role = M('Role');
            $data['rolename'] = I('post.rolename');
            $data['auth_ids'] = implode(I('post.auth_ids'),',');
            if($role->add($data)){
                $this->success('添加成功',U('Role/index'));
            }else{
                $this->error('添加失败');
            }
        }else{
            $this->display();
        }
    }
	
	//角色  修改
    public function edit(){
    	$role =M('Role');  //实例化表
        if(IS_POST){
            $id = I('post.id',0,'int');
            $data['rolename'] = I('post.rolename');
            $data['auth_ids'] = implode(I('post.auth_ids'),',');
            if($role->where('id='.$id)->save($data) !== false){
                 $this->success('修改成功',U('Role/index'));
            }else{
                $this->error('修改失败',U('Role/index'));
            }
            
        }else{
            $id = I('get.id',0,'int');  //获取id
            $data = $role->where('id='.$id)->find(); //查询需修改的数据
            $data['auth_ids']= explode(',',$data['auth_ids']);  //将权限内容的格式转化为数组
            $this->assign('data',$data);
            $this->display();
        }
    }
	
	public function del(){
		$id=isset($_GET['id'])?$_GET['id']:0;
		$admin = M('Admin');// 取得一个Admin表的对象
		if($admin->where('role_id='.$id)->find()){
            $this->error('删除失败,有管理员选择该权限',U('Role/index')); //不能删除，有管理员选择该权限
       	}else{
       		if(M('Role')->where('id='.$id)->delete()){
       			$this->success('删除成功',U('Role/index')); //删除成功
       		}else{
       			$this->error('删除失败');
            }
       	}
	}
	
	
}