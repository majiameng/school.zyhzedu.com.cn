<?php
namespace Admin2\Model;
use Think\Model;

class RegionModel extends Model{
	public function getChild ($pid){
		$region = F('_common/region');
		if(empty($region)) return false;
		foreach($region as $r) {
			if($r['parent_id'] != $pid) continue;
			$info[$r['region_id']]['name'] = $r['region_name'];
		}
		return $info;
	}
   public function cacheAll()
    {
       $region = $this->select();
	   $info = $data = array();	   
	   if(!empty($region)){
		   foreach($region as $k=>$r){
			   $info[$r['region_id']] 			 = $r;
			   $val['id'] 	= $r['region_id'];	
			   $val['name'] = $r['region_name'];				
			   $data[$r['parent_id']][] = $val;	
		   }		   
	   }	   	
	   F('_common/region',$info); 
	   $this->setJs($data);	   
    }
	/**
	 * 获取地区路径
	 * @param $id   地区id
	 * @param $string 递归返回结果数组
	 */
	public function getPath($id ,$path=array(), $region=array())
	{
		if(empty($region)) $region = $this->getCache('region');
		if(empty($region)) $region = $this->getRegionService()->findAllRegion();
		if(isset($region[$id])) {
			array_unshift($path ,$region[$id]['region_name']);
			return $this->getPath($region[$id]['parent_id'],$path,$region);
		}else{			
			return implode('',$path);
		}		
	}

	/**
	 * 获取地区路径
	 * @param $id   地区id
	 * @param $string 递归返回结果数组
	 */
	public function getParent($id ,$parent=array())
	{
		$region = $this->getCache('region');
		if(empty($region)) $region = $this->getRegionService()->findAllRegion();
		if(isset($region[$id])) {
			array_unshift($parent ,$region[$id]['region_id']);
			return $this->getParent($region[$id]['parent_id'],$parent);
		}else{			
			return $parent;
		}		
	}
	
	public function setJs($region=array())
	{	
		if(!empty($region)){
			foreach($region as $k=>$val){
				foreach($val as $key=>$r){
					$region[$k][$key]['name'] = urlencode($r['name']);
				}
			}
		}		
		$file       =  WEB_PATH . '/Public/js/region.js';
		$content = 'var regionJson = '.urldecode(json_encode($region)).';';
		file_put_contents($file, $content);
	}
}