<?php
namespace Admin2\Model;
use Think\Model;

class RecruitModel extends Model{	
	
	public function getCardNo($unit,$job=0){
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
	** 生成公司编号
	*/
	public function city_company(){
		$data = F('_common/city_company');
		if(!empty($data)) return $data;
		$unit_list = F('_common/company');
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
				// D('RecruitCompany')->where(array('id'=>$r['id']))->save(array('no'=>$no));
			}
		}
		F('_common/city_company',$info);
		return $info;
	}
}