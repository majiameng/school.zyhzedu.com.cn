<?php
namespace Admin2\Controller;
use Think\Controller;

class CategoryController extends AdminBaseController{
	
	protected $cat_model;
	
	function _initialize() {
		$this->cat_model = D('Category');
	}
	
    public function index(){
        
        $cat=$this->cat_model->getAlltable();
        $this->assign('cat_table',$cat);
        $this->display();
    }




    public function add(){
    	//得到下拉列表 getTree方法在category模型类
	    $cat_option = $this->cat_model->getTree();
	    $this->assign('cat_option', $cat_option);

        $this->display();
    }
    
    public function add_post(){
    	if(IS_POST){
    		if($this->cat_model->create()){//默认是创建一个POST过来的数据对象，会用到模型层的自动验证
    			if($this->cat_model->add()){
    				$this->success('添加成功',U('Category/index'));
    			}else {
    				$this->error('添加失败');
    			}
    		}else{
    			$this->error($this->cat_model->getError());
    		}
    	}    	
    }
    
    
    public function edit(){
    	$id=  I('get.id',0,'int');
    	//需要让上级分类默认选中，得到上级id
    	$data = $this->cat_model->where('id='.$id)->find();
    	//得到下拉列表 getTree方法在category模型类
    	$cat_option = $this->cat_model->getTree(0,$data['parent_id']);
    		
    	$this->assign('cat_option', $cat_option);
    	$this->assign('data', $data);    
    	$this->display();    	
    }
    
    public function edit_post(){
    	if(IS_POST){
    		if($this->cat_model->create()){//默认是创建一个POST过来的数据对象，会用到模型层的自动验证
    			if($this->cat_model->save() !== false){
    				$this->success('更新成功',U('Category/index'));
    			}else {
    				$this->error('更新失败');
    			}
    		}else{
    			$this->error($this->cat_model->getError());
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
    	
    	//该分类有多少子分类
    	$son_num = $this->cat_model->where('parent_id='.$id)->count();
    	//该分类有多少商品
    	$pro_num = M('Product')->where('category_id='.$id)->count();
    	if($son_num > 0){
    		$this->error('该分类下还有'.$son_num.'个子分类!');
    	}elseif($pro_num > 0){
    		$this->error('该分类下还有'.$pro_num.'个商品!');
    	}else{
    		$result=$this->cat_model->where('id='.$id)->delete();
    		if($result !== false){
    			$this->success('删除成功！');
    		}else{
    			$this->error('删除失败！');
    		}
    	}
    	
    }


}

