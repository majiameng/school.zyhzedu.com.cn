<?php
namespace Admin2\Controller;
use Think\Controller;
class MessageController extends AdminBaseController {	
         protected $model;
		 
        function _initialize() {
		$this->model = D('Message');//留言信息表表
		
	}
   public function index(){
   $data = $this->model->order('id desc')->select();
  
   $this->assign('data',$data);   
   $this->display();
}
 
 public function del(){
    	
    	//删除单个
    	if(isset($_GET['id'])){
    		$id = I('get.id',0,'int');
    		$data['is_del']=1;
    		if ($this->model->where('id='.$id)->delete($data)) {
    			$this->success('删除成功！');
    		} else {
    			$this->error('删除失败！');
    		}
    	}
    	//批量删除
    	if(isset($_POST['ids'])){
    		$ids=implode(',',$_POST['ids']);
    		$data['is_del']=1;
    		if ($this->model->where("id in ($ids)")->delete($data)) {
    			$this->success('删除成功！');
    		} else {
    			$this->error('删除失败！');
    		}
    	}
    	
    	
    }
	
	
	
	
}
?>