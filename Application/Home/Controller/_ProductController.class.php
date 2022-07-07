<?php 
namespace Home\Controller;
use Think\Controller;
class ProductController extends PublicController{
	
	//新闻中心首页
	function index(){
		//轮播图
		$modelBanner=M('Banner');
		$condition['category'] = 'product';
		$banner=$modelBanner->where($condition)->find();
		$this->assign('banner',$banner);
		//print_r($banner['thumb']);
		
		//产品分类
		$pro_cat=M('Category');
		$cat=$pro_cat->select();
		$this->assign('cat',$cat);
		
		/* if(isset($_GET['j'])){//检查是否有分类
			$j=$_GET['j'];
		} */
		
		
		//实例化产品表
		$model=M('Product');
		
		//设where条件 初始值为is_del为0,如果有GET['j'],则 产品表里category_id等于get['id']
		$where = 'is_del= 0';
		if($_GET['j']){
			$where .= ' and category_id='.$_GET['j'];
		}
		
		
		
		//计算产品表总条数
		$count=$model->where($where)->count();
		
		//实例化分页模板
		$pagemi = 3;//每页显示3条
		$page = new\Think\Page($count,$pagemi);
		$page -> setConfig('prev','上一页');
		$page -> setConfig('next','下一页');
		$page -> setConfig('first','首页');
		$page -> setConfig('last','尾页');
		
		//分页超连接
		$pages=$page->show();
		
		//分页当前页
		$this->pro = $model ->where($where)
						  ->order('listorder asc,id desc')
						  ->limit($page->firstRow,$page->listRows)->select();
		$page -> rollPage = 3 ;//设置分页显示多少
		
		//print_r($this->pro);
		if(IS_AJAX){
			//echo '123';
			$str='';
			foreach($this->pro as $k=>$v){
				if(empty ($v['thumb'])){
					$v['thumb']='/Public/images/nopic.jpg';
				}
				$str .= '<li>
							<a href="'.U('Product/details',array('id'=>$v['id'],'j'=>$v['category_id'])).'"><img src="'.$v['thumb'].'" alt="" /></a>
							<div class="text">
								<h4><a href="'.U('Product/details',array('id'=>$v['id'],'j'=>$v['category_id'])).'">'.$v['title'].'</a></h4>
								<p>'.$v.description.'...</p>
								<a href="'.U('Product/details',array('id'=>$v['id'],'j'=>$v['category_id'])).'">了解更多</a>
							</div>
						</li>';
			}

			$data=array();
			$data['str']=$str;
			$data['pages']=$pages;
			//$this->ajaxReturn(json_encode($data)) ;
			echo json_encode($data);
			//echo 123;
			die;
				
		};
		
		//print_r($page);
		$this->assign('pages',$pages); //分页
		
		//$this->assign('pro',$pro);//产品
		
		
		
		
		//渲染模版
		$this->display();
	
	}
	
	
	//产品详情页
	function details(){
		//轮播图
		$modelBanner=M('Banner');
		$condition['category'] = 'product';
		$banner=$modelBanner->where($condition)->find();
		$this->assign('banner',$banner);
		//print_r($banner['thumb']);
		//读取分类表
		$modelCat=M('category');
		$cat=$modelCat->select();
		//print_r($cat);
		$this->assign('cat',$cat);
		
		
		//联表读取 产品及产品分类
		$sql='select c.* ,c.name as cat_name , p.* from __PREFIX__category as c LEFT JOIN __PREFIX__product as p on c.id = p.category_id where p.id ='.$_GET['id'];
		$one = M()->query($sql);
		foreach($one as $k=>$v){
			foreach ($v as $vv){
				$pro=$v;
			}
		}
		
		$model=M('Product_data');
		$prothumb=$model->where('id='.$pro['id'])->find();
		$prothumb['image']=json_decode($prothumb['image']);
		//print_r($pro['id']);
		
		$this->assign('prothumb',$prothumb);
		$this->assign('pro',$pro);//产品
		
		$session=$_SESSION['user'];
		$this->assign('session',$session);
		//print_r($pro);
		//print_r($_SESSION['user']['id']);
		
		
		
			
		
		
		/* //调取购物车库
		if(isset($_SESSION['user'])){
			$id=$_SESSION['user']['id'];
			$model=M('order_goods');
			$goods=$model->where( 'order_id='.$id)->select();
			
			$data=array();
			foreach($goods as $k=>$v){
				$data[]=$v[goods_id];
			}
			if(in_array($pro['id'],$data)){
				$order = "移除购物车";
			}else{
				$order = "加入购物车";
			}
		
		}else{
			$order="加入购物车";
		}
		$this->assign('order',$order); */
		
		
		/* //加入购物车或移除购物车
		if(IS_AJAX){
			$modelAdd = M('order_goods');
			$na=$_POST['na'];
			if($na=='加入购物车'){
				$$dataAdd = $modelAdd->create($dataAdd);
				$dataAdd['order_id']=$_SESSION['user']['id'];
				$dataAdd['goods_id']=$pro['id'];
				$dataAdd['goods_name']=$pro['name'];
				$dataAdd['goods_price']=$pro['real_price'];
				$dataAdd['goods_thumb']=$pro['thumb'];
				if($modelAdd->add($dataAdd)){
					$this->ajaxReturn(1);
				}else{
					$this->ajaxReturn(2);
				}
			}else if($na=='移除购物车'){
				
				$del=$modelAdd->where('goods_id='.$pro['id'])->delete();
				if($del){
					$this->ajaxReturn(3);
				}else{
					$this->ajaxReturn(4);
				}
			}
		}else{
	
			//渲染模版
			$this->display();
		} */
		//渲染模版
		$this->display();
	
	}
	
	function buy(){
		$model=D('Shop');
		$goodsId=$_POST['goodsid'];
		$user_id=$_SESSION['user']['id'];
		$modelPro=M('Product');
		$pro=$modelPro->where('id ='.$goodsId)->find();
		
		
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
		
		//把$goodsId的加入订单 info表
		$data['order_sn']=$model->create_order_sn(); //生成订单号
		$data['user_id']=$user_id;
		$data['total_price']=$pro['real_price']; //模型中算总价
		$data['create_time'] = time(); //生成时间
		$data['is_select'] = 1;
		$order_id = $modelInfo=M('Order_info')->add($data);
		
		//把$goodsId的加入订单 goods表
		$dat['order_id'] = $order_id;
		$dat['goods_id'] = $goodsId;
		$dat['goods_num'] = 1;
		$dat['goods_name'] = $pro['name'];
		$dat['goods_price'] = $pro['real_price'];
		$dat['goods_thumb'] = $pro['thumb'];
		$dat['is_select'] = 1;
		$modelGoods=M('Order_goods')->add($dat);
		
		if($modelInfo && $modelGoods){
			echo 1;
		}else{
			echo 0;
		} 
		
	}
	
	

}



?>