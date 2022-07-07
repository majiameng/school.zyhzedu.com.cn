<?php 
namespace Common\Model;
use Think\Model;
class UploadModel{
	public $dir		= './upload/'; 
	public $msg   = '';

	/*
	** code  1没有上传的文件;2非法文件;3非法图片;4未知错误;5文件太大;6非法文件;7文件格式错误
	*/
	public function upload($config){
		
		$upload = new \Think\Upload();// 实例化上传类
		
		// 设置附件上传大小// 设置附件上传类型
		if(isset($config['maxSize']) && !empty($config['maxSize'])) {
			$upload->maxSize	 = $config['maxSize'];
		}else{
			$upload->maxSize  	 =     3145728;
		}
		if(isset($config['exts']) && !empty($config['exts'])) {
			$upload->exts = is_array($config['exts'])?$config['exts']:explode(',',$config['exts']);
		}else{
			$upload->exts      =     array('xls', 'xlsx');// 设置附件上传类型
		}
		
		// 设置附件上传根目录
		if(isset($config['rootPath']) && !empty($config['rootPath'])) {
			$upload->rootPath  =     $this->dir.$config['rootPath'].'/'; 
		}else{
			$upload->rootPath  =     $this->dir.'file/';
		}				
		$upload->savePath  = isset($config['savePath'])?$config['savePath']:''; // 设置附件上传（子）目录
		$dir = realpath($upload->rootPath.$upload->savePath);
		if(!file_exists($dir)) {
			@mkdir($dir,0777,true);
		}
		if(!is_writable($dir)){
			@chmod($dir,0777);
		}
		
		// 上传文件 
		$info   =   $upload->upload();
		if(!$info) {// 上传错误提示错误信息
			$result['code'] = $upload->getErrorNo();
			$result['msg']  = $upload->getError();
			return $result;
		}
        $data = array();$image = new \Think\Image(); 
        foreach($info as $k=>$r) {
			$file = $upload->rootPath.$r['savepath'].$r['savename'];
			$image->open($file);
			$image->thumb(400, 520,\Think\Image::IMAGE_THUMB_SCALE)->save($file);
            $data[$k]['file'] = $file;
            $data[$k]['ext']  = $r['ext'];
        }
        unset($info);
        return $data;	
	}
	

}
?>