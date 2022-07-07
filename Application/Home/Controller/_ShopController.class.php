<?php 
namespace Home\Controller;
use Think\Controller;
class ShopController extends PublicController{
	
	
	function __construct(){
		parent::__construct();
			if(!isset($_SESSION['user'])){
				$this->error('请先登录',U('Login/index'));		}
		$this->cart_model = M('Cart');
	}

	protected $cart_model;
	
	//购物车首页
	function index(){
		$model=D('Shop');
		$user_id=$_SESSION['user']['id'];
		$modelInfo=M('Order_info');
		$sel=$modelInfo->where('user_id='.$user_id)->select();
		if($sel==false){
			$p='还没有下单,快去选择商品吧';
			$this->assign('p',$p);
		}else{
		//计算总金额
		$carts = $this->cart_model->where(array('user_id'=>$_SESSION['user']['id']))->select();
		$total=0;  //计算总金额
		foreach($carts as $k=>$v){
			$carts[$k]['sub_total'] = $v['goods_price'] * $v['goods_num'];
			$total += $carts[$k]['sub_total'];
		}
		
		
		
		$this->assign('carts',$carts);
		$this->assign('total',$total);
		
		//通过提交的数据做,删除购物车,加入订单页,跳转订单页
		if(!empty($_POST)){
			if(!isset($_POST['ids'])){
				echo '<script>alert("请选择要结算的商品");location.href="'.U('Shop/index').'"</script>';
				die;
			}
			//把其它的is_select改为0;
			$modelGoods=M('Order_goods');
			$modelInfo=M('Order_info');
			$findId=$modelInfo->Field('user_id,is_select,id')->select();
			foreach($findId as $k=>$v){
				if($v['user_id'] == $user_id){
					//通过user_id 修改is_select为0
					$modelInfo->where('user_id ='.$user_id)->setField('is_select','0');
					//通过 info表的id,修改Goods表的 is_select为0
					$modelGoods->where('order_id ='.$v['id'])->setField('is_select','0');
				}
					
			}
			
			
			
			
			
			$numbtn= $_POST['numbtn'];
			$cart_ids= $_POST['ids'];
			//调产品库对比 数量和价格
			$modelPro=M('product');
			$shifou = true;
			foreach($cart_ids as $k=>$v){
				$goods_id = $this->cart_model->where('id='.$v)->getField('goods_id');
				$pro=$modelPro->where('id='.$goods_id)->Field('id,stock_num,real_price')->find();
				
				if($numbtn[$v] >= $pro['stock_num']){
					$shifou = false;
					
				}
			}
			
			//如果库存充足
			if($shifou){
				 //把is_select 改为0
				$this->cart_model->where(array('user_id'=>$user_id))->setField('is_select',0);
				 foreach($cart_ids as $k=>$v){
				//把is_select 改为1
				//购物车数量改为提交的数量
				$gengxin['is_select'] = 1;
				$gengxin['goods_num'] = $numbtn[$v];
				$this->cart_model->where(array('user_id'=>$_SESSION['user']['id'],'id'=>$v))->save($gengxin);	
				
				 } 
				
				//把is_select的加入订单 info表
				$data['order_sn']=$model->create_order_sn(); //生成订单号
				$data['user_id']=$user_id; 
				$data['total_price']=$model->get_carts_total(); //模型中算总价
				$data['create_time'] = time(); //生成时间
				$data['is_select'] = 1;
				
				//print_r($data);die;
				//添加数据到order_info表
				if($order_id=$model->add_order_info($data)){
					//print_r($order_id);die;
					//添加数据到订单列表order_goods
					if($model->add_order_goods($order_id)){
						//删除Cart选中表
						if($model->del_carts_select()){
							header('location:'.U('Order/confirm'));
							echo json_encode(array('error'=>0));die;
						}else{
							echo json_encode(array('error'=>1,'msg'=>'shanchu'));die;
						}
					}else{
						echo json_encode(array('error'=>1,'msg'=>'tianjia order_goods'));die;
					}
						
				}else{
					echo json_encode(array('error'=>1,'msg'=>'tianjia'));die;
				}
				
		    }
			
		}
		
		}
		//渲染模版
		$this->display();
	}
	
}



?>