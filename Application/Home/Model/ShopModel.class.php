<?php 
namespace Home\Model;
use Think\Model;
class ShopModel extends Model{
	function _construct(){
		parent::_construct();
		if(!isset($_SESSION['user'])){
			$this->error('请先登录',U('Login/index'));		}
	}
	//指定该模型操作那张表
	protected $tableName = 'Order_info';
	
	/**
	 * 生成唯一的订单号
	 */
	function create_order_sn(){
		$order_sn = date('YmdHis').mt_rand(1000,9999);
		$one = $this->where("order_sn = {$order_sn}")->find();
		if($one){
			$this->create_order_sn();//如果有重复,就再生成一次
		}else{
			return $order_sn;
		}
	}
	 /**
	 * 把信息添加到order_info表  未完成
	 * @param unknown $data
	 */
	function add_order_info($data){
		//需要判断库存
		//需要减库存
		return $this->add($data);
	}
	/**
	 * 获取购物车中要结算的商品数组
	 */
	function get_carts_arr(){
		$cart_model = M('Cart');
		$where = array('is_select'=>1,'user_id'=>$_SESSION['user']['id']);
		$carts = $cart_model->where($where)->select();
		
		
		
		return $carts;
	}
	
	/**
	 * 得到购物车中已选择的所有商品的总价
	 */
	function get_carts_total(){
	
		$carts = $this->get_carts_arr();
	
		//把购物车中每一个商品id和数量获取到
		$total = 0;
		foreach ($carts as $k=>$v){
			$goods_num = $v['goods_num'];
			$goods_id = $v['goods_id'];
			$goods_price = M('Product')->where("id=$goods_id")->getField('real_price');
			$total += $goods_price * $goods_num;
		}
		
		
		return $total;
	}
	
	/**
	 * 添加order_goods表
	 */
	function add_order_goods($order_id){
		
		//order_id goods_id
		$carts = $this->get_carts_arr();
		$model2 = M('Order_goods');
		
		foreach($carts as $k=>$v){
				
			$data['order_id'] = $order_id;
			$data['goods_id'] = $v['goods_id'];
			$data['goods_num'] = $v['goods_num'];
			$goods = M('product')->find($data['goods_id']);
			$data['goods_name'] = $goods['name'];
			$data['goods_price'] = $goods['real_price'];
			$data['goods_thumb'] = $goods['thumb'];
			$data['is_select'] = 1;
			$model2->add($data);
		}
	
		return true;
	}
	
	
	/**
	 * 删除购物车中已结算的商品
	 */
	function del_carts_select(){
		$where = array('is_select'=>1,'user_id'=>$_SESSION['user']['id']);
		return M('Cart')->where($where)->delete();
	}
	/**
	 * 获取中Order_goods要结算的商品数组
	 */
	function get_Order_goods_arr(){
		$cart_model = M('Order_goods');
		$where = array('is_select'=>1,'user_id'=>$_SESSION['user']['id']);
		$carts = $cart_model->where($where)->select();
	
	
	
		return $carts;
	}
	
	/**
	 * 得到Order_goods中已选择的所有商品的总价
	 */
	function get_Order_goods_total(){
	
		$carts = $this->get_Order_goods_arr();
	
		//把购物车中每一个商品id和数量获取到
		$total = 0;
		foreach ($carts as $k=>$v){
			$goods_num = $v['goods_num'];
			$goods_id = $v['goods_id'];
			$goods_price = M('Product')->where("id=$goods_id")->getField('real_price');
			$total += $goods_price * $goods_num;
		}
	
	
		return $total;
	}

	/**
	 * 根据订单号 得到Order_goods中已选择的所有商品的总价
	 */
	function get_goods_total($order_sn){

		$modelInfo=M('Order_info');
		$info=$modelInfo->where('order_sn='.$order_sn)->find();


		$cart_model = M('Order_goods');
		$where = array('order_id'=>$info['id']);
		$carts = $cart_model->where($where)->select();
	
		
	
		//把购物车中每一个商品id和数量获取到
		$total = 0;
		foreach ($carts as $k=>$v){
			$goods_num = $v['goods_num'];
			$goods_id = $v['goods_id'];
			$goods_price = M('Product')->where("id=$goods_id")->getField('real_price');
			$total += $goods_price * $goods_num;
		}
	
	
		return $total;
	}
	
	
}
