<?php
namespace Admin2\Controller;
use Think\Controller;
class NewsController extends AdminBaseController {
	protected $model;
	protected $cat_model;
    protected $data_model;

	
	function _initialize() {
		$this->model = D('News');
		$this->cat_model = M('Newscat');//分类表
        $this->data_model = M('Newsdata');

	}	
    public function index(){
    	$this->_list();
        $this->display();
    }    

    //考虑到回收站和普通列表逻辑相同，故放在一个方法里
    private function _list($is_del = 0){
    	$cat_id=0;
    	$where = 'is_del = '.$is_del;
    	if(!empty($_GET['cat_id'])){
    		$cat_id=I('get.cat_id',0,'int');
    		$where.=' and category_id = '.$cat_id;
    	}    	 
    	if(!empty($_GET['start_time'])){
    		$where .= ' and create_time > '.strtotime($_GET['start_time']);
    	}
    	if(!empty($_GET['end_time'])){
    		$where .= ' and create_time < '.strtotime($_GET['end_time']);
    	}
    	if(!empty($_GET['keyword'])){
    		$where .= ' and title like "%'.$_GET['keyword'].'%"';
    	}    	 
    	$count=$this->model->where($where)->count();//总页数
    	$page = $this->page($count, 20);//因为分页比较常用，所以在父类 adminBaseController里写了page方法    	
    	$articles=$this->model
    	->order('is_top DESC,listorder ASC,id DESC')
    	->where($where)
    	->limit($page->firstRow.','.$page->listRows)
    	->select(); 
      	
    	// getField 得到这样的数组id为键，name为值：Array ( 1 => '手机', 2 => '电脑', 4 => '苹果' )
    	$cates = $this->cat_model->getField('id,name');
    	$this->assign("cates",$cates);	
    	//得到下拉列表 getTree方法在category模型类
    	$cat_option = $this->cat_model->select();
    	$this->assign('cat_option', $cat_option);    	
    	$this->assign('pages', $page->show());
    	$this->assign('articles', $articles);
    	$this->assign("formget",$_GET);    	
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
	
    public function add(){

        //得到分类的下拉列表  getTree方法在category模型类
        $cat_option = $this->cat_model->select();
        $this->assign('cat_option', $cat_option);
        $this->display();
    }
   
    public function add_post(){

    	if(IS_POST){   		
    		if(empty($_POST['category_id'])){
    			$this->error("请选择分类！");
    		}   		
    		$article=I("post.");    		
    		//咱们的内容不需要htmlspecialchars
    		$article['content']=htmlspecialchars_decode($article['content']);//将特殊的实体转换为普通字符
    		//简介
    		if(empty($_POST['description'])){
				$article['description']=mb_substr(strip_tags($article['content']),0,150,'utf-8');
			}    	
    		$article['create_time']=strtotime($article['create_time']);
    		$article['update_time']=$article['create_time'];
    		//echo $article['create_time'];die;    		
    		//图集的处理
    		$images = json_encode($article['images']);    		
    		//设置了提取缩略图或提取图片集
    		if(!empty($_POST['isthumb']) || !empty($_POST['isimglist'])){
    			//匹配content图片 放入数组
    			preg_match_all("/(?:src)=[\"|'| ]{0,}([^>]*\.(?:gif|jpg|jpeg|bmp|png))/is", htmlspecialchars_decode($_POST['content']), $matches);
    			$imgarr = count($matches[1]) > 0 ? $matches[1] : ''; // 所有图片
    			$firstPic = count($imgarr[0]) > 0 ? $imgarr[0]: '';  // 第一张图片
    			if(!empty($_POST['isthumb']) && empty($_POST['thumb']) && !empty($firstPic)){//需要判断取得的第一张图有没有
    				$article['thumb'] = $firstPic;
    			}
    			if(!empty($_POST['isimglist']) && empty($_POST['images']) && !empty($firstPic)){
    				if(count($imgarr)>0){
    					$images = json_encode($imgarr);    						
    				}
    			}	
    		}   		
    		//数据添加到数据库
    		$model = $this->model;
    		if($model->create($article)){
    			$result=$model->add();
    			if ($result) {
    				//添加副表
    				$data['id'] = $result;
    				$data['image'] = $images;
    				$data['content'] = $article['content'];    				
    				$this->data_model->add($data);
    				$this->success('添加成功',U('News/index'));
    			}else {
    				$this->error('添加失败！');
    			}
    		}else {
    			$this->error($model->getError());
    		}    		
    	}    	
    }


    public function edit(){
    	$id=  I('get.id',0,'int');
    	
    	//注意此时需要查主表和副表，联表查或分别查，哪怕分别查到放两个数组都可以
    	//当然，使用视图模型也可以 视图模型参考thinkphp的手册
    	//tp框架提供的连表查询join方法
    	//$model = M('Article');
    	//$data=$model->join('tpshop_article_data on tpshop_article.id=tpshop_article_data.id')->where('tpshop_article.id='.$id)->find();
       	//表名可以这样简写，会自动加上表前缀$data=$this->model->join('__ARTICLE_DATA__ on __ARTICLE__.id=__ARTICLE_DATA__.id')->where(C('DB_PREFIX').'article.id='.$id)->find();
        
        //最简单的，分别查两张表 得到两个数组，分别传给视图也行
    	//$data=$this->model->where('id='.$id)->find();
    	//$data2=M('product_data')->where('id='.$id)->find();
        //这里我直接同时查两张表
        $data=$this->model->table('__NEWS__ a,__NEWSDATA__ b')->where('a.id=b.id and a.id='.$id)->find();
        $data['create_time']=date('Y-m-d H:i:s',$data['create_time']);
        
        $data['image']=json_decode($data['image']);
        //得到分类的下拉列表  getTree方法在category模型类
        $cat_option = $this->cat_model->select();

        $this->assign('cat_option', $cat_option);
        
        $this->assign('data',$data);


        $this->display();
    }
    
    
    public function edit_post(){
    	if(IS_POST){
    
    		if(empty($_POST['category_id'])){
    			$this->error('请选择分类！');
    		}
    		
    
    		$article=I('post.');
    		
    		
    		//新品和推荐两个因为是复选框，如果用户不勾选，这里就获取不到，若用户不勾选，我们应该让其变成0
    		$article['is_new'] = isset($article['is_new'])?$article['is_new']:0;
    		$article['is_promote'] = isset($article['is_promote'])?$article['is_promote']:0;
    		
    
    		//咱们的内容不需要htmlspecialchars
    		$article['content']=htmlspecialchars_decode($article['content']);
    		//简介
    		if(empty($_POST['description'])){
    			$article['description']=mb_substr(strip_tags($article['content']),0,150,'utf-8');
    		}
    		 
    		$article['create_time']=strtotime($article['create_time']);
    		$article['update_time']=strtotime($article['update_time']);
    		//echo $article['create_time'];die;
    		
    		
    		//图集的处理
    		$article['image'] = json_encode($article['images']);
    		
    		//设置了提取缩略图或提取图片集
    		if(!empty($_POST['isthumb']) || !empty($_POST['isimglist'])){
    			//匹配content图片 放入数组
    			preg_match_all("/(?:src)=[\"|'| ]{0,}([^>]*\.(?:gif|jpg|jpeg|bmp|png))/is", htmlspecialchars_decode($_POST['content']), $matches);
    			$imgarr = count($matches[1]) > 0 ? $matches[1] : ''; // 所有图片
    			$firstPic = count($imgarr[0]) > 0 ? $imgarr[0]: '';  // 第一张图片
    			if(!empty($_POST['isthumb']) && empty($_POST['thumb']) && !empty($firstPic)){//需要判断取得的第一张图有没有
    				$article['thumb'] = $firstPic;
    			}
    			if(!empty($_POST['isimglist']) && empty($_POST['images']) && !empty($firstPic)){
    				if(count($imgarr)>0){
    					$article['image'] = json_encode($imgarr);
    				}
    			}
    		}
    		//更新数据到数据库
    		
    		$result=$this->model->save($article);
    		$result2=$this->data_model->save($article);
    		if($result!==false && $result2!==false){
    			$this->success('更新成功',U('News/index'));
    		}else {
    			$this->error('更新失败！');
    		}
    
    	}
    	 
    }
    
  
    
    
    //删除，并非真正删除，只是把is_del改为1
    public function del(){
    	
    	//删除单个
    	if(isset($_GET['id'])){
    		$id = I('get.id',0,'int');
    		$data['is_del']=1;
    		$rs = $this->model->where('id='.$id)->save($data);
    		if ($rs !== false) {
    			$this->success('删除成功！');
    		} else {
    			$this->error('删除失败！');
    		}
    	}
    	//批量删除
    	if(isset($_POST['ids'])){
    		$ids=implode(',',$_POST['ids']);
    		$data['is_del']=1;
			$rs = $this->model->where("id in ($ids)")->save($data);
    		if ($rs !== false) {
    			$this->success('批量删除成功！');
    		} else {
    			$this->error('删除失败！');
    		}
    	}
    	
    	
    }
    
    
    //回收站
    public function recyclebin(){
    	$this->_list(1);//找 is_del等于1的所有商品
    	$this->display();
    }
    
    //还原
    public function restore(){
    	if(isset($_GET['id'])){
    		$id = I('get.id',0,'int');
    		$data=array('id'=>$id,'is_del'=>0);
    		if ($this->model->save($data)) {
    			$this->success('还原成功！');
    		} else {
    			$this->error('还原失败！');
    		}
    	}
    	//批量还原
    	if(isset($_POST['ids'])){
    		$ids=implode(',',$_POST['ids']);
    		$data['is_del']=0;
    		if ($this->model->where("id in ($ids)")->save($data)) {
    			$this->success('还原成功！');
    		} else {
    			$this->error('还原失败！');
    		}
    	}
    	
    	
    }
    
    //彻底删除
    public function clean(){
    	
    	if(isset($_GET['id'])){
    		$id = I('get.id',0,'int');
    		$result=$this->model->where('id='.$id)->delete();
    		//别忘了，副表信息也要删除
    		$result2=$this->data_model->where('id='.$id)->delete();
    		if($result!==false && $result2!==false){
    			$this->success('删除成功！');
    		}else{
    			$this->error('删除失败！');
    		}
    	}
    	
    	if(isset($_POST['ids'])){
    		$ids=implode(',',$_POST['ids']);
    		$result=$this->model->where("id in ($ids)")->delete();
    		$result2=$this->data_model->where("id in ($ids)")->delete();
    		if($result!==false && $result2!==false){
    			$this->success('删除成功！');
    		}else{
    			$this->error('删除失败！');
    		}
    	}
    	
    	
    	
    	 
    }
    
    //推荐
    public function recommend(){
    	if(isset($_POST['ids']) && $_GET['recommend']){
    		$ids=implode(',',$_POST['ids']);
    		$data['is_recommend']=1;
    		if ($this->model->where("id in ($ids)")->save($data) !== false) {
    			$this->success('批量推荐设置成功！');
    		} else {
    			$this->error('批量推荐设置失败！');
    		}
    	}
    	if(isset($_POST['ids']) && $_GET['unrecommend']){
    		$ids=implode(',',$_POST['ids']);
    		$data['is_recommend']=0;
    		if ($this->model->where("id in ($ids)")->save($data) !== false) {
    			$this->success('取消推荐成功！');
    		} else {
    			$this->error('取消推荐失败！');
    		}
    	}
    }
    
    //置顶
    public function top(){
    	if(isset($_POST['ids']) && $_GET['top']){
    		$ids=implode(',',$_POST['ids']);
    		$data['is_top']=1;
    		if ($this->model->where("id in ($ids)")->save($data) !== false) {
    			$this->success('批量置顶设置成功！');
    		} else {
    			$this->error('批量置顶设置失败！');
    		}
    	}
    	if(isset($_POST['ids']) && $_GET['untop']){
    		$ids=implode(',',$_POST['ids']);
    		$data['is_top']=0;
    		if ($this->model->where("id in ($ids)")->save($data) !== false) {
    			$this->success('取消置顶成功！');
    		} else {
    			$this->error('取消置顶失败！');
    		}
    	}
    }
    
    
    //移动到
    public function move(){
    	if(IS_POST){
    		if(isset($_POST['ids']) && isset($_POST['cat_id'])){
    			$ids=implode(',',$_POST['ids']);
    			$data['category_id']=$_POST['cat_id'];
    			if ($this->model->where("id in ($ids)")->save($data) !== false) {
    				$this->success('移动成功！');
    			} else {
    				$this->error('移动失败！');
    			}
    		}else{
    			$this->error('请选择！');
    		}
    	}
    }
	
	
	
}