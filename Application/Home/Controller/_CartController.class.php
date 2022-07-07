<?php 
namespace Home\Controller;
use Think\Controller;
class CartController extends PublicController{
	//user_id,goods_id,goods_name,goods_thumb,goods_price,goods_num,create_time;
	function add(){
		
		if(IS_AJAX){
			
			//判断是否登录
			if(isset($_SESSION['user'])){
				//定义 post过来的变量
				$user_id=$_SESSION['user']['id'];
				$num=I("post.num");
				$goods_id=I("post.goods_id");
				//读取产品列表	
				$modelPro=M('Product');
				$pro=$modelPro->where('id='.$goods_id)->find();
				
				
				
				if($pro['stock_num']>=$num){   //判断库存数量是否够
					$model=M('Cart');  //查询库 条件是产品ID和客户ID
					$one=$model->where('goods_id ='.$goods_id.' and user_id='.$user_id)->find();
					if($one){ //判断是否有此条内容,如果有,就修改产品数量
						$rs=$model->where('id='.$one['id'])->setInc('goods_num',$num);
						               
						if($rs!==false){
							$this->ajaxReturn(array('error'=>0,'mes'=>'成功'));
						}else{
							$this->ajaxReturn(array('error'=>1,'mes'=>'加入失败'));
						}
					}else{  //判断是否有此条内容,如果没有,创建
						$dataAdd = $model->create($dataAdd);
						$dataAdd['user_id']= $user_id;
						$dataAdd['goods_id']= $goods_id;
						$dataAdd['goods_name']= $pro['name'];
						$dataAdd['goods_price']= $pro['real_price'];
						$dataAdd['goods_thumb']= $pro['thumb'];
						$dataAdd['goods_num']= $one['goods_num']+$num;
						$dataAdd['create_time']= time();
						$dataAdd['is_select']=0;
						if($model->add($dataAdd)){
							$this->ajaxReturn(array('error'=>0,'mes'=>'成功'));
						}else{
							$this->ajaxReturn(array('error'=>1,'mes'=>'加入失败'));
						}
					}
					
					
				}else{  
					$this->ajaxReturn(array('error'=>1,'mes'=>'库存数量不够'));//数量不够
				}
				
			}else{
				$this->ajaxReturn(array('error'=>1,'mes'=>'没有登录'));//没有登录
			}
			
			 
			
		
			
		}
		
	}
	
	
}