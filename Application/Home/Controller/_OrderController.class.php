<?php 
namespace Home\Controller;
use Think\Controller;
class OrderController extends PublicController{
	function __construct(){
			parent::__construct();
			if(!isset($_SESSION['user'])){
				$this->error('请先登录',U('Login/index'));		}
	}

	
	function index(){
		$user_id=$_SESSION['user']['id'];
		$modelInfo=M('Order_info');
		$sel=$modelInfo->where('user_id='.$user_id)->select();
		if($sel==false){
			$p='还没有下单,快去选择商品吧';
			$this->assign('p',$p);
		}else{		
			
			$model=M('Order_goods');
			$cart=$model->where(array('user_id'=>$user_id))->select();
			$this->assign('cart',$cart);
			
			//把所有IS_select改为0
			$model->where(array('user_id'=>$user_id))->setField('is_select',0);
			$modelInfo=M('Order_info');
			$modelInfo->where(array('user_id'=>$user_id))->setField('is_select',0);
			//print_r($cart);
			/* foreach($cart as $k=>$v){
				$aa[]=$v['order_id'];
			}
			foreach($aa as $vv){
				$modelInfor=M('Order_info');
				$info[]=$modelInfor->where('id='.$vv)->find();
				$this->assign('info',$info);
			} */
			
			foreach($cart as $k=>&$vv){
				//echo $vv['order_id'];
				$modelInfor=M('Order_info');
				$llll=$modelInfor->where('id='.$vv['order_id'])->find();
				$vv['status']  = $llll['order_status'];
				$vv['order_sn']=$llll['order_sn'];
			}

			//按订单读取,上方无效
			//改is_select为0,并且删除没有产品的info表
			$one=D('Order');
			$one->save_select($user_id);
			$one->del_order_info($user_id);

			//读取
			$modelInfo=M('Order_info');
			$info=$modelInfo->where('user_id='.$user_id)->select();
			foreach($info as &$v){
				$modelGood=M('Order_goods');
				$goods=$modelGood->where('order_id='.$v['id'])->select(); 
				$v['goods'] = $goods;
				
				
			}
			 //print_r($info);
				$this->assign('info',$info); 
			 	
			
		}	
		//print_r($info);
		
		//渲染模版
		$this->display();
	
	}
	
	
	
	function details(){
		$modelInfo=M('Order_info');
		
		$where['user_id']=$_SESSION['user']['id'];
		$where['order_sn']=$_GET['sn'];
		$info=$modelInfo->where($where)->find();
		
			
				$modelGood=M('Order_goods');
				$good=$modelGood->where('order_id ='.$info['id'])->select();
				foreach($good as $kk=>$vv){
					$info[goods][]=$vv;
					//print_r($info);
				}
			
		$model=D('Shop');
		$total=$model->get_goods_total($info['order_sn']);
		
		$this->assign('info',$info);
		$this->assign('total',$total);	
		//print_r($total);
	
		//渲染模版
		$this->display();
	
	}
	
	function confirm(){
		$user_id=$_SESSION['user']['id'];
		//调取购物车
		$cart_model=M('Order_goods');
		$cart=$cart_model->where(array('user_id'=>$user_id,'is_select'=>1))->select();
		//print_r($cart);
		$this->assign('cart',$cart);

		
		$total=0;  //计算总金额
		foreach($cart as $k=>$v){
			$cart[$k]['sub_total'] = $v['goods_price'] * $v['goods_num'];
			$total += $cart[$k]['sub_total'];
		}
		
		//调取用户默认地址
		$model=M('User_address');
		if($_GET['id'] != ''){
			$add=$model->where(array('user_id'=>$user_id,'id'=>$_GET['id']))->find();
		}else{
			$add=$model->order('is_default desc,id desc')->where(array('user_id'=>$user_id))->find();
		}
		
		//print_r($add);
		$this->assign('add',$add);
		$this->assign('total',$total);
		
		//渲染模版
		$this->display();
	
	}
	function del(){
		$user_id=$_SESSION['user']['id'];
		if(IS_AJAX){
			$id=$_POST['id'];
			//echo 123;
			$model=M('Cart');
			$del=$model->where(array('user_id'=>$user_id,'id'=>$id))->delete();
			if($del){
				echo 1;
			}else{
				echo 0;
			}
		}
	}
	function address(){
		$user_id=$_SESSION['user']['id'];
		$model=D('Order');
		$addressId=$_POST['addressId'];
		$user=$model->get_user_address($addressId);
		$order_id=$_POST['order_id'];
		
		$modelAdd=M('Order_info');
		$data['tel']=$user['tel'];
		$data['mobile']=$user['mobile'];
		$data['zipcode']=$user['zipcode'];
		$data['consignee']=$user['consignee'];
		$data['province']=$model->get_region($user['province']);
		$data['city']=$model->get_region($user['city']);
		$data['district']=$model->get_region($user['district']);
		//print_r($data['province']);
		  if($modelAdd->where('id ='.$order_id)->save($data)){
		  	//session('order_id',$order_id);
		  	$_SESSION['order_id']=$order_id;
			echo json_encode(array('error'=>0,'msg'=>'成功'));die;
		}else{
			echo json_encode(array('error'=>1,'msg'=>'shanchu'));die;
		}  
		
	}
	
