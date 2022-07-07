<?php
namespace Admin2\Model;
use Think\Model;

class RecruitModel extends Model{	
	
	public function getCardNo($unit,$job=0){ 
		$map = array('id'=>$unit);		
		$company = D('RecruitCompany')->where($map)->find();
		$companysn=$company['regionsn'];//县区编号	
		
		$city = D("Region")->where(array('region_id'=>$company['city']))->find();
		$citysn  = $city['no'];//县区编号
		$citysn2 = $city['no2'];//守库押运县区编号		
		//押运刚的地区编号使用另一个
		if ($job==5 && strlen($citysn2) > 0 ) $citysn=$citysn2;
		
		$map = array('company'=>$unit);
		$gang = D('Recruit')->where($map)->select();	
		$gangsn=0; //岗位编号
		foreach ($gang as $k=>$g)  //获取当前岗位是第几个岗位
		{
			if ($g['job']==$job)
			{
			    $gangsn=$k;break;
			}
		}
 		
	   /* 
		$map = array('unit'=>$unit,'job'=>$job,'status'=>1);
		 */		
		$map = array('unit'=>$unit,'job'=>$job);
		$map['no'] = array('neq','');  
		$num = D('Resume')->where($map)->count();		
		if($num > 0) {
			$no = D('Resume')->where($map)->max('no');
			$no++;
			return $no;
		}		
		$num=$gangsn*1000+$num+1;  //同一招聘科室下各岗位直接差值
		$num="00000".$num;
		$num=substr($num,-5);
		return $citysn.$companysn.$num;			 
	}
	
	public function getCardNo——bake($unit,$job=0){
		if($job==5)	$cityno = F('_common/cityno_'.$job);
		else $cityno = F('_common/cityno');
		
		$unit_list = F('_common/company');
		$company = $unit_list[$unit];
		$city_no = $cityno[$company['city']]['no'];
		$no = $city_no . $company['no'];
		$map = array('unit'=>$unit,'job'=>$job);
		$map['no'] = array('neq','');
		$num = D('Resume')->where($map)->count();
		if($num > 0) {
			$no = D('Resume')->where($map)->max('no');
			$no++;
			// if($num >=500) $no+= 501;
			// else $no++;
			return $no;
		}
		unset($map['job']);
		$total = D('Resume')->where($map)->count();		
		if($total==0) $num++;		
		if($total > 0 && $num==0) {
			$job==5?$num++:$num = 501;	//守库押运5  从0起 			
		}		
		$no .= str_pad($num,5,0,STR_PAD_LEFT);		
		return $no;
	}
	
	/*
	** 获取 公司编号缓存
	*/
	public function city_company(){
		$data = F('_common/city_company');
		if(!empty($data)) return $data;
		$info = $this->saveCityCompanyCache(); //重新生成缓存
		return $info;
	}
	/*
	** 生成公司编号 缓存
	*/
	public function saveCityCompanyCache($unit_list=array()){
		if(empty($unit_list)) {
			$unit_list = D('RecruitCompany')->getField('id,name,regionsn,city');
		}
		// $unit_list = D('Common/Recruit')->getCompany();
		$info = array();
		foreach($unit_list as $k=>$r){
			$info[$r['city']][$k] = $r;
		}
		foreach($info as $k=>$val) {
			$i = 1;
			foreach($val as $key=>$r){
				$no = str_pad($i,2,0,STR_PAD_LEFT);
				$info[$k][$key]['no'] = $no;
				$i++;
			}
		}
		F('_common/city_company',$info);
		return $info;
	}
}