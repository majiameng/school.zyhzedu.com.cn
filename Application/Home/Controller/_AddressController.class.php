<?php 
namespace Home\Controller;
use Think\Controller;
class AddressController extends PublicController{
	function _construct(){
			parent::_construct();
			if(!isset($_SESSION['user'])){
				$this->error('请先登录',U('Login/index'));		}
	}
	
	//新闻中心首页
	function index(){
		
		$user_id=$_SESSION['user']['id'];
		//添加地址到 数据库
		if(IS_AJAX){
		//echo 123;die;
			$model=M('User_address');
			$address=$model->select();
			
			$data['user_id']= $_SESSION['user']['id'];
			$data['consignee']=$_POST['consignee'];
			$data['zipcode']=$_POST['zipcode'];
			$data['mobile']=$_POST['mobile'];
			$data['tel']=$_POST['phone'];
			$data['is_default'] = $_POST['is_default'];
			
			$data['province']=$_POST['sheng'];
			$data['city']=$_POST['city'];
			$data['district']=$_POST['area'];
			$data['address']=$_POST['address'];
			
				
			
			if($data['is_default'] == true){   //如果设为默认为1,那么其它地址改为0 ,并且加入地址库
				
				$data['is_default'] = 1;
				$save=$model->where(array('user_id'=>$user_id))->setField('is_default',0);
				if($save !== false){
					if($model->add($data)){
						$this->ajaxReturn(array('error'=>0,'mes'=>'成功'));die;
					}else{
						$this->ajaxReturn(array('error'=>1,'mes'=>'加入失败'));die;
					}
				}else{
					$this->ajaxReturn(array('error'=>1,'mes'=>'改为默认失败'));die;
				}
			}else{
				
				
				if($model->add($data)){
						$this->ajaxReturn(array('error'=>0,'mes'=>'成功'));die;//array('error'=>0,'mes'=>'成功')
					}else{
						$this->ajaxReturn(array('error'=>1,'mes'=>'加入失败'));die;
					} 
			} 
			
		}
		
		//省市区初始值
		$modelCity=M('Region');
		$reg=$modelCity->where('parent_id = 1')->select();
		$regCity=$modelCity->where('parent_id ='.$reg[0]['region_id'])->select();
		$this->regArea=$modelCity->where('parent_id ='.$regCity[0]['region_id'])->select();
		$this->assign('reg',$reg);
		$this->assign('regCity',$regCity);	
		
		//调地址库
		$modelAddress=M('User_address');
		$addre0=$modelAddress->order('is_default desc')
							->where(array('user_id' =>$user_id))->select();
		
		$count=count($addre0);
		//print_r($count);
		$this->assign('addre0',$addre0);
		$this->assign('count',$count);

		
		

		//print_r($_SERVER);
		
		//渲染模版
		$this->display();
	
	}
	//市区js传值
	function cityCheck(){
		if(IS_AJAX){
			//echo (111);die;
			$modelCity=M('Region');
			$regSheng=$modelCity->where('parent_id ='.$_POST['sheng'])->select();
			$regArea=$modelCity->where('parent_id='.$regSheng[0]['region_id'])->select();
			$data=array();
			$data['regSheng']=$regSheng;
			$data['regArea']=$regArea;
			echo json_encode($data);die; 
			//print_r($regSheng);	
			//print_r($regArea);die;
			
		}		
	}
	//ajax删除地址
	function del(){
		if(IS_AJAX){
			//echo (11);die;
			$id=$_POST['id'];
			$model=M('User_address');
			$de=$model->where('id ='.$id)->delete();
			if($de){
				$this->ajaxReturn(array('error'=>0,'mes'=>'成功'));die;
			}else{
				$this->ajaxReturn(array('error'=>1,'mes'=>'失败'));die;
			}
		}
	}
	//ajax修改地址
	function move(){
		if(IS_AJAX){
			//echo (11);die;
			$id=$_POST['id'];
			$model=M('User_address');
			$de=$model->where('id ='.$id)->find();
			if($de){
				echo json_encode($de);die;
			}else{
				$this->ajaxReturn(array('error'=>1,'mes'=>'失败'));die;
			}
		}
	}
	
	
	
	

}



?>