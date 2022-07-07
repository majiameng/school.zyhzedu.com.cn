<?php 
namespace Home\Controller;
use Think\Controller;
class SchoolController extends PublicController{
	
	//校园风采首页
	function index(){
		
		$news=M('News');
		$one=$news->order('id desc')
            ->where('category_id=2 AND is_del=0')
		    ->limit('4')->select();
		$this->assign('one',$one);

		
		//计算产品表总条数
		$where = 'is_del= 0';
		$count=$news->where($where)->count();
		//print_r($count);
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
		$this->pro = $news ->where($where)
						  ->order('listorder asc,id desc')
						  ->limit($page->firstRow,$page->listRows)->select();
		$page -> rollPage = 3 ;//设置分页显示多少

		//print_r($one);
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
	function shang(){
		if(IS_AJAX){
		$model=M('Article');
		$rs=$model->where('id <'.$_GET['id'])
				->order('id desc')->find();
		$this->assign('rs',$rs);
		$str='';
		if(empty ($rs['thumb'])){
					$rs['thumb']='/Public/images/nopic.jpg';
				}
		$str .='<h2>'.$rs['title'].'</h2>
			<h3>发布时间：<span>'.date('y-m-d',$rs['create_time']).'</span>&nbsp;&nbsp;|&nbsp;&nbsp;浏览过：<span>1920</span></h3>
			<div class="mg">
				<p>当前位置：<a href="'.U('Index/index').'">首页</a>><a href="'.U('Product/index').'">新闻中心><a href="###" class="on">新闻资讯</a></p>
				<img src="'.$rs['thumb'].'" alt="" />
				
				<p>'.$rs['description'].'</p>
				
				<object type="application/x-shockwave-flash" data="http://static.youku.com/v1.0.0657/v/swf/player_yknpsv.swf" width="556" height="510" id="movie_player"><param name="allowFullScreen" value="true"><param name="allowscriptaccess" value="always"><param name="allowFullScreenInteractive" value="true"><param name="wmode" value="direct"><param name="bgcolor" value="#000000"><param name="flashvars" value="VideoIDS=XMTczOTY5NDU4OA==&amp;ShowId=0&amp;category=91&amp;Cp=0&amp;sv=true&amp;Light=on&amp;THX=off&amp;unCookie=0&amp;frame=0&amp;pvid=1474988949252GwgzyM&amp;uepflag=0&amp;Tid=0&amp;isAutoPlay=false&amp;Version=/v1.0.170&amp;show_ce=0&amp;winType=interior&amp;Type=Folder&amp;Fid=28251439&amp;Pt=0&amp;Ob=1&amp;plchid=&amp;playmode=2&amp;embedid=AjQzNDkyMzY0NwJ3d3cueW91a3UuY29tAi8=&amp;ysuid=1474730148924GNP&amp;vext=bc%3D%26pid%3D1474988949252GwgzyM%26unCookie%3D0%26frame%3D0%26type%3D1%26fob%3D1%26fpo%3D0%26svt%3D0%26cna%3Djh1sEMl%2BTAQCATspXZnKYNqn%26emb%3DAjQzNDkyMzY0NwJ3d3cueW91a3UuY29tAi8%3D%26dn%3D%E7%BD%91%E9%A1%B5%26hwc%3D1%26mtype%3Doth&amp;cna=jh1sEMl+TAQCATspXZnKYNqn&amp;pageStartTime=1474988949251"><param name="movie" value="http://static.youku.com/v1.0.0657/v/swf/player_yknpsv.swf"><div class="player_html5"><div class="picture" style="height:100%"><div style="line-height:460px;"><span style="font-size:18px">您还没有安装flash播放器,请点击<a href="http://www.adobe.com/go/getflash" target="_blank">这里</a>安装</span></div></div></div></object>
				
				<div class="fr">
					<h3>'.$rs['writer'].'</h3>
					<p>'.$rs['content'].'</p>
				</div>
				<input type="hidden" value="'.$rs['id'].'" class="ids">
				<div class="page"><a href="javascript:shang()" class="shang">上一篇</a><a href="javascript:history.back();">返回</a><a href="javascript:xia()">下一篇</a></div>
			</div>';
				
				
				
				
				
			$data['str']=$str;
			
			echo json_encode($data);
			die;
		}
				
	}
	function xia(){
		if(IS_AJAX){
			$model=M('Article');
			$rs=$model->where('id >'.$_GET['id'])
			->order('id asc')->find();
			$this->assign('rs',$rs);
			$str='';
			if(empty ($rs['thumb'])){
				$rs['thumb']='/Public/images/nopic.jpg';
			}
			$str .='<h2>'.$rs['title'].'</h2>
			<h3>发布时间：<span>'.date('y-m-d',$rs['create_time']).'</span>&nbsp;&nbsp;|&nbsp;&nbsp;浏览过：<span>1920</span></h3>
			<div class="mg">
				<p>当前位置：<a href="'.U('Index/index').'">首页</a>><a href="'.U('Product/index').'">新闻中心><a href="###" class="on">新闻资讯</a></p>
				<img src="'.$rs['thumb'].'" alt="" />
	
				<p>'.$rs['description'].'</p>
	
				<object type="application/x-shockwave-flash" data="http://static.youku.com/v1.0.0657/v/swf/player_yknpsv.swf" width="556" height="510" id="movie_player"><param name="allowFullScreen" value="true"><param name="allowscriptaccess" value="always"><param name="allowFullScreenInteractive" value="true"><param name="wmode" value="direct"><param name="bgcolor" value="#000000"><param name="flashvars" value="VideoIDS=XMTczOTY5NDU4OA==&amp;ShowId=0&amp;category=91&amp;Cp=0&amp;sv=true&amp;Light=on&amp;THX=off&amp;unCookie=0&amp;frame=0&amp;pvid=1474988949252GwgzyM&amp;uepflag=0&amp;Tid=0&amp;isAutoPlay=false&amp;Version=/v1.0.170&amp;show_ce=0&amp;winType=interior&amp;Type=Folder&amp;Fid=28251439&amp;Pt=0&amp;Ob=1&amp;plchid=&amp;playmode=2&amp;embedid=AjQzNDkyMzY0NwJ3d3cueW91a3UuY29tAi8=&amp;ysuid=1474730148924GNP&amp;vext=bc%3D%26pid%3D1474988949252GwgzyM%26unCookie%3D0%26frame%3D0%26type%3D1%26fob%3D1%26fpo%3D0%26svt%3D0%26cna%3Djh1sEMl%2BTAQCATspXZnKYNqn%26emb%3DAjQzNDkyMzY0NwJ3d3cueW91a3UuY29tAi8%3D%26dn%3D%E7%BD%91%E9%A1%B5%26hwc%3D1%26mtype%3Doth&amp;cna=jh1sEMl+TAQCATspXZnKYNqn&amp;pageStartTime=1474988949251"><param name="movie" value="http://static.youku.com/v1.0.0657/v/swf/player_yknpsv.swf"><div class="player_html5"><div class="picture" style="height:100%"><div style="line-height:460px;"><span style="font-size:18px">您还没有安装flash播放器,请点击<a href="http://www.adobe.com/go/getflash" target="_blank">这里</a>安装</span></div></div></div></object>
	
				<div class="fr">
					<h3>'.$rs['writer'].'</h3>
					<p>'.$rs['content'].'</p>
				</div>
				<input type="hidden" value="'.$rs['id'].'" class="ids">
				<div class="page"><a href="javascript:;" class="shang">上一篇</a><a href="javascript:history.back();">返回</a><a href="javascript:;">下一篇</a></div>
			</div>';
	
	
	
	
	
			$data['str']=$str;
				
			echo json_encode($data);
			die;
		}
	
	}

}



?>