	function delOrder(){
		
		if(IS_AJAX){
			$id=$_POST['id'];
			//print_r($id);
			$model=M('Order_goods');
			foreach($id as $k=>$v){
				//print_r($order_id);
				 $del=$model->where(array('id'=>$v))->delete();
				
			}
			if($del){
				echo 1;die;
			}else{
				echo 0;die;
			}
			
		}
	}
	
	function pay(){
		
		 if(IS_AJAX){
		 	
			$id=$_POST['id'];
			$model=M('Order_goods');
			$modelInfo=M('Order_info');
			$modelPro=M('Product');
			
			foreach($id as $k=>$v){
				
				
				$goods=$model->where('id ='.$v)->Field('goods_id,goods_num')->find();//得到goods_id,为了得到产品的数量
				//print_r($goods);die;
				$pro=$modelPro->where('id ='.$goods['goods_id'])->Field('id,stock_num,real_price,thumb')->find();//得到产品数量
				if($goods['goods_num'] <= $pro['stock_num']){
					$data['is_select']=1;
					$data['goods_price']=$pro['real_price'];
					$data['goods_thumb']=$pro['thumb'];
					$save=$model-> where('id ='.$v)->save($data); //更新goods表的价格和图片,is_select为1
				}

				//把is_select的加入订单 info表
				$modelShop=D('Shop');
				$data['order_sn']=$modelShop->create_order_sn(); //生成订单号
				$data['user_id']=$_SESSION['user']['id'];
				$data['total_price']=$modelShop->get_Order_goods_total(); //模型中算总价
				$data['create_time'] = time(); //生成时间
				$data['is_select'] = 1;
				$addInfo=$modelInfo->add($data);
				
				//修改Order_goods里的order_id
				$saveOrder_id=$model->where('is_select = 1')->setField('order_id',$addInfo);
				
			}
			
		 	if($save){
		 		if($addInfo){
		 			if($saveOrder_id){
		 				echo 1;
		 			}else{
		 				echo 3;
		 			}
		 		}else{
		 			echo 2;
		 		}
		 	}else{
		 		echo 0;
		 	} 
				
				
			
		} 
		
	}

	function payIndex(){
		if(IS_AJAX){
			if(!empty($_POST)){
				//把info表和goods表is_select改为0
				$order=D('Order');
				$order->save_select($_SESSION['user']['id']);
				if($order){
					//print_r($_POST);
					$sn=$_POST['sn'];
					$infoSave=$modelInfo=M('Order_info')->where('order_sn ='.$sn)->setField('is_select',1);
					$info=$modelInfo=M('Order_info')->where('order_sn ='.$sn)->getField('id');
					$goods=$modelGood=M('Order_goods')->where('order_id ='.$info)->setField('is_select',1);
					if($infoSave && $goods){
						echo 1;
					}else{
						echo 0;
					}	
				}
			}else{
				echo 2;
			}
		}
	}
	function delGoods(){
		$user_id=$_SESSION['user']['id'];
		if(IS_AJAX){
			$id=$_POST['id'];
			$sn=$_POST['sn'];
			//echo 123;
			$model=M('Order_goods');
			$del=$model->where(array('user_id'=>$user_id,'id'=>$id))->delete();
			$modelTotal=D('Shop');
			$total=$modelTotal->get_goods_total($sn);
			if($del){
				$this->ajaxReturn(array('error'=>1,'mes'=>$total));
				
			}else{
				$this->ajaxReturn(array('error'=>0,'mes'=>$total));
			}
		}
	}

}



?>