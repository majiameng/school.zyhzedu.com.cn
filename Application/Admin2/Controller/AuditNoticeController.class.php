<?php
namespace Admin2\Controller;
use Think\Controller;
class AuditNoticeController extends AdminBaseController {	
	protected $model;
	protected $data_model;
	protected $cat_model;
	
	function _initialize() {
		
	}	
    public function index(){
    	$this->_list();		
        $this->assign('region',F('_common/region'))->display();
    }    

    //考虑到回收站和普通列表逻辑相同，故放在一个方法里
    private function _list(){
		$where = array();
    	if(!empty($_GET['city'])){
    		$city = I('get.city',0,'int');
    		$where['city'] = $city;
    	}   
		$model = D('AuditNotice');
    	$count = $model->where($where)->count();//总页数
    	$page  = $this->page($count, 20);   	
    	$list  = $model->order('id DESC')->where($where)
					->limit($page->firstRow.','.$page->listRows)
					->select(); 
    	$this->assign('pages', $page->show());
    	$this->assign('list', $list);   	
    }
	
    public function add(){
        $this->display();
    }
      
    public function add_post(){       
    	if(IS_POST){   		
    		if(empty($_POST['city'])){
    			$this->error("请选择地区");
    		} 
			if(empty($_POST['address'])){
    			$this->error("请填写地点");
    		}   
			$city = I('post.city','','intval');
			$model = D('AuditNotice');
			$r = $model->where(array('city'=>$city))->getField('id');			
			if($r) $this->error("该地区通知已经存在");
		
			$data['start_time']	 = I('post.start_time','','trim');
			$data['end_time']	 = I('post.end_time','','trim');
			$data['address'] = I('post.address','','trim');
			$data['city'] 	 = $city;
			    		  		
    		//数据添加到数据库
    		
    		if($model->create($data)){
    			$r = $model->add();
    			if($r) {
					$this->cacheall();
    				$this->success('添加成功！');exit;
    			}
				$this->error('添加失败！');
    		}else {
    			$this->error($model->getError());
    		}    		
    	}    	
    }


    public function edit(){
    	$id=  I('get.id',0,'int');
		if(empty($id)) {
			$this->error('记录不存在');
		}
        $info = D('AuditNotice')->where(array('id'=>$id))->find();        
		$this->assign('info',$info)->display();
    }
    
    
    public function edit_post(){
    	if(IS_POST){    
    		if(empty($_POST['city'])){
    			$this->error("请选择地区");
    		} 
			if(empty($_POST['address'])){
    			$this->error("请填写地点");
    		}      		
			$id = I('post.id',0,'intval');
			$city = I('post.city','','intval');
			$model = D('AuditNotice');
			$map['city'] = $city;
			$map['id']   = array('neq',$id);			
			$r = $model->where($map)->getField('id');			
			if($r) $this->error("该地区通知已经存在");			
			$info = array(
				'start_time' => I('post.start_time','','trim'),
				'end_time' 	 => I('post.end_time','','trim'),
				'address' 	 => I('post.address','','trim'),
				'city' 		 => I('post.city','','intval'),
			);
    		$r = $model->where(array('id'=>$id))->save($info);
    		if($r){
				$this->cacheall();
    			$this->success('更新成功！');
    		}else {
    			$this->error('更新失败！');
    		}    
    	}
    	 
    }
    
    public function del(){   	
		$ids = I('request.id');		
		if(!is_array($ids)) {
			$ids = empty($ids)?array():(array)$ids;
		}
		if(empty($ids)) {
			$this->error('请选择要删除的信息');
		}	
    	$map['id'] = array('in',$ids);
		$rs = D('AuditNotice')->where($map)->delete();
		if ($rs !== false) {
			$this->success('批量删除成功！');
		} else {
			$this->error('删除失败！');
		}    	
    }     
	
   public function cacheall(){
	   $list = D('AuditNotice')->select();
	   $info = array();
	   foreach($list as $k=>$r){
		   $info[$r['city']] = $r;
	   }
	   F('_common/audit_notice',$info);
	   return true;
   }
    
	//排序
    public function listorder() {
    	//dump($_POST);die;
    	$ids = $_POST['listorders'];
    	foreach ($ids as $key => $v) {
    		$data['listorder'] = $v;
    		$this->model->where(array('id' => $key))->save($data);
    	}
    	$this->success('排序更新成功！');    	
    }
	
	
	
}