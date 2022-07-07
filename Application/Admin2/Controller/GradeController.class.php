<?php
namespace Admin2\Controller;
use Think\Controller;
class GradeController extends AdminBaseController {	
	protected $model;
	protected $data_model;
	protected $cat_model;
	
	function _initialize() {
		
	}	
    public function index(){
    	$this->_list();		
        $this->display();
    }    

    //考虑到回收站和普通列表逻辑相同，故放在一个方法里
    private function _list(){
		$where = array();
		$unit = I('get.unit',0,'intval');
		$job  = I('get.job',0,'intval');
    	if(!empty($unit)) $where['unit'] = $unit;
    	if(!empty($job)) $where['job'] = $job;
		$model = D('Common/Grade');
    	$count = $model->where($where)->count();//总页数
    	$page  = $this->page($count, 20);   	
    	$list  = $model->order('id DESC')->where($where)
					->limit($page->firstRow.','.$page->listRows)
					->select(); 
    	$this->assign('pages', $page->show());
    	$this->assign('list', $list);   	
    	$this->assign('company', D('Common/Recruit')->getCompany());   	
    	$this->assign('job', D('Common/Recruit')->getJob());   	
    }
	
    public function add(){
		if(IS_POST){   
			$unit = I('post.unit',0,'intval');
			$job  = I('post.job',0,'intval');
			$score  = I('post.score',0,'floatval');
    		if(empty($unit)){
    			$this->error("请选择系部名称");
    		} 
			if(empty($job)){
    			$this->error("请选择报考岗位");
    		}  
			if(empty($score)){
    			$this->error("请填写分数");
    		}   
			$model = D('Common/Grade');
			$map['unit'] = $unit;
			$map['job']  = $job;
			$r = $model->where($map)->getField('id');			
			if($r) $this->error("该岗位分数已经存在");
			$data['unit']	 = $unit;
			$data['job']	 = $job;
			$data['desc'] 	 = I('post.desc','','trim');
			$data['score'] 	 = $score;
			    		  		
    		//数据添加到数据库    		
    		if($model->create($data)){
    			$r = $model->add();
    			if($r) {
    				$this->success('添加成功！');exit;
    			}
				$this->error('添加失败！');
    		}else {
    			$this->error($model->getError());
    		}    		
    	} 
        $this->display();
    }
      
    public function edit(){
    	$id=  I('param.id',0,'intval');		
		if(IS_POST){    
    		$unit = I('post.unit',0,'intval');
			$job  = I('post.job',0,'intval');
			$score  = I('post.score',0,'floatval');
    		if(empty($unit)){
    			$this->error("请选择系部名称");
    		} 
			if(empty($job)){
    			$this->error("请选择报考岗位");
    		}  
			if(empty($score)){
    			$this->error("请填写分数");
    		}   
			$model = D('Common/Grade');
			$r = $model->where(array('id'=>$id))->getField('id');			
			if(empty($r)) $this->error("记录不存在");
			
			$map['unit'] = $unit;
			$map['job']  = $job;
			$map['id']   = array('neq',$id);			
			$r = $model->where($map)->getField('id');			
			if($r) $this->error("该岗位分数已经存在");
			
			$data['unit']	 = $unit;
			$data['job']	 = $job;
			$data['desc'] 	 = I('post.desc','','trim');
			$data['score'] 	 = $score;
			
    		$r = $model->where(array('id'=>$id))->save($data);
    		if($r){
    			$this->success('更新成功！');
    		}else {
    			$this->error('更新失败！');
    		}  
			exit;
    	}
		if(empty($id)) {
			$this->error('记录不存在');
		}
        $info =  D('Common/Grade')->where(array('id'=>$id))->find();        
		$this->assign('info',$info)->display();
    }
        
    public function del(){   	
		$ids = I('request.id');		
		if(!is_array($ids)) {
			$ids = (array)$ids;
		}
		if(empty($ids)) {
			$this->error('请选择要删除的信息');
		}	
    	$map['id'] = array('in',$ids);
		$rs = D('Common/Grade')->where($map)->delete();
		if ($rs !== false) {
			$this->success('删除成功！');
		} else {
			$this->error('删除失败！');
		}    	
    } 	
   
	
}