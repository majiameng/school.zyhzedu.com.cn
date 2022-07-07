<?php
/**
 * 权限控制器
 */
namespace Admin2\Controller;
use Think\Controller;
class AuthController extends AdminBaseController {

	
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

	
	//权限列表页
	public function index(){
		$this->assign('auth_status',F('_common/auth_status'))
			->display();
	}
	
	//权限添加
    public function add(){
        if(IS_POST){
            $model = M('Auth');
            $data = I('post.');//收集表单数据，缺少一个level(当前权限级别)
            //level有两种情况，如果pid等于0，则level等于0
            //如果pid不等于0，可以根据pid查找父级权限的level，当前level等于父级level+1
            if($_POST['pid'] == 0){
            	$data['level'] = 0;
            }else{
            	$auth_p = $model->field('level')->find($_POST['pid']);//查询父级权限的level
            	$data['level'] = $auth_p['level']+1;            	
            }            
            if($model->add($data)){
                $this->success('添加成功',U('Auth/index'));
            }else{
                $this->error('添加失败');
            }
        }else{
        	$pid = I('get.pid',0,'int');
        	$this->assign('pid',$pid)
				->assign('auth_status',F('_common/auth_status'))
				->display();
        }
    }
	
	//权限  修改
    public function edit(){
    	$model = M('Auth');  //实例化表
        if(IS_POST){
            $id = I('post.id',0,'int');
            $data = I('post.');//收集表单数据，缺少一个level(当前权限级别)
            //level有两种情况，如果pid等于0，则level等于0
            //如果pid不等于0，可以根据pid查找父级权限的level，当前level等于父级level+1
            if($_POST['pid'] == 0){
            	$data['level'] = 0;
            }else{
            	$auth_p = $model->field('level')->find($_POST['pid']);//查询父级权限的level
            	$data['level'] = $auth_p['level']+1;
            }
            if($model->where('id='.$id)->save($data) !== false){
                 $this->success('修改成功',U('Auth/index'));
            }else{
                $this->error('修改失败',U('Auth/index'));
            }
        }else{
            $id = I('get.id',0,'int');  //获取id
            $data = $model->where('id='.$id)->find(); //查询需修改的数据
            $this->assign('data',$data)
				->assign('auth_status',F('_common/auth_status'))
				->display();
        }
    }
	
	public function del(){
		$id=isset($_GET['id'])?$_GET['id']:0;
       	if(M('Auth')->where('id='.$id)->delete()){
       		$this->success('删除成功',U('Auth/index')); //删除成功
       	}else{
       		$this->error('删除失败');
        }
	}
	
	
}