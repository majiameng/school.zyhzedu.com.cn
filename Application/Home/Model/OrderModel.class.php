<?php 
namespace Home\Model;
use Think\Model;
class OrderModel extends Model{
	//指定该模型操作那张表
	protected $tableName = 'User_address';
	
	/**
	 * 获取地址页面数据
	 */
	function get_region($region_id){
		$cart_model = M('Region');
		$where = array('region_id'=>"{$region_id}");
		$region = $cart_model->where($where)->getField('region_name');
		return $region;
	}
	
	/**
	 * 获取用户地址数据
	 */
	function get_user_address($addressId){
	
		//$addModer=M('User_address'); 
		$userAddress=$this->where('id ='.$addressId)->find();
	
		return $userAddress;
	}
	/**
	 * 改is_select为0
	 */
	function save_select($user_id){
		$modelInfo=M('Order_info');
		$modelGood=M('Order_goods');
		$info=$modelInfo->where('user_id ='.$user_id)->field('is_select,id')->select();
		
		foreach($info as $v){
			if($v['is_select']==1){
				$modelInfo->where('user_id='.$user_id)->setField('is_select',0);
				$goods=$modelGood->where('order_id ='.$v['id'])->field('is_select')->select();
				foreach($goods as $vv){
					if($vv == 1){
						$modelGood->where('order_id ='.$v['id'])->setField('is_select',0);
					}
				}
				
			}
		}
		return true;
	}

	//删除没有产品的goods_info表
	function del_order_info($user_id){
		$modelInfo=M('Order_info');
		$modelGood=M('Order_goods');
		$info=$modelInfo->where('user_id ='.$user_id)->select();
		foreach($info as $v){
			$one=$modelGood->where('order_id='.$v['id'])->select();
			if($one == false){
				$modelInfo->where('id='.$v['id'])->delete();
		    }
		}
		return true;
	}
	
	


	
}