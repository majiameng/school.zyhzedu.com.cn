<?php 
namespace Home\Model;
use Think\Model;
class PayModel extends Model{
	//指定该模型操作那张表
	protected $tableName = 'Order_goods';
	
	/**
	 * 获取订单数据
	 */
	function get_order_goods($region_id){
		$where = array('region_id'=>"{$region_id}",'is_select'=>1);
		$region = $this->where($where)->select();
		return $region;
	}
	
	
	
	


	
}