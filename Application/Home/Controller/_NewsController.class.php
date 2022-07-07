<?php 
namespace Home\Controller;
use Think\Controller;
class NewsController extends PublicController{
	
	//新闻中心首页
	function index(){
        //新闻分类
        $pro_cat=M('Newscat');
        $cat=$pro_cat->select();
        $this->assign('cat',$cat);

       
		$news=M('News');
        $where = 'is_del= 0';
        if($_GET['j']){
            $where .= ' and category_id='.$_GET['j'];
        }
		$one=$news->order('id desc')
		    ->limit('12')
            ->where($where)->select();
		$this->assign('one',$one);



		
		//计算产品表总条数
		$where = 'is_del= 0';
        if($_GET['j']){
            $where .= ' and category_id='.$_GET['j'];
        }
		$count=$news->where($where)->count();
		//print_r($count);
		//实例化分页模板
		$pagemi = 12;//每页显示3条
		$page = new\Think\Page($count,$pagemi);
		$page -> setConfig('prev','上一页');
		$page -> setConfig('next','下一页');
		$page -> setConfig('first','首页');
		$page -> setConfig('last','尾页');
		
		//分页超连接
		$pages=$page->show();
		
		//分页当前页
		$this->pro = $news ->where($where)
						  ->order('listorder asc,id desc')
						  ->limit($page->firstRow,$page->listRows)->select();
		$page -> rollPage = 5 ;//设置分页显示多少


        $this->assign('pages',$pages); //分页
		//渲染模版
		$this->display();
	
	}
	
	
	
	function details(){
		$id=$_GET['id'];
        $modelNews=M('News');
        $data=$modelNews->table('__NEWS__ a,__NEWSDATA__ b')->where('a.id=b.id and a.id='.$id)->find();
		$this->assign('data',$data);
		//渲染模版
		$this->display();
	
	}


}



?>