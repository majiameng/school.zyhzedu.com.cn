<?php
namespace Admin2\Controller;
use Think\Controller;

class NewsCateController extends AdminBaseController{
	
	protected $cat_model;
	
	function _initialize() {
		$this->cat_model = D('Newscat');
	}
	
    public function index(){
        
        $cat=$this->cat_model->select();
        $this->assign('cat_table',$cat);
        //print_r($cat);die;
        $this->display();
    }




    public function add(){

		$this->display();
    }
    
    public function add_post(){
    	if(IS_POST) {
            $data['name'] = $_POST['name'];
            $info = $this->cat_model->data($data)->add();
            if ($info) {
                $this->success('添加成功',U('NewsCate/index'));
            } else {
                $this->error('添加失败');
            }

        }
    }
    
    
    public function edit(){
    	$id=  I('get.id',0,'int');
    	//需要让上级分类默认选中，得到上级id
    	$data = $this->cat_model->where('id='.$id)->find();
    	//得到下拉列表 getTree方法在category模型类
    	$this->assign('data', $data);    
    	$this->display();    	
    }
    
    public function edit_post(){
    	if(IS_POST){
    	    //print_r($_POST);die;
            $data['name'] = $_POST['name'];
            //$data['content']= trim($_POST['content']);
            $info = $this->cat_model->where('id='.$_POST['id'])->save($data);
            if($info){
                $this->success('更新成功',U('NewsCate/index'));
            }else {
                $this->error('更新失败');
            }
    	}
    }
    
    
    /**
     * 删除方法
     * 有子分类不能删
     * 分类里有商品不能删
     */
    public function del(){
    	$id=  I('get.id',0,'int');
    	

    		$result=$this->cat_model->where('id='.$id)->delete();
    		if($result !== false){
    			$this->success('删除成功！');
    		}else{
    			$this->error('删除失败！');
    		}

    	
    }


}

