<?php 
namespace Home\Controller;
use Think\Controller;
class PayController extends PublicController{
	function __construct(){
		parent::__construct();
		if(!isset($_SESSION['user'])){
			$this->error('请先登录',U('Login/index'));		}
	}
	
	//新闻中心首页
	function index(){
		$order_id=$_SESSION['order_id'];
		//print_r($_SESSION['order_id']);
		$model=D('Pay');
		$goods=$model->get_order_goods($order_id);
		$this->assign('goods',$goods);
		
		$model2=M('order_info');
		$address=$model2->where('id ='.$order_id)->find();
		$this->assign('address',$address);
		$this->assign('order_id',$order_id);
		//print_r($address['total_price']);
		//渲染模版
		$this->display();
	
	}
	function del(){
		//print_r($_POST);
		$order_id=$_POST['order_id'];
		$modelGood=M('order_goods');
		$modelGood->where('order_id ='.$order_id)->setField('is_select','0');
		$modelInfo=M('order_info');
		$modelInfo->where('id ='.$order_id)->setField('is_select','0');
		if($modelGood && $modelInfo){
			$this->ajaxReturn(1);
		}else{
			$this->ajaxReturn(0);
		}
		
	}
	
	
	

}



?>