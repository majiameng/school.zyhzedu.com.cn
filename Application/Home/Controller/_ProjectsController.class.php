<?php
namespace Home\Controller;
use Think\Controller;
class ProjectsController extends PublicController{


    //文件下载
    function download_file(){
        $file = $_GET['file'];
        if(is_file($file)){
            $length = filesize($file);
            //$type = mime_content_type($file);
            $finfo    = finfo_open(FILEINFO_MIME_TYPE);
            $type = finfo_file($finfo, $file);
            finfo_close($finfo);
            $showname =  ltrim(strrchr($file,'/'),'/');
            header("Content-Description: File Transfer");
            header('Content-type: ' . $type);
            header('Content-Length:' . $length);
            if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) { //for IE
                header('Content-Disposition: attachment; filename="' . rawurlencode($showname) . '"');
            } else {
                header('Content-Disposition: attachment; filename="' . $showname . '"');
            }
            readfile($file);
            exit;
        } else {
            exit('文件已被删除！');
        }
    }
	
	//首页
	function index(){

		//渲染模版
		$this->display();
	
	}
    function vie(){

        //渲染模版
        $this->display();

    }
    function agreement(){

        //渲染模版
        $this->display();

    }


    public function upp(){


        if(IS_AJAX) {

            

	    $re_id=$_POST['recid'];//调取recruit 的id


        //保存文件
        /*$upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg', 'txt');// 设置附件上传类型
        $upload->savePath  =      ''; // 设置附件上传目录
        $upload->rootPath = './Public/Uploads/';
        $upload->saveName = 'myfun';
        $upload->saveName = $re_id.'_'.time().'_'.mt_rand(1000,9000);
        $info   =   $upload->upload();*/



            $filename  = '';
            if($_FILES['file']['name']!=='' && $_FILES['file']['error']==0){  //判断提交图片的名字不为 并且 错误是0
                $houzhui = strrchr($_FILES['file']['name'],'.'); //提取提交的图片的后缀
                //echo $houzhui;die;
                $filename = './Public/uploads/projects_down/'.$re_id.'_'.date('Ymd').'_'.mt_rand(100,999).$houzhui; //保存图片的新路径
                //echo $filename;die;

                $info = move_uploaded_file($_FILES['file']['tmp_name'],$filename);  //存储图片

                }

       // print_r($_FILES);
        //print_r($_FILES['file']['tmp_name']);die;

        //保存到数据库
        $model = M('Projects_down');
        $data['pro_url'] = $filename;
		$data['projects_id'] = $re_id;
		$data['is_del'] = 0;

		

		
        
		$User = $model->add($data);


        //回传信息
        if(!$info && !$User) {// 上传错误提示错误信息
            echo 1;
        }else {// 上传成功
            echo 2;
        }
    }

	}
	
	public function yzm(){

			if(IS_AJAX){
				//echo (123);
				$yzm = new \Think\Verify();

				if($yzm->check($_POST['yzm'])){
					if($yzm){
						echo 1;
					}else{
						echo 0;
					}

				}

			}
	}
	public function addMessage(){
		if(IS_AJAX){
			//echo(123);
			if(!empty($_POST)){
				$model=M('message');
				$model->create();
					$data['username']=$_POST['username'];
					$data['address']=$_POST['address'];
					$data['tel']=$_POST['tel'];
					$data['email']=$_POST['email'];
					$data['content']=$_POST['content'];
				if($model->add($data)){
					$this->ajaxReturn(1);	
				}else{
					$this->ajaxReturn(2);
				}
			}
		}
	}
	function code(){
		$config =    array(
				'imageW'=>120,
				'imageH'=>50,
				'length'=>4,
				'useNoise'=>false,
				'fontSize'=>16,
		);
		$Verify = new \Think\Verify($config);
		$Verify->entry();
	}



    function fileupload(){
        $dir_base = "./Public/uploads/repord/";     //文件上传根目录
             //没有成功上传文件，报错并退出。
        if(empty($_FILES)) {
          echo "<textarea><img src='{$dir_base}error.jpg'/></textarea>";
          exit(0);
         }

        $output = "<textarea>";
        $index = 0;        //$_FILES 以文件name为数组下标，不适用foreach($_FILES as $index=>$file)
        $mutiFileName = array();
        foreach($_FILES as $k=>$v){

            $upload_file_name = 'upload_file' . $index;        //对应index.html FomData中的文件命名


            $houzhui = strrchr($v['name'],'.'); //提取提交的图片的后缀
            $filename= date('YmdHis').'_'.mt_rand(100,999).$houzhui; //保存图片的新路径

            //$filename = $_FILES[$upload_file_name]['name'];

            $gb_filename = iconv('utf-8','gb2312',$filename);    //名字转换成gb2312处理



             //文件不存在才上传
           if(!file_exists($dir_base.$gb_filename)) {
                  $isMoved = false;  //默认上传失败
             $MAXIMUM_FILESIZE = 1 * 1024 * 1024;     //文件大小限制    1M = 1 * 1024 * 1024 B;
             $rEFileTypes = "/^\.(jpg|jpeg|gif|png){1}$/i";
             if ($_FILES[$upload_file_name]['size'] <= $MAXIMUM_FILESIZE && preg_match($rEFileTypes, strrchr($gb_filename, '.'))) {
                    $isMoved = @move_uploaded_file ( $_FILES[$upload_file_name]['tmp_name'], $dir_base.$gb_filename);        //上传文件

                 $info = $dir_base.$gb_filename;
                 $mutiFileName[$k] = $info;
			 }
            }else{

                $info = $dir_base.$gb_filename;
                $mutiFileName[$k] = $info;

              $isMoved = true;    //已存在文件设置为上传成功

            }

            if($isMoved){

               //输出图片文件<img>标签
             //注：在一些系统src可能需要urlencode处理，发现图片无法显示，
             //    请尝试 urlencode($gb_filename) 或 urlencode($filename)，不行请查看HTML中显示的src并酌情解决。
             $output .= "<img src='{$dir_base}{$filename}' title='{$filename}' alt='{$filename}'/>";

            }else {
               $output .= "<img src='{$dir_base}error.jpg' title='{$filename}' alt='{$filename}'/>";
            }
            $index++;
        }
         echo $output."</textarea>";
        $mutiF = json_encode($mutiFileName);
        setcookie("Repord",$mutiF, time()+3600);
    }



    //AJAX提交表单
    function tabSubmit(){	
        if(IS_AJAX) {
			$msg['code'] = 1;
			$user = session('user');
			if(empty($user)){
				$msg['code'] = 3;
				$this->ajaxReturn($msg);exit;
			}
			if($this->isApply()){
				$msg['code'] = 4;
				$this->ajaxReturn($msg);exit;
			}
            $model = M('Entry');
            $data['userID'] = $user['id'];
            $data['type'] = intval($_POST['type']);
            $data['name'] = I('post.name','','trim');
            $data['sex'] = I('post.sex','','intval');
            $data['card'] = I('post.card','','trim');
            $data['birth'] = $_POST['birth'];
            $data['personalTel'] = $_POST['personalTel'];
            $data['focusedInputEmail'] = $_POST['focusedInputEmail'];
            $data['school'] = $_POST['school'];
            $data['schoolTel'] = $_POST['schoolTel'];
            $data['class'] = $_POST['schoolClass'];
            $data['optionsRadiosinsel'] = $_POST['optionsRadiosinsel'];
            $data['major'] = $_POST['major'];
            $data['optionsRadiosinMajor'] = $_POST['optionsRadiosinMajor'];
            $data['father'] = $_POST['father'];
            $data['fatherTel'] = $_POST['fatherTel'];
            $data['mather'] = $_POST['mather'];
            $data['matherTel'] = $_POST['matherTel'];
            $data['teacher'] = $_POST['teacher'];
            $data['teacherTel'] = $_POST['teacherTel'];
            $data['address'] = trim($_POST['address']);
            $data['road'] = intval($_POST['road']);
            $data['roadName'] = trim($_POST['roadName']);
            $data['otherRoad'] = $_POST['otherRoad'];
            $data['repord'] = $_COOKIE['Repord'];
            // $data['from'] = $_POST['from'];
            $data['is_select'] = 0;
            $data['is_del'] = 0;
            $data['accepted'] = 0;
            $data['classMe'] = 0;
            $data['is_pay'] = 0;
            $data['score'] = I('post.score',0,'floatval');
            $data['cur'] = I('post.typecur',0,'intval');
            $data['addtime'] = $data['uptime'] = time();
        	
			if(!empty($data['name'])) $data['protocol_img'] = transfer_to_image($data['name'],'protocol');
            $info = $model->data($data)->add();            
            if($info){
                setcookie("Repord", '');
				$msg['num'] = $model->where('is_del=0')->count();
				$this->ajaxReturn($msg);exit;
            }else{
                $msg['code'] = 0;
				$this->ajaxReturn($msg);exit;
            }
        }
    }
    function tableTianjing(){
		$id = I('get.id',0,'intval');
		if(in_array($id,array(42))) {
			$this->error('报名已经截止');exit;
		}
		$userid = session('user.id');
        if(empty($userid)){
            $this->error('请先登录',U('login/index'));exit;
        }
		$is_apply = $this->isApply();		
		if(!$is_apply) {
			$User= M('Projects');
			$title = $User->where('id='.$id)->find();
			if(empty($title)) $this->error('非法操作');
			$this->assign('title',$title);
			
			$moderFrom = M('Projects_from');
			$from = $moderFrom->where('is_del=0')->select();
			$this->assign('from',$from);
			
			if(IS_POST){
				$card=$_COOKIE;
			}
		}					
		$this->assign('is_apply',$is_apply)
			->assign('road_type',get_road_type())
			->display();
    }

    //判断是否提交
    function isSubmit(){
        if(IS_AJAX){
            $typeID = $_POST['typeID'];
            $User= M('Entry');
			$str = $this->isApply();
            if($str){
                $this->ajaxReturn(1); //已提交过报名表
            }else{
                $this->ajaxReturn(0); //没有提交过报名表
            }
        }
    }
	
	public function isApply() {		
		$where['userID'] = session('user.id');
		$r = D('Entry')->where($where)->getField('id');		
		return !empty($r)?true:false;
	}

	function cooperation(){		
        //渲染模版
        $this->display();
    }
    function procedure(){
	    $this->display();
    }
	
}
?>